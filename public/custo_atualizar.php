<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

try {

    /* =========================
       DADOS RECEBIDOS
    ========================= */
    $id              = $_POST['id'] ?? null;
    $descricao       = trim($_POST['descricao'] ?? '');
    $categoria       = trim($_POST['categoria'] ?? '');
    $tipo            = $_POST['tipo'] ?? null;
    $valor           = $_POST['valor'] ?? null;
    $recorrente      = $_POST['recorrente'] ?? 'nao';
    $dia_recorrencia = $_POST['dia_recorrencia'] ?? null;
    $data            = $_POST['data'] ?? null;

    /* =========================
       VALIDAÇÕES BÁSICAS
    ========================= */
    if (!$id || !$descricao || !$tipo || !$valor || !$data) {
        throw new Exception("Preencha todos os campos obrigatórios.");
    }

    if ($recorrente === 'sim') {
        if (!$dia_recorrencia || $dia_recorrencia < 1 || $dia_recorrencia > 31) {
            throw new Exception("Informe um dia válido para recorrência.");
        }
    } else {
        $dia_recorrencia = null;
    }

    /* =========================
       VERIFICAR EXISTÊNCIA
    ========================= */
    $stmt = $pdo->prepare("SELECT id FROM custos WHERE id = ?");
    $stmt->execute([$id]);
    if (!$stmt->fetch()) {
        throw new Exception("Custo não encontrado.");
    }

    /* =========================
       ATUALIZAR
    ========================= */
    $stmt = $pdo->prepare("
        UPDATE custos SET
            descricao = ?,
            categoria = ?,
            tipo = ?,
            valor = ?,
            recorrente = ?,
            dia_recorrencia = ?,
            data = ?
        WHERE id = ?
    ");

    $stmt->execute([
        $descricao,
        $categoria ?: null,
        $tipo,
        $valor,
        $recorrente,
        $dia_recorrencia,
        $data,
        $id
    ]);

    /* =========================
       FINALIZAR
    ========================= */
    header("Location: custos.php");
    exit;

} catch (Exception $e) {
    die("Erro ao atualizar custo: " . $e->getMessage());
}
