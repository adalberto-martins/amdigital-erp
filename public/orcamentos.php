<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

$orcamentos = $pdo->query("
    SELECT o.*, c.nome AS cliente
    FROM orcamentos o
    LEFT JOIN clientes c ON c.id = o.cliente_id
    ORDER BY o.id DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Orçamentos</title>
</head>
<body>

<h1>Orçamentos</h1>

<a href="orcamento_novo.php">➕ Novo Orçamento</a>

<table border="1" width="100%">
<tr>
    <th>ID</th>
    <th>Cliente</th>
    <th>Status</th>
    <th>Valor</th>
    <th>Lucro</th>
    <th>Margem</th>
</tr>

<?php foreach ($orcamentos as $o): ?>
<tr onclick="location.href='orcamento_editar.php?id=<?= $o['id'] ?>'">
    <td><?= $o['id'] ?></td>
    <td><?= htmlspecialchars($o['cliente'] ?? '—') ?></td>
    <td><?= strtoupper($o['status']) ?></td>
    <td>R$ <?= number_format($o['valor_estimado'],2,',','.') ?></td>
    <td>R$ <?= number_format($o['lucro_estimado'],2,',','.') ?></td>
    <td><?= number_format($o['margem_estimada'],2,',','.') ?>%</td>
</tr>
<?php endforeach; ?>

</table>

</body>
</html>
