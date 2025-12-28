<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

$id = $_GET['id'] ?? null;
if (!$id) die("Orçamento inválido");

$pdo->beginTransaction();

/* buscar orçamento */
$orc = $pdo->prepare("SELECT * FROM orcamentos WHERE id=?");
$orc->execute([$id]);
$o = $orc->fetch(PDO::FETCH_ASSOC);

if (!$o || $o['status'] !== 'aprovado') {
    die("Orçamento não aprovado");
}

/* criar OS */
$stmt = $pdo->prepare("
    INSERT INTO ordens_servico 
        (cliente_id, descricao, valor, status)
    VALUES (?, ?, ?, 'aberta')
");

$stmt->execute([
    $o['cliente_id'],
    $o['descricao'],
    $o['valor_estimado']
]);

$osId = $pdo->lastInsertId();

/* copiar serviços */
$servs = $pdo->prepare("
    SELECT * FROM orcamento_servicos WHERE orcamento_id=?
");
$servs->execute([$id]);

foreach ($servs as $s) {
    $stmt = $pdo->prepare("
        INSERT INTO os_servicos
            (os_id, servico_id, quantidade, valor_unitario)
        VALUES (?, ?, ?, ?)
    ");

    $stmt->execute([
        $osId,
        $s['servico_id'],
        $s['quantidade'],
        $s['valor_unitario']
    ]);
}

/* marcar orçamento como convertido */
$pdo->prepare("
    UPDATE orcamentos SET status='convertido' WHERE id=?
")->execute([$id]);

$pdo->commit();

header("Location: ordens_servico.php");
exit;









