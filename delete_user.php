<?php
/**
 * ============================================================================
 * PAGE DE SUPPRESSION UTILISATEUR (DELETE_USER.PHP)
 * ============================================================================
 *
 * Cette page permet à un administrateur de supprimer un utilisateur.
 *
 * Processus :
 * 1. Vérifier que l'utilisateur est administrateur
 * 2. Vérifier qu'un ID a été fourni dans l'URL
 * 3. Vérifier que l'utilisateur à supprimer existe
 * 4. Empêcher l'admin de se supprimer lui-même (protection importante!)
 * 5. Supprimer l'utilisateur de la base de données
 * 6. Rediriger vers admin.php
 *
 * Protections :
 * - Accessible UNIQUEMENT aux administrateurs
 * - Vérifie que l'ID existe en base de données
 * - Empêche l'auto-suppression (un admin ne peut pas se supprimer)
 *
 * NOTE IMPORTANTE : Ce fichier ne contient PAS de HTML
 * Car il fait une suppression puis une redirection immédiate
 */

// ----------------------------------------------------------------------------
// DÉMARRER LA SESSION
// ----------------------------------------------------------------------------

// Démarrer la session pour accéder aux informations de l'utilisateur connecté
session_start();

// ============================================================================
// PROTECTION 1 : ADMINISTRATEUR REQUIS
// ============================================================================

/**
 * Cette page est UNIQUEMENT accessible aux administrateurs
 * Si un utilisateur normal ou non connecté tente d'y accéder, redirection
 */

// Vérifier que l'utilisateur est connecté ET est administrateur
// !isset() : vérifie qu'une variable N'EXISTE PAS
// || : OU logique
// $_SESSION['is_admin'] != 1 : vérifie que l'utilisateur N'EST PAS admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    // L'utilisateur n'est pas autorisé
    // Rediriger vers la page d'accueil
    header('Location: index.php');
    // Arrêter l'exécution du script
    exit();
}

// Si on arrive ici, l'utilisateur est bien administrateur ✓

// ----------------------------------------------------------------------------
// INCLURE LA BASE DE DONNÉES
// ----------------------------------------------------------------------------

// Inclure le fichier de connexion PDO
require_once 'db.php';

// ============================================================================
// PROTECTION 2 : VÉRIFIER QU'UN ID A ÉTÉ FOURNI
// ============================================================================

/**
 * Cette page est appelée avec un paramètre GET : delete_user.php?id=5
 * Si l'ID n'est pas fourni, on ne peut pas savoir qui supprimer
 */

// Vérifier si $_GET['id'] existe et n'est pas vide
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Pas d'ID fourni → impossible de savoir quel utilisateur supprimer
    // Rediriger vers la page admin
    header('Location: admin.php');
    exit();
}

// ----------------------------------------------------------------------------
// RÉCUPÉRER L'ID DE L'UTILISATEUR À SUPPRIMER
// ----------------------------------------------------------------------------

// Convertir l'ID en entier
// (int) : cast (conversion de type) vers entier
// Exemples :
// - (int)"5" = 5
// - (int)"abc" = 0
// - (int)"5abc" = 5
$user_id = (int)$_GET['id'];

// ============================================================================
// PROTECTION 3 : EMPÊCHER L'AUTO-SUPPRESSION
// ============================================================================

/**
 * Un administrateur ne doit PAS pouvoir se supprimer lui-même !
 *
 * Pourquoi ?
 * - S'il se supprime, il n'y aura peut-être plus d'admin
 * - Il sera déconnecté immédiatement
 * - Cela pourrait bloquer l'accès à l'administration
 *
 * Exemple de scénario problématique :
 * 1. L'admin (seul admin du site) se supprime par erreur
 * 2. Plus personne n'a accès à l'administration
 * 3. Il faut intervenir manuellement dans la base de données
 */

