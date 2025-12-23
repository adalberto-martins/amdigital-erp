<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

/* =========================
   BUSCA / FILTROS
========================= */
$busca  = $_GET['busca']  ?? '';
$status = $_GET['status'] ?? '';

$sql = "
    SELECT p.*, c.nome AS cliente
    FROM projetos p
    JOIN clientes c ON c.id = p.cliente_id
    WHERE 1=1
";
$params = [];

if (!empty($busca)) {
    $sql .= " AND (p.nome LIKE ? OR c.nome LIKE ?)";
    $params[] = "%$busca%";
    $params[] = "%$busca%";
}

if (!empty($status)) {
    $sql .= " AND p.status = ?";
    $params[] = $status;
}

$sql .= " ORDER BY p.nome";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$projetos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Projetos</title>

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
</style>
</head>

<body>
<div class="container">

<h1>Projetos</h1>

<!-- BOTÕES -->
<div class="botoes">
    <a href="projeto_novo.php" class="btn btn-primary">➕ Novo Projeto</a>
    <a href="index.php" class="btn btn-secondary">⬅ Voltar ao Dashboard</a>
</div>

<!-- FILTRO -->
<form method="get" class="filtro">
    <input type="text" name="busca"
           value="<?= htmlspecialchars($busca) ?>"
           placeholder="Projeto ou Cliente">

    <select name="status">
        <option value="">Todos</option>
        <option value="ativo" <?= $status=='ativo'?'selected':'' ?>>Ativo</option>
        <option value="em_andamento" <?= $status=='em_andamento'?'selected':'' ?>>Em andamento</option>
        <option value="concluido" <?= $status=='concluido'?'selected':'' ?>>Concluído</option>
        <option value="cancelado" <?= $status=='cancelado'?'selected':'' ?>>Cancelado</option>
    </select>

    <button class="btn btn-primary">🔍 Buscar</button>
</form>

<!-- TABELA -->
<div class="table-wrapper">
<table class="system-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Projeto</th>
            <th>Cliente</th>
            <th>Tipo</th>
            <th>Status</th>
            <th>Valor</th>
        </tr>
    </thead>
    <tbody>
    <?php if (empty($projetos)): ?>
        <tr>
            <td colspan="6">Nenhum projeto encontrado</td>
        </tr>
    <?php endif; ?>

    <?php foreach ($projetos as $p): ?>
        <tr class="linha-click"
            onclick="window.location='projeto_editar.php?id=<?= $p['id'] ?>'">

            <td><?= $p['id'] ?></td>
            <td><?= htmlspecialchars($p['nome']) ?></td>
            <td><?= htmlspecialchars($p['cliente']) ?></td>
            <td><?= htmlspecialchars($p['tipo']) ?></td>
            <td><?= htmlspecialchars($p['status']) ?></td>
            <td>R$ <?= number_format($p['valor'],2,',','.') ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>

</div>
</body>
</html>
