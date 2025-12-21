<?php
require __DIR__ . "/../app/auth/seguranca.php";
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>AMDigital ERP</title>

<style>
body {
    font-family: Arial, sans-serif;
    background: #f4f6f8;
    margin: 0;
}

header {
    background: #1f2937;
    color: #fff;
    padding: 20px;
}

header h1 {
    margin: 0;
}

main {
    padding: 30px;
}

.cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px;
}

.card {
    background: #ffffff;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 4px 10px rgba(0,0,0,.08);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: transform .2s ease, box-shadow .2s ease;
}

.card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 20px rgba(0,0,0,.12);
}

.card h3 {
    margin: 0;
    font-size: 18px;
}

.card p {
    color: #555;
    margin: 10px 0 20px;
    font-size: 14px;
}

.card a {
    text-decoration: none;
    color: #fff;
    background: #2563eb;
    padding: 10px;
    border-radius: 6px;
    text-align: center;
    font-weight: bold;
}

.card a:hover {
    background: #1e40af;
}

footer {
    margin-top: 40px;
    text-align: center;
    color: #777;
}
</style>
</head>

<body>

<header>
    <h1>AMDigital ERP</h1>
    <p>Bem-vindo, <?= htmlspecialchars($_SESSION['usuario_nome']) ?></p>
</header>

<main>

<h2>Painel Principal</h2>
<p>Selecione um módulo para iniciar.</p>

<div class="cards">

    <div class="card">
        <h3>👥 Clientes</h3>
        <p>Cadastro e gerenciamento de clientes.</p>
        <a href="clientes.php">Acessar</a>
    </div>

    <div class="card">
        <h3>📁 Projetos</h3>
        <p>Projetos vinculados aos clientes.</p>
        <a href="projetos.php">Acessar</a>
    </div>

    <div class="card">
        <h3>💰 Financeiro</h3>
        <p>Contas a pagar e receber.</p>
        <a href="financeiro.php">Acessar</a>
    </div>

    <div class="card">
        <h3>🧾 Custos</h3>
        <p>Controle de custos fixos e variáveis.</p>
        <a href="custos.php">Acessar</a>
    </div>

    <div class="card">
        <h3>📊 Dashboard</h3>
        <p>Indicadores, lucros e gráficos.</p>
        <a href="dashboard.php">Acessar</a>
    </div>

    <div class="card">
        <h3>👤 Usuários</h3>
        <p>Gerenciamento de usuários do sistema.</p>
        <a href="usuarios.php">Acessar</a>
    </div>

    <div class="card">
        <h3>🚪 Sair</h3>
        <p>Encerrar sessão do sistema.</p>
        <a href="logout.php">Logout</a>
    </div>

</div>

</main>

<footer>
    AMDigital ERP © <?= date('Y') ?>
</footer>

</body>
</html>

