<?php
class Cartao {
    private $conn;
    private $table_name = "Cartao";

    public $ID_Cartao;
    public $NS_Cartao;
    public $ID_Pessoa;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Criar um novo cartão
    public function criar() {
        $query = "INSERT INTO " . $this->table_name . " (NS_Cartao, ID_Pessoa) VALUES (:NS_Cartao, :ID_Pessoa)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':NS_Cartao', $this->NS_Cartao);
        $stmt->bindParam(':ID_Pessoa', $this->ID_Pessoa);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Listar todos os cartões
    public function listar() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    //Listar cartão por ID
    public function listarPorId() {
        $query = "SELECT ID_Cartao, NS_Cartao, ID_Pessoa FROM " . $this->table_name . " WHERE ID_Cartao = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // bind
        $stmt->bindParam(':id', $this->ID_Cartao);
        
        $stmt->execute();
        
        return $stmt;
    }    

    // Atualizar um cartão
    public function atualizar() {
        $query = "UPDATE " . $this->table_name . " SET NS_Cartao = :NS_Cartao, ID_Pessoa = :ID_Pessoa WHERE ID_Cartao = :ID_Cartao";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':NS_Cartao', $this->NS_Cartao);
        $stmt->bindParam(':ID_Pessoa', $this->ID_Pessoa);
        $stmt->bindParam(':ID_Cartao', $this->ID_Cartao);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Verificar cartão (método existente)
    public function verificarCartao() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE NS_Cartao = :NS_Cartao LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':NS_Cartao', $this->NS_Cartao);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return true;
        }

        return false;
    }

    // Deletar um cartão
    public function deletar() {
        $query = "DELETE FROM " . $this->table_name . " WHERE ID_Cartao = :ID_Cartao";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ID_Cartao', $this->ID_Cartao);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}
?>
