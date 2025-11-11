<?php
/**
 * ============================================================================
 * PAGE D'INSCRIPTION - VERSION POO
 * ============================================================================
 *
 * Cette version utilise la Programmation Orientée Objet (POO).
 *
 * Changements par rapport à la version procédurale :
 * - Validation centralisée dans Validator
 * - Logique d'inscription dans Auth::register()
 * - Gestion de session avec Session
 * - Code beaucoup plus court et lisible
 */

// Charger l'autoloader
require_once 'autoload.php';

// Importer les classes nécessaires
use App\Services\Auth;
use App\Utils\Response;

// Variables pour les messages
$error = '';
$success = '';

// Traiter le formulaire si soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Récupérer les données du formulaire
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Tenter l'inscription
    $result = Auth::register($email, $password, $confirmPassword);

    if ($result['success']) {
        // Inscription réussie → afficher message de succès
        $success = $result['message'] . ' You can now <a href="login_poo.php">login</a>.';
    } else {
        // Inscription échouée → afficher l'erreur
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

    <h2>Register - Version POO</h2>

    <?php if (!empty($error)): ?>
        <p style="color: red;">
            <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
        </p>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <p style="color: green;">
            <strong><?php echo $success; ?></strong>
        </p>
    <?php endif; ?>

    <form method="POST" action="register_poo.php">

        <p>
            <label for="email">Email:</label><br>
            <input type="email" id="email" name="email" required>
        </p>

        <p>
            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required>
        </p>

        <p>
            <label for="confirm_password">Confirm Password:</label><br>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </p>

        <p>
            <button type="submit">Register</button>
        </p>

    </form>

    <p>Already have an account? <a href="login_poo.php">Login here</a></p>

</main>

</body>
</html>

<!--
===============================================================================
COMPARAISON PROCÉDURAL vs POO
===============================================================================

PROCÉDURAL (register.php) - environ 330 lignes :
- Validation manuelle de chaque champ :
  if (empty($email)) { $error = "Email is required."; }
  elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { ... }
  elseif (empty($password)) { ... }
  elseif (strlen($password) < 8) { ... }
  elseif (!preg_match('/[A-Z]/', $password)) { ... }
  elseif (!preg_match('/[!@#$...]/', $password)) { ... }
  elseif ($password !== $confirm_password) { ... }

- Vérification email existant :
  $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
  $stmt->execute(['email' => $email]);
  $existing_user = $stmt->fetch();
  if ($existing_user) { $error = "Email already registered."; }

- Hash du mot de passe :
  $hashed_password = password_hash($password, PASSWORD_BCRYPT);

- Insertion en base :
  $stmt = $pdo->prepare("INSERT INTO users (email, password, is_admin) VALUES (:email, :password, 0)");
  $stmt->execute(['email' => $email, 'password' => $hashed_password]);

POO (register_poo.php) - environ 80 lignes (dont 45 de commentaires) :
- Toute la logique est dans Auth::register() :
  $result = Auth::register($email, $password, $confirmPassword);

- Auth::register() fait tout :
  • Validation avec Validator
  • Vérification email avec User::emailExists()
  • Hash du mot de passe
  • Création avec User->create()

AVANTAGES DE LA VERSION POO :
✅ Code 4x plus court
✅ Logique réutilisable (peut être utilisée dans une API, CLI, etc.)
✅ Validation centralisée et cohérente
✅ Tests unitaires faciles
✅ Modification en un seul endroit
✅ Code plus maintenable

PRINCIPE DRY (Don't Repeat Yourself) :
La version POO respecte le principe DRY car toute la logique d'inscription
est dans Auth::register(). Si on crée une API, on peut réutiliser la même
méthode au lieu de dupliquer tout le code de validation.

===============================================================================
-->
