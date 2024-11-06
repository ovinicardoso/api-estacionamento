<?php

include('controle_estacionamento.php');

// Variáveis para armazenar mensagens
$mensagem = "";
$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_usuario = $_POST['nome_usuario'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];
    $id_cartao = $_POST['id_cartao'];

    if (empty($nome_usuario) || empty($telefone)) {
        $error_message = "Erro: Nome e Telefone são obrigatórios.";
    } else {
        $dados = array(
            'Nome_Pessoa' => $nome_usuario,
            'Telefone' => $telefone,
            'Email' => $email,
            'ID_Cartao' => $id_cartao // Enviar o ID do Cartão
        );

        // URL da API
        $url_api = "http://localhost/api-estacionamento/api/pessoas/index.php";
        $ch = curl_init($url_api);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dados));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));

        $resposta = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Erro cURL: ' . curl_error($ch);
            curl_close($ch);
            exit;
        }

        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Código de status da resposta da API
        curl_close($ch);

        if ($status_code == 200) {
            $resposta_json = json_decode($resposta);
            $mensagem = $resposta_json->message;
        } else {
            $resposta_json = json_decode($resposta);
            $error_message = isset($resposta_json->message) ? $resposta_json->message : "Erro desconhecido.";
        }
    }
}

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Verificar cartões não associados a nenhuma pessoa
$sql_cartao = $conn->query("SELECT * FROM Cartao WHERE ID_Cartao NOT IN (SELECT ID_Cartao FROM Pessoa WHERE ID_Cartao IS NOT NULL)");
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Pessoa - Star Parking</title>
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
        input[type="email"],
        select {
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
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .mensagem {
            margin-top: 20px;
            padding: 10px;
            color: green;
            background-color: #e8f8e8;
            border: 1px solid #d4eed4;
            border-radius: 4px;
        }

        .error-message {
            margin-top: 20px;
            padding: 10px;
            color: red;
            background-color: #f8e8e8;
            border: 1px solid #eed4d4;
            border-radius: 4px;
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
            <h1>Adicionar Nova Pessoa</h1>
            <?php if ($mensagem): ?>
                <div class="mensagem"><?php echo $mensagem; ?></div>
            <?php elseif ($error_message): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>
            <form method="POST">
                <label>Nome:</label>
                <input type="text" name="nome_usuario" required>

                <label>Telefone:</label>
                <input type="text" name="telefone" required>

                <label>Email:</label>
                <input type="email" name="email" required>

                <label>Cartão:</label>
                <select name="id_cartao" required>
                    <option value="">Selecione um cartão</option>
                    <?php while ($cartao = $sql_cartao->fetch_assoc()): ?>
                        <option value="<?= $cartao['ID_Cartao'] ?>"><?= $cartao['Nome_Cartao'] . ' - ' . $cartao['NS_Cartao'] ?></option>
                    <?php endwhile; ?>
                </select>

                <input type="submit" value="Adicionar">
            </form>
        </div>
    </div>
</body>

</html>