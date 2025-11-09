<?php
/**
 * ============================================================================
 * PAGE DE PROFIL (PROFILE.PHP)
 * ============================================================================
 *
 * Cette page permet à un utilisateur connecté de modifier ses informations :
 * - Changer son email
 * - Changer son mot de passe
 *
 * Protections :
 * - Page accessible UNIQUEMENT aux utilisateurs connectés
 * - Vérification que l'email n'est pas déjà utilisé par un autre utilisateur
 * - Validation complète du nouveau mot de passe (si fourni)
 */

// ----------------------------------------------------------------------------
// DÉMARRER LA SESSION ET VÉRIFIER L'AUTHENTIFICATION
// ----------------------------------------------------------------------------

// Démarrer la session pour accéder à $_SESSION
session_start();

// ============================================================================
// PROTECTION DE LA PAGE : UTILISATEUR CONNECTÉ REQUIS
// ============================================================================

/**
 * Cette page est RÉSERVÉE aux utilisateurs connectés.
 * Si quelqu'un essaie d'y accéder sans être connecté, on le redirige vers login.php
 */

// Vérifier si l'utilisateur est connecté
// Si $_SESSION['user_id'] n'existe pas, c'est qu'il n'est PAS connecté
if (!isset($_SESSION['user_id'])) {
    // ! est l'opérateur de négation
    // isset() retourne TRUE si la variable existe, FALSE sinon
    // !isset() retourne FALSE si la variable existe, TRUE sinon

    // Rediriger vers la page de connexion
    header('Location: login.php');

    // Arrêter immédiatement l'exécution pour éviter que le code suivant soit exécuté
    exit();
}

// Si on arrive ici, c'est que l'utilisateur est connecté ✓
// On peut continuer en toute sécurité

// ----------------------------------------------------------------------------
// INCLURE LA BASE DE DONNÉES
// ----------------------------------------------------------------------------

// Inclure le fichier de connexion PDO
require_once 'config/db.php';

// ----------------------------------------------------------------------------
// INITIALISER LES VARIABLES
// ----------------------------------------------------------------------------

// Messages d'erreur et de succès
$error = '';
$success = '';

