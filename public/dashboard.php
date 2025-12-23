<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

/* =========================
   INDICADORES
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

// CUSTOS
$custos = $pdo->query("
    SELECT COALESCE(SUM(valor),0) FROM custos
")->fetchColumn();

// LUCRO REAL
$lucro = ($recebido - $pago) - $custos;

$mensal = $pdo->query("
    SELECT 
        DATE_FORMAT(vencimento, '%Y-%m') AS mes,
        SUM(CASE WHEN tipo='receber' THEN valor ELSE 0 END) AS receber,
        SUM(CASE WHEN tipo='pagar' THEN valor ELSE 0 END) AS pagar
    FROM financeiro
    WHERE status IN ('pendente','pago')
    GROUP BY mes
    ORDER BY mes
")->fetchAll(PDO::FETCH_ASSOC);

$meses = [];
$valoresReceber = [];
$valoresPagar = [];

foreach ($mensal as $m) {
    $meses[] = date('m/Y', strtotime($m['mes'].'-01'));
    $valoresReceber[] = (float)$m['receber'];
    $valoresPagar[] = (float)$m['pagar'];
}

$lucroMensalSQL = $pdo->query("
    SELECT 
        mes,
        SUM(recebido) AS recebido,
        SUM(pago) AS pago,
        SUM(custos) AS custos
    FROM (
        SELECT 
            DATE_FORMAT(vencimento, '%Y-%m') AS mes,
            SUM(CASE WHEN tipo='receber' AND status='pago' THEN valor ELSE 0 END) AS recebido,
            SUM(CASE WHEN tipo='pagar' AND status='pago' THEN valor ELSE 0 END) AS pago,
            0 AS custos
        FROM financeiro
        GROUP BY mes

        UNION ALL

        SELECT 
            DATE_FORMAT(data, '%Y-%m') AS mes,
            0, 0,
            SUM(valor) AS custos
        FROM custos
        GROUP BY mes
    ) t
    GROUP BY mes
    ORDER BY mes
")->fetchAll(PDO::FETCH_ASSOC);

$mesesLucro = [];
$valoresLucro = [];

foreach ($lucroMensalSQL as $l) {
    $mesesLucro[] = date('m/Y', strtotime($l['mes'].'-01'));
    $lucroMes = ($l['recebido'] - $l['pago']) - $l['custos'];
    $valoresLucro[] = (float)$lucroMes;
}

/* =========================
   LUCRO POR CLIENTE
========================= */

