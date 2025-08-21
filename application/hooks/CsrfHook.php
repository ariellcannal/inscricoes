<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CsrfHook {
    /**
     * Allow AJAX requests to send the CSRF token via header.
     * Moves X-CSRF-TOKEN header into \$_POST so CI's native
     * CSRF protection can validate it automatically.
     */
    public function add_token_from_header()
    {
        $tokenName = config_item('csrf_token_name');
        $headerToken = isset($_SERVER['HTTP_X_CSRF_TOKEN']) ? $_SERVER['HTTP_X_CSRF_TOKEN'] : null;
        if ($headerToken && empty($_POST[$tokenName])) {
            $_POST[$tokenName] = $headerToken;
        }
    }
}

