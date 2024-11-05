<?php
include('controle_estacionamento.php');

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

$message = ''; // Para armazenar mensagens de erro/sucesso

// Processa a atualização do cartão
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id_cartao']) && isset($_POST['nome_cartao']) && isset($_POST['ns_cartao'])) {
        $id_cartao = $_POST['id_cartao'];
        $nome_cartao = $_POST['nome_cartao'];
        $ns_cartao = $_POST['ns_cartao'];

        // Atualiza o cartão com o novo nome e número de série
        $sql = $conn->prepare("UPDATE cartao SET Nome_Cartao = ?, NS_Cartao = ? WHERE ID_Cartao = ?");
        $sql->bind_param("ssi", $nome_cartao, $ns_cartao, $id_cartao);

        try {
            $sql->execute();
            $message = 'Cartão atualizado com sucesso!'; // Mensagem de sucesso
        } catch (mysqli_sql_exception $e) {
            $message = 'Erro ao atualizar o cartão: ' . $e->getMessage(); // Mensagem de erro
        } finally {
            $sql->close();
        }
    } elseif (isset($_POST['delete_cartao'])) {
        $id_cartao = $_POST['delete_cartao'];

        // Exclui o cartão
        $sql = $conn->prepare("DELETE FROM cartao WHERE ID_Cartao = ?");
        $sql->bind_param("i", $id_cartao);

        try {
            $sql->execute();
            $message = 'Cartão excluído com sucesso!'; // Mensagem de sucesso
        } catch (mysqli_sql_exception $e) {
            if (strpos($e->getMessage(), 'a foreign key constraint fails') !== false) {
                $message = 'Erro ao excluir o cartão: Este cartão está vinculado a movimentações e não pode ser excluído.';
            } else {
                $message = 'Erro ao excluir o cartão: ' . $e->getMessage();
            }
        }
    }
}

// Consulta todos os cartões existentes no banco de dados
$result = $conn->query("SELECT ID_Cartao, Nome_Cartao, NS_Cartao FROM Cartao");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Cartões</title>
    <link rel="stylesheet" href="sidebar_style.css">
    <style>
        /* Estilos gerais e da sidebar */
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
        th, td {
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
            color: green; /* Cor da mensagem de sucesso */
            background-color: #e8f8e8; /* Fundo verde claro */
            border: 1px solid #d4eed4; /* Borda verde clara */
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

            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <form method="POST" action="gerenciar_cartao.php">
                                <input type="hidden" name="id_cartao" value="<?= $row['ID_Cartao'] ?>">
                                <input type="text" name="nome_cartao" value="<?= $row['Nome_Cartao'] ?>" required>
                        </td>
                        <td>
                                <input type="text" name="ns_cartao" value="<?= $row['NS_Cartao'] ?>" required>
                        </td>
                        <td>
                                <button type="submit">Salvar</button>
                            </form>
                            <form method="POST" action="gerenciar_cartao.php" style="display:inline;">
                                <input type="hidden" name="delete_cartao" value="<?= $row['ID_Cartao'] ?>">
                                <button type="submit" onclick="return confirm('Tem certeza que deseja excluir este cartão? Esta ação não pode ser desfeita.');">Excluir</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">Nenhum cartão encontrado.</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>
