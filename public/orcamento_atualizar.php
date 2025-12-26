<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";
require __DIR__ . "/../app/helpers/orcamento_helper.php";

/* =========================
   VALIDA POST
========================= */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: orcamentos.php");
    exit;
}

$id = $_POST['id'] ?? null;
$descricao = $_POST['descricao'] ?? '';
$statusNovo = $_POST['status'] ?? '';

if (!$id) {
    header("Location: orcamentos.php");
    exit;
}

/* =========================
   BUSCA ORÇAMENTO ATUAL
========================= */
$stmt = $pdo->prepare("
    SELECT *
    FROM orcamentos
    WHERE id = ?
");
$stmt->execute([$id]);
$orcamento = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$orcamento) {
    header("Location: orcamentos.php");
    exit;
}

/* =========================
   REGRAS DE STATUS (PASSO 3)
========================= */
if (in_array($orcamento['status'], ['rejeitado', 'convertido'])) {
    header("Location: orcamentos.php");
    exit;
}

/* =========================
   VALIDA STATUS PERMITIDO
========================= */
$statusPermitidos = ['rascunho','enviado','aprovado','rejeitado'];

if (!in_array($statusNovo, $statusPermitidos)) {
    header("Location: orcamentos.php");
    exit;
}

/* =========================
   RECALCULA VALORES (PASSO 2)
========================= */
$calculo = calcularOrcamento((float)$orcamento['valor_estimado']);

$valor_estimado  = $calculo['valor_estimado'];
$lucro_estimado  = $calculo['lucro_estimado'];
$margem_estimada = $calculo['margem_estimada'];

/* =========================
   ATUALIZA ORÇAMENTO
========================= */
$stmt = $pdo->prepare("
    UPDATE orcamentos
    SET descricao = ?,
        status = ?,
        valor_estimado = ?,
        lucro_estimado = ?,
        margem_estimada = ?
    WHERE id = ?
");
$stmt->execute([
    $descricao,
    $statusNovo,
    $valor_estimado,
    $lucro_estimado,
    $margem_estimada,
    $id
]);

/* =========================
   REDIRECIONA
========================= */
header("Location: orcamentos.php");
exit;


