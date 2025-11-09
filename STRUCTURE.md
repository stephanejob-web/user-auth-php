# 📁 STRUCTURE DU PROJET - Vue Détaillée

## 🗂️ Organisation des fichiers

```
user-auth-php/
│
├── 📄 FICHIERS À LA RACINE (Pages principales)
│   │
│   ├── index.php              # Page d'accueil
│   │   └─→ Affiche un message différent si connecté ou non
│   │
│   ├── register.php           # Inscription d'un nouvel utilisateur
│   │   └─→ Validation complète + password_hash() + INSERT
│   │
│   ├── login.php              # Connexion utilisateur
│   │   └─→ password_verify() + création session
│   │
│   ├── logout.php             # Déconnexion
│   │   └─→ session_destroy() + redirection
│   │
│   ├── profile.php            # Modification du profil
│   │   └─→ UPDATE email et/ou mot de passe (utilisateur connecté)
│   │
│   ├── admin.php              # Liste de tous les utilisateurs
│   │   └─→ fetchAll() + foreach (admin uniquement)
│   │
│   ├── edit_user.php          # Modifier n'importe quel utilisateur
│   │   └─→ Paramètres GET + UPDATE (admin uniquement)
│   │
│   ├── delete_user.php        # Supprimer un utilisateur
│   │   └─→ DELETE avec protections (admin uniquement)
│   │
│   ├── toggle_admin.php       # Basculer le statut admin
│   │   └─→ UPDATE is_admin (admin uniquement)
│   │
│   ├── header.php             # Menu de navigation + début HTML
│   │   └─→ Navigation conditionnelle selon connexion
│   │
│   ├── db.php                 # Connexion PDO active
│   │   └─→ Copie de config/db_sqlite.php
│   │
│   ├── README.md              # Ce fichier : guide principal
│   └── STRUCTURE.md           # Fichier actuel : structure détaillée
│
├── ⚙️ config/                  # CONFIGURATION
│   │
│   ├── db_sqlite.php          # Connexion SQLite (original)
│   │   └─→ PDO + SQLite + configuration
│   │
│   ├── db_mysql.php           # Connexion MySQL (alternative)
│   │   └─→ PDO + MySQL + configuration
│   │
│   └── init_db.php            # Initialisation de la base
│       └─→ CREATE TABLE + INSERT admin par défaut
│
├── 💾 database/                # BASE DE DONNÉES
│   │
│   └── database.db            # Fichier SQLite (créé par init_db.php)
│       └─→ Contient la table 'users'
│
├── 🎨 assets/                  # RESSOURCES STATIQUES
│   │
│   └── css/
│       └── style.css          # Feuille de style CSS
│           └─→ Mise en forme de toutes les pages
│
├── 📚 docs/                    # DOCUMENTATION
│   │
│   ├── README.md              # Guide principal (copie de la racine)
│   │
│   ├── QUICKSTART.md          # Démarrage rapide en 5 minutes
│   │   └─→ Installation et premiers tests
│   │
│   ├── COURS-DETAILLE-AUTHENTIFICATION.md
│   │   └─→ Cours original très détaillé (1 fichier)
│   │
│   └── cours/                 # Cours structurés
│       │
│       ├── 00-INDEX.md        # Table des matières des cours
│       │   └─→ Parcours d'apprentissage complet
│       │
│       ├── 01-introduction-et-architecture.md
│       │   └─→ Vue d'ensemble + architecture + concepts
│       │
│       ├── 02-base-de-donnees.md
│       │   └─→ db.php et init_db.php ligne par ligne
│       │
│       └── REFERENCE-RAPIDE.md
│           └─→ Aide-mémoire complet (tous les fichiers)
│
└── 🧪 tests/                   # FICHIERS DE TEST
    │
    ├── debug.php              # Débogage général
    ├── test_env.php           # Test de l'environnement PHP
    └── test_simple.php        # Tests simples
```

---

## 🔗 DÉPENDANCES ENTRE FICHIERS

### Fichiers inclus par tous

```
TOUTES LES PAGES
    ↓ require_once
    db.php (connexion PDO)

TOUTES LES PAGES (sauf logout.php)
    ↓ include
    header.php (navigation)
        ↓ <link>
        assets/css/style.css
```

### Flux de navigation

```
UTILISATEUR NON CONNECTÉ
    │
    ├─→ index.php (page d'accueil)
    │
    ├─→ register.php (créer un compte)
    │   └─→ INSERT dans users
    │
    └─→ login.php (se connecter)
        └─→ SELECT + password_verify()
        └─→ Création session
        └─→ Redirection vers index.php

UTILISATEUR CONNECTÉ
    │
    ├─→ index.php (message "Hello, email!")
    │
    ├─→ profile.php (modifier son profil)
    │   └─→ UPDATE ses propres données
    │
    └─→ logout.php (se déconnecter)
        └─→ session_destroy()
        └─→ Redirection vers index.php

ADMINISTRATEUR
    │
    ├─→ admin.php (liste tous les utilisateurs)
    │   └─→ SELECT * FROM users + fetchAll()
    │
    ├─→ edit_user.php?id=X (modifier un utilisateur)
    │   └─→ SELECT user WHERE id=X
    │   └─→ UPDATE user SET ...
    │
    ├─→ delete_user.php?id=X (supprimer un utilisateur)
    │   └─→ DELETE FROM users WHERE id=X
    │
    └─→ toggle_admin.php?id=X (basculer statut admin)
        └─→ UPDATE users SET is_admin = ...
```

