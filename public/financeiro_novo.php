<?php
require "../app/auth/verifica_login.php";
require "../config/database.php";

// Clientes
$clientes = $pdo->query("SELECT id, nome FROM clientes ORDER BY nome")->fetchAll();

// Projetos
$projetos = $pdo->query("SELECT id, nome FROM projetos ORDER BY nome")->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Novo Lançamento</title>
</head>
<body>

<h1>Novo Lançamento Financeiro</h1>

<form method="post" action="financeiro_salvar.php">

    <label>Tipo</label><br>
    <select name="tipo" required>
        <option value="receber">Receber</option>
        <option value="pagar">Pagar</option>
    </select><br><br>

    <label>Cliente</label><br>
    <select name="cliente_id">
        <option value="">—</option>
        <?php foreach ($clientes as $c): ?>
            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
        <?php endforeach; ?>
    </select><br><br>

    <label>Projeto</label><br>
    <select name="projeto_id">
        <option value="">—</option>
        <?php foreach ($projetos as $p): ?>
            <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nome']) ?></option>
        <?php endforeach; ?>
    </select><br><br>

    <label>Descrição</label><br>
    <input type="text" name="descricao" required><br><br>

    <label>Valor</label><br>
    <input type="number" name="valor" step="0.01" required><br><br>

    <label>Vencimento</label><br>
    <input type="date" name="vencimento" required><br><br>

    <label>Status</label><br>
    <select name="status">
        <option value="pendente">Pendente</option>
        <option value="pago">Pago</option>
        <option value="atrasado">Atrasado</option>
    </select><br><br>

    <button type="submit">Salvar</button>
    <a href="financeiro.php">Cancelar</a>

</form>

</body>
</html>
