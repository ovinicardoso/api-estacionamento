<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once '../../config/database.php';
require_once '../../models/Pessoa.php';

$database = new Database();
$db = $database->getConnection();

$pessoa = new Pessoa($db);

// Obter o método HTTP da requisição
$method = $_SERVER['REQUEST_METHOD'];

// Pegar o ID da URL para operações de PUT e DELETE
$id = isset($_GET['id']) ? $_GET['id'] : null;

// Adicione este código para depuração
error_log("Método: $method");

switch ($method) {
    case 'GET':
        // Listar todas as pessoas
        $stmt = $pessoa->listar();
        $num = $stmt->rowCount();

        if ($num > 0) {
            $pessoas_arr = array();
            $pessoas_arr["records"] = array();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);

                $pessoa_item = array(
                    "ID_Pessoa" => $ID_Pessoa,
                    "Nome_Pessoa" => $Nome_Pessoa,
                    "Telefone" => $Telefone,
                    "Email" => $Email
                );

                array_push($pessoas_arr["records"], $pessoa_item);
            }

            echo json_encode($pessoas_arr);
        } else {
            echo json_encode(array("message" => "Nenhuma pessoa encontrada."));
        }
        break;

    case 'POST':
        // Criar uma nova pessoa
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->Nome_Pessoa) && !empty($data->Telefone)) {
            $pessoa->Nome_Pessoa = $data->Nome_Pessoa;
            $pessoa->Telefone = $data->Telefone;
            $pessoa->Email = isset($data->Email) ? $data->Email : null;

            if ($pessoa->criar()) {
                echo json_encode(array("message" => "Pessoa criada com sucesso."));
            } else {
                echo json_encode(array("message" => "Erro ao criar pessoa."));
            }
        } else {
            echo json_encode(array("message" => "Dados incompletos."));
        }
        break;

    case 'PUT':
        // Atualizar uma pessoa existente
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($id) && !empty($data->Nome_Pessoa) && !empty($data->Telefone)) {
            $pessoa->ID_Pessoa = $id;
            $pessoa->Nome_Pessoa = $data->Nome_Pessoa;
            $pessoa->Telefone = $data->Telefone;
            $pessoa->Email = isset($data->Email) ? $data->Email : null;

            if ($pessoa->atualizar()) {
                echo json_encode(array("message" => "Pessoa atualizada com sucesso."));
            } else {
                echo json_encode(array("message" => "Falha ao atualizar a pessoa."));
            }
        } else {
            echo json_encode(array("message" => "Dados incompletos."));
        }
        break;

    case 'DELETE':
        // Deletar uma pessoa existente
        if (!empty($id)) {
            $pessoa->ID_Pessoa = $id;

            if ($pessoa->deletar()) {
                echo json_encode(array("message" => "Pessoa deletada com sucesso."));
            } else {
                echo json_encode(array("message" => "Falha ao deletar a pessoa."));
            }
        } else {
            echo json_encode(array("message" => "ID da pessoa não informado."));
        }
        break;

    default:
        echo json_encode(array("message" => "Método não suportado."));
        break;
}