---

## 📊 TYPES DE FICHIERS

### Pages PHP (13 fichiers)

| Type | Fichiers | Protection |
|------|----------|------------|
| **Publiques** | index.php, register.php, login.php | Aucune |
| **Utilisateur** | profile.php, logout.php | Session requise |
| **Admin** | admin.php, edit_user.php, delete_user.php, toggle_admin.php | Session + is_admin=1 |
| **Commun** | header.php | Inclus partout |
| **Config** | db.php, config/init_db.php | Inclus par d'autres |

### Documentation (7 fichiers)

| Fichier | Rôle | Public cible |
|---------|------|--------------|
| README.md | Guide principal | Tous |
| STRUCTURE.md | Structure du projet | Tous |
| docs/QUICKSTART.md | Démarrage rapide | Débutants |
| docs/COURS-DETAILLE-AUTHENTIFICATION.md | Cours complet (1 fichier) | Étudiants |
| docs/cours/00-INDEX.md | Index des cours | Étudiants |
| docs/cours/01-*.md | Cours structurés | Étudiants |
| docs/cours/REFERENCE-RAPIDE.md | Aide-mémoire | Tous |

---

## 🎯 POINTS D'ENTRÉE

### Pour l'utilisateur final

```
1. http://localhost:8000/
   → index.php (page d'accueil)

2. http://localhost:8000/register.php
   → Inscription

3. http://localhost:8000/login.php
   → Connexion
```

### Pour l'installation

```
1. http://localhost:8000/config/init_db.php
   → Initialisation de la base de données
   → Création de l'admin par défaut
```

### Pour l'apprentissage

```
1. docs/cours/00-INDEX.md
   → Table des matières

2. docs/QUICKSTART.md
   → Démarrage rapide

3. Les fichiers PHP eux-mêmes
   → Ultra-commentés (500+ lignes de commentaires)
```

---

## 📏 TAILLE DES FICHIERS (approximative)

| Fichier | Lignes | Commentaires | Code |
|---------|--------|--------------|------|
| register.php | 512 | 200+ | 310 |
| login.php | 390 | 150+ | 240 |
| admin.php | 534 | 200+ | 334 |
| profile.php | 483 | 150+ | 333 |
| edit_user.php | 507 | 150+ | 357 |
| delete_user.php | 347 | 100+ | 247 |
| db_sqlite.php | 246 | 100+ | 146 |
| init_db.php | 300 | 120+ | 180 |

**Total : ~3500 lignes dont 1100+ lignes de commentaires pédagogiques**

---

## 🔑 FICHIERS CLÉS PAR CONCEPT

### Apprentissage des sessions

```
login.php:175-184      # Création de session
logout.php:63-88       # Destruction de session
header.php:26-43       # Gestion de session
profile.php:35-45      # Protection de page
admin.php:42-50        # Protection admin
```

### Apprentissage de PDO

```
db.php:62              # Connexion PDO
register.php:298       # INSERT avec requête préparée
login.php:93           # SELECT avec requête préparée
profile.php:230-270    # UPDATE dynamique
delete_user.php:184    # DELETE avec requête préparée
admin.php:104          # fetchAll()
```

### Apprentissage de la sécurité

```
register.php:280       # password_hash()
login.php:142          # password_verify()
index.php:89           # htmlspecialchars()
delete_user.php:112    # Protection anti-auto-suppression
edit_user.php:81       # Conversion (int) pour sécurité
```

---

## 🎓 POUR LES ENSEIGNANTS

### Ordre de présentation recommandé

1. **Semaine 1 : Architecture**
   - STRUCTURE.md (ce fichier)
   - docs/cours/01-introduction-et-architecture.md
   - Schéma de navigation

2. **Semaine 2 : Base de données**
   - config/init_db.php (création de la base)
   - db.php (connexion PDO)
   - docs/cours/02-base-de-donnees.md

3. **Semaine 3 : Authentification**
   - register.php (inscription)
   - login.php (connexion)
   - logout.php (déconnexion)

4. **Semaine 4 : Gestion utilisateur**
   - profile.php (modification)
   - admin.php (liste)
   - edit_user.php (édition)
   - delete_user.php (suppression)

---

## 🔄 WORKFLOW TYPIQUE

### Développement

```bash
# 1. Initialiser la base
php config/init_db.php

# 2. Lancer le serveur
php -S localhost:8000

# 3. Développer
# Modifier les fichiers PHP

# 4. Tester
# Ouvrir http://localhost:8000

# 5. Réinitialiser si besoin
rm database/database.db
php config/init_db.php
```

### Apprentissage

```bash
# 1. Lire la documentation
docs/cours/00-INDEX.md

# 2. Initialiser
php config/init_db.php

# 3. Tester les fonctionnalités
http://localhost:8000

# 4. Lire le code source
# Tous les fichiers .php avec leurs commentaires

# 5. Expérimenter
# Modifier, casser, réparer !
```

---

**Cette structure est conçue pour être simple, organisée et pédagogique ! 🎓**
