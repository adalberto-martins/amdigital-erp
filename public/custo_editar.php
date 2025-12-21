<?php
require "../app/auth/verifica_login.php";
require "../config/database.php";

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: custos.php");
    exit;
}

// Buscar custo
$sql = "SELECT * FROM custos WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$custo = $stmt->fetch();

if (!$custo) {
    header("Location: custos.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Custo</title>
</head>
<body>

<h1>Editar Custo</h1>

<form method="post" action="custo_atualizar.php">
    <input type="hidden" name="id" value="<?= $custo['id'] ?>">

    <label>Descrição</label><br>
    <input type="text" name="descricao" required
           value="<?= htmlspecialchars($custo['descricao']) ?>"><br><br>

    <label>Categoria</label><br>
    <input type="text" name="categoria"
           value="<?= htmlspecialchars($custo['categoria']) ?>"><br><br>

    <label>Tipo</label><br>
    <select name="tipo" required>
        <option value="fixo" <?= $custo['tipo']=='fixo'?'selected':'' ?>>Fixo</option>
        <option value="variavel" <?= $custo['tipo']=='variavel'?'selected':'' ?>>Variável</option>
    </select><br><br>

    <label>Valor</label><br>
    <input type="number" name="valor" step="0.01" required
           value="<?= htmlspecialchars($custo['valor']) ?>"><br><br>

    <label>Data</label><br>
    <input type="date" name="data" required
           value="<?= $custo['data'] ?>"><br><br>

    <label>Recorrente</label><br>
    <select name="recorrente">
        <option value="nao" <?= $custo['recorrente']=='nao'?'selected':'' ?>>Não</option>
        <option value="sim" <?= $custo['recorrente']=='sim'?'selected':'' ?>>Sim</option>
    </select><br><br>

    <label>Dia de recorrência</label><br>
    <input type="number" name="dia_recorrencia" min="1" max="31"
           value="<?= htmlspecialchars($custo['dia_recorrencia']) ?>"><br><br>

    <button type="submit">Atualizar</button>
    <a href="custos.php">Cancelar</a>
</form>

</body>
</html>
