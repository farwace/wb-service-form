<?php
// db.php

$host = 'localhost';
$dbname = 'wb-test';
$user = 'wb-test-admin';
$pass = 'jW2rhT1gxQWVJOAn';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    //die('Connection failed: ' . $e->getMessage());
    die('Connection failed');
}
?>