// ----------------------------------------------------------------------------
// TRAITER LE FORMULAIRE SI SOUMIS
// ----------------------------------------------------------------------------

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ========================================================================
    // RÉCUPÉRER LES DONNÉES DU FORMULAIRE
    // ========================================================================

    // Nouveau email (peut être vide si l'utilisateur ne veut pas le changer)
    $new_email = trim($_POST['email']);

    // Nouveau mot de passe (peut être vide si l'utilisateur ne veut pas le changer)
    $new_password = $_POST['password'];

    // Confirmation du nouveau mot de passe
    $confirm_password = $_POST['confirm_password'];

    // ========================================================================
    // PRÉPARER LA MISE À JOUR
    // ========================================================================

    /**
     * L'utilisateur peut vouloir :
     * 1. Changer SEULEMENT son email
     * 2. Changer SEULEMENT son mot de passe
     * 3. Changer LES DEUX
     * 4. Ne rien changer (soumettre le formulaire vide → erreur)
     *
     * On va construire dynamiquement la requête SQL en fonction de ce qui est fourni
     */

    // Variable booléenne pour savoir si on doit faire une mise à jour
    $should_update = false;

    // Tableau associatif pour stocker les champs à mettre à jour
    // Format : ['nom_colonne' => 'nouvelle_valeur']
    $updates = [];

    // ========================================================================
    // VALIDATION ET TRAITEMENT DE L'EMAIL
    // ========================================================================

    // Si l'utilisateur a fourni un nouvel email
    if (!empty($new_email)) {

        // --------------------------------------------------------------------
        // VALIDER LE FORMAT DE L'EMAIL
        // --------------------------------------------------------------------

        // filter_var() avec FILTER_VALIDATE_EMAIL vérifie le format
        if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email format.";
        }

        // --------------------------------------------------------------------
        // VÉRIFIER QUE L'EMAIL N'EST PAS DÉJÀ UTILISÉ
        // --------------------------------------------------------------------

        else {
            // L'email est valide, maintenant vérifier qu'il n'est pas déjà pris

            try {
                /**
                 * On doit vérifier que l'email n'est pas utilisé par un AUTRE utilisateur
                 * Attention : l'utilisateur actuel pourrait déjà avoir cet email !
                 *
                 * Exemple :
                 * - Utilisateur actuel : id=5, email=test@example.com
                 * - Il soumet le formulaire avec email=test@example.com (inchangé)
                 * - On ne doit PAS dire "email déjà utilisé" car c'est son propre email !
                 *
                 * Solution : WHERE email = :email AND id != :user_id
                 * Cela cherche l'email SAUF pour l'utilisateur actuel
                 */

                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email AND id != :user_id");
                $stmt->execute([
                    'email' => $new_email,
                    'user_id' => $_SESSION['user_id'] // ID de l'utilisateur connecté
                ]);
                $existing_user = $stmt->fetch();

                // Si un autre utilisateur a déjà cet email
                if ($existing_user) {
                    $error = "This email is already used by another account.";
                } else {
                    // L'email est disponible, l'ajouter aux mises à jour
                    $updates['email'] = $new_email;
                    $should_update = true;
                }

            } catch (PDOException $e) {
                $error = "An error occurred. Please try again.";
            }
        }
    }

    // ========================================================================
    // VALIDATION ET TRAITEMENT DU MOT DE PASSE
    // ========================================================================

    // Si aucune erreur jusqu'ici ET que l'utilisateur a fourni un nouveau mot de passe
    if (empty($error) && !empty($new_password)) {

        // --------------------------------------------------------------------
        // VALIDATION 1 : Longueur minimale (8 caractères)
        // --------------------------------------------------------------------

        if (strlen($new_password) < 8) {
            $error = "Password must be at least 8 characters long.";
        }

        // --------------------------------------------------------------------
        // VALIDATION 2 : Au moins une majuscule
        // --------------------------------------------------------------------

        // preg_match('/[A-Z]/', $string) retourne 1 si trouvé, 0 sinon
        elseif (!preg_match('/[A-Z]/', $new_password)) {
            $error = "Password must contain at least one uppercase letter.";
        }

        // --------------------------------------------------------------------
        // VALIDATION 3 : Au moins un caractère spécial
        // --------------------------------------------------------------------

        elseif (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $new_password)) {
            $error = "Password must contain at least one special character.";
        }

        // --------------------------------------------------------------------
        // VALIDATION 4 : Les deux mots de passe correspondent
        // --------------------------------------------------------------------

        elseif ($new_password !== $confirm_password) {
            $error = "Passwords do not match.";
        }

        // --------------------------------------------------------------------
        // SI TOUTES LES VALIDATIONS SONT PASSÉES
        // --------------------------------------------------------------------

        else {
            // Hasher le nouveau mot de passe avec BCRYPT
            // IMPORTANT : On stocke le HASH, jamais le mot de passe en clair !
            $updates['password'] = password_hash($new_password, PASSWORD_BCRYPT);
            $should_update = true;
        }
    }

    // ========================================================================
    // EXÉCUTER LA MISE À JOUR SI NÉCESSAIRE
    // ========================================================================

    // Si aucune erreur ET qu'il y a des mises à jour à faire
    if (empty($error) && $should_update) {

        try {

            // ================================================================
            // CONSTRUIRE LA REQUÊTE SQL DYNAMIQUEMENT
            // ================================================================

            /**
             * On va construire la requête UPDATE en fonction de ce qui a changé
             *
             * Exemples :
             * - Si seulement email : UPDATE users SET email = :email WHERE id = :user_id
             * - Si seulement password : UPDATE users SET password = :password WHERE id = :user_id
             * - Si les deux : UPDATE users SET email = :email, password = :password WHERE id = :user_id
             */

            // Tableau pour stocker les parties "colonne = :placeholder"
            $set_clause = [];

            // Parcourir chaque mise à jour
            // $key = nom de la colonne ('email' ou 'password')
            // $value = nouvelle valeur
            foreach ($updates as $key => $value) {
                // Ajouter "colonne = :colonne" au tableau
                // Exemple : "email = :email" ou "password = :password"
                $set_clause[] = "$key = :$key";
            }

            // Joindre tous les éléments avec des virgules
            // ['email = :email', 'password = :password'] devient 'email = :email, password = :password'
            $set_string = implode(', ', $set_clause);

            // Construire la requête SQL complète
            // Exemple : "UPDATE users SET email = :email, password = :password WHERE id = :user_id"
            $sql = "UPDATE users SET $set_string WHERE id = :user_id";

            // Préparer la requête
            $stmt = $pdo->prepare($sql);

            // Ajouter l'ID de l'utilisateur aux paramètres
            // C'est pour le WHERE id = :user_id
            $updates['user_id'] = $_SESSION['user_id'];

            // Exécuter la requête avec tous les paramètres
            // Exemple : ['email' => 'nouveau@example.com', 'user_id' => 5]
            // Ou : ['password' => '$2y$10$...', 'user_id' => 5]
            // Ou : ['email' => '...', 'password' => '$2y$10$...', 'user_id' => 5]
            $stmt->execute($updates);

            // ================================================================
            // METTRE À JOUR LA SESSION SI L'EMAIL A CHANGÉ
            // ================================================================

            /**
             * Si l'utilisateur a changé son email, on doit aussi mettre à jour
             * $_SESSION['email'] sinon l'ancien email continuerait à s'afficher
             * dans le menu jusqu'à la prochaine connexion
             */

            // Si l'email fait partie des mises à jour
            if (isset($updates['email'])) {
                // Mettre à jour l'email dans la session
                $_SESSION['email'] = $new_email;
            }

            // Afficher un message de succès
            $success = "Profile updated successfully!";

        } catch (PDOException $e) {
            // En cas d'erreur SQL
            $error = "An error occurred while updating your profile.";
        }

    }
    // Si aucune erreur mais aucune mise à jour non plus
    elseif (empty($error) && !$should_update) {
        $error = "No changes to update.";
    }
}

