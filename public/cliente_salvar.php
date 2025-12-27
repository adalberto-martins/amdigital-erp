<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

/* validação básica */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: clientes.php");
    exit;
}

$nome        = $_POST['nome'] ?? '';
$cpf_cnpj   = $_POST['cpf_cnpj'] ?? null;
$email       = $_POST['email'] ?? null;
$telefone    = $_POST['telefone'] ?? null;
$endereco    = $_POST['endereco'] ?? null;
$observacoes = $_POST['observacoes'] ?? null;
$status      = $_POST['status'] ?? 'ativo';

/* nome é obrigatório */
if (trim($nome) === '') {
    header("Location: cliente_novo.php");
    exit;
}

/* insert */
$stmt = $pdo->prepare("
    INSERT INTO clientes 
    (nome, cpf_cnpj, email, telefone, endereco, observacoes, status)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");

$stmt->execute([
    $nome,
    $cpf_cnpj,
    $email,
    $telefone,
    $endereco,
    $observacoes,
    $status
]);

/* redireciona */
header("Location: clientes.php");
exit;

