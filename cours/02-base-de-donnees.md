# 📚 COURS 02 : BASE DE DONNÉES - CONNEXION ET INITIALISATION

> **Pour débutants complets - Système d'authentification PHP**
> **Objectif :** Maîtriser complètement `db.php` et `init_db.php` ligne par ligne
> **Durée estimée :** 3 heures
> **Fichiers couverts :** `db_sqlite.php`, `init_db.php`

---

## 🎯 OBJECTIFS DE CE CHAPITRE

À la fin de ce chapitre, vous serez capable de :
- ✅ Expliquer chaque ligne de `db_sqlite.php`
- ✅ Comprendre ce qu'est PDO et pourquoi on l'utilise
- ✅ Créer une base de données SQLite avec `init_db.php`
- ✅ Comprendre la structure de la table `users`
- ✅ Initialiser un compte administrateur
- ✅ Réinitialiser la base de données

---

## 📖 TABLE DES MATIÈRES

1. [C'est quoi une base de données ?](#1-cest-quoi-une-base-de-données)
2. [SQLite vs MySQL : comprendre la différence](#2-sqlite-vs-mysql)
3. [Fichier db_sqlite.php ligne par ligne](#3-fichier-db_sqlitephp-ligne-par-ligne)
4. [Fichier init_db.php ligne par ligne](#4-fichier-init_dbphp-ligne-par-ligne)
5. [Exercices pratiques](#5-exercices-pratiques)

---

## 1. C'EST QUOI UNE BASE DE DONNÉES ?

### 📊 Analogie simple

Imaginez un **classeur Excel géant** qui :
- ✅ Stocke toutes vos données de manière organisée
- ✅ Permet de chercher rapidement une information
- ✅ Garantit que les données ne se perdent pas
- ✅ Empêche deux personnes d'avoir le même email

```
BASE DE DONNÉES = Classeur Excel sur vitamine

EXCEL NORMAL :
┌─────┬──────────────────┬─────────┬────────┐
│ ID  │ Email            │ Admin   │ Date   │
├─────┼──────────────────┼─────────┼────────┤
│ 1   │ admin@ex.com     │ Oui     │ 01/01  │
│ 2   │ user@ex.com      │ Non     │ 02/01  │
└─────┴──────────────────┴─────────┴────────┘

BASE DE DONNÉES :
┌─────┬──────────────────┬──────────┬────────────────┐
│ id  │ email            │ is_admin │ created_at     │
├─────┼──────────────────┼──────────┼────────────────┤
│ 1   │ admin@ex.com     │ 1        │ 2024-01-01...  │
│ 2   │ user@ex.com      │ 0        │ 2024-01-02...  │
└─────┴──────────────────┴──────────┴────────────────┘

DIFFÉRENCES :
- Plus rapide pour chercher
- Impossible d'avoir deux fois le même email (UNIQUE)
- Les données survivent même si on éteint le serveur
- On peut faire des requêtes complexes (SELECT, INSERT, UPDATE, DELETE)
```

---

## 2. SQLITE VS MYSQL

### 🔍 Tableau comparatif complet

| Critère | SQLite | MySQL |
|---------|--------|-------|
| **Installation** | ❌ Aucune (inclus avec PHP) | ✅ Installer MySQL Server + phpMyAdmin |
| **Configuration** | ❌ Aucune | ✅ Créer utilisateur, mot de passe, base |
| **Stockage** | 1 fichier `database.db` | Serveur séparé avec tables |
| **Démarrage** | Instantané | Démarrer le serveur MySQL |
| **Portabilité** | ✅ Copier le fichier .db | ❌ Export/import SQL nécessaire |
| **Réinitialisation** | ✅ Supprimer le fichier .db | ❌ DROP DATABASE + recréer |
| **Visualisation** | DB Browser ou VS Code extension | phpMyAdmin ou client MySQL |
| **Performance** | Bon jusqu'à 100 000 lignes | Excellent même avec millions de lignes |
| **Utilisateurs simultanés** | 1-10 | Milliers |
| **Idéal pour** | Apprentissage, prototypage | Production professionnelle |

### 🎯 Notre choix : SQLite pour apprendre

**Pourquoi SQLite en premier ?**

```
AVEC SQLite (notre choix) :
┌────────────────────────────────────────────┐
│ TEMPS DE MISE EN PLACE : 2 minutes         │
├────────────────────────────────────────────┤
│ 1. Renommer db_sqlite.php en db.php       │
│ 2. Lancer le serveur PHP                  │
│ 3. Ouvrir init_db.php dans le navigateur  │
│ 4. ✓ C'EST PRÊT !                         │
└────────────────────────────────────────────┘

AVEC MySQL (plus tard) :
┌────────────────────────────────────────────┐
│ TEMPS DE MISE EN PLACE : 30-60 minutes    │
├────────────────────────────────────────────┤
│ 1. Installer MySQL Server (15 min)        │
│ 2. Installer phpMyAdmin (10 min)          │
│ 3. Créer l'utilisateur root (5 min)       │
│ 4. Créer la base de données (5 min)       │
│ 5. Créer les tables manuellement (10 min) │
│ 6. Insérer l'admin (5 min)                │
│ 7. Configurer db.php (5 min)              │
│ 8. ✓ Enfin prêt...                        │
└────────────────────────────────────────────┘
```

**Bonne nouvelle :** Le code PHP reste **identique** ! Seul `db.php` change.

---

## 3. FICHIER DB_SQLITE.PHP LIGNE PAR LIGNE

### 📄 Vue d'ensemble

Le fichier `db_sqlite.php` (à copier vers `db.php`) fait **une seule chose** :
- Créer une connexion PDO vers la base de données SQLite

**Fichier complet : db_sqlite.php:1-246**

Analysons chaque section :

---

### 🔧 SECTION 1 : Définir le chemin de la base (lignes 23-35)

```php
$db_path = __DIR__ . '/database.db';
```

**Décortiquons cette ligne :**

| Partie | Signification | Exemple |
|--------|---------------|---------|
| `$db_path` | Variable qui contiendra le chemin | (variable créée) |
| `=` | Affectation | - |
| `__DIR__` | Constante PHP = dossier actuel | `/var/www/html/user-auth-php` |
| `.` | Opérateur de concaténation (coller des textes) | - |
| `'/database.db'` | Nom du fichier de base | `/database.db` |
| Résultat final | | `/var/www/html/user-auth-php/database.db` |

**Pourquoi utiliser `__DIR__` ?**

```php
// ❌ MAUVAIS : Chemin relatif
$db_path = 'database.db';
// Problème : PHP cherche dans le dossier ACTUEL
// Si vous êtes dans un sous-dossier, ça ne marche pas !

// ✅ BON : Chemin absolu avec __DIR__
$db_path = __DIR__ . '/database.db';
// PHP cherche TOUJOURS au bon endroit
// Peu importe d'où vous incluez le fichier
```

**Exemple pratique :**

```
Structure :
/user-auth-php/
├── db.php               ← __DIR__ = /user-auth-php
├── database.db          ← Créé ici
├── index.php
└── admin/
    └── panel.php

Dans admin/panel.php :
require_once '../db.php';

Sans __DIR__ :
→ Cherche dans /user-auth-php/admin/database.db  ❌

Avec __DIR__ :
→ Cherche dans /user-auth-php/database.db  ✅
```

---

### 🔧 SECTION 2 : Construire le DSN (lignes 38-52)

```php
$dsn = "sqlite:$db_path";
```

**C'est quoi un DSN ?**

DSN = **Data Source Name** (nom de la source de données)

C'est une **chaîne de connexion** qui dit à PDO :
- Quel type de base de données (`sqlite`, `mysql`, `pgsql`...)
- Où elle se trouve (fichier, serveur, port...)

**Exemples de DSN :**

```php
// SQLite (notre cas)
$dsn = "sqlite:/path/to/database.db";

// MySQL
$dsn = "mysql:host=localhost;dbname=mydb;charset=utf8mb4";

// PostgreSQL
$dsn = "pgsql:host=localhost;port=5432;dbname=mydb";
```

**Différences :**

```
SQLite DSN :
"sqlite:/chemin/vers/database.db"
  ↑       ↑
  Type    Chemin du fichier

  PAS de :
  - host (pas de serveur)
  - port (fichier local)
  - username/password (pas d'authentification)
  - charset (UTF-8 par défaut)

MySQL DSN :
"mysql:host=localhost;dbname=mydb;charset=utf8mb4"
  ↑     ↑             ↑           ↑
  Type  Serveur       Nom DB      Encodage

  AVEC :
  - host (localhost ou IP)
  - dbname (nom de la base)
  - charset (encodage des caractères)
  - Puis username et password en paramètres de PDO
```

---

### 🔧 SECTION 3 : Créer la connexion PDO (lignes 54-77)

```php
try {
    $pdo = new PDO($dsn);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec('PRAGMA foreign_keys = ON');

} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
```

#### Ligne 1 : `$pdo = new PDO($dsn);`

**C'est quoi PDO ?**

PDO = **PHP Data Objects** (Objets de données PHP)

```
AVANT PDO (ancien PHP) :
┌────────────────────────────────────────────┐
│  Chaque base de données avait sa propre    │
│  extension :                               │
│  - MySQL : mysql_connect()                 │
│  - PostgreSQL : pg_connect()               │
│  - SQLite : sqlite_open()                  │
│                                            │
│  Problème : Si vous changez de base,       │
│  vous devez réécrire TOUT le code !        │
└────────────────────────────────────────────┘

AVEC PDO (moderne) :
┌────────────────────────────────────────────┐
│  UNE seule interface pour toutes les bases:│
│  - new PDO('mysql:...')                    │
│  - new PDO('pgsql:...')                    │
│  - new PDO('sqlite:...')                   │
│                                            │
│  Avantage : Le reste du code PHP reste     │
│  IDENTIQUE peu importe la base !           │
└────────────────────────────────────────────┘
```

**Exemple concret :**

```php
// SQLite (db_sqlite.php:62)
$pdo = new PDO("sqlite:database.db");

// MySQL (version alternative)
$pdo = new PDO(
    "mysql:host=localhost;dbname=mydb",
    "username",  // Paramètre supplémentaire
    "password"   // Paramètre supplémentaire
);

// Le reste du code est IDENTIQUE :
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
$stmt->execute(['email' => $email]);
$user = $stmt->fetch();
```

#### Ligne 2 : Mode d'erreur EXCEPTION

```php
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
```

**Pourquoi c'est crucial ?**

```php
// SANS cette ligne (mode silencieux) :
$stmt = $pdo->prepare("SELECT * FROM table_inexistante");
// → Aucune erreur affichée
// → Impossible de savoir ce qui ne va pas
// → Débogage cauchemardesque

// AVEC cette ligne (mode exception) :
$stmt = $pdo->prepare("SELECT * FROM table_inexistante");
// → Fatal error: SQLSTATE[42S02]: Base table or view not found
// → On voit exactement le problème
// → Facile à corriger
```

**Les 3 modes d'erreur PDO :**

| Mode | Comportement | Utilisation |
|------|--------------|-------------|
| `ERRMODE_SILENT` | Aucune erreur affichée | ❌ Jamais (impossible à déboguer) |
| `ERRMODE_WARNING` | Affiche un warning PHP | ⚠️ Débogage seulement |
| `ERRMODE_EXCEPTION` | Lance une exception | ✅ TOUJOURS (notre choix) |

#### Ligne 3 : Mode de récupération ASSOC

```php
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
```

**Les différents modes de fetch :**

```php
// Données dans la base :
// id=1, email='test@example.com', is_admin=0

// FETCH_ASSOC (notre choix) :
$user = [
    'id' => 1,
    'email' => 'test@example.com',
    'is_admin' => 0
];
// Accès : $user['email']  ← Clair et lisible ✅

// FETCH_NUM :
$user = [
    0 => 1,
    1 => 'test@example.com',
    2 => 0
];
// Accès : $user[1]  ← C'est quoi déjà 1 ? ❌

// FETCH_BOTH (par défaut sans cette ligne) :
$user = [
    'id' => 1,
    0 => 1,           // Doublon !
    'email' => 'test@example.com',
    1 => 'test@example.com',  // Doublon !
    'is_admin' => 0,
    2 => 0            // Doublon !
];
// Accès : $user['email'] OU $user[1]  ← Confus ❌
```

**Recommandation :** Utilisez TOUJOURS `FETCH_ASSOC`

#### Ligne 4 : Activer les clés étrangères (SQLite uniquement)

```php
$pdo->exec('PRAGMA foreign_keys = ON');
```

**C'est quoi PRAGMA ?**

`PRAGMA` = commande spéciale de SQLite pour la configuration

**Pourquoi activer les foreign keys ?**

```sql
-- Exemple avec une table comments qui référence users

CREATE TABLE comments (
    id INTEGER PRIMARY KEY,
    user_id INTEGER,
    message TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- SANS foreign_keys = ON :
DELETE FROM users WHERE id = 5;
→ L'utilisateur est supprimé
→ Ses commentaires restent (orphelins)
→ user_id = 5 mais l'utilisateur 5 n'existe plus
→ Incohérence dans la base ! ❌

-- AVEC foreign_keys = ON :
DELETE FROM users WHERE id = 5;
→ L'utilisateur est supprimé
→ Ses commentaires sont AUSSI supprimés (CASCADE)
→ Pas d'orphelins
→ Base cohérente ✅
```

**Note :** Notre projet n'utilise pas de clés étrangères (qu'une seule table), mais c'est une bonne pratique de les activer.

---

### 🔧 SECTION 4 : Gestion des erreurs (lignes 107-119)

```php
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
```

**C'est quoi un try/catch ?**

```php
try {
    // Essayer de faire quelque chose de risqué
    $pdo = new PDO("sqlite:database.db");

} catch (PDOException $e) {
    // Si une erreur se produit, on arrive ici
    // $e contient les détails de l'erreur
    echo "Erreur : " . $e->getMessage();
}
```

**Analogie :**

```
TRY (essayer) :
    Essaie d'ouvrir le coffre-fort
    Si la clé marche → Super, continue !

CATCH (attraper l'erreur) :
    Si la clé ne marche pas → Affiche "Clé invalide"
```

**Erreurs possibles avec SQLite :**

| Erreur | Signification | Solution |
|--------|---------------|----------|
| `unable to open database file` | Pas de permission d'écriture | `chmod 755` sur le dossier |
| `database disk image is malformed` | Fichier .db corrompu | Supprimer et recréer |
| `database is locked` | Fichier déjà ouvert | Fermer les autres connexions |

---

### 📝 RÉSUMÉ du fichier db_sqlite.php

**Ce fichier fait 4 choses :**

```php
// 1. Définir le chemin
$db_path = __DIR__ . '/database.db';

// 2. Créer le DSN
$dsn = "sqlite:$db_path";

// 3. Se connecter avec PDO
try {
    $pdo = new PDO($dsn);

    // 4. Configurer PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec('PRAGMA foreign_keys = ON');

} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}

// À ce stade, la variable $pdo est prête
// Tous les autres fichiers peuvent maintenant l'utiliser
```

---

## 4. FICHIER INIT_DB.PHP LIGNE PAR LIGNE

### 📄 Vue d'ensemble

Le fichier `init_db.php` fait **4 choses** :
1. Supprimer l'ancienne base (si elle existe)
2. Créer la table `users`
3. Créer un admin par défaut
4. Afficher les identifiants de connexion

**Fichier complet : init_db.php:1-300**

---

### 🔧 SECTION 1 : Supprimer l'ancienne base (lignes 40-51)

```php
$db_path = __DIR__ . '/database.db';

if (file_exists($db_path)) {
    unlink($db_path);
    echo "✓ Ancienne base de données supprimée<br>\n";
}
```

**Pourquoi supprimer l'ancienne base ?**

```
SANS suppression :
1. Lancer init_db.php
2. La table 'users' existe déjà
3. ERREUR : "table users already exists"
4. ❌ Impossible de réinitialiser

AVEC suppression :
1. Lancer init_db.php
2. Supprime database.db
3. Recrée tout de zéro
4. ✅ Réinitialisation propre
```

**Fonctions utilisées :**

```php
// file_exists($path) : vérifie si un fichier existe
file_exists('/path/to/file.db')  // → TRUE ou FALSE

// unlink($path) : supprime un fichier
unlink('/path/to/file.db')  // → Fichier supprimé
```

---

### 🔧 SECTION 2 : Créer la table users (lignes 92-139)

```php
$sql_create_table = "
    CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        email TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,
        is_admin INTEGER DEFAULT 0,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    )
";

$pdo->exec($sql_create_table);
```

**Décortiquons CHAQUE colonne :**

#### Colonne 1 : `id INTEGER PRIMARY KEY AUTOINCREMENT`

```
id = Identifiant unique de chaque utilisateur

INTEGER
→ Type de données : nombre entier (1, 2, 3...)
→ Pas de décimales (pas 1.5 ou 2.3)

PRIMARY KEY
→ Clé primaire = identifiant UNIQUE
→ Deux utilisateurs ne peuvent PAS avoir le même id
→ Obligatoire pour chaque table

AUTOINCREMENT
→ S'incrémente automatiquement
→ 1er utilisateur : id = 1
→ 2ème utilisateur : id = 2
→ 3ème utilisateur : id = 3
→ Vous n'avez RIEN à faire !
```

**Exemple :**

```sql
-- Insertion sans spécifier l'id
INSERT INTO users (email, password) VALUES ('user1@ex.com', '...');
-- → id = 1 automatiquement

INSERT INTO users (email, password) VALUES ('user2@ex.com', '...');
-- → id = 2 automatiquement

INSERT INTO users (email, password) VALUES ('user3@ex.com', '...');
-- → id = 3 automatiquement
```

#### Colonne 2 : `email TEXT NOT NULL UNIQUE`

```
email = Adresse email de l'utilisateur

TEXT
→ Type texte (chaîne de caractères)
→ Équivalent de VARCHAR en MySQL
→ Peut contenir n'importe quelle longueur

NOT NULL
→ Obligatoire
→ Impossible d'insérer un utilisateur sans email
→ INSERT INTO users (password) VALUES ('...'); → ERREUR

UNIQUE
→ Unique dans toute la table
→ Impossible d'avoir deux utilisateurs avec le même email
→ Évite les doublons
```

**Exemple d'UNIQUE :**

```sql
-- 1er utilisateur
INSERT INTO users (email, password) VALUES ('test@example.com', '...');
→ ✅ OK, c'est le premier

-- 2ème utilisateur avec le MÊME email
INSERT INTO users (email, password) VALUES ('test@example.com', '...');
→ ❌ ERREUR : UNIQUE constraint failed: users.email
```

**Pourquoi UNIQUE est crucial ?**

```
SANS UNIQUE :
┌────┬─────────────────┬──────────┐
│ id │ email           │ password │
├────┼─────────────────┼──────────┤
│ 1  │ user@example.com│ hash1    │
│ 2  │ user@example.com│ hash2    │  ← Même email !
└────┴─────────────────┴──────────┘

Problème : Lequel est le bon ?
→ L'utilisateur ne peut pas se connecter
→ Confusion totale

AVEC UNIQUE :
→ Impossible d'insérer le 2ème
→ Chaque email est unique
→ Pas de confusion
```

#### Colonne 3 : `password TEXT NOT NULL`

```
password = Mot de passe hashé (jamais en clair !)

TEXT
→ Type texte
→ Suffisant pour un hash BCRYPT (60 caractères)

NOT NULL
→ Obligatoire
→ Impossible de créer un utilisateur sans mot de passe
```

**⚠️ IMPORTANT :** On stocke le HASH, pas le mot de passe !

```php
// ❌ JAMAIS ça :
INSERT INTO users (password) VALUES ('Test123!');
// → Mot de passe visible en clair dans la base

// ✅ TOUJOURS ça :
$hash = password_hash('Test123!', PASSWORD_BCRYPT);
INSERT INTO users (password) VALUES ('$2y$10$...');
// → Hash irréversible
```

#### Colonne 4 : `is_admin INTEGER DEFAULT 0`

```
is_admin = Flag (drapeau) pour savoir si c'est un admin

INTEGER
→ Type entier
→ Valeurs : 0 ou 1

DEFAULT 0
→ Valeur par défaut = 0 (pas admin)
→ Si on ne spécifie pas is_admin lors de l'insertion, il vaut 0
```

**Exemple :**

```sql
-- Sans spécifier is_admin
INSERT INTO users (email, password) VALUES ('user@ex.com', '...');
→ is_admin = 0 automatiquement (utilisateur normal)

-- Avec is_admin = 1
INSERT INTO users (email, password, is_admin) VALUES ('admin@ex.com', '...', 1);
→ is_admin = 1 (administrateur)
```

**Signification :**

| Valeur | Signification | Permissions |
|--------|---------------|-------------|
| `0` | Utilisateur normal | index, profile, logout |
| `1` | Administrateur | index, profile, logout, **admin**, **edit_user**, **delete_user** |

#### Colonne 5 : `created_at TEXT DEFAULT CURRENT_TIMESTAMP`

```
created_at = Date et heure de création du compte

TEXT
→ SQLite stocke les dates en texte
→ Format : "2024-01-15 10:30:45"

DEFAULT CURRENT_TIMESTAMP
→ Rempli automatiquement lors de l'insertion
→ Pas besoin de le spécifier
```

**Exemple :**

```sql
-- On insère un utilisateur SANS spécifier created_at
INSERT INTO users (email, password) VALUES ('user@ex.com', '...');

-- SQLite remplit automatiquement :
SELECT * FROM users WHERE email = 'user@ex.com';
→ created_at = "2024-01-15 10:30:45"  (date actuelle)
```

---

### 🔧 SECTION 3 : Créer l'admin par défaut (lignes 158-186)

```php
$admin_email = 'admin@example.com';
$admin_password = 'Admin123!';
$admin_password_hash = password_hash($admin_password, PASSWORD_BCRYPT);

$sql_insert_admin = "
    INSERT INTO users (email, password, is_admin)
    VALUES (:email, :password, :is_admin)
";

$stmt = $pdo->prepare($sql_insert_admin);
$stmt->execute([
    'email' => $admin_email,
    'password' => $admin_password_hash,
    'is_admin' => 1
]);
```

**Étape par étape :**

**1. Définir les identifiants**

```php
$admin_email = 'admin@example.com';
$admin_password = 'Admin123!';
```

⚠️ **Attention :** En production, changez ces identifiants immédiatement !

**2. Hasher le mot de passe**

```php
$admin_password_hash = password_hash($admin_password, PASSWORD_BCRYPT);

// Résultat exemple :
// $admin_password_hash = "$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy"
```

**3. Insérer dans la base**

```php
// Préparer la requête avec des placeholders
$stmt = $pdo->prepare("
    INSERT INTO users (email, password, is_admin)
    VALUES (:email, :password, :is_admin)
");

// Exécuter en remplaçant les placeholders
$stmt->execute([
    'email' => 'admin@example.com',
    'password' => '$2y$10$...',  // Hash
    'is_admin' => 1              // Admin
]);
```

**Résultat dans la base :**

```
Table users :
┌────┬──────────────────┬─────────────────────────┬──────────┬────────────────────┐
│ id │ email            │ password                │ is_admin │ created_at         │
├────┼──────────────────┼─────────────────────────┼──────────┼────────────────────┤
│ 1  │ admin@example.com│ $2y$10$abcdefg...      │ 1        │ 2024-01-15 10:30:45│
└────┴──────────────────┴─────────────────────────┴──────────┴────────────────────┘
```

---

## 5. EXERCICES PRATIQUES

### ✍️ Exercice 1 : Compléter le code de connexion

```php
// Fichier: db_sqlite.php

$db_path = _______ . '/database.db';

try {
    $pdo = new PDO('_______:' . $db_path);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::__________);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::___________);

} catch (_____________ $e) {
    die("Erreur : " . $e->___________());
}
```

<details>
<summary>📖 Voir la solution</summary>

```php
$db_path = __DIR__ . '/database.db';

try {
    $pdo = new PDO('sqlite:' . $db_path);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
```

</details>

---

### ✍️ Exercice 2 : Expliquer chaque colonne

Pour chaque colonne de la table `users`, expliquez son rôle :

1. `id INTEGER PRIMARY KEY AUTOINCREMENT` → **`_______________________`**
2. `email TEXT NOT NULL UNIQUE` → **`_______________________`**
3. `password TEXT NOT NULL` → **`_______________________`**
4. `is_admin INTEGER DEFAULT 0` → **`_______________________`**
5. `created_at TEXT DEFAULT CURRENT_TIMESTAMP` → **`_______________________`**

<details>
<summary>📖 Voir la solution</summary>

1. Identifiant unique qui s'incrémente automatiquement (1, 2, 3...)
2. Email de l'utilisateur, obligatoire et unique (pas de doublons)
3. Mot de passe hashé (jamais en clair), obligatoire
4. Flag admin (0 = utilisateur normal, 1 = admin), valeur par défaut = 0
5. Date de création du compte, remplie automatiquement

</details>

---

### ✍️ Exercice 3 : Pratique

**Tâche :** Exécutez `init_db.php` et vérifiez la création de la base

1. Ouvrez votre navigateur
2. Allez sur `http://localhost:8000/init_db.php`
3. Vérifiez que :
   - ✅ Un fichier `database.db` a été créé
   - ✅ Le message de succès s'affiche
   - ✅ Les identifiants admin sont affichés

4. Ouvrez `database.db` avec DB Browser for SQLite
5. Vérifiez que :
   - ✅ La table `users` existe
   - ✅ Elle contient 5 colonnes
   - ✅ Il y a 1 ligne (l'admin)

---

## 🎓 RÉCAPITULATIF

### Ce que vous devez retenir

| Fichier | Rôle | Lignes clés |
|---------|------|-------------|
| `db_sqlite.php` | Connexion PDO à SQLite | 62, 64, 76 |
| `init_db.php` | Création de la base et de l'admin | 92-100, 171-184 |

### Commandes PDO essentielles

```php
// Connexion
$pdo = new PDO($dsn);

// Configuration
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

// Exécuter une requête simple (sans résultat)
$pdo->exec("CREATE TABLE ...");

// Requête préparée (avec résultat)
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
$stmt->execute(['email' => $email]);
$user = $stmt->fetch();
```

---

## 📚 PROCHAINE ÉTAPE

Dans le **Chapitre 03 - Inscription (register.php)**, vous apprendrez :
- Créer un formulaire HTML
- Valider les données en PHP
- Hasher un mot de passe
- Insérer un utilisateur dans la base
- Gérer les erreurs

➡️ **[Passer au chapitre 03-inscription-register.md](03-inscription-register.md)**

---

**🎉 Félicitations ! Vous maîtrisez maintenant la base de données !**

Vous comprenez comment SQLite fonctionne, comment se connecter avec PDO, et comment créer la structure de la base. Dans le prochain chapitre, nous allons utiliser cette base pour créer des utilisateurs avec `register.php`.
