<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Tracking Helper
 * 
 * Centraliza a lógica de rastreabilidade de Pixel e Analytics
 * seguindo boas práticas de mercado.
 * 
 * @package    Inscricoes
 * @subpackage Helpers
 * @category   Tracking
 * @author     Manus
 */

if (!function_exists('get_tracking_scripts')) {
    /**
     * Gera scripts de tracking (Pixel e Analytics) para o head
     * 
     * @param array $grp Dados do grupo
     * @return string HTML com scripts de tracking
     */
    function get_tracking_scripts($grp) {
        if (ENVIRONMENT !== 'production') {
            return '';
        }
        
        $scripts = [];
        
        // Meta Pixel (Facebook Pixel)
        if (!empty($grp['grp_pixel'])) {
            $pixel_id = htmlspecialchars($grp['grp_pixel'], ENT_QUOTES, 'UTF-8');
            $scripts[] = <<<HTML
<!-- Meta Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '{$pixel_id}');
fbq('track', 'PageView');
</script>
<!-- End Meta Pixel Code -->
HTML;
        }
        
        // Google Analytics 4
        if (!empty($grp['grp_analytics'])) {
            $analytics_id = htmlspecialchars($grp['grp_analytics'], ENT_QUOTES, 'UTF-8');
            $scripts[] = <<<HTML
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id={$analytics_id}"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '{$analytics_id}');
</script>
<!-- End Google tag -->
HTML;
        }
        
        return implode("\n", $scripts);
    }
}

if (!function_exists('get_tracking_noscript')) {
    /**
     * Gera noscript tags para tracking (apenas Pixel)
     * 
     * @param array $grp Dados do grupo
     * @return string HTML com noscript tags
     */
    function get_tracking_noscript($grp) {
        if (ENVIRONMENT !== 'production' || empty($grp['grp_pixel'])) {
            return '';
        }
        
        $pixel_id = htmlspecialchars($grp['grp_pixel'], ENT_QUOTES, 'UTF-8');
        
        return <<<HTML
<!-- Meta Pixel Code -->
<noscript>
    <img height="1" width="1" style="display:none" 
         src="https://www.facebook.com/tr?id={$pixel_id}&ev=PageView&noscript=1" />
</noscript>
<!-- End Meta Pixel Code -->
HTML;
    }
}

if (!function_exists('track_event_view_content')) {
    /**
     * Gera script para evento ViewContent
     * 
     * @param array $grp Dados do grupo
     * @return string JavaScript para tracking
     */
    function track_event_view_content($grp) {
        if (ENVIRONMENT !== 'production') {
            return '';
        }
        
        $scripts = [];
        
        // Meta Pixel - ViewContent
        if (!empty($grp['grp_pixel'])) {
            $content_name = htmlspecialchars($grp['grp_nomePublico'], ENT_QUOTES, 'UTF-8');
            $content_ids = json_encode([$grp['grp_id']]);
            $value = number_format((float)$grp['grp_valor'], 2, '.', '');
            
            $scripts[] = <<<JS
// Meta Pixel - ViewContent
if (typeof fbq !== 'undefined') {
    fbq('track', 'ViewContent', {
        content_name: '{$content_name}',
        content_ids: {$content_ids},
        content_type: 'product',
        value: {$value},
        currency: 'BRL'
    });
}
JS;
        }
        
        // Google Analytics - view_item
        if (!empty($grp['grp_analytics'])) {
            $item_name = htmlspecialchars($grp['grp_nomePublico'], ENT_QUOTES, 'UTF-8');
            $item_id = $grp['grp_id'];
            $value = number_format((float)$grp['grp_valor'], 2, '.', '');
            
            $scripts[] = <<<JS
// Google Analytics - view_item
if (typeof gtag !== 'undefined') {
    gtag('event', 'view_item', {
        currency: 'BRL',
        value: {$value},
        items: [{
            item_id: '{$item_id}',
            item_name: '{$item_name}',
            price: {$value},
            quantity: 1
        }]
    });
}
JS;
        }
        
        if (empty($scripts)) {
            return '';
        }
        
        return '<script>' . "\n" . implode("\n\n", $scripts) . "\n" . '</script>';
    }
}

if (!function_exists('track_event_initiate_checkout')) {
    /**
     * Gera script para evento InitiateCheckout
     * 
     * @param array $grp Dados do grupo
     * @return string JavaScript para tracking
     */
    function track_event_initiate_checkout($grp) {
        if (ENVIRONMENT !== 'production') {
            return '';
        }
        
        $scripts = [];
        
        // Meta Pixel - InitiateCheckout
        if (!empty($grp['grp_pixel'])) {
            $content_name = htmlspecialchars($grp['grp_nomePublico'], ENT_QUOTES, 'UTF-8');
            $content_ids = json_encode([$grp['grp_id']]);
            $value = number_format((float)$grp['grp_valor'], 2, '.', '');
            
            $scripts[] = <<<JS
// Meta Pixel - InitiateCheckout
if (typeof fbq !== 'undefined') {
    fbq('track', 'InitiateCheckout', {
        content_name: '{$content_name}',
        content_ids: {$content_ids},
        content_type: 'product',
        value: {$value},
        currency: 'BRL',
        num_items: 1
    });
}
JS;
        }
        
        // Google Analytics - begin_checkout
        if (!empty($grp['grp_analytics'])) {
            $item_name = htmlspecialchars($grp['grp_nomePublico'], ENT_QUOTES, 'UTF-8');
            $item_id = $grp['grp_id'];
            $value = number_format((float)$grp['grp_valor'], 2, '.', '');
            
            $scripts[] = <<<JS
// Google Analytics - begin_checkout
if (typeof gtag !== 'undefined') {
    gtag('event', 'begin_checkout', {
        currency: 'BRL',
        value: {$value},
        items: [{
            item_id: '{$item_id}',
            item_name: '{$item_name}',
            price: {$value},
            quantity: 1
        }]
    });
}
JS;
        }
        
        if (empty($scripts)) {
            return '';
        }
        
        return '<script>' . "\n" . implode("\n\n", $scripts) . "\n" . '</script>';
    }
}

