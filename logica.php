<?php
$servername = "localhost";        // ou o IP do servidor MySQL
$username = "gabriel";
$password = "Fwwc8888";
$database = "estmet";

// Criar conexão
$conn = new mysqli($servername, $username, $password, $database);

// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
} else {
    echo "Conectado com sucesso!";
}

$id = $_GET['id'] ?? null;
$umidade = $_GET['umid'] ?? null;
$temperatura = $_GET['temp'] ?? null;

if ($id === null || $umidade === null || $temperatura === null) {
    die("Parâmetros ausentes.");
}

$relatorio = [
    'id' => $id,
    'umidade' => $umidade,
    'temperatura' => $temperatura,
    'data' => date("d/m/Y H:i:s")
];

// Caminho para o arquivo
$arquivo = __DIR__ . '/data/monitoramento.json';

// Cria o diretório se não existir
if (!file_exists(__DIR__ . '/data')) {
    mkdir(__DIR__ . '/data', 0777, true);
}

// Se o arquivo já existir, carrega os dados
if (file_exists($arquivo)) {
    $relatorios = json_decode(file_get_contents($arquivo), true);
    if (!is_array($relatorios)) {
        $relatorios = [];
    }
} else {
    $relatorios = [];
}

// Adiciona o novo relatório
$relatorios[] = $relatorio;

// Salva tudo novamente no JSON
file_put_contents($arquivo, json_encode($relatorios, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
$sql1 = "INSERT INTO medicao (
  temp, umid
) VALUES (
  '$temperatura', '$umidade'
);";

echo "$sql1";

if (mysqli_query($conn, $sql1)) {
    echo "Dados inseridos com sucesso!";
} else {
    echo "Erro: " . mysqli_error($conn);
}

// Fecha a conexão
mysqli_close($conn);

?>

