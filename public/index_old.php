<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

// GARANTIA DEFENSIVA (pode remover depois)
if (!isset($pdo)) {
    die("Erro crítico: conexão com banco não encontrada.");
}
// ORDEM DE SERVIÇO - INDICADORES
$totalOS = $pdo->query("SELECT COUNT(*) FROM ordens_servico")->fetchColumn();

$osAbertas = $pdo->query("
    SELECT COUNT(*) FROM ordens_servico WHERE status = 'aberta'
")->fetchColumn();

$osExecutando = $pdo->query("
    SELECT COUNT(*) FROM ordens_servico WHERE status = 'executando'
")->fetchColumn();

$osCanceladas= $pdo->query("
    SELECT COUNT(*) FROM ordens_servico WHERE status = 'cancelada'
")->fetchColumn();

$osConcluidas = $pdo->query("
    SELECT COUNT(*) FROM ordens_servico WHERE status = 'concluida'
")->fetchColumn();
// PROJETOS - INDICADORES
$totalProjetos = $pdo->query("SELECT COUNT(*) FROM projetos")->fetchColumn();

$projetosOrcamentos = $pdo->query("
    SELECT COUNT(*) FROM ordens_servico WHERE status = 'orcamento'
")->fetchColumn();

$projetosEm_Andamento = $pdo->query("
    SELECT COUNT(*) FROM ordens_servico WHERE status = 'andamento'
")->fetchColumn();

$projetosFinalizado = $pdo->query("
    SELECT COUNT(*) FROM ordens_servico WHERE status = 'finalizado'
")->fetchColumn();

// ===============================
// INDICADORES - FINANCEIRO
// ===============================

// A RECEBER (pendente)
$totalReceber = $pdo->query("
    SELECT IFNULL(SUM(valor),0)
    FROM financeiro
    WHERE tipo = 'receber' AND status = 'pendente'
")->fetchColumn();

// A PAGAR (pendente)
$totalPagar = $pdo->query("
    SELECT IFNULL(SUM(valor),0)
    FROM financeiro
    WHERE tipo = 'pagar' AND status = 'pendente'
")->fetchColumn();

// RECEBIDO
$totalRecebido = $pdo->query("
    SELECT IFNULL(SUM(valor),0)
    FROM financeiro
    WHERE tipo = 'receber' AND status = 'pago'
")->fetchColumn();

// PAGO
$totalPago = $pdo->query("
    SELECT IFNULL(SUM(valor),0)
    FROM financeiro
    WHERE tipo = 'pagar' AND status = 'pago'
")->fetchColumn();

$receberVencido = $pdo->query("
    SELECT IFNULL(SUM(valor),0)
    FROM financeiro
    WHERE tipo = 'receber'
      AND status = 'pendente'
      AND vencimento < CURDATE()
")->fetchColumn();

// A PAGAR VENCIDOS
$pagarVencido = $pdo->query("
    SELECT IFNULL(SUM(valor),0)
    FROM financeiro
    WHERE tipo = 'pagar'
      AND status = 'pendente'
      AND vencimento < CURDATE()
")->fetchColumn();

// SALDO ATUAL
$saldoAtual = ($totalReceber + $totalRecebido) - ($totalPagar + $totalPago);

// ===============================
// INDICADORES - CUSTOS
// ===============================

// TOTAL DE CUSTOS
$totalCustos = $pdo->query("
    SELECT IFNULL(SUM(valor),0)
    FROM custos
")->fetchColumn();

// CUSTOS FIXOS
$custosFixos = $pdo->query("
    SELECT IFNULL(SUM(valor),0)
    FROM custos
    WHERE tipo = 'fixo'
")->fetchColumn();

// CUSTOS VARIÁVEIS
$custosVariaveis = $pdo->query("
    SELECT IFNULL(SUM(valor),0)
    FROM custos
    WHERE tipo = 'variavel'
")->fetchColumn();

// CUSTOS DO MÊS ATUAL
$custosMes = $pdo->query("
    SELECT IFNULL(SUM(valor),0)
    FROM custos
    WHERE MONTH(data) = MONTH(CURDATE())
      AND YEAR(data) = YEAR(CURDATE())
")->fetchColumn();

// CUSTOS VENCIDOS (DATA MENOR QUE HOJE)
$custosVencidos = $pdo->query("
    SELECT IFNULL(SUM(valor),0)
    FROM custos
    WHERE data < CURDATE()
")->fetchColumn();


// ===============================
// INDICADORES - CLIENTES
// ===============================

// TOTAL DE CLIENTES
$totalClientes = $pdo->query("
    SELECT COUNT(*) FROM clientes
")->fetchColumn();

// CLIENTES ATIVOS
$clientesAtivos = $pdo->query("
    SELECT COUNT(*) FROM clientes
    WHERE status = 'ativo'
")->fetchColumn();

// CLIENTES INATIVOS
$clientesInativos = $pdo->query("
    SELECT COUNT(*) FROM clientes
    WHERE status = 'inativo'
")->fetchColumn();

// CLIENTES COM OS ABERTA
$clientesComOS = $pdo->query("
    SELECT COUNT(DISTINCT cliente_id)
    FROM ordens_servico
    WHERE status IN ('aberta','executando')
")->fetchColumn();

// ===============================
// DASHBOARD - VISÃO GERAL
// ===============================

// CLIENTES ATIVOS
$clientesAtivos = $pdo->query("
    SELECT COUNT(*) FROM clientes WHERE status = 'ativo'
")->fetchColumn();

// OS EM ANDAMENTO
$osEmAndamento = $pdo->query("
    SELECT COUNT(*) FROM ordens_servico
    WHERE status IN ('aberta','executando')
")->fetchColumn();

// RECEITA EM ABERTO
$receitaAberta = $pdo->query("
    SELECT IFNULL(SUM(valor),0)
    FROM financeiro
    WHERE tipo = 'receber' AND status = 'pendente'
")->fetchColumn();

// CUSTOS DO MÊS
$custosMes = $pdo->query("
    SELECT IFNULL(SUM(valor),0)
    FROM custos
    WHERE MONTH(data) = MONTH(CURDATE())
      AND YEAR(data) = YEAR(CURDATE())
")->fetchColumn();

// SALDO ATUAL
$saldoAtual = $pdo->query("
    SELECT
      (SELECT IFNULL(SUM(valor),0) FROM financeiro WHERE tipo='receber')
    - (SELECT IFNULL(SUM(valor),0) FROM financeiro WHERE tipo='pagar')
")->fetchColumn();

// LUCRO ESTIMADO DO MÊS
$receitaMes = $pdo->query("
    SELECT IFNULL(SUM(valor),0)
    FROM financeiro
    WHERE tipo='receber'
      AND MONTH(vencimento)=MONTH(CURDATE())
      AND YEAR(vencimento)=YEAR(CURDATE())
")->fetchColumn();

$lucroMes = $receitaMes - $custosMes;
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

.dashboard-hero {
    margin-bottom: 30px;
}

.hero-card {
    width: 100%;
    padding: 30px;
}

.hero-big {
    font-size: 42px;
    font-weight: bold;
    margin: 10px 0;
}

.hero-metrics {
    margin-top: 20px;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 20px;
}

.hero-metrics div {
    background: #f8fafc;
    padding: 14px;
    border-radius: 10px;
    text-align: center;
}

.hero-metrics strong {
    font-size: 18px;
}

.dashboard-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 24px;
}

.dashboard-cards .card {
    min-height: 180px;
}

footer {
    margin-top: 40px;
    text-align: center;
    color: #777;
}

.dashboard-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 20px;
}

.card {
    background: #ffffff;
    border-radius: 14px;
    padding: 20px;
    box-shadow: 0 4px 10px rgba(0,0,0,.08);
    transition: .2s;
}

.card:hover {
    transform: translateY(-2px);
}

.card h3 {
    margin: 0 0 10px;
    font-size: 16px;
}

.card .big {
    font-size: 32px;
    font-weight: bold;
}

.card small {
    color: #6b7280;
}

.card a {
    display: inline-block;
    margin-top: 12px;
    text-decoration: none;
    font-weight: bold;
}

.card-clientes {
    border-left: 5px solid #3b82f6 ; /* azul */
}

.card-projetos {
    border-left: 5px solid #8b5cf6; /* roxo */
}

.card-os {
    border-left: 5px solid #2563eb; /* azul forte */
}

.card-financeiro {
    border-left: 5px solid #16a34a; /* verde */
}

.card-custos {
    border-left: 5px solid #dc2626; /* vermelho */
}

.card-usuarios {
    border-left: 5px solid #374151; /* cinza */
}

.card-dashboard {
    border-left: 5px solid #0ea5e9; /* Azul neutro */
}

.card-sair {
    border-left: 5px solid #b91c1c; /* Vermelho escuro */
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

<div class="card card-clientes">
    <h3>👥 Clientes</h3>

    <div class="big"><?= $totalClientes ?></div>
    <small>Total cadastrados</small>

    <hr>

    <small>
        ✅ Ativos: <?= $clientesAtivos ?><br>
        ⛔ Inativos: <?= $clientesInativos ?><br>
        🧾 Com OS ativa: <?= $clientesComOS ?>
    </small>

    <a href="clientes.php">➡ Acessar 👥</a>
</div>


    <div class="card card-projetos">
        <h3>📁 Projetos</h3>

        <div class="big"><?= $totalProjetos ?></div>
        <small>Total cadastradas</small>

        <hr>

        <small>
            🟦 Orçamentos: <?= $projetosOrcamentos ?><br>
            🟨 Em andamento: <?= $osExecutando ?><br>
            🟩 Finalizado: <?= $osConcluidas ?>

        </small>
        <a href="projetos.php">➡ Acessar 📁</a>
    </div>

<div class="card card-financeiro">
    <h3>💰 Financeiro</h3>

    <div class="big">
        R$ <?= number_format($saldoAtual, 2, ',', '.') ?>
    </div>
    <small>Saldo atual</small>

    <hr>

    <small>
        💵 A Receber: R$ <?= number_format($totalReceber, 2, ',', '.') ?><br>
        💸 A Pagar: R$ <?= number_format($totalPagar, 2, ',', '.') ?><br>

        ⏰ Receber Vencido:
            <strong style="color:#dc2626">
                 R$ <?= number_format($receberVencido, 2, ',', '.') ?>
            </strong><br>

        ⏰ Pagar Vencido:
            <strong style="color:#dc2626">
                 R$ <?= number_format($pagarVencido, 2, ',', '.') ?>
            </strong><br>

        ✅ Recebido: R$ <?= number_format($totalRecebido, 2, ',', '.') ?><br>
        ❌ Pago: R$ <?= number_format($totalPago, 2, ',', '.') ?>

    </small>

    <a href="financeiro.php">➡ Acessar 💰</a>
</div>


<div class="card card-custos">
    <h3>🧾 Custos</h3>

    <div class="big">
        R$ <?= number_format($totalCustos, 2, ',', '.') ?>
    </div>
    <small>Total de custos</small>

    <hr>

    <small>
        💸 Fixos: R$ <?= number_format($custosFixos, 2, ',', '.') ?><br>
        🔀 Variáveis: R$ <?= number_format($custosVariaveis, 2, ',', '.') ?><br>
        📆 Mês atual: R$ <?= number_format($custosMes, 2, ',', '.') ?><br>

        ⏰ Vencidos:
        <strong style="color:#dc2626">
            R$ <?= number_format($custosVencidos, 2, ',', '.') ?>
        </strong>
    </small>

    <a href="custos.php">➡ Acessar 🧾</a>
</div>


<div class="card card-dashboard">
    <h3>📊 Visão Geral</h3>

    <div class="big">
        R$ <?= number_format($saldoAtual, 2, ',', '.') ?>
    </div>
    <small>Saldo atual da empresa</small>

    <hr>

    <small>
        👥 Clientes ativos: <?= $clientesAtivos ?><br>
        🧾 OS em andamento: <?= $osEmAndamento ?><br>
        💵 Receita em aberto: R$ <?= number_format($receitaAberta,2,',','.') ?><br>
        💸 Custos do mês: R$ <?= number_format($custosMes,2,',','.') ?><br>
        📈 Lucro estimado (mês): 
        <strong><?= number_format($lucroMes,2,',','.') ?></strong>
    </small>
</div>


    <div class="card card-usuarios">
        <h3>👤 Usuários</h3>
        <p>Gerenciamento de usuários do sistema.</p>
        <a href="usuarios.php">➡ Acessar 👤</a>
    </div>

        <div class="card card-os">
        <h3>⚙️ Ordens de Serviço</h3>

        <div class="big"><?= $totalOS ?></div>
        <small>Total cadastradas</small>

        <hr>

        <small>
            🟦 Abertas: <?= $osAbertas ?><br>
            🟨 Executando: <?= $osExecutando ?><br>
            🟥 Canceladas: <?= $osCanceladas ?><br>
            🟩 Concluídas: <?= $osConcluidas ?>
        </small>

        <a href="ordens_servico.php">➡ Acessar ⚙️</a>
    </div>

    <div class="card card-sair">
        <h3>🚪 Sair</h3>
        <p>Encerrar sessão do sistema.</p>
        <a href="logout.php" onclick="return confirm('Deseja realmente sair do Sistema?')">➡ Logout 🚪</a>
    </div>

</div>


</div>

</main>

<footer>
    AMDigital ERP © <?= date('Y') ?>
</footer>

</body>
</html>

