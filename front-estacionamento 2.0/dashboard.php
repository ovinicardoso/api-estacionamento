<?php
include('controle_estacionamento.php');

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Limpa as movimentações se o botão for pressionado
if (isset($_POST['limpar_movimentacoes'])) {
    $conn->query("DELETE FROM movimentacao"); // Limpa todas as movimentações
}

// Consulta as movimentações, incluindo Nome do Cartão e Número da Vaga
$movimentacoes_sql = "
    SELECT 
        m.ID_Movimentacao, 
        m.Hora_Entrada, 
        m.Hora_Saida, 
        c.Nome_Cartao, 
        v.ID_Vaga 
    FROM 
        movimentacao m 
    JOIN 
        cartao c ON m.ID_Cartao = c.ID_Cartao 
    JOIN 
        vaga v ON m.ID_Vaga = v.ID_Vaga 
    ORDER BY 
        m.Hora_Entrada DESC";
$movimentacoes_result = $conn->query($movimentacoes_sql);
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
        <form method="POST">
            <button type="submit" name="limpar_movimentacoes" onclick="return confirm('Você tem certeza que deseja limpar todas as movimentações?')">Limpar Tela</button> <!-- Botão para limpar a tela -->
        </form>
        <table>
            <tr>
                <th>Hora de Entrada</th>
                <th>Hora de Saída</th>
                <th>Nome do Cartão</th>
                <th>Número da Vaga</th>
            </tr>
            <?php if ($movimentacoes_result->num_rows > 0): ?>
                <?php while($movimentacao = $movimentacoes_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $movimentacao['Hora_Entrada']; ?></td>
                        <td><?php echo $movimentacao['Hora_Saida']; ?></td>
                        <td><?php echo $movimentacao['Nome_Cartao']; ?></td>
                        <td><?php echo $movimentacao['ID_Vaga']; ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4">Nenhuma movimentação encontrada.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</div>

</body>
</html>

<?php
$conn->close();
?>
