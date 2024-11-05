<?php
// URL da API
$api_url = 'http://localhost/api-estacionamento/api/movimentacao/index.php'; // Ajuste para o seu domínio

// Função para fazer requisição GET para a API
function getMovimentacoes($url) {
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
                <th>Número da Vaga</th>
            </tr>
            <?php if (!empty($movimentacoes)): ?>
                <?php foreach ($movimentacoes as $movimentacao): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($movimentacao['Hora_Entrada']); ?></td>
                        <td><?php echo htmlspecialchars($movimentacao['Hora_Saida']); ?></td>
                        <td><?php echo htmlspecialchars($movimentacao['Nome_Cartao']); ?></td>
                        <td><?php echo htmlspecialchars($movimentacao['ID_Vaga']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4">Nenhuma movimentação encontrada.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</div>

</body>
</html>
