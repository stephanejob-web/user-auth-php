<?php
/**
 * ============================================================================
 * CLASSE AUTH - SERVICE D'AUTHENTIFICATION
 * ============================================================================
 *
 * Cette classe gère toute la logique d'authentification :
 * - Inscription (register)
 * - Connexion (login)
 * - Déconnexion (logout)
 * - Vérification des permissions
 *
 * Avantages :
 * - Centralisation de la logique d'authentification
 * - Code réutilisable
 * - Facilite les tests
 * - Séparation des responsabilités
 */

namespace App\Services;

use App\Models\User;
use App\Services\Session;
use App\Services\Validator;

class Auth
{
    /**
     * Inscrit un nouvel utilisateur
     *
     * @param string $email Email de l'utilisateur
     * @param string $password Mot de passe en clair
     * @param string $confirmPassword Confirmation du mot de passe
     * @return array ['success' => bool, 'message' => string, 'user' => User|null]
     */
    public static function register(string $email, string $password, string $confirmPassword): array
    {
        // Créer un validateur
        $validator = new Validator();

        // Nettoyer l'email
        $email = Validator::sanitizeEmail($email);

        // Valider l'email
        $validator->validateEmail($email);

        // Valider le mot de passe
        $validator->validatePassword($password);

        // Valider la correspondance des mots de passe
        $validator->validatePasswordMatch($password, $confirmPassword);

        // Si des erreurs de validation
        if ($validator->hasErrors()) {
            return [
                'success' => false,
                'message' => $validator->getFirstError(),
                'user' => null
            ];
        }

        // Vérifier si l'email existe déjà
        if (User::emailExists($email)) {
            return [
                'success' => false,
                'message' => 'This email is already registered.',
                'user' => null
            ];
        }

        // Hasher le mot de passe
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Créer l'utilisateur
        $user = new User($email, $hashedPassword);
        $user->setIsAdmin(0); // Par défaut, pas admin

        // Enregistrer dans la base de données
        if ($user->create()) {
            return [
                'success' => true,
                'message' => 'Registration successful!',
                'user' => $user
            ];
        } else {
            return [
                'success' => false,
                'message' => 'An error occurred during registration.',
                'user' => null
            ];
        }
    }

    /**
     * Connecte un utilisateur
     *
     * @param string $email Email de l'utilisateur
     * @param string $password Mot de passe en clair
     * @return array ['success' => bool, 'message' => string, 'user' => User|null]
     */
    public static function login(string $email, string $password): array
    {
        // Créer un validateur
        $validator = new Validator();

        // Nettoyer l'email
        $email = Validator::sanitizeEmail($email);

        // Valider que l'email et le mot de passe ne sont pas vides
        $validator->validateRequired($email, 'Email');
        $validator->validateRequired($password, 'Password');

        // Si des erreurs de validation
        if ($validator->hasErrors()) {
            return [
                'success' => false,
                'message' => $validator->getFirstError(),
                'user' => null
            ];
        }

        // Chercher l'utilisateur par email
        $user = User::findByEmail($email);

        // Si l'utilisateur n'existe pas
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Invalid email or password.',
                'user' => null
            ];
        }

        // Vérifier le mot de passe
        if (!password_verify($password, $user->getPassword())) {
            return [
                'success' => false,
                'message' => 'Invalid email or password.',
                'user' => null
            ];
        }

        // Authentification réussie
        // Créer la session
        Session::login($user->getId(), $user->getEmail(), $user->getIsAdmin());

        // Régénérer l'ID de session pour la sécurité
        Session::regenerate();

        return [
            'success' => true,
            'message' => 'Login successful!',
            'user' => $user
        ];
    }

    /**
     * Déconnecte l'utilisateur actuel
     *
     * @return void
     */
    public static function logout(): void
    {
        Session::logout();
    }

    /**
     * Vérifie si un utilisateur est connecté
     *
     * @return bool True si connecté, False sinon
     */
    public static function check(): bool
    {
        return Session::isAuthenticated();
    }

    /**
     * Vérifie si l'utilisateur connecté est un administrateur
     *
     * @return bool True si admin, False sinon
     */
    public static function isAdmin(): bool
    {
        return Session::isAdmin();
    }

    /**
     * Récupère l'utilisateur connecté
     *
     * @return User|null Utilisateur connecté ou null
     */
    public static function user(): ?User
    {
        $userId = Session::getUserId();

        if ($userId === null) {
            return null;
        }

        return User::findById($userId);
    }

    /**
     * Récupère l'ID de l'utilisateur connecté
     *
     * @return int|null ID de l'utilisateur ou null
     */
    public static function id(): ?int
    {
        return Session::getUserId();
    }

    /**
     * Exige qu'un utilisateur soit connecté (sinon redirection)
     *
     * @param string $redirectTo URL de redirection si non connecté
     * @return void
     */
    public static function requireAuth(string $redirectTo = 'login.php'): void
    {
        if (!self::check()) {
            header("Location: $redirectTo");
            exit();
        }
    }

    /**
     * Exige qu'un utilisateur soit administrateur (sinon redirection)
     *
     * @param string $redirectTo URL de redirection si pas admin
     * @return void
     */
    public static function requireAdmin(string $redirectTo = 'index.php'): void
    {
        // Vérifier d'abord si l'utilisateur est connecté
        self::requireAuth();

        // Puis vérifier s'il est admin
        if (!self::isAdmin()) {
            header("Location: $redirectTo");
            exit();
        }
    }

    /**
     * Vérifie si un utilisateur est invité (non connecté)
     *
     * @return bool True si invité, False sinon
     */
    public static function guest(): bool
    {
        return !self::check();
    }
}

