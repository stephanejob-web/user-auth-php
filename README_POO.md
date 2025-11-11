# 📚 Projet Authentification PHP - Version POO

## 🎯 Introduction

Ce projet a été **refactorisé en POO (Programmation Orientée Objet)** pour démontrer les principes fondamentaux de la POO en PHP.

### Comparaison Version Procédurale vs POO

| Aspect | Procédural | POO |
|--------|-----------|-----|
| **Lignes de code** | ~1500 lignes | ~900 lignes |
| **Duplication** | Beaucoup | Minimale |
| **Maintenance** | Difficile | Facile |
| **Tests** | Difficile | Facile |
| **Réutilisabilité** | Faible | Élevée |
| **Organisation** | Fichiers plats | Structure modulaire |

---

## 📁 Structure du Projet

```
user-auth-php/
├── src/                           # Code source POO
│   ├── Config/
│   │   └── Database.php          # Connexion DB (Singleton)
│   ├── Models/
│   │   └── User.php              # Modèle User (Active Record)
│   ├── Services/
│   │   ├── Auth.php              # Service d'authentification
│   │   ├── Session.php           # Gestion des sessions
│   │   └── Validator.php         # Validation des données
│   └── Utils/
│       └── Response.php          # Gestion des réponses HTTP
│
├── autoload.php                   # Chargement automatique des classes
│
├── Fichiers POO (nouvelles versions)
├── login_poo.php                  # Connexion
├── register_poo.php               # Inscription
├── admin_poo.php                  # Dashboard admin
├── profile_poo.php                # Édition profil
├── edit_user_poo.php              # Édition utilisateur
├── delete_user_poo.php            # Suppression utilisateur
├── toggle_admin_poo.php           # Basculer statut admin
└── logout_poo.php                 # Déconnexion
```

---

## 🏗️ Architecture POO

### 1. **Database** (Pattern Singleton)

**Emplacement :** `src/Config/Database.php`

**Responsabilité :** Gérer la connexion unique à la base de données

**Pattern utilisé :** Singleton

```php
// Utilisation
$database = Database::getInstance();
$pdo = $database->getConnection();
```

**Pourquoi Singleton ?**
- ✅ Une seule connexion pour toute l'application
- ✅ Économie de ressources
- ✅ Accès centralisé

---

### 2. **User** (Pattern Active Record)

**Emplacement :** `src/Models/User.php`

**Responsabilité :** Représenter un utilisateur et gérer les opérations CRUD

**Méthodes principales :**

```php
// Création
$user = new User('email@example.com', password_hash('Pass123!', PASSWORD_BCRYPT));
$user->create();

// Lecture
$user = User::findById(5);
$user = User::findByEmail('email@example.com');
$users = User::findAll();

// Mise à jour
$user->setEmail('newemail@example.com');
$user->updateEmail();

// Suppression
$user->delete();

// Utilitaires
User::emailExists('email@example.com');
User::count();
```

---

### 3. **Auth** (Service Layer)

**Emplacement :** `src/Services/Auth.php`

**Responsabilité :** Gérer l'authentification (login, register, logout)

**Méthodes principales :**

```php
// Inscription
$result = Auth::register($email, $password, $confirmPassword);
if ($result['success']) {
    // Succès
}

// Connexion
$result = Auth::login($email, $password);
if ($result['success']) {
    // Connecté
}

// Déconnexion
Auth::logout();

// Vérifications
Auth::check();        // Connecté ?
Auth::isAdmin();      // Admin ?
Auth::user();         // Objet User
Auth::id();           // ID utilisateur

// Protection de page
Auth::requireAuth();  // Redirige si non connecté
Auth::requireAdmin(); // Redirige si pas admin
```

---

### 4. **Session** (Service Layer)

**Emplacement :** `src/Services/Session.php`

**Responsabilité :** Gérer les sessions PHP

**Méthodes principales :**

