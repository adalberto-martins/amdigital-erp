<?php
require "../app/auth/verifica_login.php";
if ($_SESSION['usuario_nivel'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

require "../config/database.php";

$nome   = $_POST['nome'] ?? '';
$email  = $_POST['email'] ?? '';
$senha  = $_POST['senha'] ?? '';
$nivel  = $_POST['nivel'] ?? 'usuario';
$status = $_POST['status'] ?? 'ativo';

if ($nome === '' || $email === '' || $senha === '') {
    header("Location: usuario_novo.php");
    exit;
}

$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

$sql = "INSERT INTO usuarios (nome, email, senha, nivel, status)
        VALUES (?, ?, ?, ?, ?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$nome, $email, $senhaHash, $nivel, $status]);

header("Location: usuarios.php");
exit;
