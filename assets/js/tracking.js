/**
 * Tracking Module
 * 
 * Gerencia eventos de rastreabilidade (Pixel e Analytics)
 * seguindo boas práticas de mercado.
 * 
 * @package    Inscricoes
 * @category   Tracking
 * @author     Manus
 */

var Tracking = (function() {
    'use strict';
    
    var initialized = false;
    var events = {
        viewContent: false,
        initiateCheckout: false,
        addPaymentInfo: false
    };
    
    /**
     * Inicializa o módulo de tracking
     */
    function init() {
        if (initialized) return;
        initialized = true;
        
        // Aguardar carregamento do DOM
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', bindEvents);
        } else {
            bindEvents();
        }
    }
    
    /**
     * Vincula eventos aos elementos do formulário
     */
    function bindEvents() {
        // Evento InitiateCheckout - quando CPF é validado
        var cpfInput = document.getElementById('alu_cpf');
        if (cpfInput) {
            cpfInput.addEventListener('blur', function() {
                if (this.value && !this.classList.contains('is-invalid')) {
                    trackInitiateCheckout();
                }
            });
        }
        
        // Evento AddPaymentInfo - quando forma de pagamento é selecionada
        var fopSelect = document.getElementById('fop');
        if (fopSelect) {
            fopSelect.addEventListener('change', function() {
                if (this.value) {
                    var paymentType = getPaymentType(this.value);
                    trackAddPaymentInfo(paymentType);
                }
            });
        }
    }
    
    /**
     * Dispara evento InitiateCheckout
     */
    function trackInitiateCheckout() {
        if (events.initiateCheckout) return;
        events.initiateCheckout = true;
        
        if (App.environment !== 'production') return;
        
        // Dados do grupo (devem estar disponíveis globalmente)
        if (typeof window.trackingData === 'undefined') return;
        
        var data = window.trackingData;
        
        // Meta Pixel
        if (typeof fbq !== 'undefined' && data.pixel) {
            fbq('track', 'InitiateCheckout', {
                content_name: data.content_name,
                content_ids: [data.content_id],
                content_type: 'product',
                value: data.value,
                currency: 'BRL',
                num_items: 1
            });
        }
        
        // Google Analytics
        if (typeof gtag !== 'undefined' && data.analytics) {
            gtag('event', 'begin_checkout', {
                currency: 'BRL',
                value: data.value,
                items: [{
                    item_id: data.content_id,
                    item_name: data.content_name,
                    price: data.value,
                    quantity: 1
                }]
            });
        }
    }
    
    /**
     * Dispara evento AddPaymentInfo
     * @param {string} paymentType - Tipo de pagamento (pix ou credit_card)
     */
    function trackAddPaymentInfo(paymentType) {
        if (events.addPaymentInfo) return;
        events.addPaymentInfo = true;
        
        if (App.environment !== 'production') return;
        
        if (typeof window.trackingData === 'undefined') return;
        
        var data = window.trackingData;
        
        // Meta Pixel
        if (typeof fbq !== 'undefined' && data.pixel) {
            fbq('track', 'AddPaymentInfo', {
                content_name: data.content_name,
                content_ids: [data.content_id],
                content_type: 'product',
                value: data.value,
                currency: 'BRL'
            });
        }
        
        // Google Analytics
        if (typeof gtag !== 'undefined' && data.analytics) {
            gtag('event', 'add_payment_info', {
                currency: 'BRL',
                value: data.value,
                payment_type: paymentType,
                items: [{
                    item_id: data.content_id,
                    item_name: data.content_name,
                    price: data.value,
                    quantity: 1
                }]
            });
        }
    }
    
    /**
     * Extrai tipo de pagamento da forma selecionada
     * @param {string} fopValue - Valor do select de forma de pagamento
     * @returns {string} - 'pix' ou 'credit_card'
     */
    function getPaymentType(fopValue) {
        if (!fopValue) return 'unknown';
        
        var parts = fopValue.split('_');
        if (parts.length < 3) return 'unknown';
        
        var aceitaCartao = parts[2];
        return aceitaCartao === '1' ? 'credit_card' : 'pix';
    }
    
    // API pública
    return {
        init: init,
        trackInitiateCheckout: trackInitiateCheckout,
        trackAddPaymentInfo: trackAddPaymentInfo
    };
})();

// Inicializar automaticamente
if (typeof App !== 'undefined') {
    Tracking.init();
}
