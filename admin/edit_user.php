<?php
/**
 * ============================================================================
 * PAGE D'ÉDITION UTILISATEUR (EDIT_USER.PHP)
 * ============================================================================
 *
 * Cette page permet à un administrateur de modifier n'importe quel utilisateur.
 *
 * Modifications possibles :
 * - Changer l'email de l'utilisateur
 * - Changer le statut admin (0 ou 1)
 * - Réinitialiser le mot de passe (optionnel)
 *
 * Protections :
 * - Accessible UNIQUEMENT aux administrateurs
 * - Vérification que l'ID utilisateur passé en paramètre existe
 * - Validation complète des données (email, mot de passe)
 */

// ----------------------------------------------------------------------------
// DÉMARRER LA SESSION
// ----------------------------------------------------------------------------

session_start();

// ============================================================================
// PROTECTION 1 : ADMINISTRATEUR REQUIS
// ============================================================================

// Vérifier que l'utilisateur est connecté ET administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    // Rediriger vers l'accueil si non autorisé
    header('Location: ../index.php');
    exit();
}

// ----------------------------------------------------------------------------
// INCLURE LA BASE DE DONNÉES
// ----------------------------------------------------------------------------

require_once '../config/db.php';

// ----------------------------------------------------------------------------
// INITIALISER LES VARIABLES
// ----------------------------------------------------------------------------

$error = '';
$success = '';
$user = null; // Contiendra les informations de l'utilisateur à éditer

// ============================================================================
// PROTECTION 2 : VÉRIFIER QU'UN ID A ÉTÉ FOURNI
// ============================================================================

/**
 * Cette page est appelée avec un paramètre GET : edit_user.php?id=5
 * On doit vérifier que ce paramètre existe et n'est pas vide
 */

// Vérifier si $_GET['id'] existe et n'est pas vide
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Pas d'ID fourni → impossible de savoir quel utilisateur éditer
    // Rediriger vers la page admin
    header('Location: admin.php');
    exit();
}

// ----------------------------------------------------------------------------
// RÉCUPÉRER L'ID DE L'UTILISATEUR À ÉDITER
// ----------------------------------------------------------------------------

/**
 * (int) : conversion forcée en entier
 *
 * Pourquoi c'est important ?
 * - $_GET['id'] est toujours une chaîne de caractères (string)
 * - Même si l'URL est "?id=5", $_GET['id'] = "5" (string)
 * - (int)"5" = 5 (integer)
 * - Bonus sécurité : (int)"5abc" = 5 (supprime les caractères invalides)
 */
$user_id = (int)$_GET['id'];

// ============================================================================
// RÉCUPÉRER LES INFORMATIONS DE L'UTILISATEUR
// ============================================================================

try {
    // Préparer la requête pour récupérer l'utilisateur par son ID
    // On récupère : id, email, is_admin
    // On ne récupère PAS le password (pas besoin et plus sécurisé)
    $stmt = $pdo->prepare("SELECT id, email, is_admin FROM users WHERE id = :id");
    $stmt->execute(['id' => $user_id]);
    $user = $stmt->fetch();

    // ========================================================================
    // PROTECTION 3 : VÉRIFIER QUE L'UTILISATEUR EXISTE
    // ========================================================================

    // Si fetch() retourne FALSE, l'utilisateur n'existe pas
    if (!$user) {
        // ID invalide ou utilisateur supprimé
        // Rediriger vers admin.php
        header('Location: admin.php');
        exit();
    }

} catch (PDOException $e) {
    // Erreur de base de données
    $error = "Error loading user data.";
}

