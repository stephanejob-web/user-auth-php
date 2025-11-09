# 📋 GUIDE DE RÉFÉRENCE RAPIDE

> **Aide-mémoire pour tous les fichiers du projet**
> **Consultez ce fichier quand vous cherchez une information précise**

---

## 🎯 UTILISATION

Ce guide liste pour **chaque fichier** :
- ✅ Son rôle principal
- ✅ Les concepts clés utilisés
- ✅ Les lignes de code importantes
- ✅ Les fonctions PHP essentielles

---

## 📁 FICHIERS DE BASE

### `db_sqlite.php` (Connexion PDO SQLite)

**Rôle :** Se connecter à la base de données SQLite avec PDO

**Concepts clés :**
- PDO (PHP Data Objects)
- DSN (Data Source Name)
- Gestion d'erreurs avec try/catch
- Configuration PDO

**Code essentiel :**
```php
// Ligne 29 : Chemin de la base
$db_path = __DIR__ . '/database.db';

// Ligne 43 : DSN
$dsn = "sqlite:$db_path";

// Ligne 62 : Connexion PDO
$pdo = new PDO($dsn);

// Ligne 69 : Mode erreur EXCEPTION
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Ligne 76 : Mode fetch ASSOC
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

// Ligne 101 : Activer foreign keys
$pdo->exec('PRAGMA foreign_keys = ON');
```

**Fonctions PHP :**
- `__DIR__` : Dossier actuel
- `new PDO()` : Créer une connexion
- `setAttribute()` : Configurer PDO
- `exec()` : Exécuter une commande SQL

**Voir aussi :** `db.php:1-160`

---

### `init_db.php` (Initialisation de la base)

**Rôle :** Créer la table `users` et l'admin par défaut

**Concepts clés :**
- CREATE TABLE avec SQLite
- Types de données SQLite
- password_hash()
- INSERT avec requêtes préparées

**Code essentiel :**
```php
// Ligne 47 : Vérifier si la base existe
if (file_exists($db_path)) {
    unlink($db_path);  // Supprimer
}

// Lignes 92-100 : Créer la table users
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    is_admin INTEGER DEFAULT 0,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP
)

// Ligne 167 : Hasher le mot de passe admin
$admin_password_hash = password_hash($admin_password, PASSWORD_BCRYPT);

// Lignes 177-184 : Insérer l'admin
$stmt = $pdo->prepare("INSERT INTO users (email, password, is_admin) VALUES (:email, :password, :is_admin)");
$stmt->execute([
    'email' => $admin_email,
    'password' => $admin_password_hash,
    'is_admin' => 1
]);
```

**Fonctions PHP :**
- `file_exists()` : Vérifier si un fichier existe
- `unlink()` : Supprimer un fichier
- `password_hash()` : Hasher un mot de passe
- `prepare()` : Préparer une requête
- `execute()` : Exécuter une requête

**Voir aussi :** `init_db.php:1-300`

---

## 🎨 FICHIERS DE PRÉSENTATION

### `header.php` (Menu de navigation)

**Rôle :** Afficher le menu selon l'état de connexion

**Concepts clés :**
- Gestion de session
- Navigation conditionnelle
- Balises sémantiques HTML5

**Code essentiel :**
```php
// Ligne 26 : Démarrer la session (si pas déjà démarrée)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ligne 127 : Navigation si connecté
if (isset($_SESSION['user_id'])) {
    // Afficher Profile, Logout

    // Ligne 164 : Navigation si admin
    if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) {
        // Afficher Admin
    }
} else {
    // Afficher Register, Login
}
```

**Fonctions PHP :**
- `session_status()` : État de la session
- `session_start()` : Démarrer une session
- `isset()` : Vérifier si une variable existe

**Voir aussi :** `header.php:1-227`

---

## 👤 PAGES PUBLIQUES

### `index.php` (Page d'accueil)

**Rôle :** Afficher la page d'accueil (différente si connecté ou non)

**Concepts clés :**
- Affichage conditionnel
- htmlspecialchars()

**Code essentiel :**
```php
// Ligne 47 : Vérifier si connecté
if (isset($_SESSION['user_id'])) {
    // Afficher "Hello, email!"
    echo htmlspecialchars($_SESSION['email']);

    // Ligne 89 : Si admin
    if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) {
        // Afficher message admin
    }
} else {
    // Afficher invitation à se connecter
}
```

**Fonctions PHP :**
- `htmlspecialchars()` : Échapper les caractères HTML (sécurité XSS)

**Voir aussi :** `index.php:1-229`

---

### `register.php` (Inscription)

**Rôle :** Créer un nouveau compte utilisateur

