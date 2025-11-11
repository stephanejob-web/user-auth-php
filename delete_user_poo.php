<?php
/**
 * ============================================================================
 * PAGE SUPPRESSION UTILISATEUR - VERSION POO
 * ============================================================================
 */

require_once 'autoload.php';

use App\Services\Auth;
use App\Services\Session;
use App\Models\User;
use App\Utils\Response;

Session::start();
Auth::requireAdmin();

// Récupérer l'ID de l'utilisateur à supprimer
$userId = (int) ($_GET['id'] ?? 0);

// Vérifier que l'ID est valide
if ($userId <= 0) {
    Response::redirectWithMessage('admin_poo.php', 'error', 'Invalid user ID.');
}

// Empêcher un admin de se supprimer lui-même
if ($userId == Auth::id()) {
    Response::redirectWithMessage('admin_poo.php', 'error', 'You cannot delete your own account.');
}

// Récupérer l'utilisateur
$user = User::findById($userId);

if (!$user) {
    Response::redirectWithMessage('admin_poo.php', 'error', 'User not found.');
}

// Supprimer l'utilisateur
if ($user->delete()) {
    Response::redirectWithMessage('admin_poo.php', 'success', 'User deleted successfully.');
} else {
    Response::redirectWithMessage('admin_poo.php', 'error', 'Error deleting user.');
}
