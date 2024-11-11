<?php
session_start();

// Verifica se o usuário está autenticado (se a sessão existe)
if (!isset($_SESSION['usuario_id'])) {
    // Se não estiver autenticado, redireciona para a página de login
    header("Location: login.php");
    exit();
}

// URL da API
$api_url = 'http://localhost/api-estacionamento/api/vagas/index.php';

// Função para fazer requisições à API
function fazer_requisicao_api($url, $dados = null, $metodo = 'GET')
{
    $ch = curl_init();

    if ($metodo == 'POST' || $metodo == 'PUT') {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dados));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
        ));
    }

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $metodo);

    $resposta = curl_exec($ch);
    curl_close($ch);

    return json_decode($resposta, true);
}

// Manipulação da ocupação da vaga e atualização do nome
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $vaga_id = $_POST['vaga_id'];

    // Atualizar nome da vaga se um novo nome for enviado
    if (isset($_POST['novo_nome'])) {
        $novo_nome = $_POST['novo_nome'];
        $dados = array(
            "ID_Vaga" => $vaga_id,
            "Nome_Vaga" => $novo_nome
        );
        fazer_requisicao_api($api_url, $dados, 'PUT');
    } else {
        // Alterna entre ocupar e desocupar a vaga
        $ocupado = $_POST['ocupado'] == 1 ? 0 : 1;
        $dados = array(
            "ID_Vaga" => $vaga_id,
            "Ocupado" => $ocupado
        );
        fazer_requisicao_api($api_url, $dados, 'PUT');
    }
}

$vagas_resposta = fazer_requisicao_api($api_url);
$vagas_result = $vagas_resposta['vagas'] ?? [];
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Vagas - Star Parking</title>
    <link rel="stylesheet" href="sidebar_style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }

        .vaga-container {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
        }

        .vaga {
            border: 2px solid #ccc;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            width: 250px;
            transition: background-color 0.3s;
        }

        .ocupada {
            background-color: #f8d7da;
            border-color: #dc3545;
        }

        .livre {
            background-color: #d4edda;
            border-color: #28a745;
        }

        button {
            margin-top: 10px;
            padding: 5px 10px;
            cursor: pointer;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 3px;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        .sucesso {
            color: green;
            font-weight: bold;
        }

        .erro {
            color: red;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="container">
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
            <h1>Gerenciar Vagas</h1>
            <div id="vaga-container" class="vaga-container">
                <?php foreach ($vagas_result as $vaga): ?>
                    <div class="vaga <?php echo $vaga['Ocupado'] == 1 ? 'ocupada' : 'livre'; ?>">
                        <form method="POST">
                            <input type="text" name="novo_nome" value="<?php echo htmlspecialchars($vaga['Nome_Vaga']); ?>">
                            <input type="hidden" name="vaga_id" value="<?php echo $vaga['ID_Vaga']; ?>">
                            <button type="submit">Salvar Nome</button>
                        </form>
                        <p>Status: <?php echo $vaga['Ocupado'] == 1 ? 'Ocupada' : 'Livre'; ?></p>
                        <form method="POST">
                            <input type="hidden" name="vaga_id" value="<?php echo $vaga['ID_Vaga']; ?>">
                            <input type="hidden" name="ocupado" value="<?php echo $vaga['Ocupado']; ?>">
                            <button type="submit"><?php echo $vaga['Ocupado'] == 1 ? 'Desocupar' : 'Ocupar'; ?></button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
            <div id="mensagem-sucesso" class="sucesso"></div>
        </div>
    </div>

    <script>
        setInterval(function() {
            fetchVagas();
        }, 5000);

        function fetchVagas() {
            fetch("http://localhost/api-estacionamento/api/vagas/index.php")
                .then(response => response.json())
                .then(data => {
                    const vagasContainer = document.getElementById("vaga-container");
                    vagasContainer.innerHTML = '';
                    data.vagas.forEach(vaga => {
                        const vagaDiv = document.createElement("div");
                        vagaDiv.classList.add("vaga");
                        vagaDiv.classList.add(vaga.Ocupado == 1 ? "ocupada" : "livre");

                        vagaDiv.innerHTML = `
                            <form method="POST">
                                <input type="text" name="novo_nome" value="${vaga.Nome_Vaga}">
                                <input type="hidden" name="vaga_id" value="${vaga.ID_Vaga}">
                                <button type="submit">Salvar Nome</button>
                            </form>
                            <p>Status: ${vaga.Ocupado == 1 ? "Ocupada" : "Livre"}</p>
                            <form method="POST">
                                <input type="hidden" name="vaga_id" value="${vaga.ID_Vaga}">
                                <input type="hidden" name="ocupado" value="${vaga.Ocupado}">
                                <button type="submit">${vaga.Ocupado == 1 ? 'Desocupar' : 'Ocupar'}</button>
                            </form>
                        `;
                        vagasContainer.appendChild(vagaDiv);
                    });
                });
        }
    </script>

</body>

</html>