<?php

class Assets
{

    var $css_url = "";

    var $js_url = "";

    var $css = array();

    var $js = array();

    var $line_break = "";

    var $uncache_var = true;

    var $css_media = '';

    var $js_inline = [];
    

    public function __construct($parms)
    {
        $this->css_url = $parms['css_url'];
        $this->js_url = $parms['js_url'];
        $this->line_break = $parms['line_break'];
        $this->uncache_var = $parms['uncache_var'];
    }

    public function css($path, $full_url = false)
    {
        $this->css[$path]['url'] = $path;
        $this->css[$path]['full'] = $full_url;
    }

    public function js($path, $full_url = false, $load_in_footer = false)
    {
        $this->js[$path]['url'] = $path;
        $this->js[$path]['full'] = $full_url;
        $this->js[$path]['footer'] = $load_in_footer;
    }

    public function inline($chave, $valor, $namespace = 'App')
    {
        if (! empty($valor)) {
            $this->js_inline[$namespace][$chave] = $valor;
        } else {
            unset($this->js_inline[$namespace][$chave]);
        }
    }

    public function renderInline($return = false)
    {
        $out = '';
        if (! empty($this->js_inline)) {
            $out .= "<script>";
            foreach ($this->js_inline as $namespace => $attr) {
                $out .= 'window.' . $namespace . ' = window.' . $namespace . ' || {};';
                $out .= $namespace.' = ' . json_encode($attr) . ';';                
            }
            $out .= "</script>";
        }
        if ($return) {
            return $out;
        } else {
            echo $out;
        }
    }

    public function setUncacheVar($var)
    {
        $this->uncache_var = $var;
    }

    public function setCSSMedia($media)
    {
        $this->css_media = $media;
    }

    public function print_view_head($type = null, $return = false)
    {
        if (is_null($type) || $type == "css") {
            foreach ($this->css as $css) {
                if (! $css['full']) {
                    $href = $this->css_url . '/' . $css['url'];
                    if ($this->uncache_var) {
                        $href .= "?" . time();
                    }
                    while (strpos($href, '//') !== false) {
                        $href = str_replace('//', '/', $href);
                    }
                } else {
                    $href = $css['url'];
                }
                $out = '<link rel="stylesheet" ';
                if (! empty($this->css_media)) {
                    $out .= 'media="' . $this->css_media . '"';
                }
                $out .= ' type="text/css" href="' . $href . '"/>';
                $css_out[] = $out;
            }
            if ($return) {
                return implode($this->line_break, $css_out);
            } else {
                echo implode($this->line_break, $css_out);
            }
        }
        if (is_null($type) || $type == "js") {
            foreach ($this->js as $js) {
                if (! $js['footer']) {
                    if (! $js['full']) {
                        $href = $this->js_url . '/' . $js['url'];
                        if ($this->uncache_var && strpos($href, '?')) {
                            $href .= "&" . time();
                        } else if ($this->uncache_var) {
                            $href .= "?" . time();
                        }
                        while (strpos($href, '//') !== false)
                            $href = str_replace('//', '/', $href);
                    } else {

                        $href = $js['url'];
                    }
                    $js_out[] = '<script src="' . $href . '"></script>';
                }
            }
            echo implode($this->line_break, $js_out);
        }
    }

    public function print_view_footer()
    {
        $js_out = array();
        foreach ($this->js as $js) {
            if ($js['footer'] === true) {
                if (! $js['full']) {
                    $href = $this->js_url . '/' . $js['url'];
                    if ($this->uncache_var && strpos($href, '?')) {
                        $href .= "&" . time();
                    } else if ($this->uncache_var) {
                        $href .= "?" . time();
                    }
                    while (strpos($href, '//') !== false)
                        $href = str_replace('//', '/', $href);
                } else {

                    $href = $js['url'];
                }
                $js_out[] = '<script src="' . $href . '"></script>';
            }
        }
        if (count($js_out)) {
            echo implode($this->line_break, $js_out);
        }
    }
}

/* End of file Assets.php */