// ----------------------------------------------------------------------------
// TRAITER LE FORMULAIRE SI SOUMIS
// ----------------------------------------------------------------------------

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ========================================================================
    // RÉCUPÉRER LES DONNÉES DU FORMULAIRE
    // ========================================================================

    // Email (obligatoire)
    $new_email = trim($_POST['email']);

    // Statut admin
    // isset($_POST['is_admin']) : retourne TRUE si la checkbox est cochée
    // Retourne FALSE si la checkbox n'est PAS cochée
    // ? 1 : 0 : convertit TRUE en 1, FALSE en 0
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    // Nouveau mot de passe (optionnel)
    $new_password = $_POST['password'];

    // ========================================================================
    // PRÉPARER LA MISE À JOUR
    // ========================================================================

    $should_update = false;
    $updates = [];

    // ========================================================================
    // VALIDATION DE L'EMAIL
    // ========================================================================

    // L'email est obligatoire
    if (empty($new_email)) {
        $error = "Email is required.";
    }
    // Valider le format
    elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    }
    // Vérifier qu'il n'est pas déjà utilisé par un AUTRE utilisateur
    else {
        try {
            // Chercher l'email SAUF pour l'utilisateur actuel
            // Cela permet de garder le même email sans erreur
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email AND id != :user_id");
            $stmt->execute([
                'email' => $new_email,
                'user_id' => $user_id
            ]);
            $existing_user = $stmt->fetch();

            if ($existing_user) {
                $error = "This email is already used by another account.";
            } else {
                // Email valide et disponible
                $updates['email'] = $new_email;
                $should_update = true;
            }

        } catch (PDOException $e) {
            $error = "An error occurred. Please try again.";
        }
    }

    // ========================================================================
    // TRAITER LE STATUT ADMIN
    // ========================================================================

    // Toujours ajouter is_admin aux mises à jour
    // (même si la valeur n'a pas changé, ce n'est pas grave)
    if (empty($error)) {
        $updates['is_admin'] = $is_admin;
        $should_update = true;
    }

    // ========================================================================
    // VALIDATION DU MOT DE PASSE (SI FOURNI)
    // ========================================================================

    // Si un nouveau mot de passe a été fourni
    if (empty($error) && !empty($new_password)) {

        // Validation 1 : Longueur minimale
        if (strlen($new_password) < 8) {
            $error = "Password must be at least 8 characters long.";
        }
        // Validation 2 : Au moins une majuscule
        elseif (!preg_match('/[A-Z]/', $new_password)) {
            $error = "Password must contain at least one uppercase letter.";
        }
        // Validation 3 : Au moins un caractère spécial
        elseif (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $new_password)) {
            $error = "Password must contain at least one special character.";
        }
        // Toutes les validations passées
        else {
            // Hasher le nouveau mot de passe
            $updates['password'] = password_hash($new_password, PASSWORD_BCRYPT);
            $should_update = true;
        }
    }

    // ========================================================================
    // EXÉCUTER LA MISE À JOUR
    // ========================================================================

    if (empty($error) && $should_update) {

        try {

            // ================================================================
            // CONSTRUIRE LA REQUÊTE SQL DYNAMIQUEMENT
            // ================================================================

            $set_clause = [];

            // Parcourir chaque mise à jour et construire la clause SET
            foreach ($updates as $key => $value) {
                $set_clause[] = "$key = :$key";
            }

            // Joindre avec des virgules
            $set_string = implode(', ', $set_clause);

            // Requête complète
            // Exemple : UPDATE users SET email = :email, is_admin = :is_admin WHERE id = :user_id
            $sql = "UPDATE users SET $set_string WHERE id = :user_id";

            // Préparer
            $stmt = $pdo->prepare($sql);

            // Ajouter l'ID utilisateur
            $updates['user_id'] = $user_id;

            // Exécuter
            $stmt->execute($updates);

            // ================================================================
            // METTRE À JOUR LES DONNÉES LOCALES POUR L'AFFICHAGE
            // ================================================================

            // Mettre à jour $user pour que le formulaire affiche les nouvelles valeurs
            $user['email'] = $new_email;
            $user['is_admin'] = $is_admin;

            // Message de succès
            $success = "User updated successfully!";

        } catch (PDOException $e) {
            $error = "An error occurred while updating the user.";
        }
    }
}

