-- --------------------------------------------------------
--
-- Adicionar coluna para referenciar taxa adicional na tabela operadoras_transacoes
--

ALTER TABLE `operadoras_transacoes` 
ADD COLUMN `otr_taxaAdicional` int DEFAULT NULL AFTER `otr_inscricao`,
ADD KEY `otr_taxaAdicional` (`otr_taxaAdicional`),
ADD CONSTRAINT `fk_operadoras_transacoes_taxa` FOREIGN KEY (`otr_taxaAdicional`) REFERENCES `grupos_taxas_adicionais` (`gtx_id`) ON DELETE SET NULL ON UPDATE CASCADE;
