<?php
include ('db.php');

// Conexão com o banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Função para obter todas as pessoas cadastradas no banco de dados
$pessoas_sql = "SELECT ID_Pessoa, Nome_Pessoa FROM pessoa";
$pessoas_result = $conn->query($pessoas_sql);

// Função para obter o status das vagas
$vagas_sql = "SELECT vaga.ID_Vaga, vaga.Numero_Vaga, vaga.Ocupado, movimentacao.Hora_entrada, movimentacao.Hora_saida, pessoa.Nome_Pessoa
              FROM vaga
              LEFT JOIN movimentacao ON vaga.ID_Vaga = movimentacao.ID_Vaga
              LEFT JOIN pessoa ON movimentacao.ID_Pessoa = pessoa.ID_Pessoa";
$vagas_result = $conn->query($vagas_sql);

// Atualizar informações da vaga quando o formulário for submetido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_vaga = $_POST['id_vaga'];
    $id_pessoa = $_POST['id_pessoa'];
    $hora_entrada = $_POST['hora_entrada'];
    $hora_saida = $_POST['hora_saida'];
    $ocupado = isset($_POST['ocupado']) ? 1 : 0;  // Definir se a vaga está ocupada ou não

    // Atualiza o status da vaga (Ocupado ou Não)
    $update_vaga_sql = "UPDATE vaga SET Ocupado = '$ocupado' WHERE ID_Vaga = '$id_vaga'";
    $conn->query($update_vaga_sql);

    // Inserir ou atualizar a movimentação (horário de entrada e saída) associada à vaga
    $update_mov_sql = "INSERT INTO movimentacao (ID_Vaga, ID_Pessoa, Hora_entrada, Hora_saida)
                       VALUES ('$id_vaga', '$id_pessoa', '$hora_entrada', '$hora_saida')
                       ON DUPLICATE KEY UPDATE ID_Pessoa = '$id_pessoa', Hora_entrada = '$hora_entrada', Hora_saida = '$hora_saida'";
    $conn->query($update_mov_sql);

    // Redirecionar para evitar reenvio de formulário
    header("Location: administracao_vagas.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administração de Vagas - StarPark</title>
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

<div class="container">
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
        <h1 style="text-align: center;">Administração de Vagas</h1>

        <form action="administracao_vagas.php" method="POST">
            <table>
                <thead>
                    <tr>
                        <th>Número da Vaga</th>
                        <th>Ocupante</th>
                        <th>Hora de Entrada</th>
                        <th>Hora de Saída</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($vagas_result->num_rows > 0): ?>
                        <?php while($row = $vagas_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['Numero_Vaga']; ?></td>
                                <td>
                                    <select name="id_pessoa">
                                        <option value="">Selecione uma pessoa</option>
                                        <?php if ($pessoas_result->num_rows > 0): ?>
                                            <?php while($pessoa = $pessoas_result->fetch_assoc()): ?>
                                                <option value="<?php echo $pessoa['ID_Pessoa']; ?>" <?php echo ($pessoa['Nome_Pessoa'] == $row['Nome_Pessoa']) ? 'selected' : ''; ?>>
                                                    <?php echo $pessoa['Nome_Pessoa']; ?>
                                                </option>
                                            <?php endwhile; ?>
                                        <?php endif; ?>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="hora_entrada" value="<?php echo $row['Hora_entrada']; ?>" placeholder="HH:MM">
                                </td>
                                <td>
                                    <input type="text" name="hora_saida" value="<?php echo $row['Hora_saida']; ?>" placeholder="HH:MM">
                                </td>
                                <td>
                                    <input type="hidden" name="id_vaga" value="<?php echo $row['ID_Vaga']; ?>">
                                    <input type="checkbox" name="ocupado" value="1" <?php echo ($row['Ocupado']) ? 'checked' : ''; ?>> Ocupada
                                    <br>
                                    <input type="submit" value="Atualizar">
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">Nenhuma vaga registrada.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </form>
    </div>
</div>

</body>
</html>

<?php
$conn->close();
?>
