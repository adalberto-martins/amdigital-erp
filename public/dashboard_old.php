<?php
require "../app/auth/verifica_login.php";
require "../config/database.php";

/* =========================
   INDICADORES GERAIS
========================= */

// Total a receber
$sql = "SELECT SUM(valor) AS total 
        FROM financeiro 
        WHERE tipo = 'receber'";
$totalReceber = $pdo->query($sql)->fetch()['total'] ?? 0;

// Total a pagar
$sql = "SELECT SUM(valor) AS total 
        FROM financeiro 
        WHERE tipo = 'pagar'";
$totalPagar = $pdo->query($sql)->fetch()['total'] ?? 0;

// Total de custos
$sql = "SELECT SUM(valor) AS total FROM custos";
$totalCustos = $pdo->query($sql)->fetch()['total'] ?? 0;

// Lucro geral
$lucroGeral = $totalReceber - $totalPagar - $totalCustos;

/* =========================
   LUCRO POR CLIENTE
========================= */

$sql = "
    SELECT 
        c.nome AS cliente,
        SUM(CASE WHEN f.tipo = 'receber' THEN f.valor ELSE 0 END) AS receita,
        SUM(CASE WHEN f.tipo = 'pagar' THEN f.valor ELSE 0 END) AS despesa
    FROM clientes c
    LEFT JOIN financeiro f ON f.cliente_id = c.id
    GROUP BY c.id
    ORDER BY c.nome
";
$lucroClientes = $pdo->query($sql)->fetchAll();

/* =========================
   LUCRO POR PROJETO
========================= */

$sql = "
    SELECT
        p.nome AS projeto,
        c.nome AS cliente,
        SUM(CASE WHEN f.tipo = 'receber' THEN f.valor ELSE 0 END) AS receita,
        SUM(CASE WHEN f.tipo = 'pagar' THEN f.valor ELSE 0 END) AS despesa
    FROM projetos p
    LEFT JOIN clientes c ON c.id = p.cliente_id
    LEFT JOIN financeiro f ON f.projeto_id = p.id
    GROUP BY p.id
    ORDER BY p.nome
";
$lucroProjetos = $pdo->query($sql)->fetchAll();

// Lucro 

$sql = "
    SELECT 
        DATE_FORMAT(vencimento, '%Y-%m') AS mes,
        SUM(CASE WHEN tipo = 'receber' THEN valor ELSE 0 END) -
        SUM(CASE WHEN tipo = 'pagar' THEN valor ELSE 0 END) AS lucro
    FROM financeiro
    GROUP BY mes
    ORDER BY mes
";

$lucroMensal = $pdo->query($sql)->fetchAll();

$labels = [];
$valores = [];

foreach ($lucroMensal as $l) {
    $labels[] = $l['mes'];
    $valores[] = $l['lucro'];
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Dashboard Financeiro</title>
</head>
<body>

<h1>Dashboard Financeiro</h1>

<h2>Resumo Geral</h2>
<ul>
    <li><strong>Total a Receber:</strong> R$ <?= number_format($totalReceber, 2, ',', '.') ?></li>
    <li><strong>Total a Pagar:</strong> R$ <?= number_format($totalPagar, 2, ',', '.') ?></li>
    <li><strong>Custos:</strong> R$ <?= number_format($totalCustos, 2, ',', '.') ?></li>
    <li><strong>Lucro Geral:</strong> R$ <?= number_format($lucroGeral, 2, ',', '.') ?></li>
</ul>

<hr>

<h2>Lucro Mensal</h2>
<canvas id="graficoLucro" width="400" height="150"></canvas>


<hr>

<h2>Lucro por Cliente</h2>
<table border="1" cellpadding="8" cellspacing="0">
<tr>
    <th>Cliente</th>
    <th>Receita</th>
    <th>Despesa</th>
    <th>Lucro</th>
</tr>

<?php foreach ($lucroClientes as $lc):
    $lucroCliente = ($lc['receita'] ?? 0) - ($lc['despesa'] ?? 0);
?>
<tr>
    <td><?= htmlspecialchars($lc['cliente']) ?></td>
    <td>R$ <?= number_format($lc['receita'] ?? 0, 2, ',', '.') ?></td>
    <td>R$ <?= number_format($lc['despesa'] ?? 0, 2, ',', '.') ?></td>
    <td><strong>R$ <?= number_format($lucroCliente, 2, ',', '.') ?></strong></td>
</tr>
<?php endforeach; ?>
</table>

<hr>

<h2>Lucro por Projeto</h2>
<table border="1" cellpadding="8" cellspacing="0">
<tr>
    <th>Projeto</th>
    <th>Cliente</th>
    <th>Receita</th>
    <th>Despesa</th>
    <th>Lucro</th>
</tr>

<?php foreach ($lucroProjetos as $lp):
    $lucroProjeto = ($lp['receita'] ?? 0) - ($lp['despesa'] ?? 0);
?>
<tr>
    <td><?= htmlspecialchars($lp['projeto']) ?></td>
    <td><?= htmlspecialchars($lp['cliente'] ?? '—') ?></td>
    <td>R$ <?= number_format($lp['receita'] ?? 0, 2, ',', '.') ?></td>
    <td>R$ <?= number_format($lp['despesa'] ?? 0, 2, ',', '.') ?></td>
    <td><strong>R$ <?= number_format($lucroProjeto, 2, ',', '.') ?></strong></td>
</tr>
<?php endforeach; ?>
</table>

<hr>

<nav>
    <a href="clientes.php">Clientes</a> |
    <a href="projetos.php">Projetos</a> |
    <a href="financeiro.php">Financeiro</a> |
    <a href="custos.php">Custos</a> |
    <a href="index.php">Sair</a>
</nav>

<script>
const ctx = document.getElementById('graficoLucro').getContext('2d');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [{
            label: 'Lucro Mensal (R$)',
            data: <?= json_encode($valores) ?>,
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

</body>
</html>
