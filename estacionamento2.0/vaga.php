<?php
// Iniciar a sessão
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    // Se não estiver logado, redireciona para a página de login
    header("Location: login.php");
    exit();
}

// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "estacionamento";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Consultas ao banco de dados
$sql = "SELECT * FROM vaga";
$result = $conn->query($sql);

$sql = "SELECT * FROM cartao";
$result_cartao = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vagas</title>
    <link rel="stylesheet" href="style.css">
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
    <h1>Informações das Vagas</h1>

    <div class="vaga-container">
    <h2 style="color: white; text-align: center;">StarPark</h2>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="vaga">
                <h2>Vaga <?php echo $row['id']; ?></h2>
                <p>Status: <?php echo $row['status']; ?></p>
                <?php if ($row['status'] == "Ocupada"): ?>
                    <p>Carro: 
                        <?php
                        // Procurar o carro associado a esta vaga
                        $carro = "Nenhum carro associado.";
                        while ($cartao = $result_cartao->fetch_assoc()) {
                            if ($cartao['vaga_id'] == $row['id']) {
                                $carro = $cartao['nome'] . " (Entrada: " . $cartao['entrada'] . ", Saída: " . $cartao['saida'] . ")";
                                break;
                            }
                        }
                        echo $carro;
                        ?>
                    </p>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>

    <div class="button-container">
        <a class="button" href="administracao.php">Ir para Administração</a>
        <a class="button" href="dashboard.php">Voltar ao Dashboard</a>
    </div>
</body>
</html>

<?php
$conn->close();
?>
