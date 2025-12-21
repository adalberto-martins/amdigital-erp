<?php
session_start();
require "../config/database.php";

// Dados do formulário
$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';

// Validação simples
if ($email === '' || $senha === '') {
    header("Location: login.php");
    exit;
}

// Consulta no banco
$sql = "SELECT * FROM usuarios WHERE email = ? AND senha = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$email, $senha]);
$usuario = $stmt->fetch();

if ($usuario) {
    // Login OK → cria sessão
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['usuario_nome'] = $usuario['nome'];

    header("Location: index.php");
    exit;
} else {
    // Login inválido
    header("Location: login.php");
    exit;
}
