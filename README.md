# Projet PHP - Authentification et Gestion d'Utilisateurs

> **🚀 Démarrage rapide ?** Consultez le [QUICKSTART.md](QUICKSTART.md) (5 minutes)

## Description

Projet pédagogique simple en PHP pour illustrer les concepts de base :
- Inscription utilisateur (registration)
- Connexion (login)
- Déconnexion (logout)
- Édition de profil
- Gestion administrateur

**Idéal pour :**
- 👨‍🎓 Étudiants en reconversion
- 📚 Apprentissage de PHP et PDO
- 🔐 Comprendre l'authentification sécurisée
- 💾 Découvrir les bases de données (SQLite ou MySQL)

## 💡 SQLite vs MySQL - Comprendre la différence

### Qu'est-ce que SQLite ?

**SQLite** est une base de données **ultra-simple** qui stocke tout dans **un seul fichier** (database.db).

**Imaginez la différence comme ceci :**

| 🗄️ **MySQL** | 📁 **SQLite** |
|--------------|---------------|
| Comme un **serveur de stockage** dans une entreprise | Comme un **fichier Excel** sur votre ordinateur |
| Besoin d'installer un logiciel séparé (MySQL Server) | Déjà inclus avec PHP, rien à installer |
| Besoin de créer un compte, un mot de passe, une base | Juste un fichier .db |
| Besoin de phpMyAdmin pour voir les données | Ouvrir le fichier avec VS Code ou un logiciel |

### 🎯 Pourquoi utiliser SQLite AU DÉBUT du projet ?

**Pour vous concentrer sur l'apprentissage de PHP !**

Avec SQLite, en 2 minutes vous êtes prêt :
1. ✅ Renommer 3 fichiers
2. ✅ Lancer le serveur PHP
3. ✅ Créer la base en 1 clic (init_db.php)
4. ✅ **Commencer à coder !**

Avec MySQL, vous devriez passer 30-60 minutes à :
1. ❌ Installer MySQL Server
2. ❌ Installer phpMyAdmin
3. ❌ Créer un utilisateur et un mot de passe
4. ❌ Créer la base de données
5. ❌ Créer les tables manuellement
6. ❌ Insérer l'utilisateur admin
7. ✅ Enfin commencer à coder...

**SQLite = Zéro configuration, vous codez tout de suite !**

### 🚀 Pourquoi passer à MySQL PLUS TARD ?

**MySQL devient nécessaire quand vous déployez en production.**

| Situation | Base recommandée |
|-----------|------------------|
| 👨‍🎓 Apprendre PHP | ✅ SQLite |
| 🧪 Tester votre code | ✅ SQLite |
| 💻 Projet personnel | ✅ SQLite |
| 🌐 Site web avec **plusieurs utilisateurs simultanés** | ✅ MySQL |
| 🏢 Application professionnelle | ✅ MySQL |
| 📊 Grosse base de données (> 100 000 lignes) | ✅ MySQL |

**La bonne nouvelle ?** Le code PHP reste **identique** ! Seul le fichier `db.php` change.

### 👀 Comment voir la base de données SQLite avec VS Code ?

**Méthode 1 : Extension "SQLite Viewer" (recommandée)**

1. **Installer l'extension :**
   - Ouvrez VS Code
   - Cliquez sur l'icône Extensions (carré avec 4 petits carrés) dans la barre latérale
   - Recherchez **"SQLite Viewer"** (ou "SQLite" par alexcvzz)
   - Cliquez sur **Installer**

2. **Voir votre base de données :**
   - Dans l'explorateur de fichiers de VS Code, cliquez sur le fichier **`database.db`**
   - La base s'ouvre automatiquement avec toutes les tables visibles
   - Cliquez sur la table **`users`** pour voir tous les utilisateurs
   - Vous pouvez voir l'ID, l'email, le mot de passe hashé, le statut admin, etc.

**Méthode 2 : DB Browser for SQLite (interface dédiée)**

1. **Télécharger le logiciel :**
   - Allez sur https://sqlitebrowser.org/
   - Téléchargez la version pour votre système (Windows/Mac/Linux)
   - Installez le logiciel

2. **Ouvrir votre base :**
   - Lancez "DB Browser for SQLite"
   - Cliquez sur "Ouvrir une base de données"
   - Sélectionnez le fichier **`database.db`** dans votre projet
   - Vous voyez maintenant toutes les tables et données avec une belle interface graphique

**Astuce :** Avec ces outils, vous pouvez voir en temps réel ce qui se passe dans votre base quand vous créez un utilisateur ou modifiez un profil !

---

## Prérequis

