<?php
require "../app/auth/verifica_login.php";
require "../config/database.php";

// Buscar clientes para o select
$sql = "SELECT id, nome FROM clientes ORDER BY nome";
$stmt = $pdo->query($sql);
$clientes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Novo Projeto</title>
</head>
<body>

<h1>Novo Projeto</h1>

<form method="post" action="projeto_salvar.php">

    <label>Cliente</label><br>
    <select name="cliente_id" required>
        <option value="">Selecione</option>
        <?php foreach ($clientes as $c): ?>
            <option value="<?= $c['id'] ?>">
                <?= htmlspecialchars($c['nome']) ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <label>Nome do Projeto</label><br>
    <input type="text" name="nome" required><br><br>

    <label>Tipo</label><br>
    <input type="text" name="tipo" placeholder="Site, Landing Page, Sistema"><br><br>

    <label>Valor</label><br>
    <input type="number" name="valor" step="0.01"><br><br>

    <label>Status</label><br>
    <select name="status">
        <option value="orcamento">Orçamento</option>
        <option value="andamento">Em andamento</option>
        <option value="finalizado">Finalizado</option>
    </select><br><br>

    <button type="submit">Salvar</button>
    <a href="projetos.php">Cancelar</a>

</form>

</body>
</html>
