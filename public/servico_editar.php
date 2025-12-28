<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

$id = $_GET['id'] ?? null;
if (!$id) {
    die("Serviço inválido");
}

$stmt = $pdo->prepare("SELECT * FROM servicos WHERE id = ?");
$stmt->execute([$id]);
$s = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$s) {
    die("Serviço não encontrado");
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Editar Serviço</title>

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

/* botões */
.botoes {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.btn {
    padding: 10px 18px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
    transition: .2s;
    border: none;
    cursor: pointer;
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

/* formulário */
.form-wrapper {
    display: flex;
    justify-content: center;
}

form {
    width: 100%;
    max-width: 900px;
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 4px 10px rgba(0,0,0,.08);
}

form label {
    font-weight: bold;
    display: block;
    margin-top: 10px;
}

form input,
form select,
form textarea {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border-radius: 8px;
    border: 1px solid #cbd5f5;
}

form textarea {
    min-height: 100px;
}
</style>
</head>

<body>
<div class="container">

<h1>Editar Serviço #<?= $s['id'] ?></h1>

<div class="botoes">
    <a href="servicos.php" class="btn btn-secondary">⬅ Voltar</a>
</div>

<div class="form-wrapper">
<form method="post" action="servico_atualizar.php">

    <input type="hidden" name="id" value="<?= $s['id'] ?>">

    <label>Nome do Serviço</label>
    <input type="text" name="nome" value="<?= htmlspecialchars($s['nome']) ?>" required>

    <label>Categoria</label>
    <input type="text" name="categoria" value="<?= htmlspecialchars($s['categoria']) ?>" required>

    <label>Descrição</label>
    <textarea name="descricao"><?= htmlspecialchars($s['descricao']) ?></textarea>

    <label>Valor Base (R$)</label>
    <input type="number" step="0.01" name="valor_base" value="<?= $s['valor_base'] ?>" required>

    <label>Status</label>
    <select name="ativo">
        <option value="sim" <?= $s['ativo']=='sim'?'selected':'' ?>>Ativo</option>
        <option value="nao" <?= $s['ativo']=='nao'?'selected':'' ?>>Inativo</option>
    </select>

    <div class="botoes" style="margin-top:20px;">
        <button type="submit" class="btn btn-primary">Salvar</button>

        <a href="servico_excluir.php?id=<?= $s['id'] ?>"
           class="btn btn-danger"
           onclick="return confirm('Deseja excluir este serviço?')">
           Excluir
        </a>
    </div>

</form>
</div>

</div>
</body>
</html>