- PHP 7.4 ou supérieur
- Serveur web (Apache, Nginx, ou PHP built-in server)
- **Base de données** : SQLite (recommandé pour l'apprentissage) OU MySQL
- **Éditeur de code** : VS Code (recommandé) avec l'extension "SQLite Viewer"

## Installation

### ⚡ Option A : SQLite (RECOMMANDÉ pour les étudiants)

**Avantages :**
- ✅ Pas d'installation de MySQL nécessaire
- ✅ Pas de phpMyAdmin
- ✅ Configuration en 3 étapes simples
- ✅ Parfait pour se concentrer sur le code PHP

**Étapes :**

1. **Préparer les fichiers de connexion (renommage manuel) :**
   - **Renommez** `db.php` en `db_mysql.php` (clic droit → Renommer)
   - **Dupliquez** `db_sqlite.php` (clic droit → Copier → Coller)
   - **Renommez** la copie en `db.php` (clic droit → Renommer)

   **Important :** On GARDE `db_mysql.php` pour montrer la migration plus tard !

2. **Initialiser la base de données :**
   - Lancez le serveur PHP : `php -S localhost:8000`
   - Ouvrez votre navigateur : `http://localhost:8000/init_db.php`
   - La base de données sera créée automatiquement !

3. **C'est tout !** Allez sur `http://localhost:8000` et connectez-vous avec :
   - Email : `admin@example.com`
   - Mot de passe : `Admin123!`

---

### Option B : MySQL (optionnel)

**Pour ceux qui veulent utiliser MySQL :**

#### 1. Création de la base de données

Exécutez le script SQL suivant dans phpMyAdmin ou votre client MySQL :

```sql
-- Créer la base de données
CREATE DATABASE IF NOT EXISTS user_auth_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Utiliser la base de données
USE user_auth_db;

-- Créer la table users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    is_admin TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insérer un utilisateur admin par défaut (mot de passe: Admin123!)
INSERT INTO users (email, password, is_admin) VALUES
('admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);
```

#### 2. Configuration de la base de données

Assurez-vous que le fichier `db.php` (version MySQL) contient vos informations de connexion :

```php
$host = 'localhost';
$dbname = 'user_auth_db';
$username = 'root';
$password = '';
```

#### 3. Démarrage du serveur

Vous pouvez utiliser le serveur PHP intégré :

```bash
php -S localhost:8000
```

Puis accédez à `http://localhost:8000` dans votre navigateur.

---

## 🔄 Réinitialisation de la base de données (SQLite)

Pour repartir de zéro avec SQLite :

1. **Supprimer le fichier de base de données :**
   - Supprimez le fichier `database.db` (clic droit → Supprimer)

2. **Relancer l'initialisation :**
   - Allez sur `http://localhost:8000/init_db.php`
   - La base sera recréée avec l'admin par défaut

## 🛠️ Outils pour explorer la base SQLite

Voir la section **"Comment voir la base de données SQLite avec VS Code ?"** plus haut dans ce README pour les instructions détaillées.

**Rappel rapide :**
- **VS Code** : Extension "SQLite Viewer" → Cliquez sur `database.db`
- **DB Browser for SQLite** : Logiciel dédié disponible sur https://sqlitebrowser.org/

## Utilisation

### Compte administrateur par défaut

- **Email** : admin@example.com
- **Mot de passe** : Admin123!

### Règles de mot de passe

Les mots de passe doivent respecter les critères suivants :
- Minimum 8 caractères
- Au moins une lettre majuscule
- Au moins un caractère spécial (!@#$%^&*(),.?":{}|<>)

## 📊 Schéma de l'application

### Architecture globale

```
┌─────────────────────────────────────────────────────────────────┐
│                         NAVIGATEUR WEB                          │
│                     http://localhost:8000                       │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                      SERVEUR PHP (port 8000)                    │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌──────────────┐    ┌──────────────┐    ┌──────────────┐     │
│  │  Pages PHP   │◄───│  header.php  │───►│  Sessions    │     │
│  │              │    │  (navigation)│    │  PHP         │     │
│  └──────┬───────┘    └──────────────┘    └──────────────┘     │
│         │                                                       │
│         │  ┌────────────────────────────────────────────┐     │
│         └──┤ index.php      (Accueil)                   │     │
│            ├────────────────────────────────────────────┤     │
│            │ register.php   (Inscription)               │     │
│            ├────────────────────────────────────────────┤     │
│            │ login.php      (Connexion)                 │     │
│            ├────────────────────────────────────────────┤     │
│            │ logout.php     (Déconnexion)               │     │
│            ├────────────────────────────────────────────┤     │
│            │ profile.php    (Profil utilisateur)        │     │
│            ├────────────────────────────────────────────┤     │
│            │ admin.php      (Liste utilisateurs)        │     │
│            ├────────────────────────────────────────────┤     │
│            │ edit_user.php  (Éditer utilisateur)        │     │
│            ├────────────────────────────────────────────┤     │
│            │ delete_user.php(Supprimer utilisateur)     │     │
│            └───────────────┬────────────────────────────┘     │
│                            │                                   │
│                            ▼                                   │
│                   ┌─────────────────┐                          │
│                   │    db.php       │                          │
│                   │ (Connexion PDO) │                          │
│                   └────────┬────────┘                          │
└────────────────────────────┼────────────────────────────────────┘
                             │
                             ▼
                   ┌──────────────────┐
                   │  database.db     │
                   │  (SQLite)        │
                   │                  │
                   │  Table: users    │
                   │  - id            │
                   │  - email         │
                   │  - password      │
                   │  - is_admin      │
                   │  - created_at    │
                   └──────────────────┘
```

### Flux d'authentification

```
┌──────────────────────────────────────────────────────────────────┐
│                    CYCLE DE VIE D'UN UTILISATEUR                 │
└──────────────────────────────────────────────────────────────────┘

1️⃣  INSCRIPTION (register.php)
    ┌────────────────┐
    │  Utilisateur   │
    │  remplit le    │──► Validation email
    │  formulaire    │    (filter_var)
    └────────┬───────┘
             │
             ├──────────────► Validation mot de passe
             │                (8 cars, majuscule, spécial)
             │
             ├──────────────► Hash du mot de passe
             │                (password_hash)
             │
             └──────────────► INSERT dans la base
                              Redirection vers login.php

2️⃣  CONNEXION (login.php)
    ┌────────────────┐
    │  Utilisateur   │
    │  entre email/  │──► SELECT user FROM database
    │  mot de passe  │    WHERE email = ?
    └────────┬───────┘
             │
             ├──────────────► Vérification mot de passe
             │                (password_verify)
             │
             ├──────────────► Création session PHP
             │                $_SESSION['user_id']
             │                $_SESSION['email']
             │                $_SESSION['is_admin']
             │
             └──────────────► Redirection vers index.php

3️⃣  NAVIGATION (toutes les pages)
    ┌────────────────┐
    │  header.php    │
    │  vérifie si    │──► isset($_SESSION['user_id'])
    │  connecté      │    ├─► OUI : Affiche menu complet
    └────────────────┘    └─► NON : Affiche login/register

4️⃣  DÉCONNEXION (logout.php)
    ┌────────────────┐
    │  Utilisateur   │
    │  clique sur    │──► session_destroy()
    │  "Logout"      │
    └────────┬───────┘
             │
             └──────────────► Suppression $_SESSION
                              Redirection vers index.php
```

### Navigation de l'application

```
                        ┌─────────────────┐
                        │   index.php     │
                        │   (Accueil)     │
                        └────────┬────────┘
                                 │
                ┌────────────────┼────────────────┐
                │                                 │
                ▼                                 ▼
        ┌───────────────┐               ┌───────────────┐
        │ register.php  │               │  login.php    │
        │ (Inscription) │               │ (Connexion)   │
        └───────┬───────┘               └───────┬───────┘
                │                               │
                └───────────────┬───────────────┘
                                │
                                ▼
                    ┌──────────────────────┐
                    │  SESSION ACTIVE      │
                    │  Utilisateur loggé   │
                    └──────────┬───────────┘
                               │
                ┌──────────────┼──────────────┐
                │                             │
                ▼                             ▼
        ┌───────────────┐            ┌────────────────┐
        │  profile.php  │            │   is_admin?    │
        │  (Mon profil) │            └────────┬───────┘
        └───────────────┘                     │
                                              │ OUI
                                              ▼
                                    ┌──────────────────┐
                                    │   admin.php      │
                                    │ (Gestion users)  │
                                    └────────┬─────────┘
                                             │
                                ┌────────────┼────────────┐
                                │                         │
                                ▼                         ▼
                        ┌───────────────┐      ┌───────────────────┐
                        │ edit_user.php │      │ delete_user.php   │
                        │ (Modifier)    │      │ (Supprimer)       │
                        └───────────────┘      └───────────────────┘

        ┌─────────────────────────────────────────────────┐
        │  logout.php accessible depuis toutes les pages  │
        │         via le menu dans header.php             │
        └─────────────────────────────────────────────────┘
```

### Rôles et permissions

```
┌──────────────────────────────────────────────────────────────────┐
│                         UTILISATEUR NORMAL                        │
├──────────────────────────────────────────────────────────────────┤
│  ✅ Peut s'inscrire (register.php)                               │
│  ✅ Peut se connecter (login.php)                                │
│  ✅ Peut voir son profil (profile.php)                           │
│  ✅ Peut modifier SON email                                      │
│  ✅ Peut modifier SON mot de passe                               │
│  ✅ Peut se déconnecter (logout.php)                             │
│  ❌ Ne peut PAS accéder à admin.php                              │
│  ❌ Ne peut PAS modifier d'autres utilisateurs                   │
│  ❌ Ne peut PAS supprimer d'utilisateurs                         │
└──────────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────────────┐
│                         ADMINISTRATEUR                            │
├──────────────────────────────────────────────────────────────────┤
│  ✅ Toutes les permissions d'un utilisateur normal               │
│  ✅ PLUS : Accès au tableau de bord admin (admin.php)           │
│  ✅ PLUS : Voir la liste de TOUS les utilisateurs               │
│  ✅ PLUS : Modifier N'IMPORTE QUEL utilisateur (edit_user.php)  │
│  ✅ PLUS : Supprimer des utilisateurs (delete_user.php)         │
│  ✅ PLUS : Promouvoir/rétrograder le statut admin                │
│  ⚠️  Protection : Ne peut PAS se supprimer lui-même             │
└──────────────────────────────────────────────────────────────────┘
```

### Sécurité - Protections en place

```
┌─────────────────────────────────────────────────────────────────┐
│                    COUCHES DE SÉCURITÉ                          │
└─────────────────────────────────────────────────────────────────┘

🔒 NIVEAU 1 : VALIDATION DES DONNÉES
    ├─► Email : filter_var($email, FILTER_VALIDATE_EMAIL)
    ├─► Mot de passe : preg_match (8 cars, majuscule, spécial)
    └─► Nettoyage : trim() sur toutes les entrées

🔒 NIVEAU 2 : PROTECTION BASE DE DONNÉES
    ├─► Requêtes préparées PDO (prepare + execute)
    ├─► Pas de concaténation SQL directe
    └─► Protection contre les injections SQL

🔒 NIVEAU 3 : MOTS DE PASSE
    ├─► Hash : password_hash() avec BCRYPT
    ├─► Vérification : password_verify()
    └─► Jamais stockés en clair

🔒 NIVEAU 4 : SESSIONS
    ├─► Vérification sur chaque page protégée
    ├─► isset($_SESSION['user_id'])
    └─► Destruction complète au logout

🔒 NIVEAU 5 : CONTRÔLES ADMIN
    ├─► Vérification is_admin sur admin.php
    ├─► Double vérification sur edit_user.php
    ├─► Triple vérification sur delete_user.php
    └─► Protection auto-suppression admin

🔒 NIVEAU 6 : AFFICHAGE
    ├─► htmlspecialchars() pour éviter XSS
    └─► Protection contre l'injection de code HTML/JS
```

---

## 🎓 Guide pédagogique : Comprendre edit_user.php

Cette section explique **en détail** le fonctionnement du fichier `edit_user.php`, qui est souvent le plus difficile à comprendre pour les étudiants.

### 🔍 Concept clé : Récupérer un utilisateur avec $_GET

#### Étape 1 : Comprendre les paramètres GET dans l'URL

Quand vous cliquez sur "Edit" dans `admin.php`, vous êtes redirigé vers une URL comme :

```
http://localhost:8000/edit_user.php?id=5
                                      ↑
                              Paramètre GET
```

**Décomposition de l'URL :**
```
http://localhost:8000/edit_user.php  ?  id  =  5
         ↑                  ↑          ↑   ↑    ↑
      Domaine           Fichier    Séparateur Clé Valeur
```

**En PHP, vous récupérez cette valeur avec :**
```php
$_GET['id']  // Contient "5" (attention : c'est une STRING, pas un INT)
```

#### Étape 2 : Conversion en entier (IMPORTANT !)

```php
// ❌ MAUVAIS : $_GET['id'] est une string
$user_id = $_GET['id'];  // $user_id = "5" (string)

// ✅ BON : Conversion forcée en entier
$user_id = (int)$_GET['id'];  // $user_id = 5 (integer)
```

**Pourquoi convertir ?**

| Valeur dans l'URL | Sans (int) | Avec (int) |
|-------------------|------------|------------|
| `?id=5` | "5" (string) | 5 (integer) |
| `?id=5abc` | "5abc" (string) | 5 (integer) ⚠️ Sécurité ! |
| `?id=abc` | "abc" (string) | 0 (integer) |
| `?id=` | "" (string vide) | 0 (integer) |

**Sécurité bonus :** `(int)` supprime automatiquement les caractères invalides !

#### Étape 3 : Récupérer l'utilisateur dans la base de données

```php
// Préparer la requête
$stmt = $pdo->prepare("SELECT id, email, is_admin FROM users WHERE id = :id");

// Exécuter avec l'ID récupéré
$stmt->execute(['id' => $user_id]);

// Récupérer le résultat
$user = $stmt->fetch();
```

**Résultat : $user contient un tableau associatif**

```php
$user = [
    'id' => 5,
    'email' => 'john@example.com',
    'is_admin' => 0
];
```

**Vérification importante :**
```php
if (!$user) {
    // L'utilisateur n'existe pas (ID invalide ou supprimé)
    header('Location: admin.php');
    exit();
}
```

---

### 📝 Pré-remplir les champs du formulaire

#### Concept : Afficher les valeurs actuelles dans le formulaire

**Pour que l'admin voie les informations actuelles et puisse les modifier :**

```html
<!-- Champ email pré-rempli -->
<input type="email" name="email"
       value="<?php echo htmlspecialchars($user['email']); ?>">
```

**Exemple visuel :**

Si `$user['email'] = 'john@example.com'`, le HTML généré sera :
```html
<input type="email" name="email" value="john@example.com">
```

**Le navigateur affiche :**
```
┌────────────────────────────────────┐
│ Email: [john@example.com         ] │  ← Champ pré-rempli
└────────────────────────────────────┘
```

L'admin peut alors **modifier** cette valeur ou la **garder**.

---

### ☑️ Comprendre les checkbox (is_admin)

#### Comportement des checkbox HTML

```html
<!-- Checkbox cochée -->
<input type="checkbox" name="is_admin" checked>

<!-- Checkbox NON cochée -->
<input type="checkbox" name="is_admin">
```

**Différence CRUCIALE en PHP :**

| État de la checkbox | Lors de la soumission du formulaire |
|---------------------|-------------------------------------|
| ✅ Cochée | `isset($_POST['is_admin'])` = **TRUE** |
| ☐ NON cochée | `isset($_POST['is_admin'])` = **FALSE** |

**Code PHP pour récupérer la valeur :**
```php
// Si cochée : $is_admin = 1
// Si NON cochée : $is_admin = 0
$is_admin = isset($_POST['is_admin']) ? 1 : 0;
```

#### Pré-cocher la checkbox selon la valeur actuelle

**Objectif :** Si l'utilisateur est déjà admin, cocher la case. Sinon, la laisser décochée.

```php
<!-- Si is_admin == 1, ajouter "checked", sinon rien -->
<input type="checkbox" name="is_admin"
       <?php echo $user['is_admin'] == 1 ? 'checked' : ''; ?>>
```

**Exemples de rendu HTML :**

| Valeur de `$user['is_admin']` | HTML généré | Affichage |
|-------------------------------|-------------|-----------|
| `1` (admin) | `<input type="checkbox" name="is_admin" checked>` | ☑️ Cochée |
| `0` (pas admin) | `<input type="checkbox" name="is_admin">` | ☐ Décochée |

---

### 🔄 Flux complet : De l'affichage à la mise à jour

#### Scénario complet : Modifier l'utilisateur ID=5

```
ÉTAPE 1 : ADMIN CLIQUE SUR "EDIT" (admin.php)
┌──────────────────────────────────────────────┐
│ Liste des utilisateurs                       │
│                                              │
│ ID | Email              | Admin | Actions   │
│ 5  | john@example.com   | No    | [Edit]   │ ← Clic ici
└──────────────────────────────────────────────┘
        ↓
Lien : <a href="edit_user.php?id=5">Edit</a>


ÉTAPE 2 : CHARGEMENT DE edit_user.php?id=5
┌──────────────────────────────────────────────┐
│ 1. Récupération : $_GET['id'] = "5"         │
│ 2. Conversion : $user_id = (int)"5" = 5     │
│ 3. SELECT id, email, is_admin               │
│    FROM users WHERE id = 5                   │
│ 4. Résultat :                                │
│    $user = [                                 │
│        'id' => 5,                            │
│        'email' => 'john@example.com',        │
│        'is_admin' => 0                       │
│    ]                                         │
└──────────────────────────────────────────────┘
        ↓

ÉTAPE 3 : AFFICHAGE DU FORMULAIRE PRÉ-REMPLI
┌──────────────────────────────────────────────┐
│ Edit User                                    │
│                                              │
│ User ID: 5                                   │
│                                              │
│ Email: [john@example.com              ]     │
│        ↑ Pré-rempli avec $user['email']     │
│                                              │
│ ☐ Administrator                              │
│ ↑ Décochée car $user['is_admin'] = 0        │
│                                              │
│ New Password: [                    ]         │
│ (leave empty to keep current)                │
│                                              │
│ [Update User]                                │
└──────────────────────────────────────────────┘
        ↓

ÉTAPE 4 : ADMIN MODIFIE ET SOUMET
┌──────────────────────────────────────────────┐
│ Email: [john.doe@example.com          ]     │
│        ↑ Changé par l'admin                  │
│                                              │
│ ☑️ Administrator                              │
│ ↑ Coché par l'admin                          │
│                                              │
│ New Password: [NewPass123!            ]     │
│ ↑ Nouveau mot de passe                       │
│                                              │
│ [Update User] ← CLIC                         │
└──────────────────────────────────────────────┘
        ↓

ÉTAPE 5 : TRAITEMENT PHP ($_SERVER['REQUEST_METHOD'] === 'POST')
┌──────────────────────────────────────────────┐
│ RÉCUPÉRATION :                               │
│ $new_email = $_POST['email']                 │
│   → "john.doe@example.com"                   │
│                                              │
│ $is_admin = isset($_POST['is_admin']) ? 1:0  │
│   → 1 (car checkbox cochée)                  │
│                                              │
│ $new_password = $_POST['password']           │
│   → "NewPass123!"                            │
│                                              │
│ VALIDATION :                                 │
│ ✅ Email valide (filter_var)                 │
│ ✅ Email non utilisé par un autre user       │
│ ✅ Password >= 8 caractères                  │
│ ✅ Password contient majuscule               │
│ ✅ Password contient caractère spécial       │
│                                              │
│ PRÉPARATION DES MISES À JOUR :               │
│ $updates = [                                 │
│     'email' => 'john.doe@example.com',       │
│     'is_admin' => 1,                         │
│     'password' => '$2y$10$...' (hashé)       │
│ ]                                            │
└──────────────────────────────────────────────┘
        ↓

ÉTAPE 6 : CONSTRUCTION DYNAMIQUE DE LA REQUÊTE SQL
┌──────────────────────────────────────────────┐
│ BOUCLE sur $updates :                        │
│                                              │
│ foreach ($updates as $key => $value) {       │
│     $set_clause[] = "$key = :$key";          │
│ }                                            │
│                                              │
│ RÉSULTAT :                                   │
│ $set_clause = [                              │
│     "email = :email",                        │
│     "is_admin = :is_admin",                  │
│     "password = :password"                   │
│ ]                                            │
│                                              │
│ JOINTURE :                                   │
│ $set_string = implode(', ', $set_clause)     │
│   → "email = :email, is_admin = :is_admin,   │
│      password = :password"                   │
│                                              │
│ SQL FINAL :                                  │
│ UPDATE users SET                             │
│   email = :email,                            │
│   is_admin = :is_admin,                      │
│   password = :password                       │
│ WHERE id = :user_id                          │
└──────────────────────────────────────────────┘
        ↓

ÉTAPE 7 : EXÉCUTION
┌──────────────────────────────────────────────┐
│ $stmt = $pdo->prepare($sql);                 │
│                                              │
│ $updates['user_id'] = 5;                     │
│                                              │
│ $stmt->execute($updates);                    │
│   → [                                        │
│       'email' => 'john.doe@example.com',     │
│       'is_admin' => 1,                       │
│       'password' => '$2y$10$...',            │
│       'user_id' => 5                         │
│     ]                                        │
│                                              │
│ SUCCESS : "User updated successfully!"       │
└──────────────────────────────────────────────┘
        ↓

ÉTAPE 8 : AFFICHAGE DU MESSAGE
┌──────────────────────────────────────────────┐
│ ✅ User updated successfully!                │
│                                              │
│ User ID: 5                                   │
│                                              │
│ Email: [john.doe@example.com          ]     │
│        ↑ Nouvelle valeur affichée            │
│                                              │
│ ☑️ Administrator                              │
│ ↑ Maintenant coché                           │
└──────────────────────────────────────────────┘
```

---

### 🔐 Point crucial : Garder l'ID dans l'action du formulaire

**PROBLÈME si on oublie l'ID :**

```html
<!-- ❌ MAUVAIS : Pas d'ID dans l'action -->
<form method="POST" action="edit_user.php">
```

**Que se passe-t-il lors de la soumission ?**
```
1. Soumission POST vers edit_user.php (sans ?id=5)
2. $_GET['id'] n'existe plus !
3. Le code vérifie if (!isset($_GET['id']))
4. Redirection vers admin.php
5. ❌ ERREUR : Les modifications sont perdues !
```

**SOLUTION : Inclure l'ID dans l'action**

```html
<!-- ✅ BON : ID inclus dans l'action -->
<form method="POST" action="edit_user.php?id=<?php echo $user['id']; ?>">
```

**Résultat :**
```html
<form method="POST" action="edit_user.php?id=5">
```

**Lors de la soumission :**
```
1. POST vers edit_user.php?id=5
2. $_GET['id'] = "5" ✅
3. Le code peut traiter la mise à jour !
```

---

### 🆚 Différences clés : edit_user.php vs profile.php

| Critère | `profile.php` | `edit_user.php` |
|---------|---------------|-----------------|
| **Qui peut modifier ?** | L'utilisateur lui-même | Admin uniquement |
| **Quel utilisateur ?** | Utilisateur connecté | N'importe quel utilisateur |
| **ID récupéré via** | `$_SESSION['user_id']` | `$_GET['id']` |
| **Protection** | Session active | Session + is_admin = 1 |
| **Peut changer is_admin ?** | ❌ Non | ✅ Oui (checkbox) |
| **Email** | Optionnel | Obligatoire |
| **Mot de passe** | Optionnel | Optionnel |
| **WHERE clause SQL** | `WHERE id = $_SESSION['user_id']` | `WHERE id = $_GET['id']` |
| **Après succès** | Reste sur profile.php | Reste sur edit_user.php?id=X |

---

### 💡 Points difficiles pour les étudiants

#### 1. Pourquoi (int)$_GET['id'] ?

```php
// $_GET retourne TOUJOURS des strings !
$_GET['id'] = "5"  // string
$_GET['id'] = "5abc"  // string dangereuse !

// (int) force la conversion
(int)"5" = 5        // ✅ Sûr
(int)"5abc" = 5     // ✅ Caractères supprimés
(int)"abc" = 0      // ✅ Devient 0 (invalide)
```

#### 2. Checkbox : Pourquoi isset() ?

```php
// Quand la checkbox EST cochée
$_POST = ['is_admin' => 'on'];
isset($_POST['is_admin']) → TRUE → 1

// Quand la checkbox N'EST PAS cochée
$_POST = [];  // Clé 'is_admin' absente !
isset($_POST['is_admin']) → FALSE → 0
```

#### 3. Vérification email unique SAUF pour l'utilisateur actuel

```php
// ❌ MAUVAIS : Empêche de garder le même email
SELECT id FROM users WHERE email = :email

// ✅ BON : Autorise le même email pour l'utilisateur actuel
SELECT id FROM users WHERE email = :email AND id != :user_id
```

**Exemple :**
- Admin modifie l'utilisateur ID=5
- Email actuel : `john@example.com`
- Admin ne change PAS l'email, laisse `john@example.com`
- Avec `AND id != 5` : Aucun autre utilisateur n'a cet email → ✅ OK
- Sans `AND id != 5` : L'utilisateur 5 a déjà cet email → ❌ Erreur !

#### 4. Construction dynamique de la requête

**Pourquoi ne pas écrire :**
```php
UPDATE users SET email = :email, password = :password WHERE id = :user_id
```

**Problème :** Et si l'admin ne change PAS le mot de passe ?
- `$_POST['password']` est vide
- On ne doit PAS mettre à jour le password
- Mais la requête ci-dessus est fixe !

**Solution : Requête dynamique**
```php
$updates = [];

// Toujours mettre à jour l'email
$updates['email'] = $new_email;

// Mettre à jour le password SEULEMENT s'il est fourni
if (!empty($new_password)) {
    $updates['password'] = password_hash($new_password, PASSWORD_BCRYPT);
}

// Construire la clause SET dynamiquement
foreach ($updates as $key => $value) {
    $set_clause[] = "$key = :$key";
}

$set_string = implode(', ', $set_clause);
// Résultat : "email = :email" OU "email = :email, password = :password"
```

---

### ✅ Checklist pour comprendre edit_user.php

- [ ] Je comprends comment récupérer `$_GET['id']` depuis l'URL
- [ ] Je comprends pourquoi convertir en `(int)`
- [ ] Je sais récupérer l'utilisateur avec `SELECT ... WHERE id = :id`
- [ ] Je sais pré-remplir un champ avec `value="<?php echo $user['email']; ?>"`
- [ ] Je comprends le comportement des checkbox avec `isset($_POST['checkbox_name'])`
- [ ] Je sais pré-cocher une checkbox avec `<?php echo $condition ? 'checked' : ''; ?>`
- [ ] Je comprends pourquoi garder `?id=5` dans l'action du formulaire
- [ ] Je comprends la requête `WHERE email = :email AND id != :user_id`
- [ ] Je comprends la construction dynamique de la requête UPDATE
- [ ] Je connais les différences entre `edit_user.php` et `profile.php`

---

## Structure du projet

### 📦 Fichiers fournis (dans le dépôt Git)

```
user-auth-php/
├── db.php              # ⚙️ Connexion MySQL (VERSION ORIGINALE)
├── db_sqlite.php       # ⚙️ Connexion SQLite (à copier vers db.php)
├── init_db.php         # 🔧 Script d'initialisation SQLite
├── test_env.php        # 🔍 Test de l'environnement
├── header.php          # 🧭 Navigation et menu
├── index.php           # 🏠 Page d'accueil
├── register.php        # ✍️ Page d'inscription
├── login.php           # 🔑 Page de connexion
├── logout.php          # 🚪 Script de déconnexion
├── profile.php         # 👤 Page de profil utilisateur
├── admin.php           # 👨‍💼 Tableau de bord administrateur
├── edit_user.php       # ✏️ Édition d'un utilisateur (admin)
├── delete_user.php     # 🗑️ Suppression d'un utilisateur (admin)
├── README.md           # 📖 Ce fichier
├── QUICKSTART.md       # 🚀 Guide de démarrage rapide
└── .gitignore          # 🚫 Fichiers à ignorer
```

### 📝 Fichiers créés localement (par les étudiants)

```
user-auth-php/
├── db.php              # ✅ Copie de db_sqlite.php (créé à l'étape 1)
├── db_mysql.php        # 💾 Sauvegarde de l'original MySQL (créé à l'étape 1)
└── database.db         # 💾 Base de données SQLite (créé par init_db.php)
```

**Note :** Les fichiers créés localement sont ignorés par Git (voir `.gitignore`), donc chaque étudiant créera ses propres fichiers.

## Fonctionnalités

### Pour tous les utilisateurs
- S'inscrire avec email et mot de passe
- Se connecter
- Se déconnecter
- Modifier son email et mot de passe

### Pour les administrateurs
- Voir la liste de tous les utilisateurs
- Modifier les informations de n'importe quel utilisateur
- Supprimer des utilisateurs
- Changer le statut admin d'un utilisateur

## Sécurité

- Les mots de passe sont hashés avec `password_hash()` (BCRYPT)
- Utilisation de PDO avec requêtes préparées pour éviter les injections SQL
- Validation des données côté serveur
- Sessions PHP pour gérer l'authentification

## Notes pédagogiques

Ce projet est volontairement simple et ne contient pas de CSS. Chaque ligne de code est commentée en français pour faciliter l'apprentissage.

## 📚 SQLite vs MySQL : Quel choix pour l'apprentissage ?

### ✅ Pourquoi SQLite est recommandé pour les étudiants

| Critère | SQLite | MySQL |
|---------|--------|-------|
| **Installation** | Aucune (inclus avec PHP) | Installation de MySQL + phpMyAdmin |
| **Configuration** | 1 fichier à renommer | Configuration serveur, utilisateur, base |
| **Démarrage** | 1 clic sur init_db.php | Créer DB, table, insérer données manuellement |
| **Portabilité** | 1 fichier .db à copier | Export/import SQL complexe |
| **Réinitialisation** | Supprimer database.db | DROP DATABASE + recréer tout |
| **Débogage** | Visualiser avec DB Browser | Nécessite phpMyAdmin ou client MySQL |
| **Focus** | 100% sur le code PHP | 30% config DB + 70% code PHP |

### 🎯 Quand passer de SQLite à MySQL ?

**Restez sur SQLite tant que :**
- ✅ Vous apprenez PHP et les bases de données
- ✅ Vous avez moins de 100 000 enregistrements
- ✅ C'est un projet personnel ou de formation
- ✅ Vous testez des fonctionnalités

**Passez à MySQL quand :**
- 🚀 Vous déployez en production
- 🚀 Plusieurs utilisateurs simultanés (> 10)
- 🚀 Base de données > 1 Go
- 🚀 Besoin de fonctionnalités avancées (procédures stockées, triggers complexes)

### 🔄 Migration SQLite → MySQL (plus tard)

Le code PHP reste **IDENTIQUE** ! Seul le fichier `db.php` change.

**Quand vos étudiants voudront passer à MySQL :**

1. **Basculer sur la version MySQL (renommage manuel) :**
   - **Renommez** `db.php` en `db_sqlite_backup.php` (pour sauvegarder SQLite)
   - **Renommez** `db_mysql.php` en `db.php` (pour activer MySQL)

2. **Créer la base MySQL :**
   - Ouvrir phpMyAdmin ou votre client MySQL
   - Exécuter le script SQL fourni dans le README (section "Option B")

3. **C'est tout !** Le code PHP continue de fonctionner sans modification.

**Pour montrer la migration en cours :**
- Comparer `db_sqlite.php` et `db_mysql.php` côte à côte
- Montrer que seul le DSN change : `sqlite:database.db` vs `mysql:host=localhost`
- Le reste du code (register.php, login.php, etc.) reste identique !

## 🎓 Pour les formateurs

### 📋 Progression pédagogique recommandée

**Jour 1 : Mise en place (30 min)**
- Cloner le projet
- Exécuter les 3 commandes du QUICKSTART.md
- Se connecter et tester toutes les fonctionnalités
- ✅ Résultat : les étudiants ont un projet fonctionnel sans galère de config

**Jour 2-3 : Apprentissage du code PHP (6h)**
- Étudier les fichiers dans cet ordre :
  1. `db_sqlite.php` → Comprendre PDO et la connexion
  2. `init_db.php` → Voir comment créer une table SQL
  3. `register.php` → Validation complète, password_hash()
  4. `login.php` → password_verify(), sessions PHP
  5. `profile.php` → UPDATE SQL, requêtes dynamiques
  6. `admin.php` → fetchAll(), boucles foreach, tableaux HTML
  7. `edit_user.php` → Paramètres GET, checkbox, pré-remplissage
  8. `delete_user.php` → Protections multiples, redirections

**Jour 4 : Exercices pratiques**
- Ajouter un champ "nom" et "prénom" aux utilisateurs
- Ajouter une page "Forgot password"
- Créer une pagination pour la liste admin
- Ajouter un filtre de recherche

**Optionnel : Montrer la migration MySQL**
1. Comparer `db_sqlite.php` et l'original `db.php` (MySQL) côte à côte
2. Montrer que SEUL le DSN change
3. Basculer vers MySQL et montrer que tout fonctionne sans toucher au code

### 🎯 Points clés à souligner

**Sécurité :**
- ⚠️ JAMAIS `$_POST` directement dans SQL → Requêtes préparées
- ⚠️ JAMAIS stocker les mots de passe en clair → password_hash()
- ⚠️ TOUJOURS htmlspecialchars() pour l'affichage → Protection XSS

**Bonnes pratiques :**
- ✅ Validation côté serveur (même si validation HTML5)
- ✅ Messages d'erreur génériques (sécurité)
- ✅ Protection des pages (vérifier les sessions)
- ✅ try/catch pour les erreurs de base de données

### 📚 Fichiers les plus pédagogiques

| Fichier | Concepts enseignés |
|---------|-------------------|
| `db_sqlite.php` | PDO, connexion base de données, gestion d'erreurs try/catch |
| `init_db.php` | Création de tables SQL, INSERT, structure de base de données |
| `register.php` | Validation complète, filter_var(), preg_match(), password_hash() |
| `login.php` | password_verify(), sessions, redirections, sécurité |
| `profile.php` | Requêtes UPDATE dynamiques, validation optionnelle, sessions |
| `admin.php` | fetchAll(), boucles foreach, tableaux HTML, vérifications admin |
| `edit_user.php` | Paramètres GET, checkbox, pré-remplissage de formulaire |
| `delete_user.php` | Protections en cascade, gestion d'erreurs, sécurité admin |
| `header.php` | Include PHP, navigation dynamique, sessions |
| `logout.php` | Destruction de session, session_destroy(), redirections |

---

## 📝 Licence

Ce projet est fourni à des fins pédagogiques uniquement. Libre d'utilisation pour l'apprentissage et l'enseignement.

## 🤝 Contribution

Les suggestions et améliorations sont les bienvenues ! N'hésitez pas à :
- Signaler des bugs
- Proposer des améliorations pédagogiques
- Ajouter des exercices pratiques

## 📧 Support

Pour toute question, contactez votre formateur ou consultez les commentaires détaillés dans chaque fichier PHP.

---

**Bon apprentissage ! 🚀**
