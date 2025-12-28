<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

/* clientes */
$stmtClientes = $pdo->query("
    SELECT id, nome 
    FROM clientes 
    WHERE status = 'ativo' 
    ORDER BY nome
");
$clientes = $stmtClientes->fetchAll(PDO::FETCH_ASSOC);

/* serviços */
$stmtServicos = $pdo->query("
    SELECT id, nome, categoria, valor_base 
    FROM servicos 
    WHERE ativo = 1 
    ORDER BY categoria, nome
");
$servicos = $stmtServicos->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Novo Orçamento</title>

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
    margin-bottom: 15px;
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
    background: #f97316;
    color: #fff;
}

.btn-primary:hover {
    background: #ea580c;
}

.btn-secondary {
    background: #e5e7eb;
    color: #374151;
}

.btn-secondary:hover {
    background: #d1d5db;
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
    margin-top: 12px;
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
    min-height: 120px;
}

/* serviços */
.servicos-box {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 10px;
    background: #f8fafc;
    max-height: 260px;
    overflow-y: auto;
}

.servico-item {
    display: block;
    padding: 6px;
    cursor: pointer;
}

.servico-item:hover {
    background: #f1f5f9;
}

.info-box {
    background: #f8fafc;
    padding: 15px;
    border-radius: 8px;
    margin-top: 15px;
    border: 1px solid #e5e7eb;
}
</style>
</head>

<body>
<div class="container">

<h1>Novo Orçamento</h1>

<div class="botoes">
    <a href="orcamentos.php" class="btn btn-secondary">⬅ Voltar</a>
</div>

<div class="form-wrapper">
<form method="post" action="orcamento_salvar.php">

    <label>Cliente</label>
    <select name="cliente_id" required>
        <option value="">Selecione</option>
        <?php foreach ($clientes as $c): ?>
            <option value="<?= $c['id'] ?>">
                <?= htmlspecialchars($c['nome']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Descrição do Orçamento</label>
    <textarea name="descricao"></textarea>

    <label>Serviços</label>
    <div class="servicos-box">
        <?php foreach ($servicos as $s): ?>
            <label class="servico-item">
                <input 
                    type="checkbox"
                    name="servicos[]"
                    value="<?= $s['id'] ?>"
                    data-valor="<?= $s['valor_base'] ?>"
                    onchange="recalcularTotal()"
                >
                <strong><?= htmlspecialchars($s['nome']) ?></strong>
                <small>
                    (<?= htmlspecialchars($s['categoria']) ?>) — 
                    R$ <?= number_format($s['valor_base'],2,',','.') ?>
                </small>
            </label>
        <?php endforeach; ?>
    </div>

    <div class="botoes" style="margin-top:10px;">
        <a href="servico_novo.php" class="btn btn-secondary">
            ➕ Novo Serviço
        </a>
    </div>

    <label>Valor estimado (R$)</label>
    <input 
        type="text" 
        id="valor_estimado" 
        name="valor_estimado" 
        readonly
    >

    <label>Status</label>
    <select name="status">
        <option value="rascunho">Rascunho</option>
        <option value="enviado">Enviado</option>
    </select>

    <div class="botoes" style="margin-top:20px;">
        <button type="submit" class="btn btn-primary">
            Salvar Orçamento
        </button>
    </div>

</form>
</div>

</div>

<script>
function recalcularTotal() {
    let total = 0;

    document.querySelectorAll('input[name="servicos[]"]:checked')
        .forEach(el => {
            total += parseFloat(el.dataset.valor);
        });

    document.getElementById('valor_estimado').value =
        total.toFixed(2).replace('.', ',');
}
</script>

</body>
</html>



