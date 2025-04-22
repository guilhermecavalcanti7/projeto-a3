<?php
// Configurações do banco de dados
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'eleicao2024';

// Conectando ao banco de dados
$conn = new mysqli($host, $user, $pass, $db);

// Verificando a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Recebe os dados enviados pelo JavaScript
$id_candidato = isset($_POST['id_candidato']) ? $_POST['id_candidato'] : ''; 
$cargo_votado = 'representante de turma'; // Cargo fixo

// Lista de candidatos válidos (todos agora são representantes de turma)
$candidatos_validos = ['30', '26', '69666', '26132'];

// Verifica o tipo de voto
$tipo_voto = 'candidato'; // Assume voto válido

if (strtolower($id_candidato) === 'branco') {
    $tipo_voto = 'branco';
    $id_candidato = null;
} elseif (!in_array($id_candidato, $candidatos_validos)) {
    $tipo_voto = 'nulo';
    $id_candidato = null;
}

// Insere o voto na tabela 'votos'
$sql = "INSERT INTO votos (cargo, candidato_id, tipo_voto) 
        VALUES ('$cargo_votado', " . ($id_candidato ? "'$id_candidato'" : "NULL") . ", '$tipo_voto')";

if ($conn->query($sql) === TRUE) {
    echo "Voto registrado com sucesso!";
} else {
    echo "Erro ao registrar voto: " . $conn->error;
}

$conn->close();
?>
