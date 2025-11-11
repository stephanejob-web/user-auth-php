<?php
/**
 * ============================================================================
 * PAGE BASCULER STATUT ADMIN - VERSION POO
 * ============================================================================
 */

require_once 'autoload.php';

use App\Services\Auth;
use App\Services\Session;
use App\Models\User;
use App\Utils\Response;

Session::start();
Auth::requireAdmin();

// Récupérer l'ID de l'utilisateur
$userId = (int) ($_GET['id'] ?? 0);

// Vérifier que l'ID est valide
if ($userId <= 0) {
    Response::redirectWithMessage('admin_poo.php', 'error', 'Invalid user ID.');
}

// Empêcher un admin de modifier son propre statut
if ($userId == Auth::id()) {
    Response::redirectWithMessage('admin_poo.php', 'error', 'You cannot change your own admin status.');
}

// Récupérer l'utilisateur
$user = User::findById($userId);

if (!$user) {
    Response::redirectWithMessage('admin_poo.php', 'error', 'User not found.');
}

// Basculer le statut admin
if ($user->toggleAdmin()) {
    $message = $user->isAdmin() ? 'User promoted to admin.' : 'Admin rights removed.';
    Response::redirectWithMessage('admin_poo.php', 'success', $message);
} else {
    Response::redirectWithMessage('admin_poo.php', 'error', 'Error updating user status.');
}
