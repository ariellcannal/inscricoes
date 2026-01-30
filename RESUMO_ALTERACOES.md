# Resumo das Alterações - Taxas Adicionais

## 📋 Status da Implementação

### ✅ Concluído

1. **Estrutura de Banco de Dados**
   - Tabela `grupos_taxas_adicionais` criada
   - Alteração em `operadoras_transacoes` preparada
   - SQLs prontos para execução

2. **Backend - Models**
   - `Grupos_model.php`: Métodos para buscar taxas adicionais
   - Integração com estrutura existente

3. **Backend - Controllers**
   - `Grupos.php`: Nested xCrud para gerenciar taxas
   - Interface administrativa completa

4. **Backend - Helpers**
   - `grupos_helper.php`: Callbacks de validação e formatação
   - Lógica de descrição automática

5. **Documentação**
   - Guia completo de implementação
   - Exemplos de código
   - Instruções passo a passo

### 🔄 Pendente (Próximas Etapas)

1. **Executar SQLs no Banco**
   ```bash
   # No phpMyAdmin ou cliente MySQL
   source sql/grupos_taxas_adicionais.sql;
   source sql/alter_operadoras_transacoes.sql;
   ```

2. **Controller Inscricoes.php**
   - Adicionar lógica de processamento de taxas no método `inscricao()`
   - Criar campos dinâmicos para cada taxa
   - Ver exemplo completo em `INSTRUCOES_IMPLEMENTACAO.md`

3. **Helper inscricoes_helper.php**
   - Atualizar callbacks `BI_inscricao_aluno` e `AI_inscricao_aluno`
   - Processar transações das taxas adicionais
   - Ver exemplo completo em `INSTRUCOES_IMPLEMENTACAO.md`

4. **View inscricao_form.php**
   - Substituir seção de pagamento por versão com abas
   - Adicionar blocos de taxas adicionais
   - Ver template completo em `INSTRUCOES_IMPLEMENTACAO.md`

5. **JavaScript**
   - Atualizar `inscricoes_aluno.js`
   - Controle de abas PIX/Cartão
   - Lógica "utilizar mesmo cartão"
   - Validações de campos

## 📊 Arquivos Modificados

### Novos Arquivos
- `sql/grupos_taxas_adicionais.sql` - Criação da tabela
- `sql/alter_operadoras_transacoes.sql` - Alteração para referência
- `sql/visualizar_sql.html` - Visualização formatada do SQL
- `INSTRUCOES_IMPLEMENTACAO.md` - Guia completo
- `RESUMO_ALTERACOES.md` - Este arquivo

### Arquivos Alterados
- `application/controllers/Grupos.php` - Nested xCrud de taxas
- `application/models/Grupos_model.php` - Métodos de busca
- `application/helpers/grupos_helper.php` - Callbacks

## 🎯 Como Usar

### 1. Criar Taxas Adicionais (Admin)

1. Acesse o painel administrativo de Grupos
2. Edite um grupo existente
3. Vá até a aba "Taxas Adicionais"
4. Clique em "Adicionar"
5. Preencha os campos:
   - **Valor Total**: Valor da taxa
   - **Parcelamento Máximo**: Número de parcelas
   - **Comentário**: Texto curto (ex: "Taxa de Material")
   - **Aceita Cartão**: Se permite pagamento em cartão
   - **Cobrar na Primeira Parcela/PIX**: 
     - ✅ **true**: Taxa será cobrada junto com o curso
     - ❌ **false**: Taxa terá formulário próprio de pagamento
   - **Ordem de Exibição**: Ordem no formulário
   - **Público**: Se aparece no formulário público

### 2. Fluxo de Cobrança

#### Taxas com `gtx_primeiraParcela = true`

**Forma de Pagamento: PIX**
```
Valor do Curso: R$ 1.000,00
+ Taxa Material: R$ 150,00
+ Taxa Uniforme: R$ 80,00
= Total PIX: R$ 1.230,00
```

**Forma de Pagamento: Cartão**
```
Transação 1: Curso - R$ 1.000,00 (parcelado)
Transação 2: Taxa Material - R$ 150,00 (à vista)
Transação 3: Taxa Uniforme - R$ 80,00 (à vista)
```

#### Taxas com `gtx_primeiraParcela = false`

