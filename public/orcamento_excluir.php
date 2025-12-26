<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: orcamentos.php");
    exit;
}

/* =========================
   BUSCA ORÇAMENTO
========================= */
$stmt = $pdo->prepare("
    SELECT status
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
   REGRA DE NEGÓCIO (PASSO 3)
========================= */
/*
 Somente orçamento em RASCUNHO
 pode ser excluído
*/
if ($orcamento['status'] !== 'rascunho') {
    header("Location: orcamentos.php");
    exit;
}

/* =========================
   EXCLUI ORÇAMENTO
========================= */
$stmt = $pdo->prepare("
    DELETE FROM orcamentos
    WHERE id = ?
");
$stmt->execute([$id]);

/* =========================
   REDIRECIONA
========================= */
header("Location: orcamentos.php");
exit;

