<?php
require "../app/auth/verifica_login.php";

// Somente admin
if ($_SESSION['usuario_nivel'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

require "../config/database.php";

$usuarios = $pdo->query("SELECT id, nome, email, nivel, status FROM usuarios ORDER BY nome")->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Usuários</title>
</head>
<body>

<h1>Usuários</h1>

<a href="usuario_novo.php">➕ Novo Usuário</a>

<table border="1" cellpadding="8" cellspacing="0">
<tr>
    <th>Nome</th>
    <th>Email</th>
    <th>Nível</th>
    <th>Status</th>
    <th>Ações</th>
</tr>

<?php foreach ($usuarios as $u): ?>
<tr>
    <td><?= htmlspecialchars($u['nome']) ?></td>
    <td><?= htmlspecialchars($u['email']) ?></td>
    <td><?= htmlspecialchars($u['nivel']) ?></td>
    <td><?= htmlspecialchars($u['status']) ?></td>
    <td>
        <a href="usuario_editar.php?id=<?= $u['id'] ?>">✏️ Editar</a>
    </td>
</tr>
<?php endforeach; ?>
</table>

<br>
<a href="dashboard.php">⬅ Voltar</a>

</body>
</html>
