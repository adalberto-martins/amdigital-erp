<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

/* =========================
   VALIDAR ID
========================= */
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    die("Custo inválido.");
}

/* =========================
   BUSCAR CUSTO
========================= */
$stmt = $pdo->prepare("SELECT * FROM custos WHERE id = ?");
$stmt->execute([$id]);
$c = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$c) {
    die("Custo não encontrado.");
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Editar Custo</title>

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

/* card */
.form-card {
    background: #fff;
    padding: 24px;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0,0,0,.08);
}

/* formulário */
.form-group {
    margin-bottom: 14px;
}

.form-group label {
    display: block;
    font-weight: bold;
    margin-bottom: 4px;
}

.form-group input,
.form-group select {
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

.btn-danger {
    background: #7f1d1d;
    color: #fff;
}

.btn-danger:hover {
    background: #450a0a;
}
</style>
</head>

<body>
<div class="container">

<h1>Editar Custo</h1>

<div class="form-card">
<form method="post" action="custo_atualizar.php">

    <input type="hidden" name="id" value="<?= $c['id'] ?>">

    <div class="form-group">
        <label>Descrição</label>
        <input type="text" name="descricao"
               value="<?= htmlspecialchars($c['descricao']) ?>"
               required>
    </div>

    <div class="form-group">
        <label>Categoria</label>
        <input type="text" name="categoria"
               value="<?= htmlspecialchars($c['categoria'] ?? '') ?>">
    </div>

    <div class="form-group">
        <label>Tipo</label>
        <select name="tipo" required>
            <option value="fixo" <?= $c['tipo']=='fixo'?'selected':'' ?>>Fixo</option>
            <option value="variavel" <?= $c['tipo']=='variavel'?'selected':'' ?>>Variável</option>
        </select>
    </div>

    <div class="form-group">
        <label>Valor</label>
        <input type="number" name="valor" step="0.01"
               value="<?= $c['valor'] ?>" required>
    </div>

    <div class="form-group">
        <label>Recorrente</label>
        <select name="recorrente">
            <option value="nao" <?= $c['recorrente']=='nao'?'selected':'' ?>>Não</option>
            <option value="sim" <?= $c['recorrente']=='sim'?'selected':'' ?>>Sim</option>
        </select>
    </div>

    <div class="form-group">
        <label>Dia da Recorrência (se aplicável)</label>
        <input type="number" name="dia_recorrencia" min="1" max="31"
               value="<?= $c['dia_recorrencia'] ?? '' ?>">
    </div>

    <div class="form-group">
        <label>Data</label>
        <input type="date" name="data"
               value="<?= $c['data'] ?>" required>
    </div>

    <div class="botoes">
        <button type="submit" class="btn btn-primary">
            💾 Salvar
        </button>

        <a href="custos.php" class="btn btn-secondary">
            ⬅ Voltar
        </a>

        <a href="custo_excluir.php?id=<?= $c['id'] ?>"
           class="btn btn-danger"
           onclick="return confirm('Deseja excluir este custo?')">
           🗑 Excluir
        </a>
    </div>

</form>
</div>

</div>
</body>
</html>

