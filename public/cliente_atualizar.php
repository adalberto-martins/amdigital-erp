<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

$id = $_POST['id'] ?? null;
if (!$id) {
    header("Location: clientes.php");
    exit;
}

// Buscar dados atuais
$stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = ?");
$stmt->execute([$id]);
$clienteAtual = $stmt->fetch();

if (!$clienteAtual) {
    header("Location: clientes.php");
    exit;
}

// Capturar dados do formulário
$nome         = $_POST['nome'] ?? '';
$cpf_cnpj     = $_POST['cpf_cnpj'] ?? '';
$endereco     = $_POST['endereco'] ?? '';
$email        = $_POST['email'] ?? '';
$telefone     = $_POST['telefone'] ?? '';
$observacoes  = $_POST['observacoes'] ?? '';
$status       = $_POST['status'] ?? 'ativo';

// REGRA: se vier vazio, mantém o valor antigo
$nome        = $nome        !== '' ? $nome        : $clienteAtual['nome'];
$cpf_cnpj    = $cpf_cnpj    !== '' ? $cpf_cnpj    : $clienteAtual['cpf_cnpj'];
$endereco    = $endereco    !== '' ? $endereco    : $clienteAtual['endereco'];
$email       = $email       !== '' ? $email       : $clienteAtual['email'];
$telefone    = $telefone    !== '' ? $telefone    : $clienteAtual['telefone'];
$observacoes = $observacoes !== '' ? $observacoes : $clienteAtual['observacoes'];

// Atualizar
$sql = "UPDATE clientes SET
    nome = ?,
    cpf_cnpj = ?,
    endereco = ?,
    email = ?,
    telefone = ?,
    observacoes = ?,
    status = ?
WHERE id = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    $nome,
    $cpf_cnpj,
    $endereco,
    $email,
    $telefone,
    $observacoes,
    $status,
    $id
]);

header("Location: clientes.php");
exit;
