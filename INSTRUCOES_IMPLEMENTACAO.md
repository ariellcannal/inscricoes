# Instruções para Implementação das Taxas Adicionais

## Resumo das Alterações Realizadas

### 1. Banco de Dados

#### Tabela `grupos_taxas_adicionais`
- **Arquivo SQL**: `/sql/grupos_taxas_adicionais.sql`
- Estrutura idêntica a `grupos_formas` com prefixo `gtx_`
- Campo adicional: `gtx_primeiraParcela` (boolean, padrão true)

#### Alteração na Tabela `operadoras_transacoes`
- **Arquivo SQL**: `/sql/alter_operadoras_transacoes.sql`
- Nova coluna: `otr_taxaAdicional` (referência para `grupos_taxas_adicionais`)

### 2. Backend - Models

#### Grupos_model.php
Adicionados dois métodos:
- `getTaxasAdicionais($grp_id, $linkOculto = null, $publico = true)` - Busca taxas de um grupo
- `getTaxaAdicional($gtx_id)` - Busca uma taxa específica

### 3. Backend - Controllers

#### Grupos.php
Adicionado nested table para gerenciar taxas adicionais:
- Configuração similar ao nested de `grupos_formas`
- Campos: valor, parcelas, primeira parcela, ordem, público, etc.
- Callbacks: `BI_gtx` e `BU_gtx`

### 4. Backend - Helpers

#### grupos_helper.php
Adicionados callbacks:
- `BI_gtx($postdata, $xcrud)` - Before Insert para taxas
- `BU_gtx($postdata, $gtx_id, $xcrud)` - Before Update para taxas

## Implementações Necessárias

### 1. Controller Inscricoes.php - Método `inscricao()`

#### Adicionar após linha 429 (após processamento das formas de pagamento):

