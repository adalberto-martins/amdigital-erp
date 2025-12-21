<?php
require __DIR__ . "/../app/auth/seguranca.php";
require "../config/database.php";

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: clientes.php");
    exit;
}

$sql = "SELECT * FROM clientes WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$cliente = $stmt->fetch();

if (!$cliente) {
    header("Location: clientes.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Cliente</title>
</head>
<body>

<h1>Editar Cliente</h1>

<form method="post" action="cliente_atualizar.php">
    <input type="hidden" name="id" value="<?= $cliente['id'] ?>">

    <label>Nome</label><br>
    <input type="text" name="nome" required value="<?= htmlspecialchars($cliente['nome']) ?>"><br><br>

    <label>Email</label><br>
    <input type="email" name="email" value="<?= htmlspecialchars($cliente['email']) ?>"><br><br>

    <label>Telefone</label><br>
    <input type="text" name="telefone" value="<?= htmlspecialchars($cliente['telefone']) ?>"><br><br>

    <label>Status</label><br>
    <select name="status">
        <option value="ativo" <?= $cliente['status']=='ativo'?'selected':'' ?>>Ativo</option>
        <option value="prospect" <?= $cliente['status']=='prospect'?'selected':'' ?>>Prospect</option>
        <option value="inativo" <?= $cliente['status']=='inativo'?'selected':'' ?>>Inativo</option>
    </select><br><br>

    <button type="submit">Atualizar</button>
    <a href="clientes.php">Cancelar</a>
</form>

</body>
</html>
