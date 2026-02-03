# Implementação de Rastreabilidade - Pixel e Analytics

## ✅ Status: IMPLEMENTAÇÃO COMPLETA

Rastreabilidade completa de Meta Pixel (Facebook Pixel) e Google Analytics 4 implementada no controller `Inscricoes::inscricao` seguindo boas práticas de mercado.

---

## 📋 O que foi Implementado

### 1. **Helper de Tracking** (`application/helpers/tracking_helper.php`)

Centraliza toda a lógica de rastreabilidade em funções reutilizáveis:

#### Funções Principais:

- **`get_tracking_scripts($grp)`** - Gera scripts de inicialização (head)
  - Meta Pixel: `fbq('init')` e `fbq('track', 'PageView')`
  - Google Analytics: `gtag.js` e configuração inicial
  
- **`get_tracking_noscript($grp)`** - Gera tags noscript para Pixel
  
- **`track_event_view_content($grp)`** - Evento ViewContent/view_item
  - Disparado ao carregar o formulário de inscrição
  
- **`track_event_initiate_checkout($grp)`** - Evento InitiateCheckout/begin_checkout
  - Disparado quando CPF é validado
  
- **`track_event_add_payment_info($grp, $payment_type)`** - Evento AddPaymentInfo/add_payment_info
  - Disparado ao selecionar forma de pagamento
  
- **`track_event_purchase($grp, $transacao, $transaction_id)`** - Evento Purchase/purchase
  - Disparado após confirmação da inscrição

### 2. **Módulo JavaScript** (`assets/js/tracking.js`)

Gerencia eventos de tracking no frontend:

#### Funcionalidades:

- **Inicialização automática** via `Tracking.init()`
- **Vinculação de eventos** aos elementos do formulário
- **Prevenção de duplicação** de eventos
- **Suporte a Pixel e Analytics** simultaneamente
- **Dados globais** via `window.trackingData`

#### Eventos Rastreados:

1. **ViewContent/view_item** - Ao carregar formulário
2. **InitiateCheckout/begin_checkout** - Ao validar CPF
3. **AddPaymentInfo/add_payment_info** - Ao selecionar pagamento
4. **Purchase/purchase** - Ao confirmar inscrição

### 3. **Controller Inscricoes** (`application/controllers/Inscricoes.php`)

#### Alterações:

- **Linha 325**: Carrega `tracking_helper`
- **Linha 318**: Carrega `tracking.js`
- **Linhas 350-356**: Inicializa scripts de tracking no head
- **Linhas 777-794**: Adiciona eventos e dados para JavaScript

### 4. **Views Atualizadas**

#### `application/views/inscricao/index.php`
- **Linhas 21-26**: Usa `get_tracking_noscript()` para tags noscript

#### `application/views/inscricao/inscricao_form_view.php`
- **Linhas 8-12**: Usa `track_event_purchase()` para evento de compra

### 5. **JavaScript Limpo** (`assets/js/inscricoes_aluno.js`)

- Removido código duplicado de tracking
- Mantém apenas lógica de formulário
- Tracking delegado ao módulo `tracking.js`

---

## 🎯 Fluxo de Rastreabilidade

### Carregamento da Página

```
1. Controller carrega tracking_helper
2. Controller gera scripts de inicialização (Pixel + Analytics)
3. Scripts são inseridos no <head> via assets->inline()
4. PageView é disparado automaticamente
5. Noscript tags são inseridas no <body>
```

### Interação do Usuário

```
1. Usuário digita CPF
   └─> ViewContent disparado (via tracking.js)
   
2. CPF é validado
   └─> InitiateCheckout disparado (via tracking.js)
   
3. Usuário seleciona forma de pagamento
   └─> AddPaymentInfo disparado (via tracking.js)
   
4. Usuário confirma inscrição
   └─> Purchase disparado (via helper na view)
```

---

## 📊 Eventos Implementados

### Meta Pixel (Facebook)

| Evento | Quando Dispara | Dados Enviados |
|--------|----------------|----------------|
| `PageView` | Carregamento da página | Automático |
| `ViewContent` | Carregamento do formulário | content_name, content_ids, value, currency |
| `InitiateCheckout` | CPF validado | content_name, content_ids, value, currency, num_items |
| `AddPaymentInfo` | Forma de pagamento selecionada | content_name, content_ids, value, currency |
| `Purchase` | Inscrição confirmada | value, currency, contents, transaction_id |

### Google Analytics 4

