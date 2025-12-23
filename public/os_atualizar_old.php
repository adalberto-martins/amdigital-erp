<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

$id     = $_POST['id'] ?? null;
$status = $_POST['status'] ?? null;

if (!$id || !$status) {
    header("Location: ordens_servico.php");
    exit;
}

// Atualizar status da OS
$sql = "UPDATE ordens_servico SET status = ? WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$status, $id]);

header("Location: ordens_servico.php");
exit;
