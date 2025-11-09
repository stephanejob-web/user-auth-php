<?php
/**
 * ============================================================================
 * PAGE DE DÉCONNEXION (LOGOUT.PHP)
 * ============================================================================
 *
 * Cette page déconnecte l'utilisateur en détruisant sa session.
 *
 * Processus de déconnexion :
 * 1. Démarrer la session (pour pouvoir la modifier)
 * 2. Supprimer toutes les variables de session
 * 3. Détruire la session elle-même
 * 4. Rediriger vers la page d'accueil
 *
 * NOTE IMPORTANTE : Ce fichier ne contient QUE du code PHP, pas de HTML
 * Car il fait une redirection immédiate après avoir détruit la session
 */

// ----------------------------------------------------------------------------
// ÉTAPE 1 : DÉMARRER LA SESSION
// ----------------------------------------------------------------------------

/**
 * Pourquoi faire session_start() pour se DÉCONNECTER ?
 *
 * Paradoxe apparent : pour détruire une session, il faut d'abord la démarrer !
 *
 * Explication :
 * - session_start() reprend la session existante (grâce au cookie PHPSESSID)
 * - PHP charge toutes les données de la session dans $_SESSION
 * - Ensuite, on peut supprimer ces données et détruire la session
 *
 * Sans session_start(), PHP ne saurait pas QUELLE session détruire
 */
session_start();

// À ce stade, $_SESSION contient toutes les données de l'utilisateur :
// - $_SESSION['user_id']
// - $_SESSION['email']
// - $_SESSION['is_admin']

// ----------------------------------------------------------------------------
// ÉTAPE 2 : SUPPRIMER TOUTES LES VARIABLES DE SESSION
// ----------------------------------------------------------------------------

/**
 * session_unset() : supprime TOUTES les variables stockées dans $_SESSION
 *
 * Effet :
 * - Avant : $_SESSION = ['user_id' => 5, 'email' => 'test@example.com', 'is_admin' => 0]
 * - Après : $_SESSION = []
 *
 * Techniquement, c'est équivalent à faire :
 * unset($_SESSION['user_id']);
 * unset($_SESSION['email']);
 * unset($_SESSION['is_admin']);
 * etc. pour TOUTES les variables
 *
 * Mais session_unset() est plus simple et sûr (on n'oublie aucune variable)
 *
 * NOTE : La session existe toujours à ce stade, mais elle est vide
 */
session_unset();

// ----------------------------------------------------------------------------
// ÉTAPE 3 : DÉTRUIRE LA SESSION COMPLÈTEMENT
// ----------------------------------------------------------------------------

/**
 * session_destroy() : détruit la session côté serveur
 *
 * Qu'est-ce qui est détruit ?
 * - Le fichier de session sur le serveur (souvent dans /tmp/sess_abc123...)
 * - Les données ne sont plus récupérables
 *
 * Qu'est-ce qui N'est PAS détruit ?
 * - Le cookie PHPSESSID dans le navigateur de l'utilisateur
 *   (il sera simplement ignoré car la session n'existe plus côté serveur)
 *
 * Différence entre session_unset() et session_destroy() :
 * - session_unset() : vide le contenu de la session (mais la session existe encore)
 * - session_destroy() : supprime complètement la session
 *
 * En pratique, on fait TOUJOURS les deux pour une déconnexion propre :
 * 1. session_unset() pour vider les données
 * 2. session_destroy() pour supprimer la session
 */
session_destroy();

// ----------------------------------------------------------------------------
// ÉTAPE 4 : REDIRIGER VERS LA PAGE D'ACCUEIL
// ----------------------------------------------------------------------------

/**
 * header('Location: index.php') : redirige le navigateur vers index.php
 *
 * Pourquoi rediriger ?
 * - L'utilisateur vient de se déconnecter
 * - On veut lui montrer la page d'accueil (version non connectée)
 * - Sans redirection, il verrait une page blanche (ce fichier n'a pas de HTML)
 *
 * Rappel : header() doit être appelé AVANT tout affichage HTML
 * C'est le cas ici car ce fichier ne contient que du PHP
 */
header('Location: index.php');

// ----------------------------------------------------------------------------
// ÉTAPE 5 : ARRÊTER L'EXÉCUTION
// ----------------------------------------------------------------------------

/**
 * exit() : arrête immédiatement l'exécution du script PHP
 *
 * Pourquoi c'est nécessaire après header('Location: ...') ?
 *
 * - header() envoie une instruction au navigateur de rediriger
 * - Mais PHP continue d'exécuter le code qui suit !
 * - Si on avait du code après (ce qui n'est pas le cas ici), il serait exécuté
 * - exit() garantit qu'aucun code ne sera exécuté après la redirection
 *
 * Bonne pratique : TOUJOURS mettre exit() après header('Location: ...')
 *
 * Exemples de ce qui pourrait mal se passer sans exit() :
 * - Requêtes SQL inutiles qui s'exécutent
 * - Données sensibles qui sont traitées alors que l'utilisateur est déconnecté
 * - Logs qui sont créés avec des informations incorrectes
 */
