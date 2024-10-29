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

// Receber dados do formulário
$professor = $_POST['professor'];
$disciplina = $_POST['disciplina'];

// Inserir no banco
$sql = "INSERT INTO palestrantes (professor, disciplina) VALUES ('$professor', '$disciplina')";

if ($conn->query($sql) === true) {
    echo "Palestra confirmada com sucesso!";
} else {
    echo "Erro: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
