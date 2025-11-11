<?php
/**
 * ============================================================================
 * PAGE D'ACCUEIL - VERSION POO
 * ============================================================================
 */

require_once 'autoload.php';

use App\Services\Auth;
use App\Services\Session;

Session::start();

// Récupérer les messages flash s'ils existent
$successMessage = Session::getFlash('success');
$errorMessage = Session::getFlash('error');

include_once 'header_poo.php';
?>

<main>

    <h2>Welcome to User Authentication System - Version POO</h2>

    <?php if ($successMessage): ?>
        <p style="color: green;">
            <strong><?php echo htmlspecialchars($successMessage); ?></strong>
        </p>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
        <p style="color: red;">
            <strong><?php echo htmlspecialchars($errorMessage); ?></strong>
        </p>
    <?php endif; ?>

    <?php if (Auth::check()): ?>
        <!-- Utilisateur connecté -->
        <p>Hello, <strong><?php echo htmlspecialchars(Session::getUserEmail()); ?></strong>!</p>

        <p>You are logged in. You can:</p>
        <ul>
            <li><a href="profile_poo.php">Edit your profile</a></li>
            <?php if (Auth::isAdmin()): ?>
                <li><a href="admin_poo.php">Admin Dashboard</a> (Admin only)</li>
            <?php endif; ?>
            <li><a href="logout_poo.php">Logout</a></li>
        </ul>

        <h3>Statistics</h3>
        <?php
        use App\Models\User;
        $totalUsers = User::count();
        ?>
        <p>Total registered users: <strong><?php echo $totalUsers; ?></strong></p>

    <?php else: ?>
        <!-- Utilisateur non connecté -->
        <p>This is a PHP authentication system demonstrating Object-Oriented Programming (OOP).</p>

        <h3>Get Started</h3>
        <ul>
            <li><a href="register_poo.php">Register</a> - Create a new account</li>
            <li><a href="login_poo.php">Login</a> - Sign in to your account</li>
        </ul>

        <h3>Features</h3>
        <ul>
            <li>✅ User Registration with validation</li>
            <li>✅ Secure Login with password hashing</li>
            <li>✅ Profile Management</li>
            <li>✅ Admin Dashboard</li>
            <li>✅ OOP Architecture (Classes, Namespaces, Autoloading)</li>
        </ul>

    <?php endif; ?>

    <hr>

    <h3>About This Project</h3>
    <p>
        This project has been <strong>refactored to OOP</strong> to demonstrate:
    </p>
    <ul>
        <li><strong>Classes and Objects</strong> - User, Auth, Session, Validator, etc.</li>
        <li><strong>Encapsulation</strong> - Private properties with getters/setters</li>
        <li><strong>Design Patterns</strong> - Singleton (Database), Active Record (User), Service Layer (Auth)</li>
        <li><strong>Namespaces</strong> - Code organization (App\Models, App\Services, etc.)</li>
        <li><strong>Autoloading</strong> - PSR-4 autoloading standard</li>
        <li><strong>Code Reusability</strong> - DRY principle (Don't Repeat Yourself)</li>
    </ul>

    <p>
        📚 <strong>Read the full documentation:</strong>
        <a href="README_POO.md" target="_blank">README_POO.md</a>
    </p>

</main>

</body>
</html>
