<?php
/**
 * ============================================================================
 * MODÈLE USER - REPRÉSENTATION D'UN UTILISATEUR
 * ============================================================================
 *
 * Cette classe représente un utilisateur et gère toutes les opérations
 * liées aux utilisateurs dans la base de données (CRUD).
 *
 * CRUD signifie :
 * - Create (Créer)
 * - Read (Lire)
 * - Update (Mettre à jour)
 * - Delete (Supprimer)
 *
 * Pattern Active Record :
 * Cette classe combine les données (propriétés) et le comportement (méthodes)
 * liés à un utilisateur.
 */

namespace App\Models;

use App\Config\Database;
use PDO;
use PDOException;

class User
{
    /**
     * Propriétés de l'utilisateur (correspondent aux colonnes de la table)
     */
    private ?int $id = null;
    private string $email;
    private string $password;
    private int $isAdmin = 0;
    private ?string $createdAt = null;

    /**
     * Connexion PDO (partagée par toutes les instances)
     * @var PDO
     */
    private PDO $pdo;

    /**
     * Constructeur
     *
     * @param string|null $email Email de l'utilisateur
     * @param string|null $password Mot de passe (en clair ou hashé)
     */
    public function __construct(?string $email = null, ?string $password = null)
    {
        // Obtenir la connexion à la base de données
        $database = Database::getInstance();
        $this->pdo = $database->getConnection();

        // Initialiser les propriétés si fournies
        if ($email) $this->email = $email;
        if ($password) $this->password = $password;
    }

    // ========================================================================
    // GETTERS ET SETTERS
    // ========================================================================

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getIsAdmin(): int
    {
        return $this->isAdmin;
    }

