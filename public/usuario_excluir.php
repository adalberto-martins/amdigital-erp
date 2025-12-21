<?php
require "../app/auth/verifica_login.php";

// Somente admin
if ($_SESSION['usuario_nivel'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

require "../config/database.php";

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
