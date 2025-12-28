<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

/* validação básica */
$id     = $_POST['id'] ?? null;
$nome   = trim($_POST['nome'] ?? '');
$email  = trim($_POST['email'] ?? '');
$nivel  = $_POST['nivel'] ?? 'usuario';
$status = $_POST['status'] ?? 'ativo';
$senha  = $_POST['senha'] ?? '';

if (!$id || !$nome || !$email) {
    die("Dados inválidos");
}

/* impedir que usuário se desative sozinho */
if ($id == $_SESSION['usuario_id'] && $status !== 'ativo') {
    die("Você não pode desativar o próprio usuário");
}

/* atualizar dados sem senha */
$sql = "
    UPDATE usuarios SET
        nome   = ?,
        email  = ?,
        nivel  = ?,
        status = ?
";

/* parâmetros iniciais */
$params = [$nome, $email, $nivel, $status];

/* se senha foi preenchida, atualizar também */
if (!empty($senha)) {

    if (strlen($senha) < 8) {
        die("A senha deve ter no mínimo 8 caracteres");
    }

    $hash = password_hash($senha, PASSWORD_DEFAULT);
    $sql .= ", senha = ?";
    $params[] = $hash;
}

/* finalizar query */
$sql .= " WHERE id = ?";
$params[] = $id;

/* executar */
$stmt = $pdo->prepare($sql);
$stmt->execute($params);

/* redirecionar */
header("Location: usuarios.php");
exit;

