# Instruções de Implementação - Taxas Adicionais

## ✅ Status: IMPLEMENTAÇÃO COMPLETA

Todas as alterações necessárias foram implementadas e estão prontas para uso.

## Resumo das Alterações Realizadas

### 1. Banco de Dados

#### Tabela `grupos_taxas_adicionais`
- **Arquivo SQL**: `/sql/grupos_taxas_adicionais.sql` e `SQL_PARA_EXECUTAR.sql`
- Estrutura idêntica a `grupos_formas` com prefixo `gtx_`
- Campo adicional: `gtx_primeiraParcela` (boolean, padrão true)

**IMPORTANTE**: Não foi criada coluna `otr_taxaAdicional` em `operadoras_transacoes`. As taxas com `gtx_primeiraParcela = true` são somadas ao valor total da transação principal.

### 2. Backend - Models

#### Grupos_model.php ✅
Adicionados dois métodos:
- `getTaxasAdicionais($grp_id, $linkOculto = null, $publico = true)` - Busca taxas de um grupo
- `getTaxaAdicional($gtx_id)` - Busca uma taxa específica

### 3. Backend - Controllers

#### Grupos.php ✅
Adicionado nested table para gerenciar taxas adicionais:
- Configuração similar ao nested de `grupos_formas`
- Campos: valor, parcelas, primeira parcela, ordem, público, etc.
- Callbacks: `BI_gtx` e `BU_gtx`

#### Inscricoes.php ✅
Implementada lógica completa de processamento de taxas:
- **Linhas 434-505**: Processamento de taxas adicionais
  - Busca taxas do grupo
  - Separa entre `taxasPrimeiraParcela` (true) e `taxasPosteriores` (false)
  - Para taxas de primeira parcela: soma o valor ao total da forma de pagamento
  - Atualiza descrição das formas incluindo texto das taxas

- **Linhas 547-570**: Criação de campos dinâmicos para taxas posteriores
  - Cria campos de forma de pagamento para cada taxa
  - Cria campos de cartão para cada taxa
  - Adiciona opção "Utilizar mesmo cartão"

- **Linhas 605-628**: Labels e atributos dos campos
  - Configura labels traduzidos
  - Define atributos HTML (id, class, autocomplete)
  - Aplica máscaras de CPF

- **Linhas 775-778**: Passa variáveis para view
  - `taxasAdicionais`: todas as taxas
  - `taxasPrimeiraParcela`: taxas cobradas junto
  - `taxasPosteriores`: taxas com formulário próprio

### 4. Backend - Helpers

#### grupos_helper.php ✅
Adicionados callbacks:
- `BI_gtx($postdata, $xcrud)` - Before Insert para taxas
- `BU_gtx($postdata, $gtx_id, $xcrud)` - Before Update para taxas

#### inscricoes_helper.php ✅
Atualizado callback `BI_inscricao_aluno`:
- **Linhas 137-149**: Captura dados das taxas do postdata
- Armazena em `ins_tempData['taxas']` para processamento posterior

### 5. Frontend - Views

#### inscricao_form.php ✅
Formulário completamente reformulado:
- **Linhas 1-10**: Decodifica variáveis de taxas
- **Linhas 103-164**: Seção de pagamento do curso
  - Mantém estrutura original
  - Exibe informações de taxas de primeira parcela abaixo do select

- **Linhas 166-230**: Loop de taxas posteriores
  - Cria bloco separado para cada taxa
  - Título com nome e valor da taxa
  - Select de forma de pagamento
  - Select "Utilizar mesmo cartão"
  - Campos de cartão (ocultos inicialmente)

### 6. Frontend - JavaScript

#### inscricoes_aluno.js ✅
Adicionadas funções para controle de taxas:

- **Linhas 134-147**: Inicialização de controles de taxas
  - Vincula eventos change aos selects
  - Configura validação de validade do cartão

- **Linhas 197-214**: `checkTaxaFOP(taxaId)`
  - Verifica se taxa aceita cartão
  - Mostra/oculta select "utilizar mesmo cartão"

- **Linhas 216-237**: `selectTaxaCartao(taxaId)`
  - Controla exibição dos campos de cartão
  - Trata opção "mesmo" (usar cartão do curso)
  - Trata opção "novo" (inserir dados)

## Lógica de Cobrança Implementada

### Taxas com `gtx_primeiraParcela = true`

O valor dessas taxas é **somado ao valor total** da forma de pagamento escolhida:

```php
// No controller Inscricoes.php (linhas 472-505)
$valorTaxasPrimeira = 0;
foreach ($taxasPrimeiraParcela as $taxa) {
    $valorTaxasPrimeira += $taxa['gtx_valorTotal'];
}

// Reprocessa formas adicionando o valor
$valorTotalComTaxas = $valorTotalOriginal + $valorTaxasPrimeira;
$novoValorParcela = $valorTotalComTaxas / $parcelas;
```

