<?php
include('controle_estacionamento.php');

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
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

        // Atualizar dados na tabela pessoa
        $sql_pessoa = $conn->prepare("UPDATE pessoa SET Nome_Pessoa = ?, Telefone = ?, Email = ? WHERE ID_Pessoa = ?");
        $sql_pessoa->bind_param("sssi", $nome_usuario, $telefone, $email, $id_pessoa);
        if ($sql_pessoa->execute()) {
            // Atualizar o cartão se um novo for selecionado
            if ($novo_cartao_id) {
                // Verifica se o cartão está disponível
                $verifica_cartao_sql = $conn->prepare("SELECT ID_Cartao FROM Cartao WHERE ID_Cartao = ? AND ID_Pessoa IS NULL");
                $verifica_cartao_sql->bind_param("i", $novo_cartao_id);
                $verifica_cartao_sql->execute();
                $verifica_cartao_result = $verifica_cartao_sql->get_result();

                if ($verifica_cartao_result->num_rows > 0) {
                    // Atualiza o cartão da pessoa
                    $sql_cartao = $conn->prepare("UPDATE Cartao SET ID_Pessoa = ? WHERE ID_Cartao = ?");
                    $sql_cartao->bind_param("ii", $id_pessoa, $novo_cartao_id);
                    $sql_cartao->execute();
                    $message = "Registro atualizado com sucesso e cartão trocado.";
                } else {
                    $message = "Erro: O cartão selecionado já está em uso.";
                }
                $verifica_cartao_sql->close();
            } else {
                $message = "Registro atualizado com sucesso.";
            }
        } else {
            $message = "Erro ao atualizar o registro: " . $sql_pessoa->error;
        }
        $sql_pessoa->close();
    }

    // Excluir registro
    if (isset($_POST['delete'])) {
        $id_pessoa = $_POST['id_pessoa'];

        // Tornar o cartão disponível, removendo a referência da pessoa
        $sql_cartao = $conn->prepare("UPDATE Cartao SET ID_Pessoa = NULL WHERE ID_Pessoa = ?");
        $sql_cartao->bind_param("i", $id_pessoa);
        $sql_cartao->execute();
        $sql_cartao->close();

        // Excluir a pessoa da tabela pessoa
        $sql_pessoa = $conn->prepare("DELETE FROM Pessoa WHERE ID_Pessoa = ?");
        $sql_pessoa->bind_param("i", $id_pessoa);
        if ($sql_pessoa->execute()) {
            $message = "Registro excluído com sucesso.";
        } else {
            $message = "Erro ao excluir o registro: " . $sql_pessoa->error;
        }
        $sql_pessoa->close();
    }
}

// Selecionar registros de pessoa
$sql = "SELECT ID_Pessoa, Nome_Pessoa, Telefone, Email FROM Pessoa";
$result = $conn->query($sql);

// Selecionar cartões disponíveis
$sql_cartoes = "SELECT ID_Cartao, NS_Cartao FROM Cartao";
$result_cartoes = $conn->query($sql_cartoes);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Pessoas - Star Parkin</title>
    <link rel="stylesheet" href="sidebar_style.css">
    <style>
        /* Estilos gerais e da sidebar */
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
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="id_pessoa" value="<?= $row['ID_Pessoa'] ?>">
                                    <input type="text" name="nome_usuario" value="<?= htmlspecialchars($row['Nome_Pessoa']) ?>" required>
                            </td>
                            <td>
                                <input type="text" name="telefone" value="<?= htmlspecialchars($row['Telefone']) ?>" required>
                            </td>
                            <td>
                                <input type="email" name="email" value="<?= htmlspecialchars($row['Email']) ?>" required>
                            </td>
                            <td>
                                <?php
                                // Obter o número do cartão associado
                                $cartao_sql = $conn->prepare("SELECT NS_Cartao FROM Cartao WHERE ID_Pessoa = ?");
                                $cartao_sql->bind_param("i", $row['ID_Pessoa']);
                                $cartao_sql->execute();
                                $cartao_result = $cartao_sql->get_result();
                                $cartao_row = $cartao_result->fetch_assoc();
                                echo htmlspecialchars($cartao_row ? $cartao_row['NS_Cartao'] : 'Nenhum cartão');
                                ?>
                            </td>
                            <td>
                                <select name="novo_cartao">
                                    <option value="">Selecionar Cartão</option>
                                    <?php while ($cartao = $result_cartoes->fetch_assoc()): ?>
                                        <option value="<?= $cartao['ID_Cartao'] ?>"><?= htmlspecialchars($cartao['NS_Cartao']) ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </td>
                            <td>
                                <button type="submit" name="edit">Salvar</button>
                                <button type="submit" name="delete" onclick="return confirm('Tem certeza que deseja excluir este registro?')">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
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

<?php
$conn->close();
?>