// ----------------------------------------------------------------------------
// INCLURE LE HEADER
// ----------------------------------------------------------------------------

// Maintenant qu'on a fini le traitement, on peut afficher le HTML
include_once 'includes/header.php';
?>

<!-- ========================================================================== -->
<!-- CONTENU HTML DE LA PAGE PROFIL -->
<!-- ========================================================================== -->

<main>

    <h2>Edit Profile</h2>

    <?php
    // Afficher le message d'erreur s'il existe
    if (!empty($error)):
    ?>
        <p style="color: red;">
            <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
        </p>
    <?php
    endif;
    ?>

    <?php
    // Afficher le message de succès s'il existe
    if (!empty($success)):
    ?>
        <p style="color: green;">
            <strong><?php echo htmlspecialchars($success); ?></strong>
        </p>
    <?php
    endif;
    ?>

    <!-- ========================================================================== -->
    <!-- AFFICHER L'EMAIL ACTUEL -->
    <!-- ========================================================================== -->

    <!-- Montrer à l'utilisateur quel est son email actuel -->
    <p><strong>Current email:</strong> <?php echo htmlspecialchars($_SESSION['email']); ?></p>

    <!-- ========================================================================== -->
    <!-- FORMULAIRE DE MODIFICATION DU PROFIL -->
    <!-- ========================================================================== -->

    <form method="POST" action="profile.php">

        <!-- ================================================================ -->
        <!-- SECTION : CHANGEMENT D'EMAIL -->
        <!-- ================================================================ -->

        <h3>Change Email</h3>

        <p>
            <label for="email">New Email (leave empty to keep current):</label><br>
            <!--
            Pas d'attribut "required" ici car le champ est OPTIONNEL
            L'utilisateur peut laisser vide s'il ne veut pas changer son email
            -->
            <input type="email" id="email" name="email">
        </p>

        <!-- ================================================================ -->
        <!-- SECTION : CHANGEMENT DE MOT DE PASSE -->
        <!-- ================================================================ -->

        <h3>Change Password</h3>

        <p>
            <label for="password">New Password (leave empty to keep current):</label><br>
            <!--
            Également optionnel
            Si l'utilisateur ne veut pas changer son mot de passe, il laisse vide
            -->
            <input type="password" id="password" name="password">
        </p>

        <p>
            <label for="confirm_password">Confirm New Password:</label><br>
            <!--
            Confirmation du mot de passe
            Validé en PHP : $new_password !== $confirm_password
            -->
            <input type="password" id="confirm_password" name="confirm_password">
        </p>

        <!-- Rappel des règles de mot de passe -->
        <p>
            <small>Password must be at least 8 characters, contain one uppercase letter and one special character.</small>
            <!-- <small> réduit la taille du texte (indique une information secondaire) -->
        </p>

        <!-- ================================================================ -->
        <!-- BOUTON DE SOUMISSION -->
        <!-- ================================================================ -->

        <p>
            <button type="submit">Update Profile</button>
        </p>

    </form>

    <!-- ========================================================================== -->
    <!-- LIEN DE RETOUR -->
    <!-- ========================================================================== -->

    <p><a href="index.php">Back to Home</a></p>

