<?php
date_default_timezone_set('America/Sao_Paulo');

header('Content-Type: application/json');
header("Content-Type: application/json; charset=UTF-8");

require_once '../../config/database.php';
require_once '../../models/Movimentacao.php';

$database = new Database();
$conn = $database->getConnection();

$movimentacao = new Movimentacao($conn); // Cria uma nova instância do modelo Movimentacao
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            // Lê uma movimentação específica por ID
            $movimentacao->id_movimentacao = $_GET['id'];
            $movimentacao->read();
            echo json_encode($movimentacao);
        } else {
            // Lê todas as movimentações, incluindo o nome do cartão e nome da vaga
            $query = "
                SELECT 
                    m.ID_Movimentacao, 
                    m.Hora_Entrada, 
                    m.Hora_Saida, 
                    c.Nome_Cartao, 
                    v.Nome_Vaga  -- Altere aqui para pegar o Nome da Vaga
                FROM 
                    Movimentacao m 
                LEFT JOIN 
                    Cartao c ON m.ID_Cartao = c.ID_Cartao 
                LEFT JOIN 
                    Vaga v ON m.ID_Vaga = v.ID_Vaga";

            $stmt = $conn->prepare($query);
            $stmt->execute();
            $movimentacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($movimentacoes);
        }
        break;

    case 'POST':
        // Cria uma nova movimentação
        $data = json_decode(file_get_contents("php://input"));

        if (isset($data->sensorNumero) && isset($data->status)) {
            // Registrar ou atualizar o status da vaga com base no sensor
            $sensor_numero = $data->sensorNumero;
            $status_ocupado = ($data->status === "ativado") ? 0 : 1; // 0 para ocupado, 1 para livre
    
            if ($movimentacao->atualizarStatusVaga($sensor_numero, $status_ocupado)) {
                $status_mensagem = $status_ocupado ? "ocupado" : "livre";
                echo json_encode(["message" => "Status da vaga $sensor_numero atualizado para $status_mensagem."]);
            } else {
                echo json_encode(["message" => "Erro ao atualizar o status da vaga."]);
            }
        } else {
            // Lógica para movimentação normal
        }
        break;

        $movimentacao->hora_entrada = $data->hora_entrada;
        $movimentacao->hora_saida = $data->hora_saida;
        $movimentacao->id_cartao = $data->id_cartao;
        $movimentacao->id_vaga = $data->id_vaga;

        if ($movimentacao->create()) {
            echo json_encode(["message" => "Movimentação criada com sucesso."]);
        } else {
            echo json_encode(["message" => "Não foi possível criar a movimentação."]);
        }
        break;

    case 'PUT':
        // Atualiza uma movimentação existente
        $data = json_decode(file_get_contents("php://input"));
        $movimentacao->id_movimentacao = $data->id_movimentacao;
        $movimentacao->hora_entrada = $data->hora_entrada;
        $movimentacao->hora_saida = $data->hora_saida;
        $movimentacao->id_cartao = $data->id_cartao;
        $movimentacao->id_vaga = $data->id_vaga;

        if ($movimentacao->update()) {
            echo json_encode(["message" => "Movimentação atualizada com sucesso."]);
        } else {
            echo json_encode(["message" => "Não foi possível atualizar a movimentação."]);
        }
        break;

    case 'DELETE':
        // Deleta uma movimentação
        $data = json_decode(file_get_contents("php://input"));
        $movimentacao->id_movimentacao = $data->id_movimentacao;

        if ($movimentacao->delete()) {
            echo json_encode(["message" => "Movimentação deletada com sucesso."]);
        } else {
            echo json_encode(["message" => "Não foi possível deletar a movimentação."]);
        }
        break;

    default:
        echo json_encode(["message" => "Método não suportado."]);
        break;
}
