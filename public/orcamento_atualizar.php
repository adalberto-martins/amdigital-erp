<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

$id = $_POST['id'] ?? null;
if (!$id) die("ID inválido");

// buscar orçamento atual
$stmt = $pdo->prepare("SELECT valor_estimado FROM orcamentos WHERE id=?");
$stmt->execute([$id]);
$orcamentoAtual = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$orcamentoAtual) die("Orçamento não encontrado");

$valor = (float)$orcamentoAtual['valor_estimado'];

// custo médio (mesma lógica do dashboard)
$totalCustos = $pdo->query("
    SELECT COALESCE(SUM(valor),0) FROM custos
")->fetchColumn();

$totalProjetos = $pdo->query("
    SELECT COUNT(*) FROM projetos
")->fetchColumn();

$custoEstimado = $totalProjetos > 0 ? ($totalCustos / $totalProjetos) : 0;

$lucro  = $valor - $custoEstimado;
$margem = $valor > 0 ? ($lucro / $valor) * 100 : 0;

$stmt = $pdo->prepare("
    UPDATE orcamentos SET
        cliente_id = ?,
        tipo_projeto = ?,
        tipo_design = ?,
        urgencia = ?,
        descricao = ?,
        custo_estimado = ?,
        lucro_estimado = ?,
        margem_estimada = ?,
        status = ?
    WHERE id = ?
");

$stmt->execute([
    $_POST['cliente_id'] ?: null,
    $_POST['tipo_projeto'],
    $_POST['tipo_design'],
    $_POST['urgencia'],
    $_POST['descricao'],
    $custoEstimado,
    $lucro,
    $margem,
    $_POST['status'],
    $id
]);

header("Location: orcamentos.php");
exit;

