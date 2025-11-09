<?php
/**
 * ============================================================================
 * BASCULER LE STATUT ADMIN (TOGGLE_ADMIN.PHP)
 * ============================================================================
 *
 * Ce fichier permet de changer rapidement le statut admin d'un utilisateur.
 * Il inverse le statut actuel :
 * - Si l'utilisateur est admin (is_admin = 1) → devient utilisateur normal (0)
 * - Si l'utilisateur est normal (is_admin = 0) → devient admin (1)
 *
 * Utilisation : toggle_admin.php?id=5
 * Ensuite redirection vers admin.php
 */

// ----------------------------------------------------------------------------
// DÉMARRER LA SESSION
// ----------------------------------------------------------------------------

session_start();

// ============================================================================
// PROTECTION : ADMINISTRATEUR REQUIS
// ============================================================================

// Vérifier que l'utilisateur connecté est administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    // Non autorisé → retour à l'accueil
    header('Location: ../index.php');
    exit();
}

// ----------------------------------------------------------------------------
// INCLURE LA BASE DE DONNÉES
// ----------------------------------------------------------------------------

require_once '../config/db.php';

// ============================================================================
// VÉRIFIER QU'UN ID A ÉTÉ FOURNI
// ============================================================================

/**
 * Ce fichier est appelé avec : toggle_admin.php?id=5
 * On doit vérifier que l'ID existe dans l'URL
 */
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Pas d'ID fourni → retour au panneau admin
    header('Location: admin.php');
    exit();
}

// Récupérer l'ID et le convertir en entier
$user_id = (int)$_GET['id'];

// ============================================================================
// PROTECTION : NE PAS SE DÉSACTIVER SOI-MÊME
// ============================================================================

/**
 * IMPORTANT : Un admin ne doit pas pouvoir se retirer ses propres droits admin
 * Sinon il pourrait se bloquer lui-même et ne plus pouvoir gérer le système !
 */
if ($user_id == $_SESSION['user_id']) {
    // Tentative de modifier son propre statut → refusé
    header('Location: admin.php');
    exit();
}

// ============================================================================
// BASCULER LE STATUT ADMIN
// ============================================================================

try {

    // ========================================================================
    // ÉTAPE 1 : RÉCUPÉRER LE STATUT ACTUEL
    // ========================================================================

    $stmt = $pdo->prepare("SELECT id, email, is_admin FROM users WHERE id = :id");
    $stmt->execute(['id' => $user_id]);
    $user = $stmt->fetch();

    // Vérifier que l'utilisateur existe
    if (!$user) {
        // ID invalide → retour au panneau admin
        header('Location: admin.php');
        exit();
    }

    // ========================================================================
    // ÉTAPE 2 : INVERSER LE STATUT
    // ========================================================================

    /**
     * Logique d'inversion :
     * - Si is_admin = 1 → nouveau statut = 0 (retirer les droits admin)
     * - Si is_admin = 0 → nouveau statut = 1 (donner les droits admin)
     *
     * Opérateur ternaire :
     * condition ? valeur_si_vrai : valeur_si_faux
     */
    $new_status = $user['is_admin'] == 1 ? 0 : 1;

    // ========================================================================
    // ÉTAPE 3 : METTRE À JOUR DANS LA BASE DE DONNÉES
    // ========================================================================

    $stmt = $pdo->prepare("UPDATE users SET is_admin = :is_admin WHERE id = :id");
    $stmt->execute([
        'is_admin' => $new_status,
        'id' => $user_id
    ]);

    // ========================================================================
    // ÉTAPE 4 : REDIRECTION VERS LA PAGE ADMIN
    // ========================================================================

    /**
     * Redirection vers admin.php
     * On pourrait ajouter un message de succès, mais pour simplifier
     * on redirige directement
     */
    header('Location: admin.php');
    exit();

} catch (PDOException $e) {

    // ========================================================================
    // GESTION DES ERREURS
    // ========================================================================

    // En cas d'erreur SQL, rediriger vers admin.php
    // En production, on devrait logger l'erreur
    header('Location: admin.php');
    exit();
}

?>

<!--
===============================================================================
NOTES PÉDAGOGIQUES - BASCULER LE STATUT ADMIN
===============================================================================

1. POURQUOI CE FICHIER ?
   - Pour changer le statut admin RAPIDEMENT depuis admin.php
   - Sans passer par le formulaire edit_user.php
   - Un seul clic = changement de statut

2. COMMENT ÇA FONCTIONNE ?
   Étape 1 : L'admin clique sur "Make Admin" ou "Remove Admin" dans admin.php
   Étape 2 : Appel à toggle_admin.php?id=5
   Étape 3 : Ce fichier récupère l'ID (5)
   Étape 4 : Il lit le statut actuel (is_admin = 0 ou 1)
   Étape 5 : Il inverse le statut (0 → 1 ou 1 → 0)
   Étape 6 : Il met à jour la base de données
   Étape 7 : Redirection vers admin.php

3. PROTECTION IMPORTANTE :
   if ($user_id == $_SESSION['user_id'])
   - Empêche un admin de se retirer ses propres droits
   - Sinon il pourrait se bloquer et ne plus avoir accès au panneau admin
   - C'est une protection de sécurité essentielle

4. INVERSION DU STATUT :
   $new_status = $user['is_admin'] == 1 ? 0 : 1;

   Exemples :
   - Si is_admin = 1 → $new_status = 0 (retirer admin)
   - Si is_admin = 0 → $new_status = 1 (donner admin)

5. REQUÊTE SQL :
   UPDATE users SET is_admin = :is_admin WHERE id = :id
   - Met à jour UNIQUEMENT la colonne is_admin
   - Pour l'utilisateur avec l'ID spécifié
   - Utilise des paramètres préparés pour la sécurité

6. REDIRECTION :
   header('Location: admin.php');
   exit();
   - Retourne toujours vers admin.php
   - exit() est OBLIGATOIRE après header()
   - Sinon le code continuerait à s'exécuter

7. DIFFÉRENCE AVEC EDIT_USER.PHP :
   edit_user.php                 toggle_admin.php
   -------------------------------------------------------
   - Formulaire complet          - Pas de formulaire
   - Modifie email, password,    - Modifie UNIQUEMENT is_admin
     is_admin
   - Affiche une page HTML       - Redirige directement
   - Plus lent (plusieurs clics) - Plus rapide (un seul clic)

8. SÉCURITÉ :
   ✓ Vérification que l'utilisateur est admin
   ✓ Vérification que l'ID existe
   ✓ Protection anti-auto-désactivation
   ✓ Requêtes préparées (injection SQL impossible)
   ✓ Conversion en entier : (int)$_GET['id']

===============================================================================
-->
