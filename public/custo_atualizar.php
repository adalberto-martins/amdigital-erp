<?php
require "../app/auth/verifica_login.php";
require "../config/database.php";

$id              = $_POST['id'] ?? null;
$descricao       = $_POST['descricao'] ?? '';
$categoria       = $_POST['categoria'] ?? '';
$tipo            = $_POST['tipo'] ?? '';
$valor           = $_POST['valor'] ?? 0;
$data            = $_POST['data'] ?? '';
$recorrente      = $_POST['recorrente'] ?? 'nao';
$dia_recorrencia = $_POST['dia_recorrencia'] ?: null;

if (!$id || $descricao === '' || $tipo === '' || $valor <= 0 || $data === '') {
    header("Location: custos.php");
    exit;
}

$sql = "UPDATE custos
        SET descricao = ?, categoria = ?, tipo = ?, valor = ?,
            data = ?, recorrente = ?, dia_recorrencia = ?
        WHERE id = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    $descricao,
    $categoria,
    $tipo,
    $valor,
    $data,
    $recorrente,
    $dia_recorrencia,
    $id
]);

header("Location: custos.php");
exit;