</main>

</body>
</html>

<!--
===============================================================================
NOTES PÉDAGOGIQUES - MISE À JOUR DE PROFIL
===============================================================================

1. PROTECTION DE PAGE :
   - Vérifier isset($_SESSION['user_id']) au début
   - Rediriger vers login.php si non connecté
   - TOUJOURS mettre exit() après header('Location: ...')

2. CHAMPS OPTIONNELS :
   - L'utilisateur peut changer SEULEMENT l'email
   - OU SEULEMENT le mot de passe
   - OU les deux
   - On vérifie avec !empty() quel champ a été rempli

3. REQUÊTE SQL DYNAMIQUE :
   - On construit la requête en fonction des champs fournis
   - $set_clause = [] : tableau pour stocker les parties SET
   - foreach($updates as $key => $value) : parcourir les mises à jour
   - implode(', ', $set_clause) : joindre avec des virgules
   - Résultat : "UPDATE users SET email = :email WHERE id = :user_id"
   - OU : "UPDATE users SET password = :password WHERE id = :user_id"
   - OU : "UPDATE users SET email = :email, password = :password WHERE id = :user_id"

4. VÉRIFICATION EMAIL UNIQUE :
   - WHERE email = :email AND id != :user_id
   - Cherche l'email SAUF pour l'utilisateur actuel
   - Permet de garder le même email sans erreur
   - Empêche d'utiliser l'email d'un autre utilisateur

5. MISE À JOUR DE SESSION :
   - Si l'email change, $_SESSION['email'] doit être mis à jour
   - Sinon l'ancien email continuerait à s'afficher dans le menu
   - Le mot de passe n'est PAS stocké en session (sécurité)

6. VALIDATION MOT DE PASSE :
   - Même règles que register.php
   - Minimum 8 caractères
   - Au moins une majuscule
   - Au moins un caractère spécial
   - Les deux mots de passe doivent correspondre

7. DIFFÉRENCES AVEC REGISTER.PHP :
   register.php                      profile.php
   ----------------------------------------
   - Tous les champs obligatoires    - Tous les champs optionnels
   - Crée un nouvel utilisateur     - Modifie l'utilisateur existant
   - INSERT INTO users              - UPDATE users SET ... WHERE id = ...
   - Pas de protection de page      - Protection : utilisateur connecté requis
   - is_admin = 0 par défaut        - Ne modifie PAS is_admin
   - Redirige vers login.php        - Reste sur la même page

8. SÉCURITÉ :
   - Protection de page (utilisateur connecté)
   - Requêtes préparées PDO (injection SQL)
   - password_hash() pour les mots de passe
   - htmlspecialchars() pour l'affichage
   - Validation email unique
   - Validation stricte des mots de passe

===============================================================================
-->
