<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

$stmt = $pdo->prepare("
    INSERT INTO servicos
        (nome, categoria, descricao, valor_base, ativo)
    VALUES (?, ?, ?, ?, ?)
");

$stmt->execute([
    $_POST['nome'],
    $_POST['categoria'],
    $_POST['descricao'],
    $_POST['valor_base'],
    $_POST['ativo']
]);

header("Location: servicos.php");
exit;
