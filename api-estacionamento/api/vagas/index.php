<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once '../../config/database.php';
require_once '../../models/Vaga.php';

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    die(json_encode(array("message" => "Erro ao conectar ao banco de dados.")));
}

$vaga = new Vaga($db);

// Obter o método HTTP da requisição
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Listar todas as vagas
        $stmt = $vaga->listar();
        $num = $stmt->rowCount();

        if ($num > 0) {
            $vagas_arr = array();
            $vagas_arr["vagas"] = array();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);

                $vaga_item = array(
                    "ID_Vaga" => $ID_Vaga,
                    "Nome_Vaga" => $Nome_Vaga,
                    "Ocupado" => $Ocupado
                );

                array_push($vagas_arr["vagas"], $vaga_item);
            }

            // Exibe as vagas em formato JSON
            echo json_encode($vagas_arr);
        } else {
            echo json_encode(array("message" => "Nenhuma vaga encontrada."));
        }
        break;

    case 'POST':
        // Criar uma nova vaga
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->Nome_Vaga)) {
            $vaga->Nome_Vaga = $data->Nome_Vaga;
            $vaga->Ocupado = $data->Ocupado ?? 0;

            if ($vaga->criar()) {
                echo json_encode(array("message" => "Vaga criada com sucesso."));
            } else {
                echo json_encode(array("message" => "Falha ao criar vaga."));
            }
        } else {
            echo json_encode(array("message" => "Dados incompletos."));
        }
        break;

    case 'PUT':
        // Atualizar o status de uma vaga existente
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->ID_Vaga) && isset($data->Ocupado)) {
            $vaga->ID_Vaga = $data->ID_Vaga;
            $vaga->Ocupado = $data->Ocupado;

            if ($vaga->atualizarStatus($vaga->ID_Vaga, $vaga->Ocupado)) {
                echo json_encode(array("message" => "Status da vaga atualizado."));
            } else {
                echo json_encode(array("message" => "Falha ao atualizar o status."));
            }
        } else {
            echo json_encode(array("message" => "Dados incompletos."));
        }
        break;

    case 'DELETE':
        // Deletar uma vaga existente
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->ID_Vaga)) {
            $vaga->ID_Vaga = $data->ID_Vaga;

            if ($vaga->deletar()) {
                echo json_encode(array("message" => "Vaga deletada com sucesso."));
            } else {
                echo json_encode(array("message" => "Falha ao deletar vaga."));
            }
        } else {
            echo json_encode(array("message" => "ID da vaga não informado."));
        }
        break;

    default:
        echo json_encode(array("message" => "Método não suportado."));
        break;
}
?>
