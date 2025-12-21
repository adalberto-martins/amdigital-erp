<?php
require "../app/auth/verifica_login.php";
require "../config/database.php";

// Dados do formulário
$descricao       = $_POST['descricao'] ?? '';
$categoria       = $_POST['categoria'] ?? '';
$tipo            = $_POST['tipo'] ?? '';
$valor           = $_POST['valor'] ?? 0;
$data            = $_POST['data'] ?? '';
$recorrente      = $_POST['recorrente'] ?? 'nao';
$dia_recorrencia = $_POST['dia_recorrencia'] ?: null;

// Validação mínima
if ($descricao === '' || $tipo === '' || $valor <= 0 || $data === '') {
    header("Location: custo_novo.php");
    exit;
}

// Inserção no banco
$sql = "INSERT INTO custos 
        (descricao, categoria, tipo, valor, data, recorrente, dia_recorrencia)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    $descricao,
    $categoria,
    $tipo,
    $valor,
    $data,
    $recorrente,
    $dia_recorrencia
]);

// Volta para listagem
header("Location: custos.php");
exit;
