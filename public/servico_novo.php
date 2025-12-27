<?php
require __DIR__ . "/../app/auth/seguranca.php";
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Novo Serviço</title>

<style>
body{font-family:Arial;background:#f1f5f9}
.container{max-width:1200px;margin:auto}
.botoes{display:flex;gap:10px;margin-bottom:15px}
.btn{padding:10px 18px;border-radius:8px;text-decoration:none;font-weight:bold}
.btn-primary{background:#8b5cf6;color:#fff}
.btn-primary:hover{background:#7c3aed}
.btn-secondary{background:#e5e7eb;color:#374151}
.table-wrapper{display:flex;justify-content:center}
table{width:100%;background:#fff;border-collapse:collapse;border-radius:10px;overflow:hidden}
th,td{padding:12px;border-bottom:1px solid #e5e7eb}
tr.linha-click{cursor:pointer}
tr.linha-click:hover{background:#f3f4f6}
</style>
</head>
<body>

<h1>Novo Serviço</h1>

<form method="post" action="servico_salvar.php">
    <label>Nome</label>
    <input name="nome" required>

    <label>Categoria</label>
    <input name="categoria" required>

    <label>Descrição</label>
    <textarea name="descricao"></textarea>

    <label>Valor Base</label>
    <input name="valor_base" type="number" step="0.01" required>

    <label>Status</label>
    <select name="ativo">
        <option value="sim">Ativo</option>
        <option value="nao">Inativo</option>
    </select>

    <button type="submit">Salvar</button>
</form>

</body>
</html>
