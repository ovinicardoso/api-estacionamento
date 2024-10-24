<?php
class Movimentacao {
    private $conn;

    public $id_movimentacao;
    public $hora_entrada;
    public $hora_saida;
    public $id_cartao;
    public $id_vaga;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Método para criar uma movimentação
    public function create() {
        $query = "INSERT INTO Movimentacao (Hora_Entrada, Hora_Saida, ID_Cartao, ID_Vaga) VALUES (:hora_entrada, :hora_saida, :id_cartao, :id_vaga)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':hora_entrada', $this->hora_entrada);
        $stmt->bindParam(':hora_saida', $this->hora_saida);
        $stmt->bindParam(':id_cartao', $this->id_cartao);
        $stmt->bindParam(':id_vaga', $this->id_vaga);

        return $stmt->execute();
    }

    // Método para ler todas as movimentações
    public function readAll() {
        $query = "SELECT * FROM Movimentacao";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Método para ler uma movimentação específica
    public function read() {
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

    // Método para atualizar uma movimentação
    public function update() {
        $query = "UPDATE Movimentacao SET Hora_Entrada = :hora_entrada, Hora_Saida = :hora_saida, ID_Cartao = :id_cartao, ID_Vaga = :id_vaga WHERE ID_Movimentacao = :id_movimentacao";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id_movimentacao', $this->id_movimentacao);
        $stmt->bindParam(':hora_entrada', $this->hora_entrada);
        $stmt->bindParam(':hora_saida', $this->hora_saida);
        $stmt->bindParam(':id_cartao', $this->id_cartao);
        $stmt->bindParam(':id_vaga', $this->id_vaga);

        return $stmt->execute();
    }

    // Método para deletar uma movimentação
    public function delete() {
        $query = "DELETE FROM Movimentacao WHERE ID_Movimentacao = :id_movimentacao";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id_movimentacao', $this->id_movimentacao);
        return $stmt->execute();
    }
}
?>
