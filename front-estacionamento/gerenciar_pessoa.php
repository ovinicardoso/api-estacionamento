<?php
include('controle_estacionamento.php');

$api_url = 'http://localhost/api-estacionamento/api';

// Função para fazer requisição GET
function make_get_request($url)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

// Função para fazer requisição PUT
function make_put_request($url, $data)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen(json_encode($data))
    ));

    $response = curl_exec($ch);

    // Verificar erros de cURL
    if (curl_errno($ch)) {
        echo 'Erro cURL: ' . curl_error($ch);
        exit;
    }

    curl_close($ch);

    // Verificar a resposta da API
    return json_decode($response, true);
}

// Função para fazer requisição DELETE
function make_delete_request($url, $data)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

// Inicializar a variável para a mensagem
$message = "";

// Lógica para editar e excluir
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Editar registro
    if (isset($_POST['edit'])) {
        $id_pessoa = $_POST['ID_Pessoa'];
        $nome_usuario = $_POST['Nome_Usuario'];
        $telefone = $_POST['Telefone'];
        $email = $_POST['Email'];
        $novo_cartao_id = $_POST['novo_cartao'] ?? null;

        $data = [
            'ID_Pessoa' => $id_pessoa,
            'Nome_Pessoa' => $nome_usuario,
            'Telefone' => $telefone,
            'Email' => $email,
        ];

        // Adicionar 'ID_Cartao' apenas se o cartão foi alterado
        if (!empty($novo_cartao_id)) {
            $data['ID_Cartao'] = $novo_cartao_id;
        }

        $response = make_put_request("$api_url/pessoas/index.php", $data);
        if (isset($response['message']) && $response['message'] == 'Pessoa atualizada com sucesso.') {
            $message = "Registro atualizado com sucesso.";
        } else {
            $message = "Erro ao atualizar o registro.";
        }
    }

    // Excluir registro
    if (isset($_POST['delete'])) {
        $id_pessoa = $_POST['ID_Pessoa']; // Agora você está pegando corretamente o ID

        $data = ['ID_Pessoa' => $id_pessoa];
        $response = make_delete_request("$api_url/pessoas/index.php", $data);

        if (isset($response['message']) && $response['message'] == 'Pessoa deletada com sucesso.') {
            $message = "Registro excluído com sucesso.";
        } else {
            $message = "Erro ao excluir o registro.";
        }
    }
}

// Conectar ao banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Alteração na consulta para obter o nome do cartão
$sql_pessoas = $conn->query("
    SELECT p.*, c.Nome_Cartao 
    FROM Pessoa p 
    LEFT JOIN Cartao c ON p.ID_Cartao = c.ID_Cartao
");

$people = [];
while ($row = $sql_pessoas->fetch_assoc()) {
    $people[] = $row;
}

// Verificar cartões não associados a nenhuma pessoa
$sql_cartao = $conn->query("SELECT * FROM Cartao WHERE ID_Cartao NOT IN (SELECT ID_Cartao FROM Pessoa WHERE ID_Cartao IS NOT NULL)");

// Armazenando cartões disponíveis
$cartoes = [];
while ($cartao = $sql_cartao->fetch_assoc()) {
    $cartoes[] = $cartao;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Pessoas - Star Parking</title>
    <link rel="stylesheet" href="sidebar_style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            height: 100vh;
        }

        .container {
            display: flex;
            width: 100%;
        }

        .sidebar {
            width: 20%;
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            display: flex;
            flex-direction: column;
            height: 100vh;
            position: fixed;
        }

        .sidebar h2 {
            margin-bottom: 30px;
        }

        .sidebar ul {
            list-style-type: none;
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

        .message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
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

        select {
            padding: 5px;
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
        <h1>Gerenciar Pessoas</h1>

        <?php if ($message): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Telefone</th>
                    <th>Email</th>
                    <th>Cartão</th>
                    <th>Trocar Cartão</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($people) > 0): ?>
                    <?php foreach ($people as $row): ?>
                        <tr>
                            <form method="POST" style="display: inline;">
                                <td>
                                    <input type="hidden" name="ID_Pessoa" value="<?= $row['ID_Pessoa'] ?>">
                                    <input type="text" name="Nome_Usuario" value="<?= htmlspecialchars($row['Nome_Pessoa']) ?>" required>
                                </td>
                                <td>
                                    <input type="text" name="Telefone" value="<?= htmlspecialchars($row['Telefone']) ?>" required>
                                </td>
                                <td>
                                    <input type="email" name="Email" value="<?= htmlspecialchars($row['Email']) ?>" required>
                                </td>
                                <td>
                                    <?= htmlspecialchars($row['Nome_Cartao'] ?? 'Nenhum cartão') ?> <!-- Agora exibe o nome do cartão -->
                                </td>
                                <td>
                                    <select name="novo_cartao">
                                        <option value="">Selecionar Cartão</option>
                                        <?php foreach ($cartoes as $cartao): ?>
                                            <option value="<?= $cartao['ID_Cartao'] ?>"><?= htmlspecialchars($cartao['Nome_Cartao']) ?> - <?= htmlspecialchars($cartao['NS_Cartao']) ?></option> <!-- Exibe nome do cartão -->
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <button type="submit" name="edit">Salvar</button>
                                    <button type="submit" name="delete" onclick="return confirm('Tem certeza que deseja excluir este registro?')">Excluir</button>
                                </td>
                            </form>
                        </tr>

                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">Nenhum registro encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>

</html>