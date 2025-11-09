<?php
/**
 * ============================================================================
 * PAGE ADMINISTRATION (ADMIN.PHP)
 * ============================================================================
 *
 * Cette page affiche la liste de tous les utilisateurs enregistrés.
 * Elle permet aux administrateurs de :
 * - Voir tous les utilisateurs
 * - Éditer un utilisateur (email, statut admin, mot de passe)
 * - Supprimer un utilisateur
 *
 * Protections :
 * - Page accessible UNIQUEMENT aux administrateurs (is_admin = 1)
 * - Si utilisateur non connecté → redirection vers index.php
 * - Si utilisateur connecté mais PAS admin → redirection vers index.php
 */

// ----------------------------------------------------------------------------
// DÉMARRER LA SESSION
// ----------------------------------------------------------------------------

// Démarrer la session pour accéder aux informations de l'utilisateur connecté
session_start();

// ============================================================================
// PROTECTION DE LA PAGE : ADMINISTRATEUR REQUIS
// ============================================================================

/**
 * Double vérification de sécurité :
 * 1. L'utilisateur doit être connecté ($_SESSION['user_id'] existe)
 * 2. L'utilisateur doit être administrateur ($_SESSION['is_admin'] == 1)
 *
 * Si l'une des deux conditions n'est PAS remplie, on redirige vers index.php
 */

// Vérifier si l'utilisateur est connecté ET est administrateur
// !isset() : l'utilisateur n'est PAS connecté
// || : OU logique (si l'une des deux conditions est vraie)
// $_SESSION['is_admin'] != 1 : l'utilisateur n'est PAS admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    // Une des deux conditions est vraie → accès refusé

    // Rediriger vers la page d'accueil
    header('Location: index.php');

    // Arrêter l'exécution
    exit();
}

// Si on arrive ici, c'est que l'utilisateur est connecté ET administrateur ✓
// On peut afficher la page d'administration en toute sécurité

// ----------------------------------------------------------------------------
// INCLURE LA BASE DE DONNÉES
// ----------------------------------------------------------------------------

// Inclure le fichier de connexion PDO
require_once 'db.php';

// ----------------------------------------------------------------------------
// RÉCUPÉRER LA LISTE DE TOUS LES UTILISATEURS
// ----------------------------------------------------------------------------

// On utilise try/catch pour gérer les erreurs de base de données
try {

    // ========================================================================
    // PRÉPARER ET EXÉCUTER LA REQUÊTE
    // ========================================================================

    /**
     * Requête SQL pour récupérer tous les utilisateurs
     *
     * Colonnes récupérées :
     * - id : pour les liens Edit et Delete
     * - email : adresse email de l'utilisateur
     * - is_admin : statut administrateur (0 ou 1)
     * - created_at : date de création du compte
     *
     * ORDER BY id DESC :
     * - Trie par ID décroissant (du plus grand au plus petit)
     * - Effet : les utilisateurs les plus récents apparaissent en premier
     * - Sans ORDER BY, l'ordre serait imprévisible
     */

    // Préparer la requête
    // Ici, pas de placeholder car on ne filtre pas les résultats
    $stmt = $pdo->prepare("SELECT id, email, is_admin, created_at FROM users ORDER BY id DESC");

    // Exécuter la requête
    // Pas de paramètres à passer car pas de placeholders
    $stmt->execute();

    // fetchAll() : récupère TOUS les résultats dans un tableau
    // Différence avec fetch() : fetch() récupère UNE seule ligne
    // Retourne un tableau de tableaux associatifs
    // Exemple : [
    //   ['id' => 3, 'email' => 'user3@example.com', 'is_admin' => 0, 'created_at' => '2024-01-03 10:00:00'],
    //   ['id' => 2, 'email' => 'user2@example.com', 'is_admin' => 1, 'created_at' => '2024-01-02 09:00:00'],
    //   ['id' => 1, 'email' => 'admin@example.com', 'is_admin' => 1, 'created_at' => '2024-01-01 08:00:00']
    // ]
    $users = $stmt->fetchAll();

} catch (PDOException $e) {
    // ========================================================================
    // GESTION DES ERREURS
    // ========================================================================

    // En cas d'erreur SQL, initialiser un tableau vide
    // Cela évite une erreur PHP "undefined variable" plus tard
    $users = [];

    // Stocker le message d'erreur
    // En production, on devrait logger $e->getMessage() dans un fichier
    $error = "Error loading users.";
}

