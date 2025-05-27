<?php
include_once 'config/database.php';

$consulta ="select * from medicao order by id_medicao desc limit 1";
$resultado = mysqli_query($conn, $consulta);
$dados = mysqli_fetch_array($resultado, MYSQLI_BOTH);


$dataOriginal = $dados['data_leitura'];
$data = new DateTime($dataOriginal);
$dataFormatada = $data->format('d/m/Y H:i:s');

//print_r($dados);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estação Meteorológica IFPR - Ivaiporã</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
</head>
<body class="light-theme">
    <div class="container mt-4">
        <div class="header-container">
            <div class="text-center mb-4">
                <h1 class="main-title">Estação Meteorológica IFPR - Ivaiporã</h1>
                <p class="last-update">Última atualização: <span id="lastUpdate"><?php echo $dataFormatada; ?></span></p>
            </div>
            <button id="themeToggle" class="btn btn-outline-primary theme-toggle">
                <i class="fas fa-moon"></i>
            </button>
        </div>

        <div id="dashboard" class="row g-4">
            <!-- Card Status -->
            <div class="col-md-4 col-lg-3">
                <div class="card status-card">
                    <div class="card-body text-center">
                        <h5 class="card-title">
                            <i class="fas fa-cloud-sun me-2"></i>
                            Status Atual
                        </h5>
                        <div class="status-icon">
                            <i class="fas fa-cloud-sun"></i>
                        </div>
                        <p id="currentStatus" class="mt-2">Ensolarado</p>
                    </div>
                </div>
            </div>

            <!-- Card Temperatura -->
            <div class="col-md-4 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <h5 class="card-title">
                                <i class="fas fa-temperature-high me-2"></i>
                                Temperatura
                            </h5>
                            <div class="card-controls">
                                <button class="btn btn-sm btn-link move-card"><i class="fas fa-arrows-alt"></i></button>
                                <button class="btn btn-sm btn-link lock-card"><i class="fas fa-lock-open"></i></button>
                            </div>
                        </div>
                        <div class="value-container text-center">
                            <span id="value-temperatura" class="big-value"><?php echo "$dados[temp]"; ?></span>
                            <span class="unit">°C</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Umidade -->
            <div class="col-md-4 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <h5 class="card-title">
                                <i class="fas fa-tint me-2"></i>
                                Umidade
                            </h5>
                            <div class="card-controls">
                                <button class="btn btn-sm btn-link move-card"><i class="fas fa-arrows-alt"></i></button>
                                <button class="btn btn-sm btn-link lock-card"><i class="fas fa-lock-open"></i></button>
                            </div>
                        </div>
                        <div class="value-container text-center">
                            <span id="value-umidade" class="big-value"><?php echo "$dados[umid]"; ?></span>
                            <span class="unit">%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card UV -->
            <div class="col-md-4 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <h5 class="card-title">
                                <i class="fas fa-sun me-2"></i>
                                Índice UV
                            </h5>
                            <div class="card-controls">
                                <button class="btn btn-sm btn-link move-card"><i class="fas fa-arrows-alt"></i></button>
                                <button class="btn btn-sm btn-link lock-card"><i class="fas fa-lock-open"></i></button>
                            </div>
                        </div>
                        <div class="value-container text-center">
                            <span id="value-uv" class="big-value"><?php echo "$dados[raduv]"; ?></span>
                            <span class="unit">mW/cm²</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Luminosidade -->
            <div class="col-md-4 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <h5 class="card-title">
                                <i class="fas fa-lightbulb me-2"></i>
                                Luminosidade
                            </h5>
                            <div class="card-controls">
                                <button class="btn btn-sm btn-link move-card"><i class="fas fa-arrows-alt"></i></button>
                                <button class="btn btn-sm btn-link lock-card"><i class="fas fa-lock-open"></i></button>
                            </div>
                        </div>
                        <div class="value-container text-center">
                            <span id="value-luminosidade" class="big-value"><?php echo "$dados[ilumi]"; ?></span>
                            <span class="unit">lux</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Vento -->
            <div class="col-md-4 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <h5 class="card-title">
                                <i class="fas fa-wind me-2"></i>
                                Velocidade do Vento
                            </h5>
                            <div class="card-controls">
                                <button class="btn btn-sm btn-link move-card"><i class="fas fa-arrows-alt"></i></button>
                                <button class="btn btn-sm btn-link lock-card"><i class="fas fa-lock-open"></i></button>
                            </div>
                        </div>
                        <div class="value-container text-center">
                            <span id="value-vento" class="big-value"><?php echo "$dados[velovent]"; ?></span>
                            <span class="unit">km/h</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Direção do Vento -->
            <div class="col-md-4 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <h5 class="card-title">
                                <i class="fas fa-compass me-2"></i>
                                Direção do Vento
                            </h5>
                            <div class="card-controls">
                                <button class="btn btn-sm btn-link move-card"><i class="fas fa-arrows-alt"></i></button>
                                <button class="btn btn-sm btn-link lock-card"><i class="fas fa-lock-open"></i></button>
                            </div>
                        </div>
                        <div class="wind-rose">
                            <div class="compass">
                                <div class="direction" id="windDirection"><?php echo "$dados[direvent]"; ?></div>
                                <div class="arrow" id="windArrow"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Precipitação -->
            <div class="col-md-4 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <h5 class="card-title">
                                <i class="fas fa-cloud-rain me-2"></i>
                                Precipitação
                            </h5>
                            <div class="card-controls">
                                <button class="btn btn-sm btn-link move-card"><i class="fas fa-arrows-alt"></i></button>
                                <button class="btn btn-sm btn-link lock-card"><i class="fas fa-lock-open"></i></button>
                            </div>
                        </div>
                        <div class="value-container text-center">
                            <span id="value-chuva" class="big-value"><?php echo "$dados[mmchuva]"; ?></span>
                            <span class="unit">mm</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico Principal -->
        <div class="card mt-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title">
                        <i class="fas fa-chart-line me-2"></i>
                        Histórico de Dados
                    </h5>
                    <div class="btn-group">
                        <button class="btn btn-outline-primary active" data-period="24h">24h</button>
                        <button class="btn btn-outline-primary" data-period="week">Semana</button>
                        <button class="btn btn-outline-primary" data-period="month">Mês</button>
                        <button class="btn btn-outline-primary" onclick="exportData()">
                            <i class="fas fa-download"></i> Exportar
                        </button>
                    </div>
                </div>
                <div id="mainChart" style="height: 400px;"></div>
            </div>
        </div>

        <footer class="text-center mt-5 mb-4">
            <p>Copyright © IFPR Campus Ivaiporã</p>
            <p class="small">Desenvolvido por Gabriel Lima, Henrique Golinelli, David Cunha e Eduardo Sanches</p>
        </footer>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html> 