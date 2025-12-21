<?php
require "../app/auth/verifica_login.php";
require "../config/database.php";

// Dados do formulário
$tipo       = $_POST['tipo'] ?? '';
$cliente_id = $_POST['cliente_id'] ?: null;
$projeto_id = $_POST['projeto_id'] ?: null;
$descricao  = $_POST['descricao'] ?? '';
$valor      = $_POST['valor'] ?? 0;
$vencimento = $_POST['vencimento'] ?? '';
$status     = $_POST['status'] ?? 'pendente';

// Validação mínima
if ($tipo === '' || $descricao === '' || $valor <= 0 || $vencimento === '') {
    header("Location: financeiro_novo.php");
    exit;
}

// Inserção
$sql = "INSERT INTO financeiro 
        (tipo, cliente_id, projeto_id, descricao, valor, vencimento, status)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    $tipo,
    $cliente_id,
    $projeto_id,
    $descricao,
    $valor,
    $vencimento,
    $status
]);

header("Location: financeiro.php");
exit;
