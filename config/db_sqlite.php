<?php
/**
 * ============================================================================
 * FICHIER DE CONNEXION À LA BASE DE DONNÉES SQLite
 * ============================================================================
 *
 * VERSION ALTERNATIVE du fichier db.php pour utiliser SQLite au lieu de MySQL.
 *
 * POUR LES ÉTUDIANTS :
 * Pour utiliser SQLite au lieu de MySQL, renommez ce fichier en "db.php"
 * (et renommez l'ancien db.php en db_mysql.php pour le garder)
 *
 * Avantages de SQLite pour l'apprentissage :
 * - Pas besoin d'installer MySQL
 * - Pas besoin de phpMyAdmin
 * - Base de données dans un simple fichier .db
 * - Facile à partager et à sauvegarder
 * - Parfait pour les projets d'apprentissage
 *
 * Note : En production, préférez MySQL/PostgreSQL pour les applications web
 */

// ----------------------------------------------------------------------------
// ÉTAPE 1 : Définir le chemin de la base de données SQLite
// ----------------------------------------------------------------------------

// __DIR__ : constante PHP qui contient le chemin du répertoire actuel
// Exemple : /var/www/html/user-auth-php
$db_path = __DIR__ . '/database.db';

// Explication :
// - __DIR__ : répertoire où se trouve ce fichier db_sqlite.php
// - . : opérateur de concaténation (colle deux chaînes)
// - '/database.db' : nom du fichier de base de données
// Résultat : /var/www/html/user-auth-php/database.db

// ----------------------------------------------------------------------------
// ÉTAPE 2 : Construire le DSN pour SQLite
// ----------------------------------------------------------------------------

// DSN (Data Source Name) pour SQLite
// Format : "sqlite:chemin/vers/fichier.db"
$dsn = "sqlite:$db_path";

// Différences avec MySQL :
// MySQL : "mysql:host=localhost;dbname=user_auth_db;charset=utf8mb4"
// SQLite : "sqlite:/chemin/vers/database.db"
//
// SQLite est plus simple :
// - Pas de host (pas de serveur)
// - Pas de charset (UTF-8 par défaut)
// - Juste un chemin vers le fichier

// ----------------------------------------------------------------------------
// ÉTAPE 3 : Établir la connexion avec PDO
// ----------------------------------------------------------------------------

try {

    // Créer la connexion PDO pour SQLite
    // Différence avec MySQL : pas de username ni password pour SQLite
    $pdo = new PDO($dsn);

    // ----------------------------------------------------------------------------
    // CONFIGURATION 1 : Mode de gestion des erreurs
    // ----------------------------------------------------------------------------

    // Identique à MySQL : on veut des exceptions en cas d'erreur
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ----------------------------------------------------------------------------
    // CONFIGURATION 2 : Mode de récupération des données
    // ----------------------------------------------------------------------------

    // Identique à MySQL : tableaux associatifs
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // ----------------------------------------------------------------------------
    // CONFIGURATION 3 : Activer les contraintes de clés étrangères
    // ----------------------------------------------------------------------------

    /**
     * SPÉCIFIQUE À SQLite !
     *
     * Par défaut, SQLite IGNORE les contraintes de clés étrangères (FOREIGN KEY)
     * Il faut les activer manuellement avec PRAGMA foreign_keys = ON
     *
     * Exemple de contrainte de clé étrangère :
     * CREATE TABLE comments (
     *     id INTEGER PRIMARY KEY,
     *     user_id INTEGER,
     *     FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
     * )
     *
     * Avec PRAGMA foreign_keys = ON :
     * - Si on supprime un utilisateur, ses commentaires sont aussi supprimés (CASCADE)
     *
     * Sans cette ligne :
     * - Les contraintes sont ignorées (dangereux pour l'intégrité des données)
     */
    $pdo->exec('PRAGMA foreign_keys = ON');

    // exec() : exécute une commande SQL sans résultat
    // PRAGMA : commande spéciale de SQLite pour la configuration
    // foreign_keys = ON : active les contraintes de clés étrangères

} catch (PDOException $e) {

    // ----------------------------------------------------------------------------
    // GESTION DES ERREURS DE CONNEXION
    // ----------------------------------------------------------------------------

    // Erreurs possibles avec SQLite :
    // - "unable to open database file" : problème de permissions sur le dossier
    // - "database disk image is malformed" : fichier .db corrompu
    // - "database is locked" : fichier déjà ouvert par un autre processus

    die("Erreur de connexion à la base de données SQLite : " . $e->getMessage());
}

