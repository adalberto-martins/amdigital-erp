<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

$id = $_GET['id'] ?? null;
$stmt = $pdo->prepare("SELECT * FROM servicos WHERE id=?");
$stmt->execute([$id]);
$s = $stmt->fetch(PDO::FETCH_ASSOC);
if(!$s) die("Serviço não encontrado");
?>
<!DOCTYPE html>
<html>
<body>

<h1>Editar Serviço</h1>

<form method="post" action="servico_atualizar.php">
<input type="hidden" name="id" value="<?= $s['id'] ?>">

<label>Nome</label>
<input name="nome" value="<?= htmlspecialchars($s['nome']) ?>">

<label>Categoria</label>
<input name="categoria" value="<?= htmlspecialchars($s['categoria']) ?>">

<label>Descrição</label>
<textarea name="descricao"><?= htmlspecialchars($s['descricao']) ?></textarea>

<label>Valor Base</label>
<input name="valor_base" value="<?= $s['valor_base'] ?>">

<label>Status</label>
<select name="ativo">
    <option value="sim" <?= $s['ativo']=='sim'?'selected':'' ?>>Ativo</option>
    <option value="nao" <?= $s['ativo']=='nao'?'selected':'' ?>>Inativo</option>
</select>

<button type="submit">Salvar</button>
<a href="servico_excluir.php?id=<?= $s['id'] ?>" onclick="return confirm('Excluir?')">Excluir</a>
</form>

</body>
</html>