// ----------------------------------------------------------------------------
// INCLURE LE HEADER
// ----------------------------------------------------------------------------

include_once '../includes/header.php';
?>

<!-- ========================================================================== -->
<!-- CONTENU HTML DE LA PAGE D'ÉDITION -->
<!-- ========================================================================== -->

<main>

    <h2>Edit User</h2>

    <?php
    // Afficher les messages d'erreur
    if (!empty($error)):
    ?>
        <p style="color: red;">
            <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
        </p>
    <?php
    endif;
    ?>

    <?php
    // Afficher les messages de succès
    if (!empty($success)):
    ?>
        <p style="color: green;">
            <strong><?php echo htmlspecialchars($success); ?></strong>
        </p>
    <?php
    endif;
    ?>

    <?php
    /**
     * Si $user existe (devrait toujours être le cas ici car on a vérifié au début)
     */
    if ($user):
    ?>

        <!-- Afficher l'ID de l'utilisateur en cours d'édition -->
        <p><strong>User ID:</strong> <?php echo htmlspecialchars($user['id']); ?></p>

        <!-- ================================================================ -->
        <!-- FORMULAIRE D'ÉDITION -->
        <!-- ================================================================ -->

        <!--
        action="edit_user.php?id=<?php echo $user['id']; ?>"
        Important : on doit repasser l'ID en paramètre GET
        Sinon lors de la soumission, PHP ne saura pas quel utilisateur modifier
        -->
        <form method="POST" action="edit_user.php?id=<?php echo $user['id']; ?>">

            <!-- ============================================================ -->
            <!-- CHAMP EMAIL -->
            <!-- ============================================================ -->

            <p>
                <label for="email">Email:</label><br>

                <!--
                value="<?php echo htmlspecialchars($user['email']); ?>"
                Pré-remplit le champ avec l'email actuel
                L'administrateur peut le modifier s'il veut
                -->
                <input type="email" id="email" name="email"
                       value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </p>

            <!-- ============================================================ -->
            <!-- CHECKBOX ADMINISTRATEUR -->
            <!-- ============================================================ -->

            <p>
                <label for="is_admin">

                    <!--
                    <input type="checkbox">
                    - Si coché et soumis : isset($_POST['is_admin']) = TRUE
                    - Si NON coché : isset($_POST['is_admin']) = FALSE

                    checked : attribut HTML pour pré-cocher la case
                    - On l'ajoute SI l'utilisateur est déjà admin
                    - Sinon la case est décochée par défaut
                    -->
                    <input type="checkbox" id="is_admin" name="is_admin"
                           <?php echo $user['is_admin'] == 1 ? 'checked' : ''; ?>>

                    Administrator
                </label>
                <!--
                Explication de <?php echo $user['is_admin'] == 1 ? 'checked' : ''; ?>
                - Si is_admin == 1 : affiche 'checked'
                - Si is_admin == 0 : affiche '' (rien)
                - Résultat HTML si admin : <input ... checked>
                - Résultat HTML si pas admin : <input ...>
                -->
            </p>

            <!-- ============================================================ -->
            <!-- CHAMP MOT DE PASSE (OPTIONNEL) -->
            <!-- ============================================================ -->

            <p>
                <label for="password">New Password (leave empty to keep current):</label><br>

                <!--
                Pas d'attribut "value" ici pour des raisons de sécurité
                On ne veut JAMAIS afficher le mot de passe (même hashé)
                Le champ est vide par défaut
                -->
                <input type="password" id="password" name="password">
            </p>

            <!-- Rappel des règles -->
            <p>
                <small>Password must be at least 8 characters, contain one uppercase letter and one special character.</small>
            </p>

            <!-- ============================================================ -->
            <!-- BOUTON DE SOUMISSION -->
            <!-- ============================================================ -->

            <p>
                <button type="submit">Update User</button>
            </p>

        </form>

        <!-- ================================================================ -->
        <!-- LIENS DE NAVIGATION -->
        <!-- ================================================================ -->

        <p>
            <a href="admin.php">Back to Admin Dashboard</a> |
            <a href="../index.php">Back to Home</a>
        </p>

    <?php
    endif; // Fin de la vérification if ($user)
    ?>