// Vérifier si l'ID à supprimer est le même que l'ID de l'admin connecté
// === : comparaison stricte (valeur ET type)
if ($user_id === $_SESSION['user_id']) {
    // L'admin essaie de se supprimer lui-même
    // On refuse et on redirige vers admin.php
    // NOTE : Idéalement, on devrait afficher un message d'erreur
    // Mais comme on redirige, le message serait perdu
    // En production, on pourrait utiliser une "session flash" pour les messages
    header('Location: admin.php');
    exit();
}

// Si on arrive ici, l'admin ne se supprime PAS lui-même ✓

// ----------------------------------------------------------------------------
// PROCESSUS DE SUPPRESSION
// ----------------------------------------------------------------------------

// On utilise try/catch pour gérer les erreurs de base de données
try {

    // ========================================================================
    // ÉTAPE 1 : VÉRIFIER QUE L'UTILISATEUR EXISTE
    // ========================================================================

    /**
     * Avant de supprimer, on vérifie que l'utilisateur existe réellement
     * Cela évite des erreurs si :
     * - L'ID est invalide (ex: delete_user.php?id=999)
     * - L'utilisateur a déjà été supprimé
     */

    // Préparer la requête pour chercher l'utilisateur
    // On récupère id et email (pour savoir qui on supprime)
    $stmt = $pdo->prepare("SELECT id, email FROM users WHERE id = :id");

    // Exécuter la requête avec l'ID
    $stmt->execute(['id' => $user_id]);

    // Récupérer le résultat
    // fetch() retourne un tableau associatif ou FALSE
    $user = $stmt->fetch();

    // ========================================================================
    // PROTECTION 4 : VÉRIFIER QUE L'UTILISATEUR EXISTE
    // ========================================================================

    // Si fetch() a retourné FALSE, l'utilisateur n'existe pas
    if (!$user) {
        // ID invalide ou utilisateur déjà supprimé
        // Rediriger vers admin.php
        header('Location: admin.php');
        exit();
    }

    // Si on arrive ici, l'utilisateur existe bien ✓

    // ========================================================================
    // ÉTAPE 2 : SUPPRIMER L'UTILISATEUR DE LA BASE DE DONNÉES
    // ========================================================================

    /**
     * Requête SQL DELETE
     *
     * Syntaxe : DELETE FROM nom_table WHERE condition
     *
     * ATTENTION : NE JAMAIS faire DELETE FROM users sans WHERE !
     * Cela supprimerait TOUS les utilisateurs !
     *
     * Ici, on supprime uniquement l'utilisateur avec l'ID spécifié
     */

    // Préparer la requête DELETE
    // :id est un placeholder pour l'ID de l'utilisateur
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");

    // Exécuter la suppression
    // PDO remplace :id par la valeur de $user_id
    // Exemple : DELETE FROM users WHERE id = 5
    $stmt->execute(['id' => $user_id]);

    // ========================================================================
    // ÉTAPE 3 : REDIRIGER VERS LA PAGE ADMIN
    // ========================================================================

    /**
     * La suppression a réussi !
     * On redirige l'administrateur vers la page admin.php
     * où il verra la liste mise à jour (sans l'utilisateur supprimé)
     */

    // Rediriger vers admin.php
    header('Location: admin.php');

    // Arrêter l'exécution
    // Important car sinon PHP continuerait à exécuter le code après header()
    exit();

} catch (PDOException $e) {

    // ========================================================================
    // GESTION DES ERREURS DE BASE DE DONNÉES
    // ========================================================================

    /**
     * Si une erreur SQL se produit, on redirige vers admin.php
     *
     * Exemples d'erreurs possibles :
     * - Connexion à la base perdue
     * - Table 'users' n'existe pas
     * - Contrainte de clé étrangère (si d'autres tables référencent cet utilisateur)
     *
     * En production, on devrait :
     * - Logger l'erreur dans un fichier : error_log($e->getMessage())
     * - Afficher un message à l'admin (avec session flash)
     *
     * Ici, on fait simple : on redirige silencieusement
     */

    // Rediriger vers admin.php
    header('Location: admin.php');

    // Arrêter l'exécution
    exit();
}

// ----------------------------------------------------------------------------
// FIN DU FICHIER
// ----------------------------------------------------------------------------

