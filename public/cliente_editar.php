<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

/* =========================
   VALIDAR ID
========================= */
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    die("Cliente inválido.");
}

/* =========================
   BUSCAR CLIENTE
========================= */
$stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = ?");
$stmt->execute([$id]);
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cliente) {
    die("Cliente não encontrado.");
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Editar Cliente</title>

<style>
body {
    font-family: Arial, sans-serif;
    background: #f1f5f9;
}

.container {
    max-width: 1000px;
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

<h1>Editar Cliente</h1>

<div class="form-card">
<form method="post" action="cliente_atualizar.php">

    <!-- ID OCULTO -->
    <input type="hidden" name="id" value="<?= $cliente['id'] ?>">

    <div class="form-group">
        <label>Nome</label>
        <input type="text" name="nome"
               value="<?= htmlspecialchars($cliente['nome']) ?>" required>
    </div>

    <div class="form-group">
        <label>CPF / CNPJ</label>
        <input type="text" name="cpf_cnpj"
               value="<?= htmlspecialchars($cliente['cpf_cnpj']) ?>">
    </div>

    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email"
               value="<?= htmlspecialchars($cliente['email']) ?>">
    </div>

    <div class="form-group">
        <label>Telefone</label>
        <input type="text" name="telefone"
               value="<?= htmlspecialchars($cliente['telefone']) ?>">
    </div>

    <div class="form-group">
        <label>Status</label>
        <select name="status">
            <option value="ativo"   <?= $cliente['status']=='ativo'?'selected':'' ?>>Ativo</option>
            <option value="inativo" <?= $cliente['status']=='inativo'?'selected':'' ?>>Inativo</option>
        </select>
    </div>

    <div class="form-group">
        <label>Observações</label>
        <textarea name="observacoes" rows="4"><?= htmlspecialchars($cliente['observacoes']) ?></textarea>
    </div>

    <div class="botoes">
        <button type="submit" class="btn btn-primary">💾 Salvar Alterações</button>

        <a href="clientes.php" class="btn btn-secondary">⬅ Voltar</a>

        <a href="cliente_excluir.php?id=<?= $cliente['id'] ?>"
           class="btn btn-danger"
           onclick="return confirm('Deseja realmente excluir este cliente?')">
           🗑 Excluir Cliente
        </a>
    </div>

</form>
</div>

</div>
</body>
</html>
