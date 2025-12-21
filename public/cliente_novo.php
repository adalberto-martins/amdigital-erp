<?php
require "../app/auth/verifica_login.php";
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Novo Cliente</title>
</head>
<body>

<h1>Novo Cliente</h1>

<form method="post" action="cliente_salvar.php">

    <label>Nome</label><br>
    <input type="text" name="nome" required><br><br>

    <label>Email</label><br>
    <input type="email" name="email"><br><br>

    <label>Telefone</label><br>
    <input type="text" name="telefone"><br><br>

    <label>Status</label><br>
    <select name="status">
        <option value="ativo">Ativo</option>
        <option value="prospect">Prospect</option>
        <option value="inativo">Inativo</option>
    </select><br><br>

    <button type="submit">Salvar</button>
    <a href="clientes.php">Cancelar</a>

</form>

</body>
</html>
