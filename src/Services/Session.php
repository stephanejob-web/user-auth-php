<?php
/**
 * ============================================================================
 * CLASSE SESSION - GESTION DES SESSIONS
 * ============================================================================
 *
 * Cette classe encapsule toute la logique de gestion des sessions PHP.
 * Elle fournit des méthodes pour :
 * - Démarrer une session
 * - Définir et récupérer des valeurs
 * - Vérifier l'authentification
 * - Détruire la session (logout)
 *
 * Avantages :
 * - Code centralisé et réutilisable
 * - Abstraction de $_SESSION
 * - Facilite les tests
 */

namespace App\Services;

class Session
{
    /**
     * Démarre une session si elle n'est pas déjà démarrée
     *
     * @return void
     */
    public static function start(): void
    {
        // Vérifier si la session n'est pas déjà démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Définit une valeur dans la session
     *
     * @param string $key Clé de la valeur
     * @param mixed $value Valeur à stocker
     * @return void
     */
    public static function set(string $key, $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    /**
     * Récupère une valeur de la session
     *
     * @param string $key Clé de la valeur
     * @param mixed $default Valeur par défaut si la clé n'existe pas
     * @return mixed Valeur de la session ou valeur par défaut
     */
    public static function get(string $key, $default = null)
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Vérifie si une clé existe dans la session
     *
     * @param string $key Clé à vérifier
     * @return bool True si la clé existe, False sinon
     */
    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    /**
     * Supprime une valeur de la session
     *
     * @param string $key Clé à supprimer
     * @return void
     */
    public static function remove(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    /**
     * Détruit complètement la session (logout)
     *
     * @return void
     */
    public static function destroy(): void
    {
        self::start();

        // Vider toutes les variables de session
        $_SESSION = [];

        // Supprimer le cookie de session
        if (isset($_COOKIE[session_name()])) {
            setcookie(
                session_name(),
                '',
                time() - 42000,
                '/'
            );
        }

        // Détruire la session
        session_destroy();
    }

    /**
     * Vérifie si un utilisateur est connecté
     *
     * @return bool True si connecté, False sinon
     */
    public static function isAuthenticated(): bool
    {
        return self::has('user_id');
    }

    /**
     * Vérifie si l'utilisateur connecté est un administrateur
     *
     * @return bool True si admin, False sinon
     */
    public static function isAdmin(): bool
    {
        return self::get('is_admin', 0) == 1;
    }

    /**
     * Récupère l'ID de l'utilisateur connecté
     *
     * @return int|null ID de l'utilisateur ou null si non connecté
     */
    public static function getUserId(): ?int
    {
        return self::get('user_id');
    }

    /**
     * Récupère l'email de l'utilisateur connecté
     *
     * @return string|null Email de l'utilisateur ou null si non connecté
     */
    public static function getUserEmail(): ?string
    {
        return self::get('email');
    }

    /**
     * Connecte un utilisateur en créant sa session
     *
     * @param int $userId ID de l'utilisateur
     * @param string $email Email de l'utilisateur
     * @param int $isAdmin Statut admin (0 ou 1)
     * @return void
     */
    public static function login(int $userId, string $email, int $isAdmin): void
    {
        self::set('user_id', $userId);
        self::set('email', $email);
        self::set('is_admin', $isAdmin);
    }

    /**
     * Déconnecte l'utilisateur et détruit la session
     *
     * @return void
     */
    public static function logout(): void
    {
        self::destroy();
    }

    /**
     * Régénère l'ID de session (sécurité)
     *
     * Utile après une connexion pour éviter le session fixation
     *
     * @return void
     */
    public static function regenerate(): void
    {
        self::start();
        session_regenerate_id(true);
    }

    /**
     * Définit un message flash (message temporaire pour une seule page)
     *
     * @param string $key Clé du message
     * @param string $message Message à afficher
     * @return void
     */
    public static function setFlash(string $key, string $message): void
    {
        self::set('_flash_' . $key, $message);
    }

    /**
     * Récupère et supprime un message flash
     *
     * @param string $key Clé du message
     * @return string|null Message flash ou null
     */
    public static function getFlash(string $key): ?string
    {
        $flashKey = '_flash_' . $key;
        $message = self::get($flashKey);
        self::remove($flashKey);
        return $message;
    }
}

/*
===============================================================================
NOTES PÉDAGOGIQUES - CLASSE SESSION
===============================================================================

1. POURQUOI UNE CLASSE SESSION ?

   Avant (code procédural) :
   - session_start() appelé dans chaque fichier
   - Accès direct à $_SESSION partout
   - Code dupliqué pour vérifier l'authentification

   Après (POO) :
   - Abstraction complète de $_SESSION
   - Gestion centralisée des sessions
   - Méthodes pratiques pour l'authentification
   - Code plus lisible et maintenable

2. MÉTHODES STATIQUES

   Toutes les méthodes sont statiques car :
   - Une seule session existe par requête HTTP
   - Pas besoin d'instancier la classe
   - Utilisation simple : Session::method()

3. UTILISATION DE BASE

   // Démarrer la session
   Session::start();

   // Définir une valeur
   Session::set('name', 'John');

   // Récupérer une valeur
   $name = Session::get('name');

   // Vérifier si une clé existe
   if (Session::has('name')) {
       echo "Name exists!";
   }

   // Supprimer une valeur
   Session::remove('name');

4. AUTHENTIFICATION

   // Connexion d'un utilisateur
   Session::login($userId, $email, $isAdmin);

   // Vérifier si connecté
   if (Session::isAuthenticated()) {
       echo "User is logged in!";
   }

   // Vérifier si admin
   if (Session::isAdmin()) {
       echo "User is admin!";
   }

   // Récupérer l'ID de l'utilisateur
   $userId = Session::getUserId();

   // Déconnexion
   Session::logout();

5. MESSAGES FLASH

   Les messages flash sont des messages temporaires qui s'affichent
   une seule fois (sur la page suivante) puis disparaissent.

   // Définir un message flash
   Session::setFlash('success', 'User registered successfully!');

   // Redirection
   header('Location: login.php');

   // Dans login.php, récupérer et afficher le message
   $successMessage = Session::getFlash('success');
   if ($successMessage) {
       echo $successMessage;
   }

6. SÉCURITÉ : RÉGÉNÉRATION DE SESSION

   session_regenerate_id() change l'ID de session.
   Utile après une connexion pour éviter le "session fixation".

   // Dans login.php, après authentification réussie
   Session::regenerate();

7. EXEMPLE COMPLET

   // Dans login.php
   use App\Services\Session;

   Session::start();

   if ($_SERVER['REQUEST_METHOD'] === 'POST') {
       // ... validation et authentification ...

       if ($authSuccess) {
           // Connecter l'utilisateur
           Session::login($user['id'], $user['email'], $user['is_admin']);

           // Régénérer l'ID de session (sécurité)
           Session::regenerate();

           // Redirection
           header('Location: index.php');
           exit();
       }
   }

   // Dans une page protégée
   use App\Services\Session;

   Session::start();

   if (!Session::isAuthenticated()) {
       header('Location: login.php');
       exit();
   }

   // Afficher l'email de l'utilisateur
   echo "Welcome, " . Session::getUserEmail();

8. AVANTAGES

   ✅ Code plus lisible et expressif
   ✅ Abstraction de $_SESSION
   ✅ Méthodes utilitaires pratiques
   ✅ Facilite les tests (on peut mocker la classe)
   ✅ Protection contre les erreurs (session_start() multiple)
   ✅ Gestion cohérente des sessions

===============================================================================
*/
