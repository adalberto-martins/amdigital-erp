<?php
require "../app/auth/verifica_login.php";
require "../config/database.php";

$sql = "SELECT * FROM custos ORDER BY data DESC";
$stmt = $pdo->query($sql);
$custos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Custos</title>
</head>
<body>

<h1>Custos</h1>

<a href="custo_novo.php">➕ Novo Custo</a>

<table border="1" cellpadding="8" cellspacing="0" style="margin-top:10px;">
<tr>
    <th>Descrição</th>
    <th>Categoria</th>
    <th>Tipo</th>
    <th>Valor</th>
    <th>Data</th>
    <th>Recorrente</th>
    <th>Ações</th>
</tr>

<?php if (count($custos) === 0): ?>
<tr>
    <td colspan="7">Nenhum custo cadastrado.</td>
</tr>
<?php else: ?>
<?php foreach ($custos as $c): ?>
<tr>
    <td><?= htmlspecialchars($c['descricao']) ?></td>
    <td><?= htmlspecialchars($c['categoria']) ?></td>
    <td><?= $c['tipo'] === 'fixo' ? 'Fixo' : 'Variável' ?></td>
    <td>R$ <?= number_format($c['valor'], 2, ',', '.') ?></td>
    <td><?= date('d/m/Y', strtotime($c['data'])) ?></td>
    <td><?= $c['recorrente'] === 'sim' ? 'Sim' : 'Não' ?></td>
    <td>
        <a href="custo_editar.php?id=<?= $c['id'] ?>">✏️ Editar</a>
    </td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</table>


<br>
<a href="dashboard.php">⬅ Voltar ao Dashboard</a>

</body>
</html>
