<?php
$host = 'localhost';
$user = "";
$password = '';
$database = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=chat;charset=utf8", $user,$password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die($e);
}
