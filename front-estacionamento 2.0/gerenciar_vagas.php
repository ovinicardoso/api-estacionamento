<?php
include('controle_estacionamento.php'); // Inclui o arquivo de conexão ao banco de dados

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Manipulação da ocupação da vaga
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $vaga_id = $_POST['vaga_id'];

    if (isset($_POST['id_cartao']) && !empty($_POST['id_cartao'])) {
        $id_cartao = $_POST['id_cartao'];

        // Verifica se o cartão existe
        $cartao_check_sql = "SELECT ID_Cartao FROM Cartao WHERE ID_Cartao = ?";
        $cartao_check_stmt = $conn->prepare($cartao_check_sql);
        $cartao_check_stmt->bind_param("i", $id_cartao);
        $cartao_check_stmt->execute();
        $cartao_check_result = $cartao_check_stmt->get_result();

        if ($cartao_check_result->num_rows > 0) {
            // Ocupa a vaga
            $update_vaga_sql = "UPDATE Vaga SET Ocupado = 1 WHERE ID_Vaga = ?";
            $update_vaga_stmt = $conn->prepare($update_vaga_sql);
            $update_vaga_stmt->bind_param("i", $vaga_id);
            $update_vaga_stmt->execute();

            // Registra a movimentação com hora de entrada
            $log_sql = "INSERT INTO Movimentacao (Hora_Entrada, ID_Cartao, ID_Vaga) VALUES (NOW(), ?, ?)";
            $log_stmt = $conn->prepare($log_sql);
            $log_stmt->bind_param("ii", $id_cartao, $vaga_id);
            $log_stmt->execute();
        } else {
            echo "O cartão selecionado não existe.";
        }
    } else {
        // Desocupa a vaga
        $update_vaga_sql = "UPDATE Vaga SET Ocupado = 0 WHERE ID_Vaga = ?";
        $update_vaga_stmt = $conn->prepare($update_vaga_sql);
        $update_vaga_stmt->bind_param("i", $vaga_id);
        $update_vaga_stmt->execute();

        // Atualiza a movimentação com hora de saída
        $log_saida_sql = "UPDATE Movimentacao SET Hora_Saida = NOW() WHERE ID_Vaga = ? AND Hora_Saida IS NULL";
        $log_saida_stmt = $conn->prepare($log_saida_sql);
        $log_saida_stmt->bind_param("i", $vaga_id);
        $log_saida_stmt->execute();
    }
}

// Consulta para obter o status das vagas e cartões associados
$vagas_sql = "SELECT v.ID_Vaga, v.Ocupado, c.Nome_Cartao
              FROM Vaga v
              LEFT JOIN Movimentacao m ON v.ID_Vaga = m.ID_Vaga AND m.Hora_Saida IS NULL
              LEFT JOIN Cartao c ON m.ID_Cartao = c.ID_Cartao
              WHERE v.ID_Vaga IN (1, 2, 3)";
$vagas_result = $conn->query($vagas_sql);

// Consulta para obter todos os cartões disponíveis que não estão ocupando uma vaga
$cartoes_sql = "
    SELECT ID_Cartao, Nome_Cartao 
    FROM Cartao 
    WHERE ID_Cartao NOT IN (
        SELECT ID_Cartao 
        FROM Movimentacao 
        WHERE Hora_Saida IS NULL
    )";
$cartoes_result = $conn->query($cartoes_sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Vagas - Star Parking</title>
    <link rel="stylesheet" href="sidebar_style.css">
    <style>
        /* Estilos adicionais para a página de gerenciamento de vagas */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        .vaga-container {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
        }
        .vaga {
            border: 2px solid #ccc;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            width: 250px;
            transition: background-color 0.3s;
        }
        .ocupada {
            background-color: #f8d7da;
            border-color: #dc3545;
        }
        .livre {
            background-color: #d4edda;
            border-color: #28a745;
        }
        button {
            margin-top: 10px;
            padding: 5px 10px;
            cursor: pointer;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 3px;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
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
        <h1>Gerenciar Vagas</h1>
        <div class="vaga-container">
            <?php while ($vaga = $vagas_result->fetch_assoc()): ?>
                <div class="vaga <?php echo $vaga['Ocupado'] == 1 ? 'ocupada' : 'livre'; ?>">
                    <h3>Vaga <?php echo $vaga['ID_Vaga']; ?></h3>
                    <p>Status: <?php echo $vaga['Ocupado'] == 1 ? 'Ocupada' : 'Livre'; ?></p>
                    <?php if ($vaga['Ocupado'] == 1): ?>
                        <p>Cartão: <?php echo $vaga['Nome_Cartao']; ?></p>
                    <?php endif; ?>
                    <form method="POST">
                        <input type="hidden" name="vaga_id" value="<?php echo $vaga['ID_Vaga']; ?>">
                        <?php if ($vaga['Ocupado'] == 0): ?>
                            <label for="id_cartao">Selecionar Cartão:</label>
                            <select name="id_cartao" required>
                                <option value="">Selecione um cartão</option>
                                <?php
                                $cartoes_result->data_seek(0);
                                while ($cartao = $cartoes_result->fetch_assoc()): ?>
                                    <option value="<?php echo $cartao['ID_Cartao']; ?>"><?php echo $cartao['Nome_Cartao']; ?></option>
                                <?php endwhile; ?>
                            </select>
                            <button type="submit">Ocupar</button>
                        <?php else: ?>
                            <button type="submit">Desocupar</button>
                        <?php endif; ?>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

</body>
</html>

<?php
$conn->close();
?>
