<?php
/**
 * ============================================================================
 * CLASSE RESPONSE - GESTION DES RÉPONSES HTTP
 * ============================================================================
 *
 * Cette classe facilite la gestion des réponses HTTP :
 * - Redirections
 * - Codes de statut HTTP
 * - En-têtes personnalisés
 *
 * Avantages :
 * - Code plus lisible
 * - Gestion centralisée des redirections
 * - Facilite les tests
 */

namespace App\Utils;

use App\Services\Session;

class Response
{
    /**
     * Redirige vers une URL et arrête l'exécution
     *
     * @param string $url URL de destination
     * @param int $statusCode Code HTTP (301 = permanent, 302 = temporaire)
     * @return void
     */
    public static function redirect(string $url, int $statusCode = 302): void
    {
        header("Location: $url", true, $statusCode);
        exit();
    }

    /**
     * Redirige vers l'accueil
     *
     * @return void
     */
    public static function redirectToHome(): void
    {
        self::redirect('index.php');
    }

    /**
     * Redirige vers la page de connexion
     *
     * @return void
     */
    public static function redirectToLogin(): void
    {
        self::redirect('login.php');
    }

    /**
     * Redirige vers la page d'inscription
     *
     * @return void
     */
    public static function redirectToRegister(): void
    {
        self::redirect('register.php');
    }

    /**
     * Redirige vers la page admin
     *
     * @return void
     */
    public static function redirectToAdmin(): void
    {
        self::redirect('admin.php');
    }

    /**
     * Redirige avec un message flash
     *
     * @param string $url URL de destination
     * @param string $type Type de message (success, error, info)
     * @param string $message Message à afficher
     * @return void
     */
    public static function redirectWithMessage(string $url, string $type, string $message): void
    {
        Session::setFlash($type, $message);
        self::redirect($url);
    }

    /**
     * Retourne une réponse JSON
     *
     * @param array $data Données à encoder en JSON
     * @param int $statusCode Code HTTP
     * @return void
     */
    public static function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    /**
     * Retourne une erreur JSON
     *
     * @param string $message Message d'erreur
     * @param int $statusCode Code HTTP (par défaut 400)
     * @return void
     */
    public static function jsonError(string $message, int $statusCode = 400): void
    {
        self::json(['error' => $message], $statusCode);
    }

    /**
     * Retourne un succès JSON
     *
     * @param string $message Message de succès
     * @param array $data Données supplémentaires
     * @return void
     */
    public static function jsonSuccess(string $message, array $data = []): void
    {
        self::json(array_merge(['success' => $message], $data), 200);
    }

    /**
     * Définit un code de statut HTTP
     *
     * @param int $code Code HTTP
     * @return void
     */
    public static function setStatusCode(int $code): void
    {
        http_response_code($code);
    }

    /**
     * Envoie un header personnalisé
     *
     * @param string $header En-tête HTTP
     * @return void
     */
    public static function setHeader(string $header): void
    {
        header($header);
    }
}

/*
===============================================================================
NOTES PÉDAGOGIQUES - CLASSE RESPONSE
===============================================================================

1. POURQUOI UNE CLASSE RESPONSE ?

   Avant (code procédural) :
   - header('Location: index.php'); exit(); dupliqué partout
   - Code répétitif et verbeux
   - Oubli fréquent de exit() après header()

   Après (POO) :
   - Méthodes courtes et expressives
   - Évite les oublis (exit() intégré)
   - Code plus lisible

2. REDIRECTIONS SIMPLES

   // Ancien code
   header('Location: index.php');
   exit();

   // Nouveau code
   Response::redirectToHome();

   Avantages :
   - Plus court
   - Plus lisible
   - exit() automatique (pas d'oubli)

3. REDIRECTIONS AVEC MESSAGE FLASH

   // Dans register.php, après inscription réussie
   use App\Utils\Response;

   Response::redirectWithMessage(
       'login.php',
       'success',
       'Registration successful! You can now login.'
   );

   // Dans login.php, afficher le message
   use App\Services\Session;

   $successMessage = Session::getFlash('success');
   if ($successMessage) {
       echo "<p style='color: green;'>$successMessage</p>";
   }

4. CODES DE STATUT HTTP

   - 200 : OK (succès)
   - 301 : Moved Permanently (redirection permanente)
   - 302 : Found (redirection temporaire) - par défaut
   - 400 : Bad Request (erreur client)
   - 401 : Unauthorized (non authentifié)
   - 403 : Forbidden (accès interdit)
   - 404 : Not Found (page non trouvée)
   - 500 : Internal Server Error (erreur serveur)

   // Redirection permanente
   Response::redirect('new-url.php', 301);

   // Définir un code d'erreur
   Response::setStatusCode(404);
   echo "Page not found";

5. RÉPONSES JSON (POUR LES API)

   // Retourner des données JSON
   Response::json(['users' => $users], 200);

   // Retourner une erreur JSON
   Response::jsonError('User not found', 404);

   // Retourner un succès JSON
   Response::jsonSuccess('User created', ['id' => $userId]);

6. EXEMPLE COMPLET AVEC LOGIN

   // Dans login.php
   use App\Utils\Response;
   use App\Services\Session;

   if ($_SERVER['REQUEST_METHOD'] === 'POST') {
       // ... validation et authentification ...

       if ($authSuccess) {
           Session::login($user['id'], $user['email'], $user['is_admin']);
           Response::redirectToHome();
       } else {
           $error = "Invalid email or password.";
       }
   }

7. EXEMPLE AVEC PROTECTION DE PAGE

   // Dans admin.php
   use App\Utils\Response;
   use App\Services\Session;

   Session::start();

   // Si non connecté ou pas admin
   if (!Session::isAuthenticated() || !Session::isAdmin()) {
       Response::redirectToHome();
   }

8. AVANTAGES

   ✅ Code plus court et lisible
   ✅ Évite les oublis (exit() automatique)
   ✅ Méthodes expressives (redirectToHome, redirectToLogin, etc.)
   ✅ Support des messages flash
   ✅ Support des réponses JSON (utile pour des API futures)
   ✅ Facilite les tests

===============================================================================
*/
