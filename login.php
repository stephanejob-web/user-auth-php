<?php
/**
 * ============================================================================
 * PAGE DE CONNEXION (LOGIN.PHP)
 * ============================================================================
 *
 * Cette page permet à un utilisateur existant de se connecter.
 *
 * Étapes du processus :
 * 1. Afficher le formulaire de connexion
 * 2. Récupérer l'email et le mot de passe soumis
 * 3. Chercher l'utilisateur dans la base de données par email
 * 4. Vérifier le mot de passe avec password_verify()
 * 5. Si correct : créer la session et stocker les informations utilisateur
 * 6. Rediriger vers la page d'accueil
 */

// ----------------------------------------------------------------------------
// DÉMARRER LA SESSION AVANT TOUT
// ----------------------------------------------------------------------------

// Il est IMPORTANT de démarrer la session AVANT d'inclure header.php
// car nous allons potentiellement modifier $_SESSION pendant le traitement
// et faire une redirection avec header('Location: ...')
//
// Rappel : header('Location: ...') ne fonctionne que si aucun contenu HTML
// n'a déjà été envoyé au navigateur
session_start();

// Inclure le fichier de connexion à la base de données
require_once 'config/db.php';

// ----------------------------------------------------------------------------
// INITIALISER LES VARIABLES
// ----------------------------------------------------------------------------

// Variable pour stocker le message d'erreur
$error = '';

// ----------------------------------------------------------------------------
// TRAITER LE FORMULAIRE SI IL A ÉTÉ SOUMIS
// ----------------------------------------------------------------------------

