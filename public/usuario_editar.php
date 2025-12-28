<?php
require __DIR__ . "/../app/auth/seguranca.php";
exigeAdmin();

require __DIR__ . "/../config/database.php";

if ($_GET['id'] == $_SESSION['usuario_id']) {
    die("Você não pode excluir seu próprio usuário");
}

// Validar ID
$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: usuarios.php");
    exit;
}

// Buscar usuário
$sql = "SELECT id, nome, email, nivel, status FROM usuarios WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$usuario = $stmt->fetch();

// Se não existir
if (!$usuario) {
    header("Location: usuarios.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuário</title>
</head>
<body>

<h1>Editar Usuário</h1>

<form method="post" action="usuario_atualizar.php">
    <input type="hidden" name="id" value="<?= $usuario['id'] ?>">

    <label>Nome</label><br>
    <input type="text" name="nome" required
           value="<?= htmlspecialchars($usuario['nome']) ?>"><br><br>

    <label>Email</label><br>
    <input type="email" name="email" required
           value="<?= htmlspecialchars($usuario['email']) ?>"><br><br>

    <label>Nível</label><br>
    <select name="nivel">
        <option value="admin" <?= $usuario['nivel'] === 'admin' ? 'selected' : '' ?>>
            Admin
        </option>
        <option value="usuario" <?= $usuario['nivel'] === 'usuario' ? 'selected' : '' ?>>
            Usuário
        </option>
    </select><br><br>

    <label>Status</label><br>
    <select name="status">
        <option value="ativo" <?= $usuario['status'] === 'ativo' ? 'selected' : '' ?>>
            Ativo
        </option>
        <option value="inativo" <?= $usuario['status'] === 'inativo' ? 'selected' : '' ?>>
            Inativo
        </option>
    </select><br><br>

    <button type="submit">Atualizar</button>
    <a href="usuarios.php">Cancelar</a>
</form>

</body>
</html>
