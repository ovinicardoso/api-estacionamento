<?php
include('controle_estacionamento.php'); // Inclui o arquivo de conexão ao banco de dados

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Manipulação da ocupação da vaga e atualização do nome
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $vaga_id = $_POST['vaga_id'];

    // Atualizar nome da vaga se um novo nome for enviado
    if (isset($_POST['novo_nome'])) {
        $novo_nome = $_POST['novo_nome'];
        $update_nome_sql = "UPDATE Vaga SET Nome_Vaga = ? WHERE ID_Vaga = ?";
        $update_nome_stmt = $conn->prepare($update_nome_sql);
        $update_nome_stmt->bind_param("si", $novo_nome, $vaga_id);
        $update_nome_stmt->execute();
    } else {
        // Alterna entre ocupar e desocupar a vaga
        $ocupado = $_POST['ocupado'] == 1 ? 0 : 1;
        $update_vaga_sql = "UPDATE Vaga SET Ocupado = ? WHERE ID_Vaga = ?";
        $update_vaga_stmt = $conn->prepare($update_vaga_sql);
        $update_vaga_stmt->bind_param("ii", $ocupado, $vaga_id);
        $update_vaga_stmt->execute();
    }
}

// Consulta para obter o status das três vagas
$vagas_sql = "SELECT ID_Vaga, Nome_Vaga, Ocupado FROM Vaga WHERE ID_Vaga IN (1, 2, 3)";
$vagas_result = $conn->query($vagas_sql);
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
                        <form method="POST">
                            <h3><?php echo $vaga['Nome_Vaga']; ?></h3>
                            <input type="text" name="novo_nome" value="<?php echo htmlspecialchars($vaga['Nome_Vaga']); ?>">
                            <input type="hidden" name="vaga_id" value="<?php echo $vaga['ID_Vaga']; ?>">
                            <button type="submit">Salvar Nome</button>
                        </form>
                        <p>Status: <?php echo $vaga['Ocupado'] == 1 ? 'Ocupada' : 'Livre'; ?></p>
                        <form method="POST">
                            <input type="hidden" name="vaga_id" value="<?php echo $vaga['ID_Vaga']; ?>">
                            <input type="hidden" name="ocupado" value="<?php echo $vaga['Ocupado']; ?>">
                            <button type="submit"><?php echo $vaga['Ocupado'] == 1 ? 'Desocupar' : 'Ocupar'; ?></button>
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