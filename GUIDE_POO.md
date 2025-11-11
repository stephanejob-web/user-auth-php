# 🚀 Guide de Démarrage Rapide - Version POO

## 📋 Table des Matières

1. [Démarrage](#démarrage)
2. [Structure du Projet](#structure-du-projet)
3. [Les Classes Principales](#les-classes-principales)
4. [Exemples Pratiques](#exemples-pratiques)
5. [Exercices](#exercices)

---

## 🎯 Démarrage

### Étape 1 : Accéder au Projet

Ouvrez votre navigateur et accédez à :
```
http://localhost/user-auth-php/index_poo.php
```

### Étape 2 : Tester les Fonctionnalités

1. **S'inscrire** → `register_poo.php`
2. **Se connecter** → `login_poo.php`
3. **Éditer son profil** → `profile_poo.php`
4. **Accéder au dashboard admin** → `admin_poo.php` (si admin)

---

## 📁 Structure du Projet

### Organisation des Dossiers

```
src/
├── Config/          # Configuration (connexion DB)
├── Models/          # Modèles (User)
├── Services/        # Services (Auth, Session, Validator)
└── Utils/           # Utilitaires (Response)
```

### Principe de Responsabilité Unique

Chaque classe a **une seule responsabilité** :

| Classe | Responsabilité |
|--------|---------------|
| `Database` | Gérer la connexion à la base de données |
| `User` | Représenter et manipuler un utilisateur |
| `Auth` | Gérer l'authentification |
| `Session` | Gérer les sessions PHP |
| `Validator` | Valider les données |
| `Response` | Gérer les réponses HTTP |

---

## 🎓 Les Classes Principales

### 1. Database (Singleton)

**Emplacement :** `src/Config/Database.php`

```php
// Obtenir la connexion
$database = Database::getInstance();
$pdo = $database->getConnection();
```

**Concepts POO :**
- Pattern Singleton
- Constructeur privé
- Méthode statique `getInstance()`

---

### 2. User (Active Record)

**Emplacement :** `src/Models/User.php`

```php
// Créer un utilisateur
$user = new User('email@example.com', password_hash('Pass123!', PASSWORD_BCRYPT));
$user->setIsAdmin(0);
$user->create();

// Trouver un utilisateur
$user = User::findById(5);
$user = User::findByEmail('email@example.com');

// Modifier un utilisateur
$user->setEmail('newemail@example.com');
$user->updateEmail();

// Supprimer un utilisateur
$user->delete();
```

**Concepts POO :**
- Encapsulation (propriétés privées)
- Getters et Setters
- Méthodes d'instance vs méthodes statiques
- Pattern Active Record

---

### 3. Auth (Service)

**Emplacement :** `src/Services/Auth.php`

```php
// Inscription
$result = Auth::register($email, $password, $confirmPassword);

// Connexion
$result = Auth::login($email, $password);

// Déconnexion
Auth::logout();

// Vérifications
Auth::check();           // Connecté ?
Auth::isAdmin();         // Admin ?
Auth::user();            // Objet User
Auth::requireAuth();     // Protection de page
Auth::requireAdmin();    // Protection admin
```

**Concepts POO :**
- Service Layer Pattern
- Méthodes statiques
- Séparation des responsabilités

---

### 4. Session (Service)

**Emplacement :** `src/Services/Session.php`

```php
// Démarrer
Session::start();

// Définir/Récupérer
Session::set('key', 'value');
$value = Session::get('key');

// Authentification
Session::login($userId, $email, $isAdmin);
Session::logout();
Session::isAuthenticated();

// Messages flash
Session::setFlash('success', 'Message');
$message = Session::getFlash('success');
```

**Concepts POO :**
- Abstraction (cache `$_SESSION`)
- Méthodes statiques
- Encapsulation

---

### 5. Validator (Service)

**Emplacement :** `src/Services/Validator.php`

```php
$validator = new Validator();

// Valider
$validator->validateEmail($email);
$validator->validatePassword($password);

// Récupérer les erreurs
if ($validator->hasErrors()) {
    $error = $validator->getFirstError();
}

// Nettoyage
$clean = Validator::sanitize($value);
```

**Concepts POO :**
- Méthodes d'instance (avec état)
- Méthodes statiques (sans état)
- Gestion d'erreurs

---

### 6. Response (Utility)

**Emplacement :** `src/Utils/Response.php`

```php
// Redirections
Response::redirectToHome();
Response::redirectToLogin();

// Avec message flash
Response::redirectWithMessage('page.php', 'success', 'Message');

// JSON (pour API)
Response::json(['data' => $data]);
```

**Concepts POO :**
- Méthodes statiques utilitaires
- Abstraction des redirections

---

## 💡 Exemples Pratiques

### Exemple 1 : Créer une Page d'Inscription

**Fichier :** `my_register.php`

```php
<?php
require_once 'autoload.php';

use App\Services\Auth;
use App\Utils\Response;

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    $result = Auth::register($email, $password, $confirmPassword);

    if ($result['success']) {
        Response::redirectWithMessage('login_poo.php', 'success', 'Inscription réussie !');
    } else {
        $error = $result['message'];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inscription</title>
</head>
<body>
    <h1>Inscription</h1>

    <?php if ($error): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Mot de passe" required><br>
        <input type="password" name="confirm_password" placeholder="Confirmer" required><br>
        <button type="submit">S'inscrire</button>
    </form>
</body>
</html>
```

---

### Exemple 2 : Page Protégée

**Fichier :** `my_protected_page.php`

```php
<?php
require_once 'autoload.php';

use App\Services\Auth;
use App\Services\Session;

Session::start();

// Protection : utilisateur connecté requis
Auth::requireAuth();

// Récupérer l'utilisateur actuel
$user = Auth::user();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Page Protégée</title>
</head>
<body>
    <h1>Bienvenue, <?php echo htmlspecialchars($user->getEmail()); ?> !</h1>

    <p>Cette page est accessible uniquement aux utilisateurs connectés.</p>

    <a href="logout_poo.php">Se déconnecter</a>
</body>
</html>
```

---

### Exemple 3 : Lister les Utilisateurs

**Fichier :** `my_users_list.php`

```php
<?php
require_once 'autoload.php';

use App\Services\Auth;
use App\Services\Session;
use App\Models\User;

Session::start();
Auth::requireAdmin();

$users = User::findAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Liste des Utilisateurs</title>
</head>
<body>
    <h1>Liste des Utilisateurs</h1>

    <table border="1">
        <tr>
            <th>ID</th>
            <th>Email</th>
            <th>Admin</th>
        </tr>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo $user->getId(); ?></td>
                <td><?php echo htmlspecialchars($user->getEmail()); ?></td>
                <td><?php echo $user->isAdmin() ? 'Oui' : 'Non'; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <p>Total : <?php echo count($users); ?> utilisateurs</p>
</body>
</html>
```

---

## 🏋️ Exercices

### Exercice 1 : Créer une Page "Mes Informations"

**Objectif :** Créer `my_info.php` qui affiche toutes les informations de l'utilisateur connecté.

**Indices :**
- Utiliser `Auth::user()`
- Afficher : ID, Email, Statut Admin, Date de création

---

### Exercice 2 : Ajouter une Méthode `countAdmins()`

**Objectif :** Ajouter une méthode statique dans `User.php` qui compte le nombre d'administrateurs.

**Code à ajouter dans `User.php` :**

```php
public static function countAdmins(): int
{
    $database = Database::getInstance();
    $pdo = $database->getConnection();

    try {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE is_admin = 1");
        $result = $stmt->fetch();
        return (int) $result['total'];
    } catch (PDOException $e) {
        return 0;
    }
}
```

**Utilisation :**

```php
$adminCount = User::countAdmins();
echo "Nombre d'admins : $adminCount";
```

---

### Exercice 3 : Créer une Classe `EmailService`

**Objectif :** Créer une nouvelle classe pour envoyer des emails.

**Fichier :** `src/Services/EmailService.php`

```php
<?php

namespace App\Services;

class EmailService
{
    public static function send(string $to, string $subject, string $message): bool
    {
        // Simulation d'envoi d'email
        // En production, utiliser mail() ou une bibliothèque comme PHPMailer

        echo "Email envoyé à : $to<br>";
        echo "Sujet : $subject<br>";
        echo "Message : $message<br>";

        return true;
    }
}
```

**Utilisation :**

```php
use App\Services\EmailService;

EmailService::send('user@example.com', 'Bienvenue', 'Merci de vous être inscrit !');
```

---

### Exercice 4 : Ajouter la Gestion des Rôles

**Objectif :** Étendre `User` pour gérer plusieurs rôles (admin, moderator, user).

**Indices :**
- Ajouter une colonne `role` dans la table `users`
- Ajouter une méthode `hasRole(string $role): bool`
- Modifier `Auth::requireAdmin()` pour utiliser les rôles

---

## 🎯 Points Clés à Retenir

### 1. Toujours utiliser `autoload.php`

```php
require_once 'autoload.php';
```

### 2. Utiliser les `use` pour importer les classes

```php
use App\Services\Auth;
use App\Models\User;
```

### 3. Protéger les pages

```php
Auth::requireAuth();  // Utilisateur connecté requis
Auth::requireAdmin(); // Admin requis
```

### 4. Valider TOUJOURS les données utilisateur

```php
$validator = new Validator();
$validator->validateEmail($email);
```

### 5. Privilégier les méthodes de classes aux requêtes SQL directes

```php
// ❌ Mauvais
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute(['id' => $id]);

// ✅ Bon
$user = User::findById($id);
```

---

## 🚀 Pour Aller Plus Loin

### 1. Lire le Code Source

Explorez chaque classe dans `src/` pour comprendre comment elle fonctionne.

### 2. Comparer Procédural vs POO

Comparez les fichiers :
- `login.php` (procédural) vs `login_poo.php` (POO)
- `register.php` vs `register_poo.php`
- `admin.php` vs `admin_poo.php`

### 3. Créer Vos Propres Classes

Essayez de créer :
- `Article` (modèle pour des articles de blog)
- `Comment` (modèle pour des commentaires)
- `Logger` (service pour logger les erreurs)

### 4. Apprendre les Design Patterns

- Singleton (Database)
- Active Record (User)
- Service Layer (Auth, Session, Validator)
- Factory Pattern
- Repository Pattern

---

## 📚 Ressources

- **Documentation PHP POO :** https://www.php.net/manual/fr/language.oop5.php
- **PSR-4 Autoloading :** https://www.php-fig.org/psr/psr-4/
- **Design Patterns :** https://refactoring.guru/design-patterns/php
- **README_POO.md :** Documentation complète du projet

---

## ✅ Checklist d'Apprentissage

- [ ] J'ai testé toutes les pages POO
- [ ] J'ai lu le code source de chaque classe
- [ ] J'ai compris le pattern Singleton (Database)
- [ ] J'ai compris les namespaces et l'autoloading
- [ ] J'ai fait les exercices
- [ ] J'ai créé ma propre classe

---

**Bon apprentissage de la POO ! 🎓**
