<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

/* filtro */
$categoria = $_GET['categoria'] ?? '';

$sql = "SELECT * FROM servicos";
$params = [];

if ($categoria) {
    $sql .= " WHERE categoria = ?";
    $params[] = $categoria;
}

$sql .= " ORDER BY categoria, nome";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* categorias para o filtro */
$categorias = $pdo->query("
    SELECT DISTINCT categoria 
    FROM servicos 
    ORDER BY categoria
")->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Serviços</title>

<style>
body {
    font-family: Arial, sans-serif;
    background: #f1f5f9;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
}

h1 {
    margin-bottom: 10px;
}

/* filtros */
.filtro {
    display: flex;
    gap: 10px;
    margin-bottom: 15px;
}

.filtro select {
    padding: 8px;
}

/* botões */
.botoes {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
}

.btn {
    padding: 10px 18px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
    transition: .2s;
}

.btn-primary {
    background: #8b5cf6;
    color: #fff;
}

.btn-primary:hover {
    background: #7c3aed;
}

.btn-secondary {
    background: #e5e7eb;
    color: #374151;
}

.btn-secondary:hover {
    background: #d1d5db;
}

/* tabela */
.table-wrapper {
    display: flex;
    justify-content: center;
}

.system-table {
    width: 100%;
    max-width: 1200px;
    border-collapse: collapse;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 10px rgba(0,0,0,.08);
}

.system-table th,
.system-table td {
    padding: 12px;
    border-bottom: 1px solid #e5e7eb;
    text-align: left;
}

.system-table thead {
    background: #f8fafc;
}

.linha-click {
    cursor: pointer;
    transition: background-color .15s ease;
}

.linha-click:hover {
    background-color: #f3f4f6;
}

.status-ativo {
    color: #16a34a;
    font-weight: bold;
}

.status-inativo {
    color: #dc2626;
    font-weight: bold;
}
</style>
</head>

<body>
<div class="container">

<h1>Serviços</h1>

<div class="botoes">
    <a href="servico_novo.php" class="btn btn-primary">➕ Novo Serviço</a>
    <a href="index.php" class="btn btn-secondary">⬅ Dashboard</a>
</div>

<form class="filtro" method="get">
    <select name="categoria" onchange="this.form.submit()">
        <option value="">Todas as categorias</option>
        <?php foreach ($categorias as $cat): ?>
            <option value="<?= htmlspecialchars($cat) ?>"
                <?= $cat === $categoria ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat) ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>

<div class="table-wrapper">
<table class="system-table">
<thead>
<tr>
    <th>Nome</th>
    <th>Categoria</th>
    <th>Valor base</th>
    <th>Status</th>
</tr>
</thead>
<tbody>
<?php foreach ($servicos as $s): ?>
<tr class="linha-click"
    onclick="location.href='servico_editar.php?id=<?= $s['id'] ?>'">
    <td><?= htmlspecialchars($s['nome']) ?></td>
    <td><?= htmlspecialchars($s['categoria']) ?></td>
    <td>R$ <?= number_format($s['valor_base'],2,',','.') ?></td>
    <td class="<?= $s['ativo']=='sim' ? 'status-ativo' : 'status-inativo' ?>">
        <?= $s['ativo']=='sim' ? 'Ativo' : 'Inativo' ?>
    </td>
</tr>
<?php endforeach; ?>

<?php if (!$servicos): ?>
<tr>
    <td colspan="4">Nenhum serviço encontrado</td>
</tr>
<?php endif; ?>
</tbody>
</table>
</div>

</div>
</body>
</html>

