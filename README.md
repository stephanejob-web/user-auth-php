# 🔐 Système d'Authentification PHP

> **Projet pédagogique complet pour apprendre l'authentification en PHP**
> **SQLite + PDO + Sessions + Sécurité**

---

## 📁 STRUCTURE DU PROJET

```
user-auth-php/
│
├── 📄 PAGES PRINCIPALES (à la racine)
│   ├── index.php              # Page d'accueil
│   ├── register.php           # Inscription
│   ├── login.php              # Connexion
│   ├── logout.php             # Déconnexion
│   ├── profile.php            # Profil utilisateur
│   ├── header.php             # Menu de navigation
│   │
│   ├── admin.php              # Liste des utilisateurs (admin)
│   ├── edit_user.php          # Modifier un utilisateur (admin)
│   ├── delete_user.php        # Supprimer un utilisateur (admin)
│   └── toggle_admin.php       # Basculer statut admin (admin)
│
├── ⚙️ config/                  # Configuration
│   ├── db_sqlite.php          # Connexion SQLite (original)
│   ├── db_mysql.php           # Connexion MySQL (alternative)
│   └── init_db.php            # Initialisation de la base
│
├── 💾 database/                # Base de données
│   └── database.db            # Fichier SQLite (créé par init_db.php)
│
├── 🎨 assets/                  # Ressources statiques
│   └── css/
│       └── style.css          # Feuille de style
│
├── 📚 docs/                    # Documentation
│   ├── README.md              # Guide principal (ce fichier)
│   ├── QUICKSTART.md          # Démarrage rapide
│   ├── COURS-DETAILLE-AUTHENTIFICATION.md
│   └── cours/                 # Cours détaillés
│       ├── 00-INDEX.md
│       ├── 01-introduction-et-architecture.md
│       ├── 02-base-de-donnees.md
│       └── REFERENCE-RAPIDE.md
│
├── 🧪 tests/                   # Fichiers de test
│   ├── debug.php
│   ├── test_env.php
│   └── test_simple.php
│
└── 🔧 db.php                   # Connexion active (copie de db_sqlite.php)
```

---

## 🚀 DÉMARRAGE RAPIDE (5 minutes)

### 1. Initialiser la base de données

```bash
# Méthode 1 : Via le navigateur
http://localhost:8000/config/init_db.php

# Méthode 2 : Via la ligne de commande
php config/init_db.php
```

✅ Cela créera :
- Le fichier `database/database.db`
- La table `users`
- Un compte admin par défaut

### 2. Se connecter

**Identifiants admin par défaut :**
- Email : `admin@example.com`
- Mot de passe : `Admin123!`

⚠️ **Important :** Changez ces identifiants en production !

### 3. Tester

```
1. Ouvrir http://localhost:8000
2. Cliquer sur "Register" pour créer un compte
3. Se connecter avec le nouveau compte
4. Tester le profil (modifier email/mot de passe)
5. Se connecter en admin pour accéder au panneau admin
```

---

## 📖 DOCUMENTATION

### Pour les débutants

1. **[docs/QUICKSTART.md](docs/QUICKSTART.md)** - Démarrage rapide en 5 minutes
2. **[docs/cours/00-INDEX.md](docs/cours/00-INDEX.md)** - Table des matières des cours
3. **[docs/cours/01-introduction-et-architecture.md](docs/cours/01-introduction-et-architecture.md)** - Comprendre le projet
4. **[docs/cours/02-base-de-donnees.md](docs/cours/02-base-de-donnees.md)** - Base de données et PDO

### Pour référence

- **[docs/cours/REFERENCE-RAPIDE.md](docs/cours/REFERENCE-RAPIDE.md)** - Aide-mémoire complet
- **[docs/COURS-DETAILLE-AUTHENTIFICATION.md](docs/COURS-DETAILLE-AUTHENTIFICATION.md)** - Cours original très détaillé

---

## 🎯 FONCTIONNALITÉS

### Pour tous les utilisateurs

- ✅ **Inscription** avec validation complète
- ✅ **Connexion** sécurisée
- ✅ **Déconnexion**
- ✅ **Modification de profil** (email et mot de passe)

### Pour les administrateurs