    public function setIsAdmin(int $isAdmin): void
    {
        $this->isAdmin = $isAdmin;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function isAdmin(): bool
    {
        return $this->isAdmin === 1;
    }

    // ========================================================================
    // MÉTHODES CRUD
    // ========================================================================

    /**
     * Crée un nouvel utilisateur dans la base de données
     *
     * @return bool True si succès, False sinon
     */
    public function create(): bool
    {
        try {
            $sql = "INSERT INTO users (email, password, is_admin) VALUES (:email, :password, :is_admin)";
            $stmt = $this->pdo->prepare($sql);

            $result = $stmt->execute([
                'email' => $this->email,
                'password' => $this->password,
                'is_admin' => $this->isAdmin
            ]);

            // Récupérer l'ID du nouvel utilisateur
            if ($result) {
                $this->id = (int) $this->pdo->lastInsertId();
            }

            return $result;

        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Met à jour l'utilisateur dans la base de données
     *
     * @return bool True si succès, False sinon
     */
    public function update(): bool
    {
        try {
            $sql = "UPDATE users SET email = :email, password = :password, is_admin = :is_admin WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);

            return $stmt->execute([
                'email' => $this->email,
                'password' => $this->password,
                'is_admin' => $this->isAdmin,
                'id' => $this->id
            ]);

        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Met à jour uniquement l'email de l'utilisateur
     *
     * @return bool True si succès, False sinon
     */
    public function updateEmail(): bool
    {
        try {
            $sql = "UPDATE users SET email = :email WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);

            return $stmt->execute([
                'email' => $this->email,
                'id' => $this->id
            ]);

        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Met à jour uniquement le mot de passe de l'utilisateur
     *
     * @return bool True si succès, False sinon
     */
    public function updatePassword(): bool
    {
        try {
            $sql = "UPDATE users SET password = :password WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);

            return $stmt->execute([
                'password' => $this->password,
                'id' => $this->id
            ]);

        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Bascule le statut admin de l'utilisateur (0 → 1 ou 1 → 0)
     *
     * @return bool True si succès, False sinon
     */
    public function toggleAdmin(): bool
    {
        try {
            // Inverser le statut admin
            $this->isAdmin = $this->isAdmin === 1 ? 0 : 1;

            $sql = "UPDATE users SET is_admin = :is_admin WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);

            return $stmt->execute([
                'is_admin' => $this->isAdmin,
                'id' => $this->id
            ]);

        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Supprime l'utilisateur de la base de données
     *
     * @return bool True si succès, False sinon
     */
    public function delete(): bool
    {
        try {
            $sql = "DELETE FROM users WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);

            return $stmt->execute(['id' => $this->id]);

        } catch (PDOException $e) {
            return false;
        }
    }

    // ========================================================================
    // MÉTHODES STATIQUES DE RECHERCHE (FIND)
    // ========================================================================

    /**
     * Trouve un utilisateur par son ID
     *
     * @param int $id ID de l'utilisateur
     * @return User|null Utilisateur trouvé ou null
     */
    public static function findById(int $id): ?User
    {
        $database = Database::getInstance();
        $pdo = $database->getConnection();

        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $data = $stmt->fetch();

            if (!$data) {
                return null;
            }

            return self::hydrate($data);

        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Trouve un utilisateur par son email
     *
     * @param string $email Email de l'utilisateur
     * @return User|null Utilisateur trouvé ou null
     */
    public static function findByEmail(string $email): ?User
    {
        $database = Database::getInstance();
        $pdo = $database->getConnection();

        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $data = $stmt->fetch();

            if (!$data) {
                return null;
            }

            return self::hydrate($data);

        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Récupère tous les utilisateurs
     *
     * @param string $orderBy Colonne pour le tri (par défaut : id DESC)
     * @return array Tableau d'objets User
     */
    public static function findAll(string $orderBy = 'id DESC'): array
    {
        $database = Database::getInstance();
        $pdo = $database->getConnection();

        try {
            // Sécuriser le ORDER BY (on accepte uniquement certaines colonnes)
            $allowedOrderBy = ['id DESC', 'id ASC', 'email ASC', 'email DESC', 'created_at DESC'];
            if (!in_array($orderBy, $allowedOrderBy)) {
                $orderBy = 'id DESC';
            }

            $stmt = $pdo->query("SELECT * FROM users ORDER BY $orderBy");
            $usersData = $stmt->fetchAll();

            $users = [];
            foreach ($usersData as $data) {
                $users[] = self::hydrate($data);
            }

            return $users;

        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Compte le nombre total d'utilisateurs
     *
     * @return int Nombre d'utilisateurs
     */
    public static function count(): int
    {
        $database = Database::getInstance();
        $pdo = $database->getConnection();

        try {
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
            $result = $stmt->fetch();
            return (int) $result['total'];

        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Vérifie si un email existe déjà (pour un autre utilisateur)
     *
     * @param string $email Email à vérifier
     * @param int|null $excludeId ID à exclure de la recherche
     * @return bool True si l'email existe, False sinon
     */
    public static function emailExists(string $email, ?int $excludeId = null): bool
    {
        $database = Database::getInstance();
        $pdo = $database->getConnection();

        try {
            if ($excludeId !== null) {
                // Chercher l'email en excluant l'utilisateur actuel
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email AND id != :id");
                $stmt->execute(['email' => $email, 'id' => $excludeId]);
            } else {
                // Chercher l'email
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
                $stmt->execute(['email' => $email]);
            }

            return $stmt->fetch() !== false;

        } catch (PDOException $e) {
            return false;
        }
    }

    // ========================================================================
    // MÉTHODES UTILITAIRES
    // ========================================================================

    /**
     * Hydrate un objet User à partir d'un tableau de données
     *
     * "Hydrater" signifie remplir un objet avec des données
     *
     * @param array $data Données de la base de données
     * @return User Objet User hydraté
     */
    private static function hydrate(array $data): User
    {
        $user = new User();
        $user->id = (int) $data['id'];
        $user->email = $data['email'];
        $user->password = $data['password'];
        $user->isAdmin = (int) $data['is_admin'];
        $user->createdAt = $data['created_at'];

        return $user;
    }

    /**
     * Convertit l'utilisateur en tableau associatif
     *
     * @param bool $includePassword Inclure le mot de passe (par défaut : false)
     * @return array Tableau représentant l'utilisateur
     */
    public function toArray(bool $includePassword = false): array
    {
        $data = [
            'id' => $this->id,
            'email' => $this->email,
            'is_admin' => $this->isAdmin,
            'created_at' => $this->createdAt
        ];

        if ($includePassword) {
            $data['password'] = $this->password;
        }

        return $data;
    }
}

/*
===============================================================================
NOTES PÉDAGOGIQUES - MODÈLE USER
===============================================================================

1. QU'EST-CE QU'UN MODÈLE ?

   Un modèle est une classe qui représente une entité de la base de données.
   Ici, User représente la table "users".

   - Une instance de User = une ligne dans la table users
   - Les propriétés de User = les colonnes de la table
   - Les méthodes de User = les opérations sur les utilisateurs

2. PATTERN ACTIVE RECORD

   User combine les données ET le comportement :
   - Données : $id, $email, $password, $isAdmin, $createdAt
   - Comportement : create(), update(), delete(), find*()

3. UTILISATION - CRÉER UN UTILISATEUR

   use App\Models\User;

   $user = new User('test@example.com', password_hash('Password123!', PASSWORD_BCRYPT));
   $user->setIsAdmin(0);

   if ($user->create()) {
       echo "User created with ID: " . $user->getId();
   }

4. UTILISATION - TROUVER UN UTILISATEUR

   // Par ID
   $user = User::findById(5);
   if ($user) {
       echo $user->getEmail();
   }

   // Par email
   $user = User::findByEmail('test@example.com');
   if ($user) {
       echo "User found: " . $user->getId();
   }

   // Tous les utilisateurs
   $users = User::findAll();
   foreach ($users as $user) {
       echo $user->getEmail() . "<br>";
   }

5. UTILISATION - METTRE À JOUR UN UTILISATEUR

   $user = User::findById(5);
   if ($user) {
       // Changer l'email
       $user->setEmail('newemail@example.com');
       $user->updateEmail();

       // Changer le mot de passe
       $user->setPassword(password_hash('NewPassword123!', PASSWORD_BCRYPT));
       $user->updatePassword();

       // Basculer le statut admin
       $user->toggleAdmin();
   }

6. UTILISATION - SUPPRIMER UN UTILISATEUR

   $user = User::findById(5);
   if ($user) {
       if ($user->delete()) {
           echo "User deleted successfully";
       }
   }

   // Ou directement
   if (User::deleteById(5)) {
       echo "User deleted";
   }

7. VÉRIFIER SI UN EMAIL EXISTE

   // Vérifier si l'email existe
   if (User::emailExists('test@example.com')) {
       echo "Email already taken";
   }

   // Vérifier en excluant l'utilisateur actuel (utile pour profile.php)
   if (User::emailExists('test@example.com', $currentUserId)) {
       echo "Email already used by another user";
   }

8. HYDRATATION

   "Hydrater" signifie remplir un objet avec des données.

   Avant (tableau associatif) :
   $userData = ['id' => 5, 'email' => 'test@example.com', ...];
   echo $userData['email']; // Accès par tableau

   Après (objet User) :
   $user = User::hydrate($userData);
   echo $user->getEmail(); // Accès par méthode (plus propre)

9. AVANTAGES DU MODÈLE USER

   ✅ Encapsulation des données
   ✅ Code réutilisable
   ✅ Validation centralisée possible
   ✅ Facilite les tests
   ✅ Code plus lisible et maintenable
   ✅ Type-safe (avec les types PHP)

10. DIFFÉRENCES AVEC L'ANCIEN CODE

    Ancien (procédural) :
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    Nouveau (POO) :
    $user = User::findByEmail($email);

    Plus court, plus lisible, plus maintenable !

===============================================================================
*/
