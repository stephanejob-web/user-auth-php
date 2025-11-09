# 🚀 Démarrage Rapide (5 minutes)

## 💡 Pourquoi SQLite au début ?

**SQLite** = Une base de données dans **un seul fichier** (database.db)

**Avantages pour vous :**
- ✅ Déjà inclus avec PHP (rien à installer)
- ✅ Pas besoin de MySQL ni phpMyAdmin
- ✅ Configuration en 2 minutes
- ✅ Vous vous concentrez sur le code PHP !

**Plus tard**, vous passerez à MySQL pour les sites en production. Mais le code PHP reste identique !

---

## Étape 1 : Préparer les fichiers de connexion

**Renommage manuel des fichiers (via l'explorateur de fichiers) :**

1. **Renommez** le fichier `db.php` en `db_mysql.php`
   - Clic droit sur `db.php` → Renommer → tapez `db_mysql.php`
   - *(On garde ce fichier pour montrer MySQL plus tard)*

2. **Dupliquez** le fichier `db_sqlite.php`
   - Clic droit sur `db_sqlite.php` → Copier → Coller
   - Une copie nommée `db_sqlite.php copie` ou `db_sqlite - Copie.php` apparaît

3. **Renommez** cette copie en `db.php`
   - Clic droit sur la copie → Renommer → tapez `db.php`

**Explication :**
- On garde `db_mysql.php` pour plus tard (migration MySQL)
- On garde `db_sqlite.php` (fichier original)
- On crée `db.php` à partir de SQLite pour utiliser SQLite maintenant
- Pas besoin de MySQL ni phpMyAdmin !

---

## Étape 2 : Démarrer le serveur

```bash
php -S localhost:8000
```

**Explication :** Lance un serveur web PHP sur le port 8000.

---

## Étape 3 : Créer la base de données

Ouvrez votre navigateur et allez sur :

```
http://localhost:8000/init_db.php
```

✅ Vous devriez voir : **"Base de données initialisée avec succès !"**

---

## Étape 4 : Se connecter

Allez sur :

```
http://localhost:8000
```

Cliquez sur **"Login"** et utilisez :

- **Email :** `admin@example.com`
- **Mot de passe :** `Admin123!`

---

## ✅ C'est tout !

Vous pouvez maintenant :
- ✅ Créer de nouveaux utilisateurs (Register)
- ✅ Modifier votre profil (Profile)
- ✅ Gérer les utilisateurs (Admin → uniquement pour les admins)

---

## 🔄 Pour réinitialiser la base de données

1. **Supprimez** le fichier `database.db` (clic droit → Supprimer)
2. Retournez sur `http://localhost:8000/init_db.php`

---

## 📖 Pour aller plus loin

Lisez le fichier [README.md](README.md) pour comprendre :
- Comment fonctionne le code
- La structure du projet
- Les différences SQLite vs MySQL
- Les fonctionnalités de sécurité

---

## ❓ Problèmes courants

### "Call to undefined function PDO::__construct()"
→ PDO n'est pas activé. Vérifiez votre `php.ini` et activez `extension=pdo_sqlite`

### "Unable to open database file"
→ Problème de permissions sur le dossier du projet.
- **Windows** : Généralement pas de problème
- **Mac/Linux** : Le dossier doit avoir les permissions d'écriture

### La page init_db.php affiche du texte brut (code PHP visible)
→ Le serveur PHP n'est pas démarré. Lancez `php -S localhost:8000` dans le terminal

### "SQLSTATE[HY000]: General error: 8 attempt to write a readonly database"
→ Le dossier n'a pas les permissions d'écriture. Contactez votre formateur.