// ----------------------------------------------------------------------------
// NOTE IMPORTANTE SUR LES PERMISSIONS
// ----------------------------------------------------------------------------

/**
 * SQLite a besoin de permissions d'écriture !
 *
 * Le serveur web (Apache/Nginx) doit pouvoir :
 * 1. Créer le fichier database.db s'il n'existe pas
 * 2. Écrire dans ce fichier
 * 3. Créer un fichier de verrouillage database.db-journal
 *
 * Sur Linux/Mac :
 * chmod 755 /chemin/vers/dossier/projet
 * chmod 644 /chemin/vers/dossier/projet/database.db (si le fichier existe)
 *
 * Sur Windows :
 * - Généralement pas de problème avec XAMPP/WAMP
 *
 * En cas d'erreur "unable to open database file" :
 * - Vérifier que le dossier a les bonnes permissions
 * - Vérifier que PHP a le droit d'écrire dans ce dossier
 */

// ----------------------------------------------------------------------------
// FIN DU FICHIER
// ----------------------------------------------------------------------------

// À partir de maintenant, la variable $pdo est disponible
// Elle fonctionne EXACTEMENT comme avec MySQL !
//
// Les requêtes SQL sont les mêmes (ou presque) :
// - SELECT, INSERT, UPDATE, DELETE : identiques
// - AUTO_INCREMENT : devient AUTOINCREMENT en SQLite
// - TINYINT : devient INTEGER en SQLite
// - VARCHAR : devient TEXT en SQLite
//
// Mais PDO abstrait ces différences pour nous !

/*
===============================================================================
NOTES PÉDAGOGIQUES - SQLite vs MySQL
===============================================================================

1. DIFFÉRENCES DE CONNEXION :

   MySQL :
   $pdo = new PDO($dsn, $username, $password);

   SQLite :
   $pdo = new PDO($dsn); // Pas de username/password

2. TYPES DE DONNÉES :

   MySQL                    SQLite
   ----------------------------------------
   INT AUTO_INCREMENT   →   INTEGER PRIMARY KEY AUTOINCREMENT
   VARCHAR(255)         →   TEXT
   TINYINT(1)          →   INTEGER
   TIMESTAMP           →   TEXT ou INTEGER (unix timestamp)

   Note : SQLite a seulement 5 types : NULL, INTEGER, REAL, TEXT, BLOB

3. CRÉATION DE TABLE :

   MySQL :
   CREATE TABLE users (
       id INT AUTO_INCREMENT PRIMARY KEY,
       email VARCHAR(255) NOT NULL UNIQUE,
       password VARCHAR(255) NOT NULL,
       is_admin TINYINT(1) DEFAULT 0,
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

   SQLite :
   CREATE TABLE users (
       id INTEGER PRIMARY KEY AUTOINCREMENT,
       email TEXT NOT NULL UNIQUE,
       password TEXT NOT NULL,
       is_admin INTEGER DEFAULT 0,
       created_at TEXT DEFAULT CURRENT_TIMESTAMP
   );

4. FICHIERS CRÉÉS PAR SQLite :

   - database.db : fichier principal de la base de données
   - database.db-journal : fichier temporaire pour les transactions
   - database.db-wal : fichier pour le mode Write-Ahead Logging (optionnel)

5. AVANTAGES DE SQLite POUR L'APPRENTISSAGE :

   ✅ Installation : aucune (inclus avec PHP)
   ✅ Configuration : aucune
   ✅ Portabilité : copier le fichier .db suffit
   ✅ Sauvegarde : copier le fichier .db
   ✅ Réinitialisation : supprimer le fichier .db et relancer init_db.php

6. QUAND UTILISER SQLite vs MySQL ?

   SQLite : PARFAIT pour
   - Apprentissage et prototypage
   - Petites applications (< 100 000 lignes)
   - Applications mono-utilisateur
   - Applications embarquées
   - Tests automatisés

   MySQL : NÉCESSAIRE pour
   - Sites web avec plusieurs utilisateurs simultanés
   - Grosses bases de données (> 1 million de lignes)
   - Applications nécessitant des utilisateurs multiples
   - Production professionnelle

7. MIGRATION SQLite → MySQL :

   Pour passer de SQLite à MySQL plus tard :
   1. Exporter les données de SQLite
   2. Adapter les types de données dans le schéma
   3. Changer db.php pour utiliser MySQL
   4. Importer les données dans MySQL

   Ou utiliser des outils : https://github.com/dumblob/mysql2sqlite

===============================================================================
*/
?>
