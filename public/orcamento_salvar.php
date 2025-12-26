<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../app/helpers/orcamento_helper.php";
require __DIR__ . "/../config/database.php";


$valor = (float) $_POST['valor_estimado'];

$calculo = calcularOrcamento($valor);

$valor_estimado  = $calculo['valor_estimado'];
$lucro_estimado  = $calculo['lucro_estimado'];
$margem_estimada = $calculo['margem_estimada'];


// custo médio (mesma lógica do dashboard)
$totalCustos = $pdo->query("SELECT COALESCE(SUM(valor),0) FROM custos")->fetchColumn();
$totalProjetos = $pdo->query("SELECT COUNT(*) FROM projetos")->fetchColumn();
$custoEstimado = $totalProjetos > 0 ? $totalCustos / $totalProjetos : 0;

$lucro = $valor - $custoEstimado;
$margem = $valor > 0 ? ($lucro / $valor) * 100 : 0;

$stmt = $pdo->prepare("
    INSERT INTO orcamentos
    (cliente_id,tipo_projeto,tipo_design,urgencia,descricao,
     valor_estimado,custo_estimado,lucro_estimado,margem_estimada)
    VALUES (?,?,?,?,?,?,?,?,?)
");

$stmt->execute([
    $_POST['cliente_id'] ?: null,
    $_POST['tipo_projeto'],
    $_POST['tipo_design'],
    $_POST['urgencia'],
    $_POST['descricao'],
    $valor,
    $custoEstimado,
    $lucro,
    $margem
]);

header("Location: orcamentos.php");
