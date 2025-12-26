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
   REGRA DE NEGÓCIO
========================= */
if ($orcamento['status'] !== 'aprovado') {
    header("Location: orcamentos.php");
    exit;
}

try {
    /* =========================
       TRANSAÇÃO
    ========================= */
    $pdo->beginTransaction();

    /* =========================
       CRIA PROJETO
    ========================= */
    $stmt = $pdo->prepare("
        INSERT INTO projetos
        (cliente_id, nome, tipo, descricao, valor, status, data_inicio)
        VALUES
        (?, ?, ?, ?, ?, 'ativo', CURDATE())
    ");
    $stmt->execute([
        $orcamento['cliente_id'],
        'Projeto - Orçamento #' . $orcamento['id'],
        $orcamento['tipo_projeto'],
        $orcamento['descricao'],
        $orcamento['valor_estimado']
    ]);

    /* =========================
       ATUALIZA ORÇAMENTO
========================= */
    $stmt = $pdo->prepare("
        UPDATE orcamentos
        SET status = 'convertido'
        WHERE id = ?
    ");
    $stmt->execute([$id]);

    $pdo->commit();

    /* =========================
       REDIRECIONA
    ========================= */
    header("Location: projetos.php");
    exit;

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    die("Erro ao converter orçamento em projeto: " . $e->getMessage());
}






