# 📚 COURS ULTRA DÉTAILLÉ : CRÉER UN SYSTÈME D'AUTHENTIFICATION PHP

> **Pour débutants complets - Reconversion professionnelle**
> **Objectif :** Comprendre CHAQUE ligne de code que vous écrivez
> **Durée :** 12-15 heures (prenez votre temps !)

---

## 🎯 COMMENT UTILISER CE COURS

### ⚠️ RÈGLES IMPORTANTES

1. **NE COPIEZ-COLLEZ PAS** le code sans le lire
2. **TAPEZ** chaque ligne vous-même (ça aide à mémoriser)
3. **LISEZ** toutes les explications avant de coder
4. **TESTEZ** après chaque étape
5. **POSEZ-VOUS** les questions : "Pourquoi cette ligne ?"

### 📖 Légende du cours

```
💡 = Conseil important
⚠️ = Attention, erreur fréquente
🔍 = Explication approfondie
✍️ = Exercice pratique
🧪 = Test à faire
❓ = Question de réflexion
```

---

## TABLE DES MATIÈRES

1. [Préparation de l'environnement](#chapitre-1-préparation)
2. [Comprendre ce qu'on va créer](#chapitre-2-comprendre)
3. [Créer la base de données - Étape par étape](#chapitre-3-base-de-données)
4. [Le fichier de connexion - Ligne par ligne](#chapitre-4-connexion)
5. [Le header - Construire le menu](#chapitre-5-header)
6. [Page d'inscription - Partie 1 : Le formulaire](#chapitre-6-inscription-1)
7. [Page d'inscription - Partie 2 : La validation](#chapitre-7-inscription-2)
8. [Page de connexion - Comprendre les sessions](#chapitre-8-connexion)
9. [Et la suite...](#suite)

---

# CHAPITRE 1 : PRÉPARATION DE L'ENVIRONNEMENT

## 🎯 Objectif du chapitre

À la fin de ce chapitre, vous aurez :
- ✅ Un serveur PHP qui fonctionne
- ✅ Un éditeur de code installé
- ✅ Un dossier de projet créé
- ✅ Compris comment tester votre code

---

## ÉTAPE 1.1 : Installer un serveur local

### 🤔 C'est quoi un "serveur local" ?

**Analogie simple :**

Imaginez que vous voulez cuisiner :
- **Sans cuisine équipée** : Impossible de cuisiner chez vous
- **Avec cuisine équipée** : Vous pouvez cuisiner chez vous

Le serveur local, c'est **la cuisine pour coder** :
- **Sans serveur** : Votre navigateur ne peut pas lire le PHP
- **Avec serveur** : Votre navigateur peut afficher vos pages PHP

### 📥 Quel logiciel installer ?

Je recommande **MAMP** (Mac) ou **XAMPP** (Windows/Mac/Linux) :

**MAMP :**
- Site : https://www.mamp.info
- Gratuit
- Simple à utiliser
- Interface visuelle

**XAMPP :**
- Site : https://www.apachefriends.org
- Gratuit
- Plus populaire
- Fonctionne partout

### 🔧 Installation de MAMP (exemple)

1. Téléchargez MAMP depuis le site officiel
2. Ouvrez le fichier téléchargé
3. Suivez l'assistant d'installation (Suivant → Suivant → Installer)
4. Lancez MAMP
5. Cliquez sur "Start Servers"
6. Vous devriez voir deux feux verts 🟢 (Apache et MySQL)

### 🧪 TEST : Vérifier que ça marche

1. Ouvrez votre navigateur
2. Tapez dans la barre d'adresse : `http://localhost`
3. Vous devriez voir une page MAMP ou XAMPP

**❓ Ça ne marche pas ?**
- Vérifiez que les serveurs sont démarrés (feux verts)
- Essayez `http://localhost:8888` (MAMP utilise parfois ce port)

---

## ÉTAPE 1.2 : Installer un éditeur de code

### 🤔 Pourquoi pas le Bloc-notes ?

**Analogie :**
- **Bloc-notes** = Écrire avec un crayon sur du papier
- **Éditeur de code** = Écrire avec un stylo magique qui :
  - Colore votre texte
  - Détecte les erreurs
  - Auto-complète vos mots

### 📥 Télécharger Visual Studio Code (VSCode)

1. Allez sur : https://code.visualstudio.com
2. Cliquez sur "Download"
3. Installez le logiciel
4. Lancez VSCode

### 🔧 Extensions utiles (optionnel)

Dans VSCode, cliquez sur l'icône "Extensions" (à gauche) et installez :
- **PHP Intelephense** : Auto-complétion pour PHP
- **SQLite Viewer** : Voir votre base de données

---

## ÉTAPE 1.3 : Créer le dossier du projet

### 📁 Où créer le dossier ?

**IMPORTANT :** Le dossier DOIT être dans le dossier web de votre serveur !

**Pour MAMP :**
```
/Applications/MAMP/htdocs/
```

**Pour XAMPP :**
```
Windows : C:\xampp\htdocs\
Mac/Linux : /opt/lampp/htdocs/
```

### ✍️ Créer le dossier

**Méthode 1 : Avec l'explorateur de fichiers**

1. Ouvrez le dossier `htdocs`
2. Clic droit → Nouveau dossier
3. Nommez-le : `user-auth-php`

**Méthode 2 : Avec VSCode**

1. Ouvrez VSCode
2. Fichier → Ouvrir un dossier
3. Naviguez vers `htdocs`
4. Créez un nouveau dossier `user-auth-php`

### 🧪 TEST : Créer un fichier de test

1. Dans le dossier `user-auth-php`, créez un fichier `test.php`
2. Écrivez dedans :

```php
<?php
echo "Hello, PHP fonctionne !";
?>
```

3. Ouvrez votre navigateur
4. Allez sur : `http://localhost/user-auth-php/test.php`
5. Vous devriez voir : `Hello, PHP fonctionne !`

**✅ Si ça marche : Bravo, votre environnement est prêt !**
**❌ Si ça ne marche pas : Vérifiez que vous êtes dans le bon dossier**

---

# CHAPITRE 2 : COMPRENDRE CE QU'ON VA CRÉER

## 🎯 Vue d'ensemble du projet

Avant de coder, il faut **comprendre** ce qu'on va construire.

### 📊 Schéma général

```
┌─────────────────────────────────────────────────────────────┐
│                    NOTRE SYSTÈME                             │
└─────────────────────────────────────────────────────────────┘

VISITEUR NON CONNECTÉ
    ↓
[Page d'accueil] → Peut voir le contenu public
    ↓
[S'inscrire] → Crée un compte
    ↓
[Se connecter] → Entre email + mot de passe
    ↓
UTILISATEUR CONNECTÉ
    ↓
[Profil] → Peut modifier ses infos
    ↓
[Se déconnecter] → Retour visiteur

ADMINISTRATEUR (utilisateur spécial)
    ↓
Peut faire TOUT ce qu'un utilisateur fait
    +
[Panneau Admin] → Voir tous les utilisateurs
    ↓
[Modifier utilisateur] → Changer email, rôle, mot de passe
    ↓
[Supprimer utilisateur] → Retirer quelqu'un
```

---

## 🗂️ Les fichiers qu'on va créer

### Fichiers de base (infrastructure)

| Fichier | Rôle | Analogie |
|---------|------|----------|
| `db.php` | Se connecter à la base de données | La clé de votre coffre-fort |
| `init_db.php` | Créer la base de données | Construire le coffre-fort |
| `header.php` | Menu de navigation | L'en-tête de toutes vos pages |
| `style.css` | Rendre joli | La décoration de votre maison |

### Fichiers utilisateur (fonctionnalités publiques)

| Fichier | Rôle | Ce qu'il fait |
|---------|------|---------------|
| `index.php` | Page d'accueil | Première page que les gens voient |
| `register.php` | Inscription | Créer un nouveau compte |
| `login.php` | Connexion | Se connecter avec email/mot de passe |
| `logout.php` | Déconnexion | Se déconnecter |
| `profile.php` | Profil | Modifier son propre compte |

### Fichiers admin (réservés aux administrateurs)

| Fichier | Rôle | Ce qu'il fait |
|---------|------|---------------|
| `admin.php` | Panneau admin | Voir tous les utilisateurs |
| `edit_user.php` | Éditer | Modifier n'importe quel utilisateur |
| `delete_user.php` | Supprimer | Retirer un utilisateur |
| `toggle_admin.php` | Promouvoir/rétrograder | Changer le rôle en 1 clic |

---

## 🔑 Concepts clés à comprendre AVANT de coder

### CONCEPT 1 : La base de données

#### 🤔 C'est quoi ?

Une base de données, c'est comme un **classeur Excel géant** qui stocke vos données.

**Comparaison :**

```
EXCEL :
┌─────┬──────────────────┬─────────┬────────┐
│ ID  │ Email            │ Admin   │ Date   │
├─────┼──────────────────┼─────────┼────────┤
│ 1   │ admin@ex.com     │ Oui     │ 01/01  │
│ 2   │ user@ex.com      │ Non     │ 02/01  │
└─────┴──────────────────┴─────────┴────────┘

BASE DE DONNÉES :
Table "users"
┌─────┬──────────────────┬──────────┬────────────────┐
│ id  │ email            │ is_admin │ created_at     │
├─────┼──────────────────┼──────────┼────────────────┤
│ 1   │ admin@ex.com     │ 1        │ 2024-01-01...  │
│ 2   │ user@ex.com      │ 0        │ 2024-01-02...  │
└─────┴──────────────────┴──────────┴────────────────┘
```

#### 🎯 Pourquoi on en a besoin ?

Sans base de données :
- ❌ On ne peut pas stocker les comptes utilisateurs
- ❌ Tout est perdu quand on ferme le navigateur
- ❌ Impossible de se connecter

Avec base de données :
- ✅ Les comptes sont sauvegardés
- ✅ On peut se connecter/déconnecter
- ✅ Les données persistent

---

### CONCEPT 2 : Les sessions PHP

#### 🤔 C'est quoi une session ?

**Analogie du badge d'accès :**

Imaginez un immeuble de bureaux :

```
SANS BADGE (sans session) :
Vous arrivez → "Qui êtes-vous ?"
Vous allez au 2e étage → "Qui êtes-vous ?" (on vous re-demande !)
Vous allez au 3e étage → "Qui êtes-vous ?" (encore !)
= TRÈS PÉNIBLE !

AVEC BADGE (avec session) :
Vous arrivez → Vous montrez votre badge une fois
Vous allez au 2e étage → Vous montrez votre badge (reconnu)
Vous allez au 3e étage → Vous montrez votre badge (toujours reconnu)
= PRATIQUE !
```

#### 🎯 Comment ça marche en PHP ?

```
1. CONNEXION (login.php)
   Utilisateur entre : email + mot de passe
   → Si correct : PHP crée une SESSION
   → SESSION = comme donner un badge

2. NAVIGATION (index.php, profile.php, etc.)
   PHP vérifie : "Vous avez un badge ?"
   → Si oui : "OK, entrez"
   → Si non : "Allez vous connecter d'abord"

3. DÉCONNEXION (logout.php)
   PHP détruit la session
   → C'est comme rendre son badge
```

#### 💻 En code PHP, ça donne quoi ?

```php
// CRÉER une session (lors de la connexion)
session_start();
$_SESSION['user_id'] = 5;
$_SESSION['email'] = 'user@example.com';

// VÉRIFIER une session (sur les pages protégées)
if (isset($_SESSION['user_id'])) {
    echo "Vous êtes connecté !";
} else {
    echo "Vous devez vous connecter";
}

// DÉTRUIRE une session (lors de la déconnexion)
session_destroy();
```

---

### CONCEPT 3 : Le hachage de mot de passe

#### ⚠️ RÈGLE D'OR : JAMAIS de mot de passe en clair !

**MAUVAIS (DANGEREUX) :**

```
Table users :
┌────┬─────────────┬──────────┐
│ id │ email       │ password │
├────┼─────────────┼──────────┤
│ 1  │ user@ex.com │ Test123! │  ← ON VOIT LE MOT DE PASSE !
└────┴─────────────┴──────────┘

Si quelqu'un vole la base :
= Il a TOUS les mots de passe ! 💀
```

**BON (SÉCURISÉ) :**

```
Table users :
┌────┬─────────────┬─────────────────────────────────────────┐
│ id │ email       │ password                                │
├────┼─────────────┼─────────────────────────────────────────┤
│ 1  │ user@ex.com │ $2y$10$abcdefghijklmnop... (60 caract.) │
└────┴─────────────┴─────────────────────────────────────────┘

Si quelqu'un vole la base :
= Impossible de retrouver le mot de passe ! ✅
```

#### 🔍 Comment ça marche ?

**Le hachage est une transformation IRRÉVERSIBLE :**

```
MOT DE PASSE : "Test123!"
        ↓
    [HACHAGE]
        ↓
HASH : "$2y$10$abcdefghijklmnop..."

⚠️ IMPOSSIBLE de revenir en arrière :
   "$2y$10$abcdefg..." → ??? (on ne peut pas retrouver "Test123!")
```

#### 💻 En code PHP :

```php
// LORS DE L'INSCRIPTION : Hasher le mot de passe
$mot_de_passe = "Test123!";
$hash = password_hash($mot_de_passe, PASSWORD_BCRYPT);
// $hash = "$2y$10$abcdefg..."
// On stocke $hash dans la base

// LORS DE LA CONNEXION : Vérifier le mot de passe
$mot_de_passe_saisi = "Test123!";
$hash_en_base = "$2y$10$abcdefg...";

if (password_verify($mot_de_passe_saisi, $hash_en_base)) {
    echo "Mot de passe correct !";
} else {
    echo "Mot de passe incorrect !";
}
```

#### ❓ Question : Comment PHP sait si c'est bon alors ?

**Réponse :**

`password_verify()` re-hache le mot de passe saisi et compare avec le hash stocké :

```
Utilisateur saisit : "Test123!"
    ↓
password_verify() le hache : "$2y$10$abcdefg..."
    ↓
Compare avec la base : "$2y$10$abcdefg..."
    ↓
SI IDENTIQUE : Mot de passe correct ✅
SI DIFFÉRENT : Mot de passe incorrect ❌
```

---

# CHAPITRE 3 : CRÉER LA BASE DE DONNÉES

## 🎯 Objectif du chapitre

Créer une base de données SQLite avec une table "users" pour stocker nos utilisateurs.

---

## ÉTAPE 3.1 : Comprendre ce qu'est SQLite

### 🤔 C'est quoi SQLite ?

**Comparaison :**

```
MySQL / PostgreSQL :
= Grand serveur de base de données
= Comme un entrepôt géant
= Besoin de configuration
= Utilisé par les gros sites

SQLite :
= Base de données dans un fichier
= Comme un classeur portable
= Aucune configuration
= Parfait pour apprendre et petits projets
```

### 📁 Comment ça fonctionne ?

```
Projet normal (MySQL) :
┌───────────┐         ┌──────────────┐
│ Votre PHP │ ←────→  │ Serveur MySQL│
└───────────┘         └──────────────┘
                      (serveur séparé)

Projet SQLite :
┌───────────┐         ┌──────────────┐
│ Votre PHP │ ←────→  │ database.db  │
└───────────┘         └──────────────┘
                      (simple fichier)
```

**Avantage :** Un seul fichier `database.db` contient TOUTE votre base !

---

## ÉTAPE 3.2 : Créer le fichier de connexion `db.php`

### 📋 Ce qu'on va faire

1. Créer un fichier `db.php`
2. Écrire le code pour se connecter à la base
3. Tester que ça marche

### ✍️ Créer le fichier

1. Dans VSCode, clic droit sur `user-auth-php` → Nouveau fichier
2. Nommez-le : `db.php`

### 📝 Écrire le code LIGNE PAR LIGNE

Je vais vous expliquer **chaque ligne** avant que vous la tapiez.

---

#### LIGNE 1 : Balise d'ouverture PHP

```php
<?php
```

**🔍 Explication :**
- `<?php` dit à PHP : "Commence à lire du code PHP ici"
- Toujours au début d'un fichier PHP
- Pas besoin de `?>` à la fin (c'est une bonne pratique de ne pas le mettre)

---

#### LIGNES 2-8 : Commentaire d'explication

```php
/**
 * CONNEXION À LA BASE DE DONNÉES
 *
 * Ce fichier sera inclus dans toutes les pages qui ont besoin
 * d'accéder à la base de données.
 */
```

**🔍 Explication :**
- Les commentaires commencent par `/**` et finissent par `*/`
- PHP ne lit PAS ces lignes (c'est juste pour nous)
- Ça explique à quoi sert le fichier

**💡 Conseil :** Prenez l'habitude de commenter votre code !

---

#### LIGNE 10 : Définir le chemin de la base

```php
$db_path = __DIR__ . '/database.db';
```

**🔍 Décomposition :**

1. `$db_path` = créer une variable nommée "db_path"
2. `=` = affecter une valeur
3. `__DIR__` = constante PHP qui donne le dossier actuel
4. `.` = concaténer (coller) des textes
5. `'/database.db'` = le nom du fichier de base
6. `;` = fin de l'instruction

**❓ Mais ça donne quoi exactement ?**

```php
Si votre fichier db.php est dans :
/Applications/MAMP/htdocs/user-auth-php/

Alors __DIR__ vaut :
/Applications/MAMP/htdocs/user-auth-php

Donc $db_path vaut :
/Applications/MAMP/htdocs/user-auth-php/database.db
```

**💡 Pourquoi utiliser `__DIR__` ?**

```
SANS __DIR__ (mauvais) :
$db_path = 'database.db';
→ PHP cherche dans le dossier où vous ÊTES
→ Si vous êtes dans un sous-dossier, ça ne marche pas !

AVEC __DIR__ (bon) :
$db_path = __DIR__ . '/database.db';
→ PHP cherche TOUJOURS au bon endroit
→ Ça marche de partout !
```

---

#### LIGNES 12-14 : Commencer un bloc try-catch

```php
try {
    // Le code ici
```

**🔍 Explication :**

`try-catch` = "Essaie de faire ça, et si ça échoue, fais ça"

**Analogie :**

```
TRY (essayer) :
    Essaie d'ouvrir le coffre-fort
    Si la clé marche → Super !

CATCH (attraper l'erreur) :
    Si la clé ne marche pas → Affiche un message
```

**En code :**

```php
try {
    // Essaie de te connecter à la base
} catch (Exception $e) {
    // Si ça échoue, affiche l'erreur
}
```

---

#### LIGNE 16 : Créer la connexion PDO

```php
    $pdo = new PDO('sqlite:' . $db_path);
```

**🔍 Décomposition :**

1. `$pdo` = variable qui va contenir notre connexion
2. `=` = affecter
3. `new PDO(...)` = créer un nouvel objet PDO
4. `'sqlite:'` = type de base (SQLite)
5. `. $db_path` = coller le chemin du fichier

**❓ C'est quoi PDO ?**

PDO = **PHP Data Objects**

C'est une **manière moderne et sécurisée** de parler aux bases de données.

**Analogie :**

```
Vous voulez parler à quelqu'un qui parle une autre langue :

SANS TRADUCTEUR (ancien PHP) :
Vous → ??? → Base de données
= Risque de ne pas se comprendre
= Risque de dire des bêtises (injections SQL)

AVEC TRADUCTEUR (PDO) :
Vous → Traducteur → Base de données
= Communication claire
= Sécurisé
```

---

#### LIGNES 19-20 : Configuration de PDO

```php
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
```

**🔍 Décomposition :**

1. `$pdo->` = appeler une fonction sur l'objet $pdo
2. `setAttribute(...)` = définir un paramètre
3. `PDO::ATTR_ERRMODE` = "le mode d'erreur"
4. `PDO::ERRMODE_EXCEPTION` = "lance une exception en cas d'erreur"

**❓ Pourquoi c'est important ?**

```
SANS cette ligne :
Si erreur SQL → PHP ne dit rien
= Impossible de savoir ce qui ne va pas !

AVEC cette ligne :
Si erreur SQL → PHP lance une exception
= Vous voyez l'erreur et pouvez la corriger !
```

---

```php
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
```

**🔍 Explication :**

Quand on récupère des données, on peut les avoir sous différentes formes.

**Exemple :**

```php
PDO::FETCH_ASSOC (ce qu'on choisit) :
[
    'id' => 1,
    'email' => 'user@example.com',
    'is_admin' => 0
]
= Facile à lire : $user['email']

PDO::FETCH_NUM (autre option) :
[
    0 => 1,
    1 => 'user@example.com',
    2 => 0
]
= Plus difficile : $user[1] (c'est quoi déjà 1 ?)
```

**💡 Conseil :** Utilisez toujours `FETCH_ASSOC`, c'est plus clair !

---

#### LIGNES 22-24 : Attraper les erreurs

```php
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
```

**🔍 Explication :**

1. `} catch (PDOException $e) {` = si une erreur de type PDOException arrive
2. `die(...)` = arrêter tout et afficher un message
3. `$e->getMessage()` = récupérer le message d'erreur

**❓ C'est quoi "die()" ?**

`die()` = arrêter complètement l'exécution du script

**Exemple :**

```php
echo "Ligne 1";
die("Erreur !");
echo "Ligne 2"; // Cette ligne ne s'exécutera JAMAIS
```

---

#### LIGNE 25 : Fermeture du PHP (optionnelle)

```php
?>
```

**💡 Bonne pratique :** Dans les fichiers 100% PHP (sans HTML), on ne met PAS de `?>` à la fin.

**Pourquoi ?**

```
Avec ?> :
<?php
$pdo = ...;
?>

(espace vide ici)

= PHP envoie cet espace vide au navigateur
= Peut causer des bugs avec les headers HTTP

Sans ?> :
<?php
$pdo = ...;


= Pas d'espace envoyé
= Plus sûr !
```

---

### 📄 Code complet de `db.php`

Maintenant, tapez TOUT le code ensemble :

```php
<?php
/**
 * CONNEXION À LA BASE DE DONNÉES
 *
 * Ce fichier sera inclus dans toutes les pages qui ont besoin
 * d'accéder à la base de données.
 */

$db_path = __DIR__ . '/database.db';

try {

    $pdo = new PDO('sqlite:' . $db_path);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
```

**✅ Sauvegardez le fichier (Ctrl+S ou Cmd+S)**

---

## ÉTAPE 3.3 : Créer la structure de la base `init_db.php`

### 📋 Ce qu'on va faire

Ce fichier va :
1. Se connecter à la base (avec `db.php`)
2. Créer la table "users"
3. Créer un compte admin par défaut

### ✍️ Créer le fichier

Créez un nouveau fichier : `init_db.php`

---

### 📝 Écrire le code LIGNE PAR LIGNE

#### LIGNES 1-7 : En-tête et inclusion

```php
<?php
/**
 * INITIALISATION DE LA BASE DE DONNÉES
 */

require_once 'db.php';
```

**🔍 Explication de `require_once` :**

- `require` = inclure un autre fichier PHP
- `once` = une seule fois (même si on l'appelle plusieurs fois)

**Analogie :**

```
require 'db.php' :
= "Va chercher le fichier db.php et mets son code ici"

require_once 'db.php' :
= "Va chercher le fichier db.php, mais si tu l'as déjà fait, ne le refais pas"
```

**❓ Pourquoi `require_once` et pas juste `require` ?**

```
Avec require (sans once) :
require 'db.php';
require 'db.php'; // Erreur ! $pdo déjà défini !

Avec require_once :
require_once 'db.php';
require_once 'db.php'; // OK, PHP ignore la 2e fois
```

---

#### LIGNES 9-20 : Créer la requête SQL

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
```

**🔍 Décomposition ligne par ligne :**

**Ligne 1 :** `CREATE TABLE IF NOT EXISTS users (`

- `CREATE TABLE` = créer une table
- `IF NOT EXISTS` = seulement si elle n'existe pas déjà
- `users` = nom de la table
- `(` = début de la définition des colonnes

**💡 Pourquoi `IF NOT EXISTS` ?**

```
SANS :
CREATE TABLE users...
→ Si on exécute 2 fois : ERREUR "Table déjà existante"

AVEC :
CREATE TABLE IF NOT EXISTS users...
→ Si on exécute 2 fois : Pas d'erreur, PHP ignore
```

---

**Ligne 2 :** `id INTEGER PRIMARY KEY AUTOINCREMENT,`

- `id` = nom de la colonne
- `INTEGER` = type de données (nombre entier)
- `PRIMARY KEY` = clé primaire (identifiant unique)
- `AUTOINCREMENT` = augmente automatiquement (1, 2, 3, 4...)

**❓ C'est quoi une PRIMARY KEY ?**

**Analogie :**

```
Dans une classe :
- Plusieurs élèves peuvent avoir le même prénom
- Plusieurs élèves peuvent avoir le même nom
- MAIS chaque élève a un numéro d'étudiant UNIQUE

Le numéro d'étudiant = PRIMARY KEY
```

**En base de données :**

```
Table users :
┌────┬─────────────┬──────────┐
│ id │ email       │ password │
├────┼─────────────┼──────────┤
│ 1  │ user@ex.com │ ...      │  ← id = 1 (unique)
│ 2  │ admin@ex.com│ ...      │  ← id = 2 (unique)
│ 3  │ test@ex.com │ ...      │  ← id = 3 (unique)
└────┴─────────────┴──────────┘

On ne peut PAS avoir deux utilisateurs avec id = 1
```

**❓ C'est quoi AUTOINCREMENT ?**

```
Vous créez un utilisateur :
→ PHP calcule automatiquement : id = 1

Vous créez un autre utilisateur :
→ PHP calcule automatiquement : id = 2

Vous créez un autre utilisateur :
→ PHP calcule automatiquement : id = 3

Vous n'avez RIEN à faire, c'est automatique !
```

---

**Ligne 3 :** `email TEXT NOT NULL UNIQUE,`

- `email` = nom de la colonne
- `TEXT` = type texte (peut contenir des lettres)
- `NOT NULL` = obligatoire (ne peut pas être vide)
- `UNIQUE` = unique dans toute la table

**❓ Pourquoi UNIQUE ?**

```
SANS UNIQUE :
┌────┬─────────────┐
│ id │ email       │
├────┼─────────────┤
│ 1  │ user@ex.com │
│ 2  │ user@ex.com │  ← Même email ! Problème !
└────┴─────────────┘

= Deux comptes avec le même email
= L'utilisateur ne peut pas se connecter (lequel ?)

AVEC UNIQUE :
┌────┬─────────────┐
│ id │ email       │
├────┼─────────────┤
│ 1  │ user@ex.com │
│ 2  │ admin@ex.com│  ← Différent, OK
└────┴─────────────┘

= Chaque email est unique
= Pas de confusion possible
```

---

**Ligne 4 :** `password TEXT NOT NULL,`

- `password` = nom de la colonne
- `TEXT` = type texte
- `NOT NULL` = obligatoire

**⚠️ Attention :** On va stocker le HASH, pas le mot de passe en clair !

```
JAMAIS ça :
password = "Test123!"

TOUJOURS ça :
password = "$2y$10$abcdefghijklmnop..."
```

---

**Ligne 5 :** `is_admin INTEGER DEFAULT 0,`

- `is_admin` = nom de la colonne
- `INTEGER` = nombre entier
- `DEFAULT 0` = valeur par défaut = 0

**❓ Pourquoi 0 ou 1 ?**

```
0 = FALSE = Non, pas admin = utilisateur normal
1 = TRUE  = Oui, admin     = administrateur

Exemple :
┌────┬─────────────┬──────────┐
│ id │ email       │ is_admin │
├────┼─────────────┼──────────┤
│ 1  │ admin@ex.com│ 1        │  ← Admin
│ 2  │ user@ex.com │ 0        │  ← Utilisateur normal
└────┴─────────────┴──────────┘
```

---

**Ligne 6 :** `created_at TEXT DEFAULT CURRENT_TIMESTAMP`

- `created_at` = nom de la colonne
- `TEXT` = type texte
- `DEFAULT CURRENT_TIMESTAMP` = date/heure actuelle automatiquement

**❓ Ça sert à quoi ?**

```
Quand vous créez un utilisateur :
→ SQLite remplit automatiquement created_at avec la date actuelle

Exemple :
┌────┬─────────────┬────────────────────┐
│ id │ email       │ created_at         │
├────┼─────────────┼────────────────────┤
│ 1  │ user@ex.com │ 2024-01-15 10:30:00│
└────┴─────────────┴────────────────────┘

= Vous savez quand le compte a été créé !
```

---

**Ligne 7 :** `)`

- Fermeture de la définition de la table

---

**Ligne 8 :** `";`

- Fin de la chaîne SQL
- `;` pour terminer l'instruction PHP

---

#### LIGNES 22-30 : Exécuter la création de table

```php
try {
    $pdo->exec($sql_create_table);
    echo "✅ Table 'users' créée avec succès !<br>";

} catch (PDOException $e) {
    die("❌ Erreur lors de la création de la table : " . $e->getMessage());
}
```

**🔍 Explication :**

- `$pdo->exec(...)` = exécuter une requête SQL
- `echo` = afficher un message
- `<br>` = saut de ligne en HTML

**❓ Pourquoi `exec()` et pas `query()` ?**

```
exec() :
- Pour les requêtes qui NE RETOURNENT PAS de données
- CREATE, INSERT, UPDATE, DELETE
- Retourne le nombre de lignes affectées

query() :
- Pour les requêtes qui RETOURNENT des données
- SELECT
- Retourne les résultats
```

---

#### LIGNES 32-60 : Créer un admin par défaut

```php
$stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE is_admin = 1");
$stmt->execute();
$admin_count = $stmt->fetchColumn();
```

**🔍 Ligne par ligne :**

**Ligne 1 :** `$stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE is_admin = 1");`

- `SELECT COUNT(*)` = compter le nombre de lignes
- `FROM users` = dans la table users
- `WHERE is_admin = 1` = où is_admin vaut 1

**Traduction :**
"Compte combien d'utilisateurs sont admin"

**Ligne 2 :** `$stmt->execute();`

- Exécuter la requête préparée

**Ligne 3 :** `$admin_count = $stmt->fetchColumn();`

- `fetchColumn()` = récupérer la première colonne du résultat
- Ici, ça va retourner un nombre : 0, 1, 2, etc.

---

```php
if ($admin_count == 0) {
    // Créer un admin
}
```

**🔍 Explication :**

Si aucun admin n'existe (`$admin_count == 0`), on en crée un.

---

```php
$admin_email = 'admin@example.com';
$admin_password = 'Admin123!';
$admin_password_hash = password_hash($admin_password, PASSWORD_BCRYPT);
```

**🔍 Ligne par ligne :**

1. Email de l'admin : `admin@example.com`
2. Mot de passe : `Admin123!` (en clair, juste pour nous)
3. Hash du mot de passe : `$2y$10$...` (ce qu'on va stocker)

**⚠️ Important :** On ne stocke JAMAIS `$admin_password` dans la base !
On stocke `$admin_password_hash` !

---

```php
$stmt = $pdo->prepare("
    INSERT INTO users (email, password, is_admin)
    VALUES (:email, :password, 1)
");
```

**🔍 Explication :**

- `INSERT INTO users` = insérer dans la table users
- `(email, password, is_admin)` = dans ces colonnes
- `VALUES (:email, :password, 1)` = avec ces valeurs
- `:email` et `:password` = placeholders (seront remplacés)

**❓ Pourquoi `:email` au lieu de mettre directement l'email ?**

```
MAUVAIS (injection SQL possible) :
$sql = "INSERT INTO users VALUES ('$email', '$password')";
→ Si $email = "'; DROP TABLE users; --"
→ Votre table est supprimée ! 💀

BON (requête préparée) :
$stmt = $pdo->prepare("INSERT INTO users VALUES (:email, :password)");
$stmt->execute(['email' => $email, 'password' => $password]);
→ PDO échappe automatiquement les caractères dangereux
→ Impossible d'injecter du SQL ! ✅
```

---

```php
$stmt->execute([
    'email' => $admin_email,
    'password' => $admin_password_hash
]);
```

**🔍 Explication :**

- On remplace `:email` par `$admin_email`
- On remplace `:password` par `$admin_password_hash`
- PDO exécute la requête

**Résultat dans la base :**

```
Table users :
┌────┬──────────────────┬─────────────────────┬──────────┐
│ id │ email            │ password            │ is_admin │
├────┼──────────────────┼─────────────────────┼──────────┤
│ 1  │ admin@example.com│ $2y$10$abcdefg...  │ 1        │
└────┴──────────────────┴─────────────────────┴──────────┘
```

---

### 📄 Code complet de `init_db.php`

```php
<?php
/**
 * INITIALISATION DE LA BASE DE DONNÉES
 */

require_once 'db.php';

// Créer la table users
$sql_create_table = "
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    is_admin INTEGER DEFAULT 0,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP
)
";

try {
    $pdo->exec($sql_create_table);
    echo "✅ Table 'users' créée avec succès !<br>";

} catch (PDOException $e) {
    die("❌ Erreur lors de la création de la table : " . $e->getMessage());
}

// Créer un admin par défaut
$stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE is_admin = 1");
$stmt->execute();
$admin_count = $stmt->fetchColumn();

if ($admin_count == 0) {

    $admin_email = 'admin@example.com';
    $admin_password = 'Admin123!';
    $admin_password_hash = password_hash($admin_password, PASSWORD_BCRYPT);

    $stmt = $pdo->prepare("
        INSERT INTO users (email, password, is_admin)
        VALUES (:email, :password, 1)
    ");

    $stmt->execute([
        'email' => $admin_email,
        'password' => $admin_password_hash
    ]);

    echo "✅ Compte administrateur créé !<br>";
    echo "📧 Email : admin@example.com<br>";
    echo "🔑 Mot de passe : Admin123!<br>";

} else {
    echo "ℹ️ Un compte administrateur existe déjà.<br>";
}

echo "<br><a href='index.php'>→ Aller à la page d'accueil</a>";
```

**✅ Sauvegardez le fichier**

---

## ÉTAPE 3.4 : Exécuter l'initialisation

### 🧪 TEST

1. Ouvrez votre navigateur
2. Allez sur : `http://localhost/user-auth-php/init_db.php`
3. Vous devriez voir :

```
✅ Table 'users' créée avec succès !
✅ Compte administrateur créé !
📧 Email : admin@example.com
🔑 Mot de passe : Admin123!

→ Aller à la page d'accueil
```

4. Regardez dans votre dossier : un fichier `database.db` a été créé ! 🎉

---

### ✍️ EXERCICE DE COMPRÉHENSION

Avant de continuer, assurez-vous de comprendre :

**Question 1 :** À quoi sert `__DIR__` ?
```
Votre réponse : _____________________________________
```

**Question 2 :** Pourquoi utilise-t-on `password_hash()` ?
```
Votre réponse : _____________________________________
```

**Question 3 :** Que fait `AUTOINCREMENT` ?
```
Votre réponse : _____________________________________
```

**Question 4 :** Pourquoi utiliser `:email` au lieu de mettre directement l'email dans la requête ?
```
Votre réponse : _____________________________________
```

<details>
<summary>📖 Voir les réponses</summary>

1. `__DIR__` donne le chemin absolu du dossier contenant le fichier PHP actuel
2. Pour transformer le mot de passe en hash irréversible (sécurité)
3. Augmente automatiquement l'ID à chaque nouvel enregistrement (1, 2, 3...)
4. Pour éviter les injections SQL (les placeholders sont échappés automatiquement par PDO)

</details>

---

# 🎯 FIN DU CHAPITRE 3

Vous avez maintenant :
- ✅ Un fichier `db.php` qui se connecte à la base
- ✅ Un fichier `init_db.php` qui crée la structure
- ✅ Une table `users` dans `database.db`
- ✅ Un compte admin créé

**Prochaine étape :** Créer le header et la navigation !

---

# SUITE DU COURS...

(La suite continue avec le même niveau de détail pour TOUS les chapitres restants)

---

**💬 NOTE POUR VOUS, LE FORMATEUR :**

Ce cours est BEAUCOUP plus long car chaque concept est expliqué en profondeur. Je peux continuer à ce niveau de détail pour TOUS les chapitres restants :

- Chapitre 4 : Header et navigation
- Chapitre 5 : Page d'inscription (formulaire)
- Chapitre 6 : Page d'inscription (validation)
- Chapitre 7 : Page de connexion
- Chapitre 8 : Page de profil
- Chapitre 9 : Déconnexion
- Chapitre 10 : Panneau admin
- Etc.

**Voulez-vous que je continue avec le même niveau de détail pour toute la suite ?**
