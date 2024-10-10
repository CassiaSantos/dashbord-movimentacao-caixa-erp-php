<?php
// Conectar ao banco de dados
include 'db.php'; // Inclua seu arquivo de conexão

// Verificar se a conexão foi bem-sucedida
if (!$conn) {
    die("Erro na conexão: " . mysqli_connect_error());
}

// Consultar os dados da tabela 'sis_caixa'
$sql = "SELECT data, entrada, saida FROM sis_caixa ORDER BY data ASC";
$result = $conn->query($sql);

// Arrays para armazenar os dados
$datas = [];
$entradas = [];
$saidas = [];

// Armazenar os dados nos arrays e ajusta as datas para o formato ISO 8601
while ($row = $result->fetch_assoc()) {
    $datas[] = date('c', strtotime($row['data'])); // Convertendo para ISO 8601
    $entradas[] = $row['entrada'] ?: 0; // Se for NULL, substitui por 0
    $saidas[] = $row['saida'] ?: 0;     // Se for NULL, substitui por 0
}

// Debug para ver os dados retornados
// echo '<pre>';
// print_r($datas);
// print_r($entradas);
// print_r($saidas);
// echo '</pre>';

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gráfico de Entradas e Saídas</title>
    <!-- Inclua a biblioteca Chart.js e o adaptador luxon -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/luxon@2.1.1/build/global/luxon.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-luxon@1.1.0"></script> 
</head>
<body>
    <h2>Gráfico de Entradas e Saídas do Caixa</h2>
    <canvas id="caixaChart"></canvas> <!-- Onde o gráfico será renderizado -->

    <script>
        // Recebendo os dados do PHP para o JavaScript
        const datas = <?php echo json_encode($datas); ?>;
        const entradas = <?php echo json_encode($entradas); ?>;
        const saidas = <?php echo json_encode($saidas); ?>;

        // Configuração do gráfico
        const ctx = document.getElementById('caixaChart').getContext('2d');
        const caixaChart = new Chart(ctx, {
            type: 'line', // Tipo de gráfico de linha
            data: {
                labels: datas, // Datas como rótulos do eixo X
                datasets: [{
                    label: 'Entradas (R$)',
                    data: entradas,
                    borderColor: 'green', // Cor da linha de entradas
                    fill: false
                }, {
                    label: 'Saídas (R$)',
                    data: saidas,
                    borderColor: 'red', // Cor da linha de saídas
                    fill: false
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        type: 'time',  // Garantir que o eixo X está sendo tratado como tempo
                        time: {
                            unit: 'day',
                            tooltipFormat: 'DD/MM/YYYY'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Valor em R$'
                        }
                    }
                }

            }
        });
    </script>
</body>
</html>