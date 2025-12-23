<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

/* =========================
   BUSCA / FILTROS
========================= */
$busca  = $_GET['busca'] ?? '';
$status = $_GET['status'] ?? '';

$sql = "
    SELECT 
        os.id,
        os.status,
        os.valor,
        os.criado_em,
        p.nome AS projeto,
        c.nome AS cliente
    FROM ordens_servico os
    JOIN projetos p ON p.id = os.projeto_id
    JOIN clientes c ON c.id = os.cliente_id
    WHERE 1=1
";
$params = [];

if (!empty($busca)) {
    $sql .= " AND (p.nome LIKE ? OR c.nome LIKE ?)";
    $params[] = "%$busca%";
    $params[] = "%$busca%";
}

if (!empty($status)) {
    $sql .= " AND os.status = ?";
    $params[] = $status;
}

$sql .= " ORDER BY os.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$ordens = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Ordens de Serviço</title>

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

<h1>Ordens de Serviço</h1>

<!-- BOTÕES -->
<div class="botoes">
    <a href="os_nova.php" class="btn btn-primary">➕ Nova OS</a>
    <a href="index.php" class="btn btn-secondary">⬅ Voltar ao Dashboard</a>
</div>

<!-- FILTRO -->
<form method="get" class="filtro">
    <input type="text" name="busca"
           value="<?= htmlspecialchars($busca) ?>"
           placeholder="Cliente ou Projeto">

    <select name="status">
        <option value="">Todos</option>
        <option value="aberta" <?= $status=='aberta'?'selected':'' ?>>Aberta</option>
        <option value="em_execucao" <?= $status=='em_execucao'?'selected':'' ?>>Em execução</option>
        <option value="concluida" <?= $status=='concluida'?'selected':'' ?>>Concluída</option>
        <option value="cancelada" <?= $status=='cancelada'?'selected':'' ?>>Cancelada</option>
    </select>

    <button class="btn btn-primary">🔍 Buscar</button>
</form>

<!-- TABELA -->
<div class="table-wrapper">
<table class="system-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>Projeto</th>
            <th>Status</th>
            <th>Valor</th>
            <th>Criada em</th>
        </tr>
    </thead>
    <tbody>

    <?php if (empty($ordens)): ?>
        <tr>
            <td colspan="6">Nenhuma ordem de serviço encontrada</td>
        </tr>
    <?php endif; ?>

    <?php foreach ($ordens as $os): ?>
        <tr class="linha-click"
            onclick="window.location='os_editar.php?id=<?= $os['id'] ?>'">

            <td><?= $os['id'] ?></td>
            <td><?= htmlspecialchars($os['cliente']) ?></td>
            <td><?= htmlspecialchars($os['projeto']) ?></td>
            <td><?= htmlspecialchars($os['status']) ?></td>
            <td>R$ <?= number_format($os['valor'],2,',','.') ?></td>
            <td><?= date('d/m/Y', strtotime($os['criado_em'])) ?></td>
        </tr>
    <?php endforeach; ?>

    </tbody>
</table>
</div>

</div>
</body>
</html>

