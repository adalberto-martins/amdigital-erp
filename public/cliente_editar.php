<?php
require __DIR__ . "/../app/auth/seguranca.php";
require __DIR__ . "/../config/database.php";

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: clientes.php");
    exit;
}

/* busca cliente */
$stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = ?");
$stmt->execute([$id]);
$c = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$c) {
    header("Location: clientes.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Editar Cliente</title>

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
    margin-bottom: 10px;
}

/* botões */
.botoes {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.btn {
    padding: 10px 18px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
    transition: .2s;
    border: none;
    cursor: pointer;
}

.btn-primary {
    background: #2563eb;
    color: #fff;
}

.btn-primary:hover {
    background: #1e40af;
}

.btn-secondary {
    background: #e5e7eb;
    color: #374151;
}

.btn-secondary:hover {
    background: #d1d5db;
}

.btn-danger {
    background: #dc2626;
    color: #fff;
}

.btn-danger:hover {
    background: #b91c1c;
}

/* formulário */
.form-wrapper {
    display: flex;
    justify-content: center;
}

form {
    width: 100%;
    max-width: 900px;
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 4px 10px rgba(0,0,0,.08);
}

form label {
    font-weight: bold;
    display: block;
    margin-top: 10px;
}

form input,
form textarea,
form select {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border-radius: 8px;
    border: 1px solid #cbd5f5;
}

form textarea {
    min-height: 100px;
}

/* info */
.info-box {
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 15px;
    font-size: 14px;
}

input.invalido {
    border: 2px solid #dc2626;
    background: #fee2e2;
}

</style>
</head>

<body>
<div class="container">

<h1>Editar Cliente #<?= $c['id'] ?></h1>

<div class="botoes">
    <a href="clientes.php" class="btn btn-secondary">⬅ Voltar</a>
</div>

<div class="form-wrapper">
<form method="post" action="cliente_atualizar.php">

    <input type="hidden" name="id" value="<?= $c['id'] ?>">

    <div class="info-box">
        <strong>ID:</strong> <?= $c['id'] ?><br>
        <strong>Criado em:</strong>
        <?= date('d/m/Y H:i', strtotime($c['criado_em'])) ?>
    </div>

    <label>Nome</label>
    <input type="text" name="nome"
           value="<?= htmlspecialchars($c['nome']) ?>" required>

    <label>CPF / CNPJ</label>
    <input type="text" name="cpf_cnpj"
           value="<?= htmlspecialchars($c['cpf_cnpj'] ?? '') ?>">

    <label>Email</label>
    <input type="email" name="email"
           value="<?= htmlspecialchars($c['email'] ?? '') ?>">

    <label>Telefone</label>
    <input type="text" name="telefone"
           value="<?= htmlspecialchars($c['telefone'] ?? '') ?>">

    <label>Endereço</label>
    <input type="text" name="endereco"
           value="<?= htmlspecialchars($c['endereco'] ?? '') ?>">

    <label>Observações</label>
    <textarea name="observacoes"><?= htmlspecialchars($c['observacoes'] ?? '') ?></textarea>

    <label>Status</label>
    <select name="status">
        <option value="ativo" <?= $c['status'] === 'ativo' ? 'selected' : '' ?>>
            Ativo
        </option>
        <option value="inativo" <?= $c['status'] === 'inativo' ? 'selected' : '' ?>>
            Inativo
        </option>
    </select>

    <div class="botoes" style="margin-top:20px;">
        <button type="submit" class="btn btn-primary">
            Salvar Alterações
        </button>

        <a href="cliente_excluir.php?id=<?= $c['id'] ?>"
           class="btn btn-danger"
           onclick="return confirm('Excluir este cliente?')">
           Excluir
        </a>
    </div>

</form>
</div>

</div>
<script>
/* ===== CPF / CNPJ ===== */
function maskCpfCnpj(value) {
    value = value.replace(/\D/g, '');

    if (value.length <= 11) {
        // CPF
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    } else {
        // CNPJ
        value = value.replace(/^(\d{2})(\d)/, '$1.$2');
        value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
        value = value.replace(/\.(\d{3})(\d)/, '.$1/$2');
        value = value.replace(/(\d{4})(\d)/, '$1-$2');
    }
    return value;
}

/* ===== VALIDA CPF ===== */
function validarCPF(cpf) {
    cpf = cpf.replace(/\D/g, '');

    if (cpf.length !== 11 || /^(\d)\1+$/.test(cpf)) return false;

    let soma = 0;
    for (let i = 0; i < 9; i++) {
        soma += parseInt(cpf.charAt(i)) * (10 - i);
    }
    let resto = (soma * 10) % 11;
    if (resto === 10 || resto === 11) resto = 0;
    if (resto !== parseInt(cpf.charAt(9))) return false;

    soma = 0;
    for (let i = 0; i < 10; i++) {
        soma += parseInt(cpf.charAt(i)) * (11 - i);
    }
    resto = (soma * 10) % 11;
    if (resto === 10 || resto === 11) resto = 0;

    return resto === parseInt(cpf.charAt(10));
}

/* ===== VALIDA CNPJ ===== */
function validarCNPJ(cnpj) {
    cnpj = cnpj.replace(/\D/g, '');

    if (cnpj.length !== 14 || /^(\d)\1+$/.test(cnpj)) return false;

    let tamanho = cnpj.length - 2;
    let numeros = cnpj.substring(0, tamanho);
    let digitos = cnpj.substring(tamanho);
    let soma = 0;
    let pos = tamanho - 7;

    for (let i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--;
        if (pos < 2) pos = 9;
    }

    let resultado = soma % 11 < 2 ? 0 : 11 - (soma % 11);
    if (resultado !== parseInt(digitos.charAt(0))) return false;

    tamanho++;
    numeros = cnpj.substring(0, tamanho);
    soma = 0;
    pos = tamanho - 7;

    for (let i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--;
        if (pos < 2) pos = 9;
    }

    resultado = soma % 11 < 2 ? 0 : 11 - (soma % 11);
    return resultado === parseInt(digitos.charAt(1));
}

/* ===== TELEFONE ===== */
function maskTelefone(value) {
    value = value.replace(/\D/g, '');
    value = value.replace(/^(\d{2})(\d)/g, '($1) $2');

    if (value.length > 14) {
        value = value.replace(/(\d{5})(\d)/, '$1-$2');
    } else {
        value = value.replace(/(\d{4})(\d)/, '$1-$2');
    }
    return value;
}

if (!valido) {
    cpfCnpj.classList.add('invalido');
} else {
    cpfCnpj.classList.remove('invalido');
}


/* ===== EVENTOS ===== */
document.addEventListener('DOMContentLoaded', () => {

    const cpfCnpj = document.querySelector('input[name="cpf_cnpj"]');
    const telefone = document.querySelector('input[name="telefone"]');

    if (cpfCnpj) {
        cpfCnpj.addEventListener('input', e => {
            e.target.value = maskCpfCnpj(e.target.value);
        });
    }

    if (telefone) {
        telefone.addEventListener('input', e => {
            e.target.value = maskTelefone(e.target.value);
        });
    }

});
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {

    const cpfCnpj = document.querySelector('input[name="cpf_cnpj"]');
    const form = cpfCnpj?.closest('form');

    if (!cpfCnpj || !form) return;

    function validarCampo() {
        const valor = cpfCnpj.value.replace(/\D/g, '');

        if (valor === '') return true; // campo opcional

        let valido = false;
        if (valor.length === 11) valido = validarCPF(valor);
        else if (valor.length === 14) valido = validarCNPJ(valor);

        if (!valido) {
            alert('CPF ou CNPJ inválido.');
            cpfCnpj.focus();
        }

        return valido;
    }

    // valida ao sair do campo
    cpfCnpj.addEventListener('blur', validarCampo);

    // valida ao enviar
    form.addEventListener('submit', e => {
        if (!validarCampo()) {
            e.preventDefault();
        }
    });

});
</script>

</body>
</html>

