<?php
date_default_timezone_set('America/Sao_Paulo');

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once '../../config/database.php';
require_once '../../models/Cartao.php';
require_once '../../models/Movimentacao.php';

$database = new Database();
$db = $database->getConnection();
$cartao = new Cartao($db);

// Obter o método HTTP da requisição
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
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
                        "NS_Cartao" => $NS_Cartao
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

        if (!$data) {
            echo json_encode(array("message" => "Erro: Nenhum dado JSON recebido."));
            break;
        }

        if (!empty($data->Nome_Cartao)) {
            // Armazena o nome do cartão e deixa NS_Cartao vazio
            $cartao->Nome_Cartao = $data->Nome_Cartao;
            $cartao->NS_Cartao = null; 

            if ($cartao->criar()) { 
                echo json_encode(array("message" => "Nome do cartão armazenado. Aproxime o cartão do leitor."));
            } else {
                echo json_encode(array("message" => "Erro ao armazenar o nome do cartão."));
            }

        } elseif (!empty($data->NS_Cartao)) {
            // Define NS_Cartao e busca o último registro sem UID associado
            $cartao->NS_Cartao = $data->NS_Cartao;

            if ($cartao->verificarCartao()) {

                // Buscar o ID do cartão associado ao NS_Cartao
                $stmt = $cartao->listarPorNS();
                $cartao_data = $stmt->fetch(PDO::FETCH_ASSOC);
                $id_cartao = $cartao_data['ID_Cartao'];

                // Criar uma nova movimentação
                $movimentacao = new Movimentacao($db);
                $movimentacao->id_cartao = $id_cartao;

                // Verifica se há uma movimentação sem hora de saída
                if ($movimentacao->existeMovimentacaoSemSaida()) {
                    // Atualizar a movimentação com a hora de saída
                    $movimentacao->hora_saida = date("Y-m-d H:i:s");
                    
                    if ($movimentacao->atualizarHoraSaida()) {
                        echo "1";
                    } else {
                        echo json_encode(["message" => "Erro ao registrar a movimentação de saída."]);
                    }
                } else {
                    // Criar uma nova movimentação de entrada
                    $movimentacao->hora_entrada = date("Y-m-d H:i:s");
                    $movimentacao->id_vaga = isset($data->ID_Vaga) ? $data->ID_Vaga : null; // ID da vaga, se disponível

                    if ($movimentacao->create()) {
                        echo "1";
                    } else {
                        echo json_encode(["message" => "Não foi possível criar a movimentação."]);
                    }
                }

            } else {

                $stmt = $cartao->buscarPorCartaoNulo();
                if ($stmt) {
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($row) {
                        $cartao->ID_Cartao = $row['ID_Cartao'];
                        $cartao->Nome_Cartao = $row['Nome_Cartao'];

                        if ($cartao->atualizar()) {
                            echo "0";
                        } else {
                            echo json_encode(array("message" => "Erro ao atualizar o cartão no banco de dados."));
                        }
                    } else {
                        echo "2";
                    }
                } else {
                    echo json_encode(array("message" => "Erro ao buscar cartão pendente no banco de dados."));
                }
            }

        } else {
            echo json_encode(array("message" => "Dados incompletos. Envie Nome_Cartao ou NS_Cartao."));
        }
    break;


    case 'PUT':
        // Atualizar cartão existente
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->ID_Cartao) && !empty($data->NS_Cartao) && !empty($data->Nome_Cartao)) {
            $cartao->ID_Cartao = $data->ID_Cartao;
            $cartao->Nome_Cartao = $data->Nome_Cartao;
            $cartao->NS_Cartao = $data->NS_Cartao;

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
