<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

$id = $_GET['id'] ?? null;
if (!$id) die("Orçamento inválido");

$stmt = $pdo->prepare("
    SELECT o.*, c.nome AS cliente
    FROM orcamentos o
    LEFT JOIN clientes c ON c.id = o.cliente_id
    WHERE o.id = ?
");
$stmt->execute([$id]);
$orcamento = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$orcamento) die("Orçamento não encontrado");

$clientes = $pdo->query("SELECT id,nome FROM clientes WHERE status='ativo'")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Editar Orçamento</title>
<link rel="stylesheet" href="../assets/css/system.css">

</head>
<body>

<h1>Editar Orçamento #<?= $orcamento['id'] ?></h1>

<form method="post" action="orcamento_atualizar.php">

<input type="hidden" name="id" value="<?= $orcamento['id'] ?>">

<select name="cliente_id">
    <option value="">Cliente</option>
    <?php foreach ($clientes as $c): ?>
        <option value="<?= $c['id'] ?>" <?= $c['id']==$orcamento['cliente_id']?'selected':'' ?>>
            <?= htmlspecialchars($c['nome']) ?>
        </option>
    <?php endforeach; ?>
</select>

<select name="tipo_projeto">
    <?php foreach (['institucional','landing','loja','sistema'] as $t): ?>
        <option value="<?= $t ?>" <?= $t==$orcamento['tipo_projeto']?'selected':'' ?>>
            <?= ucfirst($t) ?>
        </option>
    <?php endforeach; ?>
</select>

<select name="tipo_design">
    <?php foreach (['simples','pro','premium'] as $d): ?>
        <option value="<?= $d ?>" <?= $d==$orcamento['tipo_design']?'selected':'' ?>>
            <?= ucfirst($d) ?>
        </option>
    <?php endforeach; ?>
</select>

<select name="urgencia">
    <?php foreach (['normal','rapida','urgente'] as $u): ?>
        <option value="<?= $u ?>" <?= $u==$orcamento['urgencia']?'selected':'' ?>>
            <?= ucfirst($u) ?>
        </option>
    <?php endforeach; ?>
</select>

<textarea name="descricao"><?= htmlspecialchars($orcamento['descricao']) ?></textarea>

<p>
Valor estimado: <strong>R$ <?= number_format($orcamento['valor_estimado'],2,',','.') ?></strong><br>
Lucro: R$ <?= number_format($orcamento['lucro_estimado'],2,',','.') ?><br>
Margem: <?= number_format($orcamento['margem_estimada'],2,',','.') ?>%
</p>

<select name="status">
    <?php foreach (['rascunho','enviado','aprovado','rejeitado'] as $s): ?>
        <option value="<?= $s ?>" <?= $s==$orcamento['status']?'selected':'' ?>>
            <?= ucfirst($s) ?>
        </option>
    <?php endforeach; ?>
</select>

<br><br>
<button>Salvar Alterações</button>
<a href="orcamentos.php">Cancelar</a>

</form>

</body>
</html>
