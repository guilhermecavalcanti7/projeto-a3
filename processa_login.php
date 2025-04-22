<?php
session_start();

// Simulação de banco de dados de usuários
$usuarios = [
    "12345" => ["senha" => "senha123", "nome" => "João da Silva"],
    "67890" => ["senha" => "senha456", "nome" => "Maria Oliveira"]
];

// Recebe os dados do formulário
$ra = $_POST['ra'] ?? '';
$senha = $_POST['senha'] ?? '';

// Verifica se o usuário existe
if (isset($usuarios[$ra]) && $usuarios[$ra]['senha'] === $senha) {
    $_SESSION['usuario'] = $usuarios[$ra]['nome'];
    header("Location: selecionar_eleicao.html");

    exit;
} else {
    echo "<script>alert('R.A. ou senha inválidos!');window.location.href='login.html';</script>";
}
?>
