<?php
include ('db.php');

// Conexão com o banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Função para obter informações das vagas e ocupantes
$vagas_sql = "SELECT Vaga.Nome_Vaga, Movimentacao.Hora_Entrada, Movimentacao.Hora_Saida, Pessoa.Nome_Pessoa
              FROM Vaga
              LEFT JOIN Movimentacao ON Vaga.ID_Vaga = Movimentacao.ID_Vaga
              LEFT JOIN Cartao ON Movimentacao.ID_Cartao = Cartao.ID_Cartao
              LEFT JOIN Pessoa ON Cartao.ID_Pessoa = Pessoa.ID_Pessoa";
$vagas_result = $conn->query($vagas_sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Vagas - Star Parking</title>
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
padding: 10px;
border-radius: 5px;
transition: background-color 0.3s;
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
        <h1 style="text-align: center;">Visualização de Vagas</h1>
        
        <table>
            <thead>
                <tr>
                    <th>Número da Vaga</th>
                    <th>Ocupante</th>
                    <th>Hora de Entrada</th>
                    <th>Hora de Saída</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($vagas_result->num_rows > 0): ?>
                    <?php while($row = $vagas_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['Nome_Vaga']; ?></td>
                            <td><?php echo $row['Nome_Pessoa']; ?></td>
                            <td><?php echo $row['Hora_Entrada']; ?></td>
                            <td><?php echo $row['Hora_Saida']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">Nenhuma vaga registrada.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>

<?php
$conn->close();
?>
