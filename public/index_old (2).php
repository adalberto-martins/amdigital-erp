<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

/* =========================
   INDICADORES DE PROJETOS
========================= */

// Quantidade
$totalProjetos = $pdo->query("SELECT COUNT(*) FROM projetos")->fetchColumn();

$emAndamento = $pdo->query("
    SELECT COUNT(*) FROM projetos WHERE status = 'em_andamento'
")->fetchColumn();

$concluidos = $pdo->query("
    SELECT COUNT(*) FROM projetos WHERE status = 'concluido'
")->fetchColumn();

$cancelados = $pdo->query("
    SELECT COUNT(*) FROM projetos WHERE status = 'cancelado'
")->fetchColumn();

// Valores
$valorTotal = $pdo->query("
    SELECT COALESCE(SUM(valor),0) FROM projetos
")->fetchColumn();

$valorEmAndamento = $pdo->query("
    SELECT COALESCE(SUM(valor),0)
    FROM projetos
    WHERE status = 'em_andamento'
")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Dashboard - AMDigital ERP</title>

<style>
body {
    font-family: Arial, sans-serif;
    background: #f1f5f9;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
}

h1 {
    margin: 20px 0;
}

/* cards */
.cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
}

.card {
    background: #fff;
    border-radius: 14px;
    padding: 20px;
    box-shadow: 0 4px 10px rgba(0,0,0,.08);
    transition: .2s;
}

.card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 20px rgba(0,0,0,.12);
}

.card h3 {
    margin: 0;
    font-size: 14px;
    color: #64748b;
}

.card .valor {
    font-size: 26px;
    font-weight: bold;
    margin-top: 6px;
}

/* cores */
.roxo { border-left: 6px solid #8b5cf6; }
.azul { border-left: 6px solid #2563eb; }
.verde { border-left: 6px solid #16a34a; }
.vermelho { border-left: 6px solid #dc2626; }
.teal { border-left: 6px solid #0f766e; }
.laranja { border-left: 6px solid #f59e0b; }

/* menu */
.menu {
    margin-top: 30px;
    display: flex;
    gap: 12px;
}

.menu a {
    text-decoration: none;
    padding: 12px 18px;
    background: #e5e7eb;
    border-radius: 8px;
    color: #374151;
    font-weight: bold;
    transition: .2s;
}

.menu a:hover {
    background: #d1d5db;
}
</style>
</head>

<body>
<div class="container">

<h1>Dashboard</h1>

<!-- INDICADORES -->
<div class="cards">

    <div class="card roxo">
        <h3>Total de Projetos</h3>
        <div class="valor"><?= $totalProjetos ?></div>
    </div>

    <div class="card azul">
        <h3>Projetos em Andamento</h3>
        <div class="valor"><?= $emAndamento ?></div>
    </div>

    <div class="card verde">
        <h3>Projetos Concluídos</h3>
        <div class="valor"><?= $concluidos ?></div>
    </div>

    <div class="card vermelho">
        <h3>Projetos Cancelados</h3>
        <div class="valor"><?= $cancelados ?></div>
    </div>

    <div class="card teal">
        <h3>Valor Total em Projetos</h3>
        <div class="valor">R$ <?= number_format($valorTotal,2,',','.') ?></div>
    </div>

    <div class="card laranja">
        <h3>Valor em Andamento</h3>
        <div class="valor">R$ <?= number_format($valorEmAndamento,2,',','.') ?></div>
    </div>

</div>

<!-- MENU -->
<div class="menu">
    <a href="clientes.php">Clientes</a>
    <a href="projetos.php">Projetos</a>
    <a href="index.php">Dashboard</a>
</div>

</div>
</body>
</html>
