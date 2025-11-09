<?php
// Script de test simple pour diagnostiquer le problème
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Simple</title>
</head>
<body>
    <h1>Test Page</h1>
    <p>Si vous voyez ce message UNE SEULE FOIS, le problème vient d'ailleurs.</p>
    <p>Si vous voyez ce message PLUSIEURS FOIS, il y a un problème de configuration.</p>
    <p>Timestamp: <?php echo date('Y-m-d H:i:s'); ?></p>
</body>
</html>
