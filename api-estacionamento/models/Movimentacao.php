<?php
date_default_timezone_set('America/Sao_Paulo');

class Movimentacao
{
    private $conn;

    public $id_movimentacao;
    public $hora_entrada;
    public $hora_saida;
    public $id_cartao;
    public $id_vaga;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Método para criar uma movimentação
    public function create()
    {
        $query = "INSERT INTO Movimentacao (Hora_Entrada, Hora_Saida, ID_Cartao, ID_Vaga) VALUES (:hora_entrada, :hora_saida, :id_cartao, :id_vaga)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':hora_entrada', $this->hora_entrada);
        $stmt->bindParam(':hora_saida', $this->hora_saida);
        $stmt->bindParam(':id_cartao', $this->id_cartao);
        $stmt->bindParam(':id_vaga', $this->id_vaga);

        return $stmt->execute();
    }

    // Método para ler todas as movimentações
    public function readAll()
    {
        $query = "SELECT * FROM Movimentacao";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Verificar se existe uma movimentação sem hora de saída para o cartão
    public function existeMovimentacaoSemSaida() {
        $query = "SELECT ID_Movimentacao FROM Movimentacao WHERE ID_Cartao = :id_cartao AND Hora_Saida IS NULL LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_cartao', $this->id_cartao);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id_movimentacao = $row['ID_Movimentacao']; // Armazena o ID da movimentação para atualização
            return true;
        }
        return false;
    }

    // Atualizar a movimentação com a hora de saída
    public function atualizarHoraSaida() {
        $query = "UPDATE Movimentacao SET Hora_Saida = :hora_saida WHERE ID_Movimentacao = :id_movimentacao";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':hora_saida', $this->hora_saida);
        $stmt->bindParam(':id_movimentacao', $this->id_movimentacao);

        return $stmt->execute();
    }

    // Método para ler uma movimentação específica
    public function read()
    {
        $query = "SELECT * FROM Movimentacao WHERE ID_Movimentacao = :id_movimentacao";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_movimentacao', $this->id_movimentacao);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->hora_entrada = $row['Hora_Entrada'];
            $this->hora_saida = $row['Hora_Saida'];
            $this->id_cartao = $row['ID_Cartao'];
            $this->id_vaga = $row['ID_Vaga'];
        }
    }

    // Método para atualizar o status da vaga
    private function updateVagaStatus($status)
    {
        // Mapeia o status para 0 (não ocupado) ou 1 (ocupado)
        $statusValue = ($status === 'ocupada') ? 1 : 0; // 1 para 'ocupada', 0 para 'livre'

        // Atualiza o status da vaga
        $query = "UPDATE Vaga SET Ocupado = :status WHERE ID_Vaga = :id_vaga";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':status', $statusValue);
        $stmt->bindParam(':id_vaga', $this->id_vaga);

        return $stmt->execute();
    }

    // Método para atualizar o status da vaga com base no sensor
    public function atualizarStatusVaga($sensor_numero, $status_ocupado)
    {
        $query = "UPDATE Vaga SET Ocupado = :status_ocupado WHERE ID_Vaga = :sensor_numero";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':status_ocupado', $status_ocupado);
        $stmt->bindParam(':sensor_numero', $sensor_numero);

        if ($stmt->execute() && $status_ocupado == 1) {
            // Se a vaga foi marcada como "ocupada", associar à primeira movimentação com hora de saída nula
            return $this->associarVagaMovimentacao($sensor_numero);
        }

        return $stmt->execute();
    }

    // Método para associar a vaga à primeira movimentação com hora de saída nula
    private function associarVagaMovimentacao($id_vaga)
    {
        // Buscar a primeira movimentação com hora de saída nula
        $query = "SELECT ID_Movimentacao FROM Movimentacao WHERE Hora_Saida IS NULL AND ID_Vaga IS NULL ORDER BY Hora_Entrada ASC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $id_movimentacao = $row['ID_Movimentacao'];

            // Associar a movimentação à vaga
            $query_update = "UPDATE Movimentacao SET ID_Vaga = :id_vaga WHERE ID_Movimentacao = :id_movimentacao";
            $stmt_update = $this->conn->prepare($query_update);
            $stmt_update->bindParam(':id_vaga', $id_vaga);
            $stmt_update->bindParam(':id_movimentacao', $id_movimentacao);

            return $stmt_update->execute();
        }

        return false; // Retorna falso se não houver movimentação com hora de saída nula
    }

    // Método para atualizar uma movimentação
    public function update()
    {
        $query = "UPDATE Movimentacao SET Hora_Entrada = :hora_entrada, Hora_Saida = :hora_saida, ID_Cartao = :id_cartao, ID_Vaga = :id_vaga WHERE ID_Movimentacao = :id_movimentacao";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id_movimentacao', $this->id_movimentacao);
        $stmt->bindParam(':hora_entrada', $this->hora_entrada);
        $stmt->bindParam(':hora_saida', $this->hora_saida);
        $stmt->bindParam(':id_cartao', $this->id_cartao);
        $stmt->bindParam(':id_vaga', $this->id_vaga);

        if ($stmt->execute()) {
            // Atualiza o status da vaga com base na hora de saída
            if ($this->hora_saida) {
                return $this->updateVagaStatus('livre'); // Libera a vaga quando a saída é registrada
            } else {
                return $this->updateVagaStatus('ocupada'); // Marca como ocupada caso não haja hora de saída
            }
        }
        return false; // Se a atualização falhar, retorna falso
    }


    // Método para deletar uma movimentação
    public function delete()
    {
        $query = "DELETE FROM Movimentacao WHERE ID_Movimentacao = :id_movimentacao";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id_movimentacao', $this->id_movimentacao);
        return $stmt->execute();
    }
}
