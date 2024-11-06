-- Criação da tabela de cartões
CREATE TABLE Cartao (
    ID_Cartao INT AUTO_INCREMENT PRIMARY KEY,
    Nome_Cartao VARCHAR(20),
    NS_Cartao VARCHAR(20) UNIQUE
);

-- Criação da tabela de pessoas
CREATE TABLE Pessoa (
    ID_Pessoa INT AUTO_INCREMENT PRIMARY KEY,
    Nome_Pessoa VARCHAR(100) NOT NULL,
    Telefone VARCHAR(15) NOT NULL,
    Email VARCHAR(100) NOT NULL,
    ID_Cartao INT,
    FOREIGN KEY (ID_Cartao) REFERENCES Cartao(ID_Cartao) ON DELETE SET NULL
);

-- Criação da tabela de usuários
CREATE TABLE Usuario (
    ID_Usuario INT AUTO_INCREMENT PRIMARY KEY,
    Nome_Usuario VARCHAR(100) NOT NULL,
    Email VARCHAR(100) NOT NULL,
    Senha VARCHAR(100) NOT NULL
);

-- Criação da tabela de vagas
CREATE TABLE Vaga (
    ID_Vaga INT AUTO_INCREMENT PRIMARY KEY,
    Nome_Vaga VARCHAR(30) NOT NULL,
    Ocupado TINYINT(1) NOT NULL
);

-- Criação da tabela de movimentações
CREATE TABLE Movimentacao (
    ID_Movimentacao INT AUTO_INCREMENT PRIMARY KEY,
    Hora_Entrada DATETIME NOT NULL,
    Hora_Saida DATETIME DEFAULT NULL,
    ID_Cartao INT NOT NULL,
    ID_Vaga INT DEFAULT NULL,
    FOREIGN KEY (ID_Cartao) REFERENCES Cartao(ID_Cartao),
    FOREIGN KEY (ID_Vaga) REFERENCES Vaga(ID_Vaga) 
);

--Inserção de dados teste
INSERT INTO Cartao (Nome_Cartao, NS_Cartao) VALUES
('Cartão Branco', 'A1 B2 C3 D4'),
('Cartão Azul', 'A1 B7 D9 G4');
INSERT INTO Pessoa (Nome_Pessoa, Telefone, Email, ID_Cartao) VALUES
('João Silva', '11987654321', 'joao.silva@email.com', 1),
('Maria Oliveira', '11987654322', 'maria.oliveira@email.com', 2);
INSERT INTO Vaga (Nome_Vaga, Ocupado) VALUES
('Vaga A', 0),
('Vaga B', 0),
('Vaga C', 0);
INSERT INTO Movimentacao (Hora_Entrada, Hora_Saida, ID_Cartao, ID_Vaga) VALUES
('2024-11-06 08:00:00', '2024-11-06 18:00:00', 1, 1),
('2024-11-06 09:00:00', '2024-11-06 17:00:00', 2, 2),
('2024-11-06 10:00:00', NULL, 3, NULL);


