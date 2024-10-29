<?php
session_start(); // Inicia a sessão
?>

<!DOCTYPE html>
<html lang="pt-BR"> <!-- Definindo o idioma para português -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Star Parking - Dashboard</title>
    <link rel="stylesheet" href="sidebar_style.css"> <!-- Arquivo CSS da sidebar -->
    <style>
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
            background-color: #f4f4f4; /* Cor de fundo */
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
            text-align: center; /* Centralizando o título */
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

        /* Estilo da tabela */
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
    </style>
</head>
<body>
    <div class="container">
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
            <h1>Bem-vindo ao Star Parking!</h1>
            <p class="welcome-message">Estamos felizes em tê-lo aqui. Gerencie suas operações de estacionamento com facilidade!</p>
        </div>
    </div>
</body>
</html>
