<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

$id = $_GET['id'] ?? null;
if (!$id) die("ID inválido");

$stmt = $pdo->prepare("DELETE FROM orcamentos WHERE id=?");
$stmt->execute([$id]);

header("Location: orcamentos.php");
