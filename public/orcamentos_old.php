<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

/* =========================
   BUSCA DOS ORÇAMENTOS
========================= */
$orcamentos = $pdo->query("
    SELECT o.*, c.nome AS cliente
    FROM orcamentos o
    LEFT JOIN clientes c ON c.id = o.cliente_id
    ORDER BY o.id DESC
")->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   FUNÇÃO COR STATUS
========================= */
function corStatus($status) {
    return match($status) {
        'rascunho'   => '#64748b',
        'enviado'    => '#2563eb',
        'aprovado'   => '#16a34a',
        'convertido' => '#0f766e',
        'rejeitado'  => '#dc2626',
        default      => '#475569'
    };
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Orçamentos</title>

<style>
body {
    font-family: Arial, sans-serif;
    background: #f1f5f9;
}

.container {
    max-width: 1200px;
    margin: auto;
}

h1 {
    margin-bottom: 20px;
}

.botoes {
    margin-bottom: 15px;
}

.botoes a {
    text-decoration: none;
    padding: 10px 14px;
    background: #2563eb;
    color: #fff;
    border-radius: 8px;
    font-weight: bold;
}

table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
}

thead {
    background: #e5e7eb;
}

th, td {
    padding: 12px;
    text-align: left;
}

tbody tr {
    cursor: pointer;
    transition: background .2s;
}

tbody tr:hover {
    background: #f1f5f9;
}

th {
    font-size: 13px;
    color: #374151;
}

td {
    font-size: 14px;
}
</style>
</head>

<body>
<div class="container">

<h1>Orçamentos</h1>

<div class="botoes">
    <a href="orcamento_novo.php">➕ Novo Orçamento</a>
    <a href="index.php" style="background:#64748b;">⬅ Dashboard</a>
</div>

<table>
<thead>
<tr>
    <th>ID</th>
    <th>Cliente</th>
    <th>Status</th>
    <th>Valor</th>
    <th>Lucro</th>
    <th>Margem</th>
</tr>
</thead>

<tbody>
<?php if (count($orcamentos) === 0): ?>
<tr>
    <td colspan="6" style="text-align:center;color:#64748b;">
        Nenhum orçamento cadastrado
    </td>
</tr>
<?php endif; ?>

<?php foreach ($orcamentos as $o): ?>
<tr onclick="location.href='orcamento_editar.php?id=<?= $o['id'] ?>'">

    <td><?= $o['id'] ?></td>

    <td><?= htmlspecialchars($o['cliente'] ?? '—') ?></td>

    <td style="color:<?= corStatus($o['status']) ?>; font-weight:bold;">
        <?= strtoupper($o['status']) ?>
    </td>

    <td>
        R$ <?= number_format((float)$o['valor_estimado'],2,',','.') ?>
    </td>

    <td>
        R$ <?= number_format((float)$o['lucro_estimado'],2,',','.') ?>
    </td>

    <td>
        <?= number_format((float)$o['margem_estimada'],2,',','.') ?>%
    </td>

</tr>
<?php endforeach; ?>
</tbody>
</table>

</div>
</body>
</html>