// Total de custos
$totalCustos = $pdo->query("
    SELECT COALESCE(SUM(valor),0) FROM custos
")->fetchColumn();

// Total de clientes ativos
$totalClientes = $pdo->query("
    SELECT COUNT(*) FROM clientes WHERE status = 'ativo'
")->fetchColumn();

// Custo médio por cliente
$custoPorCliente = $totalClientes > 0 ? ($totalCustos / $totalClientes) : 0;

// Receita e despesas por cliente
$lucroClientes = $pdo->query("
    SELECT 
        c.nome AS cliente,
        COALESCE(SUM(CASE 
            WHEN f.tipo='receber' AND f.status='pago' THEN f.valor 
            ELSE 0 END),0) AS recebido,
        COALESCE(SUM(CASE 
            WHEN f.tipo='pagar' AND f.status='pago' THEN f.valor 
            ELSE 0 END),0) AS pago
    FROM clientes c
    LEFT JOIN financeiro f ON f.cliente_id = c.id
    WHERE c.status = 'ativo'
    GROUP BY c.id
    ORDER BY recebido DESC
")->fetchAll(PDO::FETCH_ASSOC);

$clientesNomes = [];
$clientesLucro = [];

foreach ($lucroClientes as $c) {
    $clientesNomes[] = $c['cliente'];
    $lucro = ($c['recebido'] - $c['pago']) - $custoPorCliente;
    $clientesLucro[] = round($lucro, 2);
}

/* =========================
   LUCRO POR PROJETO
========================= */

// Total de custos
$totalCustos = $pdo->query("
    SELECT COALESCE(SUM(valor),0) FROM custos
")->fetchColumn();

// Total de projetos ativos
$totalProjetos = $pdo->query("
    SELECT COUNT(*) 
    FROM projetos 
    WHERE status IN ('ativo','em_andamento','concluido')
")->fetchColumn();

// Custo médio por projeto
$custoPorProjeto = $totalProjetos > 0 ? ($totalCustos / $totalProjetos) : 0;

// Receita e despesas por projeto
$lucroProjetosSQL = $pdo->query("
    SELECT 
        p.nome AS projeto,
        COALESCE(SUM(CASE 
            WHEN f.tipo='receber' AND f.status='pago' THEN f.valor 
            ELSE 0 END),0) AS recebido,
        COALESCE(SUM(CASE 
            WHEN f.tipo='pagar' AND f.status='pago' THEN f.valor 
            ELSE 0 END),0) AS pago
    FROM projetos p
    LEFT JOIN financeiro f ON f.projeto_id = p.id
    WHERE p.status IN ('ativo','em_andamento','concluido')
    GROUP BY p.id
    ORDER BY recebido DESC
")->fetchAll(PDO::FETCH_ASSOC);

$projetosNomes = [];
$projetosLucro = [];

foreach ($lucroProjetosSQL as $p) {
    $projetosNomes[] = $p['projeto'];
    $lucro = ($p['recebido'] - $p['pago']) - $custoPorProjeto;
    $projetosLucro[] = round($lucro, 2);
}

/* =========================
   MARGEM POR PROJETO (%)
========================= */

$projetosMargem = [];

foreach ($lucroProjetosSQL as $p) {
    $receita = (float)$p['recebido'];
    $lucro   = ($p['recebido'] - $p['pago']) - $custoPorProjeto;

    if ($receita > 0) {
        $margem = ($lucro / $receita) * 100;
    } else {
        $margem = 0;
    }

    $projetosMargem[] = round($margem, 2);
}

/* =========================
   ALERTA DE PROJETOS
========================= */

$projetosAlerta = [];

foreach ($lucroProjetosSQL as $index => $p) {

    $receita = (float)$p['recebido'];
    $lucro   = ($p['recebido'] - $p['pago']) - $custoPorProjeto;
    $margem  = $receita > 0 ? ($lucro / $receita) * 100 : 0;

    if ($lucro < 0) {
        $projetosAlerta[] = [
            'projeto' => $p['projeto'],
            'tipo'    => 'critico',
            'lucro'   => round($lucro,2),
            'margem'  => round($margem,2)
        ];
    } elseif ($margem < 20) {
        $projetosAlerta[] = [
            'projeto' => $p['projeto'],
            'tipo'    => 'atencao',
            'lucro'   => round($lucro,2),
            'margem'  => round($margem,2)
        ];
    }
}

/* =========================
   ALERTA POR CLIENTE
========================= */

$clientesAlerta = [];

foreach ($lucroClientes as $c) {

    $receita = (float)$c['recebido'];
    $lucro   = ($c['recebido'] - $c['pago']) - $custoPorCliente;
    $margem  = $receita > 0 ? ($lucro / $receita) * 100 : 0;

    if ($lucro < 0) {
        $clientesAlerta[] = [
            'cliente' => $c['cliente'],
            'tipo'    => 'critico',
            'lucro'   => round($lucro,2),
            'margem'  => round($margem,2)
        ];
    } elseif ($margem < 20) {
        $clientesAlerta[] = [
            'cliente' => $c['cliente'],
            'tipo'    => 'atencao',
            'lucro'   => round($lucro,2),
            'margem'  => round($margem,2)
        ];
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Dashboard</title>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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

/* gráficos */
.graficos {
    margin-top: 40px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
}

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
    <div class="card azul"><h3>Clientes Ativos</h3><div class="valor"><?= $clientes ?></div></div>
    <div class="card roxo"><h3>Projetos Ativos</h3><div class="valor"><?= $projetos ?></div></div>
    <div class="card cinza"><h3>OS Abertas</h3><div class="valor"><?= $os_abertas ?></div></div>
    <div class="card laranja"><h3>OS em Execução</h3><div class="valor"><?= $os_execucao ?></div></div>
    <div class="card verde"><h3>OS Concluídas</h3><div class="valor"><?= $os_concluidas ?></div></div>
    <div class="card azul"><h3>A Receber</h3><div class="valor">R$ <?= number_format($receber,2,',','.') ?></div></div>
    <div class="card laranja"><h3>A Pagar</h3><div class="valor">R$ <?= number_format($pagar,2,',','.') ?></div></div>
    <div class="card vermelho"><h3>Vencido</h3><div class="valor">R$ <?= number_format($vencido,2,',','.') ?></div></div>
    <div class="card cinza"><h3>Custos</h3><div class="valor">R$ <?= number_format($custos,2,',','.') ?></div></div>
    <div class="card verde"><h3>Lucro Real</h3><div class="valor">R$ <?= number_format($lucro,2,',','.') ?></div></div>
</div>

    
    <div class="graficos">

        <?php if (!empty($projetosAlerta)): ?>
            <div class="card" style="grid-column:1 / -1; border-left:6px solid #dc2626;">
                <h3>⚠️ Projetos em Alerta</h3>

                <table class="system-table" style="margin-top:15px;">
                    <thead>
                        <tr>
                            <th>Projeto</th>
                            <th>Status</th>
                            <th>Lucro</th>
                            <th>Margem</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($projetosAlerta as $a): ?>
                        <tr>
                            <td><?= htmlspecialchars($a['projeto']) ?></td>
                            <td>
                                <?php if ($a['tipo']=='critico'): ?>
                                    <span style="color:#dc2626;font-weight:bold;">CRÍTICO</span>
                                <?php else: ?>
                                    <span style="color:#f59e0b;font-weight:bold;">ATENÇÃO</span>
                                <?php endif; ?>
                            </td>
                            <td>R$ <?= number_format($a['lucro'],2,',','.') ?></td>
                            <td><?= number_format($a['margem'],2,',','.') ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>


    <?php if (!empty($clientesAlerta)): ?>
<div class="card" style="grid-column:1 / -1; border-left:6px solid #f59e0b;">
    <h3>⚠️ Clientes em Alerta</h3>

    <table class="system-table" style="margin-top:15px;">
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Status</th>
                <th>Lucro</th>
                <th>Margem</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($clientesAlerta as $a): ?>
            <tr>
                <td><?= htmlspecialchars($a['cliente']) ?></td>
                <td>
                    <?php if ($a['tipo']=='critico'): ?>
                        <span style="color:#dc2626;font-weight:bold;">CRÍTICO</span>
                    <?php else: ?>
                        <span style="color:#f59e0b;font-weight:bold;">ATENÇÃO</span>
                    <?php endif; ?>
                </td>
                <td>R$ <?= number_format($a['lucro'],2,',','.') ?></td>
                <td><?= number_format($a['margem'],2,',','.') ?>%</td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?> 



        <div class="card">
            <h3>Financeiro</h3>
            <canvas id="graficoFinanceiro"></canvas>
        </div>

        <div class="card">
            <h3>Ordens de Serviço</h3>
            <canvas id="graficoOS"></canvas>
        </div>

        <div class="card" style="grid-column: 1 / -1;">
            <h3>Fluxo Mensal (Receber × Pagar)</h3>
            <canvas id="graficoMensal"></canvas>
        </div>

        <div class="card" style="grid-column: 1 / -1;">
            <h3>Lucro Mensal</h3>
            <canvas id="graficoLucroMensal"></canvas>
        </div>

        <div class="card" style="grid-column: 1 / -1;">
            <h3>Lucro por Cliente</h3>
            <canvas id="graficoLucroCliente"></canvas>
        </div>

        <div class="card" style="grid-column: 1 / -1;">
            <h3>Lucro por Projeto</h3>
            <canvas id="graficoLucroProjeto"></canvas>
        </div>

        <div class="card" style="grid-column: 1 / -1;">
            <h3>Margem de Lucro por Projeto (%)</h3>
            <canvas id="graficoMargemProjeto"></canvas>
        </div>
    </div>
</div>

<div class="menu">
    <a href="clientes.php">Clientes</a>
    <a href="projetos.php">Projetos</a>
    <a href="ordens_servico.php">OS</a>
    <a href="financeiro.php">Financeiro</a>
    <a href="custos.php">Custos</a>
</div>

</div>

<script>
// Gráfico Financeiro
new Chart(document.getElementById('graficoFinanceiro'), {
    type: 'bar',
    data: {
        labels: ['A Receber', 'A Pagar', 'Vencido', 'Lucro'],
        datasets: [{
            data: [
                <?= $receber ?>,
                <?= $pagar ?>,
                <?= $vencido ?>,
                <?= $lucro ?>
            ],
            backgroundColor: ['#2563eb','#f59e0b','#dc2626','#16a34a']
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } }
    }
});

// Gráfico Mensal
new Chart(document.getElementById('graficoMensal'), {
    type: 'line',
    data: {
        labels: <?= json_encode($meses) ?>,
        datasets: [
            {
                label: 'A Receber',
                data: <?= json_encode($valoresReceber) ?>,
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37,99,235,0.1)',
                tension: 0.4
            },
            {
                label: 'A Pagar',
                data: <?= json_encode($valoresPagar) ?>,
                borderColor: '#dc2626',
                backgroundColor: 'rgba(220,38,38,0.1)',
                tension: 0.4
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' }
        }
    }
});


// Gráfico de Lucro Mensal
new Chart(document.getElementById('graficoLucroMensal'), {
    type: 'line',
    data: {
        labels: <?= json_encode($mesesLucro) ?>,
        datasets: [{
            label: 'Lucro',
            data: <?= json_encode($valoresLucro) ?>,
            borderColor: '#16a34a',
            backgroundColor: 'rgba(22,163,74,0.15)',
            tension: 0.4,
            fill: true,
            pointRadius: 5
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' }
        }
    }
});

// Gráfico de Lucro por Cliente
new Chart(document.getElementById('graficoLucroCliente'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($clientesNomes) ?>,
        datasets: [{
            label: 'Lucro (R$)',
            data: <?= json_encode($clientesLucro) ?>,
            backgroundColor: '#8b5cf6'
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});


// Gráfico de Lucro por Projeto
new Chart(document.getElementById('graficoLucroProjeto'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($projetosNomes) ?>,
        datasets: [{
            label: 'Lucro (R$)',
            data: <?= json_encode($projetosLucro) ?>,
            backgroundColor: '#2563eb'
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});


// Gráfico de Margem por Projeto
new Chart(document.getElementById('graficoMargemProjeto'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($projetosNomes) ?>,
        datasets: [{
            label: 'Margem (%)',
            data: <?= json_encode($projetosMargem) ?>,
            backgroundColor: '#16a34a'
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return value + '%';
                    }
                }
            }
        }
    }
});

// Gráfico OS
new Chart(document.getElementById('graficoOS'), {
    type: 'doughnut',
    data: {
        labels: ['Abertas', 'Em Execução', 'Concluídas'],
        datasets: [{
            data: [
                <?= $os_abertas ?>,
                <?= $os_execucao ?>,
                <?= $os_concluidas ?>
            ],
            backgroundColor: ['#64748b','#8b5cf6','#16a34a']
        }]
    },
    options: { responsive: true }
});
</script>

</body>
</html>
