<?php
require "../app/auth/verifica_login.php";
require "../config/database.php";

// Dados do formulário
$nome = $_POST['nome'] ?? '';
$email = $_POST['email'] ?? '';
$telefone = $_POST['telefone'] ?? '';
$status = $_POST['status'] ?? 'ativo';

// Validação mínima
if ($nome === '') {
    header("Location: cliente_novo.php");
    exit;
}

// Inserção no banco
$sql = "INSERT INTO clientes (nome, email, telefone, status)
        VALUES (?, ?, ?, ?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$nome, $email, $telefone, $status]);

// Volta para listagem
header("Location: clientes.php");
exit;
