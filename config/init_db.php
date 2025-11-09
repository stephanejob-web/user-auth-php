<?php
/**
 * ============================================================================
 * SCRIPT D'INITIALISATION DE LA BASE DE DONNÉES SQLite
 * ============================================================================
 *
 * Ce script crée automatiquement :
 * 1. Le fichier de base de données SQLite (database.db)
 * 2. La table 'users' avec tous ses champs
 * 3. Un utilisateur administrateur par défaut
 *
 * COMMENT UTILISER CE SCRIPT :
 *
 * Méthode 1 - Via le navigateur :
 * 1. Assurez-vous d'avoir renommé db_sqlite.php en db.php
 * 2. Ouvrez votre navigateur et allez sur : http://localhost:8000/init_db.php
 * 3. La base de données sera créée
 * 4. Vous verrez un message de succès
 *
 * Méthode 2 - Via la ligne de commande :
 * php init_db.php
 *
 * IMPORTANT :
 * - Exécutez ce script UNE SEULE FOIS au début
 * - Si vous voulez réinitialiser la base, supprimez database.db et relancez ce script
 * - Ce script écrase la base existante si elle existe déjà
 */

// ----------------------------------------------------------------------------
// ÉTAPE 1 : Définir le chemin de la base de données
// ----------------------------------------------------------------------------

// __DIR__ : répertoire actuel où se trouve ce script (config/)
// On remonte d'un niveau (..) puis on va dans database/
$db_path = dirname(__DIR__) . '/database/database.db';

// ----------------------------------------------------------------------------
// ÉTAPE 2 : Supprimer l'ancienne base si elle existe (pour réinitialisation)
// ----------------------------------------------------------------------------

/**
 * file_exists() : vérifie si un fichier existe
 * unlink() : supprime un fichier
 *
 * Si database.db existe déjà, on le supprime pour repartir de zéro
 * Cela évite les erreurs "table already exists"
 */
if (file_exists($db_path)) {
    // Supprimer le fichier de base de données
    unlink($db_path);
    echo "✓ Ancienne base de données supprimée<br>\n";
}

// ----------------------------------------------------------------------------
// ÉTAPE 3 : Créer la connexion PDO à SQLite
// ----------------------------------------------------------------------------

try {

    // Créer la connexion PDO
    // Si le fichier database.db n'existe pas, PDO le crée automatiquement
    $pdo = new PDO("sqlite:$db_path");

    // Configurer le mode d'erreur : lancer des exceptions
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Activer les contraintes de clés étrangères (important pour SQLite)
    $pdo->exec('PRAGMA foreign_keys = ON');

    echo "✓ Connexion à SQLite établie<br>\n";

    // ========================================================================
    // ÉTAPE 4 : Créer la table 'users'
    // ========================================================================

    /**
     * Schéma de la table users pour SQLite :
     *
     * Différences avec MySQL :
     * - INT AUTO_INCREMENT → INTEGER PRIMARY KEY AUTOINCREMENT
     * - VARCHAR(255) → TEXT
     * - TINYINT(1) → INTEGER
     * - TIMESTAMP → TEXT (SQLite n'a pas de type TIMESTAMP natif)
     *
     * Colonnes :
     * - id : clé primaire, auto-incrémentée
     * - email : adresse email, unique, non nulle
     * - password : mot de passe hashé, non nul
     * - is_admin : 0 = utilisateur normal, 1 = administrateur
     * - created_at : date de création (format texte ISO 8601)
     */

    $sql_create_table = "
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            email TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            is_admin INTEGER DEFAULT 0,
            created_at TEXT DEFAULT CURRENT_TIMESTAMP
        )
    ";

    // Explication ligne par ligne :
    //
    // CREATE TABLE IF NOT EXISTS users
    // - CREATE TABLE : créer une nouvelle table
    // - IF NOT EXISTS : seulement si elle n'existe pas déjà (évite les erreurs)
    // - users : nom de la table
    //
    // id INTEGER PRIMARY KEY AUTOINCREMENT
    // - id : nom de la colonne
    // - INTEGER : type entier
    // - PRIMARY KEY : clé primaire (identifiant unique de chaque ligne)
    // - AUTOINCREMENT : s'incrémente automatiquement (1, 2, 3, ...)
    //
    // email TEXT NOT NULL UNIQUE
    // - email : nom de la colonne
    // - TEXT : type texte (équivalent à VARCHAR en MySQL)
    // - NOT NULL : la colonne ne peut pas être vide
    // - UNIQUE : deux utilisateurs ne peuvent pas avoir le même email
    //
    // password TEXT NOT NULL
    // - password : stocke le hash du mot de passe (jamais le mot de passe en clair!)
    // - TEXT : suffisant pour un hash BCRYPT (60 caractères)
    // - NOT NULL : obligatoire
    //
    // is_admin INTEGER DEFAULT 0
    // - is_admin : flag pour savoir si c'est un admin
    // - INTEGER : type entier (0 ou 1)
    // - DEFAULT 0 : valeur par défaut = 0 (pas admin)
    //
    // created_at TEXT DEFAULT CURRENT_TIMESTAMP
    // - created_at : date et heure de création du compte
    // - TEXT : SQLite stocke les dates en texte format ISO 8601
    // - DEFAULT CURRENT_TIMESTAMP : rempli automatiquement à la création

    // Exécuter la création de table
    $pdo->exec($sql_create_table);

    echo "✓ Table 'users' créée<br>\n";

    // ========================================================================
    // ÉTAPE 5 : Créer un utilisateur administrateur par défaut
    // ========================================================================

    /**
     * Pour que l'application soit utilisable immédiatement,
     * on crée un compte administrateur par défaut.
     *
     * Identifiants :
     * - Email : admin@example.com
     * - Mot de passe : Admin123!
     *
     * IMPORTANT : En production, il faut :
     * 1. Changer ces identifiants immédiatement après la première connexion
     * 2. Ou supprimer cet utilisateur et en créer un autre
     */

    // Email de l'admin par défaut
    $admin_email = 'admin@example.com';

    // Mot de passe en clair (TEMPORAIRE - seulement pour l'initialisation)
    $admin_password = 'Admin123!';

    // Hasher le mot de passe avec BCRYPT
    // password_hash() génère un hash sécurisé de 60 caractères
    // Exemple : $2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy
    $admin_password_hash = password_hash($admin_password, PASSWORD_BCRYPT);

    // Préparer la requête d'insertion
    // Placeholders : :email, :password, :is_admin
    $sql_insert_admin = "
        INSERT INTO users (email, password, is_admin)
        VALUES (:email, :password, :is_admin)
    ";

    // Préparer la requête
    $stmt = $pdo->prepare($sql_insert_admin);

    // Exécuter avec les valeurs
    $stmt->execute([
        'email' => $admin_email,           // admin@example.com
        'password' => $admin_password_hash, // Hash BCRYPT du mot de passe
        'is_admin' => 1                     // 1 = administrateur
    ]);

    echo "✓ Utilisateur administrateur créé<br>\n";

    // ========================================================================
    // ÉTAPE 6 : Afficher les informations de connexion
    // ========================================================================

    echo "<br>\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━<br>\n";
    echo "<strong>✓ Base de données initialisée avec succès !</strong><br>\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━<br>\n";
    echo "<br>\n";
    echo "<strong>Compte administrateur créé :</strong><br>\n";
    echo "Email : <strong>admin@example.com</strong><br>\n";
    echo "Mot de passe : <strong>Admin123!</strong><br>\n";
    echo "<br>\n";
    echo "Fichier de base de données : <strong>database.db</strong><br>\n";
    echo "<br>\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━<br>\n";
    echo "<br>\n";
    echo "Vous pouvez maintenant :<br>\n";
    echo "1. Aller sur <a href='index.php'>index.php</a><br>\n";
    echo "2. Vous connecter avec les identifiants ci-dessus<br>\n";
    echo "3. Créer de nouveaux utilisateurs<br>\n";
    echo "<br>\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━<br>\n";
    echo "<br>\n";
    echo "<strong>Note :</strong> Pour réinitialiser la base de données, supprimez le fichier <code>database.db</code> et relancez ce script.<br>\n";

} catch (PDOException $e) {

    // ========================================================================
    // GESTION DES ERREURS
    // ========================================================================

    // En cas d'erreur SQL, afficher le message
    echo "<strong style='color: red;'>Erreur lors de l'initialisation :</strong><br>\n";
    echo $e->getMessage();
    echo "<br><br>\n";

    // Erreurs courantes :
    //
    // "unable to open database file"
    // → Le dossier n'a pas les permissions d'écriture
    // → Solution : chmod 755 sur le dossier
    //
    // "table users already exists"
    // → La table existe déjà (ne devrait pas arriver avec IF NOT EXISTS)
    // → Solution : supprimer database.db et relancer
    //
    // "UNIQUE constraint failed: users.email"
    // → L'email existe déjà
    // → Solution : supprimer database.db et relancer
}

