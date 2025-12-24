<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

$id = $_GET['id'] ?? null;
if (!$id) die("Orçamento inválido");

// buscar orçamento
$stmt = $pdo->prepare("SELECT * FROM orcamentos WHERE id=? AND status='aprovado'");
$stmt->execute([$id]);
$o = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$o) die("Orçamento não aprovado ou inexistente");

// criar projeto
$stmt = $pdo->prepare("
    INSERT INTO projetos
    (cliente_id,nome,tipo,descricao,valor,status,data_inicio)
    VALUES (?,?,?,?,?,'ativo',CURDATE())
");
$stmt->execute([
    $o['cliente_id'],
    'Projeto do Orçamento #'.$o['id'],
    $o['tipo_projeto'],
    $o['descricao'],
    $o['valor_estimado']
]);

$projetoId = $pdo->lastInsertId();

// criar financeiro (a receber)
$stmt = $pdo->prepare("
    INSERT INTO financeiro
    (tipo,cliente_id,projeto_id,descricao,valor,vencimento,status)
    VALUES ('receber',?,?,?,?,DATE_ADD(CURDATE(),INTERVAL 15 DAY),'pendente')
");
$stmt->execute([
    $o['cliente_id'],
    $projetoId,
    'Projeto aprovado (Orçamento #'.$o['id'].')',
    $o['valor_estimado']
]);

// atualizar orçamento
$pdo->prepare("UPDATE orcamentos SET status='aprovado' WHERE id=?")->execute([$id]);

header("Location: projetos.php");
