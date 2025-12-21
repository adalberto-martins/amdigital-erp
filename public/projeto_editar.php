<?php
require "../app/auth/verifica_login.php";
require "../config/database.php";

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: projetos.php");
    exit;
}

// Buscar projeto
$sql = "SELECT * FROM projetos WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$projeto = $stmt->fetch();

if (!$projeto) {
    header("Location: projetos.php");
    exit;
}

// Buscar clientes
$sqlClientes = "SELECT id, nome FROM clientes ORDER BY nome";
$clientes = $pdo->query($sqlClientes)->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Projeto</title>
</head>
<body>

<h1>Editar Projeto</h1>

<form method="post" action="projeto_atualizar.php">
    <input type="hidden" name="id" value="<?= $projeto['id'] ?>">

    <label>Cliente</label><br>
    <select name="cliente_id" required>
        <?php foreach ($clientes as $c): ?>
            <option value="<?= $c['id'] ?>"
                <?= $c['id'] == $projeto['cliente_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['nome']) ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <label>Nome do Projeto</label><br>
    <input type="text" name="nome" required
           value="<?= htmlspecialchars($projeto['nome']) ?>"><br><br>

    <label>Tipo</label><br>
    <input type="text" name="tipo"
           value="<?= htmlspecialchars($projeto['tipo']) ?>"><br><br>

    <label>Valor</label><br>
    <input type="number" name="valor" step="0.01"
           value="<?= htmlspecialchars($projeto['valor']) ?>"><br><br>

    <label>Status</label><br>
    <select name="status">
        <option value="orcamento" <?= $projeto['status']=='orcamento'?'selected':'' ?>>Orçamento</option>
        <option value="andamento" <?= $projeto['status']=='andamento'?'selected':'' ?>>Em andamento</option>
        <option value="finalizado" <?= $projeto['status']=='finalizado'?'selected':'' ?>>Finalizado</option>
    </select><br><br>

    <button type="submit">Atualizar</button>
    <a href="projetos.php">Cancelar</a>
</form>

</body>
</html>
