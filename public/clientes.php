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
</style>
</head>
<body>

<h1>Clientes</h1>

<a href="cliente_novo.php">➕ Novo Cliente</a>

<table border="1" cellpadding="8" cellspacing="0" style="margin-top:10px;">
    <tr>
        <th>Id</th>
        <th>Nome</th>
        <th>CPF / CNPJ</th>
        <th>Endereço</th>
        <th>Email</th>
        <th>Telefone</th>
        <th>Observações</th>
        <th>Status</th>
        <th>Criado em</th>
        <th>Ações</th>
    </tr>

    <?php if (count($clientes) === 0): ?>
        <tr>
            <td colspan="5">Nenhum cliente cadastrado.</td>
        </tr>
    <?php else: ?>
        <?php foreach ($clientes as $c): ?>
        <tr>
            <td><?= htmlspecialchars($c['id']) ?></td>
            <td><?= htmlspecialchars($c['nome']) ?></td>
            <td><?= htmlspecialchars(($c['cpf_cnpj'] ?? '')) ?></td>
            <td><?= htmlspecialchars($c['endereco'] ?? '') ?></td>
            <td><?= htmlspecialchars($c['email']) ?></td>
            <td><?= htmlspecialchars($c['telefone']) ?></td>
            <td><?= htmlspecialchars($c['observacoes'] ?? '') ?></td>
            <td><?= htmlspecialchars($c['status']) ?></td>
            <td><?= htmlspecialchars($c['criado_em']) ?></td>
            
            <td>
                <a href="cliente_editar.php?id=<?= $c['id'] ?>">✏️ Editar</a>
                <a href="cliente_excluir.php?id=<?= $c['id'] ?>"
   onclick="return confirm('Deseja excluir este cliente?')">
   🗑 Excluir
</a>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</table>


<br>
<a href="dashboard.php">⬅ Voltar ao Dashboard</a>

</body>
</html>
