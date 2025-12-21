<?php
require "../app/auth/verifica_login.php";
require "../config/database.php";

$id = $_POST['id'] ?? null;
$nome = $_POST['nome'] ?? '';
$email = $_POST['email'] ?? '';
$telefone = $_POST['telefone'] ?? '';
$status = $_POST['status'] ?? 'ativo';

if (!$id || $nome === '') {
    header("Location: clientes.php");
    exit;
}

$sql = "UPDATE clientes
        SET nome = ?, email = ?, telefone = ?, status = ?
        WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$nome, $email, $telefone, $status, $id]);

header("Location: clientes.php");
exit;