```php
// Démarrer
Session::start();

// Définir/Récupérer
Session::set('key', 'value');
$value = Session::get('key', 'default');

// Vérifier
Session::has('key');

// Supprimer
Session::remove('key');
Session::destroy();

// Authentification
Session::login($userId, $email, $isAdmin);
Session::logout();
Session::isAuthenticated();
Session::isAdmin();

// Messages flash
Session::setFlash('success', 'Message');
$message = Session::getFlash('success');
```

---

### 5. **Validator** (Service Layer)

**Emplacement :** `src/Services/Validator.php`

**Responsabilité :** Valider les données utilisateur

**Méthodes principales :**

```php
$validator = new Validator();

// Validations
$validator->validateEmail($email);
$validator->validatePassword($password);
$validator->validatePasswordMatch($password, $confirm);
$validator->validateRequired($value, 'Field Name');

// Gestion des erreurs
if ($validator->hasErrors()) {
    $error = $validator->getFirstError();
    $errors = $validator->getErrors();
}

// Nettoyage
$clean = Validator::sanitize($value);
$cleanEmail = Validator::sanitizeEmail($email);
```

---

### 6. **Response** (Utility)

**Emplacement :** `src/Utils/Response.php`

**Responsabilité :** Gérer les réponses HTTP (redirections, JSON)

**Méthodes principales :**

```php
// Redirections
Response::redirect('page.php');
Response::redirectToHome();
Response::redirectToLogin();
Response::redirectToAdmin();

// Redirection avec message flash
Response::redirectWithMessage('page.php', 'success', 'Message');

// Réponses JSON
Response::json(['data' => $data]);
Response::jsonSuccess('Success message');
Response::jsonError('Error message', 400);
```

---

## 🎓 Concepts POO Utilisés

### 1. **Encapsulation**

Les données sont privées et accessibles via des getters/setters :

```php
class User {
    private ?int $id = null;
    private string $email;

    public function getId(): ?int {
        return $this->id;
    }

    public function setEmail(string $email): void {
        $this->email = $email;
    }
}
```

### 2. **Abstraction**

On cache la complexité derrière une interface simple :

```php
// Avant (procédural) - complexe
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
$stmt->execute(['email' => $email]);
$user = $stmt->fetch();

// Après (POO) - simple
$user = User::findByEmail($email);
```

### 3. **Méthodes Statiques**

Utilisées pour des opérations qui ne dépendent pas d'une instance :

```php
// Pas besoin d'instancier la classe
$user = User::findById(5);
Auth::requireAuth();
Session::start();
```

### 4. **Pattern Singleton**

Garantit une seule instance de la classe Database :

```php
class Database {
    private static ?Database $instance = null;

    private function __construct() { }

    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
```

### 5. **Namespaces**

Organisation du code en espaces de noms :

```php
namespace App\Models;
namespace App\Services;
namespace App\Config;
```

### 6. **Autoloading (PSR-4)**

Chargement automatique des classes :

```php
// autoload.php charge automatiquement toutes les classes
require_once 'autoload.php';

use App\Services\Auth;
use App\Models\User;
```

---

## 📝 Exemples d'Utilisation

### Exemple 1 : Inscription

```php
require_once 'autoload.php';

use App\Services\Auth;
use App\Utils\Response;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = Auth::register($email, $password, $confirmPassword);

    if ($result['success']) {
        Response::redirectWithMessage('login_poo.php', 'success', 'Registration successful!');
    } else {
        $error = $result['message'];
    }
}
```

### Exemple 2 : Connexion

```php
require_once 'autoload.php';

use App\Services\Auth;
use App\Utils\Response;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = Auth::login($email, $password);

    if ($result['success']) {
        Response::redirectToHome();
    } else {
        $error = $result['message'];
    }
}
```

### Exemple 3 : Protection de Page

```php
require_once 'autoload.php';

use App\Services\Auth;
use App\Services\Session;

Session::start();

// Pour une page utilisateur
Auth::requireAuth();

// Pour une page admin
Auth::requireAdmin();
```

