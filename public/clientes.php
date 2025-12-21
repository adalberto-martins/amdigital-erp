<?php
require "../app/auth/verifica_login.php";
require "../config/database.php";

// Busca clientes
$sql = "SELECT * FROM clientes ORDER BY nome";
$stmt = $pdo->query($sql);
$clientes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Clientes</title>
</head>
<body>

<h1>Clientes</h1>

<a href="cliente_novo.php">➕ Novo Cliente</a>

<table border="1" cellpadding="8" cellspacing="0" style="margin-top:10px;">
    <tr>
        <th>Nome</th>
        <th>Email</th>
        <th>Telefone</th>
        <th>Status</th>
        <th>Ações</th>
    </tr>

    <?php if (count($clientes) === 0): ?>
        <tr>
            <td colspan="5">Nenhum cliente cadastrado.</td>
        </tr>
    <?php else: ?>
        <?php foreach ($clientes as $c): ?>
        <tr>
            <td><?= htmlspecialchars($c['nome']) ?></td>
            <td><?= htmlspecialchars($c['email']) ?></td>
            <td><?= htmlspecialchars($c['telefone']) ?></td>
            <td><?= htmlspecialchars($c['status']) ?></td>
            <td>
                <a href="cliente_editar.php?id=<?= $c['id'] ?>">✏️ Editar</a>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</table>


<br>
<a href="dashboard.php">⬅ Voltar ao Dashboard</a>

</body>
</html>
