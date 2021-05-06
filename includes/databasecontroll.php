<?php

$host = '127.0.0.1.';
$user = 'root';
$password = 'root';
$database = 'chat';

try {
    $conn = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $user,$password );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die($e);
}
