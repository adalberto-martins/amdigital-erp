<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

/* =========================
   INDICADORES FINANCEIROS
========================= */

// A RECEBER (pendente + futuro)
$receber = $pdo->query("
    SELECT COALESCE(SUM(valor),0)
    FROM financeiro
    WHERE tipo = 'receber' AND status = 'pendente'
")->fetchColumn();

// RECEBIDO
$recebido = $pdo->query("
    SELECT COALESCE(SUM(valor),0)
    FROM financeiro
    WHERE tipo = 'receber' AND status = 'pago'
")->fetchColumn();

// A PAGAR
$pagar = $pdo->query("
    SELECT COALESCE(SUM(valor),0)
    FROM financeiro
    WHERE tipo = 'pagar' AND status = 'pendente'
")->fetchColumn();

// PAGO
$pago = $pdo->query("
    SELECT COALESCE(SUM(valor),0)
    FROM financeiro
    WHERE tipo = 'pagar' AND status = 'pago'
")->fetchColumn();

// VENCIDO (dinâmico)
$vencido = $pdo->query("
    SELECT COALESCE(SUM(valor),0)
    FROM financeiro
    WHERE status = 'pendente'
      AND vencimento < CURDATE()
")->fetchColumn();

// SALDO
$saldo = ($recebido - $pago);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Dashboard Financeiro</title>

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
.verde { border-left: 6px solid #16a34a; }
.azul { border-left: 6px solid #2563eb; }
.vermelho { border-left: 6px solid #dc2626; }
.laranja { border-left: 6px solid #f59e0b; }
.roxo { border-left: 6px solid #8b5cf6; }
.cinza { border-left: 6px solid #64748b; }

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

<h1>Dashboard Financeiro</h1>

<div class="cards">

    <div class="card azul">
        <h3>A Receber</h3>
        <div class="valor">R$ <?= number_format($receber,2,',','.') ?></div>
    </div>

    <div class="card verde">
        <h3>Recebido</h3>
        <div class="valor">R$ <?= number_format($recebido,2,',','.') ?></div>
    </div>

    <div class="card laranja">
        <h3>A Pagar</h3>
        <div class="valor">R$ <?= number_format($pagar,2,',','.') ?></div>
    </div>

    <div class="card cinza">
        <h3>Pago</h3>
        <div class="valor">R$ <?= number_format($pago,2,',','.') ?></div>
    </div>

    <div class="card vermelho">
        <h3>Vencido</h3>
        <div class="valor">R$ <?= number_format($vencido,2,',','.') ?></div>
    </div>

    <div class="card roxo">
        <h3>Saldo Atual</h3>
        <div class="valor">R$ <?= number_format($saldo,2,',','.') ?></div>
    </div>

</div>

<div class="menu">
    <a href="financeiro.php">Financeiro</a>
    <a href="index.php">Dashboard Geral</a>
</div>

</div>
</body>
</html>
