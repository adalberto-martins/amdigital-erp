<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

/* somente POST */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ordens_servico.php");
    exit;
}

$id         = $_POST['id'] ?? null;
$status     = $_POST['status'] ?? null;
$descricao  = $_POST['descricao'] ?? '';
$valor      = $_POST['valor'] ?? 0;
$data_fim   = $_POST['data_fim'] ?? null;

if (!$id || !$status) {
    header("Location: ordens_servico.php");
    exit;
}

/* BUSCA OS */
$stmt = $pdo->prepare("SELECT * FROM ordens_servico WHERE id = ?");
$stmt->execute([$id]);
$os = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$os) {
    header("Location: ordens_servico.php");
    exit;
}

/* ATUALIZA OS */
$stmt = $pdo->prepare("
    UPDATE ordens_servico SET
        descricao = ?,
        valor = ?,
        status = ?,
        data_fim = ?
    WHERE id = ?
");

$stmt->execute([
    $descricao,
    $valor,
    $status,
    $data_fim,
    $id
]);

/* =========================
   FINANCEIRO AUTOMÁTICO
========================= */
if ($status === 'concluida') {

    /* evita duplicar lançamento */
    $stmt = $pdo->prepare("
        SELECT id FROM financeiro
        WHERE os_id = ? AND tipo = 'receber'
    ");
    $stmt->execute([$id]);

    if (!$stmt->fetch()) {

        $vencimento = date('Y-m-d', strtotime('+7 days'));

        $stmt = $pdo->prepare("
            INSERT INTO financeiro
                (tipo, cliente_id, projeto_id, os_id, descricao, valor, vencimento, status)
            VALUES
                ('receber', ?, ?, ?, ?, ?, ?, 'pendente')
        ");

        $stmt->execute([
            $os['cliente_id'],
            $os['projeto_id'],
            $os['id'],
            'Recebimento da OS #' . $os['id'],
            $os['valor'],
            $vencimento
        ]);
    }
}

/* REDIRECIONA */
header("Location: ordens_servico.php");
exit;


