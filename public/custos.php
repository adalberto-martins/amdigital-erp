<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

/* =========================
   FILTROS
========================= */
$tipo       = $_GET['tipo'] ?? '';
$recorrente = $_GET['recorrente'] ?? '';
$busca      = $_GET['busca'] ?? '';

$sql = "
    SELECT *
    FROM custos
    WHERE 1=1
";
$params = [];

if ($tipo) {
    $sql .= " AND tipo = ?";
    $params[] = $tipo;
}

if ($recorrente) {
    $sql .= " AND recorrente = ?";
    $params[] = $recorrente;
}

if ($busca) {
    $sql .= " AND (descricao LIKE ? OR categoria LIKE ?)";
    $params[] = "%$busca%";
    $params[] = "%$busca%";
}

$sql .= " ORDER BY data DESC, id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$custos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Custos</title>

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

.filtro input,
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
    background: #dc2626;
    color: #fff;
}

.btn-primary:hover {
    background: #b91c1c;
}

.btn-secondary {
    background: #e5e7eb;
    color: #374151;
}

.btn-secondary:hover {
    background: #d1d5db;
}

/* tabela */
.system-table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 10px rgba(0,0,0,.08);
}

.system-table th,
.system-table td {
    padding: 12px;
    border-bottom: 1px solid #e5e7eb;
}

.system-table thead {
    background: #f8fafc;
}

.linha-click {
    cursor: pointer;
}

.linha-click:hover {
    background: #f3f4f6;
}

/* badges */
.badge {
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: bold;
}

.badge-fixo {
    background: #e0f2fe;
    color: #0369a1;
}

.badge-variavel {
    background: #fff7ed;
    color: #9a3412;
}

.badge-sim {
    background: #fee2e2;
    color: #991b1b;
}

.badge-nao {
    background: #e5e7eb;
    color: #374151;
}
</style>
</head>

<body>
<div class="container">

<h1>Custos</h1>

<div class="botoes">
    <a href="custo_novo.php" class="btn btn-primary">➕ Novo Custo</a>
    <a href="index.php" class="btn btn-secondary">⬅ Voltar</a>
</div>

<form method="get" class="filtro">
    <input type="text" name="busca"
           value="<?= htmlspecialchars($busca) ?>"
           placeholder="Descrição ou categoria">

    <select name="tipo">
        <option value="">Todos os tipos</option>
        <option value="fixo" <?= $tipo=='fixo'?'selected':'' ?>>Fixo</option>
        <option value="variavel" <?= $tipo=='variavel'?'selected':'' ?>>Variável</option>
    </select>

    <select name="recorrente">
        <option value="">Recorrência</option>
        <option value="sim" <?= $recorrente=='sim'?'selected':'' ?>>Recorrente</option>
        <option value="nao" <?= $recorrente=='nao'?'selected':'' ?>>Não recorrente</option>
    </select>

    <button class="btn btn-primary">🔍 Buscar</button>
</form>

<table class="system-table">
<thead>
<tr>
    <th>ID</th>
    <th>Descrição</th>
    <th>Categoria</th>
    <th>Tipo</th>
    <th>Recorrente</th>
    <th>Data</th>
    <th>Valor</th>
</tr>
</thead>
<tbody>

<?php if (empty($custos)): ?>
<tr>
    <td colspan="7">Nenhum custo encontrado</td>
</tr>
<?php endif; ?>

<?php foreach ($custos as $c): ?>
<tr class="linha-click"
    onclick="window.location='custo_editar.php?id=<?= $c['id'] ?>'">

    <td><?= $c['id'] ?></td>
    <td><?= htmlspecialchars($c['descricao']) ?></td>
    <td><?= htmlspecialchars($c['categoria'] ?? '-') ?></td>
    <td>
        <span class="badge badge-<?= $c['tipo'] ?>">
            <?= strtoupper($c['tipo']) ?>
        </span>
    </td>
    <td>
        <span class="badge badge-<?= $c['recorrente'] ?>">
            <?= strtoupper($c['recorrente']) ?>
        </span>
    </td>
    <td><?= date('d/m/Y', strtotime($c['data'])) ?></td>
    <td>R$ <?= number_format($c['valor'],2,',','.') ?></td>
</tr>
<?php endforeach; ?>

</tbody>
</table>

</div>
</body>
</html>
