<?php
require "../app/auth/verifica_login.php";
require "../config/database.php";

$sql = "
    SELECT 
        p.id,
        p.nome AS projeto,
        p.tipo,
        p.valor,
        p.status,
        c.nome AS cliente
    FROM projetos p
    LEFT JOIN clientes c ON c.id = p.cliente_id
    ORDER BY p.nome
";

$stmt = $pdo->query($sql);
$projetos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Projetos</title>
</head>
<body>

<h1>Projetos</h1>

<a href="projeto_novo.php">➕ Novo Projeto</a>

<table border="1" cellpadding="8" cellspacing="0" style="margin-top:10px;">
<tr>
    <th>Projeto</th>
    <th>Cliente</th>
    <th>Tipo</th>
    <th>Valor</th>
    <th>Status</th>
    <th>Ações</th>
</tr>

<?php if (count($projetos) === 0): ?>
<tr>
    <td colspan="6">Nenhum projeto cadastrado.</td>
</tr>
<?php else: ?>
<?php foreach ($projetos as $p): ?>
<tr>
    <td><?= htmlspecialchars($p['projeto']) ?></td>
    <td><?= htmlspecialchars($p['cliente'] ?? '—') ?></td>
    <td><?= htmlspecialchars($p['tipo']) ?></td>
    <td>R$ <?= number_format($p['valor'], 2, ',', '.') ?></td>
    <td><?= htmlspecialchars($p['status']) ?></td>
    <td>
        <a href="projeto_editar.php?id=<?= $p['id'] ?>">✏️ Editar</a>
        <a href="projeto_excluir.php?id=<?= $p['id'] ?>"
   onclick="return confirm('Deseja excluir este projeto?')">
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
