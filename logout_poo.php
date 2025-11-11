<?php
/**
 * ============================================================================
 * PAGE DÉCONNEXION - VERSION POO
 * ============================================================================
 */

require_once 'autoload.php';

use App\Services\Auth;
use App\Utils\Response;

// Déconnecter l'utilisateur
Auth::logout();

// Rediriger vers l'accueil
Response::redirectToHome();
