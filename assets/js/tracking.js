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
        pageView: false,
        lead: false,
        initiateCheckout: false,
        addPaymentInfo: false,
        purchase: false
    };
    
    function isProduction() {
        return (typeof App !== 'undefined' && App.environment === 'production') || (typeof App === 'undefined');
    }
    
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

        if (typeof jQuery !== 'undefined') {
            $(document).on('xcrudafterrequest xcrudinit', bindEvents);
        }
    }
    
    /**
     * Vincula eventos aos elementos do formulário
     */
    function bindEvents() {
        // PageView
        trackPageView();
        
        // Evento Lead - quando CPF é validado
        var cpfInput = document.getElementById('alu_cpf');
        if (cpfInput) {
            cpfInput.removeEventListener('blur', cpfBlurHandler);
            cpfInput.addEventListener('blur', cpfBlurHandler);
        }
        
        // Evento InitiateCheckout - quando select2 de fop é clicado
        var fopSelect = document.getElementById('fop');
        if (fopSelect) {
            if (typeof jQuery !== 'undefined') {
                $(fopSelect).off('select2:open.tracking');
                $(fopSelect).on('select2:open.tracking', function() {
                    trackInitiateCheckout();
                });
            }
            fopSelect.removeEventListener('change', paymentChangeHandler);
            fopSelect.addEventListener('change', paymentChangeHandler);
        }
        
        // Trigger purchase if elements are present (PIX or success message)
        var pixCard = document.querySelector('.card.text-center.border-primary');
        var successAlert = document.querySelector('.alert.alert-success');
        if (pixCard || (successAlert && successAlert.textContent.includes('Inscrição realizada'))) {
            trackPurchase();
        }
    }

    function cpfBlurHandler() {
        if (this.value && !this.classList.contains('is-invalid')) {
            trackLead();
        }
    }

    function paymentChangeHandler() {
        if (this.value) {
            var paymentType = getPaymentType(this.value);
            trackAddPaymentInfo(paymentType);
        }
    }
    
    /**
     * Dispara evento PageView
     */
    function trackPageView() {
        if (events.pageView) return;
        events.pageView = true;
        
        if (!isProduction()) return;
        
        // Meta Pixel
        if (typeof fbq !== 'undefined') {
            fbq('track', 'PageView');
        }
        
        // Google Analytics
        if (typeof gtag !== 'undefined') {
            gtag('event', 'page_view');
        }
    }
    
    /**
     * Dispara evento Lead
     */
    function trackLead() {
        if (events.lead) return;
        events.lead = true;
        
        if (!isProduction()) return;
        
        // Dados do grupo (devem estar disponíveis globalmente)
        if (typeof window.trackingData === 'undefined') return;
        
        var data = window.trackingData;
        
        // Meta Pixel
        if (typeof fbq !== 'undefined' && data.pixel) {
            fbq('track', 'Lead', {
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
            gtag('event', 'generate_lead', {
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
     * Dispara evento InitiateCheckout
     */
    function trackInitiateCheckout() {
        if (events.initiateCheckout) return;
        events.initiateCheckout = true;
        
        if (!isProduction()) return;
        
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
        
        if (!isProduction()) return;
        
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
     * Dispara evento Purchase
     */
    function trackPurchase() {
        if (events.purchase) return;
        events.purchase = true;
        
        if (!isProduction()) return;
        
        if (typeof window.trackingData === 'undefined' || typeof window.transactionId === 'undefined') return;
        
        var data = window.trackingData;
        var transactionId = window.transactionId;
        
        // Meta Pixel
        if (typeof fbq !== 'undefined' && data.pixel) {
            fbq('track', 'Purchase', {
                value: data.value,
                currency: 'BRL',
                content_name: data.content_name,
                content_ids: [data.content_id],
                content_type: 'product',
                contents: [{
                    id: data.content_id,
                    quantity: 1,
                    item_price: data.value
                }],
                num_items: 1
            });
        }
        
        // Google Analytics
        if (typeof gtag !== 'undefined' && data.analytics) {
            gtag('event', 'purchase', {
                transaction_id: transactionId,
                value: data.value,
                currency: 'BRL',
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
        trackLead: trackLead,
        trackInitiateCheckout: trackInitiateCheckout,
        trackAddPaymentInfo: trackAddPaymentInfo,
        trackPurchase: trackPurchase
    };
})();

// Inicializar automaticamente
Tracking.init();