- ✅ **Liste de tous les utilisateurs**
- ✅ **Modifier n'importe quel utilisateur**
- ✅ **Promouvoir/rétrograder** un utilisateur en admin
- ✅ **Supprimer** un utilisateur (avec protections)

---

## 🔒 SÉCURITÉ

Ce projet implémente les **bonnes pratiques de sécurité** :

### ✅ Protection des mots de passe
- Hachage avec `password_hash()` (BCRYPT)
- Vérification avec `password_verify()`
- Jamais stockés en clair

### ✅ Protection contre les injections SQL
- Requêtes préparées PDO
- Paramètres liés (bound parameters)
- Aucune concaténation directe

### ✅ Protection contre XSS
- `htmlspecialchars()` sur toutes les sorties
- Échappement systématique des données utilisateur

### ✅ Protection des pages
- Vérification de session pour les pages utilisateur
- Double vérification (utilisateur + admin) pour les pages admin
- Redirection automatique si non autorisé

### ✅ Protection des opérations critiques
- Un admin ne peut pas se supprimer lui-même
- Vérification d'existence avant suppression
- Messages d'erreur vagues (sécurité par l'obscurité)

---

## 🗄️ BASE DE DONNÉES

### Structure de la table `users`

```sql
CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    is_admin INTEGER DEFAULT 0,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP
);
```

### Colonnes

| Colonne | Type | Description |
|---------|------|-------------|
| `id` | INTEGER | Identifiant unique auto-incrémenté |
| `email` | TEXT | Email de l'utilisateur (unique) |
| `password` | TEXT | Mot de passe hashé (BCRYPT) |
| `is_admin` | INTEGER | 0 = utilisateur, 1 = admin |
| `created_at` | TEXT | Date de création du compte |

---

## 🛠️ TECHNOLOGIES UTILISÉES

- **PHP 7.4+** : Langage serveur
- **SQLite 3** : Base de données (fichier local)
- **PDO** : Interface de base de données
- **HTML5** : Structure des pages
- **CSS3** : Mise en forme
- **Sessions PHP** : Gestion de connexion

---

## 📝 PAGES DU PROJET

### Pages publiques (accessibles à tous)

| Fichier | Rôle | Concepts clés |
|---------|------|---------------|
| `index.php` | Page d'accueil | Affichage conditionnel, sessions |
| `register.php` | Inscription | Validation, `password_hash()`, INSERT |
| `login.php` | Connexion | `password_verify()`, sessions, SELECT |

### Pages protégées (utilisateur connecté)

| Fichier | Rôle | Concepts clés |
|---------|------|---------------|
| `profile.php` | Modification profil | UPDATE dynamique, validation |
| `logout.php` | Déconnexion | `session_destroy()`, redirection |

### Pages admin (administrateur uniquement)

| Fichier | Rôle | Concepts clés |
|---------|------|---------------|
| `admin.php` | Liste utilisateurs | `fetchAll()`, foreach, tableaux |
| `edit_user.php` | Modifier utilisateur | Paramètres GET, checkbox, UPDATE |
| `delete_user.php` | Supprimer utilisateur | DELETE, protections multiples |
| `toggle_admin.php` | Basculer statut | UPDATE ciblé |

### Fichiers de configuration

| Fichier | Rôle |
|---------|------|
| `db.php` | Connexion PDO active (copie de db_sqlite.php) |
| `config/db_sqlite.php` | Connexion SQLite (original) |
| `config/db_mysql.php` | Connexion MySQL (alternative) |
| `config/init_db.php` | Création de la base et de l'admin |
| `header.php` | Menu de navigation et début HTML |

---

## 🔄 RÉINITIALISER LA BASE DE DONNÉES

### Méthode 1 : Supprimer et recréer

```bash
# 1. Supprimer la base
rm database/database.db

# 2. Recréer
php config/init_db.php
# OU ouvrir http://localhost:8000/config/init_db.php
```

### Méthode 2 : init_db.php le fait automatiquement

Le script `config/init_db.php` supprime automatiquement l'ancienne base avant de recréer.

---

## 🎓 POUR LES ÉTUDIANTS

### Parcours d'apprentissage recommandé

1. **Semaine 1 : Fondations**
   - Lire [docs/cours/00-INDEX.md](docs/cours/00-INDEX.md)
   - Lire [docs/cours/01-introduction-et-architecture.md](docs/cours/01-introduction-et-architecture.md)
   - Lire [docs/cours/02-base-de-donnees.md](docs/cours/02-base-de-donnees.md)
   - Tester l'initialisation de la base

