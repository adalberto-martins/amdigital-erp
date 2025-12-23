<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

try {

    /* =========================
       DADOS RECEBIDOS
    ========================= */
    $id         = $_POST['id'] ?? null;
    $status     = $_POST['status'] ?? null;
    $valor      = $_POST['valor'] ?? null;
    $descricao  = $_POST['descricao'] ?? null;
    $vencimento = $_POST['vencimento'] ?? null;

    if (!$id || !$status) {
        throw new Exception("Dados inválidos.");
    }

    /* =========================
       BUSCAR LANÇAMENTO ATUAL
    ========================= */
    $stmt = $pdo->prepare("SELECT * FROM financeiro WHERE id = ?");
    $stmt->execute([$id]);
    $f = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$f) {
        throw new Exception("Lançamento não encontrado.");
    }

    $origemOS = !empty($f['os_id']);

    /* =========================
       INICIAR TRANSAÇÃO
    ========================= */
    $pdo->beginTransaction();

    /* =========================
       REGRAS DE ATUALIZAÇÃO
    ========================= */

    if ($origemOS) {
        // 🔒 Lançamento vindo da OS
        // Só pode alterar STATUS

        $stmt = $pdo->prepare("
            UPDATE financeiro
            SET status = ?
            WHERE id = ?
        ");
        $stmt->execute([$status, $id]);

    } else {
        // ✏️ Lançamento manual
        $stmt = $pdo->prepare("
            UPDATE financeiro
            SET status = ?, valor = ?, descricao = ?, vencimento = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $status,
            $valor,
            $descricao,
            $vencimento,
            $id
        ]);
    }

    /* =========================
       FINALIZAR
    ========================= */
    $pdo->commit();

    header("Location: financeiro.php");
    exit;

} catch (Exception $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    die("Erro ao atualizar lançamento financeiro: " . $e->getMessage());
}