if (!function_exists('track_event_add_payment_info')) {
    /**
     * Gera script para evento AddPaymentInfo
     * 
     * @param array $grp Dados do grupo
     * @param string $payment_type Tipo de pagamento (pix ou credit_card)
     * @return string JavaScript para tracking
     */
    function track_event_add_payment_info($grp, $payment_type = 'credit_card') {
        if (ENVIRONMENT !== 'production') {
            return '';
        }
        
        $scripts = [];
        
        // Meta Pixel - AddPaymentInfo
        if (!empty($grp['grp_pixel'])) {
            $content_name = htmlspecialchars($grp['grp_nomePublico'], ENT_QUOTES, 'UTF-8');
            $content_ids = json_encode([$grp['grp_id']]);
            $value = number_format((float)$grp['grp_valor'], 2, '.', '');
            
            $scripts[] = <<<JS
// Meta Pixel - AddPaymentInfo
if (typeof fbq !== 'undefined') {
    fbq('track', 'AddPaymentInfo', {
        content_name: '{$content_name}',
        content_ids: {$content_ids},
        content_type: 'product',
        value: {$value},
        currency: 'BRL'
    });
}
JS;
        }
        
        // Google Analytics - add_payment_info
        if (!empty($grp['grp_analytics'])) {
            $item_name = htmlspecialchars($grp['grp_nomePublico'], ENT_QUOTES, 'UTF-8');
            $item_id = $grp['grp_id'];
            $value = number_format((float)$grp['grp_valor'], 2, '.', '');
            $payment_type_safe = htmlspecialchars($payment_type, ENT_QUOTES, 'UTF-8');
            
            $scripts[] = <<<JS
// Google Analytics - add_payment_info
if (typeof gtag !== 'undefined') {
    gtag('event', 'add_payment_info', {
        currency: 'BRL',
        value: {$value},
        payment_type: '{$payment_type_safe}',
        items: [{
            item_id: '{$item_id}',
            item_name: '{$item_name}',
            price: {$value},
            quantity: 1
        }]
    });
}
JS;
        }
        
        if (empty($scripts)) {
            return '';
        }
        
        return '<script>' . "\n" . implode("\n\n", $scripts) . "\n" . '</script>';
    }
}

if (!function_exists('track_event_purchase')) {
    /**
     * Gera script para evento Purchase
     * 
     * @param array $grp Dados do grupo
     * @param object $transacao Objeto da transação
     * @param string $transaction_id ID da transação
     * @return string JavaScript para tracking
     */
    function track_event_purchase($grp, $transacao, $transaction_id) {
        if (ENVIRONMENT !== 'production') {
            return '';
        }
        
        $scripts = [];
        $noscripts = [];
        
        $value = number_format((float)$transacao->getValorBruto(), 2, '.', '');
        
        // Meta Pixel - Purchase
        if (!empty($grp['grp_pixel'])) {
            $pixel_id = htmlspecialchars($grp['grp_pixel'], ENT_QUOTES, 'UTF-8');
            $content_name = htmlspecialchars($grp['grp_nomePublico'], ENT_QUOTES, 'UTF-8');
            $content_ids = json_encode([$grp['grp_id']]);
            $contents = json_encode([
                [
                    'id' => $grp['grp_id'],
                    'quantity' => 1,
                    'item_price' => (float)$value
                ]
            ]);
            
            $scripts[] = <<<JS
// Meta Pixel - Purchase
if (typeof fbq !== 'undefined') {
    fbq('track', 'Purchase', {
        value: {$value},
        currency: 'BRL',
        content_name: '{$content_name}',
        content_ids: {$content_ids},
        content_type: 'product',
        contents: {$contents},
        num_items: 1
    });
}
JS;
            
            $contents_encoded = urlencode($contents);
            $noscripts[] = <<<HTML
<noscript>
    <img height="1" width="1" style="display:none"
         src="https://www.facebook.com/tr?id={$pixel_id}&ev=Purchase&cd[value]={$value}&cd[currency]=BRL&cd[content_type]=product&cd[contents]={$contents_encoded}&noscript=1" />
</noscript>
HTML;
        }
        
        // Google Analytics - purchase
        if (!empty($grp['grp_analytics'])) {
            $item_name = htmlspecialchars($grp['grp_nomePublico'], ENT_QUOTES, 'UTF-8');
            $item_id = $grp['grp_id'];
            $transaction_id_safe = htmlspecialchars($transaction_id, ENT_QUOTES, 'UTF-8');
            
            $scripts[] = <<<JS
// Google Analytics - purchase
if (typeof gtag !== 'undefined') {
    gtag('event', 'purchase', {
        transaction_id: '{$transaction_id_safe}',
        value: {$value},
        currency: 'BRL',
        items: [{
            item_id: '{$item_id}',
            item_name: '{$item_name}',
            price: {$value},
            quantity: 1
        }]
    });
}
JS;
        }
        
        if (empty($scripts)) {
            return '';
        }
        
        $output = '<script>' . "\n" . implode("\n\n", $scripts) . "\n" . '</script>';
        
        if (!empty($noscripts)) {
            $output .= "\n" . implode("\n", $noscripts);
        }
        
        return $output;
    }
}
