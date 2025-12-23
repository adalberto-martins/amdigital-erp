<?php
require "../app/auth/verifica_login.php";
require "../config/database.php";

$sql = "
    SELECT
        f.id,
        f.tipo,
        f.descricao,
        f.valor,
        f.vencimento,
        f.status,
        c.nome AS cliente,
        p.nome AS projeto
    FROM financeiro f
    LEFT JOIN clientes c ON c.id = f.cliente_id
    LEFT JOIN projetos p ON p.id = f.projeto_id
    ORDER BY f.vencimento DESC
";

$stmt = $pdo->query($sql);
$financeiro = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Financeiro</title>
</head>
<body>

<h1>Financeiro</h1>

<a href="financeiro_novo.php">➕ Novo Lançamento</a>

<table border="1" cellpadding="8" cellspacing="0" style="margin-top:10px;">
<tr>
    <th>Tipo</th>
    <th>Descrição</th>
    <th>Cliente</th>
    <th>Projeto</th>
    <th>Valor</th>
    <th>Vencimento</th>
    <th>Status</th>
    <th>Ações</th>
</tr>

<?php if (count($financeiro) === 0): ?>
<tr>
    <td colspan="8">Nenhum lançamento financeiro.</td>
</tr>
<?php else: ?>
<?php foreach ($financeiro as $f): ?>
<tr>
    <td><?= $f['tipo'] === 'receber' ? 'Receber' : 'Pagar' ?></td>
    <td><?= htmlspecialchars($f['descricao']) ?></td>
    <td><?= htmlspecialchars($f['cliente'] ?? '—') ?></td>
    <td><?= htmlspecialchars($f['projeto'] ?? '—') ?></td>
    <td>R$ <?= number_format($f['valor'], 2, ',', '.') ?></td>
    <td><?= date('d/m/Y', strtotime($f['vencimento'])) ?></td>
    <td><?= htmlspecialchars($f['status']) ?></td>
    <td>
        <a href="financeiro_editar.php?id=<?= $f['id'] ?>">✏️ Editar</a>
        <a href="financeiro_excluir.php?id=<?= $f['id'] ?>"
   onclick="return confirm('Deseja excluir este lançamento?')">
   🗑 Excluir
</a>
    </td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</table>


<br>
<a href="dashboard.php">⬅ Voltar ao Dashboard</a>

</body>
</html>
