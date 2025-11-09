# 📚 COURS COMPLET - SYSTÈME D'AUTHENTIFICATION PHP

> **Formation complète pour débutants**
> **De zéro à un système d'authentification professionnel**

---

## 🎯 OBJECTIF GLOBAL

Ce cours vous apprendra à créer **de A à Z** un système d'authentification sécurisé en PHP avec :
- ✅ Inscription utilisateur
- ✅ Connexion / Déconnexion
- ✅ Gestion de profil
- ✅ Panneau d'administration
- ✅ **Sécurité renforcée**

À la fin de cette formation, vous maîtriserez **tous les concepts fondamentaux** pour créer des applications web sécurisées.

---

## 📖 TABLE DES MATIÈRES

### 🔰 Niveau Débutant

| Chapitre | Titre | Fichiers couverts | Durée | Objectifs |
|----------|-------|-------------------|-------|-----------|
| **[01](01-introduction-et-architecture.md)** | **Introduction et Architecture** | Vue d'ensemble | 2h | Comprendre la structure globale du projet |
| **[02](02-base-de-donnees.md)** | **Base de données** | `db.php`, `init_db.php` | 3h | Maîtriser PDO et la création de la base |

### 📁 Référence des fichiers

Pour une analyse approfondie de chaque fichier, consultez directement les fichiers PHP qui sont **extrêmement commentés** :

| Fichier | Description | Lignes clés | Concepts |
|---------|-------------|-------------|----------|
| **db_sqlite.php** | Connexion PDO SQLite | 62, 64, 76 | PDO, DSN, PRAGMA |
| **init_db.php** | Création de la base | 92-100, 171-184 | CREATE TABLE, INSERT |
| **register.php** | Inscription utilisateur | 98-190, 280, 298 | Validation, password_hash(), INSERT |
| **login.php** | Connexion utilisateur | 93, 142, 175-184, 201 | password_verify(), sessions |
| **logout.php** | Déconnexion | 63, 88, 105 | session_unset(), session_destroy() |
| **profile.php** | Modification profil | 111-163, 170-214, 230-270 | UPDATE dynamique, session |
| **admin.php** | Liste utilisateurs | 90, 104, 227-389 | fetchAll(), foreach, tableaux HTML |
| **edit_user.php** | Édition utilisateur | 81, 91-105, 129, 358 | Paramètres GET, checkbox, UPDATE |
| **delete_user.php** | Suppression utilisateur | 46, 78, 112, 144-189 | DELETE, protections multiples |
| **header.php** | Navigation | 26, 127-197 | Sessions, navigation dynamique |
| **index.php** | Page d'accueil | 47, 89 | Affichage conditionnel |

---

## 🗺️ PARCOURS D'APPRENTISSAGE RECOMMANDÉ

### Semaine 1 : Fondations

```
Jour 1-2 : Chapitres 01-02
├─ Comprendre l'architecture
├─ Installer l'environnement
├─ Créer la base de données
└─ Tester init_db.php

Jour 3-4 : Étudier les fichiers commentés
├─ Lire db_sqlite.php ligne par ligne
├─ Lire register.php ligne par ligne
├─ Tester l'inscription
└─ Comprendre password_hash()

Jour 5-7 : Authentification
├─ Lire login.php ligne par ligne
├─ Comprendre les sessions
├─ Tester la connexion
└─ Lire logout.php
```

### Semaine 2 : Fonctionnalités

```
Jour 1-3 : Gestion utilisateur
├─ Lire profile.php
├─ Comprendre les requêtes UPDATE dynamiques
├─ Tester la modification de profil
└─ Analyser la sécurité

Jour 4-7 : Administration
├─ Lire admin.php (liste utilisateurs)
├─ Comprendre fetchAll() et foreach
├─ Lire edit_user.php (paramètres GET, checkbox)
├─ Lire delete_user.php (protections)
└─ Tester toutes les fonctionnalités admin
```

---

## 📚 RESSOURCES SUPPLÉMENTAIRES

### Fichiers déjà fournis dans le projet

Tous les fichiers PHP du projet contiennent des **commentaires ultra-détaillés** qui expliquent :
- 🔍 Chaque ligne de code
- 💡 Pourquoi on fait comme ça
- ⚠️ Les erreurs courantes à éviter
- ✅ Les bonnes pratiques

**Vous n'avez qu'à lire les fichiers PHP directement !**

### Documentation recommandée

