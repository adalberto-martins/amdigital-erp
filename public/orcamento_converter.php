<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM orcamentos WHERE id=? AND status='aprovado'");
$stmt->execute([$id]);
$o = $stmt->fetch();

if (!$o) die("Orçamento não aprovado");

$pdo->prepare("
INSERT INTO projetos
(cliente_id,nome,tipo,descricao,valor,status,data_inicio)
VALUES (?,?,?,?,?,'ativo',CURDATE())
")->execute([
    $o['cliente_id'],
    'Projeto Orçamento #'.$o['id'],
    $o['tipo_projeto'],
    $o['descricao'],
    $o['valor_estimado']
]);

$pdo->prepare("
INSERT INTO financeiro
(tipo,cliente_id,descricao,valor,vencimento,status)
VALUES ('receber',?,?,?,DATE_ADD(CURDATE(),INTERVAL 15 DAY),'pendente')
")->execute([
    $o['cliente_id'],
    'Projeto aprovado (Orçamento #'.$o['id'].')',
    $o['valor_estimado']
]);

$pdo->prepare("UPDATE orcamentos SET status='convertido' WHERE id=?")->execute([$id]);

header("Location: projetos.php");


