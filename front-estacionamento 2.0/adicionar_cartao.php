<?php
include('controle_estacionamento.php');

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

$message = ""; // Variável para armazenar a mensagem de feedback

// Adicionando um novo cartão
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_cartao = $_POST['nome_cartao']; // Nome do cartão
    $ns_cartao = $_POST['ns_cartao']; // Número do cartão

    // Insere o novo cartão com ID_Pessoa nulo
    $sql_cartao = $conn->prepare("INSERT INTO cartao (Nome_Cartao, NS_Cartao, ID_Pessoa) VALUES (?, ?, NULL)");
    $sql_cartao->bind_param("ss", $nome_cartao, $ns_cartao);

    if ($sql_cartao->execute()) {
        $message = "Cartão adicionado com sucesso!"; // Mensagem de sucesso
    } else {
        $message = "Erro ao adicionar cartão: " . $sql_cartao->error; // Mensagem de erro
    }

    $sql_cartao->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StarPark - Adicionar Cartão</title>
    <link rel="stylesheet" href="sidebar_style.css">
    <style>
        .content {
            margin-left: 280px;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            width: 100%;
            max-width: 500px;
            padding: 20px;
            background-color: #f4f4f4;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }

        input[type="text"], input[type="email"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        /* Estilo da caixa de mensagem */
        .message {
            padding: 10px;
            background-color: #e8f4e5; /* Fundo suave */
            border: 1px solid #c2e4d7; /* Borda suave */
            border-radius: 5px;
            margin-top: 20px;
            color: #2c662d; /* Texto em verde */
            font-weight: bold;
        }

        .error-message {
            background-color: #f8d7da; /* Fundo suave para erro */
            border: 1px solid #f5c6cb; /* Borda suave para erro */
            color: #721c24; /* Texto em vermelho */
        }
    </style>
</head>
<body>
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
        <div class="form-container">
            <h1>Adicionar Novo Cartão</h1>
            <form method="POST">
                <label>Nome do Cartão:</label>
                <input type="text" name="nome_cartao" required>
                
                <label>Número de Série do Cartão:</label>
                <input type="text" name="ns_cartao" required>
                
                <input type="submit" value="Adicionar">
            </form>

            <!-- Mensagem de feedback após a submissão -->
            <?php if ($message): ?>
                <div class="message <?php echo strpos($message, 'sucesso') !== false ? '' : 'error-message'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