</main>

</body>
</html>

<!--
===============================================================================
NOTES PÉDAGOGIQUES - ÉDITION UTILISATEUR PAR ADMIN
===============================================================================

1. TRIPLE PROTECTION :
   a. Vérifier que l'utilisateur est admin
   b. Vérifier qu'un ID a été fourni dans l'URL
   c. Vérifier que cet ID correspond à un utilisateur existant

2. PARAMÈTRE GET :
   - URL : edit_user.php?id=5
   - Récupération : $_GET['id']
   - Conversion : (int)$_GET['id']
   - Important : TOUJOURS convertir en int pour la sécurité

3. CHECKBOX HTML :
   <input type="checkbox" name="is_admin" checked>
   - Si coché : isset($_POST['is_admin']) = TRUE
   - Si NON coché : isset($_POST['is_admin']) = FALSE
   - Donc : $is_admin = isset($_POST['is_admin']) ? 1 : 0

4. PRÉ-REMPLIR UN FORMULAIRE :
   <input value="<?php echo htmlspecialchars($user['email']); ?>">
   - Affiche la valeur actuelle
   - L'utilisateur peut la modifier
   - TOUJOURS utiliser htmlspecialchars()

5. DIFFÉRENCES AVEC PROFILE.PHP :

   profile.php                      edit_user.php
   -------------------------------------------------------
   - Modifie l'utilisateur connecté - Modifie n'importe quel utilisateur
   - WHERE id = $_SESSION['user_id'] - WHERE id = $_GET['id']
   - Ne peut PAS changer is_admin   - PEUT changer is_admin (checkbox)
   - Protection : user connecté     - Protection : user admin
   - Champs tous optionnels         - Email obligatoire

6. SÉCURITÉ EMAIL UNIQUE :
   WHERE email = :email AND id != :user_id
   - Cherche l'email SAUF pour l'utilisateur en cours d'édition
   - Permet de garder le même email sans erreur
   - Empêche de prendre l'email d'un autre utilisateur

7. MISE À JOUR DE SESSION :
   ATTENTION : On ne met PAS à jour $_SESSION ici !
   - On modifie un autre utilisateur, pas l'utilisateur connecté
   - $_SESSION doit rester inchangée
   - Exception : si l'admin se modifie lui-même (même ID)
     → Mais ce cas est rare et non géré ici

8. FLOW COMPLET :
   admin.php
      ↓ Clic sur "Edit" pour l'utilisateur ID=5
   edit_user.php?id=5
      ↓ Récupération : $_GET['id'] = "5"
      ↓ Conversion : $user_id = (int)$_GET['id'] = 5
      ↓ Requête SQL : SELECT ... WHERE id = 5
      ↓ Affichage du formulaire pré-rempli
      ↓ Soumission du formulaire
      ↓ UPDATE users SET ... WHERE id = 5
      ↓ Message de succès
   edit_user.php?id=5 (même page)

9. POURQUOI GARDER L'ID DANS L'ACTION DU FORMULAIRE ?
   <form action="edit_user.php?id=5">

   - Sans ?id=5, lors de la soumission POST, l'ID serait perdu
   - La page ne saurait plus quel utilisateur modifier
   - On aurait une erreur car $_GET['id'] n'existerait plus

10. CONVERSION DE TYPE :
    (int)$_GET['id']

    Exemples :
    - (int)"5" = 5
    - (int)"5abc" = 5 (supprime les lettres)
    - (int)"abc" = 0
    - (int)"" = 0

    Pourquoi c'est important ?
    - Sécurité : évite l'injection SQL
    - Type safe : garantit qu'on a un entier
    - Validation : supprime les valeurs invalides

===============================================================================
-->
