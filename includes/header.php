<?php
/**
 * ============================================================================
 * FICHIER HEADER - NAVIGATION ET EN-TÊTE HTML
 * ============================================================================
 *
 * Ce fichier contient :
 * 1. La gestion de la session PHP
 * 2. Le début du document HTML (<!DOCTYPE>, <html>, <head>)
 * 3. Le menu de navigation qui s'adapte selon l'état de l'utilisateur
 *
 * Il sera inclus au début de chaque page avec include 'includes/header.php';
 *
 * IMPORTANT : Définir $base_path AVANT d'inclure ce fichier
 * - Depuis la racine : $base_path = '';
 * - Depuis admin/ : $base_path = '../';
 */

// ----------------------------------------------------------------------------
// VARIABLE DE CHEMIN DE BASE
// ----------------------------------------------------------------------------

// Si $base_path n'est pas défini, on le détecte automatiquement
if (!isset($base_path)) {
    // On vérifie si on est dans un sous-dossier
    $base_path = (basename(dirname($_SERVER['PHP_SELF'])) !== 'user-auth-php') ? '../' : '';
}

// ----------------------------------------------------------------------------
// GESTION DE LA SESSION PHP
// ----------------------------------------------------------------------------

// session_status() retourne l'état actuel de la session :
// - PHP_SESSION_DISABLED : les sessions sont désactivées (rare)
// - PHP_SESSION_NONE : aucune session n'est active (c'est ce qu'on vérifie)
// - PHP_SESSION_ACTIVE : une session est déjà active

// === est l'opérateur de comparaison stricte (valeur ET type doivent correspondre)
// Différence avec == : '1' == 1 est TRUE, mais '1' === 1 est FALSE
if (session_status() === PHP_SESSION_NONE) {

    // session_start() démarre une nouvelle session OU reprend une session existante
    //
    // Qu'est-ce qu'une session ?
    // - Un mécanisme pour stocker des informations côté serveur entre les requêtes HTTP
    // - Chaque visiteur reçoit un identifiant unique (session ID) stocké dans un cookie
    // - Les données de session sont stockées sur le serveur (pas dans le navigateur)
    // - Utilisé pour savoir qui est connecté, stocker des préférences temporaires, etc.
    //
    // Fonctionnement :
    // 1. session_start() crée un cookie nommé PHPSESSID dans le navigateur
    // 2. Ce cookie contient un identifiant unique (ex: a1b2c3d4e5f6...)
    // 3. PHP crée un fichier temporaire sur le serveur lié à cet identifiant
    // 4. Tout ce qu'on met dans $_SESSION est sauvegardé dans ce fichier
    // 5. À chaque requête, PHP lit le cookie et charge les données de session
    session_start();
}

// À ce stade, la variable superglobale $_SESSION est disponible
// C'est un tableau associatif pour stocker/lire des données entre les pages
// Exemple : $_SESSION['user_id'] = 5; sera accessible sur toutes les pages
?>
<!DOCTYPE html>
<!-- DOCTYPE indique au navigateur qu'on utilise HTML5 -->

