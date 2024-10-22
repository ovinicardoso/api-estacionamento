<?php
class Vaga {
    private $conn;
    private $table_name = "Vaga";

    public $ID_Vaga;
    public $Nome_Vaga;
    public $Ocupado;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Método para criar uma nova vaga
    public function criar() {
        $query = "INSERT INTO " . $this->table_name . " (Nome_Vaga, Ocupado) VALUES (:Nome_Vaga, :Ocupado)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':Nome_Vaga', $this->Nome_Vaga);
        $stmt->bindParam(':Ocupado', $this->Ocupado);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Método para listar todas as vagas
    public function listar() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    // Método para atualizar uma vaga
    public function atualizarStatus($ID_Vaga, $Ocupado) {
        $query = "UPDATE " . $this->table_name . " SET Ocupado = :Ocupado WHERE ID_Vaga = :ID_Vaga";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':Ocupado', $Ocupado);
        $stmt->bindParam(':ID_Vaga', $ID_Vaga);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Método para deletar uma vaga
    public function deletar() {
        $query = "DELETE FROM " . $this->table_name . " WHERE ID_Vaga = :ID_Vaga";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ID_Vaga', $this->ID_Vaga);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}
?>
