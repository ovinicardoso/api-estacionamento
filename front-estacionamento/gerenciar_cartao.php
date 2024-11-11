<?php
session_start();

// Verifica se o usuário está autenticado (se a sessão existe)
if (!isset($_SESSION['usuario_id'])) {
    // Se não estiver autenticado, redireciona para a página de login
    header("Location: login.php");
    exit();
}

include('controle_estacionamento.php');

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

$message = ''; // Para armazenar mensagens de erro/sucesso

// Função para fazer requisições para a API
function requestAPI($url, $method, $data = null)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    if ($data !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    }

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return ['response' => json_decode($response, true), 'http_code' => $http_code];
}

// URL base da API
$api_url = 'http://localhost/api-estacionamento/api/cartoes/index.php';

// Processa a atualização do cartão
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id_cartao']) && isset($_POST['nome_cartao']) && isset($_POST['ns_cartao'])) {
        $id_cartao = $_POST['id_cartao'];
        $nome_cartao = $_POST['nome_cartao'];
        $ns_cartao = $_POST['ns_cartao'];

        // Dados para a atualização do cartão
        $data = [
            'ID_Cartao' => $id_cartao,
            'Nome_Cartao' => $nome_cartao,
            'NS_Cartao' => $ns_cartao
        ];

        // Faz a requisição PUT para atualizar o cartão
        $result = requestAPI($api_url, 'PUT', $data);

        if ($result['http_code'] === 200) {
            $message = 'Cartão atualizado com sucesso!';
        } else {
            $message = 'Erro ao atualizar o cartão: ' . ($result['response']['message'] ?? 'Erro desconhecido.');
        }
    } elseif (isset($_POST['delete_cartao'])) {
        $id_cartao = $_POST['delete_cartao'];

        // Dados para deletar o cartão
        $data = ['ID_Cartao' => $id_cartao];

        // Faz a requisição DELETE para excluir o cartão
        $result = requestAPI($api_url, 'DELETE', $data);

        if ($result['http_code'] === 200) {
            $message = 'Cartão excluído com sucesso!';
        } else {
            $message = 'Erro ao excluir o cartão: ' . ($result['response']['message'] ?? 'Erro desconhecido.');
        }
    }
}

// Consulta todos os cartões existentes via API
$result = requestAPI($api_url, 'GET');

if ($result['http_code'] === 200 && isset($result['response']['cartoes'])) {
    $cartoes = $result['response']['cartoes'];
} else {
    $cartoes = [];
    $message = 'Erro ao buscar os cartões: ' . ($result['response']['message'] ?? 'Erro desconhecido.');
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Cartões - Star Parking</title>
    <link rel="stylesheet" href="sidebar_style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            height: 100vh;
            margin: 0;
        }

        .sidebar {
            width: 20%;
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
        }

        .sidebar h2 {
            margin-bottom: 30px;
        }

        .sidebar ul {
            list-style-type: none;
            padding: 0;
        }

        .sidebar ul li {
            margin-bottom: 20px;
        }

        .sidebar ul li a {
            color: white;
            text-decoration: none;
            font-size: 18px;
            display: block;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .sidebar ul li a:hover {
            background-color: #34495e;
        }

        .content {
            margin-left: 20%;
            padding: 20px;
            width: 80%;
        }

        h1 {
            color: #333;
        }

        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: white;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #ddd;
        }

        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .message {
            margin-top: 20px;
            padding: 10px;
            background-color: #e8f8e8;
            border: 1px solid #d4eed4;
            border-radius: 4px;
        }

        .success {
            color: green;
            border-color: green;
        }

        .error {
            color: red;
            border-color: red;
        }

        .input-largo {
            width: 350px;
            padding: 8px;
            font-size: 14px;
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
        <h1>Gerenciar Cartões</h1>

        <?php if ($message): ?>
            <div class="message <?= strpos($message, 'Erro') !== false ? 'error' : 'success' ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <table>
            <tr>
                <th>Nome do Cartão</th>
                <th>Número de Série</th>
                <th>Ações</th>
            </tr>

            <?php if (!empty($cartoes)): ?>
                <?php foreach ($cartoes as $cartao): ?>
                    <tr>
                        <td>
                            <form method="POST" action="gerenciar_cartao.php">
                                <input type="hidden" name="id_cartao" value="<?= $cartao['ID_Cartao'] ?>">
                                <input type="text" name="nome_cartao" value="<?= $cartao['Nome_Cartao'] ?>" required class="input-largo">
                        </td>
                        <td>
                            <input type="text" name="ns_cartao" value="<?= $cartao['NS_Cartao'] ?>" required class="input-largo">
                        </td>
                        <td>
                            <button type="submit">Atualizar</button>
                            </form>
                            <form method="POST" action="gerenciar_cartao.php">
                                <input type="hidden" name="delete_cartao" value="<?= $cartao['ID_Cartao'] ?>">
                                <button type="submit" onclick="return confirm('Tem certeza que deseja excluir?')">Excluir</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </table>
    </div>
</body>

</html>