O aluno escolhe forma de pagamento para cada taxa:
```
Formulário 1: Pagamento do Curso
  └─ PIX ou Cartão (1-12x)

Formulário 2: Pagamento de Taxa Material
  └─ PIX ou Cartão (1-3x)
  └─ Opção: "Utilizar mesmo cartão"

Formulário 3: Pagamento de Taxa Uniforme
  └─ PIX ou Cartão (à vista)
  └─ Opção: "Utilizar mesmo cartão"
```

## 🔧 Estrutura da Tabela

```sql
CREATE TABLE `grupos_taxas_adicionais` (
  `gtx_id` int NOT NULL AUTO_INCREMENT,
  `gtx_grupo` int NOT NULL,                    -- FK para grupos
  `gtx_parcelas` int NOT NULL,                 -- Máximo de parcelas
  `gtx_valorTotal` decimal(10,2) NOT NULL,     -- Valor total da taxa
  `gtx_ordem` int NOT NULL DEFAULT '0',        -- Ordem de exibição
  `gtx_publico` tinyint(1) NOT NULL DEFAULT '1', -- Visível no form público
  `gtx_aceitaCartao` tinyint(1) NOT NULL,      -- Aceita cartão de crédito
  `gtx_linkOculto` varchar(32),                -- Link oculto (opcional)
  `gtx_linkOcultoValidade` datetime,           -- Validade do link
  `gtx_comentario` varchar(16),                -- Comentário curto
  `gtx_descricao` varchar(256),                -- Descrição formatada (auto)
  `gtx_primeiraParcela` tinyint(1) NOT NULL DEFAULT '1', -- Cobrar junto?
  PRIMARY KEY (`gtx_id`),
  FOREIGN KEY (`gtx_grupo`) REFERENCES `grupos` (`grp_id`)
);
```

## 📝 Exemplo de Uso Real

### Cenário: Curso de Teatro com Taxas

**Configuração no Admin:**

1. **Curso**: Teatro Intensivo
   - Valor: R$ 2.400,00
   - Formas: 1x PIX, 12x Cartão

2. **Taxa 1**: Material Didático
   - Valor: R$ 200,00
   - Parcelas: 1x
   - `gtx_primeiraParcela`: **true**
   - Descrição: "Taxa de Material"

3. **Taxa 2**: Uniforme
   - Valor: R$ 150,00
   - Parcelas: 3x
   - `gtx_primeiraParcela`: **false**
   - Descrição: "Taxa de Uniforme"

**Resultado no Formulário:**

```
┌─────────────────────────────────────────┐
│ PAGAMENTO DO CURSO                      │
├─────────────────────────────────────────┤
│ [PIX] [Cartão de Crédito]              │
│                                         │
│ Opções:                                 │
│ ○ 1x de R$ 2.400,00 no PIX             │
│   + R$ 200,00 ref. Taxa de Material    │
│                                         │
│ ○ 12x de R$ 200,00 no cartão           │
│   + R$ 200,00 ref. Taxa de Material    │
│   (cobrado à vista na 1ª parcela)      │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│ PAGAMENTO DE TAXA DE UNIFORME           │
├─────────────────────────────────────────┤
│ [PIX] [Cartão de Crédito]              │
│                                         │
│ Opções:                                 │
│ ○ 1x de R$ 150,00 no PIX               │
│ ○ 3x de R$ 50,00 no cartão             │
│                                         │
│ Cartão: [Utilizar mesmo cartão ▼]      │
└─────────────────────────────────────────┘
```

## 🚀 Links Úteis

- **PR no GitHub**: https://github.com/ariellcannal/inscricoes/pull/34
- **Visualizar SQL**: Abra `sql/visualizar_sql.html` no navegador
- **Instruções Completas**: `INSTRUCOES_IMPLEMENTACAO.md`

## ⚠️ Importante

- Sempre teste em ambiente de desenvolvimento primeiro
- Faça backup do banco antes de executar os SQLs
- Valide os cálculos de valores antes de ir para produção
- Teste todos os cenários de pagamento (PIX, Cartão, Misto)

## 📞 Suporte

Para dúvidas ou problemas na implementação, consulte o arquivo `INSTRUCOES_IMPLEMENTACAO.md` que contém exemplos completos de código para cada etapa.
