<?php
/**
 * ============================================================================
 * CLASSE DATABASE - PATTERN SINGLETON
 * ============================================================================
 *
 * Cette classe gère la connexion à la base de données SQLite en utilisant
 * le pattern Singleton pour garantir qu'une seule instance existe.
 *
 * Pattern Singleton :
 * - Une seule instance de la classe peut exister
 * - L'instance est partagée dans toute l'application
 * - Empêche les connexions multiples inutiles à la base de données
 *
 * Avantages :
 * - Économie de ressources (une seule connexion)
 * - Accès centralisé à la base de données
 * - Facilite les tests et la maintenance
 */

namespace App\Config;

use PDO;
use PDOException;

class Database
{
    /**
     * Instance unique de la classe (pattern Singleton)
     * @var Database|null
     */
    private static ?Database $instance = null;

    /**
     * Objet PDO pour la connexion à la base de données
     * @var PDO|null
     */
    private ?PDO $connection = null;

    /**
     * Chemin vers le fichier de base de données SQLite
     * @var string
     */
    private string $dbPath;

    /**
     * CONSTRUCTEUR PRIVÉ
     *
     * Le constructeur est privé pour empêcher l'instanciation directe
     * avec "new Database()". On doit passer par getInstance().
     *
     * C'est une caractéristique clé du pattern Singleton.
     */
    private function __construct()
    {
        // Définir le chemin de la base de données
        // __DIR__ pointe vers src/Config/
        // On remonte de 2 niveaux pour atteindre la racine du projet
        $this->dbPath = dirname(__DIR__, 2) . '/database/database.db';

        // Établir la connexion
        $this->connect();
    }

    /**
     * MÉTHODE getInstance() - Point d'accès unique au Singleton
     *
     * Cette méthode crée l'instance si elle n'existe pas encore,
     * ou retourne l'instance existante.
     *
     * @return Database Instance unique de Database
     */
    public static function getInstance(): Database
    {
        // Si l'instance n'existe pas encore
        if (self::$instance === null) {
            // Créer une nouvelle instance (appelle le constructeur privé)
            self::$instance = new self();
        }

        // Retourner l'instance (nouvelle ou existante)
        return self::$instance;
    }

    /**
     * Établit la connexion à la base de données SQLite
     *
     * @throws PDOException Si la connexion échoue
     */
    private function connect(): void
    {
        try {
            // Construire le DSN (Data Source Name) pour SQLite
            $dsn = "sqlite:" . $this->dbPath;

            // Créer la connexion PDO
            $this->connection = new PDO($dsn);

            // Configuration 1 : Mode de gestion des erreurs en exceptions
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Configuration 2 : Mode de récupération en tableaux associatifs
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            // Configuration 3 : Activer les contraintes de clés étrangères (spécifique SQLite)
            $this->connection->exec('PRAGMA foreign_keys = ON');

        } catch (PDOException $e) {
            // En cas d'erreur, afficher un message et arrêter l'exécution
            die("Erreur de connexion à la base de données SQLite : " . $e->getMessage());
        }
    }

    /**
     * Retourne l'objet PDO pour exécuter des requêtes
     *
     * @return PDO Connexion PDO à la base de données
     */
    public function getConnection(): PDO
    {
        return $this->connection;
    }

    /**
     * EMPÊCHER LE CLONAGE
     *
     * Cette méthode empêche de cloner l'instance avec "clone $database".
     * C'est important pour le pattern Singleton.
     */
    private function __clone()
    {
        // Méthode vide et privée pour bloquer le clonage
    }

    /**
     * EMPÊCHER LA DÉSÉRIALISATION
     *
     * Cette méthode empêche de recréer l'objet via unserialize().
     * Protection supplémentaire pour le Singleton.
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }
}

/*
===============================================================================
NOTES PÉDAGOGIQUES - PATTERN SINGLETON
===============================================================================

1. QU'EST-CE QU'UN PATTERN SINGLETON ?

   Le Singleton est un design pattern qui garantit qu'une classe n'a qu'une
   seule instance et fournit un point d'accès global à cette instance.

2. POURQUOI UTILISER UN SINGLETON POUR LA BASE DE DONNÉES ?

   ✅ Une seule connexion PDO pour toute l'application
   ✅ Évite les connexions multiples (économie de ressources)
   ✅ Accès centralisé et cohérent
   ✅ Facilite les tests et le débogage

3. COMMENT UTILISER CETTE CLASSE ?

   // MAUVAISE façon (ne fonctionne pas - constructeur privé) :
   // $db = new Database(); // ❌ ERREUR !

   // BONNE façon :
   $database = Database::getInstance();
   $pdo = $database->getConnection();

   // Exécuter une requête :
   $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
   $stmt->execute(['email' => 'test@example.com']);
   $user = $stmt->fetch();

4. LES 3 CARACTÉRISTIQUES D'UN SINGLETON :

   a) Constructeur PRIVÉ
      → Empêche l'instanciation directe avec "new"

   b) Variable statique PRIVÉE pour stocker l'instance
      → self::$instance

   c) Méthode publique STATIQUE pour accéder à l'instance
      → getInstance()

5. PROTECTIONS SUPPLÉMENTAIRES :

   - __clone() privé : empêche le clonage
   - __wakeup() : empêche la désérialisation

6. DIFFÉRENCES AVEC L'ANCIEN CODE PROCÉDURAL :

   Ancien (db.php) :
   require_once 'db.php';
   // $pdo est maintenant disponible globalement
   $stmt = $pdo->prepare("...");

   Nouveau (POO) :
   $database = Database::getInstance();
   $pdo = $database->getConnection();
   $stmt = $pdo->prepare("...");

7. EXEMPLE D'UTILISATION DANS L'APPLICATION :

   // Dans login.php
   use App\Config\Database;

   $database = Database::getInstance();
   $pdo = $database->getConnection();

   $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
   $stmt->execute(['email' => $email]);

8. NAMESPACE :

   namespace App\Config;

   - Organise le code en espaces de noms
   - Évite les conflits de noms de classes
   - Suit la structure des dossiers : App\Config → src/Config/

===============================================================================
*/