<html lang="fr">
<!-- <html> est la racine du document -->
<!-- lang="fr" indique que le contenu principal est en français (utile pour l'accessibilité) -->

<head>
    <!-- <head> contient les métadonnées (informations sur la page, non visibles) -->

    <!-- Définir l'encodage des caractères pour afficher correctement les accents et caractères spéciaux -->
    <!-- UTF-8 est l'encodage universel qui supporte tous les alphabets (latin, arabe, chinois, etc.) -->
    <meta charset="UTF-8">

    <!-- Balise pour le responsive design (adaptation aux mobiles et tablettes) -->
    <!-- width=device-width : la largeur de la page = largeur de l'écran -->
    <!-- initial-scale=1.0 : pas de zoom initial -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Titre de la page affiché dans l'onglet du navigateur -->
    <title>User Authentication System</title>

    <!-- Lien vers la feuille de style CSS -->
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/style.css">
</head>

<body>
    <!-- <body> contient tout le contenu visible de la page -->

    <!-- ========================================================================== -->
    <!-- EN-TÊTE DE LA PAGE -->
    <!-- ========================================================================== -->

    <header>
        <!-- <header> est une balise sémantique HTML5 pour l'en-tête de page -->

        <!-- Titre principal de l'application -->
        <h1>User Authentication System</h1>
        <!-- <h1> est le titre le plus important (heading 1) -->
        <!-- Il ne devrait y avoir qu'un seul <h1> par page pour le SEO et l'accessibilité -->

        <!-- ========================================================================== -->
        <!-- MENU DE NAVIGATION -->
        <!-- ========================================================================== -->

        <nav>
            <!-- <nav> est une balise sémantique pour les menus de navigation -->

            <ul>
                <!-- <ul> = Unordered List (liste non ordonnée, avec des puces) -->
                <!-- On l'utilise pour structurer le menu car un menu est une liste de liens -->

                <!-- =============================================== -->
                <!-- LIEN TOUJOURS VISIBLE : HOME -->
                <!-- =============================================== -->

                <li>
                    <!-- <li> = List Item (élément de liste) -->
                    <a href="<?php echo $base_path; ?>index.php">Home</a>
                    <!-- <a> = Anchor (lien hypertexte) -->
                    <!-- href = "hypertext reference" = destination du lien -->
                </li>

                <?php
                /**
                 * -----------------------------------------------------------------------
                 * NAVIGATION CONDITIONNELLE SELON L'ÉTAT DE CONNEXION
                 * -----------------------------------------------------------------------
                 *
                 * On affiche différents liens selon que l'utilisateur est :
                 * - Connecté : afficher Profile, Logout (et Admin si admin)
                 * - Non connecté : afficher Register, Login
                 */

                // Vérifier si l'utilisateur est connecté
                // isset() vérifie si une variable existe et n'est pas NULL
                // Quand un utilisateur se connecte dans login.php, on fait : $_SESSION['user_id'] = ...
                // Si cette variable existe, c'est qu'il est connecté
                if (isset($_SESSION['user_id'])):
                ?>

                    <!-- ================================================== -->
                    <!-- LIENS POUR UTILISATEURS CONNECTÉS -->
                    <!-- ================================================== -->

                    <li>
                        <a href="<?php echo $base_path; ?>profile.php">Profile</a>
                        <!-- Lien vers la page de modification du profil -->
                    </li>

                    <li>
                        <a href="<?php echo $base_path; ?>logout.php">Logout</a>
                        <!-- Lien pour se déconnecter -->
                    </li>

                    <?php
                    /**
                     * -------------------------------------------------------------------
                     * LIEN ADMIN (visible UNIQUEMENT pour les administrateurs)
                     * -------------------------------------------------------------------
                     *
                     * Double vérification :
                     * 1. La variable de session is_admin existe (isset)
                     * 2. Sa valeur est égale à 1 (== 1)
                     *
                     * Rappel : dans la base de données, is_admin est un TINYINT(1)
                     * - 0 = utilisateur normal
                     * - 1 = administrateur
                     */

                    // isset($_SESSION['is_admin']) : vérifie que la variable existe
                    // && : opérateur logique ET (les deux conditions doivent être vraies)
                    // $_SESSION['is_admin'] == 1 : vérifie que la valeur est 1
                    // == (double égal) : comparaison de valeur (1 == "1" est TRUE)
                    // === (triple égal) : comparaison stricte (1 === "1" est FALSE)
                    if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1):
                    ?>

                        <!-- Lien Admin visible UNIQUEMENT pour les administrateurs -->
                        <li>
                            <a href="<?php echo $base_path; ?>admin/admin.php">Admin</a>
                            <!-- Ce lien mène au tableau de bord administrateur -->
                        </li>

                    <?php
                    endif; // Fin de la condition is_admin
                    ?>

                <?php
                else: // Si l'utilisateur N'EST PAS connecté
                ?>

                    <!-- ================================================== -->
                    <!-- LIENS POUR VISITEURS NON CONNECTÉS -->
                    <!-- ================================================== -->

                    <li>
                        <a href="<?php echo $base_path; ?>register.php">Register</a>
                        <!-- Lien vers la page d'inscription -->
                    </li>

                    <li>
                        <a href="<?php echo $base_path; ?>login.php">Login</a>
                        <!-- Lien vers la page de connexion -->
                    </li>

                <?php
                endif; // Fin de la condition isset($_SESSION['user_id'])
                ?>

            </ul>
            <!-- Fin de la liste du menu -->

        </nav>
        <!-- Fin de la navigation -->

        <!-- Ligne horizontale pour séparer visuellement le header du contenu -->
        <!-- <hr> = Horizontal Rule (ligne de séparation) -->
        <!-- C'est une balise auto-fermante (pas besoin de </hr>) -->
        <hr>

    </header>
    <!-- Fin du header -->

    <!-- ========================================================================== -->
    <!-- NOTE IMPORTANTE POUR LES ÉTUDIANTS -->
    <!-- ========================================================================== -->

    <!--
    APRÈS CE FICHIER, CHAQUE PAGE AJOUTERA SON CONTENU PRINCIPAL

    Structure typique d'une page :
    2. <main> ... </main>                  ← Contenu spécifique de la page
    3. </body></html>                      ← Fermeture des balises HTML

    Le header.php ne ferme PAS les balises <body> et <html>
    car chaque page doit ajouter son contenu avant de les fermer !
    -->
