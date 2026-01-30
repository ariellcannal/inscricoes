-- --------------------------------------------------------
--
-- Estrutura para tabela `grupos_taxas_adicionais`
-- Baseada na estrutura de `grupos_formas` com prefixo gtx_
--

CREATE TABLE `grupos_taxas_adicionais` (
  `gtx_id` int NOT NULL AUTO_INCREMENT,
  `gtx_grupo` int NOT NULL,
  `gtx_parcelas` int NOT NULL,
  `gtx_valorTotal` decimal(10,2) NOT NULL,
  `gtx_ordem` int NOT NULL DEFAULT '0',
  `gtx_publico` tinyint(1) NOT NULL DEFAULT '1',
  `gtx_aceitaCartao` tinyint(1) NOT NULL,
  `gtx_linkOculto` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gtx_linkOcultoValidade` datetime DEFAULT NULL,
  `gtx_comentario` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gtx_descricao` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gtx_primeiraParcela` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`gtx_id`),
  KEY `gtx_grupo` (`gtx_grupo`),
  CONSTRAINT `fk_grupos_taxas_adicionais_grupo` FOREIGN KEY (`gtx_grupo`) REFERENCES `grupos` (`grp_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
