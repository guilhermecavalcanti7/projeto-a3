<?php
require_once 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Conectar ao banco de dados
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

// Buscar dados da tabela de alunos (por exemplo)
$sql = "SELECT ra, nome FROM alunos";
$result = $conn->query($sql);

// Verificar se existem registros
if ($result->num_rows > 0) {
    // Iniciar o HTML do PDF
    $html = '
    <!DOCTYPE html>
    <html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <style>
            body {
                font-family: Arial, sans-serif;
            }
            h1 {
                text-align: center;
                margin-bottom: 20px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }
            th, td {
                border: 1px solid #000;
                padding: 8px;
                text-align: center;
            }
            th {
                background-color: #f4f4f4;
            }
        </style>
    </head>
    <body>
        <h1>Lista de Presença</h1>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>RA</th>
                    <th>Nome</th>
                </tr>
            </thead>
            <tbody>
    ';

    // Adicionar registros ao HTML
    $count = 1;
    while ($row = $result->fetch_assoc()) {
        $html .= '
            <tr>
                <td>' . $count++ . '</td>
                <td>' . $row['ra'] . '</td>
                <td>' . $row['nome'] . '</td>
            </tr>
        ';
    }

    $html .= '
            </tbody>
        </table>
    </body>
    </html>
    ';

    // Criar o PDF
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Enviar o PDF para o navegador
    $dompdf->stream('Lista_de_Presenca.pdf', ['Attachment' => false]);
} else {
    echo "Nenhum registro encontrado.";
}

$conn->close();
?>
