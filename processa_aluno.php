<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "auditorio";

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Receber dados
$ra = $_POST['ra'];
$nome = $_POST['nome'];

// Inserir no banco
$sql = "INSERT INTO alunos (ra, nome) VALUES ('$ra', '$nome')";

if ($conn->query($sql) === true) {
    echo "Presença confirmada com sucesso!";
} else {
    echo "Erro: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
