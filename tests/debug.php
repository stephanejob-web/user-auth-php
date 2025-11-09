<?php
// Script de diagnostic
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== DIAGNOSTIC SCRIPT ===<br><br>";

// Test 1: Session
echo "1. Testing session...<br>";
session_start();
echo "✓ Session started successfully<br><br>";

// Test 2: Database connection
echo "2. Testing database connection...<br>";
try {
    require_once 'db.php';
    echo "✓ Database connected successfully<br><br>";
} catch (Exception $e) {
    echo "✗ Database ERROR: " . $e->getMessage() . "<br><br>";
}

// Test 3: Header inclusion
echo "3. Testing header inclusion...<br>";
echo "BEFORE HEADER<br>";
include_once 'header.php';
echo "AFTER HEADER<br><br>";

// Test 4: Check if we reach here
echo "4. Script execution completed<br>";
echo "If you see this message ONCE, the problem is isolated.<br>";
echo "If you see this message MULTIPLE TIMES, header.php is the issue.<br>";
?>
