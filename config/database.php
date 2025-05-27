<?php
$servername = "localhost";
$username = "gabriel";
$password = "Fwwc8888";
$database = "estmet";

try {
    $conn = new mysqli($servername, $username, $password, $database);
    
    if ($conn->connect_error) {
        throw new Exception("Conexão falhou: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8");
} catch (Exception $e) {
    die("Erro na conexão: " . $e->getMessage());
}
?> 