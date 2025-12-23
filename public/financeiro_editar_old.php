<?php
require "../app/auth/verifica_login.php";
require "../config/database.php";

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: financeiro.php");
    exit;
}

// Buscar lançamento
$sql = "SELECT * FROM financeiro WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$lanc = $stmt->fetch();

if (!$lanc) {
    header("Location: financeiro.php");
    exit;
}

// Clientes e projetos
$clientes = $pdo->query("SELECT id, nome FROM clientes ORDER BY nome")->fetchAll();
$projetos = $pdo->query("SELECT id, nome FROM projetos ORDER BY nome")->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Lançamento</title>
</head>
<body>

<h1>Editar Lançamento Financeiro</h1>

<form method="post" action="financeiro_atualizar.php">
    <input type="hidden" name="id" value="<?= $lanc['id'] ?>">

    <label>Tipo</label><br>
    <select name="tipo" required>
        <option value="receber" <?= $lanc['tipo']=='receber'?'selected':'' ?>>Receber</option>
        <option value="pagar" <?= $lanc['tipo']=='pagar'?'selected':'' ?>>Pagar</option>
    </select><br><br>

    <label>Cliente</label><br>
    <select name="cliente_id">
        <option value="">—</option>
        <?php foreach ($clientes as $c): ?>
            <option value="<?= $c['id'] ?>"
                <?= $c['id']==$lanc['cliente_id']?'selected':'' ?>>
                <?= htmlspecialchars($c['nome']) ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <label>Projeto</label><br>
    <select name="projeto_id">
        <option value="">—</option>
        <?php foreach ($projetos as $p): ?>
            <option value="<?= $p['id'] ?>"
                <?= $p['id']==$lanc['projeto_id']?'selected':'' ?>>
                <?= htmlspecialchars($p['nome']) ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <label>Descrição</label><br>
    <input type="text" name="descricao" required
           value="<?= htmlspecialchars($lanc['descricao']) ?>"><br><br>

    <label>Valor</label><br>
    <input type="number" name="valor" step="0.01" required
           value="<?= htmlspecialchars($lanc['valor']) ?>"><br><br>

    <label>Vencimento</label><br>
    <input type="date" name="vencimento" required
           value="<?= $lanc['vencimento'] ?>"><br><br>

    <label>Status</label><br>
    <select name="status">
        <option value="pendente" <?= $lanc['status']=='pendente'?'selected':'' ?>>Pendente</option>
        <option value="pago" <?= $lanc['status']=='pago'?'selected':'' ?>>Pago</option>
        <option value="atrasado" <?= $lanc['status']=='atrasado'?'selected':'' ?>>Atrasado</option>
    </select><br><br>

    <button type="submit">Atualizar</button>
    <a href="financeiro.php">Cancelar</a>
</form>

</body>
</html>