exit();

// ----------------------------------------------------------------------------
// FIN DU FICHIER
// ----------------------------------------------------------------------------

// Tout ce qui est après exit() n'est JAMAIS exécuté
// PHP s'arrête complètement à la ligne exit()

/*
===============================================================================
NOTES PÉDAGOGIQUES - DÉCONNEXION ET GESTION DES SESSIONS
===============================================================================

1. DIFFÉRENCE ENTRE LES FONCTIONS DE SESSION :

   session_start()
   └─> Démarre ou reprend une session
       - Crée une nouvelle session si elle n'existe pas
       - Reprend la session existante si elle existe déjà
       - Charge les données de $_SESSION depuis le serveur

   session_unset()
   └─> Vide toutes les variables de $_SESSION
       - $_SESSION devient un tableau vide []
       - La session existe toujours, mais sans données
       - Équivalent à : $_SESSION = [];

   session_destroy()
   └─> Détruit complètement la session côté serveur
       - Supprime le fichier de session sur le serveur
       - La session n'existe plus
       - Les données ne sont plus récupérables

2. POURQUOI CES 3 ÉTAPES ?

   Pourquoi ne pas juste faire session_destroy() ?

   - session_destroy() SEUL ne vide pas $_SESSION dans le script actuel
   - Il détruit juste la session pour les PROCHAINES requêtes
   - session_unset() assure que $_SESSION est vide IMMÉDIATEMENT

   Analogie :
   - session_unset() : vider une boîte de tous ses objets
   - session_destroy() : jeter la boîte elle-même

3. FLUX COMPLET DE CONNEXION/DÉCONNEXION :

   CONNEXION (login.php) :
   ┌────────────────────────────────────────┐
   │ session_start()                        │
   │ $_SESSION['user_id'] = 5               │
   │ $_SESSION['email'] = 'test@example.com'│
   │ $_SESSION['is_admin'] = 0              │
   │ header('Location: index.php')          │
   │ exit()                                 │
   └────────────────────────────────────────┘

   NAVIGATION (index.php, profile.php, etc.) :
   ┌────────────────────────────────────────┐
   │ session_start()                        │
   │ // Les données sont toujours là :     │
   │ // $_SESSION['user_id'] = 5           │
   │ // $_SESSION['email'] = 'test@...'    │
   │ // $_SESSION['is_admin'] = 0          │
   └────────────────────────────────────────┘

   DÉCONNEXION (logout.php) :
   ┌────────────────────────────────────────┐
   │ session_start()                        │
   │ session_unset()    // Vide $_SESSION  │
   │ session_destroy()  // Détruit tout    │
   │ header('Location: index.php')          │
   │ exit()                                 │
   └────────────────────────────────────────┘

4. SÉCURITÉ :

   - Toujours faire session_unset() + session_destroy() pour la déconnexion
   - Ne PAS juste faire unset($_SESSION['user_id'])
     (d'autres données pourraient rester en session)

   - Toujours rediriger après la déconnexion
   - Toujours utiliser exit() après la redirection

5. COOKIES :

   Le cookie PHPSESSID reste dans le navigateur après logout
   mais il ne sert plus à rien car la session n'existe plus
   côté serveur.

   Pour supprimer aussi le cookie, on pourrait ajouter :
   setcookie('PHPSESSID', '', time() - 3600, '/');

   Mais ce n'est pas obligatoire car le cookie sera écrasé
   lors de la prochaine connexion.

6. DURÉE DE VIE D'UNE SESSION :

   Par défaut, une session expire :
   - Après 24 minutes d'inactivité (paramètre PHP session.gc_maxlifetime)
   - Quand l'utilisateur ferme son navigateur (pour les cookies de session)
   - Quand l'utilisateur clique sur "Logout"

   On peut modifier ces paramètres dans php.ini ou avec :
   ini_set('session.gc_maxlifetime', 3600); // 1 heure

7. FICHIERS DE SESSION :

   Où sont stockées les sessions ?
   - Linux/Mac : /tmp/sess_abc123def456...
   - Windows : C:\Windows\Temp\sess_abc123def456...

   Format du nom de fichier : sess_[ID_DE_SESSION]

   Contenu du fichier (format sérialisé PHP) :
   user_id|i:5;email|s:17:"test@example.com";is_admin|i:0;

===============================================================================
*/
?>
