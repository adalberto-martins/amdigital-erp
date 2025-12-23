<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

/* =========================
   FILTROS
========================= */
$tipo   = $_GET['tipo']   ?? '';
$status = $_GET['status'] ?? '';
$busca  = $_GET['busca']  ?? '';

$sql = "
    SELECT 
        f.*,
        c.nome AS cliente,
        p.nome AS projeto
    FROM financeiro f
    LEFT JOIN clientes c ON c.id = f.cliente_id
    LEFT JOIN projetos p ON p.id = f.projeto_id
    WHERE 1=1
";
$params = [];

if ($tipo) {
    $sql .= " AND f.tipo = ?";
    $params[] = $tipo;
}

if ($status) {
    if ($status === 'vencido') {
        $sql .= " AND f.status = 'pendente' AND f.vencimento < CURDATE()";
    } else {
        $sql .= " AND f.status = ?";
        $params[] = $status;
    }
}

if ($busca) {
    $sql .= " AND (c.nome LIKE ? OR p.nome LIKE ? OR f.descricao LIKE ?)";
    $params[] = "%$busca%";
    $params[] = "%$busca%";
    $params[] = "%$busca%";
}

$sql .= " ORDER BY f.vencimento ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$lancamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Financeiro</title>

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
    background: #16a34a;
    color: #fff;
}

.btn-primary:hover {
    background: #15803d;
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

/* status visual */
.status-pago { color: #16a34a; font-weight: bold; }
.status-pendente { color: #f59e0b; font-weight: bold; }
.status-vencido { color: #dc2626; font-weight: bold; }
</style>
</head>

<body>
<div class="container">

<h1>Financeiro</h1>

<div class="botoes">
    <a href="financeiro_novo.php" class="btn btn-primary">➕ Novo Lançamento</a>
    <a href="index.php" class="btn btn-secondary">⬅ Voltar</a>
</div>

<form method="get" class="filtro">
    <select name="tipo">
        <option value="">Todos</option>
        <option value="receber" <?= $tipo=='receber'?'selected':'' ?>>Receber</option>
        <option value="pagar" <?= $tipo=='pagar'?'selected':'' ?>>Pagar</option>
    </select>

    <select name="status">
        <option value="">Todos</option>
        <option value="pendente" <?= $status=='pendente'?'selected':'' ?>>Pendente</option>
        <option value="pago" <?= $status=='pago'?'selected':'' ?>>Pago</option>
        <option value="vencido" <?= $status=='vencido'?'selected':'' ?>>Vencido</option>
    </select>

    <input type="text" name="busca"
           value="<?= htmlspecialchars($busca) ?>"
           placeholder="Cliente, projeto ou descrição">

    <button class="btn btn-primary">🔍 Buscar</button>
</form>

<table class="system-table">
<thead>
<tr>
    <th>ID</th>
    <th>Tipo</th>
    <th>Cliente</th>
    <th>Projeto</th>
    <th>Descrição</th>
    <th>Vencimento</th>
    <th>Valor</th>
    <th>Status</th>
</tr>
</thead>
<tbody>

<?php if (empty($lancamentos)): ?>
<tr>
    <td colspan="8">Nenhum lançamento encontrado</td>
</tr>
<?php endif; ?>

<?php foreach ($lancamentos as $f): 
    $statusVisual = $f['status'];
    if ($f['status'] === 'pendente' && $f['vencimento'] < date('Y-m-d')) {
        $statusVisual = 'vencido';
    }
?>
<tr class="linha-click"
    onclick="window.location='financeiro_editar.php?id=<?= $f['id'] ?>'">

    <td><?= $f['id'] ?></td>
    <td><?= strtoupper($f['tipo']) ?></td>
    <td><?= htmlspecialchars($f['cliente'] ?? '-') ?></td>
    <td><?= htmlspecialchars($f['projeto'] ?? '-') ?></td>
    <td><?= htmlspecialchars($f['descricao']) ?></td>
    <td><?= date('d/m/Y', strtotime($f['vencimento'])) ?></td>
    <td>R$ <?= number_format($f['valor'],2,',','.') ?></td>
    <td class="status-<?= $statusVisual ?>">
        <?= strtoupper($statusVisual) ?>
    </td>
</tr>
<?php endforeach; ?>

</tbody>
</table>

</div>
</body>
</html>
