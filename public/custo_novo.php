<?php
require "../app/auth/verifica_login.php";
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Novo Custo</title>
</head>
<body>

<h1>Novo Custo</h1>

<form method="post" action="custo_salvar.php">

    <label>Descrição</label><br>
    <input type="text" name="descricao" required><br><br>

    <label>Categoria</label><br>
    <input type="text" name="categoria" placeholder="Ex: Hospedagem, Internet"><br><br>

    <label>Tipo</label><br>
    <select name="tipo" required>
        <option value="fixo">Fixo</option>
        <option value="variavel">Variável</option>
    </select><br><br>

    <label>Valor</label><br>
    <input type="number" name="valor" step="0.01" required><br><br>

    <label>Data</label><br>
    <input type="date" name="data" required><br><br>

    <label>Recorrente</label><br>
    <select name="recorrente">
        <option value="nao">Não</option>
        <option value="sim">Sim</option>
    </select><br><br>

    <label>Dia de recorrência (se recorrente)</label><br>
    <input type="number" name="dia_recorrencia" min="1" max="31"><br><br>

    <button type="submit">Salvar</button>
    <a href="custos.php">Cancelar</a>

</form>

</body>
</html>
