<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

/* =========================
   INDICADORES PRINCIPAIS
========================= */

// CLIENTES
$clientes = $pdo->query("
    SELECT COUNT(*) FROM clientes WHERE status = 'ativo'
")->fetchColumn();

// PROJETOS
$projetos = $pdo->query("
    SELECT COUNT(*) FROM projetos WHERE status IN ('ativo','em_andamento')
")->fetchColumn();

// ORDENS DE SERVIÇO
$os_abertas = $pdo->query("
    SELECT COUNT(*) FROM ordens_servico WHERE status = 'aberta'
")->fetchColumn();

$os_execucao = $pdo->query("
    SELECT COUNT(*) FROM ordens_servico WHERE status = 'em_execucao'
")->fetchColumn();

$os_concluidas = $pdo->query("
    SELECT COUNT(*) FROM ordens_servico WHERE status = 'concluida'
")->fetchColumn();

// FINANCEIRO
$receber = $pdo->query("
    SELECT COALESCE(SUM(valor),0)
    FROM financeiro
    WHERE tipo='receber' AND status='pendente'
")->fetchColumn();

$pagar = $pdo->query("
    SELECT COALESCE(SUM(valor),0)
    FROM financeiro
    WHERE tipo='pagar' AND status='pendente'
")->fetchColumn();

$vencido = $pdo->query("
    SELECT COALESCE(SUM(valor),0)
    FROM financeiro
    WHERE status='pendente' AND vencimento < CURDATE()
")->fetchColumn();

$recebido = $pdo->query("
    SELECT COALESCE(SUM(valor),0)
    FROM financeiro
    WHERE tipo='receber' AND status='pago'
")->fetchColumn();

$pago = $pdo->query("
    SELECT COALESCE(SUM(valor),0)
    FROM financeiro
    WHERE tipo='pagar' AND status='pago'
")->fetchColumn();

// CUSTOS (DESPESAS)
$custos = $pdo->query("
    SELECT COALESCE(SUM(valor),0)
    FROM custos
")->fetchColumn();

// LUCRO REAL
$lucro = ($recebido - $pago) - $custos;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Dashboard</title>

<style>
body {
    font-family: Arial, sans-serif;
    background: #f1f5f9;
}

.container {
    max-width: 1300px;
    margin: 0 auto;
}

h1 {
    margin-bottom: 20px;
}

/* cards */
.cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
}

.card {
    background: #fff;
    padding: 22px;
    border-radius: 14px;
    box-shadow: 0 4px 10px rgba(0,0,0,.08);
    transition: .2s;
}

.card:hover {
    transform: translateY(-4px);
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
.verde { border-left: 6px solid #16a34a; }
.azul { border-left: 6px solid #2563eb; }
.roxo { border-left: 6px solid #8b5cf6; }
.laranja { border-left: 6px solid #f59e0b; }
.vermelho { border-left: 6px solid #dc2626; }
.cinza { border-left: 6px solid #64748b; }

/* menu */
.menu {
    margin-top: 30px;
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
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

<h1>Dashboard Geral</h1>

<div class="cards">

    <div class="card azul">
        <h3>Clientes Ativos</h3>
        <div class="valor"><?= $clientes ?></div>
    </div>

    <div class="card roxo">
        <h3>Projetos Ativos</h3>
        <div class="valor"><?= $projetos ?></div>
    </div>

    <div class="card cinza">
        <h3>OS Abertas</h3>
        <div class="valor"><?= $os_abertas ?></div>
    </div>

    <div class="card laranja">
        <h3>OS em Execução</h3>
        <div class="valor"><?= $os_execucao ?></div>
    </div>

    <div class="card verde">
        <h3>OS Concluídas</h3>
        <div class="valor"><?= $os_concluidas ?></div>
    </div>

    <div class="card azul">
        <h3>A Receber</h3>
        <div class="valor">R$ <?= number_format($receber,2,',','.') ?></div>
    </div>

    <div class="card laranja">
        <h3>A Pagar</h3>
        <div class="valor">R$ <?= number_format($pagar,2,',','.') ?></div>
    </div>

    <div class="card vermelho">
        <h3>Vencido</h3>
        <div class="valor">R$ <?= number_format($vencido,2,',','.') ?></div>
    </div>

    <div class="card cinza">
        <h3>Custos Totais</h3>
        <div class="valor">R$ <?= number_format($custos,2,',','.') ?></div>
    </div>

    <div class="card verde">
        <h3>Lucro Real</h3>
        <div class="valor">R$ <?= number_format($lucro,2,',','.') ?></div>
    </div>

</div>

<div class="menu">
    <a href="clientes.php">Clientes</a>
    <a href="projetos.php">Projetos</a>
    <a href="ordens_servico.php">Ordens de Serviço</a>
    <a href="financeiro.php">Financeiro</a>
    <a href="custos.php">Custos</a>
</div>

</div>
</body>
</html>
