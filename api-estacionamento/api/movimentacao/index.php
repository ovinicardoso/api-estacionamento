<?php
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
            // Lê todas as movimentações
            $stmt = $movimentacao->readAll();
            $movimentacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($movimentacoes);
        }
        break;

    case 'POST':
        // Cria uma nova movimentação
        $data = json_decode(file_get_contents("php://input"));
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
?>