// Ce code ne sera JAMAIS exécuté car :
// - Soit on fait header() + exit() après la suppression
// - Soit on fait header() + exit() en cas d'erreur
// - Soit on fait header() + exit() en cas de protection
//
// Toutes les branches du code se terminent par exit()

/*
===============================================================================
NOTES PÉDAGOGIQUES - SUPPRESSION UTILISATEUR
===============================================================================

1. PROTECTIONS MULTIPLES :
   a. Vérifier que l'utilisateur est admin
   b. Vérifier qu'un ID a été fourni
   c. Vérifier que l'utilisateur à supprimer existe
   d. Empêcher l'auto-suppression (IMPORTANT!)

2. POURQUOI EMPÊCHER L'AUTO-SUPPRESSION ?
   - Un admin ne doit pas se supprimer lui-même
   - Risque : plus aucun admin dans le système
   - Blocage total de l'administration
   - Nécessiterait une intervention manuelle en base de données

3. REQUÊTE SQL DELETE :
   DELETE FROM nom_table WHERE condition

   DANGER : DELETE FROM users (sans WHERE)
   → Supprime TOUS les utilisateurs !

   SAFE : DELETE FROM users WHERE id = 5
   → Supprime uniquement l'utilisateur avec id = 5

4. DIFFÉRENCES AVEC LES AUTRES PAGES :
   - delete_user.php ne contient PAS de HTML
   - Pas de formulaire, pas d'affichage
   - Seulement : vérifications + suppression + redirection
   - C'est une "action page" (page d'action)

5. FLOW COMPLET :
   admin.php
      ↓ Clic sur "Delete" pour l'utilisateur ID=5
      ↓ Confirmation JavaScript : "Are you sure?"
      ↓ Si OK : navigation vers delete_user.php?id=5
   delete_user.php?id=5
      ↓ Vérifier : admin? ✓
      ↓ Vérifier : ID fourni? ✓
      ↓ Vérifier : pas auto-suppression? ✓
      ↓ Vérifier : utilisateur existe? ✓
      ↓ DELETE FROM users WHERE id = 5
      ↓ header('Location: admin.php')
      ↓ exit()
   admin.php (liste mise à jour)

6. GESTION DES ERREURS :
   - try/catch pour attraper les erreurs PDO
   - En cas d'erreur : redirection vers admin.php
   - En production : logger l'erreur
   - Pas de message d'erreur affiché (car on redirige)

7. SÉCURITÉ :
   - Protection admin
   - Requêtes préparées PDO (injection SQL)
   - Conversion (int) de l'ID
   - Protection auto-suppression
   - Vérification existence de l'utilisateur

8. CONFIRMATION JAVASCRIPT :
   Dans admin.php, le lien Delete a :
   onclick="return confirm('Are you sure...')"

   Mais cette protection peut être contournée !
   Un utilisateur malveillant peut accéder directement à :
   delete_user.php?id=5

   C'est pourquoi delete_user.php doit AUSSI vérifier :
   - Que l'utilisateur est admin
   - Que l'ID existe
   - Que ce n'est pas une auto-suppression

9. SESSION FLASH (non implémenté ici) :
   Pour afficher un message après la redirection :

   // Avant la redirection
   $_SESSION['flash_message'] = "User deleted successfully";
   header('Location: admin.php');

   // Dans admin.php
   if (isset($_SESSION['flash_message'])) {
       echo $_SESSION['flash_message'];
       unset($_SESSION['flash_message']); // Supprimer après affichage
   }

10. ALTERNATIVE : SOFT DELETE (non implémenté ici) :
    Au lieu de DELETE, on pourrait faire un "soft delete" :
    - Ajouter une colonne : deleted_at (DATE NULL)
    - UPDATE users SET deleted_at = NOW() WHERE id = 5
    - Modifier les requêtes : WHERE deleted_at IS NULL

    Avantages :
    - Récupération possible (annuler la suppression)
    - Historique conservé
    - Conformité RGPD (droit à l'oubli avec suppression définitive après X jours)

===============================================================================
*/
?>
