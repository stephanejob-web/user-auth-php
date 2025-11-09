<?php
/**
 * ============================================================================
 * PAGE D'INSCRIPTION (REGISTER.PHP)
 * ============================================================================
 *
 * Cette page permet à un nouveau visiteur de créer un compte.
 *
 * Étapes du processus :
 * 1. Afficher le formulaire d'inscription
 * 2. Valider les données soumises (email, mot de passe, confirmation)
 * 3. Vérifier que l'email n'existe pas déjà
 * 4. Hasher le mot de passe avec password_hash()
 * 5. Insérer le nouvel utilisateur dans la base de données
 * 6. Afficher un message de succès
 */

// Inclure le fichier header (navigation + début HTML)
include_once 'includes/header.php';

// Inclure le fichier de connexion à la base de données
// require_once : inclut le fichier UNE SEULE FOIS
// Si le fichier a déjà été inclus, PHP l'ignore
// Différence avec include : require arrête le script si le fichier n'existe pas
require_once 'config/db.php';

// ----------------------------------------------------------------------------
// INITIALISER LES VARIABLES POUR LES MESSAGES
// ----------------------------------------------------------------------------

// $error : contiendra le message d'erreur en cas de problème de validation
// Initialisé à une chaîne vide (pas d'erreur au départ)
$error = '';

// $success : contiendra le message de succès après inscription réussie
$success = '';

// ----------------------------------------------------------------------------
// TRAITER LE FORMULAIRE SI IL A ÉTÉ SOUMIS
// ----------------------------------------------------------------------------

