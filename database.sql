-- Criação do banco de dados
CREATE DATABASE IF NOT EXISTS estmet;
USE estmet;

-- Criação da tabela de dados meteorológicos
CREATE TABLE IF NOT EXISTS dados_meteorologicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    temperatura DECIMAL(5,2),
    umidade DECIMAL(5,2),
    uv DECIMAL(5,2),
    luminosidade DECIMAL(10,2),
    velocidade_vento DECIMAL(5,2),
    direcao_vento VARCHAR(3),
    precipitacao DECIMAL(5,2),
    data_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_data_hora (data_hora)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Inserção de dados de exemplo
INSERT INTO dados_meteorologicos (temperatura, umidade, data_hora) VALUES
(25.5, 65.0, NOW()),
(26.0, 63.5, DATE_SUB(NOW(), INTERVAL 1 HOUR)),
(24.8, 68.2, DATE_SUB(NOW(), INTERVAL 2 HOUR)); 