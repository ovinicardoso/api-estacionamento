<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "controle_estacionamento";

$conexao = new mysqli($servername, $username, $password, $dbname);

if ($conexao->connect_error) {
    die("Conexão falhou: " . $conexao->connect_error);
}
?>