// Vérifier si la requête est une soumission de formulaire (méthode POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ========================================================================
    // ÉTAPE 1 : RÉCUPÉRER LES DONNÉES DU FORMULAIRE
    // ========================================================================

    // Récupérer l'email et supprimer les espaces au début/fin
    $email = trim($_POST['email']);

    // Récupérer le mot de passe (SANS trim - voir register.php pour explications)
    $password = $_POST['password'];

    // ========================================================================
    // ÉTAPE 2 : VALIDATION BASIQUE
    // ========================================================================

    // Vérifier que l'email n'est pas vide
    if (empty($email)) {
        $error = "Email is required.";
    }
    // Vérifier que le mot de passe n'est pas vide
    elseif (empty($password)) {
        $error = "Password is required.";
    }

    // ========================================================================
    // ÉTAPE 3 : AUTHENTIFICATION
    // ========================================================================

    else {
        // Si les champs sont remplis, on tente l'authentification

        // On utilise try/catch pour gérer les erreurs de base de données
        try {

            // ================================================================
            // CHERCHER L'UTILISATEUR DANS LA BASE DE DONNÉES
            // ================================================================

            /**
             * On récupère TOUTES les informations nécessaires de l'utilisateur :
             * - id : pour le stocker dans la session
             * - email : pour le stocker dans la session et l'afficher
             * - password : le hash du mot de passe pour le vérifier
             * - is_admin : pour savoir si c'est un administrateur
             */

            // Préparer la requête SELECT avec un placeholder pour l'email
            $stmt = $pdo->prepare("SELECT id, email, password, is_admin FROM users WHERE email = :email");

            // Exécuter la requête en remplaçant :email par la vraie valeur
            $stmt->execute(['email' => $email]);

            // fetch() : récupère UNE seule ligne de résultat
            // Retourne un tableau associatif si trouvé, FALSE sinon
            $user = $stmt->fetch();

            // ================================================================
            // VÉRIFIER SI L'UTILISATEUR EXISTE
            // ================================================================

            // Si fetch() a retourné FALSE, l'utilisateur n'existe pas
            if (!$user) {
                // Message volontairement vague pour la sécurité
                // On ne dit PAS "cet email n'existe pas" pour éviter qu'un attaquant
                // puisse déterminer quels emails sont enregistrés dans notre système
                $error = "Invalid email or password.";
            }

            // ================================================================
            // VÉRIFIER LE MOT DE PASSE
            // ================================================================

            /**
             * password_verify() : fonction PHP pour vérifier un mot de passe hashé
             *
             * Syntaxe : password_verify($mot_de_passe_clair, $hash)
             *
             * Fonctionnement :
             * 1. Prend le mot de passe en clair entré par l'utilisateur
             * 2. Le hash avec le même salt que le hash stocké
             * 3. Compare les deux hash
             * 4. Retourne TRUE si identiques, FALSE sinon
             *
             * Exemple :
             * $hash = "$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy"
             * password_verify("MonMotDePasse123!", $hash) → TRUE
             * password_verify("MotDePasseIncorrect", $hash) → FALSE
             *
             * SÉCURITÉ : Cette fonction est résistante aux attaques "timing attack"
             * car elle prend toujours le même temps, que le mot de passe soit
             * correct ou non.
             */

            // elseif : exécuté seulement si l'utilisateur existe
            // ! : inverse le résultat de password_verify()
            // Si password_verify() retourne FALSE, !FALSE = TRUE → on entre dans le bloc
            elseif (!password_verify($password, $user['password'])) {
                // Le mot de passe ne correspond pas
                // Même message vague que ci-dessus pour la sécurité
                $error = "Invalid email or password.";
            }

            // ================================================================
            // AUTHENTIFICATION RÉUSSIE
            // ================================================================

            else {
                // Si on arrive ici, c'est que :
                // - L'utilisateur existe dans la base
                // - Le mot de passe est correct
                //
                // On peut maintenant créer la session et connecter l'utilisateur

                // ------------------------------------------------------------
                // STOCKER LES INFORMATIONS DANS LA SESSION
                // ------------------------------------------------------------

                /**
                 * $_SESSION : tableau associatif pour stocker des données persistantes
                 * entre les différentes pages du site
                 *
                 * Ces données seront disponibles sur TOUTES les pages tant que
                 * la session est active (tant que l'utilisateur ne se déconnecte pas
                 * et que la session n'expire pas).
                 */

                // Stocker l'ID de l'utilisateur
                // Utilisé pour vérifier si quelqu'un est connecté : isset($_SESSION['user_id'])
                // Utilisé aussi pour les requêtes SQL : "WHERE user_id = $_SESSION['user_id']"
                $_SESSION['user_id'] = $user['id'];

                // Stocker l'email de l'utilisateur
                // Utilisé pour afficher "Hello, [email]!" sur les pages
                $_SESSION['email'] = $user['email'];

                // Stocker le statut administrateur
                // 0 = utilisateur normal, 1 = administrateur
                // Utilisé pour afficher le lien "Admin" et protéger les pages admin
                $_SESSION['is_admin'] = $user['is_admin'];

                // ------------------------------------------------------------
                // REDIRIGER VERS LA PAGE D'ACCUEIL
                // ------------------------------------------------------------

                /**
                 * header() : envoie un en-tête HTTP brut au navigateur
                 * 'Location: index.php' : en-tête de redirection
                 *
                 * Effet : le navigateur est redirigé vers index.php
                 *
                 * IMPORTANT :
                 * - header() doit être appelé AVANT tout affichage HTML
                 * - C'est pourquoi on a fait session_start() en tout début de fichier
                 * - Et qu'on inclut header.php APRÈS ce traitement
                 */
                header('Location: index.php');

                // exit() : arrête immédiatement l'exécution du script
                // Nécessaire après une redirection pour éviter que le code suivant soit exécuté
                // Sans exit(), PHP continuerait à exécuter le reste du fichier
                exit();
            }

        } catch (PDOException $e) {
            // ================================================================
            // GESTION DES ERREURS DE BASE DE DONNÉES
            // ================================================================

            // Si une erreur SQL se produit
            // En production, on devrait logger $e->getMessage() dans un fichier
            // Ici, on affiche un message générique pour la sécurité
            $error = "An error occurred. Please try again.";
        }
    }
}

// ----------------------------------------------------------------------------
// INCLURE LE HEADER APRÈS LE TRAITEMENT
// ----------------------------------------------------------------------------

// On inclut header.php ICI et non au début car :
// 1. On a fait session_start() nous-mêmes en début de fichier
// 2. On a pu faire header('Location: ...') sans problème
// 3. Maintenant qu'on a fini le traitement, on peut afficher le HTML
include_once 'includes/header.php';
?>

<!-- ========================================================================== -->
<!-- CONTENU HTML DE LA PAGE DE CONNEXION -->
<!-- ========================================================================== -->

