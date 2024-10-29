<?php
include('db.php');

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Lógica para adicionar, editar e excluir
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Adicionar novo registro
    if (isset($_POST['add'])) {
        $Nome_Usuario = $_POST['Nome_Usuario'];
        $Telefone = $_POST['Telefone'];
        $Email = $_POST['Email'];
        $NS_Cartao = $_POST['NS_Cartao'];

        // Preparar e inserir dados na tabela pessoa
        $sql_pessoa = $conn->prepare("INSERT INTO pessoa (Nome_Pessoa, Telefone, Email) VALUES (?, ?, ?)");
        $sql_pessoa->bind_param("sss", $Nome_Usuario, $Telefone, $Email);
        
        if ($sql_pessoa->execute()) {
            $ID_Pessoa = $conn->insert_id; // Obtém o ID da pessoa inserida
            
            // Inserir dados na tabela cartao
            $sql_cartao = $conn->prepare("INSERT INTO Cartao (NS_Cartao, ID_Pessoa) VALUES (?, ?)");
            $sql_cartao->bind_param("si", $NS_Cartao, $ID_Pessoa);
            $sql_cartao->execute();
        }

        $sql_pessoa->close();
        $sql_cartao->close();
    }

    // Editar registro
    if (isset($_POST['edit'])) {
        $ID_Pessoa = $_POST['ID_Pessoa'];
        $Nome_Usuario = $_POST['Nome_Usuario'];
        $Telefone = $_POST['Telefone'];
        $Email = $_POST['Email'];
        $NS_Cartao = $_POST['NS_Cartao'];
        $ID_Cartao = $_POST['ID_Cartao'];

        // Atualizar dados na tabela Pessoa
        $sql_pessoa = $conn->prepare("UPDATE Pessoa SET Nome_Pessoa = ?, Telefone = ?, Email = ? WHERE ID_Pessoa = ?");
        $sql_pessoa->bind_param("sssi", $Nome_Usuario, $Telefone, $Email, $ID_Pessoa);
        $sql_pessoa->execute();

        // Atualizar dados na tabela cartao
        $sql_cartao = $conn->prepare("UPDATE Cartao SET NS_Cartao = ? WHERE ID_Cartao = ?");
        $sql_cartao->bind_param("si", $NS_Cartao, $ID_Cartao);
        $sql_cartao->execute();

        $sql_pessoa->close();
        $sql_cartao->close();
    }

    // Excluir registro
    if (isset($_POST['delete'])) {
        $ID_Pessoa = $_POST['ID_Pessoa'];
        $ID_Cartao = $_POST['ID_Cartao'];

        // Excluir da tabela cartao
        $sql_cartao = $conn->prepare("DELETE FROM Cartao WHERE ID_Cartao = ?");
        $sql_cartao->bind_param("i", $ID_Cartao);
        $sql_cartao->execute();

        // Excluir da tabela pessoa
        $sql_pessoa = $conn->prepare("DELETE FROM Pessoa WHERE ID_Pessoa = ?");
        $sql_pessoa->bind_param("i", $ID_Pessoa);
        $sql_pessoa->execute();

        $sql_cartao->close();
        $sql_pessoa->close();
    }
}

