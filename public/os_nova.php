<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

// Buscar clientes
$clientes = $pdo->query(
    "SELECT id, nome FROM clientes WHERE status = 'ativo' ORDER BY nome"
)->fetchAll();

// Buscar projetos
$projetos = $pdo->query(
    "SELECT id, nome FROM projetos ORDER BY nome"
)->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Nova Ordem de Serviço</title>

<style>
.form-container {
    background: #fff;
    padding: 20px;
    max-width: 600px;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0,0,0,.08);
}

label {
    font-weight: bold;
}

input, select, textarea {
    width: 100%;
    padding: 8px;
    margin-top: 4px;
    margin-bottom: 12px;
}

button {
    padding: 10px 16px;
    font-weight: bold;
}

.actions a {
    margin-left: 10px;
}
</style>
</head>

<body>

<div class="form-container">
<h2>Nova Ordem de Serviço</h2>

<form method="post" action="os_salvar.php">

<label>Cliente</label>
<select name="cliente_id" required>
    <option value="">Selecione</option>
    <?php foreach ($clientes as $c): ?>
        <option value="<?= $c['id'] ?>">
            <?= htmlspecialchars($c['nome']) ?>
        </option>
    <?php endforeach; ?>
</select>

<label>Projeto (opcional)</label>
<select name="projeto_id">
    <option value="">—</option>
    <?php foreach ($projetos as $p): ?>
        <option value="<?= $p['id'] ?>">
            <?= htmlspecialchars($p['nome']) ?>
        </option>
    <?php endforeach; ?>
</select>

<label>Descrição do Serviço</label>
<textarea name="descricao" rows="4" required></textarea>

<label>Valor do Serviço (R$)</label>
<input type="number" name="valor" step="0.01" required>

<div class="actions">
    <button type="submit">Salvar OS</button>
    <a href="ordens_servico.php">Cancelar</a>
</div>

</form>
</div>

</body>
</html>