```php
// Processar taxas adicionais
$taxasAdicionais = [];
$taxasPrimeiraParcela = [];
$taxasPosteriores = [];

foreach ($this->grupos_model->getTaxasAdicionais($this->vars['grp']['grp_id'], $_GET['utm_content']) as $taxa) {
    $exibe = true;
    if ($taxa['gtx_parcelas'] > 1) {
        $taxa['gtx_aceitaCartao'] = 1;
    }
    
    $gtx_valorTotal_original = $taxa['gtx_valorTotal'];
    
    if (!empty($ins['ins_valorDesconto'])) {
        $taxa['gtx_valorTotal'] = $taxa['gtx_valorTotal'] - $ins['ins_valorDesconto'];
    }
    
    if ($exibe) {
        $taxaData = [
            'gtx_id' => $taxa['gtx_id'],
            'gtx_comentario' => $taxa['gtx_comentario'],
            'gtx_descricao' => $taxa['gtx_descricao'],
            'gtx_valorTotal' => $taxa['gtx_valorTotal'],
            'gtx_parcelas' => $taxa['gtx_parcelas'],
            'gtx_aceitaCartao' => $taxa['gtx_aceitaCartao'],
            'gtx_primeiraParcela' => $taxa['gtx_primeiraParcela']
        ];
        
        $taxasAdicionais[] = $taxaData;
        
        if ($taxa['gtx_primeiraParcela'] == '1') {
            $taxasPrimeiraParcela[] = $taxaData;
        } else {
            $taxasPosteriores[] = $taxaData;
        }
    }
}

// Criar campos dinâmicos para cada taxa posterior
foreach ($taxasPosteriores as $idx => $taxa) {
    $prefix = 'taxa_' . $taxa['gtx_id'];
    
    // Criar opções de pagamento para esta taxa
    $formasTaxa = [];
    for ($i = 1; $i <= $taxa['gtx_parcelas']; $i++) {
        $key = $taxa['gtx_id'] . '_' . $i . '_' . $taxa['gtx_aceitaCartao'] . '_' . ($taxa['gtx_valorTotal'] / $i);
        $formasTaxa[$key] = ($taxa['gtx_comentario'] != "" ? $taxa['gtx_comentario'] . ': ' : '') . 
                            $i . ' parcela' . (($i > 1) ? 's' : '') . ' de R$ ' . 
                            number_format($taxa['gtx_valorTotal'] / $i, 2, ',', '.') . ' ' . 
                            ($taxa['gtx_aceitaCartao'] ? "no cartão de crédito" : "no PIX");
    }
    
    // Criar campos para esta taxa
    $xcrud->create_field($prefix . '_fop', 'select', null, $formasTaxa);
    $xcrud->create_field($prefix . '_rec_cartao', 'text', null, ['inputmode' => 'numeric']);
    $xcrud->create_field($prefix . '_rec_cartaoCodigo', 'text', null, ['inputmode' => 'numeric']);
    $xcrud->create_field($prefix . '_rec_cartaoValidadeMes', 'select', date('m'), $mes);
    $xcrud->create_field($prefix . '_rec_cartaoValidadeAno', 'select', date('Y'), $ano);
    $xcrud->create_field($prefix . '_rec_cartaoNome', 'text');
    $xcrud->create_field($prefix . '_rec_cartaoCPF', 'text');
    $xcrud->create_field($prefix . '_alu_cartoes', 'select', "novo", ["novo" => 'Inserir dados do cartão', "mesmo" => 'Utilizar mesmo cartão']);
    
    // Labels
    $xcrud->label($prefix . '_fop', 'Forma de Pagamento');
    $xcrud->label($prefix . '_rec_cartao', 'Número do Cartão');
    $xcrud->label($prefix . '_rec_cartaoCodigo', 'CVV');
    $xcrud->label($prefix . '_rec_cartaoValidadeMes', 'Validade - Mês');
    $xcrud->label($prefix . '_rec_cartaoValidadeAno', 'Validade - Ano');
    $xcrud->label($prefix . '_rec_cartaoNome', 'Nome (Como Impresso no Cartão)');
    $xcrud->label($prefix . '_rec_cartaoCPF', 'CPF do Titular');
    $xcrud->label($prefix . '_alu_cartoes', 'Selecione um cartão');
    
    // Atributos
    $xcrud->set_attr($prefix . '_fop', ['id' => $prefix . '_fop', 'class' => 'taxa-fop']);
    $xcrud->set_attr($prefix . '_rec_cartao', ['id' => $prefix . '_rec_cartao', 'autocomplete' => 'cc-number']);
    $xcrud->set_attr($prefix . '_rec_cartaoCodigo', ['id' => $prefix . '_rec_cartaoCodigo', 'autocomplete' => 'cc-csc']);
    $xcrud->set_attr($prefix . '_rec_cartaoNome', ['id' => $prefix . '_rec_cartaoNome', 'autocomplete' => 'cc-name']);
    $xcrud->set_attr($prefix . '_rec_cartaoCPF', ['id' => $prefix . '_rec_cartaoCPF']);
    $xcrud->set_attr($prefix . '_alu_cartoes', ['id' => $prefix . '_alu_cartoes']);
    
    // Máscaras
    $xcrud->mask($prefix . '_rec_cartaoCPF', '000.000.000-00');
}

// Passar variáveis para a view
$xcrud->set_var('taxasAdicionais', json_encode($taxasAdicionais));
$xcrud->set_var('taxasPrimeiraParcela', json_encode($taxasPrimeiraParcela));
$xcrud->set_var('taxasPosteriores', json_encode($taxasPosteriores));
```

### 2. Helper inscricoes_helper.php - Atualizar callbacks

#### No callback `BI_inscricao_aluno` ou `AI_inscricao_aluno`:

Adicionar lógica para processar as taxas adicionais após a criação/atualização da inscrição:

```php
// Processar taxas adicionais
$taxasPostdata = [];
foreach ($postdata->to_array() as $k => $v) {
    if (strpos($k, 'taxa_') === 0) {
        $taxasPostdata[$k] = $v;
    }
}

if (!empty($taxasPostdata)) {
    $ci->load->library('controllers/OperadorasLib', null, 'operadoras');
    
    // Agrupar por taxa
    $taxasAgrupadas = [];
    foreach ($taxasPostdata as $key => $value) {
        preg_match('/^taxa_(\d+)_(.+)$/', $key, $matches);
        if (count($matches) === 3) {
            $taxaId = $matches[1];
            $campo = $matches[2];
            $taxasAgrupadas[$taxaId][$campo] = $value;
        }
    }
    
    // Processar cada taxa
    foreach ($taxasAgrupadas as $taxaId => $dadosTaxa) {
        $taxa = $ci->grupos_model->getTaxaAdicional($taxaId);
        
        if (!$taxa) continue;
        
        // Extrair dados da forma de pagamento
        $fopData = explode('_', $dadosTaxa['fop']);
        $gfp_id = $fopData[0];
        $parcelas = $fopData[1];
        $aceitaCartao = $fopData[2];
        $valorParcela = $fopData[3];
        
        $valorTotal = $valorParcela * $parcelas;
        
        // Se for cartão e não for "utilizar mesmo cartão"
        if ($aceitaCartao && $dadosTaxa['alu_cartoes'] !== 'mesmo') {
            // Criar transação de cartão
            $dadosTransacao = [
                'ins_id' => $ins_id,
                'taxa_id' => $taxaId,
                'tipo' => 'cartao',
                'parcelas' => $parcelas,
                'valorTotal' => $valorTotal,
                'cartao' => [
                    'numero' => $dadosTaxa['rec_cartao'],
                    'cvv' => $dadosTaxa['rec_cartaoCodigo'],
                    'mes' => $dadosTaxa['rec_cartaoValidadeMes'],
                    'ano' => $dadosTaxa['rec_cartaoValidadeAno'],
                    'nome' => $dadosTaxa['rec_cartaoNome'],
                    'cpf' => $dadosTaxa['rec_cartaoCPF']
                ]
            ];
            
            // Processar transação (implementar lógica específica)
            // $ci->operadoras->processarTransacaoTaxa($dadosTransacao);
        } elseif (!$aceitaCartao) {
            // Criar transação PIX
            $dadosTransacao = [
                'ins_id' => $ins_id,
                'taxa_id' => $taxaId,
                'tipo' => 'pix',
                'valorTotal' => $valorTotal
            ];
            
            // Processar transação PIX (implementar lógica específica)
            // $ci->operadoras->processarTransacaoTaxa($dadosTransacao);
        }
    }
}
```

### 3. View inscricao_form.php - Atualizar HTML

#### Substituir a seção de "Pagamento" (linhas 103-154) por:

```php
<div class="row inscricao_form">
    <h4 class="col-md-12 mt-3">
        Pagamento do Curso<br>
        <?php if($processoSeletivo){?>
        <small>O seu pagamento só será processado após sua aprovação no processo seletivo.</small>
        <?php }?>
    </h4>
</div>

<!-- Abas de Pagamento do Curso -->
<ul class="nav nav-tabs" id="pagamentoCursoTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="pix-tab" data-bs-toggle="tab" data-bs-target="#pix-panel" type="button" role="tab">PIX</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="cartao-tab" data-bs-toggle="tab" data-bs-target="#cartao-panel" type="button" role="tab">Cartão de Crédito</button>
    </li>
</ul>

<div class="tab-content" id="pagamentoCursoTabsContent">
    <!-- Aba PIX -->
    <div class="tab-pane fade show active" id="pix-panel" role="tabpanel">
        <div class="row mb-3 mt-3">
            <div class="col-md-6 form-group">
                <?php echo $this->open_label_tag('inscricoes.fop','label').$this->fields_output['inscricoes.fop']['label'].$this->close_tag('label')?>
                <?php 
                // Filtrar apenas opções PIX
                echo $this->fields_output['inscricoes.fop']['field']
                ?>
            </div>
        </div>
        
        <?php 
        // Exibir taxas de primeira parcela
        $taxasPrimeiraParcela = json_decode($this->get_var('taxasPrimeiraParcela'), true);
        if (!empty($taxasPrimeiraParcela)) {
            foreach ($taxasPrimeiraParcela as $taxa) {
                echo '<p class="text-muted">+ R$ ' . number_format($taxa['gtx_valorTotal'], 2, ',', '.') . 
                     ' referente à ' . $taxa['gtx_descricao'] . ' cobrado no PIX</p>';
            }
        }
        ?>
    </div>
    
    <!-- Aba Cartão -->
    <div class="tab-pane fade" id="cartao-panel" role="tabpanel">
        <div class="row mb-3 mt-3">
            <div class="col-md-6 form-group">
                <?php echo $this->open_label_tag('inscricoes.fop','label').$this->fields_output['inscricoes.fop']['label'].$this->close_tag('label')?>
                <?php 
                // Filtrar apenas opções Cartão
                echo $this->fields_output['inscricoes.fop']['field']
                ?>
            </div>
            <div class="mesmo_cartao col-md-6 form-group" style="display: none;">
                <?php echo $this->open_label_tag('inscricoes.alu_cartoes','label').$this->fields_output['inscricoes.alu_cartoes']['label'].$this->close_tag('label')?><br>
                <?php echo $this->fields_output['inscricoes.alu_cartoes']['field']?>
            </div>
        </div>
        
        <?php 
        // Exibir taxas de primeira parcela
        if (!empty($taxasPrimeiraParcela)) {
            foreach ($taxasPrimeiraParcela as $taxa) {
                echo '<p class="text-muted">+ R$ ' . number_format($taxa['gtx_valorTotal'], 2, ',', '.') . 
                     ' referente à ' . $taxa['gtx_descricao'] . ' cobrado na primeira parcela</p>';
            }
        }
        ?>
        
        <div class="row dados_cartao">
            <div class="col-md-12 ">
                <h5 class="mt-3">Dados do Cartão de Crédito</h5>
            </div>
            <div class="col-md-8 row">
                <div class="col-md-6 mb-3 form-group">
                    <?php echo $this->open_label_tag('inscricoes.rec_cartaoNome','label').$this->fields_output['inscricoes.rec_cartaoNome']['label'].$this->close_tag('label')?>
                    <?php echo $this->fields_output['inscricoes.rec_cartaoNome']['field']?>
                </div>
                <div class="col-md-6 mb-3 form-group">
                    <?php echo $this->open_label_tag('inscricoes.rec_cartao','label').$this->fields_output['inscricoes.rec_cartao']['label'].$this->close_tag('label')?>
                    <?php echo $this->fields_output['inscricoes.rec_cartao']['field']?>
                </div>
                <div class="col-md-4 mb-3 form-group">
                    <label>Validade <small>(MM / AAAA)</small></label>
                    <div class="input-group">
                        <?php echo $this->fields_output['inscricoes.rec_cartaoValidadeMes']['field']?>
                        <?php echo $this->fields_output['inscricoes.rec_cartaoValidadeAno']['field']?>
                    </div>
                    <?php echo $this->fields_output['inscricoes.rec_cartaoValidade']['field']?>
                </div>
                <div class="col-md-2 mb-3 form-group">
                    <?php echo $this->open_label_tag('inscricoes.rec_cartaoCodigo','label').$this->fields_output['inscricoes.rec_cartaoCodigo']['label'].$this->close_tag('label')?>
                    <?php echo $this->fields_output['inscricoes.rec_cartaoCodigo']['field']?>
                </div>
                <div class="col-md-3 mb-3 form-group">
                    <?php echo $this->open_label_tag('inscricoes.rec_cartaoCPF','label').$this->fields_output['inscricoes.rec_cartaoCPF']['label'].$this->close_tag('label')?>
                    <?php echo $this->fields_output['inscricoes.rec_cartaoCPF']['field']?>
                </div>
            </div>
            <div class="col-md-4">
                <div class="row cartao"></div>
            </div>
        </div>
    </div>
</div>

<!-- Taxas Adicionais (gtx_primeiraParcela = false) -->
<?php 
$taxasPosteriores = json_decode($this->get_var('taxasPosteriores'), true);
if (!empty($taxasPosteriores)) {
    foreach ($taxasPosteriores as $taxa) {
        $prefix = 'taxa_' . $taxa['gtx_id'];
?>
<div class="row inscricao_form mt-4">
    <h4 class="col-md-12">
        Pagamento de <?php echo $taxa['gtx_descricao']; ?><br>
        <small>Taxa adicional</small>
    </h4>
</div>

<ul class="nav nav-tabs" id="<?php echo $prefix; ?>Tabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="<?php echo $prefix; ?>-pix-tab" data-bs-toggle="tab" data-bs-target="#<?php echo $prefix; ?>-pix-panel" type="button" role="tab">PIX</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="<?php echo $prefix; ?>-cartao-tab" data-bs-toggle="tab" data-bs-target="#<?php echo $prefix; ?>-cartao-panel" type="button" role="tab">Cartão de Crédito</button>
    </li>
</ul>

<div class="tab-content">
    <!-- Aba PIX da Taxa -->
    <div class="tab-pane fade show active" id="<?php echo $prefix; ?>-pix-panel" role="tabpanel">
        <div class="row mb-3 mt-3">
            <div class="col-md-6 form-group">
                <?php echo $this->open_label_tag('inscricoes.' . $prefix . '_fop','label').$this->fields_output['inscricoes.' . $prefix . '_fop']['label'].$this->close_tag('label')?>
                <?php echo $this->fields_output['inscricoes.' . $prefix . '_fop']['field']?>
            </div>
        </div>
    </div>
    
    <!-- Aba Cartão da Taxa -->
    <div class="tab-pane fade" id="<?php echo $prefix; ?>-cartao-panel" role="tabpanel">
        <div class="row mb-3 mt-3">
            <div class="col-md-6 form-group">
                <?php echo $this->open_label_tag('inscricoes.' . $prefix . '_fop','label').$this->fields_output['inscricoes.' . $prefix . '_fop']['label'].$this->close_tag('label')?>
                <?php echo $this->fields_output['inscricoes.' . $prefix . '_fop']['field']?>
            </div>
            <div class="col-md-6 form-group">
                <?php echo $this->open_label_tag('inscricoes.' . $prefix . '_alu_cartoes','label').$this->fields_output['inscricoes.' . $prefix . '_alu_cartoes']['label'].$this->close_tag('label')?>
                <?php echo $this->fields_output['inscricoes.' . $prefix . '_alu_cartoes']['field']?>
            </div>
        </div>
        
        <div class="row dados_cartao_<?php echo $prefix; ?>" style="display: none;">
            <div class="col-md-12">
                <h5 class="mt-3">Dados do Cartão de Crédito</h5>
            </div>
            <div class="col-md-8 row">
                <div class="col-md-6 mb-3 form-group">
                    <?php echo $this->open_label_tag('inscricoes.' . $prefix . '_rec_cartaoNome','label').$this->fields_output['inscricoes.' . $prefix . '_rec_cartaoNome']['label'].$this->close_tag('label')?>
                    <?php echo $this->fields_output['inscricoes.' . $prefix . '_rec_cartaoNome']['field']?>
                </div>
                <div class="col-md-6 mb-3 form-group">
                    <?php echo $this->open_label_tag('inscricoes.' . $prefix . '_rec_cartao','label').$this->fields_output['inscricoes.' . $prefix . '_rec_cartao']['label'].$this->close_tag('label')?>
                    <?php echo $this->fields_output['inscricoes.' . $prefix . '_rec_cartao']['field']?>
                </div>
                <div class="col-md-4 mb-3 form-group">
                    <label>Validade <small>(MM / AAAA)</small></label>
                    <div class="input-group">
                        <?php echo $this->fields_output['inscricoes.' . $prefix . '_rec_cartaoValidadeMes']['field']?>
                        <?php echo $this->fields_output['inscricoes.' . $prefix . '_rec_cartaoValidadeAno']['field']?>
                    </div>
                </div>
                <div class="col-md-2 mb-3 form-group">
                    <?php echo $this->open_label_tag('inscricoes.' . $prefix . '_rec_cartaoCodigo','label').$this->fields_output['inscricoes.' . $prefix . '_rec_cartaoCodigo']['label'].$this->close_tag('label')?>
                    <?php echo $this->fields_output['inscricoes.' . $prefix . '_rec_cartaoCodigo']['field']?>
                </div>
                <div class="col-md-3 mb-3 form-group">
                    <?php echo $this->open_label_tag('inscricoes.' . $prefix . '_rec_cartaoCPF','label').$this->fields_output['inscricoes.' . $prefix . '_rec_cartaoCPF']['label'].$this->close_tag('label')?>
                    <?php echo $this->fields_output['inscricoes.' . $prefix . '_rec_cartaoCPF']['field']?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php 
    }
}
?>

<div class="row inscricao_form">
    <div class="col-md-12 mb-5">
        <?php echo $this->render_button('save_edit','save','view','btn btn-lg btn-primary col-md-12 spinner_control','','create,edit','',['data-recaptcha'=>'confirmar_inscricao']); ?>
    </div>
</div>
```

### 4. JavaScript - Atualizar inscricoes_aluno.js

Adicionar lógica para:
- Controlar exibição de abas PIX/Cartão
- Mostrar/ocultar campos de cartão conforme seleção "utilizar mesmo cartão"
- Validar campos de taxas adicionais

### 5. Lógica de Cobrança

#### Para taxas com `gtx_primeiraParcela = true`:
- **PIX**: Somar valor da taxa ao valor do PIX do curso
- **Cartão**: Cobrar cada taxa como transação independente à vista

#### Para taxas com `gtx_primeiraParcela = false`:
- Cobrar conforme forma de pagamento escolhida pelo aluno
- Cada taxa gera transação independente no banco
- Opção "utilizar mesmo cartão" usa dados do cartão principal

## Próximos Passos

1. Executar SQLs no banco de dados
2. Implementar lógica de processamento de transações das taxas
3. Atualizar JavaScript para controle de interface
4. Testar fluxo completo de inscrição com taxas
5. Validar cálculos e geração de transações