/*
===============================================================================
NOTES PÉDAGOGIQUES - CLASSE AUTH
===============================================================================

1. POURQUOI UNE CLASSE AUTH ?

   Avant (code procédural) :
   - Logique d'authentification dupliquée dans login.php, register.php
   - Code long et répétitif
   - Difficile à tester et à maintenir

   Après (POO) :
   - Logique centralisée dans Auth
   - Code réutilisable
   - Facile à tester
   - API claire et cohérente

2. INSCRIPTION (REGISTER)

   use App\Services\Auth;

   $result = Auth::register($email, $password, $confirmPassword);

   if ($result['success']) {
       echo $result['message']; // "Registration successful!"
       $user = $result['user']; // Objet User
       // Redirection vers login
   } else {
       echo $result['message']; // Message d'erreur
   }

3. CONNEXION (LOGIN)

   use App\Services\Auth;

   $result = Auth::login($email, $password);

   if ($result['success']) {
       echo $result['message']; // "Login successful!"
       $user = $result['user']; // Objet User
       // Redirection vers index
   } else {
       echo $result['message']; // "Invalid email or password."
   }

4. DÉCONNEXION (LOGOUT)

   use App\Services\Auth;

   Auth::logout();
   // Redirection vers index ou login

5. VÉRIFIER SI CONNECTÉ

   use App\Services\Auth;

   if (Auth::check()) {
       echo "User is logged in";
   }

   if (Auth::guest()) {
       echo "User is NOT logged in";
   }

6. VÉRIFIER SI ADMIN

   use App\Services\Auth;

   if (Auth::isAdmin()) {
       echo "User is administrator";
   }

7. RÉCUPÉRER L'UTILISATEUR CONNECTÉ

   use App\Services\Auth;

   $user = Auth::user();
   if ($user) {
       echo "Welcome, " . $user->getEmail();
   }

   // Ou juste l'ID
   $userId = Auth::id();

8. PROTECTION DE PAGE

   // Dans une page protégée (ex: profile.php)
   use App\Services\Auth;

   Auth::requireAuth(); // Redirige vers login si non connecté

   // Dans une page admin (ex: admin.php)
   use App\Services\Auth;

   Auth::requireAdmin(); // Redirige vers index si pas admin

9. EXEMPLE COMPLET - LOGIN.PHP

   use App\Services\Auth;
   use App\Utils\Response;

   if ($_SERVER['REQUEST_METHOD'] === 'POST') {
       $email = $_POST['email'];
       $password = $_POST['password'];

       $result = Auth::login($email, $password);

       if ($result['success']) {
           Response::redirectToHome();
       } else {
           $error = $result['message'];
       }
   }

10. EXEMPLE COMPLET - REGISTER.PHP

    use App\Services\Auth;
    use App\Utils\Response;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'];

        $result = Auth::register($email, $password, $confirmPassword);

        if ($result['success']) {
            Response::redirectWithMessage(
                'login.php',
                'success',
                'Registration successful! You can now login.'
            );
        } else {
            $error = $result['message'];
        }
    }

11. EXEMPLE COMPLET - PROFILE.PHP

    use App\Services\Auth;

    // Protection de page
    Auth::requireAuth();

    // Récupérer l'utilisateur connecté
    $user = Auth::user();
    echo "Email: " . $user->getEmail();

12. AVANTAGES

    ✅ Code court et expressif
    ✅ API cohérente et intuitive
    ✅ Gestion centralisée de l'authentification
    ✅ Validation intégrée
    ✅ Sécurité renforcée (régénération de session)
    ✅ Facilite les tests unitaires
    ✅ Facilite l'ajout de nouvelles fonctionnalités

===============================================================================
*/
