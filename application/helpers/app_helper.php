<?php
if (! function_exists('echo_cli')) {
    
    function echo_cli($txt, $echo_datetime = true, $echo_eol = true)
    {
        if (is_cli()) {
            if ($echo_datetime) {
                echo '[' . date('d/m/Y H:i:s') . '] ';
            }
            echo $txt;
            if ($echo_eol) {
                echo PHP_EOL;
            }
        }
        return false;
    }
}
if (! function_exists('tirarAcentos')) {
    
    function tirarAcentos($string)
    {
        return preg_replace(array(
            "/(á|à|ã|â|ä)/",
            "/(Á|À|Ã|Â|Ä)/",
            "/(é|è|ê|ë)/",
            "/(É|È|Ê|Ë)/",
            "/(í|ì|î|ï)/",
            "/(Í|Ì|Î|Ï)/",
            "/(ó|ò|õ|ô|ö)/",
            "/(Ó|Ò|Õ|Ô|Ö)/",
            "/(ú|ù|û|ü)/",
            "/(Ú|Ù|Û|Ü)/",
            "/(ñ)/",
            "/(Ñ)/"
        ), explode(" ", "a A e E i I o O u U n N"), $string);
    }
}
if (! function_exists('error_on')) {
    
    function error_on()
    {
        ini_set('display_errors', 'On');
        error_reporting(E_ALL);
    }
}