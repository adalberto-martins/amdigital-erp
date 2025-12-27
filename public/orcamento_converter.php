<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

/* =========================
   ID DO ORÇAMENTO
========================= */
$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: orcamentos.php");
    exit;
}

/* =========================
   BUSCA ORÇAMENTO
========================= */
$stmt = $pdo->prepare("SELECT * FROM orcamentos WHERE id = ?");
$stmt->execute([$id]);
$o = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$o || $o['status'] !== 'aprovado') {
    header("Location: orcamentos.php");
    exit;
}

/* =========================
   CRIA PROJETO
========================= */
$stmt = $pdo->prepare("
    INSERT INTO projetos
        (cliente_id, nome, tipo, descricao, valor, status, data_inicio)
    VALUES
        (?, ?, ?, ?, ?, 'em_andamento', CURDATE())
");

$stmt->execute([
    $o['cliente_id'],
    'Projeto - Orçamento #' . $o['id'],
    $o['tipo_projeto'],
    $o['descricao'],
    $o['valor_estimado']
]);

$projetoId = $pdo->lastInsertId();

/* =========================
   CRIA OS AUTOMÁTICA
========================= */
$stmt = $pdo->prepare("
    INSERT INTO ordens_servico
        (cliente_id, projeto_id, descricao, valor, status, data_inicio)
    VALUES
        (?, ?, ?, ?, 'aberta', CURDATE())
");

$stmt->execute([
    $o['cliente_id'],
    $projetoId,
    'Ordem de serviço inicial do projeto',
    $o['valor_estimado']
]);

/* =========================
   MARCA ORÇAMENTO COMO CONVERTIDO
========================= */
$stmt = $pdo->prepare("
    UPDATE orcamentos
    SET status = 'convertido'
    WHERE id = ?
");
$stmt->execute([$id]);

/* =========================
   REDIRECIONA
========================= */
header("Location: projetos.php");
exit;








