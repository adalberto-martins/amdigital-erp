<?php
require "../app/auth/verifica_login.php";
if ($_SESSION['usuario_nivel'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Novo Usuário</title>
</head>
<body>

<h1>Novo Usuário</h1>

<form method="post" action="usuario_salvar.php">
    <label>Nome</label><br>
    <input type="text" name="nome" required><br><br>

    <label>Email</label><br>
    <input type="email" name="email" required><br><br>

    <label>Senha</label><br>
    <input type="password" name="senha" required><br><br>

    <label>Nível</label><br>
    <select name="nivel">
        <option value="admin">Admin</option>
        <option value="usuario">Usuário</option>
    </select><br><br>

    <label>Status</label><br>
    <select name="status">
        <option value="ativo">Ativo</option>
        <option value="inativo">Inativo</option>
    </select><br><br>

    <button type="submit">Salvar</button>
    <a href="usuarios.php">Cancelar</a>
</form>

</body>
</html>
