USE `grupotapa_grupos`;
RENAME TABLE `grupotapa_grupos`.`pagamentos` TO `grupotapa_grupos`.`recebiveis`;
RENAME TABLE `grupotapa_grupos`.`pagamentos_estornos` TO `grupotapa_grupos`.`recebiveis_estornos`;
RENAME TABLE `grupotapa_grupos`.`pagamentos_formas` TO `grupotapa_grupos`.`recebiveis_formas`;
RENAME TABLE `grupotapa_grupos`.`pagamentos_repasses` TO `grupotapa_grupos`.`recebiveis_repasses`;
RENAME TABLE `grupotapa_grupos`.`repasses_pagamentos` TO `grupotapa_grupos`.`repasses_recebiveis`;
DROP TABLE `grupotapa_grupos`.`pagamentos_receber`;

ALTER TABLE `recebiveis` CHANGE `pgt_id` `rec_id` INT NOT NULL AUTO_INCREMENT, CHANGE `pgt_inscricao` `rec_inscricao` INT NOT NULL, CHANGE `pgt_creditoUtilizado` `rec_creditoUtilizado` INT NULL DEFAULT NULL, CHANGE `pgt_valor` `rec_valor` DECIMAL(10,2) NOT NULL, CHANGE `pgt_valorLiquido` `rec_valorLiquido` DECIMAL(10,2) NULL, CHANGE `pgt_estornoValor` `rec_estornoValor` DECIMAL(10,2) NULL DEFAULT NULL, CHANGE `pgt_estornoData` `rec_estornoData` DATETIME NULL DEFAULT NULL, CHANGE `pgt_forma` `rec_forma` VARCHAR(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL, CHANGE `pgt_parcelas` `rec_parcelas` INT NOT NULL DEFAULT '1', CHANGE `pgt_documento` `rec_documento` VARCHAR(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `pgt_comprovanteRecebido` `rec_comprovanteRecebido` TINYINT(1) NOT NULL, CHANGE `pgt_dataTransacao` `rec_dataTransacao` DATETIME NOT NULL, CHANGE `pgt_dataExpiraCobranca` `rec_dataExpiraCobranca` DATETIME NULL DEFAULT NULL, CHANGE `pgt_repasseData` `rec_repasseData` DATE NOT NULL, CHANGE `pgt_repasseConfirmado` `rec_repasseConfirmado` TINYINT(1) NOT NULL DEFAULT '0', CHANGE `pgt_idParcela` `rec_parcela` TINYINT NULL DEFAULT NULL, CHANGE `pgt_descFatura` `rec_descFatura` VARCHAR(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `pgt_cartao` `rec_cartao` VARCHAR(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `pgt_cartaoCodigo` `rec_cartaoCodigo` VARCHAR(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `pgt_cartaoValidade` `rec_cartaoValidade` VARCHAR(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `pgt_cartaoNome` `rec_cartaoNome` VARCHAR(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `pgt_cartaoCPF` `rec_cartaoCPF` VARCHAR(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `pgt_tid` `rec_tid` VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `pgt_nsu` `rec_nsu` VARCHAR(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `pgt_authorizationCode` `rec_authorizationCode` VARCHAR(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `pgt_cardBin` `rec_cardBin` VARCHAR(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `pgt_last4` `rec_last4` VARCHAR(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `pgt_returnCode` `rec_returnCode` VARCHAR(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `pgt_returnMessage` `rec_returnMessage` VARCHAR(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `pgt_tentativasNegadas` `rec_tentativasNegadas` TINYINT(1) NULL DEFAULT NULL, CHANGE `pgt_operadora` `rec_operadora` VARCHAR(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `pgt_operadoraResposta` `rec_operadoraResposta` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `pgt_operadoraStatus` `rec_operadoraStatus` VARCHAR(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `pgt_operadoraID` `rec_operadoraID` VARCHAR(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `pgt_criacao` `rec_criacao` DATETIME NULL DEFAULT NULL;
ALTER TABLE `recebiveis_estornos` CHANGE `pgs_id` `res_id` INT NOT NULL AUTO_INCREMENT, CHANGE `pgs_pagamento` `res_pagamento` INT NOT NULL, CHANGE `pgs_valor` `res_valor` DECIMAL(10,2) NOT NULL, CHANGE `pgs_criacao` `res_criacao` DATETIME NOT NULL, CHANGE `pgs_returnCode` `res_returnCode` VARCHAR(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `pgs_returnMessage` `res_returnMessage` VARCHAR(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `pgs_tid` `res_tid` VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `pgs_nsu` `res_nsu` VARCHAR(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `pgs_refundId` `res_refundId` VARCHAR(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `pgs_refundDateTime` `res_refundDateTime` DATETIME NULL DEFAULT NULL, CHANGE `pgs_cancelId` `res_cancelId` VARCHAR(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `pgs_operadoraID` `res_operadoraID` VARCHAR(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `pgs_operadoraStatus` `res_operadoraStatus` VARCHAR(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `pgs_operadoraResposta` `res_operadoraResposta` VARCHAR(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
ALTER TABLE `recebiveis_formas` CHANGE `for_forma` `rfo_forma` VARCHAR(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL, CHANGE `for_operadora` `rfo_operadora` VARCHAR(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL, CHANGE `for_parcelamento` `rfo_parcelamento` TINYINT(1) NOT NULL, CHANGE `for_antecipacao` `rfo_antecipacao` TINYINT(1) NULL DEFAULT NULL, CHANGE `for_prazoEstornoTaxa` `rfo_prazoEstornoTaxa` INT NOT NULL DEFAULT '0', CHANGE `for_custoFixo` `rfo_custoFixo` DOUBLE(10,2) NULL DEFAULT NULL, CHANGE `for_taxa` `rfo_taxa` DOUBLE(10,2) NULL DEFAULT NULL, CHANGE `for_taxaParcelamento23` `rfo_taxaParcelamento23` DOUBLE(10,2) NULL DEFAULT NULL, CHANGE `for_taxaParcelamento46` `rfo_taxaParcelamento46` DOUBLE(10,2) NULL DEFAULT NULL, CHANGE `for_taxaParcelamento712` `rfo_taxaParcelamento712` DOUBLE(10,2) NULL DEFAULT NULL;
ALTER TABLE `recebiveis_repasses` CHANGE `pre_id` `rre_id` INT NOT NULL AUTO_INCREMENT, CHANGE `pre_pagamento` `rre_pagamento` INT NOT NULL, CHANGE `pre_usuario` `rre_usuario` INT NOT NULL, CHANGE `pre_valor` `rre_valor` DECIMAL(10,2) NOT NULL;
ALTER TABLE `repasses_recebiveis` CHANGE `pre_id` `rre_id` INT NOT NULL;
ALTER TABLE `recebiveis_repasses` CHANGE `rre_pagamento` `rre_recebivel` INT NOT NULL;

RENAME TABLE `grupotapa_grupos`.`recebiveis_formas` TO `grupotapa_grupos`.`operadoras_formas`;
ALTER TABLE `operadoras_formas` CHANGE `rfo_forma` `ofo_forma` VARCHAR(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL, CHANGE `rfo_operadora` `ofo_operadora` VARCHAR(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL, CHANGE `rfo_parcelamento` `ofo_parcelamento` TINYINT(1) NOT NULL, CHANGE `rfo_antecipacao` `ofo_antecipacao` TINYINT(1) NULL DEFAULT NULL, CHANGE `rfo_prazoEstornoTaxa` `ofo_prazoEstornoTaxa` INT NOT NULL DEFAULT '0', CHANGE `rfo_custoFixo` `ofo_custoFixo` DOUBLE(10,2) NULL DEFAULT NULL, CHANGE `rfo_taxa` `ofo_taxa` DOUBLE(10,2) NULL DEFAULT NULL, CHANGE `rfo_taxaParcelamento23` `ofo_taxaParcelamento23` DOUBLE(10,2) NULL DEFAULT NULL, CHANGE `rfo_taxaParcelamento46` `ofo_taxaParcelamento46` DOUBLE(10,2) NULL DEFAULT NULL, CHANGE `rfo_taxaParcelamento712` `ofo_taxaParcelamento712` DOUBLE(10,2) NULL DEFAULT NULL;

CREATE TABLE `grupotapa_grupos`.`operadoras_transacoes` (`otr_id` INT NOT NULL AUTO_INCREMENT , `otr_forma` VARCHAR(256) NOT NULL , `otr_parcelas` INT NULL DEFAULT '1' , `otr_valorBruto` DOUBLE(10,2) NOT NULL , `otr_valorLiquido` DOUBLE(10,2) NOT NULL , `otr_dataTransacao` DATETIME NOT NULL , `otr_operadora` VARCHAR(32) NOT NULL , `otr_operadoraResposta` TEXT NULL DEFAULT NULL , `otr_operadoraStatus` VARCHAR(32) NULL DEFAULT NULL , `otr_operadoraID` VARCHAR(32) NULL DEFAULT NULL , `otr_criacao` DATETIME NOT NULL , PRIMARY KEY (`otr_id`)) ENGINE = InnoDB;
ALTER TABLE `operadoras_transacoes` ADD `otr_tid` VARCHAR(32) NULL AFTER `otr_operadoraID`, ADD `otr_nsu` VARCHAR(32) NULL AFTER `otr_tid`, ADD `otr_authorizationCode` VARCHAR(6) NULL AFTER `otr_nsu`, ADD `otr_cardLast4` VARCHAR(4) NULL AFTER `otr_authorizationCode`;
ALTER TABLE `operadoras_transacoes` ADD `otr_cardBin` VARCHAR(6) NULL AFTER `otr_authorizationCode`;
ALTER TABLE `operadoras_transacoes` CHANGE `otr_tid` `otr_tid` VARCHAR(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `otr_nsu` `otr_nsu` VARCHAR(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
ALTER TABLE `operadoras_transacoes` CHANGE `otr_operadoraStatus` `otr_operadoraStatus` VARCHAR(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
ALTER TABLE `operadoras_transacoes` ADD `otr_descricaoFatura` VARCHAR(32) NULL AFTER `otr_dataTransacao`;
ALTER TABLE `operadoras_transacoes` ADD `otr_dataExpiracao` DATETIME NULL AFTER `otr_dataTransacao`, ADD `otr_confirmada` TINYINT(1) NULL AFTER `otr_dataExpiracao`;
ALTER TABLE `operadoras_transacoes` ADD `otr_cartao` VARCHAR(32) NULL AFTER `otr_forma`;
ALTER TABLE `operadoras_transacoes` ADD `otr_inscricao` INT NULL AFTER `otr_id`;
ALTER TABLE `operadoras_transacoes` ADD `otr_tipo` ENUM('pix','cartao') NULL AFTER `otr_forma`;
ALTER TABLE `operadoras_transacoes`
  ADD CONSTRAINT `transacao_forma` FOREIGN KEY (`otr_forma`) REFERENCES `operadoras_formas` (`ofo_forma`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `transacao_operadora` FOREIGN KEY (`otr_operadora`) REFERENCES `operadoras` (`opr_nome`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `transacao_inscricao` FOREIGN KEY (`otr_inscricao`) REFERENCES `inscricoes`(`ins_id`) ON DELETE RESTRICT ON UPDATE CASCADE;
  
ALTER TABLE `recebiveis` ADD `rec_transacao` INT NULL AFTER `rec_tentativasNegadas`;
ALTER TABLE `recebiveis`
  ADD CONSTRAINT `recebivel_transacao` FOREIGN KEY (`rec_transacao`) REFERENCES `operadoras_transacoes` (`otr_id`) ON DELETE RESTRICT ON UPDATE CASCADE;

INSERT INTO `operadoras_transacoes`(`otr_inscricao`, `otr_forma`, `otr_parcelas`, `otr_valorBruto`, `otr_valorLiquido`, `otr_dataTransacao`, `otr_operadora`, `otr_operadoraResposta`, `otr_operadoraStatus`, `otr_operadoraID`, `otr_tid`, `otr_nsu`, `otr_authorizationCode`, `otr_cardBin`, `otr_cardLast4`, `otr_criacao`, `otr_descricaoFatura`, `otr_cartao`)
SELECT rec_inscricao, rec_forma, rec_parcelas, SUM(rec_valor), SUM(rec_valorLiquido), rec_dataTransacao, rec_operadora, null, CONCAT(rec_returnCode," - ", rec_returnMessage), rec_operadoraID, rec_tid, rec_nsu, rec_authorizationCode, rec_cardBin, SUBSTRING(REPLACE(rec_cartao,' ',''),LENGTH(REPLACE(rec_cartao,' ',''))-3,4), rec_criacao, rec_descFatura, CONCAT('Cartão final ', SUBSTRING(REPLACE(rec_cartao,' ',''),LENGTH(REPLACE(rec_cartao,' ',''))-3,4)) FROM recebiveis WHERE rec_operadora = 'cielo_tapa' GROUP BY rec_tid;  
UPDATE `recebiveis` SET rec_transacao = (SELECT otr_id FROM operadoras_transacoes WHERE rec_operadoraID = otr_operadoraID LIMIT 1) WHERE rec_operadora = 'cielo_tapa';

INSERT INTO `operadoras_transacoes`(`otr_inscricao`, `otr_forma`, `otr_parcelas`, `otr_valorBruto`, `otr_valorLiquido`, `otr_dataTransacao`, `otr_operadora`, `otr_operadoraResposta`, `otr_operadoraStatus`, `otr_operadoraID`, `otr_tid`, `otr_nsu`, `otr_authorizationCode`, `otr_cardBin`, `otr_cardLast4`, `otr_criacao`, `otr_descricaoFatura`, `otr_cartao`) 
SELECT rec_inscricao, rec_forma, rec_parcelas, SUM(rec_valor), SUM(rec_valorLiquido), rec_dataTransacao, rec_operadora, null, CONCAT(rec_returnCode," - ", rec_returnMessage), rec_operadoraID, rec_tid, rec_nsu, rec_authorizationCode, rec_cardBin, SUBSTRING(REPLACE(rec_cartao,' ',''),LENGTH(REPLACE(rec_cartao,' ',''))-3,4), rec_criacao, rec_descFatura, CONCAT('Cartão final ', SUBSTRING(REPLACE(rec_cartao,' ',''),LENGTH(REPLACE(rec_cartao,' ',''))-3,4)) FROM recebiveis WHERE rec_operadora = 'rede_tapa' GROUP BY rec_tid;
UPDATE `recebiveis` SET rec_transacao = (SELECT otr_id FROM operadoras_transacoes WHERE rec_operadoraID = otr_operadoraID LIMIT 1) WHERE rec_operadora = 'rede_tapa';

INSERT INTO `operadoras_transacoes`(`otr_inscricao`, `otr_forma`, `otr_parcelas`, `otr_valorBruto`, `otr_valorLiquido`, `otr_dataTransacao`, `otr_operadora`, `otr_operadoraResposta`, `otr_operadoraStatus`, `otr_operadoraID`, `otr_criacao`, `otr_descricaoFatura`, `otr_confirmada`, `otr_cartao`)
SELECT rec_inscricao, rec_forma, rec_parcelas, SUM(rec_valor), SUM(rec_valorLiquido), rec_dataTransacao, rec_operadora, rec_operadoraResposta, rec_operadoraStatus, rec_operadoraID, rec_criacao, rec_descFatura, rec_repasseConfirmado, rec_cartao FROM recebiveis WHERE rec_operadora LIKE 'pagarme_%' GROUP BY rec_operadoraID;
UPDATE `recebiveis` SET rec_transacao = (SELECT otr_id FROM operadoras_transacoes WHERE rec_operadoraID = otr_operadoraID LIMIT 1) WHERE rec_operadora LIKE 'pagarme_%';

UPDATE `operadoras_transacoes` SET `otr_parcelas` = null, otr_tipo='pix' WHERE `otr_forma` LIKE '%PIX%';
UPDATE `operadoras_transacoes` SET otr_tipo='cartao' WHERE `otr_forma` NOT LIKE '%PIX%';
ALTER TABLE `operadoras_transacoes` CHANGE `otr_tipo` `otr_tipo` ENUM('pix','cartao') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;

ALTER TABLE `inscricoes` CHANGE `ins_forma` `ins_forma` INT NULL DEFAULT NULL;
ALTER TABLE `inscricoes` DROP FOREIGN KEY `grupo_inscricao`;
ALTER TABLE `inscricoes` ADD CONSTRAINT `inscricao_grupo` FOREIGN KEY (`ins_grupo`) REFERENCES `grupos`(`grp_id`) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE `inscricoes` ADD CONSTRAINT `inscricao_forma` FOREIGN KEY (`ins_forma`) REFERENCES `grupos_formas`(`gfp_id`) ON DELETE RESTRICT ON UPDATE CASCADE;


ALTER TABLE `alunos` CHANGE `alu_pagarme_id` `alu_pagarmeId` VARCHAR(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `alu_pagarme_address_id` `alu_pagarmeAddressId` VARCHAR(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `alu_endereco_numero` `alu_enderecoNumero` VARCHAR(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `alu_endereco_complemento` `alu_enderecoComplemento` VARCHAR(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `alu_endereco_bairro` `alu_enderecoBairro` VARCHAR(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `alu_endereco_cidade` `alu_enderecoCidade` VARCHAR(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `alu_endereco_estado` `alu_enderecoEstado` VARCHAR(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `alu_endereco_cep` `alu_enderecoCep` VARCHAR(9) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;

ALTER TABLE `operadoras_transacoes` ADD `otr_operadoraErros` TEXT NULL AFTER `otr_operadoraResposta`;

ALTER TABLE `grupotapa_grupos`.`operadoras_formas` DROP PRIMARY KEY, ADD PRIMARY KEY (`ofo_forma`, `ofo_operadora`) USING BTREE;

INSERT INTO `operadoras_formas`(`ofo_forma`, `ofo_operadora`, `ofo_parcelamento`, `ofo_antecipacao`, `ofo_prazoEstornoTaxa`, `ofo_custoFixo`, `ofo_taxa`, `ofo_taxaParcelamento23`, `ofo_taxaParcelamento46`, `ofo_taxaParcelamento712`) SELECT `ofo_forma`, 'pagarme_cannal', `ofo_parcelamento`, `ofo_antecipacao`, `ofo_prazoEstornoTaxa`, `ofo_custoFixo`, `ofo_taxa`, `ofo_taxaParcelamento23`, `ofo_taxaParcelamento46`, `ofo_taxaParcelamento712`
FROM `operadoras_formas`
WHERE `ofo_operadora` = 'pagarme_tapa';

ALTER TABLE `operadoras_transacoes` ADD `otr_operadoraData` DATETIME NULL AFTER `otr_operadoraID`;
ALTER TABLE `recebiveis` ADD `rec_operadoraData` DATETIME NULL AFTER `rec_operadoraID`;

ALTER TABLE `recebiveis` CHANGE `rec_repasseData` `rec_dataRecebimento` DATE NOT NULL;
ALTER TABLE `recebiveis` CHANGE `rec_repasseConfirmado` `rec_confirmado` TINYINT(1) NOT NULL DEFAULT '0';

ALTER TABLE `operadoras_transacoes` ADD `otr_valorCancelado` DOUBLE(10,2) NULL AFTER `otr_valorLiquido`, ADD `otr_dataCancelamento` DATETIME NULL AFTER `otr_valorCancelado`;

ALTER TABLE `recebiveis`
  DROP `rec_documento`,
  DROP `rec_comprovanteRecebido`,
  DROP `rec_cartao`,
  DROP `rec_cartaoCodigo`,
  DROP `rec_cartaoValidade`,
  DROP `rec_cartaoNome`,
  DROP `rec_cartaoCPF`,
  DROP `rec_tid`,
  DROP `rec_nsu`,
  DROP `rec_authorizationCode`,
  DROP `rec_cardBin`,
  DROP `rec_last4`,
  DROP `rec_returnCode`,
  DROP `rec_descFatura`,
  DROP `rec_parcelas`,
  DROP `rec_dataExpiraCobranca`,
  DROP `rec_tentativasNegadas`,
  DROP `rec_returnMessage`;

ALTER TABLE `alunos`  
  DROP `alu_pagarmeAddressId`;