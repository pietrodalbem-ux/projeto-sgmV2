-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 17/06/2026 às 13:32
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
-- Banco de dados: `sgm_db`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `ambientes`
--

CREATE TABLE `ambientes` (
  `id_ambiente` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `id_bloco` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `ambientes`
--

INSERT INTO `ambientes` (`id_ambiente`, `nome`, `id_bloco`) VALUES
(1, 'Recepção', 1),
(3, 'Linha 1', 2),
(4, 'Sala de trofeus', 5);

-- --------------------------------------------------------

--
-- Estrutura para tabela `blocos`
--

CREATE TABLE `blocos` (
  `id_bloco` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `blocos`
--

INSERT INTO `blocos` (`id_bloco`, `nome`, `descricao`) VALUES
(1, 'Bloco Administrativo', NULL),
(2, 'Produção', NULL),
(5, 'BatCaverna', 'Escura, muitos morcegos...');

-- --------------------------------------------------------

--
-- Estrutura para tabela `chamados`
--

CREATE TABLE `chamados` (
  `id_chamado` int(11) NOT NULL,
  `descricao_problema` text NOT NULL,
  `data_abertura` datetime DEFAULT current_timestamp(),
  `status` enum('aberto','agendado','em_execucao','concluido','fechado','cancelado') DEFAULT 'aberto',
  `prioridade` enum('baixa','media','alta','urgente') DEFAULT 'baixa',
  `data_previsao_conclusao` date DEFAULT NULL,
  `solucao_tecnica` text DEFAULT NULL,
  `tempo_gasto_minutos` int(11) DEFAULT NULL,
  `data_fechamento` datetime DEFAULT NULL,
  `id_solicitante` int(11) NOT NULL,
  `id_tecnico` int(11) DEFAULT NULL,
  `id_ambiente` int(11) NOT NULL,
  `id_tipo_servico` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `chamados`
--

INSERT INTO `chamados` (`id_chamado`, `descricao_problema`, `data_abertura`, `status`, `prioridade`, `data_previsao_conclusao`, `solucao_tecnica`, `tempo_gasto_minutos`, `data_fechamento`, `id_solicitante`, `id_tecnico`, `id_ambiente`, `id_tipo_servico`) VALUES
(3, 'Pode não man', '2026-05-13 11:22:50', 'em_execucao', 'alta', '2026-05-14', NULL, NULL, NULL, 5, 6, 1, 2),
(4, 'asa', '2026-05-13 11:37:45', 'em_execucao', 'alta', '5000-06-14', NULL, NULL, NULL, 3, 7, 1, 2),
(5, 'Pode não man, a eletrica esta com problema de ligar', '2026-05-13 13:35:08', 'concluido', 'alta', '2008-06-14', NULL, NULL, NULL, 5, 2, 3, 1),
(6, 'O morcego fugiu, e destruiu minha privada, se vira men, e salva nois chefe', '2026-05-13 14:00:51', 'agendado', 'media', '1999-01-04', NULL, NULL, NULL, 9, 10, 4, 1),
(7, 'Houve uma queda de agua no meio do corredor', '2026-05-29 11:28:24', 'agendado', 'alta', '2008-06-15', NULL, NULL, NULL, 3, 2, 3, 2),
(8, 'Ta tudo quebrado, nada funciona', '2026-06-10 08:13:10', 'agendado', 'alta', '2026-06-10', NULL, NULL, NULL, 3, 7, 4, 3),
(9, 'Houve um vazamento nos canos', '2026-06-10 08:17:08', 'aberto', 'baixa', NULL, NULL, NULL, NULL, 5, NULL, 3, 2),
(10, 'ffhsjdfs', '2026-06-17 08:23:44', 'aberto', 'baixa', NULL, NULL, NULL, NULL, 3, NULL, 3, 2);

-- --------------------------------------------------------

--
-- Estrutura para tabela `chamados_anexos`
--

CREATE TABLE `chamados_anexos` (
  `id_anexo` int(11) NOT NULL,
  `caminho_arquivo` varchar(255) NOT NULL,
  `tipo_anexo` enum('abertura','conclusao') NOT NULL,
  `data_upload` datetime DEFAULT current_timestamp(),
  `id_chamado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `chamados_anexos`
--

INSERT INTO `chamados_anexos` (`id_anexo`, `caminho_arquivo`, `tipo_anexo`, `data_upload`, `id_chamado`) VALUES
(2, 'assets/uploads/abertura_6a04893a96fbf.png', 'abertura', '2026-05-13 11:22:50', 3),
(3, 'assets/uploads/abertura_6a048cb9efc9e.jpg', 'abertura', '2026-05-13 11:37:45', 4),
(4, 'assets/uploads/abertura_6a04a83cd1452.png', 'abertura', '2026-05-13 13:35:08', 5),
(5, 'assets/uploads/abertura_6a04ae43a3759.jpg', 'abertura', '2026-05-13 14:00:51', 6),
(6, 'assets/uploads/abertura_6a19a2883adc8.gif', 'abertura', '2026-05-29 11:28:24', 7),
(7, 'assets/uploads/abertura_6a2946c6a19d0.jpg', 'abertura', '2026-06-10 08:13:10', 8),
(8, 'assets/uploads/abertura_6a2947b444769.jpg', 'abertura', '2026-06-10 08:17:08', 9),
(9, 'assets/uploads/abertura_6a3283c0c9bf6.jpg', 'abertura', '2026-06-17 08:23:44', 10);

-- --------------------------------------------------------

--
-- Estrutura para tabela `chamados_comentarios`
--

CREATE TABLE `chamados_comentarios` (
  `id_comentario` int(11) NOT NULL,
  `texto` text NOT NULL,
  `data_envio` datetime DEFAULT current_timestamp(),
  `id_chamado` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `caminho_arquivo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `chamados_comentarios`
--

INSERT INTO `chamados_comentarios` (`id_comentario`, `texto`, `data_envio`, `id_chamado`, `id_usuario`, `caminho_arquivo`) VALUES
(8, 'Morcego me mordeu, tenho medo de virar vampiro :(((', '2026-05-13 14:03:20', 6, 10, NULL),
(9, 'Seeeloko', '2026-05-13 14:50:35', 6, 10, NULL),
(10, 'Falta cimento', '2026-05-13 15:39:47', 6, 10, NULL),
(11, 'Oxe, por que tem uma blusa ai?', '2026-05-15 07:42:29', 5, 2, NULL),
(12, 'Muahaahaha', '2026-06-10 08:51:34', 3, 6, NULL),
(13, 'akjasjafsla', '2026-06-10 11:50:31', 3, 6, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `notificacoes`
--

CREATE TABLE `notificacoes` (
  `id_notificacao` int(11) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `mensagem` text NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `lida` tinyint(1) DEFAULT 0,
  `data_criacao` datetime DEFAULT current_timestamp(),
  `id_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `notificacoes`
--

INSERT INTO `notificacoes` (`id_notificacao`, `titulo`, `mensagem`, `link`, `lida`, `data_criacao`, `id_usuario`) VALUES
(2, 'Novo comentário no chamado #1', 'O técnico João Técnico adicionou uma observação: Deu B.O aqui, vo ter que arrumar de novo', 'gestor_detalhes.php?id=1', 0, '2026-05-13 12:06:28', 4),
(4, 'Novo comentário no chamado #2', 'O técnico João Técnico adicionou uma observação: Vem estragdo, restos mortais do nemo', 'gestor_detalhes.php?id=2', 0, '2026-05-13 13:16:05', 4),
(6, 'Novo comentário no chamado #1', 'O técnico João Técnico adicionou uma observação: Acabei de acabar', 'gestor_detalhes.php?id=1', 0, '2026-05-13 13:36:12', 4),
(8, 'Novo comentário no chamado #2', 'O técnico João Técnico adicionou uma observação: Eu precisei reabrir o chamado por conta de um p...', 'gestor_detalhes.php?id=2', 0, '2026-05-13 13:37:35', 4),
(10, 'Novo comentário no chamado #1', 'O técnico João Técnico adicionou uma observação: SEEEEE loko', 'gestor_detalhes.php?id=1', 0, '2026-05-13 13:38:31', 4),
(12, 'Novo comentário no chamado #6', 'O técnico Arfield adicionou uma observação: Morcego me mordeu, tenho medo de virar vampiro ...', 'gestor_detalhes.php?id=6', 0, '2026-05-13 14:03:20', 4),
(13, 'Novo comentário no chamado #6', 'O técnico Arfield adicionou uma observação: Morcego me mordeu, tenho medo de virar vampiro ...', 'gestor_detalhes.php?id=6', 1, '2026-05-13 14:03:20', 8),
(14, 'Novo comentário no chamado #6', 'O técnico Arfield adicionou uma observação: Seeeloko', 'gestor_detalhes.php?id=6', 1, '2026-05-13 14:50:35', 1),
(15, 'Novo comentário no chamado #6', 'O técnico Arfield adicionou uma observação: Seeeloko', 'gestor_detalhes.php?id=6', 0, '2026-05-13 14:50:35', 4),
(16, 'Novo comentário no chamado #6', 'O técnico Arfield adicionou uma observação: Seeeloko', 'gestor_detalhes.php?id=6', 1, '2026-05-13 14:50:35', 8),
(17, 'Novo comentário no chamado #6', 'O técnico Arfield adicionou uma observação: Falta cimento', 'gestor_detalhes.php?id=6', 1, '2026-05-13 15:39:47', 1),
(18, 'Novo comentário no chamado #6', 'O técnico Arfield adicionou uma observação: Falta cimento', 'gestor_detalhes.php?id=6', 0, '2026-05-13 15:39:47', 4),
(19, 'Novo comentário no chamado #6', 'O técnico Arfield adicionou uma observação: Falta cimento', 'gestor_detalhes.php?id=6', 1, '2026-05-13 15:39:47', 8),
(20, 'Novo comentário no chamado #5', 'O técnico João Técnico adicionou uma observação: Oxe, por que tem uma blusa ai?', 'gestor_detalhes.php?id=5', 1, '2026-05-15 07:42:29', 1),
(21, 'Novo comentário no chamado #5', 'O técnico João Técnico adicionou uma observação: Oxe, por que tem uma blusa ai?', 'gestor_detalhes.php?id=5', 0, '2026-05-15 07:42:29', 4),
(22, 'Novo comentário no chamado #5', 'O técnico João Técnico adicionou uma observação: Oxe, por que tem uma blusa ai?', 'gestor_detalhes.php?id=5', 0, '2026-05-15 07:42:29', 8),
(23, 'Novo comentário no chamado #3', 'O técnico Antonela adicionou uma observação: Muahaahaha', 'gestor_detalhes.php?id=3', 1, '2026-06-10 08:51:34', 1),
(24, 'Novo comentário no chamado #3', 'O técnico Antonela adicionou uma observação: Muahaahaha', 'gestor_detalhes.php?id=3', 0, '2026-06-10 08:51:34', 4),
(25, 'Novo comentário no chamado #3', 'O técnico Antonela adicionou uma observação: Muahaahaha', 'gestor_detalhes.php?id=3', 0, '2026-06-10 08:51:34', 8),
(26, 'Novo comentário no chamado #3', 'O técnico Antonela registrou uma atualização.', 'gestor_detalhes.php?id=3', 1, '2026-06-10 11:50:31', 1),
(27, 'Novo comentário no chamado #3', 'O técnico Antonela registrou uma atualização.', 'gestor_detalhes.php?id=3', 0, '2026-06-10 11:50:31', 4),
(28, 'Novo comentário no chamado #3', 'O técnico Antonela registrou uma atualização.', 'gestor_detalhes.php?id=3', 0, '2026-06-10 11:50:31', 8);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipos_servico`
--

CREATE TABLE `tipos_servico` (
  `id_tipo` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `descricao` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `tipos_servico`
--

INSERT INTO `tipos_servico` (`id_tipo`, `nome`, `descricao`) VALUES
(1, 'Elétrica', NULL),
(2, 'Hidráulica', NULL),
(3, 'Ar Condicionado', NULL),
(4, 'Civil/Predial', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha_hash` varchar(255) NOT NULL,
  `perfil` enum('solicitante','tecnico','gestor') NOT NULL DEFAULT 'solicitante',
  `ativo` tinyint(1) DEFAULT 1,
  `data_criacao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nome`, `email`, `senha_hash`, `perfil`, `ativo`, `data_criacao`) VALUES
(1, 'Chefão supremo', 'admin@sgm.com', '$2y$10$4BpefHZsOuZOFdmDq5mlLunStxkF9xpkjB12ei7rtQQWoWlMjldhi', 'gestor', 1, '2026-02-04 11:41:18'),
(2, 'João Técnico', 'tecnico@sgm.com', '$2y$10$OWj1KJGo9RJzCHaPsY40fOifXqDfA7pYlj/1nAe5b7bpQaKhPjxCK', 'tecnico', 1, '2026-02-04 11:41:18'),
(3, 'Maria Solicitante', 'usuario@sgm.com', '$2y$10$OWj1KJGo9RJzCHaPsY40fOifXqDfA7pYlj/1nAe5b7bpQaKhPjxCK', 'solicitante', 1, '2026-02-04 11:41:18'),
(4, 'Hugo', 'hugo@sgm.com', '$2y$10$OWj1KJGo9RJzCHaPsY40fOifXqDfA7pYlj/1nAe5b7bpQaKhPjxCK', 'gestor', 1, '2026-02-06 07:24:26'),
(5, 'Yasmin Gabriélli da Come Silva', 'yasmin@sgm.com', '$2y$10$jBa8aRNToArRslWQFRDNcu5teLUc9Za46e3fxScUvB8V4QhARgLLK', 'solicitante', 1, '2026-05-13 11:21:58'),
(6, 'Antonela', 'antonela@sgm.com', '$2y$10$ls5afyyJJVDvo0x/WROQLeqNQyuMa7j4Ygr0VP3fgAkuoVM7Sey0e', 'tecnico', 1, '2026-05-13 11:23:49'),
(7, 'Malevola', 'malemale@sgm.com', '$2y$10$l34iZfCLUOhkzIt.455UUeV2caq4XiUdqr0IZjaccivn35fVagap2', 'tecnico', 1, '2026-05-13 11:30:20'),
(8, 'Bruce', 'batman@wayne.com', '$2y$10$JWZt6ofXJf5gJLB3yKGO.OANhmYTtLVd1einNlkePDe.VxEkEuQby', 'gestor', 1, '2026-05-13 13:44:21'),
(9, 'Robin Hood', 'robin@azul.com', '$2y$10$OVeZ5zv39YwwvL0UzbxDLewRSB8ZXpzxS3Jomz7LHbPutOH33PxxO', 'solicitante', 1, '2026-05-13 13:58:35'),
(10, 'Arfield', 'alfredo@sgm.com', '$2y$10$81zbO5GyaOaeTIG3O0u5rertyWBwIzDmq.eCk6PjMvHCMFZDi9g3a', 'tecnico', 1, '2026-05-13 14:01:29');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `ambientes`
--
ALTER TABLE `ambientes`
  ADD PRIMARY KEY (`id_ambiente`),
  ADD KEY `fk_ambientes_blocos` (`id_bloco`);

--
-- Índices de tabela `blocos`
--
ALTER TABLE `blocos`
  ADD PRIMARY KEY (`id_bloco`);

--
-- Índices de tabela `chamados`
--
ALTER TABLE `chamados`
  ADD PRIMARY KEY (`id_chamado`),
  ADD KEY `fk_chamados_solicitante` (`id_solicitante`),
  ADD KEY `fk_chamados_tecnico` (`id_tecnico`),
  ADD KEY `fk_chamados_ambiente` (`id_ambiente`),
  ADD KEY `fk_chamados_tipo` (`id_tipo_servico`);

--
-- Índices de tabela `chamados_anexos`
--
ALTER TABLE `chamados_anexos`
  ADD PRIMARY KEY (`id_anexo`),
  ADD KEY `fk_anexos_chamados` (`id_chamado`);

--
-- Índices de tabela `chamados_comentarios`
--
ALTER TABLE `chamados_comentarios`
  ADD PRIMARY KEY (`id_comentario`),
  ADD KEY `fk_comentarios_chamado` (`id_chamado`),
  ADD KEY `fk_comentarios_usuario` (`id_usuario`);

--
-- Índices de tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD PRIMARY KEY (`id_notificacao`),
  ADD KEY `fk_notificacoes_usuario` (`id_usuario`);

--
-- Índices de tabela `tipos_servico`
--
ALTER TABLE `tipos_servico`
  ADD PRIMARY KEY (`id_tipo`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `idx_email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `ambientes`
--
ALTER TABLE `ambientes`
  MODIFY `id_ambiente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `blocos`
--
ALTER TABLE `blocos`
  MODIFY `id_bloco` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `chamados`
--
ALTER TABLE `chamados`
  MODIFY `id_chamado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `chamados_anexos`
--
ALTER TABLE `chamados_anexos`
  MODIFY `id_anexo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `chamados_comentarios`
--
ALTER TABLE `chamados_comentarios`
  MODIFY `id_comentario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  MODIFY `id_notificacao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de tabela `tipos_servico`
--
ALTER TABLE `tipos_servico`
  MODIFY `id_tipo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `ambientes`
--
ALTER TABLE `ambientes`
  ADD CONSTRAINT `fk_ambientes_blocos` FOREIGN KEY (`id_bloco`) REFERENCES `blocos` (`id_bloco`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `chamados`
--
ALTER TABLE `chamados`
  ADD CONSTRAINT `fk_chamados_ambiente` FOREIGN KEY (`id_ambiente`) REFERENCES `ambientes` (`id_ambiente`),
  ADD CONSTRAINT `fk_chamados_solicitante` FOREIGN KEY (`id_solicitante`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `fk_chamados_tecnico` FOREIGN KEY (`id_tecnico`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_chamados_tipo` FOREIGN KEY (`id_tipo_servico`) REFERENCES `tipos_servico` (`id_tipo`);

--
-- Restrições para tabelas `chamados_anexos`
--
ALTER TABLE `chamados_anexos`
  ADD CONSTRAINT `fk_anexos_chamados` FOREIGN KEY (`id_chamado`) REFERENCES `chamados` (`id_chamado`) ON DELETE CASCADE;

--
-- Restrições para tabelas `chamados_comentarios`
--
ALTER TABLE `chamados_comentarios`
  ADD CONSTRAINT `fk_comentarios_chamado` FOREIGN KEY (`id_chamado`) REFERENCES `chamados` (`id_chamado`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_comentarios_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Restrições para tabelas `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD CONSTRAINT `fk_notificacoes_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
