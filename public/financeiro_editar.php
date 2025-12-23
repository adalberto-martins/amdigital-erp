<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

/* =========================
   VALIDAR ID
========================= */
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    die("Lançamento inválido.");
}

/* =========================
   BUSCAR LANÇAMENTO
========================= */
$stmt = $pdo->prepare("
    SELECT 
        f.*,
        c.nome AS cliente,
        p.nome AS projeto
    FROM financeiro f
    LEFT JOIN clientes c ON c.id = f.cliente_id
    LEFT JOIN projetos p ON p.id = f.projeto_id
    WHERE f.id = ?
");
$stmt->execute([$id]);
$f = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$f) {
    die("Lançamento não encontrado.");
}

$origemOS = !empty($f['os_id']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Editar Lançamento Financeiro</title>

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

.readonly {
    background: #f8fafc;
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

<h1>Editar Lançamento Financeiro</h1>

<div class="info">
    <strong>Tipo:</strong> <?= strtoupper($f['tipo']) ?><br>
    <?php if ($origemOS): ?>
        <strong>Origem:</strong> Ordem de Serviço #<?= $f['os_id'] ?>
    <?php else: ?>
        <strong>Origem:</strong> Lançamento manual
    <?php endif; ?>
</div>

<div class="form-card">
<form method="post" action="financeiro_atualizar.php">

    <input type="hidden" name="id" value="<?= $f['id'] ?>">

    <div class="form-group">
        <label>Status</label>
        <select name="status">
            <option value="pendente" <?= $f['status']=='pendente'?'selected':'' ?>>Pendente</option>
            <option value="pago" <?= $f['status']=='pago'?'selected':'' ?>>Pago</option>
        </select>
    </div>

    <div class="form-group">
        <label>Data de Vencimento</label>
        <input type="date" name="vencimento"
               value="<?= $f['vencimento'] ?>"
               <?= $origemOS ? 'readonly class="readonly"' : '' ?>>
    </div>

    <div class="form-group">
        <label>Valor</label>
        <input type="number" name="valor" step="0.01"
               value="<?= $f['valor'] ?>"
               <?= $origemOS ? 'readonly class="readonly"' : '' ?>>
    </div>

    <div class="form-group">
        <label>Descrição</label>
        <textarea name="descricao" rows="3"
            <?= $origemOS ? 'readonly class="readonly"' : '' ?>
        ><?= htmlspecialchars($f['descricao']) ?></textarea>
    </div>

    <div class="botoes">
        <button type="submit" class="btn btn-primary">
            💾 Salvar
        </button>

        <a href="financeiro.php" class="btn btn-secondary">
            ⬅ Voltar
        </a>

        <?php if (!$origemOS): ?>
        <a href="financeiro_excluir.php?id=<?= $f['id'] ?>"
           class="btn btn-danger"
           onclick="return confirm('Deseja excluir este lançamento?')">
           🗑 Excluir
        </a>
        <?php endif; ?>
    </div>

</form>
</div>

</div>
</body>
</html>
