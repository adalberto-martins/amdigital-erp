<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

$clientes = $pdo->query("SELECT id,nome FROM clientes WHERE status='ativo'")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Novo Orçamento</title>
</head>
<body>

<h1>Novo Orçamento</h1>

<form method="post" action="orcamento_salvar.php" id="formOrcamento">

<select name="cliente_id">
    <option value="">Cliente (opcional)</option>
    <?php foreach ($clientes as $c): ?>
        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
    <?php endforeach; ?>
</select>

<select name="tipo_projeto" id="tipoProjeto" required>
    <option value="">Tipo de Projeto</option>
    <option value="institucional">Site Institucional</option>
    <option value="landing">Landing Page</option>
    <option value="loja">Loja Virtual</option>
    <option value="sistema">Sistema Web</option>
</select>

<select name="tipo_design" id="tipoDesign" required>
    <option value="">Tipo de Design</option>
    <option value="simples">Simples</option>
    <option value="pro">Profissional</option>
    <option value="premium">Premium</option>
</select>

<select name="urgencia" id="urgencia" required>
    <option value="">Urgência</option>
    <option value="normal">Normal</option>
    <option value="rapida">Rápida</option>
    <option value="urgente">Urgente</option>
</select>

<textarea name="descricao"></textarea>

<input type="hidden" name="valor_estimado" id="valorEstimadoInput">

<p id="valorEstimado">R$ 0,00</p>

<button>Salvar Orçamento</button>
</form>

<script>
document.addEventListener("change", calcular);

function calcular(){
    let preco = 0;

    if(tipoProjeto.value==='institucional') preco+=1500;
    if(tipoProjeto.value==='landing') preco+=900;
    if(tipoProjeto.value==='loja') preco+=2500;
    if(tipoProjeto.value==='sistema') preco+=5000;

    if(tipoDesign.value==='simples') preco+=200;
    if(tipoDesign.value==='pro') preco+=600;
    if(tipoDesign.value==='premium') preco+=1200;

    if(urgencia.value==='rapida') preco*=1.2;
    if(urgencia.value==='urgente') preco*=1.4;

    valorEstimado.innerText = "R$ "+preco.toFixed(2).replace('.',',');
    valorEstimadoInput.value = preco.toFixed(2);
}
</script>

</body>
</html>
