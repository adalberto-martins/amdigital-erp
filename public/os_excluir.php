<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: ordens_servico.php");
    exit;
}

// Excluir somente a OS (financeiro permanece)
$stmt = $pdo->prepare("DELETE FROM ordens_servico WHERE id = ?");
$stmt->execute([$id]);

header("Location: ordens_servico.php");
exit;
