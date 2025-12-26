<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: orcamentos.php");
    exit;
}

/* =========================
   BUSCA ORÇAMENTO
========================= */
$stmt = $pdo->prepare("
    SELECT o.*, c.nome AS cliente
    FROM orcamentos o
    LEFT JOIN clientes c ON c.id = o.cliente_id
    WHERE o.id = ?
");
$stmt->execute([$id]);
$o = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$o) {
    header("Location: orcamentos.php");
    exit;
}

/* =========================
   REGRAS DE STATUS
========================= */
$bloqueado = in_array($o['status'], ['rejeitado', 'convertido']);
$permitirConverter = ($o['status'] === 'aprovado');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Editar Orçamento</title>

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
    min-height: 120px;
}

/* info */
.info-box {
    background: #f8fafc;
    padding: 15px;
    border-radius: 8px;
    margin-top: 15px;
    border: 1px solid #e5e7eb;
}

.alert {
    background: #fff7ed;
    border: 1px solid #fed7aa;
    color: #9a3412;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 15px;
}
</style>
</head>

<body>
<div class="container">

<h1>Editar Orçamento #<?= $o['id'] ?></h1>

<div class="botoes">
        <a href="orcamento_pdf.php?id=<?= $o['id'] ?>"
       class="btn btn-secondary"
       target="_blank">
       📄 Gerar PDF
    </a>
    <a href="orcamentos.php" class="btn btn-secondary">⬅ Voltar</a>
</div>

<?php if ($bloqueado): ?>
    <div class="alert">
        Este orçamento está com status <strong><?= strtoupper($o['status']) ?></strong> e não pode mais ser alterado.
    </div>
<?php endif; ?>

<div class="form-wrapper">
<form method="post" action="orcamento_atualizar.php">

    <input type="hidden" name="id" value="<?= $o['id'] ?>">

    <label>Cliente</label>
    <input type="text" value="<?= htmlspecialchars($o['cliente'] ?? '—') ?>" disabled>

    <label>Tipo de Projeto</label>
    <input type="text" value="<?= ucfirst($o['tipo_projeto']) ?>" disabled>

    <label>Tipo de Design</label>
    <input type="text" value="<?= ucfirst($o['tipo_design']) ?>" disabled>

    <label>Urgência</label>
    <input type="text" value="<?= ucfirst($o['urgencia']) ?>" disabled>

    <label>Descrição</label>
    <textarea name="descricao" <?= $bloqueado ? 'disabled' : '' ?>>
<?= htmlspecialchars($o['descricao'] ?? '') ?>
    </textarea>

    <label>Status</label>
    <select name="status" <?= $bloqueado ? 'disabled' : '' ?>>
        <?php
        $statusList = ['rascunho','enviado','aprovado','rejeitado','convertido'];
        foreach ($statusList as $s):
        ?>
        <option value="<?= $s ?>" <?= $s === $o['status'] ? 'selected' : '' ?>>
            <?= ucfirst($s) ?>
        </option>
        <?php endforeach; ?>
    </select>

    <div class="info-box">
        <strong>Resumo Financeiro</strong><br><br>
        Valor estimado: <strong>R$ <?= number_format($o['valor_estimado'],2,',','.') ?></strong><br>
        Lucro estimado: <strong>R$ <?= number_format($o['lucro_estimado'],2,',','.') ?></strong><br>
        Margem estimada: <strong><?= number_format($o['margem_estimada'],2,',','.') ?>%</strong>
    </div>

    <div class="botoes" style="margin-top:20px;">
        <?php if (!$bloqueado): ?>
            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        <?php endif; ?>

        <?php if ($permitirConverter): ?>
            <a href="orcamento_converter.php?id=<?= $o['id'] ?>"
               class="btn btn-primary"
               onclick="return confirm('Converter este orçamento em projeto?')">
               Converter em Projeto
            </a>
        <?php endif; ?>

        <?php if ($o['status'] === 'rascunho'): ?>
            <a href="orcamento_excluir.php?id=<?= $o['id'] ?>"
               class="btn btn-danger"
               onclick="return confirm('Excluir este orçamento?')">
               Excluir
            </a>
        <?php endif; ?>
    </div>

</form>
</div>

</div>
</body>
</html>

