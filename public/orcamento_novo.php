<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

$clientes = $pdo->query("SELECT id,nome FROM clientes WHERE status='ativo'")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Novo Orçamento</title>
<link rel="stylesheet" href="../assets/css/system.css">


</head>
<body>

<h1>Novo Orçamento</h1>

<form method="post" action="orcamento_salvar.php">

<select name="cliente_id">
    <option value="">Cliente</option>
    <?php foreach ($clientes as $c): ?>
        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
    <?php endforeach; ?>
</select>

<select name="tipo_projeto" required>
    <option value="institucional">Site Institucional</option>
    <option value="landing">Landing Page</option>
    <option value="loja">Loja Virtual</option>
    <option value="sistema">Sistema Web</option>
</select>

<select name="tipo_design" required>
    <option value="simples">Simples</option>
    <option value="pro">Profissional</option>
    <option value="premium">Premium</option>
</select>

<select name="urgencia" required>
    <option value="normal">Normal</option>
    <option value="rapida">Rápida</option>
    <option value="urgente">Urgente</option>
</select>

<textarea name="descricao" placeholder="Descrição do orçamento"></textarea>

<input type="number" step="0.01" name="valor_estimado" required placeholder="Valor estimado">

<button>Salvar</button>
<a href="orcamentos.php">Cancelar</a>

</form>

</body>
</html>