// ----------------------------------------------------------------------------
// INCLURE LE HEADER
// ----------------------------------------------------------------------------

// Maintenant qu'on a récupéré les données, on peut afficher le HTML
include_once 'header.php';
?>

<!-- ========================================================================== -->
<!-- CONTENU HTML DE LA PAGE ADMINISTRATION -->
<!-- ========================================================================== -->

<main>

    <h2>Admin Dashboard</h2>

    <!-- Texte descriptif de la page -->
    <p>Manage all users in the system.</p>

    <?php
    /**
     * ====================================================================
     * AFFICHER LE MESSAGE D'ERREUR SI LA RÉCUPÉRATION A ÉCHOUÉ
     * ====================================================================
     */

    // Si une erreur s'est produite lors de la récupération des utilisateurs
    if (isset($error)):
    ?>

        <p style="color: red;">
            <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
        </p>

    <?php
    endif;
    ?>

    <?php
    /**
     * ====================================================================
     * AFFICHER LA LISTE DES UTILISATEURS
     * ====================================================================
     */

    // Vérifier s'il y a au moins un utilisateur
    // count() retourne le nombre d'éléments dans un tableau
    // count([]) retourne 0
    // count(['a', 'b', 'c']) retourne 3
    if (count($users) > 0):
    ?>

        <!-- ============================================================== -->
        <!-- TABLEAU HTML POUR AFFICHER LES UTILISATEURS -->
        <!-- ============================================================== -->

        <!--
        <table> : balise pour créer un tableau HTML
        - border="1" : ajoute une bordure de 1 pixel (ancien attribut HTML, mais simple)
        - cellpadding="10" : espace de 10 pixels à l'intérieur des cellules
        - cellspacing="0" : pas d'espace entre les cellules

        Note : En production, on utiliserait CSS pour le style
        Mais selon le cahier des charges, on n'utilise pas de CSS
        -->
        <table border="1" cellpadding="10" cellspacing="0">

            <!-- ========================================================== -->
            <!-- EN-TÊTE DU TABLEAU -->
            <!-- ========================================================== -->

            <thead>
                <!-- <thead> : en-tête du tableau (pour les titres de colonnes) -->

                <tr>
                    <!-- <tr> : Table Row (ligne du tableau) -->

                    <!-- <th> : Table Header (cellule d'en-tête, texte en gras par défaut) -->
                    <th>ID</th>
                    <th>Email</th>
                    <th>Admin</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>

            </thead>

            <!-- ========================================================== -->
            <!-- CORPS DU TABLEAU (DONNÉES) -->
            <!-- ========================================================== -->

            <tbody>
                <!-- <tbody> : corps du tableau (pour les données) -->

                <?php
                /**
                 * ----------------------------------------------------
                 * BOUCLE POUR AFFICHER CHAQUE UTILISATEUR
                 * ----------------------------------------------------
                 *
                 * foreach : parcourt chaque élément d'un tableau
                 * Syntaxe : foreach ($tableau as $element)
                 *
                 * À chaque itération :
                 * - $user contient un utilisateur (tableau associatif)
                 * - Exemple : ['id' => 3, 'email' => 'test@example.com', ...]
                 */

                foreach ($users as $user):
                ?>

                    <!-- Une ligne (<tr>) par utilisateur -->
                    <tr>

                        <!-- ============================================ -->
                        <!-- COLONNE 1 : ID -->
                        <!-- ============================================ -->

                        <!-- <td> : Table Data (cellule de données) -->
                        <td>
                            <?php
                            // Afficher l'ID de l'utilisateur
                            // $user['id'] accède à la clé 'id' du tableau associatif
                            echo htmlspecialchars($user['id']);
                            // htmlspecialchars() pour la sécurité, même si c'est un nombre
                            ?>
                        </td>

                        <!-- ============================================ -->
                        <!-- COLONNE 2 : EMAIL -->
                        <!-- ============================================ -->

                        <td>
                            <?php
                            // Afficher l'email de l'utilisateur
                            echo htmlspecialchars($user['email']);
                            // IMPORTANT : htmlspecialchars() car c'est une donnée utilisateur
                            ?>
                        </td>

                        <!-- ============================================ -->
                        <!-- COLONNE 3 : STATUT ADMIN -->
                        <!-- ============================================ -->

                        <td>
                            <?php
                            /**
                             * Afficher "Yes" si admin, "No" sinon
                             *
                             * Opérateur ternaire : condition ? valeur_si_vrai : valeur_si_faux
                             * Équivalent à :
                             * if ($user['is_admin'] == 1) {
                             *     echo 'Yes';
                             * } else {
                             *     echo 'No';
                             * }
                             *
                             * Mais plus concis !
                             */

                            echo $user['is_admin'] == 1 ? 'Yes' : 'No';
                            // is_admin = 1 → 'Yes'
                            // is_admin = 0 → 'No'
                            ?>
                        </td>

                        <!-- ============================================ -->
                        <!-- COLONNE 4 : DATE DE CRÉATION -->
                        <!-- ============================================ -->

                        <td>
                            <?php
                            // Afficher la date de création du compte
                            // created_at est au format : "2024-01-15 14:30:00"
                            echo htmlspecialchars($user['created_at']);
                            ?>
                        </td>

                        <!-- ============================================ -->
                        <!-- COLONNE 5 : ACTIONS -->
                        <!-- ============================================ -->

                        <td>

                            <!-- ======================================== -->
                            <!-- LIEN EDIT (MODIFIER) -->
                            <!-- ======================================== -->

                            <!--
                            Lien vers edit_user.php avec l'ID de l'utilisateur en paramètre GET
                            Exemple : edit_user.php?id=5

                            En PHP, on récupérera l'ID avec $_GET['id']
                            -->
                            <a href="edit_user.php?id=<?php echo $user['id']; ?>">Edit</a>

                            <!--
                            Le caractère | (pipe) sert de séparateur visuel entre les deux liens
                            Ce n'est pas une balise HTML, juste du texte
                            -->
                            |

                            <!-- ======================================== -->
                            <!-- LIEN TOGGLE ADMIN (BASCULER STATUT) -->
                            <!-- ======================================== -->

                            <?php
                            /**
                             * Afficher "Make Admin" ou "Remove Admin" selon le statut actuel
                             *
                             * Protection : Ne pas afficher ce lien pour l'utilisateur connecté
                             * (un admin ne peut pas se retirer ses propres droits)
                             */
                            if ($user['id'] != $_SESSION['user_id']):
                            ?>

                                <?php if ($user['is_admin'] == 1): ?>
                                    <!-- L'utilisateur est admin → proposer de retirer les droits -->
                                    <a href="toggle_admin.php?id=<?php echo $user['id']; ?>"
                                       onclick="return confirm('Remove admin rights for this user?');">Remove Admin</a>
                                <?php else: ?>
                                    <!-- L'utilisateur est normal → proposer de donner les droits admin -->
                                    <a href="toggle_admin.php?id=<?php echo $user['id']; ?>"
                                       onclick="return confirm('Make this user an administrator?');">Make Admin</a>
                                <?php endif; ?>

                                |

                            <?php endif; ?>

                            <!-- ======================================== -->
                            <!-- LIEN DELETE (SUPPRIMER) -->
                            <!-- ======================================== -->

                            <!--
                            Lien vers delete_user.php avec l'ID de l'utilisateur

                            onclick : attribut JavaScript exécuté quand on clique
                            - confirm() : affiche une boîte de dialogue de confirmation
                            - Retourne TRUE si l'utilisateur clique "OK"
                            - Retourne FALSE si l'utilisateur clique "Annuler"
                            - return false annule la navigation si l'utilisateur refuse

                            Fonctionnement :
                            1. L'utilisateur clique sur "Delete"
                            2. Une popup apparaît : "Are you sure you want to delete this user?"
                            3. Si "OK" → la page delete_user.php?id=... est chargée
                            4. Si "Annuler" → rien ne se passe (return false annule le lien)
                            -->
                            <a href="delete_user.php?id=<?php echo $user['id']; ?>"
                               onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>

                            <!--
                            NOTE SÉCURITÉ :
                            Cette confirmation JavaScript peut être contournée !
                            (Un utilisateur malveillant peut accéder directement à delete_user.php?id=5)

                            C'est pourquoi delete_user.php doit AUSSI vérifier :
                            - Que l'utilisateur est admin
                            - Que l'ID existe
                            - Qu'on ne supprime pas son propre compte
                            -->

                        </td>

                    </tr>
                    <!-- Fin de la ligne pour cet utilisateur -->

                <?php
                endforeach; // Fin de la boucle foreach
                ?>

            </tbody>

        </table>
        <!-- Fin du tableau -->

        <!-- ============================================================== -->
        <!-- AFFICHER LE NOMBRE TOTAL D'UTILISATEURS -->
        <!-- ============================================================== -->

        <p>
            <strong>Total users:</strong>
            <?php
            // count($users) retourne le nombre d'utilisateurs
            echo count($users);
            ?>
        </p>

    <?php
    else: // Si aucun utilisateur n'existe
    ?>

        <!-- ============================================================== -->
        <!-- MESSAGE SI AUCUN UTILISATEUR -->
        <!-- ============================================================== -->

        <!-- Ce cas ne devrait jamais arriver car il y a au moins l'admin ! -->
        <!-- Mais on le gère quand même pour la sécurité -->
        <p>No users found in the database.</p>

    <?php
    endif; // Fin de la vérification count($users) > 0
    ?>

    <!-- ========================================================================== -->
    <!-- LIEN DE RETOUR -->
    <!-- ========================================================================== -->

    <p><a href="index.php">Back to Home</a></p>

</main>

</body>
</html>

<!--
===============================================================================
NOTES PÉDAGOGIQUES - PAGE D'ADMINISTRATION
===============================================================================

1. DOUBLE PROTECTION :
   - Vérifier isset($_SESSION['user_id']) : utilisateur connecté
   - Vérifier $_SESSION['is_admin'] == 1 : utilisateur administrateur
   - || (OU logique) : rediriger si UNE des deux conditions échoue

2. RÉCUPÉRATION DE TOUS LES UTILISATEURS :
   - SELECT id, email, is_admin, created_at FROM users
   - ORDER BY id DESC : trier par ID décroissant
   - fetchAll() : récupérer TOUS les résultats (pas juste un)
   - Retourne un tableau de tableaux

3. BOUCLE FOREACH :
   - foreach ($users as $user) : parcourir chaque utilisateur
   - $user est un tableau associatif : ['id' => ..., 'email' => ...]
   - $user['id'], $user['email'], etc. pour accéder aux colonnes

4. TABLEAU HTML :
   Structure :
   <table>
     <thead>        ← En-tête (titres des colonnes)
       <tr>         ← Ligne
         <th>...</th> ← Cellule d'en-tête
       </tr>
     </thead>
     <tbody>        ← Corps (données)
       <tr>         ← Ligne
         <td>...</td> ← Cellule de données
       </tr>
     </tbody>
   </table>

5. OPÉRATEUR TERNAIRE :
   $user['is_admin'] == 1 ? 'Yes' : 'No'

   Équivalent à :
   if ($user['is_admin'] == 1) {
       echo 'Yes';
   } else {
       echo 'No';
   }

6. PARAMÈTRES GET DANS L'URL :
   - edit_user.php?id=5
   - Le "?id=5" est un paramètre GET
   - En PHP : $_GET['id'] contient 5
   - On peut avoir plusieurs paramètres : page.php?id=5&name=test
   - Séparateur : & (esperluette)

7. CONFIRMATION JAVASCRIPT :
   onclick="return confirm('message');"
   - confirm() affiche une popup avec OK/Annuler
   - Retourne true si OK, false si Annuler
   - return false empêche la navigation

8. SÉCURITÉ :
   - Protection admin au début du fichier
   - htmlspecialchars() sur toutes les données affichées
   - Requêtes préparées PDO
   - IMPORTANT : La protection JavaScript (confirm) peut être contournée
     → delete_user.php doit AUSSI vérifier les permissions !

9. FONCTIONS UTILES :
   - count($array) : nombre d'éléments
   - isset($var) : vérifie si une variable existe
   - !isset($var) : vérifie si une variable N'existe PAS
   - empty($array) : vérifie si un tableau est vide
   - || : OU logique
   - && : ET logique

10. DIFFÉRENCES FETCH vs FETCHALL :
    fetch()
    - Récupère UNE ligne
    - Retourne un tableau associatif ou FALSE
    - Utilisé dans login.php, register.php

    fetchAll()
    - Récupère TOUTES les lignes
    - Retourne un tableau de tableaux
    - Utilisé dans admin.php

    Exemple :
    $user = $stmt->fetch();
    // $user = ['id' => 5, 'email' => 'test@example.com']

    $users = $stmt->fetchAll();
    // $users = [
    //   ['id' => 1, 'email' => 'user1@example.com'],
    //   ['id' => 2, 'email' => 'user2@example.com'],
    //   ['id' => 3, 'email' => 'user3@example.com']
    // ]

===============================================================================
-->
