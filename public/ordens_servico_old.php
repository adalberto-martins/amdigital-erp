<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

$sql = "
SELECT 
    os.id,
    os.valor,
    os.status,
    os.criado_em,
    c.nome AS cliente,
    p.nome AS projeto
FROM ordens_servico os
JOIN clientes c ON c.id = os.cliente_id
LEFT JOIN projetos p ON p.id = os.projeto_id
ORDER BY os.id DESC
";

$ordens = $pdo->query($sql)->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Ordens de Serviço</title>

<style>
.table-container {
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0,0,0,.08);
}

.system-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

.system-table th {
    background: #f1f5f9;
    padding: 10px;
    text-align: left;
    border-bottom: 2px solid #e5e7eb;
}

.system-table td {
    padding: 10px;
    border-bottom: 1px solid #e5e7eb;
}

.badge {
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: bold;
}

.badge-aberta {
    background: #e0f2fe;
    color: #075985;
}

.badge-executando {
    background: #fef9c3;
    color: #854d0e;
}

.badge-concluida {
    background: #dcfce7;
    color: #166534;
}

.badge-cancelada {
    background: #fee2e2;
    color: #991b1b;
}
</style>
</head>

<body>

<div class="table-container">
<h2>Ordens de Serviço</h2>

<a href="os_nova.php">➕ Nova OS</a>

<table class="system-table">
<tr>
    <th>ID</th>
    <th>Cliente</th>
    <th>Projeto</th>
    <th>Valor</th>
    <th>Status</th>
    <th>Criado em</th>
    <th>Ações</th>
</tr>

<?php if (count($ordens) === 0): ?>
<tr>
    <td colspan="7">Nenhuma ordem de serviço cadastrada.</td>
</tr>
<?php else: ?>
<?php foreach ($ordens as $os): ?>
<tr>
    <td><?= $os['id'] ?></td>
    <td><?= htmlspecialchars($os['cliente'] ?? '') ?></td>
    <td><?= htmlspecialchars($os['projeto'] ?? '—') ?></td>
    <td>R$ <?= number_format($os['valor'], 2, ',', '.') ?></td>
    <td>
        <span class="badge badge-<?= $os['status'] ?>">
            <?= ucfirst($os['status']) ?>
        </span>
    </td>
    <td>
        <?= date('d/m/Y H:i', strtotime($os['criado_em'])) ?>
    </td>
<td>
    <a href="os_editar.php?id=<?= $os['id'] ?>">✏️</a>
    <a href="os_pdf.php?id=<?= $os['id'] ?>" target="_blank">📄</a>
    <a href="os_excluir.php?id=<?= $os['id'] ?>"
       onclick="return confirm('Deseja excluir esta OS?')">🗑</a>
</td>


</tr>
<?php endforeach; ?>
<?php endif; ?>
</table>

<br>
<a href="index.php">⬅ Voltar ao Dashboard</a>
</div>

</body>
</html>

