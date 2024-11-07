<?php
class Cartao
{
    private $conn;
    private $table_name = "Cartao";

    public $ID_Cartao;
    public $Nome_Cartao;
    public $NS_Cartao;
    public $ID_Pessoa;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Criar um novo cartão
    public function criar()
    {
        $query = "INSERT INTO " . $this->table_name . " (Nome_Cartao, NS_Cartao) VALUES (:Nome_Cartao, :NS_Cartao)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':Nome_Cartao', $this->Nome_Cartao);
        $stmt->bindParam(':NS_Cartao', $this->NS_Cartao, PDO::PARAM_NULL);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Método para associar o cartão à pessoa
    public function associarPessoa($id_cartao, $id_pessoa)
    {
        // Atualizar a tabela Pessoa com o ID_Cartao correspondente
        $query = "UPDATE Pessoa SET ID_Cartao = :ID_Cartao WHERE ID_Pessoa = :ID_Pessoa";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':ID_Cartao', $id_cartao);
        $stmt->bindParam(':ID_Pessoa', $id_pessoa);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Listar os cartões
    public function listar()
    {
        $query = "SELECT ID_Cartao, Nome_Cartao, NS_Cartao FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Listar os cartões pelo ID
    public function listarPorId()
    {
        $query = "SELECT ID_Cartao, Nome_Cartao, NS_Cartao FROM " . $this->table_name . " WHERE ID_Cartao = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $this->ID_Cartao);
        $stmt->execute();

        return $stmt;
    }

    // Atualizar um cartão
    public function atualizar()
    {
        $query = "UPDATE " . $this->table_name . " SET Nome_Cartao = :Nome_Cartao, NS_Cartao = :NS_Cartao WHERE ID_Cartao = :ID_Cartao";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':Nome_Cartao', $this->Nome_Cartao);
        $stmt->bindParam(':NS_Cartao', $this->NS_Cartao);
        $stmt->bindParam(':ID_Cartao', $this->ID_Cartao);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Verificar cartão (método existente)
    public function verificarCartao()
    {
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
    public function deletar()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE ID_Cartao = :ID_Cartao";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ID_Cartao', $this->ID_Cartao);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Buscar um cartão com NS vazio
    public function buscarPorCartaoNulo() {
        $query = "SELECT * FROM Cartao WHERE NS_Cartao IS NULL ORDER BY ID_Cartao DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