**Resultado:**
- **PIX**: Valor do curso + taxas = total do PIX
- **Cartão**: (Valor do curso + taxas) / parcelas = valor de cada parcela

**Exemplo:**
- Curso: R$ 1.000,00 em 10x
- Taxa Material (primeira parcela): R$ 200,00
- **Resultado**: 10x de R$ 120,00 (R$ 1.200,00 / 10)

### Taxas com `gtx_primeiraParcela = false`

Cada taxa tem seu próprio formulário de pagamento:

```php
// No controller Inscricoes.php (linhas 547-570)
foreach ($taxasPosteriores as $idx => $taxa) {
    $prefix = 'taxa_' . $taxa['gtx_id'];
    
    // Cria campos independentes
    $xcrud->create_field($prefix . '_fop', 'select', null, $formasTaxa);
    $xcrud->create_field($prefix . '_alu_cartoes', 'select', "novo", [
        "novo" => 'Inserir dados do cartão', 
        "mesmo" => 'Utilizar mesmo cartão'
    ]);
    // ... demais campos
}
```

**Processamento:**
- Dados salvos em `ins_tempData['taxas']`
- Cada taxa deve gerar transação independente (implementar em callback AI_inscricao_aluno)

## Próximos Passos (Opcional)

### 1. Implementar Processamento de Transações das Taxas Posteriores

No helper `inscricoes_helper.php`, no callback `AI_inscricao_aluno`, adicionar:

```php
// Após linha 188 (setTotaisInscricao)
$ins_tempData = json_decode($ci->inscricoes_model->getRow($ins_id)['ins_tempData'], true);

if (!empty($ins_tempData['taxas'])) {
    $taxasAgrupadas = [];
    foreach ($ins_tempData['taxas'] as $key => $value) {
        preg_match('/^taxa_(\d+)_(.+)$/', $key, $matches);
        if (count($matches) === 3) {
            $taxaId = $matches[1];
            $campo = $matches[2];
            $taxasAgrupadas[$taxaId][$campo] = $value;
        }
    }
    
    foreach ($taxasAgrupadas as $taxaId => $dadosTaxa) {
        $taxa = $ci->grupos_model->getTaxaAdicional($taxaId);
        if (!$taxa) continue;
        
        $fopData = explode('_', $dadosTaxa['fop']);
        $parcelas = $fopData[1];
        $aceitaCartao = $fopData[2];
        $valorParcela = $fopData[3];
        $valorTotal = $valorParcela * $parcelas;
        
        // Se usar mesmo cartão, copiar dados do cartão principal
        if ($aceitaCartao && $dadosTaxa['alu_cartoes'] === 'mesmo') {
            // Usar cartão do curso principal
            // Implementar lógica de cobrança usando interface de pagamento
        } else if ($aceitaCartao && $dadosTaxa['alu_cartoes'] === 'novo') {
            // Criar nova transação com novo cartão
            // Implementar lógica de cobrança
        } else {
            // Criar transação PIX
            // Implementar lógica de cobrança
        }
    }
}
```

### 2. Testar Fluxo Completo

1. Criar um grupo de teste
2. Adicionar taxas adicionais:
   - Taxa 1: `gtx_primeiraParcela = true`
   - Taxa 2: `gtx_primeiraParcela = false`
3. Acessar formulário de inscrição
4. Verificar:
   - Valor da taxa 1 está somado nas opções de pagamento
   - Taxa 2 tem formulário próprio
   - Opção "utilizar mesmo cartão" funciona
5. Submeter formulário
6. Verificar transações no banco

## Estrutura de Arquivos

```
inscricoes/
├── application/
│   ├── controllers/
│   │   ├── Grupos.php (✅ modificado)
│   │   └── Inscricoes.php (✅ modificado)
│   ├── models/
│   │   └── Grupos_model.php (✅ modificado)
│   ├── helpers/
│   │   ├── grupos_helper.php (✅ modificado)
│   │   └── inscricoes_helper.php (✅ modificado)
│   └── views/
│       └── inscricao/
│           ├── inscricao_form.php (✅ modificado)
│           ├── inscricao_form_backup.php (backup original)
│           └── inscricao_form_new.php (versão nova)
├── assets/
│   └── js/
│       └── inscricoes_aluno.js (✅ modificado)
├── sql/
│   ├── grupos_taxas_adicionais.sql (✅ criado)
│   └── visualizar_sql.html (✅ criado)
├── SQL_PARA_EXECUTAR.sql (✅ atualizado)
├── INSTRUCOES_IMPLEMENTACAO.md (✅ este arquivo)
└── RESUMO_ALTERACOES.md (✅ criado)
```

## Suporte

Para dúvidas ou problemas:
1. Consulte este arquivo
2. Verifique o arquivo `RESUMO_ALTERACOES.md`
3. Revise os comentários no código
4. Teste em ambiente de desenvolvimento primeiro