// $_SERVER est une superglobale qui contient des informations sur le serveur
// $_SERVER['REQUEST_METHOD'] contient la méthode HTTP utilisée : GET, POST, PUT, DELETE...
//
// Quand l'utilisateur charge la page pour la première fois : GET
// Quand l'utilisateur soumet le formulaire : POST
//
// === est la comparaison stricte (valeur ET type)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ce bloc est exécuté UNIQUEMENT si le formulaire a été soumis

    // ========================================================================
    // ÉTAPE 1 : RÉCUPÉRER LES DONNÉES DU FORMULAIRE
    // ========================================================================

    /**
     * $_POST est une superglobale qui contient les données envoyées par formulaire
     * Format : $_POST['nom_du_champ'] = 'valeur'
     *
     * Rappel HTML : <input name="email"> → $_POST['email']
     *               <input name="password"> → $_POST['password']
     */

    // Récupérer et nettoyer l'email
    // trim() : supprime les espaces au début et à la fin
    // Exemple : trim('  test@example.com  ') devient 'test@example.com'
    // Utile si l'utilisateur a fait un copier-coller avec des espaces
    $email = trim($_POST['email']);

    // Récupérer le mot de passe (PAS de trim pour les mots de passe!)
    // On ne fait PAS trim() sur les mots de passe car :
    // - Les espaces peuvent faire partie du mot de passe
    // - L'utilisateur doit taper exactement le même mot de passe pour se connecter
    $password = $_POST['password'];

    // Récupérer la confirmation du mot de passe
    $confirm_password = $_POST['confirm_password'];

    // ========================================================================
    // ÉTAPE 2 : VALIDATION DES DONNÉES
    // ========================================================================

    /**
     * IMPORTANT : Ne JAMAIS faire confiance aux données de l'utilisateur !
     *
     * Même si on a ajouté "required" dans le HTML, un utilisateur
     * malveillant peut contourner cette validation côté client.
     * Il FAUT TOUJOURS valider côté serveur (en PHP).
     */

    // ------------------------------------------------------------------------
    // VALIDATION 1 : Email vide
    // ------------------------------------------------------------------------

    // empty() : vérifie si une variable est vide
    // Considéré comme vide : '', 0, '0', NULL, FALSE, []
    // Retourne TRUE si vide, FALSE sinon
    if (empty($email)) {
        $error = "Email is required.";
    }

    // ------------------------------------------------------------------------
    // VALIDATION 2 : Format d'email invalide
    // ------------------------------------------------------------------------

    // elseif : "sinon si" - exécuté seulement si les conditions précédentes sont fausses
    // filter_var() : fonction PHP pour valider/nettoyer des variables
    // FILTER_VALIDATE_EMAIL : vérifie que l'email a un format valide
    // Exemples :
    // - "test@example.com" → valide (retourne "test@example.com")
    // - "test@example" → invalide (retourne FALSE)
    // - "test.example.com" → invalide (retourne FALSE)
    // - "@example.com" → invalide (retourne FALSE)
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // ! est l'opérateur de négation (NOT)
        // !TRUE = FALSE et !FALSE = TRUE
        // Donc si l'email est invalide, on entre dans ce bloc
        $error = "Invalid email format.";
    }

    // ------------------------------------------------------------------------
    // VALIDATION 3 : Mot de passe vide
    // ------------------------------------------------------------------------

    elseif (empty($password)) {
        $error = "Password is required.";
    }

    // ------------------------------------------------------------------------
    // VALIDATION 4 : Longueur minimale du mot de passe
    // ------------------------------------------------------------------------

    // strlen() : retourne la longueur d'une chaîne de caractères
    // Exemples :
    // - strlen("bonjour") retourne 7
    // - strlen("abc") retourne 3
    // - strlen("") retourne 0
    elseif (strlen($password) < 8) {
        // < signifie "strictement inférieur à"
        // Si le mot de passe a moins de 8 caractères, on refuse
        $error = "Password must be at least 8 characters long.";
    }

    // ------------------------------------------------------------------------
    // VALIDATION 5 : Au moins une majuscule
    // ------------------------------------------------------------------------

    // preg_match() : fonction pour chercher un pattern (expression régulière) dans une chaîne
    // Syntaxe : preg_match('/pattern/', $chaîne)
    // Retourne 1 si trouvé, 0 si pas trouvé
    //
    // '/[A-Z]/' est une expression régulière :
    // - Les / délimitent le pattern
    // - [A-Z] signifie : une lettre de A à Z en majuscule
    //
    // Exemples :
    // - preg_match('/[A-Z]/', 'Hello') retourne 1 (H est majuscule)
    // - preg_match('/[A-Z]/', 'hello') retourne 0 (aucune majuscule)
    // - preg_match('/[A-Z]/', 'TEST123') retourne 1 (T, E, S, T sont majuscules)
    elseif (!preg_match('/[A-Z]/', $password)) {
        // ! inverse le résultat : si preg_match retourne 0, !0 = TRUE
        $error = "Password must contain at least one uppercase letter.";
    }

    // ------------------------------------------------------------------------
    // VALIDATION 6 : Au moins un caractère spécial
    // ------------------------------------------------------------------------

    // '/[!@#$%^&*(),.?":{}|<>]/' : pattern pour les caractères spéciaux
    // [!@#$%^&*(),.?":{}|<>] : n'importe lequel de ces caractères
    //
    // Exemples :
    // - preg_match('/[!@#$...]/', 'Hello!') retourne 1 (! est spécial)
    // - preg_match('/[!@#$...]/', 'Hello123') retourne 0 (aucun caractère spécial)
    elseif (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
        $error = "Password must contain at least one special character.";
    }

    // ------------------------------------------------------------------------
    // VALIDATION 7 : Les deux mots de passe correspondent
    // ------------------------------------------------------------------------

    // !== : opérateur "différent strict" (valeur OU type différent)
    // Vérifie que password et confirm_password sont EXACTEMENT identiques
    //
    // Attention : les mots de passe sont sensibles à la casse !
    // "Password123!" !== "password123!" (P majuscule vs p minuscule)
    elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    }

    // ========================================================================
    // ÉTAPE 3 : SI TOUTES LES VALIDATIONS SONT PASSÉES
    // ========================================================================

    else {
        // Si on arrive ici, c'est que :
        // - L'email est valide
        // - Le mot de passe respecte toutes les règles
        // - Les deux mots de passe correspondent
        //
        // Maintenant, on doit :
        // 1. Vérifier que l'email n'existe pas déjà en base
        // 2. Hasher le mot de passe
        // 3. Insérer l'utilisateur en base

        // On utilise un bloc try/catch pour gérer les erreurs de base de données
        try {

            // ================================================================
            // VÉRIFIER SI L'EMAIL EXISTE DÉJÀ
            // ================================================================

            /**
             * REQUÊTES PRÉPARÉES PDO - PROTECTION CONTRE LES INJECTIONS SQL
             *
             * Injection SQL : attaque où l'utilisateur insère du code SQL malveillant
             * Exemple DANGEREUX (à NE JAMAIS faire) :
             * $sql = "SELECT * FROM users WHERE email = '$email'";
             * Si email = "test' OR '1'='1", la requête devient :
             * SELECT * FROM users WHERE email = 'test' OR '1'='1'
             * → retourne TOUS les utilisateurs !
             *
             * SOLUTION : Requêtes préparées avec placeholders
             */

            // prepare() : prépare une requête SQL avec des placeholders
            // :email est un placeholder (marqueur) qui sera remplacé par la vraie valeur
            // Les placeholders commencent toujours par :
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");

            // execute() : exécute la requête en remplaçant les placeholders
            // On passe un tableau associatif : ['placeholder' => 'valeur']
            // PDO se charge d'échapper les caractères dangereux
            $stmt->execute(['email' => $email]);

            // fetch() : récupère UNE SEULE ligne de résultat
            // Retourne un tableau associatif ou FALSE s'il n'y a pas de résultat
            // Exemple : ['id' => 5] si l'utilisateur existe
            //           FALSE si l'utilisateur n'existe pas
            $existing_user = $stmt->fetch();

            // ================================================================
            // SI L'EMAIL EXISTE DÉJÀ, REFUSER L'INSCRIPTION
            // ================================================================

            // Si fetch() a retourné un résultat (pas FALSE), l'email existe déjà
            if ($existing_user) {
                $error = "This email is already registered.";
            }

            // ================================================================
            // SINON, CRÉER LE NOUVEL UTILISATEUR
            // ================================================================

            else {

                // ------------------------------------------------------------
                // HASHER LE MOT DE PASSE
                // ------------------------------------------------------------

                /**
                 * SÉCURITÉ : JAMAIS stocker les mots de passe en clair !
                 *
                 * Si un pirate accède à la base de données, il ne doit PAS
                 * pouvoir lire les mots de passe.
                 *
                 * password_hash() : fonction PHP pour hasher un mot de passe
                 * - Utilise l'algorithme BCRYPT (très sécurisé)
                 * - Génère automatiquement un "salt" (sel) unique pour chaque mot de passe
                 * - Le hash est une chaîne de 60 caractères commençant par $2y$
                 *
                 * Exemple :
                 * password_hash("MonMotDePasse123!", PASSWORD_BCRYPT)
                 * → "$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy"
                 *
                 * Le même mot de passe générera un hash DIFFÉRENT à chaque fois
                 * (grâce au salt aléatoire). C'est normal et voulu !
                 */
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                // PASSWORD_BCRYPT : constante PHP pour l'algorithme BCRYPT
                // Alternatives : PASSWORD_ARGON2I, PASSWORD_ARGON2ID (plus récents mais BCRYPT suffit)

                // ------------------------------------------------------------
                // INSÉRER L'UTILISATEUR DANS LA BASE DE DONNÉES
                // ------------------------------------------------------------

                // Préparer la requête INSERT avec 3 placeholders
                // :email, :password, :is_admin seront remplacés par les vraies valeurs
                //
                // Structure de la requête :
                // INSERT INTO nom_table (colonne1, colonne2, ...) VALUES (valeur1, valeur2, ...)
                //
                // is_admin est fixé à 0 car les nouveaux utilisateurs ne sont pas admin par défaut
                // Un admin devra le promouvoir manuellement via la page admin
                $stmt = $pdo->prepare("INSERT INTO users (email, password, is_admin) VALUES (:email, :password, 0)");

                // Exécuter la requête avec les valeurs
                $stmt->execute([
                    'email' => $email,              // Email nettoyé avec trim()
                    'password' => $hashed_password  // Mot de passe hashé (JAMAIS le mot de passe en clair !)
                ]);

                // Si nous arrivons ici, l'insertion a réussi !
                // Afficher un message de succès avec un lien vers la page de connexion
                $success = "Registration successful! You can now <a href='login.php'>login</a>.";
            }

        } catch (PDOException $e) {
            // ================================================================
            // GESTION DES ERREURS DE BASE DE DONNÉES
            // ================================================================

            // Ce bloc est exécuté si une erreur SQL se produit
            // Exemples d'erreurs possibles :
            // - Connexion à la base perdue
            // - Table 'users' n'existe pas
            // - Colonne manquante
            // - Violation de contrainte UNIQUE

            // Pour la sécurité, on n'affiche PAS le message d'erreur détaillé
            // (qui pourrait révéler la structure de la base de données)
            // En production, on devrait logger $e->getMessage() dans un fichier
            $error = "An error occurred. Please try again.";
        }
    }
}
?>

