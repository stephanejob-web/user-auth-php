<?php
/**
 * ============================================================================
 * PAGE D'ACCUEIL (INDEX.PHP)
 * ============================================================================
 *
 * Cette page est la page d'accueil de l'application.
 * Elle affiche :
 * - Un message de bienvenue
 * - Des informations personnalisées si l'utilisateur est connecté
 * - Les fonctionnalités du système
 * - Les règles de mot de passe
 */

// Inclure le fichier header.php qui contient :
// - La gestion de session (session_start())
// - Le début du code HTML (<html>, <head>, <body>)
// - Le menu de navigation
include_once 'includes/header.php';
?>

<!-- ========================================================================== -->
<!-- CONTENU PRINCIPAL DE LA PAGE D'ACCUEIL -->
<!-- ========================================================================== -->

<main>
    <!-- <main> est une balise sémantique HTML5 pour le contenu principal -->
    <!-- Il ne devrait y avoir qu'une seule balise <main> par page -->

    <!-- Titre de la page d'accueil -->
    <h2>Welcome to User Authentication System</h2>
    <!-- <h2> est un titre de niveau 2 (moins important que <h1>) -->

    <?php
    /**
     * ------------------------------------------------------------------------
     * AFFICHAGE CONDITIONNEL SELON L'ÉTAT DE CONNEXION
     * ------------------------------------------------------------------------
     *
     * On vérifie si l'utilisateur est connecté pour afficher :
     * - Message personnalisé avec son email si connecté
     * - Invitation à se connecter/inscrire si non connecté
     */

    // Vérifier si la variable de session 'user_id' existe
    // Si elle existe, c'est qu'un utilisateur est connecté
    if (isset($_SESSION['user_id'])):
        // Bloc exécuté SI l'utilisateur EST connecté
    ?>

        <!-- ================================================== -->
        <!-- MESSAGE POUR UTILISATEUR CONNECTÉ -->
        <!-- ================================================== -->

        <!-- Afficher un message personnalisé avec l'email de l'utilisateur -->
        <p>Hello, <?php echo htmlspecialchars($_SESSION['email']); ?>!</p>
        <!--
        Explication de cette ligne :
        - <p> : paragraphe
        - echo : affiche du texte
        - $_SESSION['email'] : récupère l'email stocké dans la session
        - htmlspecialchars() : fonction de sécurité IMPORTANTE

        Pourquoi htmlspecialchars() ?
        - Convertit les caractères spéciaux HTML en entités HTML
        - Empêche les attaques XSS (Cross-Site Scripting)
        - Exemple : si l'email était "<script>alert('hack')</script>"
          Sans htmlspecialchars : le script serait exécuté (DANGER!)
          Avec htmlspecialchars : affiché comme du texte (SÛR!)
        - Transforme : < en &lt; , > en &gt; , " en &quot; , etc.

        RÈGLE : TOUJOURS utiliser htmlspecialchars() quand on affiche
        des données qui viennent de l'utilisateur ou de la base de données
        -->

        <!-- Message de confirmation de connexion -->
        <p>You are successfully logged in.</p>

        <?php
        /**
         * --------------------------------------------------------------------
         * MESSAGE SPÉCIAL POUR LES ADMINISTRATEURS
         * --------------------------------------------------------------------
         */

        // Double vérification :
        // 1. La variable is_admin existe dans la session
        // 2. Sa valeur est égale à 1
        if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1):
        ?>

            <!-- Message visible uniquement pour les admins -->
            <p><strong>You have administrator privileges.</strong></p>
            <!--
            <strong> met le texte en gras (bold) pour l'accentuer
            Alternatives :
            - <b> : aussi en gras, mais moins sémantique
            - <em> : emphase (italique par défaut)
            -->

        <?php
        endif; // Fin de la vérification is_admin
        ?>

    <?php
    else: // Sinon, si l'utilisateur N'EST PAS connecté
        // Bloc exécuté SI l'utilisateur N'EST PAS connecté
    ?>

        <!-- ================================================== -->
        <!-- MESSAGE POUR VISITEUR NON CONNECTÉ -->
        <!-- ================================================== -->

        <!-- Invitation à se connecter ou s'inscrire -->
        <p>Please <a href="login.php">login</a> or <a href="register.php">register</a> to access all features.</p>
        <!--
        <a href="..."> : lien hypertexte
        Quand l'utilisateur clique sur "login", il est redirigé vers login.php
        Quand il clique sur "register", il va vers register.php
        -->

    <?php
    endif; // Fin de la vérification isset($_SESSION['user_id'])
    ?>

    <!-- ========================================================================== -->
    <!-- SECTION : FONCTIONNALITÉS DU SYSTÈME -->
    <!-- ========================================================================== -->

    <h3>Features</h3>
    <!-- <h3> est un titre de niveau 3 (sous-section) -->

    <ul>
        <!-- <ul> : liste non ordonnée (avec des puces) -->

        <li>User registration with secure password</li>
        <!-- <li> : élément de liste -->

        <li>User login and logout</li>

        <li>Profile management (edit email and password)</li>

        <li>Admin dashboard (manage all users)</li>

    </ul>
    <!--
    Autres types de listes :
    - <ol> : Ordered List (liste numérotée 1, 2, 3...)
    - <dl> : Description List (liste de termes et définitions)
    -->

    <!-- ========================================================================== -->
    <!-- SECTION : RÈGLES DE MOT DE PASSE -->
    <!-- ========================================================================== -->

    <h3>Password Requirements</h3>

    <ul>
        <li>Minimum 8 characters</li>
        <!--
        strlen() en PHP permet de vérifier la longueur
        Exemple : strlen("bonjour") retourne 7
        -->

        <li>At least one uppercase letter</li>
        <!--
        En PHP, on vérifie avec preg_match('/[A-Z]/', $password)
        [A-Z] signifie : une lettre majuscule de A à Z
        -->

        <li>At least one special character (!@#$%^&*(),.?":{}|&lt;&gt;)</li>
        <!--
        &lt; est l'entité HTML pour < (less than)
        &gt; est l'entité HTML pour > (greater than)

        Pourquoi utiliser &lt; et &gt; ?
        - < et > sont des caractères réservés en HTML (pour les balises)
        - Pour les afficher comme du texte, on utilise leurs entités HTML

        En PHP, on vérifie avec preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)
        -->

    </ul>