| Sujet | Ressource officielle |
|-------|---------------------|
| **PHP** | [php.net/manual/fr](https://www.php.net/manual/fr/) |
| **PDO** | [php.net/manual/fr/book.pdo.php](https://www.php.net/manual/fr/book.pdo.php) |
| **SQLite** | [sqlite.org/docs.html](https://www.sqlite.org/docs.html) |
| **Sécurité** | [owasp.org](https://owasp.org/) |

---

## 🎓 CONCEPTS CLÉS PAR CHAPITRE

### Chapitre 01 : Introduction et Architecture
- Structure du projet
- Rôle de chaque fichier
- Flux de navigation
- Sessions PHP
- Hachage de mot de passe
- Requêtes préparées

### Chapitre 02 : Base de données
- SQLite vs MySQL
- PDO et DSN
- Création de table
- Types de données SQLite
- PRIMARY KEY et AUTOINCREMENT
- UNIQUE et NOT NULL
- password_hash()

### Fichiers à étudier directement

**register.php** (Inscription)
- Validation email avec `filter_var()`
- Validation mot de passe avec `preg_match()`
- `password_hash()` avec BCRYPT
- Requêtes préparées PDO
- Gestion des erreurs

**login.php** (Connexion)
- `password_verify()` pour vérifier le mot de passe
- Création de sessions avec `$_SESSION`
- Redirection avec `header()` et `exit()`
- Protection contre les timing attacks

**profile.php** (Gestion profil)
- Champs optionnels
- Construction dynamique de requêtes UPDATE
- Mise à jour de session
- Vérification email unique

**admin.php** (Administration)
- `fetchAll()` pour récupérer plusieurs lignes
- Boucle `foreach` pour afficher les résultats
- Tableaux HTML (`<table>`, `<thead>`, `<tbody>`)
- Opérateur ternaire `? :`

**edit_user.php** (Édition utilisateur)
- Paramètres GET (`$_GET['id']`)
- Conversion de type `(int)`
- Checkbox HTML et `isset()`
- Pré-remplissage de formulaire
- Protection multi-niveaux

**delete_user.php** (Suppression)
- Requête DELETE
- Protection anti-auto-suppression
- Vérifications en cascade
- Redirection après action

**header.php** (Navigation)
- Navigation conditionnelle
- Balises sémantiques HTML5
- Gestion de session globale

---

## 🛠️ GUIDE DE DÉMARRAGE RAPIDE

### 1. Installation (5 minutes)

```bash
# 1. Cloner ou télécharger le projet
cd /Applications/MAMP/htdocs/

# 2. Renommer les fichiers de base de données
# Renommer db.php en db_mysql.php (sauvegarde)
# Copier db_sqlite.php vers db.php

# 3. Lancer le serveur PHP
php -S localhost:8000

# 4. Initialiser la base de données
# Ouvrir http://localhost:8000/init_db.php dans le navigateur
```

### 2. Premiers tests (10 minutes)

```
1. Ouvrir http://localhost:8000
   → Page d'accueil (non connecté)

2. Cliquer sur "Register"
   → Créer un compte avec email + mot de passe

3. Se connecter
   → Email : celui que vous avez créé
   → Mot de passe : celui que vous avez créé

4. Tester le profil
   → Modifier email ou mot de passe

5. Se connecter en admin
   → Email : admin@example.com
   → Mot de passe : Admin123!

6. Tester le panneau admin
   → Voir la liste des utilisateurs
   → Modifier un utilisateur
   → Promouvoir/rétrograder admin
```

---

## 💡 CONSEILS PÉDAGOGIQUES

### Comment utiliser ce cours

**❌ Ne pas faire :**
- Copier-coller le code sans le lire
- Passer d'un fichier à l'autre sans comprendre
- Ignorer les commentaires dans le code
- Coder sans tester

**✅ À faire :**
- Lire TOUS les commentaires dans chaque fichier
- Tester après chaque modification
- Expérimenter (changer des valeurs, voir ce qui se passe)
- Poser des questions si vous ne comprenez pas
- Recréer le projet de zéro après l'avoir terminé

### Méthodologie d'apprentissage

```
1. LIRE le chapitre du cours
   ↓
2. LIRE le fichier PHP correspondant avec TOUS ses commentaires
   ↓
3. COMPRENDRE chaque ligne
   ↓
4. TESTER dans le navigateur
   ↓
5. EXPÉRIMENTER (modifier, casser, réparer)
   ↓
6. REFAIRE de zéro (sans regarder)
```

---

## 🎯 OBJECTIFS D'APPRENTISSAGE PAR NIVEAU

### Niveau 1 : Débutant ✅
- [ ] Je comprends la structure du projet
- [ ] Je sais ce qu'est une base de données
- [ ] Je sais créer une connexion PDO
- [ ] Je comprends le rôle de chaque fichier
- [ ] Je sais naviguer dans le projet

### Niveau 2 : Intermédiaire ⚙️
- [ ] Je sais créer un formulaire d'inscription
- [ ] Je sais valider des données en PHP
- [ ] Je maîtrise `password_hash()` et `password_verify()`
- [ ] Je comprends les sessions PHP
- [ ] Je sais créer/modifier/supprimer un utilisateur

### Niveau 3 : Avancé 🚀
- [ ] Je sais construire des requêtes SQL dynamiques
- [ ] Je comprends toutes les protections de sécurité
- [ ] Je sais gérer les permissions (admin vs user)
- [ ] Je peux recréer le projet de zéro
- [ ] Je peux expliquer chaque ligne de code

---

## 📊 ÉVALUATION DE VOS CONNAISSANCES

### Auto-évaluation rapide

**Pour chaque question, notez si vous pouvez répondre sans regarder le code :**

1. ⬜ Expliquer ce qu'est PDO et pourquoi on l'utilise
2. ⬜ Différencier `password_hash()` et `password_verify()`
3. ⬜ Expliquer pourquoi on utilise des requêtes préparées
4. ⬜ Décrire le cycle de vie d'une session PHP
5. ⬜ Lister les 5 colonnes de la table `users`
6. ⬜ Expliquer la différence entre `fetch()` et `fetchAll()`
7. ⬜ Décrire comment fonctionne `isset($_SESSION['user_id'])`
8. ⬜ Expliquer pourquoi `(int)$_GET['id']` est important
9. ⬜ Décrire les protections de `delete_user.php`
10. ⬜ Expliquer comment fonctionne une checkbox en POST

**Score :**
- 0-3 : Relire les chapitres 01-02
- 4-7 : Bien ! Continuez l'étude des fichiers
- 8-10 : Excellent ! Vous maîtrisez le projet

---

## 🆘 AIDE ET SUPPORT

### Problèmes courants

<details>
<summary><strong>La base de données ne se crée pas</strong></summary>

**Solutions :**
1. Vérifier que vous avez bien renommé `db_sqlite.php` en `db.php`
2. Vérifier les permissions du dossier : `chmod 755`
3. Supprimer `database.db` et relancer `init_db.php`
4. Vérifier les erreurs PHP dans la console

</details>

<details>
<summary><strong>Je ne peux pas me connecter</strong></summary>

**Solutions :**
1. Vérifier les identifiants : `admin@example.com` / `Admin123!`
2. Vérifier que `database.db` existe et n'est pas vide
3. Relancer `init_db.php` pour recréer l'admin
4. Vérifier que les sessions fonctionnent (`session_start()`)

</details>

<details>
<summary><strong>Les sessions ne fonctionnent pas</strong></summary>

**Solutions :**
1. Vérifier que `session_start()` est appelé AVANT tout HTML
2. Vérifier qu'il n'y a pas d'espace avant `<?php`
3. Vérifier les permissions du dossier de session PHP
4. Tester avec `var_dump($_SESSION)` pour voir le contenu

</details>

### Où trouver de l'aide ?

1. **Les commentaires dans le code** : Chaque fichier est ultra-documenté
2. **Le README.md** : Guide général et FAQ
3. **QUICKSTART.md** : Démarrage rapide en 5 minutes
4. **COURS-DETAILLE-AUTHENTIFICATION.md** : Cours original très détaillé

---

## 🎉 CONCLUSION

Ce projet vous apprendra **bien plus** que juste créer un système d'authentification. Vous allez maîtriser :

- 💾 La gestion de bases de données avec PDO
- 🔐 La sécurité web (hachage, requêtes préparées, sessions)
- 🎨 L'architecture d'une application web
- 📝 Les bonnes pratiques de programmation PHP
- 🧪 Le débogage et la résolution de problèmes

**Prenez votre temps, lisez TOUT, testez TOUT, et surtout : AMUSEZ-VOUS !** 🚀

---

## 📌 PROCHAINES ÉTAPES

1. ✅ Lire le [Chapitre 01 - Introduction et Architecture](01-introduction-et-architecture.md)
2. ✅ Lire le [Chapitre 02 - Base de données](02-base-de-donnees.md)
3. 📖 Étudier les fichiers PHP directement (ils sont ultra-commentés)
4. 🧪 Tester chaque fonctionnalité
5. 🔄 Recréer le projet de zéro

**Bon apprentissage ! 🎓**
