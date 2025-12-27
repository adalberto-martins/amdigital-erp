<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

/* clientes */
$clientes = $pdo->query("SELECT id, nome FROM clientes ORDER BY nome")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Novo Orçamento</title>

<style>
body { font-family: Arial, sans-serif; background: #f1f5f9; }
.container { max-width: 1200px; margin: auto; }
h1 { margin-bottom: 15px; }

form {
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    border: 1px solid #e5e7eb;
    max-width: 900px;
}

label { font-weight: bold; display:block; margin-top:10px; }

input, select, textarea {
    width: 100%;
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #cbd5f5;
}

textarea { min-height: 120px; }

.botoes { margin-top: 20px; display:flex; gap:10px; }

.btn {
    padding: 10px 18px;
    border-radius: 8px;
    border:none;
    cursor:pointer;
    font-weight:bold;
}

.btn-primary { background:#f97316; color:#fff; }
.btn-primary:hover { background:#ea580c; }

.btn-secondary { background:#e5e7eb; }
</style>
</head>

<body>
<div class="container">

<h1>Novo Orçamento</h1>

<form method="post" action="orcamento_salvar.php" id="orcamentoForm">

<label>Cliente</label>
<select name="cliente_id" required>
    <option value="">Selecione</option>
    <?php foreach ($clientes as $c): ?>
        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
    <?php endforeach; ?>
</select>

<label>Tipo de Projeto</label>
<select id="tipo_projeto" name="tipo_projeto">
    <option value="site">Site</option>
    <option value="sistema">Sistema</option>
    <option value="landing">Landing Page</option>
    <option value="formatacao">Formatação</option>
</select>

<label>Tipo de Design</label>
<select id="tipo_design" name="tipo_design">
    <option value="basico">Básico</option>
    <option value="profissional">Profissional</option>
    <option value="premium">Premium</option>
</select>

<label>Urgência</label>
<select id="urgencia" name="urgencia">
    <option value="normal">Normal</option>
    <option value="urgente">Urgente</option>
</select>

<label>Descrição</label>
<textarea name="descricao"></textarea>

<hr>

<label>Valor Estimado (R$)</label>
<input type="text" id="valor_estimado" name="valor_estimado" readonly>

<label>Lucro Estimado (R$)</label>
<input type="text" id="lucro_estimado" name="lucro_estimado" readonly>

<label>Margem (%)</label>
<input type="text" id="margem_estimada" name="margem_estimada" readonly>

<div class="botoes">
    <button class="btn btn-primary">Salvar Orçamento</button>
    <a href="orcamentos.php" class="btn btn-secondary">Cancelar</a>
</div>

</form>
</div>

<script>
function calcular() {

    let base = 0;

    // tipo projeto
    switch (document.getElementById('tipo_projeto').value) {
        case 'site': base = 2000; break;
        case 'sistema': base = 5000; break;
        case 'landing': base = 1200; break;
        case 'formatacao': base = 50; break;
    }

    // design
    let design = document.getElementById('tipo_design').value;
    if (design === 'profissional') base *= 1.2;
    if (design === 'premium') base *= 1.5;

    // urgência
    if (document.getElementById('urgencia').value === 'urgente') {
        base *= 1.3;
    }

    let custo = base * 0.6;
    let lucro = base - custo;
    let margem = (lucro / base) * 100;

    document.getElementById('valor_estimado').value = base.toFixed(2);
    document.getElementById('lucro_estimado').value = lucro.toFixed(2);
    document.getElementById('margem_estimada').value = margem.toFixed(2);
}

// eventos
document.querySelectorAll(
    '#tipo_projeto, #tipo_design, #urgencia'
).forEach(el => el.addEventListener('change', calcular));

calcular();
</script>

</body>
</html>



