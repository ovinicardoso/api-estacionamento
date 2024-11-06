<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once '../../config/database.php';
require_once '../../models/Cartao.php';

$database = new Database();
$db = $database->getConnection();
$cartao = new Cartao($db);

// Obter o método HTTP da requisição
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Listar cartões ou um específico
        if (isset($_GET['id'])) {
            $cartao->ID_Cartao = $_GET['id'];
            $stmt = $cartao->listarPorId();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($row);
        } else {
            $stmt = $cartao->listar();
            $num = $stmt->rowCount();

            if ($num > 0) {
                $cartoes_arr = array();
                $cartoes_arr["cartoes"] = array();

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $cartao_item = array(
                        "ID_Cartao" => $ID_Cartao,
                        "Nome_Cartao" => $Nome_Cartao,
                        "NS_Cartao" => $NS_Cartao,
                        "ID_Pessoa" => $ID_Pessoa
                    );
                    array_push($cartoes_arr["cartoes"], $cartao_item);
                }
                echo json_encode($cartoes_arr);
            } else {
                echo json_encode(array("message" => "Nenhum cartão encontrado."));
            }
        }
        break;

        case 'POST':
            // Criar novo cartão
            $data = json_decode(file_get_contents("php://input"));
        
            if (!empty($data->NS_Cartao) && !empty($data->Nome_Cartao)) {
                $cartao->Nome_Cartao = $data->Nome_Cartao;
                $cartao->NS_Cartao = $data->NS_Cartao;
        
                if ($cartao->criar()) {
                    echo json_encode(array("message" => "Cartão criado com sucesso."));
                } else {
                    echo json_encode(array("message" => "Falha ao criar o cartão."));
                }
            } else {
                echo json_encode(array("message" => "Dados incompletos."));
            }
            break;
        

    case 'PUT':
        // Atualizar cartão
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->ID_Cartao) && !empty($data->NS_Cartao) && !empty($data->ID_Pessoa)) {
            $cartao->ID_Cartao = $data->ID_Cartao;
            $cartao->Nome_Cartao = $data->Nome_Cartao;
            $cartao->NS_Cartao = $data->NS_Cartao;
            $cartao->ID_Pessoa = $data->ID_Pessoa;

            if ($cartao->atualizar()) {
                echo json_encode(array("message" => "Cartão atualizado com sucesso."));
            } else {
                echo json_encode(array("message" => "Falha ao atualizar o cartão."));
            }
        } else {
            echo json_encode(array("message" => "Dados incompletos."));
        }
        break;

    case 'DELETE':
        // Deletar cartão
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->ID_Cartao)) {
            $cartao->ID_Cartao = $data->ID_Cartao;

            if ($cartao->deletar()) {
                echo json_encode(array("message" => "Cartão deletado com sucesso."));
            } else {
                echo json_encode(array("message" => "Falha ao deletar o cartão."));
            }
        } else {
            echo json_encode(array("message" => "ID do cartão não informado."));
        }
        break;

    default:
        echo json_encode(array("message" => "Método não permitido."));
        break;
}
?>