// Selecionar registros de pessoa e cartão
$sql = "SELECT Pessoa.ID_Pessoa, Pessoa.Nome_Pessoa, Pessoa.Telefone, Pessoa.Email, Cartao.ID_Cartao, Cartao.NS_Cartao 
        FROM Pessoa 
        JOIN Cartao ON Pessoa.ID_Pessoa = Cartao.ID_Pessoa";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Star Parking - Gerenciamento de Cadastros</title>
    <link rel="stylesheet" href="sidebar_style.css">
    <style>
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
}
.sidebar {
    height: 100%;
    width: 250px;
    position: fixed;
    top: 0;
    left: 0;
    background-color: #333;
    padding-top: 20px;
}
.sidebar a {
    padding: 10px 15px;
    text-decoration: none;
    font-size: 18px;
    color: white;
    display: block;
}
.sidebar a:hover {
    background-color: #575757;
}
.content {
    margin-left: 260px;
    padding: 20px;
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
/* Resetando margens e preenchimentos padrões */
* {
margin: 0;
padding: 0;
box-sizing: border-box;
}

/* Estilo do corpo */
body {
font-family: Arial, sans-serif;
display: flex;
height: 100vh;
}

/* Container principal */
.container {
display: flex;
width: 100%;
}

/* Estilo da sidebar */
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

/* Título da sidebar */
.sidebar h2 {
margin-bottom: 30px;
}

/* Lista de navegação da sidebar */
.sidebar ul {
list-style-type: none;
}

/* Itens da lista da sidebar */
.sidebar ul li {
margin-bottom: 20px;
}

/* Links da lista da sidebar */
.sidebar ul li a {
color: white;
text-decoration: none;
font-size: 18px;
display: block;
padding: 10px; /* Adicionando preenchimento aos links */
border-radius: 5px; /* Arredondando as bordas dos links */
transition: background-color 0.3s; /* Suavizando a transição de fundo */
}

/* Efeito de hover nos links da sidebar */
.sidebar ul li a:hover {
background-color: #34495e;
}

/* Estilo do conteúdo principal */
.content {
margin-left: 20%;
padding: 20px;
width: 80%;
}

/* Estilo de cabeçalhos */
h1 {
font-size: 28px;
}

/* Estilo de parágrafos */
p {
font-size: 18px;
}

    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Star Parking</h2>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="cadastro.php">Gerenciar Cadastros</a></li>
            <li><a href="administracao_vagas.php">Administração de Vagas</a></li>
            <li><a href="visualizar_vaga.php">Visualizar Vagas</a></li>
            <li><a href="logout.php">Sair</a></li>
        </ul>
    </div>

    <div class="content">
        <h1>Gerenciamento de Cadastros</h1>

        <!-- Formulário para Adicionar -->
        <form method="POST">
            <h2>Adicionar Nova Pessoa e Cartão</h2>
            <label>Nome:</label>
            <input type="text" name="Nome_Usuario" required><br>
            <label>Telefone:</label>
            <input type="text" name="Telefone" required><br>
            <label>Email:</label>
            <input type="Email" name="Email" required><br>
            <label>Número de Série do Cartão:</label>
            <input type="text" name="NS_Cartao" required><br>
            <input type="submit" name="add" value="Adicionar" class="button">
        </form>

        <!-- Exibição da lista -->
        <h2>Lista de Pessoas e Cartões</h2>
        <table>
            <thead>
                <tr>
                    <th>ID Pessoa</th>
                    <th>Nome</th>
                    <th>Telefone</th>
                    <th>Email</th>
                    <th>ID Cartão</th>
                    <th>Número de Série</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['ID_Pessoa'] ?></td>
                            <td><?= $row['Nome_Pessoa'] ?></td>
                            <td><?= $row['Telefone'] ?></td>
                            <td><?= $row['Email'] ?></td>
                            <td><?= $row['ID_Cartao'] ?></td>
                            <td><?= $row['NS_Cartao'] ?></td>
                            <td>
                                <form method="POST" style="display:inline-block;">
                                    <input type="hidden" name="ID_Pessoa" value="<?= $row['ID_Pessoa'] ?>">
                                    <input type="hidden" name="ID_Cartao" value="<?= $row['ID_Cartao'] ?>">
                                    <input type="text" name="Nome_Usuario" value="<?= $row['Nome_Pessoa'] ?>" required>
                                    <input type="text" name="Telefone" value="<?= $row['Telefone'] ?>" required>
                                    <input type="Email" name="Email" value="<?= $row['Email'] ?>" required>
                                    <input type="text" name="NS_Cartao" value="<?= $row['NS_Cartao'] ?>" required>
                                    <input type="submit" name="edit" value="Editar" class="button">
                                </form>
                                <form method="POST" style="display:inline-block;">
                                    <input type="hidden" name="ID_Pessoa" value="<?= $row['ID_Pessoa'] ?>">
                                    <input type="hidden" name="ID_Cartao" value="<?= $row['ID_Cartao'] ?>">
                                    <input type="submit" name="delete" value="Excluir" class="button" onclick="return confirm('Tem certeza que deseja excluir?');">
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">Nenhum registro encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
