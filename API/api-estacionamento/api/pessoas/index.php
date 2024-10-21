<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';
include_once '../../models/Pessoa.php';

$database = new Database();
$db = $database->getConnection();

$pessoa = new Pessoa($db);

// Verifica o método da requisição
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
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
                "Nome_Usuario" => $Nome_Usuario,
                "Telefone" => $Telefone,
                "Email" => $Email
            );

            array_push($pessoas_arr["records"], $pessoa_item);
        }

        echo json_encode($pessoas_arr);
    } else {
        echo json_encode(array("message" => "Nenhuma pessoa encontrada."));
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Criar uma nova pessoa
    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->Nome_Usuario) && !empty($data->Telefone)) {
        $pessoa->Nome_Usuario = $data->Nome_Usuario;
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
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Atualizar uma pessoa existente
    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->ID_Pessoa) && !empty($data->Nome_Usuario) && !empty($data->Telefone)) {
        $pessoa->ID_Pessoa = $data->ID_Pessoa;
        $pessoa->Nome_Usuario = $data->Nome_Usuario;
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
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Deletar uma pessoa existente
    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->ID_Pessoa)) {
        $pessoa->ID_Pessoa = $data->ID_Pessoa;

        if ($pessoa->deletar()) {
            echo json_encode(array("message" => "Pessoa deletada com sucesso."));
        } else {
            echo json_encode(array("message" => "Falha ao deletar a pessoa."));
        }
    } else {
        echo json_encode(array("message" => "ID da pessoa não informado."));
    }
} else {
    echo json_encode(array("message" => "Método não suportado."));
}
?>
