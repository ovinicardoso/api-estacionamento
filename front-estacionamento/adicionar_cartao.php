<?php
include('controle_estacionamento.php');

$message = ""; // Variável para armazenar a mensagem de feedback

// Adicionando um novo cartão
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_cartao = $_POST['Nome_Cartao']; // Nome do cartão

    // Configura os dados que serão enviados para a API
    $data = array(
        'Nome_Cartao' => $nome_cartao,
    );

    // Converte o array em JSON
    $json_data = json_encode($data);

    // Opções do contexto da requisição HTTP
    $options = array(
        'http' => array(
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'POST',
            'content' => $json_data,
        ),
    );

    // Cria o contexto com as opções definidas
    $context = stream_context_create($options);

    // URL da API
    $url = 'http://localhost/api-estacionamento/api/cartoes/index.php';

    // Faz a requisição e captura a resposta
    $response = file_get_contents($url, false, $context);

    // Verifica se a requisição foi bem-sucedida
    if ($response === false) {
        $message = "Erro ao conectar com a API.";
    } else {
        // Converte a resposta de JSON para array
        $response_data = json_decode($response, true);

        // Verifica se o cartão foi adicionado com sucesso
        if (isset($response_data['message'])) {
            $message = $response_data['message']; // Exibe a mensagem retornada pela API
        } else {
            $message = "Erro inesperado ao adicionar o cartão.";
        }
    }
}
?>

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

        input[type="text"],
        input[type="email"] {
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

        .message {
            padding: 10px;
            background-color: #e8f4e5;
            border: 1px solid #c2e4d7;
            border-radius: 5px;
            margin-top: 20px;
            color: #2c662d;
            font-weight: bold;
        }

        .error-message {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
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
        <form method="POST" action="" onsubmit="return showPopup()">
            <label>Nome do Cartão:</label>
            <input type="text" name="Nome_Cartao" id="Nome_Cartao" required>
            <input type="submit" value="Adicionar">
        </form>

        <script>
            function showPopup() {
                // Exibe o popup
                alert("Aproxime o cartão do leitor");
                return true;
            }

            // Função para ocultar a mensagem após 3 segundos
            function hideMessage() {
                const messageBox = document.querySelector('.message');
                if (messageBox) {
                    setTimeout(() => {
                        messageBox.style.display = 'none';
                    }, 3000);
                }
            }

            // Executa hideMessage ao carregar a página
            window.onload = hideMessage;
        </script>

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