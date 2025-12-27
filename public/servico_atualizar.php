<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

$stmt = $pdo->prepare("
    UPDATE servicos SET
        nome=?, categoria=?, descricao=?, valor_base=?, ativo=?
    WHERE id=?
");

$stmt->execute([
    $_POST['nome'],
    $_POST['categoria'],
    $_POST['descricao'],
    $_POST['valor_base'],
    $_POST['ativo'],
    $_POST['id']
]);

header("Location: servicos.php");
exit;
