<?php
class Pessoa
{
    private $conn;
    private $table_name = "Pessoa";

    public $ID_Pessoa;
    public $Nome_Pessoa;
    public $Telefone;
    public $Email;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Listar todas as pessoas
    public function listar()
    {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Obter uma pessoa por ID
    public function obterPorId()
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE ID_Pessoa = :ID_Pessoa";
        $stmt = $this->conn->prepare($query);

        // Bind do parâmetro
        $stmt->bindParam(':ID_Pessoa', $this->ID_Pessoa);
        $stmt->execute();

        return $stmt;
    }

    // Criar uma nova pessoa
    public function criar()
    {
        $query = "INSERT INTO " . $this->table_name . " (Nome_Pessoa, Telefone, Email) VALUES (:Nome_Pessoa, :Telefone, :Email)";
        $stmt = $this->conn->prepare($query);

        // Sanitização dos dados
        $this->Nome_Pessoa = htmlspecialchars(strip_tags($this->Nome_Pessoa));
        $this->Telefone = htmlspecialchars(strip_tags($this->Telefone));
        $this->Email = htmlspecialchars(strip_tags($this->Email));

        // Bind dos parâmetros
        $stmt->bindParam(':Nome_Pessoa', $this->Nome_Pessoa);
        $stmt->bindParam(':Telefone', $this->Telefone);
        $stmt->bindParam(':Email', $this->Email);

        if ($stmt->execute()) {
            $this->ID_Pessoa = $this->conn->lastInsertId();

            return true;
        }
        return false;
    }

    // Atualizar uma pessoa existente
    public function atualizar()
    {
        $query = "UPDATE " . $this->table_name . " SET Nome_Pessoa = :Nome_Pessoa, Telefone = :Telefone, Email = :Email WHERE ID_Pessoa = :ID_Pessoa";
        $stmt = $this->conn->prepare($query);

        // Sanitização dos dados
        $this->Nome_Pessoa = htmlspecialchars(strip_tags($this->Nome_Pessoa));
        $this->Telefone = htmlspecialchars(strip_tags($this->Telefone));
        $this->Email = htmlspecialchars(strip_tags($this->Email));
        $this->ID_Pessoa = htmlspecialchars(strip_tags($this->ID_Pessoa));

        // Bind dos parâmetros
        $stmt->bindParam(':Nome_Pessoa', $this->Nome_Pessoa);
        $stmt->bindParam(':Telefone', $this->Telefone);
        $stmt->bindParam(':Email', $this->Email);
        $stmt->bindParam(':ID_Pessoa', $this->ID_Pessoa);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Deletar uma pessoa
    public function deletar()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE ID_Pessoa = :ID_Pessoa";
        $stmt = $this->conn->prepare($query);

        // Sanitização dos dados
        $this->ID_Pessoa = htmlspecialchars(strip_tags($this->ID_Pessoa));

        // Bind do parâmetro
        $stmt->bindParam(':ID_Pessoa', $this->ID_Pessoa);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
