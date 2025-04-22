<?php
require 'vendor/autoload.php'; // Carrega o autoloader do Composer

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Configurações do banco de dados
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'eleicao2024';

try {
    // Conexão com o banco de dados
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Definindo os candidatos (cargo unificado)
    $candidatos = [
        '30' => [
            'nome' => 'Arthur Anael',
            'partido' => 'Educação Fisica - 30',
            'cargo' => 'representante de turma'
        ],
        '26' => [
            'nome' => 'Silvio Santos',
            'partido' => 'Tecnologia - 26',
            'cargo' => 'representante de turma'
        ],
        '69666' => [
            'nome' => 'Guilherme',
            'partido' => 'Direito',
            'cargo' => 'representante de turma'
        ],
        '26132' => [
            'nome' => 'Maria Joaquina',
            'partido' => 'Saúde',
            'cargo' => 'representante de turma'
        ]
    ];

    $resultados = [];
    $totalVotos = 0;

    // Consulta para votos válidos
    $sql = "SELECT candidato_id, COUNT(*) AS quantidade FROM votos 
            WHERE tipo_voto = 'candidato' AND cargo = 'representante de turma' 
            GROUP BY candidato_id";
    $stmt = $pdo->query($sql);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['candidato_id'];
        $quantidade = (int)$row['quantidade'];
        $totalVotos += $quantidade;

        if (isset($candidatos[$id])) {
            $c = $candidatos[$id];
            $resultados[] = [
                'nome' => $c['nome'],
                'partido' => $c['partido'],
                'cargo' => $c['cargo'],
                'votos' => $quantidade
            ];
        }
    }

    // Ordenar por votos e marcar eleito
    usort($resultados, fn($a, $b) => $b['votos'] - $a['votos']);

    foreach ($resultados as $i => &$candidato) {
        $candidato['percentual'] = $totalVotos > 0 ? round(($candidato['votos'] / $totalVotos) * 100, 2) : 0;
        $candidato['status'] = ($i === 0) ? 'Eleito' : 'Não Eleito';
    }

} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}

// Cria a planilha
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Resultado da Eleição');

// Cabeçalhos
$sheet->setCellValue('A1', 'Cargo');
$sheet->setCellValue('B1', 'Nome');
$sheet->setCellValue('C1', 'Partido');
$sheet->setCellValue('D1', 'Percentual de Votos');
$sheet->setCellValue('E1', 'Quantidade de Votos');
$sheet->setCellValue('F1', 'Status');

// Preencher os dados
$row = 2;
foreach ($resultados as $candidato) {
    $sheet->setCellValue("A{$row}", ucfirst($candidato['cargo']));
    $sheet->setCellValue("B{$row}", $candidato['nome']);
    $sheet->setCellValue("C{$row}", $candidato['partido']);
    $sheet->setCellValue("D{$row}", "{$candidato['percentual']}%");
    $sheet->setCellValue("E{$row}", $candidato['votos']);
    $sheet->setCellValue("F{$row}", $candidato['status']);
    $row++;
}

// Cabeçalhos para download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="relatorio_eleicao.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