**Concepts clés :**
- Validation email avec filter_var()
- Validation mot de passe avec preg_match()
- password_hash()
- Requêtes préparées
- Vérification email unique

**Code essentiel :**
```php
// Ligne 114 : Valider email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = "Invalid email format.";
}

// Ligne 160 : Valider majuscule
elseif (!preg_match('/[A-Z]/', $password)) {
    $error = "Must contain uppercase.";
}

// Ligne 230 : Vérifier email unique
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");

// Ligne 280 : Hasher le mot de passe
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

// Ligne 298 : Insérer l'utilisateur
$stmt = $pdo->prepare("INSERT INTO users (email, password, is_admin) VALUES (:email, :password, 0)");
```

**Fonctions PHP :**
- `trim()` : Supprimer espaces début/fin
- `empty()` : Vérifier si vide
- `filter_var()` : Valider format
- `strlen()` : Longueur d'une chaîne
- `preg_match()` : Expression régulière
- `password_hash()` : Hasher mot de passe

**Voir aussi :** `register.php:1-512`

---

### `login.php` (Connexion)

**Rôle :** Authentifier un utilisateur et créer une session

**Concepts clés :**
- password_verify()
- Création de session
- Redirection avec header()

**Code essentiel :**
```php
// Ligne 93 : Récupérer l'utilisateur
$stmt = $pdo->prepare("SELECT id, email, password, is_admin FROM users WHERE email = :email");
$user = $stmt->fetch();

// Ligne 142 : Vérifier le mot de passe
if (!password_verify($password, $user['password'])) {
    $error = "Invalid email or password.";
}

// Lignes 175-184 : Créer la session
$_SESSION['user_id'] = $user['id'];
$_SESSION['email'] = $user['email'];
$_SESSION['is_admin'] = $user['is_admin'];

// Ligne 201 : Rediriger
header('Location: index.php');
exit();
```

**Fonctions PHP :**
- `password_verify()` : Vérifier un hash
- `header()` : Envoyer un en-tête HTTP
- `exit()` : Arrêter le script

**Voir aussi :** `login.php:1-390`

---

## 🔐 PAGES PROTÉGÉES

### `profile.php` (Modification du profil)

**Rôle :** Permettre à l'utilisateur de modifier son email et mot de passe

**Concepts clés :**
- Protection de page (utilisateur connecté requis)
- Champs optionnels
- Requête UPDATE dynamique
- Vérification email unique (sauf pour soi-même)

**Code essentiel :**
```php
// Ligne 35 : Protection de page
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Ligne 144 : Vérifier email unique (sauf pour soi)
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email AND id != :user_id");

// Lignes 230-270 : Construction dynamique UPDATE
$set_clause = [];
foreach ($updates as $key => $value) {
    $set_clause[] = "$key = :$key";
}
$set_string = implode(', ', $set_clause);
$sql = "UPDATE users SET $set_string WHERE id = :user_id";

// Ligne 286 : Mettre à jour la session si email changé
if (isset($updates['email'])) {
    $_SESSION['email'] = $new_email;
}
```

**Fonctions PHP :**
- `foreach` : Boucler sur un tableau
- `implode()` : Joindre un tableau en chaîne

**Voir aussi :** `profile.php:1-483`

---

### `logout.php` (Déconnexion)

**Rôle :** Détruire la session et déconnecter l'utilisateur

**Concepts clés :**
- session_unset()
- session_destroy()
- Redirection

**Code essentiel :**
```php
// Ligne 35 : Démarrer la session (pour pouvoir la détruire)
session_start();

// Ligne 63 : Vider toutes les variables de session
session_unset();

// Ligne 88 : Détruire complètement la session
session_destroy();

// Ligne 105 : Rediriger vers l'accueil
header('Location: index.php');
exit();
```

**Fonctions PHP :**
- `session_unset()` : Vider `$_SESSION`
- `session_destroy()` : Détruire la session côté serveur

**Voir aussi :** `logout.php:1-249`

---

## 👨‍💼 PAGES ADMIN

### `admin.php` (Liste des utilisateurs)

**Rôle :** Afficher tous les utilisateurs (admin uniquement)

**Concepts clés :**
- Protection admin
- fetchAll() pour récupérer plusieurs lignes
- Boucle foreach
- Tableaux HTML

**Code essentiel :**
```php
// Ligne 42 : Protection admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header('Location: index.php');
    exit();
}

// Ligne 90 : Récupérer tous les utilisateurs
$stmt = $pdo->prepare("SELECT id, email, is_admin, created_at FROM users ORDER BY id DESC");
$stmt->execute();

// Ligne 104 : fetchAll() récupère TOUT
$users = $stmt->fetchAll();

// Ligne 227 : Boucle foreach
foreach ($users as $user) {
    // Ligne 280 : Opérateur ternaire
    echo $user['is_admin'] == 1 ? 'Yes' : 'No';
}
```

