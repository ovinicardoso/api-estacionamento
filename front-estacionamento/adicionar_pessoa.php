<?php
include('controle_estacionamento.php');

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

$mensagem = ""; // Variável para a mensagem de confirmação

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_usuario = $_POST['Nome_Usuario'];
    $telefone = $_POST['Telefone'];
    $email = $_POST['Email'];
    $id_cartao = $_POST['ID_Cartao']; // ID do cartão selecionado

    // Inserir a nova pessoa
    $sql_pessoa = $conn->prepare("INSERT INTO pessoa (Nome_Pessoa, Telefone, Email) VALUES (?, ?, ?)");
    $sql_pessoa->bind_param("sss", $nome_usuario, $telefone, $email);

    if ($sql_pessoa->execute()) {
        $id_pessoa = $conn->insert_id;

        // Atualizar o cartão para associá-lo à nova pessoa
        $sql_atualizar_cartao = $conn->prepare("UPDATE Cartao SET ID_Pessoa = ? WHERE ID_Cartao = ?");
        $sql_atualizar_cartao->bind_param("ii", $id_pessoa, $id_cartao);
        $sql_atualizar_cartao->execute();

        $sql_atualizar_cartao->close();

        // Mensagem de sucesso
        $mensagem = "Pessoa adicionada com sucesso!";
    } else {
        $mensagem = "Erro ao adicionar pessoa.";
    }

    $sql_pessoa->close();
}

$sql_cartao = $conn->query("SELECT * FROM Cartao WHERE ID_Pessoa IS NULL"); // Selecionar cartões disponíveis

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StarPark - Adicionar Pessoa</title>
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
            /* Cor da mensagem de sucesso */
            background-color: #e8f8e8;
            /* Fundo verde claro */
            border: 1px solid #d4eed4;
            /* Borda verde clara */
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