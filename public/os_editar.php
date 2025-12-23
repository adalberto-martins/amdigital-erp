<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

/* =========================
   VALIDAR ID
========================= */
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    die("OS inválida.");
}

/* =========================
   BUSCAR OS
========================= */
$stmt = $pdo->prepare("
    SELECT 
        os.*,
        p.nome AS projeto,
        c.nome AS cliente
    FROM ordens_servico os
    JOIN projetos p ON p.id = os.projeto_id
    JOIN clientes c ON c.id = os.cliente_id
    WHERE os.id = ?
");
$stmt->execute([$id]);
$os = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$os) {
    die("Ordem de serviço não encontrada.");
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Editar OS</title>

<style>
body {
    font-family: Arial, sans-serif;
    background: #f1f5f9;
}

.container {
    max-width: 900px;
    margin: 0 auto;
}

h1 {
    margin-bottom: 10px;
}

.info {
    margin-bottom: 20px;
    color: #475569;
}

/* formulário */
.form-card {
    background: #fff;
    padding: 24px;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0,0,0,.08);
}

.form-group {
    margin-bottom: 14px;
}

.form-group label {
    display: block;
    font-weight: bold;
    margin-bottom: 4px;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 10px;
}

/* botões */
.botoes {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 20px;
}

.btn {
    padding: 10px 18px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
    cursor: pointer;
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

.btn-danger {
    background: #dc2626;
    color: #fff;
}

.btn-danger:hover {
    background: #b91c1c;
}
</style>
</head>

<body>
<div class="container">

<h1>Editar Ordem de Serviço</h1>
<div class="info">
    <strong>Cliente:</strong> <?= htmlspecialchars($os['cliente']) ?><br>
    <strong>Projeto:</strong> <?= htmlspecialchars($os['projeto']) ?>
</div>

<div class="form-card">
<form method="post" action="os_atualizar.php">

    <!-- ID -->
    <input type="hidden" name="id" value="<?= $os['id'] ?>">
    <input type="hidden" name="status_anterior" value="<?= $os['status'] ?>">

    <div class="form-group">
        <label>Status</label>
        <select name="status">
            <option value="aberta" <?= $os['status']=='aberta'?'selected':'' ?>>Aberta</option>
            <option value="em_execucao" <?= $os['status']=='em_execucao'?'selected':'' ?>>Em execução</option>
            <option value="concluida" <?= $os['status']=='concluida'?'selected':'' ?>>Concluída</option>
            <option value="cancelada" <?= $os['status']=='cancelada'?'selected':'' ?>>Cancelada</option>
        </select>
    </div>

    <div class="form-group">
        <label>Descrição do Serviço</label>
        <textarea name="descricao" rows="4" required><?= htmlspecialchars($os['descricao']) ?></textarea>
    </div>

    <div class="form-group">
        <label>Valor</label>
        <input type="number" name="valor" step="0.01"
               value="<?= $os['valor'] ?>" required>
    </div>

    <div class="form-group">
        <label>Data de Início</label>
        <input type="date" name="data_inicio" value="<?= $os['data_inicio'] ?? '' ?>">
    </div>

    <div class="form-group">
        <label>Data de Fim</label>
        <input type="date" name="data_fim" value="<?= $os['data_fim'] ?? '' ?>">
    </div>

    <div class="botoes">
        <button type="submit" class="btn btn-primary">
            💾 Salvar Alterações
        </button>

        <a href="ordens_servico.php" class="btn btn-secondary">
            ⬅ Voltar
        </a>

        <a href="os_excluir.php?id=<?= $os['id'] ?>"
           class="btn btn-danger"
           onclick="return confirm('Deseja realmente excluir esta OS?')">
           🗑 Excluir OS
        </a>
    </div>

</form>
</div>

</div>
</body>
</html>
