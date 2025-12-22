<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: projetos.php");
    exit;
}

$pdo->prepare("DELETE FROM projetos WHERE id = ?")->execute([$id]);

header("Location: projetos.php");
exit;
