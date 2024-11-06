<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// URL da API
$api_url = 'http://localhost/api-estacionamento/api/movimentacao/index.php'; // Ajuste para o seu domínio

if (!function_exists('getMovimentacoes')) {
    function getMovimentacoes($url)
    {
        $response = file_get_contents($url);
        if ($response === FALSE) {
            die('Erro ao acessar a API');
        }
        $movimentacoes = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            die('Erro ao decodificar a resposta JSON: ' . json_last_error_msg());
        }
        return $movimentacoes;
    }
}

// Obtendo as movimentações da API
$movimentacoes = getMovimentacoes($api_url);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Star Parking</title>
    <link rel="stylesheet" href="sidebar_style.css">
</head>

<body>

    <div class="container">
        <div class="sidebar">
            <h2>Star Parking</h2>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="gerenciar_vagas.php">Gerenciar Vagas</a></li>
                <li><a href="adicionar_pessoa.php">Adicionar Pessoa</a></li>
                <li><a href="gerenciar_pessoa.php">Gerenciar Pessoas</a></li>
                <li><a href="adicionar_cartao.php">Adicionar Cartão</a></li>
                <li><a href="gerenciar_cartao.php">Gerenciar Cartões</a></li>
                <li><a href="logout.php">Sair</a></li>
            </ul>
        </div>

        <div class="content">
            <h1>Dashboard</h1>
            <h2>Movimentações Recentes</h2>
            <table>
                <tr>
                    <th>Hora de Entrada</th>
                    <th>Hora de Saída</th>
                    <th>Nome do Cartão</th>
                    <th>Nome da Vaga</th> <!-- Alteração para "Nome da Vaga" -->
                </tr>
                <?php if (!empty($movimentacoes)): ?>
                    <?php foreach ($movimentacoes as $movimentacao): ?>
                        <tr>
                            <td><?php echo !empty($movimentacao['Hora_Entrada']) ? htmlspecialchars($movimentacao['Hora_Entrada']) : 'N/A'; ?></td>
                            <td><?php echo !empty($movimentacao['Hora_Saida']) ? htmlspecialchars($movimentacao['Hora_Saida']) : 'N/A'; ?></td>
                            <td><?php echo !empty($movimentacao['Nome_Cartao']) ? htmlspecialchars($movimentacao['Nome_Cartao']) : 'N/A'; ?></td>
                            <td><?php echo !empty($movimentacao['Nome_Vaga']) ? htmlspecialchars($movimentacao['Nome_Vaga']) : 'N/A'; ?></td> <!-- Alteração aqui para "Nome_Vaga" -->
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">Nenhuma movimentação encontrada.</td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>

    <script>
        function atualizarMovimentacoes() {
            fetch('<?php echo $api_url; ?>')
                .then(response => response.json())
                .then(data => {
                    const tabela = document.querySelector('table');
                    // Limpa as linhas existentes, exceto o cabeçalho
                    tabela.innerHTML = `
                    <tr>
                        <th>Hora de Entrada</th>
                        <th>Hora de Saída</th>
                        <th>Nome do Cartão</th>
                        <th>Nome da Vaga</th> <!-- Alteração para "Nome da Vaga" -->
                    </tr>
                `;

                    if (data.length > 0) {
                        data.forEach(movimentacao => {
                            const row = tabela.insertRow();
                            row.insertCell(0).textContent = movimentacao.Hora_Entrada || 'N/A';
                            row.insertCell(1).textContent = movimentacao.Hora_Saida || 'N/A';
                            row.insertCell(2).textContent = movimentacao.Nome_Cartao || 'N/A';
                            row.insertCell(3).textContent = movimentacao.Nome_Vaga || 'N/A'; // Alteração para "Nome_Vaga"
                        });
                    } else {
                        const row = tabela.insertRow();
                        row.insertCell(0).colSpan = 4;
                        row.cells[0].textContent = 'Nenhuma movimentação encontrada.';
                    }
                })
                .catch(error => console.error('Erro ao acessar a API:', error));
        }

        // Atualiza as movimentações a cada 5 segundos
        setInterval(atualizarMovimentacoes, 1000);
    </script>

</body>

</html>