/*
===============================================================================
NOTES PÉDAGOGIQUES - INITIALISATION DE BASE DE DONNÉES
===============================================================================

1. CRÉATION AUTOMATIQUE DU FICHIER :
   - PDO crée automatiquement database.db s'il n'existe pas
   - Pas besoin de créer le fichier manuellement
   - Le fichier est créé dans le même dossier que ce script

2. RÉINITIALISATION :
   Pour repartir de zéro :
   - Méthode 1 : Supprimer database.db et relancer init_db.php
   - Méthode 2 : Ce script supprime automatiquement l'ancien fichier

3. HASH DE MOT DE PASSE :
   password_hash('Admin123!', PASSWORD_BCRYPT)
   - Génère : $2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy
   - Chaque fois différent (salt aléatoire)
   - 60 caractères
   - Irréversible (impossible de retrouver le mot de passe original)

4. SÉCURITÉ DU COMPTE ADMIN PAR DÉFAUT :
   ⚠️ ATTENTION : En production, il faut :
   - Changer le mot de passe immédiatement
   - Ou supprimer ce compte et en créer un autre
   - Ne JAMAIS laisser admin@example.com / Admin123! en production

5. VÉRIFICATION :
   Après avoir lancé ce script, vérifiez :
   - Le fichier database.db existe dans le dossier
   - Sa taille est > 0 octets (environ 16 Ko)
   - Vous pouvez vous connecter avec admin@example.com / Admin123!

6. OUTILS POUR EXPLORER LA BASE SQLite :
   - DB Browser for SQLite : https://sqlitebrowser.org/
   - SQLite Studio : https://sqlitestudio.pl/
   - Extension VS Code : SQLite Viewer
   - Ligne de commande : sqlite3 database.db

7. COMMANDES SQLite UTILES :
   En ligne de commande (sqlite3 database.db) :
   .tables                    → Liste les tables
   .schema users              → Affiche la structure de la table users
   SELECT * FROM users;       → Affiche tous les utilisateurs
   .exit                      → Quitter

8. DIFFÉRENCES AVEC UN SCRIPT SQL MYSQL :
   MySQL (exécuté dans phpMyAdmin) :
   - Plusieurs étapes (créer DB, créer table, insérer données)
   - Interface graphique
   - Serveur séparé

   SQLite (avec ce script PHP) :
   - Tout en un seul script PHP
   - Exécutable via navigateur ou CLI
   - Fichier local

===============================================================================
*/
?>
