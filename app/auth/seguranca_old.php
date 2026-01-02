<?php
// Garante sessão ativa
if (!isset($_SESSION)) {
    session_start();
}

// Verifica login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../public/login.php");
    exit;
}

// Função para restringir por nível
function exigeAdmin() {
    if (!isset($_SESSION['usuario_nivel']) || $_SESSION['usuario_nivel'] !== 'admin') {
        header("Location: ../public/index.php");
        exit;
    }

    if ($_SESSION['usuario_nivel'] !== 'admin') {
    die("Acesso restrito ao administrador");
}

}
