<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

// Captura dos dados
$cliente_id = $_POST['cliente_id'] ?? null;
$projeto_id = $_POST['projeto_id'] ?: null;
$descricao  = $_POST['descricao'] ?? '';
$valor      = $_POST['valor'] ?? null;

// Validação básica
if (!$cliente_id || !$descricao || !$valor) {
    header("Location: os_nova.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| 1️⃣ SALVAR ORDEM DE SERVIÇO
|--------------------------------------------------------------------------
*/
$sqlOS = "INSERT INTO ordens_servico
    (cliente_id, projeto_id, descricao, valor)
VALUES (?, ?, ?, ?)";

$stmtOS = $pdo->prepare($sqlOS);
$stmtOS->execute([
    $cliente_id,
    $projeto_id,
    $descricao,
    $valor
]);

$os_id = $pdo->lastInsertId();

/*
|--------------------------------------------------------------------------
| 2️⃣ INTEGRAR COM FINANCEIRO (A RECEBER)
|--------------------------------------------------------------------------
*/
$sqlFin = "INSERT INTO financeiro
    (tipo, cliente_id, projeto_id, descricao, valor, vencimento, status)
VALUES
    ('receber', ?, ?, ?, ?, CURDATE(), 'pendente')";

$stmtFin = $pdo->prepare($sqlFin);
$stmtFin->execute([
    $cliente_id,
    $projeto_id,
    "OS #{$os_id} - {$descricao}",
    $valor
]);

/*
|--------------------------------------------------------------------------
| 3️⃣ REDIRECIONAR
|--------------------------------------------------------------------------
*/
header("Location: ordens_servico.php");
exit;
