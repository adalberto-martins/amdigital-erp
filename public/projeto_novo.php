<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

/* =========================
   LISTAR CLIENTES ATIVOS
========================= */
$stmt = $pdo->query("
    SELECT id, nome
    FROM clientes
    WHERE status = 'ativo'
    ORDER BY nome
");
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Novo Projeto</title>

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
    margin-bottom: 20px;
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
</style>
</head>

<body>
<div class="container">

<h1>Novo Projeto</h1>

<div class="form-card">
<form method="post" action="projeto_salvar.php">

    <div class="form-group">
        <label>Cliente</label>
        <select name="cliente_id" required>
            <option value="">Selecione o cliente</option>
            <?php foreach ($clientes as $c): ?>
                <option value="<?= $c['id'] ?>">
                    <?= htmlspecialchars($c['nome']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label>Nome do Projeto</label>
        <input type="text" name="nome" required>
    </div>

    <div class="form-group">
        <label>Tipo</label>
        <input type="text" name="tipo" placeholder="Site, Landing Page, Sistema...">
    </div>

    <div class="form-group">
        <label>Status</label>
        <select name="status">
            <option value="ativo">Ativo</option>
            <option value="em_andamento">Em andamento</option>
            <option value="concluido">Concluído</option>
            <option value="cancelado">Cancelado</option>
        </select>
    </div>

    <div class="form-group">
        <label>Valor</label>
        <input type="number" name="valor" step="0.01">
    </div>

    <div class="form-group">
        <label>Data de Início</label>
        <input type="date" name="data_inicio">
    </div>

    <div class="form-group">
        <label>Data de Fim</label>
        <input type="date" name="data_fim">
    </div>

    <div class="form-group">
        <label>Descrição</label>
        <textarea name="descricao" rows="4"></textarea>
    </div>

    <div class="botoes">
        <button type="submit" class="btn btn-primary">💾 Salvar Projeto</button>
        <a href="projetos.php" class="btn btn-secondary">⬅ Voltar</a>
    </div>

</form>
</div>

</div>
</body>
</html>
