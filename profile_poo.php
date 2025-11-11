<?php
/**
 * ============================================================================
 * PAGE PROFIL - VERSION POO
 * ============================================================================
 */

require_once 'autoload.php';

use App\Services\Auth;
use App\Services\Session;
use App\Services\Validator;
use App\Models\User;
use App\Utils\Response;

Session::start();
Auth::requireAuth();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newEmail = trim($_POST['email'] ?? '');
    $newPassword = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    $validator = new Validator();
    $updated = false;

    // Récupérer l'utilisateur actuel
    $user = Auth::user();

    // Valider et mettre à jour l'email si fourni
    if (!empty($newEmail)) {
        if ($validator->validateEmail($newEmail)) {
            // Vérifier que l'email n'est pas déjà utilisé par un autre utilisateur
            if (User::emailExists($newEmail, $user->getId())) {
                $validator->getErrors()[0] = "This email is already used by another account.";
            } else {
                $user->setEmail($newEmail);
                if ($user->updateEmail()) {
                    Session::set('email', $newEmail);
                    $updated = true;
                }
            }
        }
    }

    // Valider et mettre à jour le mot de passe si fourni
    if (!$validator->hasErrors() && !empty($newPassword)) {
        if ($validator->validatePassword($newPassword) &&
            $validator->validatePasswordMatch($newPassword, $confirmPassword)) {

            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            $user->setPassword($hashedPassword);

            if ($user->updatePassword()) {
                $updated = true;
            }
        }
    }

    if ($validator->hasErrors()) {
        $error = $validator->getFirstError();
    } elseif ($updated) {
        $success = "Profile updated successfully!";
    } else {
        $error = "No changes to update.";
    }
}

include_once 'header.php';
?>

<main>
    <h2>Edit Profile - Version POO</h2>

    <?php if (!empty($error)): ?>
        <p style="color: red;"><strong>Error:</strong> <?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <p style="color: green;"><strong><?php echo htmlspecialchars($success); ?></strong></p>
    <?php endif; ?>

    <p><strong>Current email:</strong> <?php echo htmlspecialchars(Session::getUserEmail()); ?></p>

    <form method="POST" action="profile_poo.php">
        <h3>Change Email</h3>
        <p>
            <label for="email">New Email (leave empty to keep current):</label><br>
            <input type="email" id="email" name="email">
        </p>

        <h3>Change Password</h3>
        <p>
            <label for="password">New Password (leave empty to keep current):</label><br>
            <input type="password" id="password" name="password">
        </p>

        <p>
            <label for="confirm_password">Confirm New Password:</label><br>
            <input type="password" id="confirm_password" name="confirm_password">
        </p>

        <p>
            <small>Password must be at least 8 characters, contain one uppercase letter and one special character.</small>
        </p>

        <p>
            <button type="submit">Update Profile</button>
        </p>
    </form>

    <p><a href="index.php">Back to Home</a></p>
</main>

</body>
</html>
