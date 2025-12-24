<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

/* =========================
   FILTROS (VOLTA)
========================= */
$busca  = $_GET['busca']  ?? '';
$status = $_GET['status'] ?? '';

$queryString = http_build_query(array_filter([
    'busca'  => $busca,
    'status' => $status
]));

/* =========================
   CLIENTES
========================= */
$clientes = $pdo->query("
    SELECT id, nome
    FROM clientes
    WHERE status = 'ativo'
    ORDER BY nome
")->fetchAll(PDO::FETCH_ASSOC);
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
    background: #f97316
;
    color: #fff;
}

.btn-primary:hover {
    background: #ea580c
;
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
    min-height: 120px;
}
</style>
</head>

<body>
<div class="container">

<h1>Novo Orçamento</h1>

<div class="botoes">
    <a href="orcamentos.php<?= $queryString ? '?' . $queryString : '' ?>"
       class="btn btn-secondary">⬅ Voltar</a>
</div>

<div class="form-wrapper">
<form method="post" action="orcamento_salvar.php">

    <label>Cliente</label>
    <select name="cliente_id">
        <option value="">Selecione</option>
        <?php foreach ($clientes as $c): ?>
            <option value="<?= $c['id'] ?>">
                <?= htmlspecialchars($c['nome']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Tipo de Projeto</label>
    <select name="tipo_projeto" required>
        <option value="institucional">Site Institucional</option>
        <option value="landing">Landing Page</option>
        <option value="loja">Loja Virtual</option>
        <option value="sistema">Sistema Web</option>
    </select>

    <label>Tipo de Design</label>
    <select name="tipo_design" required>
        <option value="simples">Simples</option>
        <option value="pro">Profissional</option>
        <option value="premium">Premium</option>
    </select>

    <label>Urgência</label>
    <select name="urgencia" required>
        <option value="normal">Normal</option>
        <option value="rapida">Rápida</option>
        <option value="urgente">Urgente</option>
    </select>

    <label>Descrição</label>
    <textarea name="descricao" placeholder="Descreva o escopo do projeto"></textarea>

    <label>Valor Estimado (R$)</label>
    <input type="number" step="0.01" name="valor_estimado" required>

    <div class="botoes" style="margin-top:20px;">
        <button type="submit" class="btn btn-primary">
            💾 Salvar Orçamento
        </button>

        <a href="orcamentos.php<?= $queryString ? '?' . $queryString : '' ?>"
           class="btn btn-secondary">
           Cancelar
        </a>
    </div>

</form>
</div>

</div>
</body>
</html>


