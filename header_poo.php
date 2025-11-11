<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Auth System - POO Version</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <nav>
        <ul>
            <li><a href="index_poo.php">Home</a></li>

            <?php
            require_once 'autoload.php';
            use App\Services\Auth;
            use App\Services\Session;

            Session::start();

            if (Auth::check()):
            ?>
                <!-- Menu pour utilisateurs connectés -->
                <li><a href="profile_poo.php">Profile</a></li>

                <?php if (Auth::isAdmin()): ?>
                    <li><a href="admin_poo.php">Admin</a></li>
                <?php endif; ?>

                <li><a href="logout_poo.php">Logout</a></li>
                <li style="margin-left: 20px;">
                    <em>Logged in as: <?php echo htmlspecialchars(Session::getUserEmail()); ?></em>
                </li>

            <?php else: ?>
                <!-- Menu pour visiteurs -->
                <li><a href="login_poo.php">Login</a></li>
                <li><a href="register_poo.php">Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
