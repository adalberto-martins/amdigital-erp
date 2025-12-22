<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: clientes.php");
    exit;
}

$pdo->prepare("DELETE FROM clientes WHERE id = ?")->execute([$id]);

header("Location: clientes.php");
exit;
