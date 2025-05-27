<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();
$consulta ="select * from medicao order by id_medicao desc limit 1";
$resultado = $db->query($consulta);
$dados = $resultado->fetchAll(PDO::FETCH_ASSOC);

$period = isset($_GET['period']) ? $_GET['period'] : '24h';
$sensor = isset($_GET['sensor']) ? $_GET['sensor'] : 'all';
$latest = isset($_GET['latest']) ? $_GET['latest'] : false;

function getTimeRange($period) {
    $end = new DateTime();
    $start = new DateTime();
    
    switch($period) {
        case '24h':
            $start->modify('-24 hours');
            break;
        case 'week':
            $start->modify('-7 days');
            break;
        case 'month':
            $start->modify('-30 days');
            break;
        default:
            $start->modify('-24 hours');
    }
    
    return array(
        'start' => $start->format('Y-m-d H:i:s'),
        'end' => $end->format('Y-m-d H:i:s')
    );
}

function getLatestData($db) {
    $query = "SELECT temperatura, umidade, uv, luminosidade, velocidade_vento, direcao_vento, precipitacao, data_hora 
              FROM dados_meteorologicos 
              ORDER BY data_hora DESC 
              LIMIT 1";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getHistoricalData($db, $period, $sensor) {
    $timeRange = getTimeRange($period);
    
    $query = "SELECT temperatura, umidade, uv, luminosidade, velocidade_vento, direcao_vento, precipitacao, data_hora 
              FROM dados_meteorologicos 
              WHERE data_hora BETWEEN :start AND :end 
              ORDER BY data_hora ASC";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':start', $timeRange['start']);
    $stmt->bindParam(':end', $timeRange['end']);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($latest) {
        $data = getLatestData($db);
        echo json_encode($data);
    } else {
        $data = getHistoricalData($db, $period, $sensor);
        echo json_encode($data);
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Método não permitido"));
}
?> 