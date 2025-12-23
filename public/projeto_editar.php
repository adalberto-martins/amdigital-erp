<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

/* =========================
   VALIDAR ID
========================= */
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    die("Projeto inválido.");
}

/* =========================
   BUSCAR PROJETO
========================= */
$stmt = $pdo->prepare("
    SELECT *
    FROM projetos
    WHERE id = ?
");
$stmt->execute([$id]);
$projeto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$projeto) {
    die("Projeto não encontrado.");
}

/* =========================
   LISTAR CLIENTES (SELECT)
========================= */
$stmtClientes = $pdo->query("
    SELECT id, nome
    FROM clientes
    WHERE status = 'ativo'
    ORDER BY nome
");
$clientes = $stmtClientes->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Editar Projeto</title>

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

<h1>Editar Projeto</h1>

<div class="form-card">
<form method="post" action="projeto_atualizar.php">

    <!-- ID OCULTO -->
    <input type="hidden" name="id" value="<?= $projeto['id'] ?>">

    <div class="form-group">
        <label>Cliente</label>
        <select name="cliente_id" required>
            <option value="">Selecione o cliente</option>
            <?php foreach ($clientes as $c): ?>
                <option value="<?= $c['id'] ?>"
                    <?= $c['id'] == $projeto['cliente_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['nome']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label>Nome do Projeto</label>
        <input type="text" name="nome"
               value="<?= htmlspecialchars($projeto['nome']) ?>" required>
    </div>

    <div class="form-group">
        <label>Tipo</label>
        <input type="text" name="tipo"
               value="<?= htmlspecialchars($projeto['tipo']) ?>">
    </div>

    <div class="form-group">
        <label>Status</label>
        <select name="status">
            <option value="ativo" <?= $projeto['status']=='ativo'?'selected':'' ?>>Ativo</option>
            <option value="em_andamento" <?= $projeto['status']=='em_andamento'?'selected':'' ?>>Em andamento</option>
            <option value="concluido" <?= $projeto['status']=='concluido'?'selected':'' ?>>Concluído</option>
            <option value="cancelado" <?= $projeto['status']=='cancelado'?'selected':'' ?>>Cancelado</option>
        </select>
    </div>

    <div class="form-group">
        <label>Valor</label>
        <input type="number" name="valor" step="0.01"
               value="<?= htmlspecialchars($projeto['valor']) ?>">
    </div>

    <div class="form-group">
        <label>Data de Início</label>
        <input type="date" name="data_inicio"
               value="<?= $projeto['data_inicio'] ?>">
    </div>

    <div class="form-group">
        <label>Data de Fim</label>
        <input type="date" name="data_fim"
               value="<?= $projeto['data_fim'] ?>">
    </div>

    <div class="form-group">
        <label>Descrição</label>
        <textarea name="descricao" rows="4"><?= htmlspecialchars($projeto['descricao']) ?></textarea>
    </div>

    <div class="botoes">
        <button type="submit" class="btn btn-primary">💾 Salvar Alterações</button>

        <a href="projetos.php" class="btn btn-secondary">⬅ Voltar</a>

        <a href="projeto_excluir.php?id=<?= $projeto['id'] ?>"
           class="btn btn-danger"
           onclick="return confirm('Deseja realmente excluir este projeto?')">
           🗑 Excluir Projeto
        </a>
    </div>

</form>
</div>

</div>
</body>
</html>
