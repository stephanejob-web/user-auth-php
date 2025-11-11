<?php
/**
 * ============================================================================
 * PAGE DE CONNEXION - VERSION POO
 * ============================================================================
 *
 * Cette version utilise la Programmation Orientée Objet (POO).
 *
 * Comparé à l'ancienne version procédurale :
 * - Beaucoup plus court et lisible
 * - Logique centralisée dans les classes
 * - Code réutilisable
 * - Facilite la maintenance
 */

// Charger l'autoloader pour les classes
require_once 'autoload.php';

// Importer les classes nécessaires
use App\Services\Auth;
use App\Services\Session;
use App\Utils\Response;

// Démarrer la session
Session::start();

// Variable pour stocker les erreurs
$error = '';

// Traiter le formulaire si soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Récupérer les données du formulaire
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Tenter la connexion
    $result = Auth::login($email, $password);

    if ($result['success']) {
        // Connexion réussie → rediriger vers l'accueil
        Response::redirectToHome();
    } else {
        // Connexion échouée → afficher l'erreur
        $error = $result['message'];
    }
}

// Inclure le header HTML
include_once 'header.php';
?>

<!-- ========================================================================== -->
<!-- CONTENU HTML DE LA PAGE -->
<!-- ========================================================================== -->

<main>

    <h2>Login - Version POO</h2>

    <?php if (!empty($error)): ?>
        <p style="color: red;">
            <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="login_poo.php">

        <p>
            <label for="email">Email:</label><br>
            <input type="email" id="email" name="email" required>
        </p>

        <p>
            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required>
        </p>

        <p>
            <button type="submit">Login</button>
        </p>

    </form>

    <p>Don't have an account? <a href="register_poo.php">Register here</a></p>

</main>

</body>
</html>

<!--
===============================================================================
COMPARAISON PROCÉDURAL vs POO
===============================================================================

PROCÉDURAL (login.php) - environ 220 lignes :
- session_start()
- require_once 'db.php'
- $error = ''
- if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $email = trim($_POST['email']);
      $password = $_POST['password'];

      if (empty($email)) {
          $error = "Email is required.";
      } elseif (empty($password)) {
          $error = "Password is required.";
      } else {
          try {
              $stmt = $pdo->prepare("SELECT id, email, password, is_admin FROM users WHERE email = :email");
              $stmt->execute(['email' => $email]);
              $user = $stmt->fetch();

              if (!$user) {
                  $error = "Invalid email or password.";
              } elseif (!password_verify($password, $user['password'])) {
                  $error = "Invalid email or password.";
              } else {
                  $_SESSION['user_id'] = $user['id'];
                  $_SESSION['email'] = $user['email'];
                  $_SESSION['is_admin'] = $user['is_admin'];
                  header('Location: index.php');
                  exit();
              }
          } catch (PDOException $e) {
              $error = "An error occurred. Please try again.";
          }
      }
  }

POO (login_poo.php) - environ 70 lignes (dont 40 de commentaires) :
- require_once 'autoload.php'
- use App\Services\Auth;
- Session::start();
- $result = Auth::login($email, $password);
- if ($result['success']) {
      Response::redirectToHome();
  }

AVANTAGES DE LA VERSION POO :
✅ Code 3x plus court
✅ Plus lisible et expressif
✅ Logique réutilisable (Auth::login peut être utilisé ailleurs)
✅ Facilite les tests unitaires
✅ Séparation des responsabilités claire
✅ Modification centralisée (changer la logique d'auth en un seul endroit)

===============================================================================
-->