<!-- ========================================================================== -->
<!-- CONTENU HTML DE LA PAGE D'INSCRIPTION -->
<!-- ========================================================================== -->

<main>

    <h2>Register</h2>

    <?php
    /**
     * --------------------------------------------------------------------
     * AFFICHER LE MESSAGE D'ERREUR SI IL EXISTE
     * --------------------------------------------------------------------
     */

    // Si $error n'est pas vide (donc il y a eu une erreur)
    if (!empty($error)):
    ?>

        <!-- Message d'erreur en rouge -->
        <p style="color: red;">
            <!-- style="color: red;" est du CSS inline (autorisé ici car c'est pour un message d'erreur) -->
            <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
            <!-- htmlspecialchars() pour éviter les attaques XSS même dans les messages d'erreur -->
        </p>

    <?php
    endif; // Fin de l'affichage du message d'erreur
    ?>

    <?php
    /**
     * --------------------------------------------------------------------
     * AFFICHER LE MESSAGE DE SUCCÈS SI IL EXISTE
     * --------------------------------------------------------------------
     */

    if (!empty($success)):
    ?>

        <!-- Message de succès en vert -->
        <p style="color: green;">
            <strong><?php echo $success; ?></strong>
            <!-- Ici on n'utilise PAS htmlspecialchars() car $success contient du HTML (<a>)
                 Et ce HTML vient de NOUS (pas de l'utilisateur), donc c'est sûr -->
        </p>

    <?php
    endif; // Fin de l'affichage du message de succès
    ?>

    <!-- ========================================================================== -->
    <!-- FORMULAIRE D'INSCRIPTION -->
    <!-- ========================================================================== -->

    <!--
    <form> : balise pour créer un formulaire
    - method="POST" : les données seront envoyées via la méthode HTTP POST
      (alternatives : GET, mais POST est recommandé pour les formulaires sensibles)
    - action="register.php" : URL vers laquelle envoyer les données
      Ici, on envoie vers la même page (register.php traite ses propres données)
    -->
    <form method="POST" action="register.php">

        <!-- ================================================================ -->
        <!-- CHAMP EMAIL -->
        <!-- ================================================================ -->

        <p>
            <!--
            <label for="email"> : étiquette du champ
            - for="email" : lie le label à l'input avec id="email"
            - Quand on clique sur le label, le champ correspondant reçoit le focus
            - Important pour l'accessibilité (lecteurs d'écran)
            -->
            <label for="email">Email:</label><br>
            <!-- <br> : saut de ligne (line break) -->

            <!--
            <input> : champ de saisie
            - type="email" : spécifie que c'est un email
              → Validation HTML5 basique (vérifie qu'il y a un @)
              → Clavier adapté sur mobile (avec @ facilement accessible)
            - id="email" : identifiant unique du champ (pour le label)
            - name="email" : nom du champ (c'est ce nom qui sera dans $_POST['email'])
            - required : attribut HTML5 qui rend le champ obligatoire
              → Le formulaire ne peut pas être soumis si le champ est vide
              → ATTENTION : cette validation peut être contournée ! Il FAUT aussi valider en PHP
            -->
            <input type="email" id="email" name="email" required>
        </p>

        <!-- ================================================================ -->
        <!-- CHAMP MOT DE PASSE -->
        <!-- ================================================================ -->

        <p>
            <label for="password">Password:</label><br>

            <!--
            type="password" : masque les caractères tapés (affiche des points ou étoiles)
            IMPORTANT : les données sont QUAND MÊME envoyées en clair au serveur !
            C'est juste un masquage visuel pour éviter qu'on lise par-dessus l'épaule
            En production, utiliser HTTPS pour chiffrer la transmission
            -->
            <input type="password" id="password" name="password" required>
        </p>

        <!-- ================================================================ -->
        <!-- CHAMP CONFIRMATION DU MOT DE PASSE -->
        <!-- ================================================================ -->

        <p>
            <label for="confirm_password">Confirm Password:</label><br>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </p>

        <!-- ================================================================ -->
        <!-- BOUTON DE SOUMISSION -->
        <!-- ================================================================ -->

        <p>
            <!--
            <button type="submit"> : bouton qui soumet le formulaire
            - Quand on clique dessus, le formulaire est envoyé à action="register.php"
            - Alternative : <input type="submit" value="Register">
            -->
            <button type="submit">Register</button>
        </p>

    </form>
    <!-- Fin du formulaire -->

    <!-- ========================================================================== -->
    <!-- LIEN VERS LA PAGE DE CONNEXION -->
    <!-- ========================================================================== -->

    <!-- Si l'utilisateur a déjà un compte, il peut aller directement se connecter -->
    <p>Already have an account? <a href="login.php">Login here</a></p>

</main>

</body>
</html>

<!--
===============================================================================
NOTES PÉDAGOGIQUES - VALIDATION ET SÉCURITÉ
===============================================================================

1. VALIDATION EN DEUX ÉTAPES :
   - Client (HTML5) : rapide, améliore l'UX, PEUT ÊTRE CONTOURNÉE
   - Serveur (PHP) : sécurisée, OBLIGATOIRE, ne peut pas être contournée

2. RÈGLES DE VALIDATION :
   - Toujours valider côté serveur même si validation côté client
   - Ne jamais faire confiance aux données utilisateur
   - Utiliser filter_var() pour valider les formats
   - Utiliser preg_match() pour vérifier les patterns complexes

3. SÉCURITÉ DES MOTS DE PASSE :
   - JAMAIS stocker en clair dans la base de données
   - Toujours utiliser password_hash() avec BCRYPT ou mieux
   - Ne PAS utiliser MD5 ou SHA1 (obsolètes et non sécurisés)
   - Le salt est automatiquement généré par password_hash()

4. PROTECTION CONTRE LES INJECTIONS SQL :
   - TOUJOURS utiliser des requêtes préparées avec PDO
   - JAMAIS concaténer des variables dans une requête SQL
   - Les placeholders (:email, :password) sont échappés automatiquement

5. PROTECTION CONTRE XSS :
   - Utiliser htmlspecialchars() pour afficher des données utilisateur
   - Convertit <script> en &lt;script&gt; (inoffensif)

6. GESTION DES ERREURS :
   - try/catch pour attraper les exceptions PDO
   - Messages d'erreur génériques pour l'utilisateur (sécurité)
   - Logger les détails d'erreur dans un fichier (débogage)
===============================================================================
-->