2. **Semaine 2 : Authentification**
   - Lire `register.php` ligne par ligne (commentaires inclus)
   - Lire `login.php` ligne par ligne
   - Lire `logout.php` ligne par ligne
   - Tester l'inscription et la connexion

3. **Semaine 3 : Gestion utilisateur**
   - Lire `profile.php` ligne par ligne
   - Lire `admin.php` ligne par ligne
   - Lire `edit_user.php` ligne par ligne
   - Lire `delete_user.php` ligne par ligne
   - Tester toutes les fonctionnalités

4. **Semaine 4 : Projet personnel**
   - Recréer le projet de zéro
   - Ajouter des fonctionnalités personnalisées
   - Expérimenter et comprendre en profondeur

### Chaque fichier PHP est ultra-commenté !

Tous les fichiers PHP contiennent des **commentaires détaillés** qui expliquent :
- 📝 Chaque ligne de code
- 💡 Pourquoi on fait comme ça
- ⚠️ Les erreurs courantes à éviter
- ✅ Les bonnes pratiques

**Exemple :** `register.php` contient plus de 200 lignes de commentaires pédagogiques !

---

## 🔧 CONFIGURATION

### Changer de SQLite à MySQL

1. **Copier le bon fichier de connexion**
   ```bash
   cp config/db_mysql.php db.php
   ```

2. **Éditer db.php avec vos paramètres**
   ```php
   $host = 'localhost';
   $dbname = 'user_auth_db';
   $username = 'root';
   $password = 'votre_mot_de_passe';
   ```

3. **Créer la base MySQL manuellement**
   - Ouvrir phpMyAdmin
   - Créer une base `user_auth_db`
   - Exécuter le schéma SQL (voir `config/db_mysql.php` commentaires)

---

## 🐛 DÉBOGAGE

### Erreur : "unable to open database file"

**Cause :** Permissions insuffisantes

**Solution :**
```bash
chmod 755 /path/to/user-auth-php
chmod 755 /path/to/user-auth-php/database
```

### Erreur : "SQLSTATE[HY000]: General error: 1 no such table: users"

**Cause :** La table n'a pas été créée

**Solution :**
```bash
php config/init_db.php
```

### Le CSS ne s'affiche pas

**Cause :** Mauvais chemin vers style.css

**Solution :** Vérifier que `header.php` pointe vers `assets/css/style.css`

---

## 📊 STATISTIQUES DU PROJET

- **13 fichiers PHP** à la racine (pages)
- **3 fichiers de config** (db et init)
- **500+ lignes de commentaires** pédagogiques
- **4 fichiers de cours** détaillés
- **1 guide de référence** rapide
- **100% sécurisé** avec les bonnes pratiques

---

## 💬 FAQ

<details>
<summary><strong>Pourquoi SQLite et pas MySQL ?</strong></summary>

SQLite est **parfait pour apprendre** car :
- ✅ Aucune installation nécessaire
- ✅ Base de données = 1 fichier
- ✅ Facile à réinitialiser
- ✅ Même syntaxe SQL que MySQL (à 95%)

MySQL est mieux pour la **production** mais plus complexe à configurer.

</details>

<details>
<summary><strong>Le projet est-il prêt pour la production ?</strong></summary>

**Non**, ce projet est **pédagogique**. Pour la production, ajoutez :
- HTTPS obligatoire
- Protection CSRF
- Rate limiting
- Validation côté client JavaScript
- Logs de sécurité
- Double authentification (2FA)

</details>

<details>
<summary><strong>Comment changer le mot de passe admin par défaut ?</strong></summary>

1. Se connecter en admin
2. Aller sur le panneau admin
3. Cliquer sur "Edit" pour l'admin
4. Changer le mot de passe
5. Sauvegarder

</details>

---

## 📄 LICENCE

Ce projet est à but pédagogique. Utilisez-le librement pour apprendre et enseigner !

---

## 🎉 CRÉDITS

Projet créé pour l'apprentissage de l'authentification PHP avec SQLite, PDO et les bonnes pratiques de sécurité.

**Bon apprentissage ! 🚀**
