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
   REGRA DE NEGÓCIO (PASSO 3)
========================= */
/*
 Somente orçamento APROVADO
 pode ser convertido em projeto
*/
if ($orcamento['status'] !== 'aprovado') {
    header("Location: orcamentos.php");
    exit;
}

/* =========================
   PROTEÇÃO CONTRA DUPLICIDADE
========================= */
if (!empty($orcamento['projeto_id'])) {
    header("Location: projetos.php");
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
        (?, ?, ?, ?, ?, 'em_andamento', CURDATE())
    ");
    $stmt->execute([
        $orcamento['cliente_id'],
        'Projeto - Orçamento #' . $orcamento['id'],
        $orcamento['tipo_projeto'],
        $orcamento['descricao'],
        $orcamento['valor_estimado']
    ]);

    $projetoId = $pdo->lastInsertId();

    /* =========================
       ATUALIZA ORÇAMENTO
    ========================= */
    $stmt = $pdo->prepare("
        UPDATE orcamentos
        SET status = 'convertido',
            projeto_id = ?
        WHERE id = ?
    ");
    $stmt->execute([$projetoId, $id]);

    /* =========================
       FINALIZA TRANSAÇÃO
    ========================= */
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
    die("ERRO REAL: " . $e->getMessage());
}