</main>
<!-- Fin du contenu principal -->

<!-- ========================================================================== -->
<!-- FERMETURE DES BALISES HTML -->
<!-- ========================================================================== -->

</body>
<!-- Fin du <body> qui a été ouvert dans header.php -->

</html>
<!-- Fin du <html> qui a été ouvert dans header.php -->

<!--
===============================================================================
NOTES PÉDAGOGIQUES - STRUCTURE D'UNE PAGE PHP/HTML
===============================================================================

1. ORDRE DES ÉLÉMENTS :
   - PHP en haut (logique métier, traitements)
   - HTML en bas (présentation, affichage)

2. VARIABLES DE SESSION :
   - $_SESSION['user_id'] : ID de l'utilisateur connecté
   - $_SESSION['email'] : Email de l'utilisateur
   - $_SESSION['is_admin'] : 1 si admin, 0 sinon

3. SÉCURITÉ :
   - Toujours utiliser htmlspecialchars() pour afficher des données
   - Ne jamais faire confiance aux données venant de l'utilisateur

4. SYNTAXE ALTERNATIVE PHP :
   - if (...): ... else: ... endif;
   - Plus lisible quand on mélange PHP et HTML
   - Équivalent à if (...) { ... } else { ... }

5. BALISES SÉMANTIQUES HTML5 :
   - <header> : en-tête de page
   - <main> : contenu principal
   - <nav> : navigation
   - <footer> : pied de page (non utilisé ici)
   - Améliorent l'accessibilité et le SEO
===============================================================================
-->
