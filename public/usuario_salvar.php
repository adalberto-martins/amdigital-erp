<?php
require __DIR__ . "/../app/auth/seguranca.php";
exigeAdmin();

require __DIR__ . "/../config/database.php";

// Garantia extra (debug defensivo)
if (!isset($pdo)) {
    die("Erro crítico: conexão com banco não encontrada.");
}

// Dados do formulário
$nome   = $_POST['nome'] ?? '';
$email  = $_POST['email'] ?? '';
$senha  = $_POST['senha'] ?? '';
$nivel  = $_POST['nivel'] ?? 'usuario';
$status = $_POST['status'] ?? 'ativo';

// Validação mínima
if ($nome === '' || $email === '' || $senha === '') {
    header("Location: usuario_novo.php");
    exit;
}

if (strlen($_POST['senha']) < 8) {
    die("Senha deve ter no mínimo 8 caracteres");
}

// Criptografar senha
$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

// Inserir no banco
$sql = "INSERT INTO usuarios (nome, email, senha, nivel, status)
        VALUES (?, ?, ?, ?, ?)";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    $nome,
    $email,
    $senhaHash,
    $nivel,
    $status
]);

header("Location: usuarios.php");
exit;
