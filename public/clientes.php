<?php
require "../app/auth/verifica_login.php";
require "../config/database.php";

// Busca clientes
$sql = "SELECT * FROM clientes ORDER BY nome";
$stmt = $pdo->query($sql);
$clientes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Clientes</title>
    
    <style>
    h1 {
        text-align: center;
    }

    a {
        padding-left: 10px;
        text-decoration: none;
    }

    .table-container {
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0,0,0,.08);
    }

    .system-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }

    .system-table th {
        background: #f1f5f9;
        padding: 10px;
        text-align: left;
        border-bottom: 2px solid #e5e7eb;
    }

    .system-table td {
        padding: 10px;
        border-bottom: 1px solid #e5e7eb;
    }

    .badge {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: bold;
    }

    .badge-aberta {
        background: #e0f2fe;
        color: #075985;
    }

    .badge-executando {
        background: #fef9c3;
        color: #854d0e;
    }

    .badge-concluida {
        background: #dcfce7;
        color: #166534;
    }

    .badge-cancelada {
        background: #fee2e2;
        color: #991b1b;
    }

    .botoes {
        text-align: center;
    }

    table {
        display: flex;
        justify-content: center;
        background: none;
        border: none;
        box-shadow: none;
        padding: 0;
        margin: 0;
    }

    .grid {
        width: 100%;
        max-width: 1800px;
        border-collapse: collapse;
        background: #fff;

        /* visual */
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        overflow: hidden; /* mantém o radius */
        box-shadow: 0 4px 10px rgba(0,0,0,.08);
    }

    .grid tbody tr {
        transition: background-color .15s ease-in-out;
    }

    .grid tbody tr:hover {
        background-color: #e0f2fe;
        cursor: pointer;
    }

    .grid tbody tr {
        transition: background-color .15s ease-in-out;
    }

    .header {
        background: #3b82f6;
    }

    .botoes {
    display: flex;
    gap: 12px;
    margin-bottom: 20px;
    }

    /* base do botão */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;

        padding: 10px 18px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;

        transition: all 0.2s ease;
        box-shadow: 0 4px 8px rgba(0,0,0,.08);
    }

    /* hover geral */
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(0,0,0,.15);
    }

    /* botão principal */
    .btn-primary {
        background: #2563eb;
        color: #fff;
    }

    .btn-primary:hover {
        background: #1e40af;
    }

    /* botão secundário */
    .btn-secondary {
        background: #e5e7eb;
        color: #374151;
    }

    .btn-secondary:hover {
        background: #d1d5db;
    }

    .btn-warning {
    background: #facc15;
    color: #1f2937;
    }

    .btn-warning:hover {
        background: #eab308;
    }

    .btn-danger {
    background: #dc2626;
    color: #fff;
    }

    .btn-danger:hover {
        background: #b91c1c;
    }

    .btn.disabled {
    opacity: .6;
    pointer-events: none;
}


</style>
</head>
<body>

<h1>Clientes</h1>

<div class="table">
    <table border="1" cellpadding="8" cellspacing="0" class="grid" style="margin-top:10px;">
        <tr>
            <th class="header">Nome</th>
            <th class="header">CPF / CNPJ</th>
            <th class="header">Endereço</th>
            <th class="header">Email</th>
            <th class="header">Telefone</th>
            <th class="header">Observações</th>
            <th class="header">Status</th>
            <th class="header">Criado em</th>
        </tr>
    
        <?php if (count($clientes) === 0): ?>
            <tr>
                <td colspan="5">Nenhum cliente cadastrado.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($clientes as $c): ?>
            <tr>
                <td><?= htmlspecialchars($c['nome']) ?></td>
                <td><?= htmlspecialchars(($c['cpf_cnpj'] ?? '')) ?></td>
                <td><?= htmlspecialchars($c['endereco'] ?? '') ?></td>
                <td><?= htmlspecialchars($c['email']) ?></td>
                <td><?= htmlspecialchars($c['telefone']) ?></td>
                <td><?= htmlspecialchars($c['observacoes'] ?? '') ?></td>
                <td><?= htmlspecialchars($c['status']) ?></td>
                <td><?= htmlspecialchars($c['criado_em']) ?></td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
</div>


<br>
<div class="botoes">
    <a href="cliente_novo.php" class="btn btn-primary">➕ Novo Cliente</a>
    <a href="cliente_editar.php?id=<?= $c['id'] ?>" class="btn btn-warning">✏️ Editar</a>
    <a href="cliente_excluir.php?id=<?= $c['id'] ?>" class="btn btn-danger"
       onclick="return confirm('Deseja excluir este cliente?')">🗑 Excluir</a>
    <a href="index.php" class="btn btn-secondary">⬅ Voltar ao Dashboard</a>
</div>

</body>
</html>
