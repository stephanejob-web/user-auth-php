<?php
/**
 * ============================================================================
 * PAGE ÉDITION UTILISATEUR - VERSION POO
 * ============================================================================
 */

require_once 'autoload.php';

use App\Services\Auth;
use App\Services\Session;
use App\Services\Validator;
use App\Models\User;
use App\Utils\Response;

Session::start();
Auth::requireAdmin();

$error = '';
$success = '';

// Récupérer l'ID de l'utilisateur à éditer
$userId = (int) ($_GET['id'] ?? 0);

if ($userId <= 0) {
    Response::redirectWithMessage('admin_poo.php', 'error', 'Invalid user ID.');
}

// Récupérer l'utilisateur
$user = User::findById($userId);

if (!$user) {
    Response::redirectWithMessage('admin_poo.php', 'error', 'User not found.');
}

// Traiter le formulaire si soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newEmail = trim($_POST['email'] ?? '');
    $newPassword = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    $validator = new Validator();
    $updated = false;

    // Valider et mettre à jour l'email
    if (!empty($newEmail) && $newEmail !== $user->getEmail()) {
        if ($validator->validateEmail($newEmail)) {
            if (User::emailExists($newEmail, $user->getId())) {
                $error = "This email is already used by another account.";
            } else {
                $user->setEmail($newEmail);
                $updated = true;
            }
        }
    }

    // Valider et mettre à jour le mot de passe si fourni
    if (empty($error) && !empty($newPassword)) {
        if ($validator->validatePassword($newPassword) &&
            $validator->validatePasswordMatch($newPassword, $confirmPassword)) {

            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            $user->setPassword($hashedPassword);
            $updated = true;
        } else {
            $error = $validator->getFirstError();
        }
    }

    // Sauvegarder les modifications
    if (empty($error) && $updated) {
        if ($user->update()) {
            $success = "User updated successfully!";
        } else {
            $error = "Error updating user.";
        }
    } elseif (empty($error) && !$updated) {
        $error = "No changes to update.";
    }

    if ($validator->hasErrors() && empty($error)) {
        $error = $validator->getFirstError();
    }
}

include_once 'header.php';
?>

<main>
    <h2>Edit User - Version POO</h2>

    <?php if (!empty($error)): ?>
        <p style="color: red;"><strong>Error:</strong> <?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <p style="color: green;"><strong><?php echo htmlspecialchars($success); ?></strong></p>
    <?php endif; ?>

    <p><strong>User ID:</strong> <?php echo $user->getId(); ?></p>
    <p><strong>Current Email:</strong> <?php echo htmlspecialchars($user->getEmail()); ?></p>
    <p><strong>Admin Status:</strong> <?php echo $user->isAdmin() ? 'Yes' : 'No'; ?></p>

    <form method="POST" action="edit_user_poo.php?id=<?php echo $user->getId(); ?>">

        <h3>Change Email</h3>
        <p>
            <label for="email">New Email (leave empty to keep current):</label><br>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user->getEmail()); ?>">
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
            <button type="submit">Update User</button>
        </p>
    </form>

    <p>
        <a href="admin_poo.php">Back to Admin Dashboard</a> |
        <a href="index.php">Back to Home</a>
    </p>
</main>

</body>
</html>
