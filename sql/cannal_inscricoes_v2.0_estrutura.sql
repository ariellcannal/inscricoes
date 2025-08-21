-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 27/05/2025 às 10:32
-- Versão do servidor: 8.0.42
-- Versão do PHP: 8.3.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `cannal_inscricoes`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `alunos`
--

CREATE TABLE `alunos` (
  `alu_id` int NOT NULL,
  `alu_nome` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `alu_nomeArtistico` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `alu_cpf` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alu_nascimento` date DEFAULT NULL,
  `alu_drt` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alu_email` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `alu_celular` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `alu_pagarmeId` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alu_endereco` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alu_enderecoNumero` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alu_enderecoComplemento` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alu_enderecoBairro` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alu_enderecoCidade` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alu_enderecoEstado` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alu_enderecoCep` varchar(9) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alu_cv` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alu_foto` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alu_criacao` datetime DEFAULT NULL,
  `alu_modificacao` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `alunos_creditos`
--

CREATE TABLE `alunos_creditos` (
  `alc_id` int NOT NULL,
  `alc_aluno` int NOT NULL,
  `alc_inscricao` int DEFAULT NULL,
  `alc_valorInicial` decimal(10,2) NOT NULL,
  `alc_valorUtilizado` decimal(10,2) DEFAULT NULL,
  `alc_motivo` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `alunos_operadoras_meta`
--

CREATE TABLE `alunos_operadoras_meta` (
  `aop_id` int NOT NULL,
  `aop_aluno` int NOT NULL,
  `aop_operadora` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `aop_key` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `aop_value` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `aop_environment` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `feriados`
--

CREATE TABLE `feriados` (
  `fer_id` int NOT NULL,
  `fer_data` date NOT NULL,
  `fer_nome` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fer_link` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fer_tipo` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fer_tipoCodigo` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fer_descricao` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `grupos`
--

CREATE TABLE `grupos` (
  `grp_id` int NOT NULL,
  `grp_nome` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `grp_slug` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `grp_nomePublico` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `grp_drtObrigatorio` tinyint(1) NOT NULL DEFAULT '1',
  `grp_dataAulaAberta` date DEFAULT NULL,
  `grp_dataInicio` date NOT NULL,
  `grp_dataFim` date DEFAULT NULL,
  `grp_diaSemana` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `grp_horaInicio` time NOT NULL,
  `grp_horaFim` time NOT NULL,
  `grp_horario` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grp_dias` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grp_encontros` int DEFAULT NULL,
  `grp_maximoInscricoes` int DEFAULT NULL,
  `grp_descricao` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `grp_descricaoDetalhes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `grp_coordenadores` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `grp_valor` decimal(10,2) NOT NULL,
  `grp_valorDescricao` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `grp_imagem` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grp_exibeSite` tinyint(1) NOT NULL DEFAULT '1',
  `grp_inscricoesAbertas` tinyint(1) NOT NULL DEFAULT '0',
  `grp_processoSeletivo` tinyint(1) NOT NULL DEFAULT '0',
  `grp_ativo` tinyint(1) NOT NULL DEFAULT '0',
  `grp_repasseAtivado` tinyint(1) NOT NULL DEFAULT '0',
  `grp_atualizacao` datetime DEFAULT NULL,
  `grp_linkWhats` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `grp_idFaturaCartao` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grp_operadora` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `grupos_datas`
--

CREATE TABLE `grupos_datas` (
  `grd_grupo` int NOT NULL,
  `grd_data` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `grupos_distribuicao`
--

CREATE TABLE `grupos_distribuicao` (
  `dst_id` int NOT NULL,
  `dst_grupo` int NOT NULL,
  `dst_usuario` int NOT NULL,
  `dst_porcentagem` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `grupos_formas`
--

CREATE TABLE `grupos_formas` (
  `gfp_id` int NOT NULL,
  `gfp_grupo` int NOT NULL,
  `gfp_parcelas` int NOT NULL,
  `gfp_valorTotal` decimal(10,2) NOT NULL,
  `gfp_ordem` int NOT NULL DEFAULT '0',
  `gfp_publico` tinyint(1) NOT NULL DEFAULT '1',
  `gfp_aceitaCartao` tinyint(1) NOT NULL,
  `gfp_linkOculto` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gfp_linkOcultoValidade` datetime DEFAULT NULL,
  `gfp_comentario` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gfp_descricao` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `inscricoes`
--

CREATE TABLE `inscricoes` (
  `ins_id` int NOT NULL,
  `ins_grupo` int NOT NULL,
  `ins_aluno` int NOT NULL,
  `ins_status` enum('Confirmada','Pendente','Devedora','Cancelada') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pendente',
  `ins_valorModulo` float DEFAULT NULL,
  `ins_valorDesconto` float DEFAULT '0',
  `ins_motivoDesconto` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ins_valorTotalPago` float DEFAULT NULL,
  `ins_valorDevido` float DEFAULT NULL,
  `ins_comentario` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ins_tempData` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ins_aprovada` datetime DEFAULT NULL,
  `ins_IP` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ins_forma` int DEFAULT NULL,
  `ins_data` datetime NOT NULL,
  `ins_user` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `operadoras`
--

CREATE TABLE `operadoras` (
  `opr_nome` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `opr_productionKey` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `opr_developmentKey` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `opr_interface` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `opr_default` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `operadoras_formas`
--

CREATE TABLE `operadoras_formas` (
  `ofo_forma` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ofo_operadora` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ofo_parcelamento` tinyint(1) NOT NULL,
  `ofo_antecipacao` tinyint(1) DEFAULT NULL,
  `ofo_prazoEstornoTaxa` int NOT NULL DEFAULT '0',
  `ofo_custoFixo` double(10,2) DEFAULT NULL,
  `ofo_taxa` double(10,2) DEFAULT NULL,
  `ofo_taxaParcelamento23` double(10,2) DEFAULT NULL,
  `ofo_taxaParcelamento46` double(10,2) DEFAULT NULL,
  `ofo_taxaParcelamento712` double(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `operadoras_transacoes`
--

CREATE TABLE `operadoras_transacoes` (
  `otr_id` int NOT NULL,
  `otr_inscricao` int DEFAULT NULL,
  `otr_forma` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `otr_tipo` enum('pix','cartao') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `otr_cartao` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `otr_parcelas` int DEFAULT '1',
  `otr_valorBruto` decimal(10,2) NOT NULL,
  `otr_valorLiquido` decimal(10,2) NOT NULL,
  `otr_valorCancelado` decimal(10,2) DEFAULT NULL,
  `otr_dataCancelamento` datetime DEFAULT NULL,
  `otr_dataTransacao` datetime NOT NULL,
  `otr_dataExpiracao` datetime DEFAULT NULL,
  `otr_pixQrCode` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `otr_pixQrCodeUrl` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `otr_confirmada` tinyint(1) DEFAULT NULL,
  `otr_descricaoFatura` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `otr_operadora` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `otr_operadoraResposta` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `otr_operadoraErros` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `otr_operadoraStatus` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `otr_operadoraID` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `otr_operadoraData` datetime DEFAULT NULL,
  `otr_tid` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `otr_nsu` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `otr_authorizationCode` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `otr_cardBin` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `otr_cardLast4` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `otr_criacao` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `operadoras_transacoes_estornos`
--

CREATE TABLE `operadoras_transacoes_estornos` (
  `tes_id` int NOT NULL,
  `tes_transacao` int NOT NULL,
  `tes_valor` decimal(10,2) NOT NULL,
  `tes_criacao` datetime NOT NULL,
  `tes_returnCode` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tes_returnMessage` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tes_tid` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tes_nsu` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tes_refundId` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tes_refundDateTime` datetime DEFAULT NULL,
  `tes_cancelId` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tes_operadoraID` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tes_operadoraStatus` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tes_operadoraResposta` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `presenca`
--

CREATE TABLE `presenca` (
  `prs_id` int NOT NULL,
  `prs_data` date NOT NULL,
  `prs_dataAula` date DEFAULT NULL,
  `prs_grupo` int NOT NULL,
  `prs_aluno` int NOT NULL,
  `prs_criador` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `recebiveis`
--

CREATE TABLE `recebiveis` (
  `rec_id` int NOT NULL,
  `rec_inscricao` int NOT NULL,
  `rec_creditoUtilizado` int DEFAULT NULL,
  `rec_valor` decimal(10,2) NOT NULL,
  `rec_valorLiquido` decimal(10,2) DEFAULT NULL,
  `rec_estornoValor` decimal(10,2) DEFAULT NULL,
  `rec_estornoData` datetime DEFAULT NULL,
  `rec_forma` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rec_dataTransacao` datetime NOT NULL,
  `rec_dataRecebimento` date NOT NULL,
  `rec_recebido` tinyint(1) NOT NULL DEFAULT '0',
  `rec_parcela` tinyint DEFAULT NULL,
  `rec_transacao` int DEFAULT NULL,
  `rec_operadora` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rec_operadoraResposta` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `rec_operadoraStatus` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rec_operadoraID` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rec_operadoraData` datetime DEFAULT NULL,
  `rec_criacao` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `recebiveis_repasses`
--

CREATE TABLE `recebiveis_repasses` (
  `rre_id` int NOT NULL,
  `rre_recebivel` int NOT NULL,
  `rre_repasse` int DEFAULT NULL,
  `rre_usuario` int NOT NULL,
  `rre_porcentagemUsuario` decimal(10,2) DEFAULT NULL,
  `rre_valor` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `repasses`
--

CREATE TABLE `repasses` (
  `rep_id` int NOT NULL,
  `rep_usuario` int NOT NULL,
  `rep_valor` decimal(10,2) NOT NULL,
  `rep_data` datetime NOT NULL,
  `rep_efetivado` datetime DEFAULT NULL,
  `rep_comentario` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `usr_id` int NOT NULL,
  `usr_nome` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `usr_senha` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usr_cv` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `usr_foto` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usr_email` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `usr_recebeInscricoes` tinyint(1) NOT NULL DEFAULT '0',
  `usr_coordenador` tinyint(1) NOT NULL DEFAULT '0',
  `usr_recebeRepasse` tinyint(1) NOT NULL DEFAULT '0',
  `usr_alertaRepasse` tinyint(1) NOT NULL DEFAULT '1',
  `usr_chavePIX` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usr_preferencias` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `alunos`
--
ALTER TABLE `alunos`
  ADD PRIMARY KEY (`alu_id`);

--
-- Índices de tabela `alunos_creditos`
--
ALTER TABLE `alunos_creditos`
  ADD PRIMARY KEY (`alc_id`),
  ADD KEY `aluno_credito_idx` (`alc_aluno`),
  ADD KEY `credito_inscricao_idx` (`alc_inscricao`);

--
-- Índices de tabela `alunos_operadoras_meta`
--
ALTER TABLE `alunos_operadoras_meta`
  ADD PRIMARY KEY (`aop_id`),
  ADD UNIQUE KEY `aop_alu_opr_key_env` (`aop_aluno`,`aop_operadora`,`aop_key`,`aop_environment`) USING BTREE,
  ADD KEY `aop_operadora` (`aop_operadora`);

--
-- Índices de tabela `feriados`
--
ALTER TABLE `feriados`
  ADD PRIMARY KEY (`fer_id`);

--
-- Índices de tabela `grupos`
--
ALTER TABLE `grupos`
  ADD PRIMARY KEY (`grp_id`),
  ADD KEY `grp_operadora` (`grp_operadora`);

--
-- Índices de tabela `grupos_datas`
--
ALTER TABLE `grupos_datas`
  ADD PRIMARY KEY (`grd_grupo`,`grd_data`);

--
-- Índices de tabela `grupos_distribuicao`
--
ALTER TABLE `grupos_distribuicao`
  ADD PRIMARY KEY (`dst_id`),
  ADD KEY `grupo_distribuicao_idx` (`dst_grupo`),
  ADD KEY `distribuicao_usuario_idx` (`dst_usuario`);

--
-- Índices de tabela `grupos_formas`
--
ALTER TABLE `grupos_formas`
  ADD PRIMARY KEY (`gfp_id`),
  ADD KEY `grupo_formas_idx` (`gfp_grupo`);

--
-- Índices de tabela `inscricoes`
--
ALTER TABLE `inscricoes`
  ADD PRIMARY KEY (`ins_id`),
  ADD KEY `grupo_inscricao_idx` (`ins_grupo`),
  ADD KEY `grupo_aluno_idx` (`ins_aluno`),
  ADD KEY `inscricao_forma` (`ins_forma`);

--
-- Índices de tabela `operadoras`
--
ALTER TABLE `operadoras`
  ADD PRIMARY KEY (`opr_nome`);

--
-- Índices de tabela `operadoras_formas`
--
ALTER TABLE `operadoras_formas`
  ADD PRIMARY KEY (`ofo_forma`,`ofo_operadora`) USING BTREE,
  ADD KEY `for_operadora` (`ofo_operadora`);

--
-- Índices de tabela `operadoras_transacoes`
--
ALTER TABLE `operadoras_transacoes`
  ADD PRIMARY KEY (`otr_id`),
  ADD KEY `transacao_forma` (`otr_forma`),
  ADD KEY `transacao_operadora` (`otr_operadora`),
  ADD KEY `transacao_inscricao` (`otr_inscricao`);

--
-- Índices de tabela `operadoras_transacoes_estornos`
--
ALTER TABLE `operadoras_transacoes_estornos`
  ADD PRIMARY KEY (`tes_id`),
  ADD KEY `cancelamentos_pagamentos_idx` (`tes_transacao`);

--
-- Índices de tabela `presenca`
--
ALTER TABLE `presenca`
  ADD PRIMARY KEY (`prs_id`),
  ADD KEY `presenca_grupo_idx` (`prs_grupo`),
  ADD KEY `presenca_aluno_idx` (`prs_aluno`);

--
-- Índices de tabela `recebiveis`
--
ALTER TABLE `recebiveis`
  ADD PRIMARY KEY (`rec_id`),
  ADD KEY `pagamentos_inscricao_idx` (`rec_inscricao`),
  ADD KEY `pagamentos_creditos_idx` (`rec_creditoUtilizado`),
  ADD KEY `pagamentos_forma_idx` (`rec_forma`),
  ADD KEY `pagamentos_operadora` (`rec_operadora`),
  ADD KEY `recebivel_transacao` (`rec_transacao`);

--
-- Índices de tabela `recebiveis_repasses`
--
ALTER TABLE `recebiveis_repasses`
  ADD PRIMARY KEY (`rre_id`),
  ADD KEY `repasses_usuario_idx` (`rre_usuario`),
  ADD KEY `pagamentos_repasses_idx` (`rre_recebivel`),
  ADD KEY `recebiveis_repasses_repasse` (`rre_repasse`);

--
-- Índices de tabela `repasses`
--
ALTER TABLE `repasses`
  ADD PRIMARY KEY (`rep_id`),
  ADD KEY `repasses_usuarios_idx` (`rep_usuario`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`usr_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `alunos`
--
ALTER TABLE `alunos`
  MODIFY `alu_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `alunos_creditos`
--
ALTER TABLE `alunos_creditos`
  MODIFY `alc_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `alunos_operadoras_meta`
--
ALTER TABLE `alunos_operadoras_meta`
  MODIFY `aop_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `feriados`
--
ALTER TABLE `feriados`
  MODIFY `fer_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `grupos`
--
ALTER TABLE `grupos`
  MODIFY `grp_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `grupos_distribuicao`
--
ALTER TABLE `grupos_distribuicao`
  MODIFY `dst_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `grupos_formas`
--
ALTER TABLE `grupos_formas`
  MODIFY `gfp_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `inscricoes`
--
ALTER TABLE `inscricoes`
  MODIFY `ins_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `operadoras_transacoes`
--
ALTER TABLE `operadoras_transacoes`
  MODIFY `otr_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `operadoras_transacoes_estornos`
--
ALTER TABLE `operadoras_transacoes_estornos`
  MODIFY `tes_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `presenca`
--
ALTER TABLE `presenca`
  MODIFY `prs_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `recebiveis`
--
ALTER TABLE `recebiveis`
  MODIFY `rec_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `recebiveis_repasses`
--
ALTER TABLE `recebiveis_repasses`
  MODIFY `rre_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `repasses`
--
ALTER TABLE `repasses`
  MODIFY `rep_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `usr_id` int NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `alunos_creditos`
--
ALTER TABLE `alunos_creditos`
  ADD CONSTRAINT `credito_aluno` FOREIGN KEY (`alc_aluno`) REFERENCES `alunos` (`alu_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `credito_inscricao` FOREIGN KEY (`alc_inscricao`) REFERENCES `inscricoes` (`ins_id`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `alunos_operadoras_meta`
--
ALTER TABLE `alunos_operadoras_meta`
  ADD CONSTRAINT `aop_operadora` FOREIGN KEY (`aop_operadora`) REFERENCES `operadoras` (`opr_nome`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `op_aluno` FOREIGN KEY (`aop_aluno`) REFERENCES `alunos` (`alu_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `grupos`
--
ALTER TABLE `grupos`
  ADD CONSTRAINT `grp_operadora` FOREIGN KEY (`grp_operadora`) REFERENCES `operadoras` (`opr_nome`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Restrições para tabelas `grupos_datas`
--
ALTER TABLE `grupos_datas`
  ADD CONSTRAINT `data_grupo` FOREIGN KEY (`grd_grupo`) REFERENCES `grupos` (`grp_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `grupos_distribuicao`
--
ALTER TABLE `grupos_distribuicao`
  ADD CONSTRAINT `distribuicao_usuario` FOREIGN KEY (`dst_usuario`) REFERENCES `usuarios` (`usr_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `grupo_distribuicao` FOREIGN KEY (`dst_grupo`) REFERENCES `grupos` (`grp_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `grupos_formas`
--
ALTER TABLE `grupos_formas`
  ADD CONSTRAINT `grupo_formas` FOREIGN KEY (`gfp_grupo`) REFERENCES `grupos` (`grp_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `inscricoes`
--
ALTER TABLE `inscricoes`
  ADD CONSTRAINT `inscricao_forma` FOREIGN KEY (`ins_forma`) REFERENCES `grupos_formas` (`gfp_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `inscricao_grupo` FOREIGN KEY (`ins_grupo`) REFERENCES `grupos` (`grp_id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Restrições para tabelas `operadoras_formas`
--
ALTER TABLE `operadoras_formas`
  ADD CONSTRAINT `for_operadora` FOREIGN KEY (`ofo_operadora`) REFERENCES `operadoras` (`opr_nome`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Restrições para tabelas `operadoras_transacoes`
--
ALTER TABLE `operadoras_transacoes`
  ADD CONSTRAINT `transacao_forma` FOREIGN KEY (`otr_forma`) REFERENCES `operadoras_formas` (`ofo_forma`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `transacao_inscricao` FOREIGN KEY (`otr_inscricao`) REFERENCES `inscricoes` (`ins_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `transacao_operadora` FOREIGN KEY (`otr_operadora`) REFERENCES `operadoras` (`opr_nome`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Restrições para tabelas `operadoras_transacoes_estornos`
--
ALTER TABLE `operadoras_transacoes_estornos`
  ADD CONSTRAINT `cancelamentos_pagamentos` FOREIGN KEY (`tes_transacao`) REFERENCES `recebiveis` (`rec_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `presenca`
--
ALTER TABLE `presenca`
  ADD CONSTRAINT `presenca_aluno` FOREIGN KEY (`prs_aluno`) REFERENCES `alunos` (`alu_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `presenca_grupo` FOREIGN KEY (`prs_grupo`) REFERENCES `grupos` (`grp_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `recebiveis`
--
ALTER TABLE `recebiveis`
  ADD CONSTRAINT `pagamentos_creditos` FOREIGN KEY (`rec_creditoUtilizado`) REFERENCES `alunos_creditos` (`alc_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `pagamentos_formas` FOREIGN KEY (`rec_forma`) REFERENCES `operadoras_formas` (`ofo_forma`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `pagamentos_inscricao` FOREIGN KEY (`rec_inscricao`) REFERENCES `inscricoes` (`ins_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `pagamentos_operadora` FOREIGN KEY (`rec_operadora`) REFERENCES `operadoras` (`opr_nome`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `recebivel_transacao` FOREIGN KEY (`rec_transacao`) REFERENCES `operadoras_transacoes` (`otr_id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Restrições para tabelas `recebiveis_repasses`
--
ALTER TABLE `recebiveis_repasses`
  ADD CONSTRAINT `recebiveis_repasses_recebivel` FOREIGN KEY (`rre_recebivel`) REFERENCES `recebiveis` (`rec_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `recebiveis_repasses_repasse` FOREIGN KEY (`rre_repasse`) REFERENCES `repasses` (`rep_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `recebiveis_repasses_usuario` FOREIGN KEY (`rre_usuario`) REFERENCES `usuarios` (`usr_id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Restrições para tabelas `repasses`
--
ALTER TABLE `repasses`
  ADD CONSTRAINT `repasses_usuarios` FOREIGN KEY (`rep_usuario`) REFERENCES `usuarios` (`usr_id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
