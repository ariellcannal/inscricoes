-- ============================================================
-- INSTRUÇÕES DE EXECUÇÃO
-- ============================================================
-- 1. Faça backup do banco de dados antes de executar
-- 2. Execute os comandos na ordem apresentada
-- 3. Verifique se não há erros após cada comando
-- ============================================================

-- ============================================================
-- PASSO 1: Criar tabela grupos_taxas_adicionais
-- ============================================================

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

-- Verificar se a tabela foi criada
SELECT 'Tabela grupos_taxas_adicionais criada com sucesso!' AS status;

-- ============================================================
-- PASSO 2: Adicionar coluna em operadoras_transacoes
-- ============================================================

ALTER TABLE `operadoras_transacoes` 
ADD COLUMN `otr_taxaAdicional` int DEFAULT NULL AFTER `otr_inscricao`;

-- Adicionar índice
ALTER TABLE `operadoras_transacoes`
ADD KEY `otr_taxaAdicional` (`otr_taxaAdicional`);

-- Adicionar foreign key
ALTER TABLE `operadoras_transacoes`
ADD CONSTRAINT `fk_operadoras_transacoes_taxa` 
FOREIGN KEY (`otr_taxaAdicional`) 
REFERENCES `grupos_taxas_adicionais` (`gtx_id`) 
ON DELETE SET NULL 
ON UPDATE CASCADE;

-- Verificar se a coluna foi adicionada
SELECT 'Coluna otr_taxaAdicional adicionada com sucesso!' AS status;

-- ============================================================
-- PASSO 3: Verificação final
-- ============================================================

-- Verificar estrutura da tabela grupos_taxas_adicionais
DESCRIBE grupos_taxas_adicionais;

-- Verificar se a coluna foi adicionada em operadoras_transacoes
SHOW COLUMNS FROM operadoras_transacoes LIKE 'otr_taxaAdicional';

-- Verificar foreign keys
SELECT 
    CONSTRAINT_NAME,
    TABLE_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM 
    INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE 
    TABLE_SCHEMA = DATABASE()
    AND (CONSTRAINT_NAME = 'fk_grupos_taxas_adicionais_grupo' 
         OR CONSTRAINT_NAME = 'fk_operadoras_transacoes_taxa');

-- ============================================================
-- EXEMPLO DE INSERÇÃO (OPCIONAL - APENAS PARA TESTE)
-- ============================================================

-- Descomente as linhas abaixo para inserir uma taxa de teste
-- Substitua o valor 1 pelo ID de um grupo real do seu banco

/*
INSERT INTO grupos_taxas_adicionais 
(gtx_grupo, gtx_parcelas, gtx_valorTotal, gtx_ordem, gtx_publico, gtx_aceitaCartao, gtx_comentario, gtx_descricao, gtx_primeiraParcela)
VALUES 
(1, 1, 150.00, 0, 1, 1, 'Taxa Material', 'R$150,00 no cartão de crédito', 1);

SELECT 'Taxa de teste inserida com sucesso!' AS status;
*/

-- ============================================================
-- FIM DA EXECUÇÃO
-- ============================================================

SELECT '✅ Todas as alterações foram aplicadas com sucesso!' AS status;
