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
        $nome_usuario = $_POST['nome_usuario'];
        $telefone = $_POST['telefone'];
        $email = $_POST['email'];
        $ns_cartao = $_POST['ns_cartao'];

        // Preparar e inserir dados na tabela pessoa
        $sql_pessoa = $conn->prepare("INSERT INTO pessoa (Nome_Pessoa, Telefone, Email) VALUES (?, ?, ?)");
        $sql_pessoa->bind_param("sss", $nome_usuario, $telefone, $email);
        
        if ($sql_pessoa->execute()) {
            $id_pessoa = $conn->insert_id; // Obtém o ID da pessoa inserida
            
            // Inserir dados na tabela cartao
            $sql_cartao = $conn->prepare("INSERT INTO cartao (NS_Cartao, ID_Pessoa) VALUES (?, ?)");
            $sql_cartao->bind_param("si", $ns_cartao, $id_pessoa);
            $sql_cartao->execute();
        }

        $sql_pessoa->close();
        $sql_cartao->close();
    }

    // Editar registro
    if (isset($_POST['edit'])) {
        $id_pessoa = $_POST['id_pessoa'];
        $nome_usuario = $_POST['nome_usuario'];
        $telefone = $_POST['telefone'];
        $email = $_POST['email'];
        $ns_cartao = $_POST['ns_cartao'];
        $id_cartao = $_POST['id_cartao'];

        // Atualizar dados na tabela pessoa
        $sql_pessoa = $conn->prepare("UPDATE pessoa SET Nome_Pessoa = ?, Telefone = ?, Email = ? WHERE ID_Pessoa = ?");
        $sql_pessoa->bind_param("sssi", $nome_usuario, $telefone, $email, $id_pessoa);
        $sql_pessoa->execute();

        // Atualizar dados na tabela cartao
        $sql_cartao = $conn->prepare("UPDATE cartao SET NS_Cartao = ? WHERE ID_Cartao = ?");
        $sql_cartao->bind_param("si", $ns_cartao, $id_cartao);
        $sql_cartao->execute();

        $sql_pessoa->close();
        $sql_cartao->close();
    }

    // Excluir registro
    if (isset($_POST['delete'])) {
        $id_pessoa = $_POST['id_pessoa'];
        $id_cartao = $_POST['id_cartao'];

        // Excluir da tabela cartao
        $sql_cartao = $conn->prepare("DELETE FROM cartao WHERE ID_Cartao = ?");
        $sql_cartao->bind_param("i", $id_cartao);
        $sql_cartao->execute();

        // Excluir da tabela pessoa
        $sql_pessoa = $conn->prepare("DELETE FROM pessoa WHERE ID_Pessoa = ?");
        $sql_pessoa->bind_param("i", $id_pessoa);
        $sql_pessoa->execute();

        $sql_cartao->close();
        $sql_pessoa->close();
    }
}

// Selecionar registros de pessoa e cartão
$sql = "SELECT pessoa.ID_Pessoa, pessoa.Nome_Pessoa, pessoa.Telefone, pessoa.Email, cartao.ID_Cartao, cartao.NS_Cartao 
        FROM pessoa 
        JOIN cartao ON pessoa.ID_Pessoa = cartao.ID_Pessoa";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StarPark - Gerenciamento de Cadastros</title>
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
        <h2>StarPark</h2>
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
            <input type="text" name="nome_usuario" required><br>
            <label>Telefone:</label>
            <input type="text" name="telefone" required><br>
            <label>Email:</label>
            <input type="email" name="email" required><br>
            <label>Número de Série do Cartão:</label>
            <input type="text" name="ns_cartao" required><br>
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
                                    <input type="hidden" name="id_pessoa" value="<?= $row['ID_Pessoa'] ?>">
                                    <input type="hidden" name="id_cartao" value="<?= $row['ID_Cartao'] ?>">
                                    <input type="text" name="nome_usuario" value="<?= $row['Nome_Pessoa'] ?>" required>
                                    <input type="text" name="telefone" value="<?= $row['Telefone'] ?>" required>
                                    <input type="email" name="email" value="<?= $row['Email'] ?>" required>
                                    <input type="text" name="ns_cartao" value="<?= $row['NS_Cartao'] ?>" required>
                                    <input type="submit" name="edit" value="Editar" class="button">
                                </form>
                                <form method="POST" style="display:inline-block;">
                                    <input type="hidden" name="id_pessoa" value="<?= $row['ID_Pessoa'] ?>">
                                    <input type="hidden" name="id_cartao" value="<?= $row['ID_Cartao'] ?>">
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
