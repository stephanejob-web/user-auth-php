<?php
/**
 * ============================================================================
 * AUTOLOAD - CHARGEMENT AUTOMATIQUE DES CLASSES
 * ============================================================================
 *
 * Ce fichier permet de charger automatiquement les classes PHP sans avoir
 * à faire des require/include manuels dans chaque fichier.
 *
 * PSR-4 Autoloading :
 * - Standard PHP pour le chargement automatique
 * - Mappe les namespaces aux dossiers
 * - App\Config\Database → src/Config/Database.php
 *
 * Utilisation :
 * require_once 'autoload.php';
 * // Maintenant toutes les classes sont accessibles automatiquement
 */

spl_autoload_register(function ($class) {
    /**
     * $class contient le nom complet de la classe avec son namespace
     * Exemple : "App\Config\Database"
     *
     * On va le transformer en chemin de fichier :
     * "App\Config\Database" → "src/Config/Database.php"
     */

    // Préfixe du namespace (notre application commence par "App\")
    $prefix = 'App\\';

    // Dossier de base où se trouvent nos classes
    $baseDir = __DIR__ . '/src/';

    // Vérifier si la classe utilise le préfixe de notre application
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // Ce n'est pas une classe de notre application, on ne fait rien
        return;
    }

    // Récupérer le nom de la classe sans le préfixe
    // "App\Config\Database" → "Config\Database"
    $relativeClass = substr($class, $len);

    // Remplacer les backslashes par des slashes et ajouter .php
    // "Config\Database" → "Config/Database.php"
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    // Si le fichier existe, l'inclure
    if (file_exists($file)) {
        require $file;
    }
});

/*
===============================================================================
NOTES PÉDAGOGIQUES - AUTOLOAD
===============================================================================

1. QU'EST-CE QUE L'AUTOLOAD ?

   Avant (sans autoload) :
   require_once 'src/Config/Database.php';
   require_once 'src/Models/User.php';
   require_once 'src/Services/Auth.php';
   require_once 'src/Services/Session.php';
   require_once 'src/Services/Validator.php';
   require_once 'src/Utils/Response.php';
   // ... et ainsi de suite pour chaque classe !

   Après (avec autoload) :
   require_once 'autoload.php';
   // C'est tout ! Toutes les classes sont chargées automatiquement

2. COMMENT ÇA MARCHE ?

   a) On enregistre une fonction avec spl_autoload_register()
   b) Quand PHP rencontre une classe inconnue (ex: new User())
   c) PHP appelle automatiquement notre fonction avec le nom de la classe
   d) Notre fonction calcule le chemin du fichier et l'inclut
   e) La classe devient disponible

3. MAPPAGE NAMESPACE → DOSSIER

   Namespace                    →  Fichier
   ───────────────────────────────────────────────────────────
   App\Config\Database          →  src/Config/Database.php
   App\Models\User              →  src/Models/User.php
   App\Services\Auth            →  src/Services/Auth.php
   App\Services\Session         →  src/Services/Session.php
   App\Services\Validator       →  src/Services/Validator.php
   App\Utils\Response           →  src/Utils/Response.php

4. UTILISATION DANS LES FICHIERS PHP

   // Dans login.php
   require_once 'autoload.php';

   use App\Services\Auth;
   use App\Services\Session;
   use App\Utils\Response;

   // Maintenant on peut utiliser les classes directement
   Session::start();
   $result = Auth::login($email, $password);

5. AVANTAGES DE L'AUTOLOAD

   ✅ Plus besoin de require/include pour chaque classe
   ✅ Code plus propre et lisible
   ✅ Évite les oublis de require
   ✅ Évite les includes en double
   ✅ Standard PSR-4 (interopérable avec Composer)

6. NAMESPACE vs USE

   // Déclaration du namespace (dans la classe)
   namespace App\Config;
   class Database { }

   // Utilisation sans "use"
   $db = new App\Config\Database();

   // Utilisation avec "use" (plus court et lisible)
   use App\Config\Database;
   $db = new Database();

7. PSR-4 STANDARD

   PSR-4 est un standard PHP pour l'autoloading :
   - Utilisé par Composer
   - Mappage namespace → dossiers
   - Conventions de nommage
   - Interopérabilité entre projets

8. EXEMPLE COMPLET

   // autoload.php (ce fichier)
   spl_autoload_register(function ($class) {
       // ... logique d'autoload ...
   });

   // Dans login.php
   require_once 'autoload.php';

   use App\Services\Auth;
   use App\Services\Session;
   use App\Utils\Response;

   Session::start();

   if ($_SERVER['REQUEST_METHOD'] === 'POST') {
       $result = Auth::login($email, $password);

       if ($result['success']) {
           Response::redirectToHome();
       }
   }

9. DÉPANNAGE

   Si une classe n'est pas trouvée :
   - Vérifier le namespace dans la classe
   - Vérifier que le fichier est au bon endroit
   - Vérifier le nom du fichier (sensible à la casse)
   - Vérifier que autoload.php est bien inclus

===============================================================================
*/