<main>

    <h2>Login</h2>

    <?php
    /**
     * --------------------------------------------------------------------
     * AFFICHER LE MESSAGE D'ERREUR SI IL EXISTE
     * --------------------------------------------------------------------
     */

    // Si une erreur s'est produite pendant l'authentification
    if (!empty($error)):
    ?>

        <!-- Message d'erreur en rouge -->
        <p style="color: red;">
            <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
            <!-- htmlspecialchars() pour éviter XSS même si nos messages d'erreur sont statiques -->
        </p>

    <?php
    endif; // Fin de l'affichage du message d'erreur
    ?>

    <!-- ========================================================================== -->
    <!-- FORMULAIRE DE CONNEXION -->
    <!-- ========================================================================== -->

    <!--
    Formulaire plus simple que register.php car seulement 2 champs :
    - Email
    - Mot de passe (pas de confirmation nécessaire)
    -->
    <form method="POST" action="login.php">

        <!-- ================================================================ -->
        <!-- CHAMP EMAIL -->
        <!-- ================================================================ -->

        <p>
            <!-- Label pour l'accessibilité -->
            <label for="email">Email:</label><br>

            <!--
            type="email" : validation HTML5 basique + clavier adapté sur mobile
            id="email" : lié au label avec for="email"
            name="email" : sera accessible dans $_POST['email']
            required : le champ ne peut pas être vide (validation côté client)
            -->
            <input type="email" id="email" name="email" required>
        </p>

        <!-- ================================================================ -->
        <!-- CHAMP MOT DE PASSE -->
        <!-- ================================================================ -->

        <p>
            <label for="password">Password:</label><br>

            <!--
            type="password" : masque les caractères tapés
            Les caractères sont affichés comme • ou * pour la confidentialité
            Mais les données sont envoyées en clair au serveur (utiliser HTTPS en production)
            -->
            <input type="password" id="password" name="password" required>
        </p>

        <!-- ================================================================ -->
        <!-- BOUTON DE SOUMISSION -->
        <!-- ================================================================ -->

        <p>
            <!--
            type="submit" : soumet le formulaire quand on clique
            Le formulaire est envoyé à action="login.php" via method="POST"
            -->
            <button type="submit">Login</button>
        </p>

    </form>
    <!-- Fin du formulaire -->

    <!-- ========================================================================== -->
    <!-- LIEN VERS LA PAGE D'INSCRIPTION -->
    <!-- ========================================================================== -->

    <!-- Si l'utilisateur n'a pas encore de compte, il peut aller s'inscrire -->
    <p>Don't have an account? <a href="register.php">Register here</a></p>

</main>

</body>
</html>

<!--
===============================================================================
NOTES PÉDAGOGIQUES - AUTHENTIFICATION ET SESSIONS
===============================================================================

1. PROCESSUS D'AUTHENTIFICATION :
   a. L'utilisateur entre email + mot de passe
   b. On cherche l'utilisateur par email dans la base
   c. On vérifie le mot de passe avec password_verify()
   d. Si correct : création de la session
   e. Redirection vers la page d'accueil

2. PASSWORD_VERIFY() vs PASSWORD_HASH() :
   - password_hash() : utilisé à l'inscription (register.php)
     → Transforme "MonMotDePasse123!" en "$2y$10$..."

   - password_verify() : utilisé à la connexion (login.php)
     → Compare "MonMotDePasse123!" avec "$2y$10$..."
     → Retourne TRUE ou FALSE

3. SESSIONS PHP :
   - session_start() : démarre ou reprend une session
   - $_SESSION['clé'] = 'valeur' : stocke une donnée dans la session
   - isset($_SESSION['clé']) : vérifie si une donnée existe
   - session_destroy() : détruit toute la session (voir logout.php)

4. SÉCURITÉ :
   - Messages d'erreur vagues : "Invalid email or password"
     → N'indique PAS si c'est l'email ou le mot de passe qui est incorrect
     → Empêche un attaquant de deviner les emails enregistrés

   - Requêtes préparées PDO : protection contre injection SQL
   - htmlspecialchars() : protection contre XSS
   - password_verify() : résistant aux timing attacks

5. REDIRECTION :
   - header('Location: url') : redirige le navigateur
   - DOIT être appelé avant tout affichage HTML
   - TOUJOURS suivi de exit() pour arrêter l'exécution

6. ORDRE DU CODE :
   ┌─────────────────────────────────────────┐
   │ 1. session_start()                      │
   │ 2. require_once 'config/db.php'                │
   │ 3. Traitement du formulaire             │
   │ 4. Redirection si succès                │
   │ 5. include 'includes/header.php'                 │
   │ 6. Affichage HTML                       │
   └─────────────────────────────────────────┘

7. DIFFÉRENCES AVEC REGISTER.PHP :
   - login.php : password_verify() pour comparer
   - register.php : password_hash() pour créer le hash
   - login.php : cherche un utilisateur existant
   - register.php : crée un nouvel utilisateur

===============================================================================
-->
