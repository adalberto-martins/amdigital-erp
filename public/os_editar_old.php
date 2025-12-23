<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: ordens_servico.php");
    exit;
}

// Buscar OS
$stmt = $pdo->prepare("
    SELECT os.*, c.nome AS cliente, p.nome AS projeto
    FROM ordens_servico os
    JOIN clientes c ON c.id = os.cliente_id
    LEFT JOIN projetos p ON p.id = os.projeto_id
    WHERE os.id = ?
");
$stmt->execute([$id]);
$os = $stmt->fetch();

if (!$os) {
    header("Location: ordens_servico.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Editar OS</title>

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

select {
    width: 100%;
    padding: 8px;
    margin-top: 4px;
    margin-bottom: 12px;
}
</style>
</head>

<body>

<div class="form-container">
<h2>Editar Ordem de Serviço #<?= $os['id'] ?></h2>

<p><strong>Cliente:</strong> <?= htmlspecialchars($os['cliente']) ?></p>
<p><strong>Projeto:</strong> <?= htmlspecialchars($os['projeto'] ?? '—') ?></p>
<p><strong>Valor:</strong> R$ <?= number_format($os['valor'],2,',','.') ?></p>
<p><strong>Descrição:</strong><br><?= nl2br(htmlspecialchars($os['descricao'])) ?></p>

<form method="post" action="os_atualizar.php">

<input type="hidden" name="id" value="<?= $os['id'] ?>">

<label>Status</label>
<select name="status">
    <option value="aberta" <?= $os['status']=='aberta'?'selected':'' ?>>Aberta</option>
    <option value="executando" <?= $os['status']=='executando'?'selected':'' ?>>Executando</option>
    <option value="concluida" <?= $os['status']=='concluida'?'selected':'' ?>>Concluída</option>
    <option value="cancelada" <?= $os['status']=='cancelada'?'selected':'' ?>>Cancelada</option>
</select>

<button type="submit">Atualizar Status</button>
<a href="ordens_servico.php">Voltar</a>

</form>
</div>

</body>
</html>

