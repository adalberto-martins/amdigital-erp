<?php
require __DIR__ . "/../app/auth/seguranca.php";
exigeAdmin();

require __DIR__ . "/../config/database.php";

// BUSCA DE USUÁRIOS (ESTA LINHA ESTAVA FALTANDO OU NÃO EXECUTAVA)
$sql = "SELECT id, nome, email, nivel, status FROM usuarios ORDER BY nome";
$stmt = $pdo->query($sql);
$usuarios = $stmt->fetchAll();
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

<?php if (count($usuarios) === 0): ?>
<tr>
    <td colspan="5">Nenhum usuário cadastrado.</td>
</tr>
<?php else: ?>
<?php foreach ($usuarios as $u): ?>
<tr>
    <td><?= htmlspecialchars($u['nome']) ?></td>
    <td><?= htmlspecialchars($u['email']) ?></td>
    <td><?= htmlspecialchars($u['nivel']) ?></td>
    <td><?= htmlspecialchars($u['status']) ?></td>
    <td>
        <a href="usuario_editar.php?id=<?= $u['id'] ?>">✏️ Editar</a>

        <?php if ($u['id'] != $_SESSION['usuario_id']): ?>
            | <a href="usuario_excluir.php?id=<?= $u['id'] ?>"
                 onclick="return confirm('Deseja excluir este usuário?')">
                 🗑 Excluir
              </a>
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</table>

<br>
<a href="index.php">⬅ Voltar</a>

</body>
</html>

