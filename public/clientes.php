<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

/* =========================
   BUSCA / FILTROS
========================= */
$busca  = $_GET['busca']  ?? '';
$status = $_GET['status'] ?? '';

$sql = "SELECT * FROM clientes WHERE 1=1";
$params = [];

if (!empty($busca)) {
    $sql .= " AND (nome LIKE ? OR cpf_cnpj LIKE ?)";
    $params[] = "%$busca%";
    $params[] = "%$busca%";
}

if (!empty($status)) {
    $sql .= " AND status = ?";
    $params[] = $status;
}

$sql .= " ORDER BY nome";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Clientes</title>

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
    background: #2563eb;
    color: #fff;
}

.btn-primary:hover {
    background: #1e40af;
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

<h1>Clientes</h1>

<!-- BOTÕES -->
<div class="botoes">
    <a href="cliente_novo.php" class="btn btn-primary">➕ Novo Cliente</a>
    <a href="index.php" class="btn btn-secondary">⬅ Voltar ao Dashboard</a>
</div>

<!-- FILTRO -->
<form method="get" class="filtro">
    <input type="text" name="busca"
           value="<?= htmlspecialchars($busca) ?>"
           placeholder="Nome ou CPF/CNPJ">

    <select name="status">
        <option value="">Todos</option>
        <option value="ativo"   <?= $status=='ativo'?'selected':'' ?>>Ativo</option>
        <option value="inativo" <?= $status=='inativo'?'selected':'' ?>>Inativo</option>
    </select>

    <button class="btn btn-primary">🔍 Buscar</button>
</form>

<!-- TABELA -->
<div class="table-wrapper">
<table class="system-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>CPF / CNPJ</th>
            <th>Email</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
    <?php if (empty($clientes)): ?>
        <tr>
            <td colspan="5">Nenhum cliente encontrado</td>
        </tr>
    <?php endif; ?>

    <?php foreach ($clientes as $c): ?>
        <tr class="linha-click"
            onclick="window.location='cliente_editar.php?id=<?= $c['id'] ?>'">

            <td><?= $c['id'] ?></td>
            <td><?= htmlspecialchars($c['nome']) ?></td>
            <td><?= htmlspecialchars($c['cpf_cnpj']) ?></td>
            <td><?= htmlspecialchars($c['email']) ?></td>
            <td><?= htmlspecialchars($c['status']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>

</div>
</body>
</html>
