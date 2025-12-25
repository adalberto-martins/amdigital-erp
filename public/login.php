<?php
if (!isset($_SESSION)) session_start();

$erro = $_SESSION['erro_login'] ?? null;
unset($_SESSION['erro_login']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Login | AMDigital ERP</title>

<style>
body {
    font-family: Arial, sans-serif;
    background: #f1f5f9;
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

.container {
    width: 100%;
    max-width: 420px;
}

.login-card {
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 8px 20px rgba(0,0,0,.12);
}

h1 {
    text-align: center;
    margin-bottom: 5px;
}

.subtitle {
    text-align: center;
    color: #6b7280;
    margin-bottom: 20px;
}

/* formulário */
label {
    font-weight: bold;
    display: block;
    margin-top: 12px;
}

input {
    width: 100%;
    padding: 10px;
    margin-top: 6px;
    border-radius: 8px;
    border: 1px solid #cbd5f5;
}

input:focus {
    outline: none;
    border-color: #2563eb;
}

/* botões */
.btn {
    width: 100%;
    margin-top: 20px;
    padding: 12px;
    border-radius: 8px;
    border: none;
    font-weight: bold;
    cursor: pointer;
    transition: .2s;
}

.btn-primary {
    background: #2563eb;
    color: #fff;
}

.btn-primary:hover {
    background: #1e40af;
}

/* erro */
.erro {
    background: #fee2e2;
    color: #991b1b;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 10px;
    text-align: center;
    border: 1px solid #fecaca;
}
</style>
</head>

<body>

<div class="container">
    <div class="login-card">

        <h1>AMDigital ERP</h1>
        <div class="subtitle">Acesso ao sistema</div>

        <?php if ($erro): ?>
            <div class="erro"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <form method="post" action="login_processa.php">

            <label>E-mail</label>
            <input type="email" name="email" required>

            <label>Senha</label>
            <input type="password" name="senha" required>

            <button type="submit" class="btn btn-primary">
                Entrar
            </button>

        </form>

    </div>
</div>

</body>
</html>