**Fonctions PHP :**
- `fetchAll()` : Récupérer toutes les lignes
- `foreach` : Boucler sur un tableau
- `count()` : Nombre d'éléments dans un tableau
- Opérateur ternaire `? :` : Condition courte

**Voir aussi :** `admin.php:1-534`

---

### `edit_user.php` (Édition utilisateur)

**Rôle :** Modifier n'importe quel utilisateur (admin uniquement)

**Concepts clés :**
- Paramètres GET
- Conversion de type (int)
- Checkbox HTML
- Pré-remplissage de formulaire
- Protection multi-niveaux

**Code essentiel :**
```php
// Ligne 61 : Vérifier qu'un ID est fourni
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: admin.php');
    exit();
}

// Ligne 81 : Conversion en int (IMPORTANT)
$user_id = (int)$_GET['id'];

// Ligne 91 : Récupérer l'utilisateur
$stmt = $pdo->prepare("SELECT id, email, is_admin FROM users WHERE id = :id");

// Ligne 100 : Vérifier qu'il existe
if (!$user) {
    header('Location: admin.php');
    exit();
}

// Ligne 129 : Récupérer checkbox
$is_admin = isset($_POST['is_admin']) ? 1 : 0;

// Ligne 358 : Pré-cocher la checkbox
<input type="checkbox" <?php echo $user['is_admin'] == 1 ? 'checked' : ''; ?>>

// Ligne 324 : Garder l'ID dans l'action
<form method="POST" action="edit_user.php?id=<?php echo $user['id']; ?>">
```

**Fonctions PHP :**
- `(int)` : Conversion forcée en entier
- `isset()` : Vérifier existence

**Points clés :**
- Les checkboxes ne sont dans `$_POST` que si cochées
- Toujours convertir `$_GET['id']` en int
- Garder l'ID dans l'URL lors de la soumission du formulaire

**Voir aussi :** `edit_user.php:1-507`

---

### `delete_user.php` (Suppression utilisateur)

**Rôle :** Supprimer un utilisateur (admin uniquement)

**Concepts clés :**
- Requête DELETE
- Protection anti-auto-suppression
- Vérifications multiples
- Redirection immédiate

**Code essentiel :**
```php
// Ligne 46 : Protection admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header('Location: index.php');
    exit();
}

// Ligne 78 : Vérifier ID fourni
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: admin.php');
    exit();
}

// Ligne 112 : Protection anti-auto-suppression
if ($user_id === $_SESSION['user_id']) {
    header('Location: admin.php');
    exit();
}

// Ligne 144 : Vérifier que l'utilisateur existe
$stmt = $pdo->prepare("SELECT id, email FROM users WHERE id = :id");
if (!$user) {
    header('Location: admin.php');
    exit();
}

// Ligne 184 : Supprimer l'utilisateur
$stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
$stmt->execute(['id' => $user_id]);

// Ligne 202 : Redirection
header('Location: admin.php');
exit();
```

**Fonctions PHP :**
- Requête DELETE en SQL

**Points clés :**
- 4 niveaux de protection
- Jamais de HTML (redirection immédiate)
- Un admin ne peut pas se supprimer lui-même

**Voir aussi :** `delete_user.php:1-347`

---

## 🔐 CONCEPTS DE SÉCURITÉ

### 1. Hachage de mot de passe

```php
// À L'INSCRIPTION
$hash = password_hash($password, PASSWORD_BCRYPT);
// Stocke : $2y$10$abcdefg...

// À LA CONNEXION
if (password_verify($password_saisi, $hash_en_base)) {
    // Mot de passe correct
}
```

**Règle d'or :** JAMAIS stocker les mots de passe en clair !

---

### 2. Requêtes préparées

```php
// ❌ DANGEREUX (injection SQL)
$sql = "SELECT * FROM users WHERE email = '$email'";

// ✅ SÉCURISÉ (requête préparée)
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
$stmt->execute(['email' => $email]);
```

**Règle d'or :** TOUJOURS utiliser des requêtes préparées !

---

### 3. Protection XSS

```php
// ❌ DANGEREUX
echo $_SESSION['email'];

// ✅ SÉCURISÉ
echo htmlspecialchars($_SESSION['email']);
```

**Règle d'or :** TOUJOURS échapper les données avant affichage !

---

### 4. Protection de pages

```php
// Utilisateur connecté requis
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Admin requis
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header('Location: index.php');
    exit();
}
```

**Règle d'or :** TOUJOURS vérifier les permissions !

---

## 📊 FONCTIONS PHP ESSENTIELLES

### Gestion de sessions

