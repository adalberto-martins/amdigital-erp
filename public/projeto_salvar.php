<?php
require "../app/auth/verifica_login.php";
require "../config/database.php";

// Dados do formulário
$cliente_id = $_POST['cliente_id'] ?? '';
$nome       = $_POST['nome'] ?? '';
$tipo       = $_POST['tipo'] ?? '';
$valor      = $_POST['valor'] ?? 0;
$status     = $_POST['status'] ?? 'orcamento';

// Validação mínima
if ($cliente_id === '' || $nome === '') {
    header("Location: projeto_novo.php");
    exit;
}

// Inserção no banco
$sql = "INSERT INTO projetos (cliente_id, nome, tipo, valor, status)
        VALUES (?, ?, ?, ?, ?)";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    $cliente_id,
    $nome,
    $tipo,
    $valor,
    $status
]);

// Volta para listagem
header("Location: projetos.php");
exit;
