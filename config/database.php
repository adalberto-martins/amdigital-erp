<?php

function conexao() {
    $host = 'localhost';
    $db   = 'amdigital';
    $user = 'root';
    $pass = '';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // FORÇA UTF-8 REAL
    $pdo->exec("SET NAMES utf8mb4");

    return $pdo;
}