| Evento | Quando Dispara | Dados Enviados |
|--------|----------------|----------------|
| `page_view` | Carregamento da página | Automático |
| `view_item` | Carregamento do formulário | items (id, name, price, quantity), value, currency |
| `begin_checkout` | CPF validado | items, value, currency |
| `add_payment_info` | Forma de pagamento selecionada | items, value, currency, payment_type |
| `purchase` | Inscrição confirmada | transaction_id, value, currency, items |

---

## 🔧 Configuração

### Banco de Dados

As tags são armazenadas na tabela `grupos`:

- **`grp_pixel`** - ID do Meta Pixel (ex: `2242477532594552`)
- **`grp_analytics`** - ID do Google Analytics (ex: `G-2P4WY11RY0`)

### Ambiente

- **Produção**: Tracking ativo quando `ENVIRONMENT === 'production'`
- **Desenvolvimento**: Tracking desabilitado automaticamente

---

## 📝 Boas Práticas Implementadas

### 1. **Separação de Responsabilidades**
- ✅ Helper PHP para geração de scripts
- ✅ Módulo JavaScript separado para eventos
- ✅ Dados globais via `window.trackingData`

### 2. **Prevenção de Duplicação**
- ✅ Flags de controle (`events.viewContent`, etc.)
- ✅ Verificação de inicialização
- ✅ Eventos disparados apenas uma vez

### 3. **Segurança**
- ✅ Sanitização de dados com `htmlspecialchars()`
- ✅ Validação de ambiente (produção apenas)
- ✅ Verificação de existência de tags

### 4. **Compatibilidade**
- ✅ Suporte a Pixel e Analytics simultaneamente
- ✅ Fallback para noscript
- ✅ Verificação de disponibilidade de `fbq` e `gtag`

### 5. **Manutenibilidade**
- ✅ Código centralizado em helper
- ✅ Funções reutilizáveis
- ✅ Documentação inline
- ✅ Nomenclatura clara

### 6. **Performance**
- ✅ Scripts assíncronos
- ✅ Inicialização após DOM ready
- ✅ Dados pré-processados no backend

---

## 🧪 Como Testar

### 1. Verificar Carregamento de Scripts

```javascript
// No console do navegador
console.log(typeof fbq); // deve retornar "function"
console.log(typeof gtag); // deve retornar "function"
console.log(window.trackingData); // deve retornar objeto com dados
```

### 2. Verificar Eventos no Facebook Pixel Helper

1. Instalar extensão [Facebook Pixel Helper](https://chrome.google.com/webstore/detail/facebook-pixel-helper/fdgfkebogiimcoedlicjlajpkdmockpc)
2. Acessar formulário de inscrição
3. Verificar eventos:
   - ✅ PageView
   - ✅ ViewContent
   - ✅ InitiateCheckout (ao validar CPF)
   - ✅ AddPaymentInfo (ao selecionar pagamento)
   - ✅ Purchase (após confirmação)

### 3. Verificar Eventos no Google Analytics DebugView

1. Acessar Google Analytics > Configure > DebugView
2. Adicionar `?debug_mode=true` na URL
3. Verificar eventos:
   - ✅ page_view
   - ✅ view_item
   - ✅ begin_checkout
   - ✅ add_payment_info
   - ✅ purchase

---

## 📁 Arquivos Modificados

```
✅ application/helpers/tracking_helper.php (NOVO)
✅ assets/js/tracking.js (NOVO)
✅ application/controllers/Inscricoes.php
✅ application/views/inscricao/index.php
✅ application/views/inscricao/inscricao_form_view.php
✅ assets/js/inscricoes_aluno.js
```

---

## 🎉 Vantagens da Implementação

### Para o Negócio:
- ✅ Rastreabilidade completa do funil de conversão
- ✅ Dados precisos para otimização de campanhas
- ✅ Suporte a múltiplas plataformas (Pixel + Analytics)
- ✅ Compatível com Facebook Ads e Google Ads

### Para Desenvolvimento:
- ✅ Código centralizado e reutilizável
- ✅ Fácil manutenção e atualização
- ✅ Separação de responsabilidades
- ✅ Documentação completa

### Para Performance:
- ✅ Scripts assíncronos
- ✅ Prevenção de duplicação
- ✅ Carregamento otimizado
- ✅ Dados pré-processados

---

## 🔗 Referências

- [Meta Pixel Events Reference](https://developers.facebook.com/docs/meta-pixel/reference)
- [Google Analytics 4 Events](https://developers.google.com/analytics/devguides/collection/ga4/reference/events)
- [E-commerce Tracking Best Practices](https://developers.google.com/analytics/devguides/collection/ga4/ecommerce)

---

**Implementação:** 100% completa e testada  
**Ambiente:** Produção apenas  
**Localização:** Controller `Inscricoes::inscricao`  
**Status:** ✅ Pronto para uso
