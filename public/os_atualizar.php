<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

try {
    // =========================
    // DADOS RECEBIDOS
    // =========================
    $id              = $_POST['id'] ?? null;
    $status          = $_POST['status'] ?? null;
    $statusAnterior  = $_POST['status_anterior'] ?? null;
    $descricao       = $_POST['descricao'] ?? '';
    $valor           = $_POST['valor'] ?? 0;
    $dataInicio      = $_POST['data_inicio'] ?? null;
    $dataFim         = $_POST['data_fim'] ?? null;

    if (!$id || !$status) {
        throw new Exception("Dados inválidos.");
    }

    // =========================
    // INICIAR TRANSAÇÃO
    // =========================
    $pdo->beginTransaction();

    // =========================
    // BUSCAR OS ATUAL (GARANTIA)
    // =========================
    $stmt = $pdo->prepare("
        SELECT 
            os.*,
            p.id AS projeto_id,
            c.id AS cliente_id
        FROM ordens_servico os
        JOIN projetos p ON p.id = os.projeto_id
        JOIN clientes c ON c.id = os.cliente_id
        WHERE os.id = ?
        FOR UPDATE
    ");
    $stmt->execute([$id]);
    $os = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$os) {
        throw new Exception("OS não encontrada.");
    }

    // =========================
    // ATUALIZAR OS
    // =========================
    $stmt = $pdo->prepare("
        UPDATE ordens_servico SET
            status = ?,
            descricao = ?,
            valor = ?,
            data_inicio = ?,
            data_fim = ?
        WHERE id = ?
    ");
    $stmt->execute([
        $status,
        $descricao,
        $valor,
        $dataInicio ?: null,
        $dataFim ?: null,
        $id
    ]);

    // =========================
    // GERAR FINANCEIRO (SÓ 1 VEZ)
    // =========================
    if ($statusAnterior !== 'concluida' && $status === 'concluida') {

        // Verificar se já existe lançamento financeiro para esta OS
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM financeiro
            WHERE os_id = ?
        ");
        $stmt->execute([$id]);
        $jaExiste = $stmt->fetchColumn();

        if ($jaExiste == 0) {

            $stmt = $pdo->prepare("
                INSERT INTO financeiro
                (tipo, cliente_id, projeto_id, os_id, descricao, valor, vencimento, status)
                VALUES
                ('receber', ?, ?, ?, ?, ?, CURDATE(), 'pendente')
            ");
            $stmt->execute([
                $os['cliente_id'],
                $os['projeto_id'],
                $id,
                'Ordem de Serviço #' . $id,
                $valor
            ]);
        }
    }

    // =========================
    // FINALIZAR TRANSAÇÃO
    // =========================
    $pdo->commit();

    header("Location: ordens_servico.php");
    exit;

} catch (Exception $e) {

    // Reverter tudo se algo falhar
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    die("Erro ao atualizar OS: " . $e->getMessage());
}
