<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

/* =========================
   FILTROS
========================= */
$busca  = $_GET['busca']  ?? '';
$status = $_GET['status'] ?? '';

$where  = [];
$params = [];

if ($busca !== '') {
    $where[] = "(c.nome LIKE ? OR o.descricao LIKE ?)";
    $params[] = "%$busca%";
    $params[] = "%$busca%";
}

if ($status !== '') {
    $where[] = "o.status = ?";
    $params[] = $status;
}

$sql = "
    SELECT o.*, c.nome AS cliente
    FROM orcamentos o
    LEFT JOIN clientes c ON c.id = o.cliente_id
";

if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY o.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orcamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   COR STATUS
========================= */
function corStatus($status) {
    return match ($status) {
        'rascunho'   => '#64748b',
        'enviado'    => '#2563eb',
        'aprovado'   => '#16a34a',
        'convertido' => '#0f766e',
        'rejeitado'  => '#dc2626',
        default      => '#374151'
    };
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Orçamentos</title>

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
    background: #f97316
;
    color: #fff;
}

.btn-primary:hover {
    background: #ea580c
;
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
</style>
</head>

<body>
<div class="container">

<h1>Orçamentos</h1>

<!-- FILTRO (PADRÃO PROJETOS) -->
<form method="get" class="filtro">
    <input type="text" name="busca" placeholder="Buscar cliente ou descrição"
           value="<?= htmlspecialchars($busca) ?>">

    <select name="status">
        <option value="">Todos os status</option>
        <?php
        $statusList = ['rascunho','enviado','aprovado','convertido','rejeitado'];
        foreach ($statusList as $s):
        ?>
        <option value="<?= $s ?>" <?= $s === $status ? 'selected' : '' ?>>
            <?= ucfirst($s) ?>
        </option>
        <?php endforeach; ?>
    </select>

    <button class="btn btn-primary">Filtrar</button>
    <a href="orcamentos.php" class="btn btn-secondary">Limpar</a>
</form>

<div class="botoes">
    <a href="orcamento_novo.php" class="btn btn-primary">➕ Novo Orçamento</a>
    <a href="index.php" class="btn btn-secondary">⬅ Dashboard</a>
</div>

<div class="table-wrapper">
<table class="system-table">
<thead>
<tr>
    <th>ID</th>
    <th>Cliente</th>
    <th>Status</th>
    <th>Valor</th>
    <th>Lucro</th>
    <th>Margem</th>
</tr>
</thead>
<tbody>

<?php if (!$orcamentos): ?>
<tr>
    <td colspan="6" style="text-align:center;color:#64748b;">
        Nenhum orçamento encontrado
    </td>
</tr>
<?php endif; ?>

<?php foreach ($orcamentos as $o): ?>
<tr class="linha-click"
    onclick="location.href='orcamento_editar.php?id=<?= $o['id'] ?>'">

    <td><?= $o['id'] ?></td>

    <td><?= htmlspecialchars($o['cliente'] ?? '—') ?></td>

    <td style="color:<?= corStatus($o['status']) ?>; font-weight:bold;">
        <?= strtoupper($o['status']) ?>
    </td>

    <td>R$ <?= number_format($o['valor_estimado'],2,',','.') ?></td>
    <td>R$ <?= number_format($o['lucro_estimado'],2,',','.') ?></td>
    <td><?= number_format($o['margem_estimada'],2,',','.') ?>%</td>
</tr>
<?php endforeach; ?>

</tbody>
</table>
</div>

</div>
</body>
</html>


