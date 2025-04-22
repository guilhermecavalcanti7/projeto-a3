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

// Todos os candidatos são representantes de turma
$candidatos = [
    '30' => [
        'nome' => 'Arthur Anael',
        'partido' => 'Educação Fisica - 30',
        'foto' => 'imagens/arthur.jpg',
        'cargo' => 'representante de turma'
    ],
    '26' => [
        'nome' => 'Silvio Santos',
        'partido' => 'Tecnologia - 26',
        'foto' => 'imagens/silvio.png',
        'cargo' => 'representante de turma'
    ],
    '69666' => [
        'nome' => 'Guilherme',
        'partido' => 'Direito',
        'foto' => 'imagens/guilherme.jpg',
        'cargo' => 'representante de turma'
    ],
    '26132' => [
        'nome' => 'Maria Joaquina',
        'partido' => 'Saúde',
        'foto' => 'imagens/maria.png',
        'cargo' => 'representante de turma'
    ]
];

$cargo = 'representante de turma';
$totalVotos = 0;
$resultados = [];

// Consulta os votos válidos
$sql = "SELECT candidato_id, COUNT(*) AS quantidade FROM votos 
        WHERE tipo_voto = 'candidato' AND cargo = '$cargo' 
        GROUP BY candidato_id";
$result = $conn->query($sql);

// Processa os votos
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id = $row['candidato_id'];
        $votos = (int)$row['quantidade'];
        $totalVotos += $votos;

        if (isset($candidatos[$id])) {
            $c = $candidatos[$id];
            $resultados[] = [
                'id' => $id,
                'nome' => $c['nome'],
                'partido' => $c['partido'],
                'foto' => $c['foto'],
                'cargo' => $cargo,
                'votos' => $votos
            ];
        }
    }
}

// Ordena por votos
usort($resultados, function ($a, $b) {
    return $b['votos'] - $a['votos'];
});

// Adiciona percentuais e marca o eleito
foreach ($resultados as $index => &$candidato) {
    $candidato['percentual'] = $totalVotos > 0 ? round(($candidato['votos'] / $totalVotos) * 100, 2) : 0;
    $candidato['eleito'] = ($index === 0);
}

// Retorna o mesmo array para as duas seções do frontend
// (isso evita erro se ele ainda espera "prefeito" e "vereador")
echo json_encode([
    'prefeito' => $resultados,
    'vereador' => $resultados
]);

$conn->close();
?>
