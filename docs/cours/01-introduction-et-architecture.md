# 📚 COURS 01 : INTRODUCTION ET ARCHITECTURE DU PROJET

> **Pour débutants complets - Système d'authentification PHP**
> **Objectif :** Comprendre la structure globale et l'architecture du projet
> **Durée estimée :** 2 heures

---

## 🎯 OBJECTIFS DE CE CHAPITRE

À la fin de ce chapitre, vous serez capable de :
- ✅ Comprendre ce qu'est un système d'authentification
- ✅ Expliquer l'architecture globale du projet
- ✅ Identifier le rôle de chaque fichier
- ✅ Comprendre le flux de navigation entre les pages
- ✅ Connaître les principes de sécurité appliqués

---

## 📖 TABLE DES MATIÈRES

1. [Qu'est-ce qu'un système d'authentification ?](#1-quest-ce-quun-système-dauthentification)
2. [Vue d'ensemble de notre projet](#2-vue-densemble-de-notre-projet)
3. [Architecture des fichiers](#3-architecture-des-fichiers)
4. [Flux de navigation](#4-flux-de-navigation)
5. [Concepts clés à maîtriser](#5-concepts-clés-à-maîtriser)
6. [Exercices de compréhension](#6-exercices-de-compréhension)

---

## 1. QU'EST-CE QU'UN SYSTÈME D'AUTHENTIFICATION ?

### 🤔 Définition simple

Un **système d'authentification** permet de :
1. **Identifier** qui vous êtes (avec un email)
2. **Vérifier** que c'est bien vous (avec un mot de passe)
3. **Se souvenir** de vous quand vous naviguez sur le site (avec des sessions)

### 📊 Analogie de la vie réelle

```
┌─────────────────────────────────────────────────────────┐
│              IMMEUBLE SÉCURISÉ                          │
├─────────────────────────────────────────────────────────┤
│                                                         │
│  1. INSCRIPTION (Register)                              │
│     → Vous demandez un badge d'accès                    │
│     → On vérifie votre identité                         │
│     → On vous donne un badge unique                     │
│                                                         │
│  2. CONNEXION (Login)                                   │
│     → Vous montrez votre badge à l'entrée               │
│     → Le garde vérifie que c'est le bon badge           │
│     → Vous entrez dans l'immeuble                       │
│                                                         │
│  3. NAVIGATION (Session)                                │
│     → Votre badge reste actif pendant votre visite      │
│     → Vous pouvez accéder aux différents étages         │
│     → Le système se souvient de qui vous êtes           │
│                                                         │
│  4. DÉCONNEXION (Logout)                                │
│     → Vous rendez votre badge en sortant                │
│     → Le badge est désactivé                            │
│     → Vous devrez vous réidentifier pour revenir        │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

**Notre projet PHP fait exactement la même chose !**

---

## 2. VUE D'ENSEMBLE DE NOTRE PROJET

### 🎨 Schéma global

```
┌──────────────────────────────────────────────────────────────┐
│                    UTILISATEUR NON CONNECTÉ                   │
└──────────────────┬───────────────────────────────────────────┘
                   │
        ┌──────────┴──────────┐
        │                     │
        ▼                     ▼
┌──────────────┐      ┌──────────────┐
│  REGISTER    │      │    LOGIN     │
│  (S'inscrire)│      │ (Se connecter)│
└──────┬───────┘      └──────┬───────┘
       │                     │
       └──────────┬──────────┘
                  │
                  ▼
    ┌─────────────────────────────┐
    │   UTILISATEUR CONNECTÉ      │
    └─────────────┬───────────────┘
                  │
       ┌──────────┼──────────┐
       │          │          │
       ▼          ▼          ▼
   ┌──────┐  ┌──────┐  ┌────────┐
   │INDEX │  │PROFILE│  │LOGOUT │
   │      │  │      │  │        │
   └──────┘  └──────┘  └────────┘
       │
       │ (si admin)
       ▼
   ┌─────────────────┐
   │   ADMIN PANEL   │
   ├─────────────────┤
   │ • Voir users    │
   │ • Modifier user │
   │ • Supprimer user│
   └─────────────────┘
```

### 🎭 Les 2 types d'utilisateurs

| Type | Caractéristiques | Pages accessibles |
|------|------------------|-------------------|
| **Utilisateur normal** | `is_admin = 0` | index, profile, logout |
| **Administrateur** | `is_admin = 1` | index, profile, logout, **admin**, **edit_user**, **delete_user** |

---

## 3. ARCHITECTURE DES FICHIERS

### 📁 Structure complète du projet

```
user-auth-php/
│
├── 🔧 FICHIERS DE BASE
│   ├── db.php                 # Connexion à la base de données
│   ├── db_sqlite.php          # Version SQLite (alternative)
│   ├── db_mysql.php           # Version MySQL (originale)
│   ├── init_db.php            # Création de la base de données
│   └── database.db            # Fichier de la base SQLite
│
├── 🎨 PRÉSENTATION
│   ├── header.php             # En-tête et menu de navigation
│   └── style.css              # Feuille de style CSS
│
├── 👤 PAGES PUBLIQUES (accessibles à tous)
│   ├── index.php              # Page d'accueil
│   ├── register.php           # Inscription
│   └── login.php              # Connexion
│
├── 🔐 PAGES PROTÉGÉES (utilisateur connecté requis)
│   ├── profile.php            # Modification du profil
│   └── logout.php             # Déconnexion
│
├── 👨‍💼 PAGES ADMIN (admin requis)
│   ├── admin.php              # Liste de tous les utilisateurs
│   ├── edit_user.php          # Modifier un utilisateur
│   ├── delete_user.php        # Supprimer un utilisateur
│   └── toggle_admin.php       # Basculer le statut admin
│
└── 📚 DOCUMENTATION
    ├── README.md              # Guide général
    ├── QUICKSTART.md          # Démarrage rapide
    └── cours/                 # Dossier des cours détaillés
        ├── 00-INDEX.md
        ├── 01-introduction-et-architecture.md (ce fichier)
        ├── 02-base-de-donnees.md
        └── ...
```

### 🔍 Rôle détaillé de chaque fichier

#### 🔧 Fichiers de base

| Fichier | Rôle | Utilisé par |
|---------|------|-------------|
| `db.php` | Se connecter à la base de données avec PDO | TOUS les autres fichiers |
| `db_sqlite.php` | Version SQLite de la connexion | Copié vers db.php en mode SQLite |
| `init_db.php` | Créer les tables et l'admin par défaut | Exécuté UNE FOIS au début |
| `database.db` | Base de données SQLite (1 fichier) | Créé par init_db.php |

#### 🎨 Fichiers de présentation

| Fichier | Rôle | Lignes importantes |
|---------|------|-------------------|
| `header.php` | Menu de navigation dynamique + début HTML | header.php:1-227 |
| `style.css` | Mise en forme CSS | Référencé dans header.php |

#### 👤 Pages publiques

| Fichier | Rôle | Protection |
|---------|------|-----------|
| `index.php` | Page d'accueil | ❌ Aucune (accessible à tous) |
| `register.php` | Créer un nouveau compte | ❌ Aucune |
| `login.php` | Se connecter | ❌ Aucune |

#### 🔐 Pages protégées

| Fichier | Rôle | Protection |
|---------|------|-----------|
| `profile.php` | Modifier SON profil | ✅ Utilisateur connecté |
| `logout.php` | Se déconnecter | ✅ Utilisateur connecté |

#### 👨‍💼 Pages admin

| Fichier | Rôle | Protection |
|---------|------|-----------|
| `admin.php` | Voir tous les utilisateurs | ✅ Admin uniquement |
| `edit_user.php` | Modifier N'IMPORTE QUEL utilisateur | ✅ Admin uniquement |
| `delete_user.php` | Supprimer un utilisateur | ✅ Admin + protections |
| `toggle_admin.php` | Donner/retirer les droits admin | ✅ Admin uniquement |

---

## 4. FLUX DE NAVIGATION

### 🔄 Parcours d'un nouvel utilisateur

```
ÉTAPE 1 : DÉCOUVERTE
┌───────────────────────────────────────────┐
│  index.php (non connecté)                 │
│  → Affiche "Please login or register"    │
│  → Menu : Home | Register | Login         │
└───────────────┬───────────────────────────┘
                │
                ▼
ÉTAPE 2 : INSCRIPTION
┌───────────────────────────────────────────┐
│  register.php                             │
│  → Formulaire : email + password          │
│  → Validation complète                    │
│  → Hash du mot de passe                   │
│  → INSERT dans la base                    │
│  → Succès : "You can now login"           │
└───────────────┬───────────────────────────┘
                │
                ▼
ÉTAPE 3 : CONNEXION
┌───────────────────────────────────────────┐
│  login.php                                │
│  → Formulaire : email + password          │
│  → Vérification avec password_verify()    │
│  → Création de la session                 │
│  → $_SESSION['user_id'] = ...             │
│  → Redirection vers index.php             │
└───────────────┬───────────────────────────┘
                │
                ▼
ÉTAPE 4 : NAVIGATION CONNECTÉE
┌───────────────────────────────────────────┐
│  index.php (connecté)                     │
│  → Affiche "Hello, email!"                │
│  → Menu : Home | Profile | Logout         │
│  → (+ Admin si is_admin = 1)              │
└───────────────┬───────────────────────────┘
                │
       ┌────────┼────────┐
       │        │        │
       ▼        ▼        ▼
   ┌──────┐ ┌──────┐ ┌──────┐
   │PROFILE│ │ADMIN │ │LOGOUT│
   └──────┘ └──────┘ └──────┘
```

### 🔒 Système de protection des pages

```
┌──────────────────────────────────────────────────────┐
│  PAGE PROTÉGÉE (ex: profile.php, admin.php)         │
├──────────────────────────────────────────────────────┤
│                                                      │
│  1. session_start()                                  │
│     ↓                                                │
│  2. Vérifier isset($_SESSION['user_id'])             │
│     ├─ OUI → Continuer                               │
│     └─ NON → header('Location: login.php')           │
│                exit()                                │
│                                                      │
│  3. (Si page admin) Vérifier is_admin == 1           │
│     ├─ OUI → Afficher la page                        │
│     └─ NON → header('Location: index.php')           │
│                exit()                                │
│                                                      │
└──────────────────────────────────────────────────────┘
```

#### Exemple de code de protection

**Protection utilisateur connecté (profile.php:35-45) :**
```php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
// L'utilisateur est connecté, on peut continuer
```

**Protection admin (admin.php:42-50) :**
```php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header('Location: index.php');
    exit();
}
// L'utilisateur est admin, on peut afficher la page
```

---

## 5. CONCEPTS CLÉS À MAÎTRISER

### 🔐 1. Les sessions PHP

#### Qu'est-ce qu'une session ?

Une **session** est un mécanisme pour stocker des informations côté serveur entre les différentes pages.

**Analogie :**
```
Sans session (impossible) :
Page 1 : "Bonjour, je m'appelle Jean"
Page 2 : "Qui êtes-vous ?"  ← Le serveur a oublié !

Avec session :
Page 1 : "Bonjour, je m'appelle Jean"
         $_SESSION['nom'] = 'Jean'
Page 2 : "Bonjour $_SESSION['nom'] !"  ← Le serveur se souvient !
         → "Bonjour Jean !"
```

#### Comment ça marche ?

```
1. session_start()
   └→ PHP crée un cookie PHPSESSID dans le navigateur
   └→ PHP crée un fichier temporaire sur le serveur

2. $_SESSION['clé'] = 'valeur'
   └→ PHP stocke les données dans le fichier serveur

3. Sur une autre page :
   session_start()
   echo $_SESSION['clé'];
   └→ PHP retrouve les données grâce au cookie
```

#### Dans notre projet

```php
// Dans login.php (après authentification réussie)
$_SESSION['user_id'] = 5;
$_SESSION['email'] = 'user@example.com';
$_SESSION['is_admin'] = 0;

// Dans n'importe quelle autre page
if (isset($_SESSION['user_id'])) {
    echo "Utilisateur connecté : " . $_SESSION['email'];
}
```

---

### 🔒 2. Le hachage de mot de passe

#### Pourquoi JAMAIS en clair ?

```
❌ DANGEREUX : Stockage en clair
┌────┬─────────────┬──────────┐
│ id │ email       │ password │
├────┼─────────────┼──────────┤
│ 1  │ user@ex.com │ Test123! │  ← VISIBLE PAR TOUS !
└────┴─────────────┴──────────┘

Si un pirate vole la base :
→ Il a TOUS les mots de passe
→ Il peut se connecter partout
→ Les utilisateurs utilisent souvent le même mot de passe partout

✅ SÉCURISÉ : Stockage hashé
┌────┬─────────────┬──────────────────────────────────────┐
│ id │ email       │ password                             │
├────┼─────────────┼──────────────────────────────────────┤
│ 1  │ user@ex.com │ $2y$10$abcdefghijklmnop...           │
└────┴─────────────┴──────────────────────────────────────┘

Si un pirate vole la base :
→ Il ne peut PAS retrouver les mots de passe
→ Le hash est irréversible
```

#### Comment ça marche ?

```php
// À L'INSCRIPTION (register.php:280)
$password = "Test123!";
$hash = password_hash($password, PASSWORD_BCRYPT);
// $hash = "$2y$10$abcdefghijklmnop..."
// On stocke $hash dans la base

// À LA CONNEXION (login.php:142)
$password_saisi = "Test123!";
$hash_en_base = "$2y$10$abcdefghijklmnop...";

if (password_verify($password_saisi, $hash_en_base)) {
    echo "Mot de passe correct !";
} else {
    echo "Mot de passe incorrect !";
}
```

**Caractéristiques du hash :**
- Irréversible (impossible de retrouver le mot de passe original)
- Unique (même mot de passe = hash différent à chaque fois grâce au "salt")
- Vérifiable (password_verify peut vérifier sans déchiffrer)

---

### 🛡️ 3. Les requêtes préparées PDO

#### Pourquoi c'est VITAL ?

**Injection SQL = hack le plus courant !**

```php
// ❌ DANGEREUX : Concaténation directe
$email = $_POST['email'];
$sql = "SELECT * FROM users WHERE email = '$email'";
$result = $pdo->query($sql);

// Si un pirate entre : email = "test' OR '1'='1"
// La requête devient :
// SELECT * FROM users WHERE email = 'test' OR '1'='1'
// → Retourne TOUS les utilisateurs !

// ✅ SÉCURISÉ : Requête préparée
$email = $_POST['email'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
$stmt->execute(['email' => $email]);
$result = $stmt->fetch();

// Même si un pirate entre : email = "test' OR '1'='1"
// PDO échappe automatiquement les caractères dangereux
// La requête cherche littéralement l'email "test' OR '1'='1"
// → Aucun utilisateur trouvé
```

#### Syntaxe dans notre projet

```php
// Étape 1 : Préparer avec des placeholders (:nom)
$stmt = $pdo->prepare("
    SELECT id, email, password
    FROM users
    WHERE email = :email
");

// Étape 2 : Exécuter en remplaçant les placeholders
$stmt->execute(['email' => $email]);

// Étape 3 : Récupérer les résultats
$user = $stmt->fetch();  // Une ligne
// OU
$users = $stmt->fetchAll();  // Toutes les lignes
```

---

### 🎨 4. Navigation dynamique avec header.php

Le fichier `header.php` est **inclus dans toutes les pages** avec `include 'header.php';`

Il affiche un menu différent selon l'état de l'utilisateur :

```php
// header.php:127-197
if (isset($_SESSION['user_id'])) {
    // UTILISATEUR CONNECTÉ
    echo '<a href="profile.php">Profile</a>';
    echo '<a href="logout.php">Logout</a>';

    if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) {
        // UTILISATEUR ADMIN
        echo '<a href="admin.php">Admin</a>';
    }
} else {
    // VISITEUR NON CONNECTÉ
    echo '<a href="register.php">Register</a>';
    echo '<a href="login.php">Login</a>';
}
```

**Résultat :**

| État | Menu affiché |
|------|--------------|
| Non connecté | Home \| Register \| Login |
| Utilisateur normal | Home \| Profile \| Logout |
| Administrateur | Home \| Profile \| **Admin** \| Logout |

---

## 6. EXERCICES DE COMPRÉHENSION

### ✍️ Exercice 1 : Identifier le bon fichier

Pour chaque action, indiquez quel fichier PHP est responsable :

1. Créer un nouveau compte utilisateur → **`_______________.php`**
2. Vérifier l'email et le mot de passe → **`_______________.php`**
3. Modifier mon propre email → **`_______________.php`**
4. Voir la liste de tous les utilisateurs → **`_______________.php`**
5. Supprimer un utilisateur → **`_______________.php`**
6. Détruire la session → **`_______________.php`**

<details>
<summary>📖 Voir les réponses</summary>

1. `register.php`
2. `login.php`
3. `profile.php`
4. `admin.php`
5. `delete_user.php`
6. `logout.php`

</details>

---

### ✍️ Exercice 2 : Vrai ou Faux

1. ⬜ Un utilisateur peut accéder à `admin.php` sans être admin
2. ⬜ Les mots de passe sont stockés en clair dans la base de données
3. ⬜ `header.php` doit être inclus dans CHAQUE page
4. ⬜ Les sessions permettent de se souvenir de l'utilisateur entre les pages
5. ⬜ `password_hash()` génère toujours le même hash pour le même mot de passe
6. ⬜ `$_SESSION['user_id']` existe même si l'utilisateur n'est pas connecté

<details>
<summary>📖 Voir les réponses</summary>

1. ❌ FAUX - admin.php vérifie `is_admin == 1`
2. ❌ FAUX - Ils sont hashés avec `password_hash()`
3. ✅ VRAI - Pour avoir le menu de navigation
4. ✅ VRAI - C'est le rôle principal des sessions
5. ❌ FAUX - Le hash change à chaque fois (salt aléatoire)
6. ❌ FAUX - La variable n'existe que si on l'a créée dans `login.php`

</details>

---

### ✍️ Exercice 3 : Compléter le code

Complétez le code de protection d'une page admin :

```php
session_start();

// Vérifier que l'utilisateur est connecté ET admin
if (!________($_SESSION['user_id']) || $_SESSION['_______'] != 1) {
    header('Location: ___________.php');
    _______();
}

// L'utilisateur est admin, on peut continuer
```

<details>
<summary>📖 Voir la réponse</summary>

```php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header('Location: index.php');
    exit();
}
```

</details>

---

### ✍️ Exercice 4 : Schéma à compléter

Complétez le flux d'authentification :

```
1. Utilisateur entre email + mot de passe
   ↓
2. PHP cherche l'utilisateur avec ___________()
   ↓
3. PHP vérifie le mot de passe avec ___________()
   ↓ (si correct)
4. PHP crée une __________ avec session_start()
   ↓
5. PHP stocke les infos dans __________['user_id']
   ↓
6. Redirection vers __________.php
```

<details>
<summary>📖 Voir la réponse</summary>

```
1. Utilisateur entre email + mot de passe
   ↓
2. PHP cherche l'utilisateur avec prepare() + execute()
   ↓
3. PHP vérifie le mot de passe avec password_verify()
   ↓ (si correct)
4. PHP crée une session avec session_start()
   ↓
5. PHP stocke les infos dans $_SESSION['user_id']
   ↓
6. Redirection vers index.php
```

</details>

---

## 🎓 RÉCAPITULATIF

### Ce que vous devez retenir

| Concept | Pourquoi c'est important |
|---------|--------------------------|
| **Sessions PHP** | Pour se souvenir de l'utilisateur entre les pages |
| **Hachage de mot de passe** | Pour protéger les mots de passe en cas de vol de la base |
| **Requêtes préparées** | Pour éviter les injections SQL (hack le plus courant) |
| **Protections de pages** | Pour empêcher l'accès non autorisé aux pages sensibles |
| **header.php** | Pour avoir un menu cohérent sur toutes les pages |

---

## 📚 PROCHAINE ÉTAPE

Dans le **Chapitre 02 - Base de données**, vous apprendrez :
- Comment créer une base de données SQLite
- La structure de la table `users`
- Comment se connecter avec PDO
- Les différences entre SQLite et MySQL
- Chaque ligne de code de `db.php` et `init_db.php`

➡️ **[Passer au chapitre 02-base-de-donnees.md](02-base-de-donnees.md)**

---

## 💬 QUESTIONS FRÉQUENTES

<details>
<summary><strong>Pourquoi PHP et pas JavaScript / Python / Java ?</strong></summary>

PHP est idéal pour apprendre l'authentification car :
- ✅ Serveur web intégré (pas besoin d'Apache/Nginx pour débuter)
- ✅ Syntaxe simple pour les formulaires HTML
- ✅ Sessions natives (pas besoin de bibliothèque externe)
- ✅ PDO intégré pour les bases de données
- ✅ Très utilisé dans le monde professionnel (WordPress, Laravel, Symfony)

</details>

<details>
<summary><strong>Est-ce que ce code est prêt pour la production ?</strong></summary>

**Non**, ce projet est pédagogique. Pour la production, il faudrait ajouter :
- HTTPS obligatoire
- Protection CSRF
- Rate limiting (limite de tentatives de connexion)
- Validation côté client JavaScript
- Logs de sécurité
- Double authentification (2FA)
- Et bien d'autres protections...

</details>

<details>
<summary><strong>Pourquoi SQLite et pas MySQL ?</strong></summary>

SQLite est **parfait pour apprendre** car :
- ✅ Aucune installation nécessaire
- ✅ Base de données = 1 fichier
- ✅ Facile à réinitialiser (supprimer le fichier)
- ✅ Même syntaxe SQL que MySQL (à 95%)

MySQL est mieux pour la **production** mais plus complexe à configurer.

Le code PHP reste **identique**, seul `db.php` change !

</details>

---

**🎉 Félicitations ! Vous avez terminé le chapitre 01 !**

Vous avez maintenant une vue d'ensemble complète du projet. Dans le prochain chapitre, nous allons plonger dans le code avec `db.php` et `init_db.php`.
