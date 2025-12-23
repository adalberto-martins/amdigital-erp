<?php
require "../app/auth/verifica_login.php";
require "../config/database.php";

$id         = $_POST['id'] ?? null;
$tipo       = $_POST['tipo'] ?? '';
$cliente_id = $_POST['cliente_id'] ?: null;
$projeto_id = $_POST['projeto_id'] ?: null;
$descricao  = $_POST['descricao'] ?? '';
$valor      = $_POST['valor'] ?? 0;
$vencimento = $_POST['vencimento'] ?? '';
$status     = $_POST['status'] ?? 'pendente';

if (!$id || $tipo === '' || $descricao === '' || $valor <= 0 || $vencimento === '') {
    header("Location: financeiro.php");
    exit;
}

$sql = "UPDATE financeiro
        SET tipo = ?, cliente_id = ?, projeto_id = ?, descricao = ?,
            valor = ?, vencimento = ?, status = ?
        WHERE id = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    $tipo,
    $cliente_id,
    $projeto_id,
    $descricao,
    $valor,
    $vencimento,
    $status,
    $id
]);

header("Location: financeiro.php");
exit;
