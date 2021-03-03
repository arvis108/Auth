<?php
$host = 'localhost';
$user = "root";
$password = 'root';
$database = "chat";

try {
    $conn = new PDO("mysql:host=$host;dbname=chat", $user,$password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die($e);
}