| Fonction | Rôle | Exemple |
|----------|------|---------|
| `session_start()` | Démarre/reprend une session | `session_start();` |
| `$_SESSION['clé']` | Stocker une donnée | `$_SESSION['user_id'] = 5;` |
| `isset($_SESSION['clé'])` | Vérifier si existe | `isset($_SESSION['user_id'])` |
| `session_unset()` | Vider toutes les variables | `session_unset();` |
| `session_destroy()` | Détruire la session | `session_destroy();` |

### Validation de données

| Fonction | Rôle | Exemple |
|----------|------|---------|
| `empty($var)` | Vérifier si vide | `empty($email)` |
| `trim($string)` | Supprimer espaces | `trim($_POST['email'])` |
| `filter_var()` | Valider format | `filter_var($email, FILTER_VALIDATE_EMAIL)` |
| `strlen($string)` | Longueur | `strlen($password) < 8` |
| `preg_match()` | Expression régulière | `preg_match('/[A-Z]/', $password)` |

### Mots de passe

| Fonction | Rôle | Exemple |
|----------|------|---------|
| `password_hash()` | Hasher un mot de passe | `password_hash($pass, PASSWORD_BCRYPT)` |
| `password_verify()` | Vérifier un hash | `password_verify($pass, $hash)` |

### Base de données (PDO)

| Méthode | Rôle | Exemple |
|---------|------|---------|
| `prepare()` | Préparer une requête | `$pdo->prepare("SELECT...")` |
| `execute()` | Exécuter | `$stmt->execute(['email' => $email])` |
| `fetch()` | Récupérer 1 ligne | `$user = $stmt->fetch()` |
| `fetchAll()` | Récupérer toutes les lignes | `$users = $stmt->fetchAll()` |
| `exec()` | Exécuter commande SQL | `$pdo->exec("CREATE TABLE...")` |

### Tableaux

| Fonction | Rôle | Exemple |
|----------|------|---------|
| `count($array)` | Nombre d'éléments | `count($users)` |
| `foreach` | Boucler | `foreach ($users as $user)` |
| `implode()` | Joindre en chaîne | `implode(', ', $array)` |

### Redirection

| Fonction | Rôle | Exemple |
|----------|------|---------|
| `header()` | Envoyer en-tête HTTP | `header('Location: index.php')` |
| `exit()` | Arrêter le script | `exit();` |

---

## 🎓 CHECKLIST DE COMPRÉHENSION

### Base de données
- [ ] Je sais ce qu'est PDO
- [ ] Je comprends la différence entre SQLite et MySQL
- [ ] Je sais créer une connexion PDO
- [ ] Je connais les 5 colonnes de la table `users`
- [ ] Je comprends PRIMARY KEY et AUTOINCREMENT

### Authentification
- [ ] Je sais hasher un mot de passe avec `password_hash()`
- [ ] Je sais vérifier un hash avec `password_verify()`
- [ ] Je comprends comment fonctionnent les sessions
- [ ] Je sais créer une session après connexion
- [ ] Je sais détruire une session

### Sécurité
- [ ] Je comprends pourquoi utiliser des requêtes préparées
- [ ] Je sais utiliser `htmlspecialchars()`
- [ ] Je comprends la protection de pages
- [ ] Je sais pourquoi ne jamais stocker les mots de passe en clair

### Opérations CRUD
- [ ] Je sais insérer un utilisateur (CREATE)
- [ ] Je sais récupérer un utilisateur (READ)
- [ ] Je sais modifier un utilisateur (UPDATE)
- [ ] Je sais supprimer un utilisateur (DELETE)

### Administration
- [ ] Je comprends `fetchAll()` vs `fetch()`
- [ ] Je sais faire une boucle `foreach`
- [ ] Je comprends les paramètres GET
- [ ] Je sais pré-remplir un formulaire
- [ ] Je comprends comment fonctionnent les checkboxes

---

## 📚 POUR ALLER PLUS LOIN

### Améliorations possibles

1. **Validation côté client (JavaScript)**
   - Validation en temps réel
   - Meilleure expérience utilisateur

2. **Double authentification (2FA)**
   - Code par SMS ou email
   - Sécurité renforcée

3. **Récupération de mot de passe**
   - Lien par email
   - Token temporaire

4. **Pagination**
   - Pour la liste admin
   - LIMIT et OFFSET en SQL

5. **Recherche et filtres**
   - Chercher un utilisateur
   - Filtrer par admin/user

6. **Logs de sécurité**
   - Enregistrer les connexions
   - Détecter les tentatives suspectes

7. **Rate limiting**
   - Limiter les tentatives de connexion
   - Protection contre bruteforce

---

**Bonne révision ! 📖**
