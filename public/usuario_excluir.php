<?php
require __DIR__ . "/../app/auth/seguranca.php";
exigeAdmin();

require __DIR__ . "/../config/database.php";

// Garantia defensiva
if (!isset($pdo)) {
    die("Erro crítico: conexão com banco não encontrada.");
}

$id = $_GET['id'] ?? null;

// Não permite excluir a si mesmo
if (!$id || $id == $_SESSION['usuario_id']) {
    header("Location: usuarios.php");
    exit;
}

// Excluir usuário
$sql = "DELETE FROM usuarios WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);

header("Location: usuarios.php");
exit;
