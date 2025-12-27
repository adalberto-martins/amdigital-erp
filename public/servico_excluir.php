<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

$stmt = $pdo->prepare("DELETE FROM servicos WHERE id=?");
$stmt->execute([$_GET['id']]);

header("Location: servicos.php");
exit;