### Exemple 4 : Gestion des Utilisateurs

```php
require_once 'autoload.php';

use App\Models\User;

// Récupérer tous les utilisateurs
$users = User::findAll();

foreach ($users as $user) {
    echo $user->getEmail() . " - ";
    echo ($user->isAdmin() ? "Admin" : "User") . "<br>";
}

// Modifier un utilisateur
$user = User::findById(5);
$user->setEmail('newemail@example.com');
$user->updateEmail();

// Supprimer un utilisateur
$user->delete();
```

---

## 🎯 Avantages de la Version POO

### 1. **Code Plus Court**
- **Procédural :** ~1500 lignes
- **POO :** ~900 lignes
- **Réduction :** 40%

### 2. **Réutilisabilité**
```php
// Même code pour inscription web ET API
$result = Auth::register($email, $password, $confirmPassword);
```

### 3. **Testabilité**
```php
// Tests unitaires faciles
class AuthTest extends TestCase {
    public function testLogin() {
        $result = Auth::login('test@example.com', 'Password123!');
        $this->assertTrue($result['success']);
    }
}
```

### 4. **Maintenance**
- Changer la logique d'authentification ? → Modifier uniquement `Auth.php`
- Changer la validation ? → Modifier uniquement `Validator.php`
- Un seul endroit à modifier au lieu de 10 fichiers

### 5. **Lisibilité**
```php
// Avant (procédural)
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header('Location: index.php');
    exit();
}

// Après (POO)
Auth::requireAdmin();
```

---

## 🚀 Pour Aller Plus Loin

### Prochaines Étapes d'Apprentissage

1. **Héritage**
   - Créer une classe `Model` de base
   - Faire hériter `User` de `Model`

2. **Interfaces**
   - Créer une interface `AuthInterface`
   - Permettre différentes implémentations (Auth, OAuth, LDAP)

3. **Dependency Injection**
   - Injecter les dépendances au lieu de les créer
   - Facilite les tests et la flexibilité

4. **Composer**
   - Utiliser Composer pour l'autoloading
   - Ajouter des bibliothèques externes

5. **Design Patterns**
   - Repository Pattern
   - Factory Pattern
   - Observer Pattern

---

## 📖 Ressources Complémentaires

- [PHP The Right Way - POO](https://phptherightway.com/#object-oriented-programming)
- [PSR-4 Autoloading](https://www.php-fig.org/psr/psr-4/)
- [Design Patterns en PHP](https://refactoring.guru/design-patterns/php)
- [Cours POO PHP - Grafikart](https://grafikart.fr/formations/programmation-objet-php)

---

## 🎓 Questions Pédagogiques

### Pourquoi utiliser POO ?
- ✅ Code plus organisé et structuré
- ✅ Réutilisabilité du code
- ✅ Facilite la collaboration en équipe
- ✅ Facilite les tests
- ✅ Standard de l'industrie

### Quand NE PAS utiliser POO ?
- ❌ Scripts très simples (< 50 lignes)
- ❌ Scripts one-shot (exécutés une seule fois)
- ❌ Prototypes rapides

### POO vs Procédural : Que choisir ?
- **Petit projet (< 500 lignes) :** Procédural OK
- **Moyen projet (500-2000 lignes) :** POO recommandé
- **Grand projet (> 2000 lignes) :** POO obligatoire

---

## ✅ Conclusion

Cette refactorisation démontre comment la POO peut :
- Réduire la complexité
- Améliorer la maintenabilité
- Faciliter les tests
- Rendre le code réutilisable

**Fichiers à comparer :**
- `login.php` (procédural) vs `login_poo.php` (POO)
- `register.php` (procédural) vs `register_poo.php` (POO)
- `admin.php` (procédural) vs `admin_poo.php` (POO)

---

## 🤝 Contribution

N'hésitez pas à explorer le code, poser des questions et expérimenter !

**Bon apprentissage de la POO ! 🚀**
