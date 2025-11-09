<?php
/**
 * ============================================================================
 * SCRIPT DE TEST DE L'ENVIRONNEMENT
 * ============================================================================
 *
 * Ce script vérifie que votre environnement PHP est correctement configuré
 * pour faire fonctionner ce projet.
 *
 * Utilisation :
 * - Via navigateur : http://localhost:8000/test_env.php
 * - Via CLI : php test_env.php
 */

// Style CSS inline pour un affichage propre
echo "<style>
    body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    .test-item { margin: 15px 0; padding: 10px; border-left: 4px solid #ccc; }
    .test-item.ok { border-color: green; background: #f0fff0; }
    .test-item.fail { border-color: red; background: #fff0f0; }
    h1 { color: #333; }
    hr { margin: 30px 0; }
    code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
</style>";

echo "<h1>🔍 Test de l'environnement PHP</h1>";
echo "<p>Ce script vérifie que votre environnement est prêt pour le projet.</p>";
echo "<hr>";

$all_ok = true;

// ============================================================================
// TEST 1 : Version de PHP
// ============================================================================

echo "<div class='test-item";
$php_version = phpversion();
$php_ok = version_compare($php_version, '7.4.0', '>=');
echo $php_ok ? " ok'>" : " fail'>";
echo $php_ok ? "✅" : "❌";
echo " <strong>Version PHP :</strong> $php_version";
if ($php_ok) {
    echo " <span class='success'>(OK)</span>";
} else {
    echo " <span class='error'>(Minimum requis : 7.4)</span>";
    $all_ok = false;
}
echo "</div>";

// ============================================================================
// TEST 2 : Extension PDO
// ============================================================================

echo "<div class='test-item";
$pdo_ok = extension_loaded('pdo');
echo $pdo_ok ? " ok'>" : " fail'>";
echo $pdo_ok ? "✅" : "❌";
echo " <strong>Extension PDO :</strong> ";
if ($pdo_ok) {
    echo "<span class='success'>Installée</span>";
} else {
    echo "<span class='error'>NON installée (OBLIGATOIRE)</span>";
    $all_ok = false;
}
echo "</div>";

// ============================================================================
// TEST 3 : Driver PDO SQLite
// ============================================================================

echo "<div class='test-item";
$sqlite_ok = extension_loaded('pdo_sqlite');
echo $sqlite_ok ? " ok'>" : " fail'>";
echo $sqlite_ok ? "✅" : "❌";
echo " <strong>Driver PDO SQLite :</strong> ";
if ($sqlite_ok) {
    echo "<span class='success'>Installé</span>";
} else {
    echo "<span class='error'>NON installé (requis pour SQLite)</span>";
    echo "<br><small>Activez <code>extension=pdo_sqlite</code> dans php.ini</small>";
    $all_ok = false;
}
echo "</div>";

// ============================================================================
// TEST 4 : Driver PDO MySQL (optionnel)
// ============================================================================

echo "<div class='test-item";
$mysql_ok = extension_loaded('pdo_mysql');
echo $mysql_ok ? " ok'>" : "'>";
echo $mysql_ok ? "✅" : "⚠️";
echo " <strong>Driver PDO MySQL :</strong> ";
if ($mysql_ok) {
    echo "<span class='success'>Installé</span>";
} else {
    echo "<span class='warning'>NON installé (optionnel - seulement si vous utilisez MySQL)</span>";
}
echo "</div>";

// ============================================================================
// TEST 5 : Fonctions de hashage de mot de passe
// ============================================================================

echo "<div class='test-item";
$hash_ok = function_exists('password_hash') && function_exists('password_verify');
echo $hash_ok ? " ok'>" : " fail'>";
echo $hash_ok ? "✅" : "❌";
echo " <strong>Fonctions de hashage (password_hash) :</strong> ";
if ($hash_ok) {
    echo "<span class='success'>Disponibles</span>";
} else {
    echo "<span class='error'>NON disponibles (requis pour la sécurité)</span>";
    $all_ok = false;
}
echo "</div>";

// ============================================================================
// TEST 6 : Permissions d'écriture
// ============================================================================

echo "<div class='test-item";
$dir = __DIR__;
$write_ok = is_writable($dir);
echo $write_ok ? " ok'>" : " fail'>";
echo $write_ok ? "✅" : "❌";
echo " <strong>Permissions d'écriture (dossier actuel) :</strong> ";
if ($write_ok) {
    echo "<span class='success'>OK</span>";
} else {
    echo "<span class='error'>Pas de permission d'écriture</span>";
    echo "<br><small>Exécutez : <code>chmod 755 " . htmlspecialchars($dir) . "</code></small>";
    $all_ok = false;
}
echo "</div>";

// ============================================================================
// TEST 7 : Vérifier si db.php existe
// ============================================================================

echo "<div class='test-item";
$db_exists = file_exists(__DIR__ . '/db.php');
echo $db_exists ? " ok'>" : "'>";
echo $db_exists ? "✅" : "⚠️";
echo " <strong>Fichier de connexion (db.php) :</strong> ";
if ($db_exists) {
    echo "<span class='success'>Trouvé</span>";
} else {
    echo "<span class='warning'>NON trouvé</span>";
    echo "<br><small>N'oubliez pas de renommer <code>db_sqlite.php</code> en <code>db.php</code></small>";
}
echo "</div>";

// ============================================================================
// TEST 8 : Vérifier si la base de données existe
// ============================================================================

echo "<div class='test-item";
$db_file_exists = file_exists(__DIR__ . '/database.db');
echo $db_file_exists ? " ok'>" : "'>";
echo $db_file_exists ? "✅" : "ℹ️";
echo " <strong>Base de données (database.db) :</strong> ";
if ($db_file_exists) {
    $size = filesize(__DIR__ . '/database.db');
    echo "<span class='success'>Trouvée (" . number_format($size) . " octets)</span>";
} else {
    echo "<span>Non créée</span>";
    echo "<br><small>Exécutez <code>init_db.php</code> pour créer la base de données</small>";
}
echo "</div>";

// ============================================================================
// RÉSULTAT FINAL
// ============================================================================

echo "<hr>";
echo "<h2>";
if ($all_ok) {
    echo "✅ <span class='success'>Tous les tests obligatoires sont passés !</span>";
} else {
    echo "❌ <span class='error'>Certains tests obligatoires ont échoué</span>";
}
echo "</h2>";

if ($all_ok) {
    echo "<p>Votre environnement est prêt. Prochaines étapes :</p>";
    echo "<ol>";
    if (!$db_exists) {
        echo "<li>Renommer <code>db_sqlite.php</code> en <code>db.php</code></li>";
    }
    if (!$db_file_exists) {
        echo "<li>Aller sur <a href='init_db.php'>init_db.php</a> pour créer la base de données</li>";
    }
    echo "<li>Aller sur <a href='index.php'>index.php</a> pour démarrer l'application</li>";
    echo "</ol>";
} else {
    echo "<p><strong>Action requise :</strong> Corrigez les erreurs ci-dessus avant de continuer.</p>";
}

echo "<hr>";
echo "<h3>📋 Informations supplémentaires</h3>";
echo "<ul>";
echo "<li><strong>OS :</strong> " . php_uname('s') . " " . php_uname('r') . "</li>";
echo "<li><strong>PHP SAPI :</strong> " . php_sapi_name() . "</li>";
echo "<li><strong>Dossier du projet :</strong> " . __DIR__ . "</li>";
echo "<li><strong>Fichier php.ini :</strong> " . php_ini_loaded_file() . "</li>";
echo "</ul>";

echo "<hr>";
echo "<p><small>Script de test - <a href='README.md'>Retour au README</a></small></p>";
?>
