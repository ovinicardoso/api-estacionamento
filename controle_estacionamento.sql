-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 10/11/2024 às 18:18
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `controle_estacionamento`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `Cartao`
--

CREATE TABLE `Cartao` (
  `ID_Cartao` int(11) NOT NULL,
  `Nome_Cartao` varchar(20) DEFAULT NULL,
  `NS_Cartao` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `Cartao`
--

INSERT INTO `Cartao` (`ID_Cartao`, `Nome_Cartao`, `NS_Cartao`) VALUES
(1, 'Tag', '9B 88 FF 00');

-- --------------------------------------------------------

--
-- Estrutura para tabela `Movimentacao`
--

CREATE TABLE `Movimentacao` (
  `ID_Movimentacao` int(11) NOT NULL,
  `Hora_Entrada` datetime NOT NULL,
  `Hora_Saida` datetime DEFAULT NULL,
  `ID_Cartao` int(11) DEFAULT NULL,
  `ID_Vaga` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `Movimentacao`
--

INSERT INTO `Movimentacao` (`ID_Movimentacao`, `Hora_Entrada`, `Hora_Saida`, `ID_Cartao`, `ID_Vaga`) VALUES
(1, '2024-11-10 14:17:15', '2024-11-10 14:17:20', NULL, NULL),
(2, '2024-11-10 14:17:33', '2024-11-10 14:17:38', 1, NULL),
(3, '2024-11-10 14:17:42', '2024-11-10 14:18:09', 1, 1),
(4, '2024-11-10 14:17:52', '2024-11-10 14:18:05', NULL, 2);

-- --------------------------------------------------------

--
-- Estrutura para tabela `Pessoa`
--

CREATE TABLE `Pessoa` (
  `ID_Pessoa` int(11) NOT NULL,
  `Nome_Pessoa` varchar(100) NOT NULL,
  `Telefone` varchar(15) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `ID_Cartao` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `Usuario`
--

CREATE TABLE `Usuario` (
  `ID_Usuario` int(11) NOT NULL,
  `Nome_Usuario` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Senha` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `Vaga`
--

CREATE TABLE `Vaga` (
  `ID_Vaga` int(11) NOT NULL,
  `Nome_Vaga` varchar(30) NOT NULL,
  `Ocupado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `Vaga`
--

INSERT INTO `Vaga` (`ID_Vaga`, `Nome_Vaga`, `Ocupado`) VALUES
(1, 'Vaga1', 0),
(2, 'Vaga2', 0),
(3, 'Vaga3', 0);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `Cartao`
--
ALTER TABLE `Cartao`
  ADD PRIMARY KEY (`ID_Cartao`),
  ADD UNIQUE KEY `NS_Cartao` (`NS_Cartao`);

--
-- Índices de tabela `Movimentacao`
--
ALTER TABLE `Movimentacao`
  ADD PRIMARY KEY (`ID_Movimentacao`),
  ADD KEY `ID_Vaga` (`ID_Vaga`),
  ADD KEY `ID_Cartao` (`ID_Cartao`);

--
-- Índices de tabela `Pessoa`
--
ALTER TABLE `Pessoa`
  ADD PRIMARY KEY (`ID_Pessoa`),
  ADD KEY `ID_Cartao` (`ID_Cartao`);

--
-- Índices de tabela `Usuario`
--
ALTER TABLE `Usuario`
  ADD PRIMARY KEY (`ID_Usuario`);

--
-- Índices de tabela `Vaga`
--
ALTER TABLE `Vaga`
  ADD PRIMARY KEY (`ID_Vaga`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `Cartao`
--
ALTER TABLE `Cartao`
  MODIFY `ID_Cartao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `Movimentacao`
--
ALTER TABLE `Movimentacao`
  MODIFY `ID_Movimentacao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `Pessoa`
--
ALTER TABLE `Pessoa`
  MODIFY `ID_Pessoa` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `Usuario`
--
ALTER TABLE `Usuario`
  MODIFY `ID_Usuario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `Vaga`
--
ALTER TABLE `Vaga`
  MODIFY `ID_Vaga` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `Movimentacao`
--
ALTER TABLE `Movimentacao`
  ADD CONSTRAINT `Movimentacao_ibfk_1` FOREIGN KEY (`ID_Vaga`) REFERENCES `Vaga` (`ID_Vaga`),
  ADD CONSTRAINT `Movimentacao_ibfk_2` FOREIGN KEY (`ID_Cartao`) REFERENCES `Cartao` (`ID_Cartao`) ON DELETE SET NULL;

--
-- Restrições para tabelas `Pessoa`
--
ALTER TABLE `Pessoa`
  ADD CONSTRAINT `Pessoa_ibfk_1` FOREIGN KEY (`ID_Cartao`) REFERENCES `Cartao` (`ID_Cartao`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
