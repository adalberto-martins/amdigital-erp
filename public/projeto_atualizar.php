<?php
require "../app/auth/verifica_login.php";
require "../config/database.php";

$id         = $_POST['id'] ?? null;
$cliente_id = $_POST['cliente_id'] ?? '';
$nome       = $_POST['nome'] ?? '';
$tipo       = $_POST['tipo'] ?? '';
$valor      = $_POST['valor'] ?? 0;
$status     = $_POST['status'] ?? 'orcamento';

if (!$id || $cliente_id === '' || $nome === '') {
    header("Location: projetos.php");
    exit;
}

$sql = "UPDATE projetos
        SET cliente_id = ?, nome = ?, tipo = ?, valor = ?, status = ?
        WHERE id = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    $cliente_id,
    $nome,
    $tipo,
    $valor,
    $status,
    $id
]);

header("Location: projetos.php");
exit;
