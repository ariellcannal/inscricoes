<?php

class Ajax extends SYS_Controller
{

    function __construct()
    {
        parent::__construct();
    }

    public function xcrud()
    {
        $this->load->library('session');
        $this->load->helper(array(
            'url',
            'xcrud'
        ));
        Xcrud_config::$scripts_url = base_url('');
        $this->output->set_output(Xcrud::get_requested_instance());
    }

    public function consultaCEP()
    {
        if (empty($this->input->post('cep')) || strlen($this->input->post('cep')) != 8) {
            set_status_header(400);
            exit('CEP Inválido');
        } else {
            $c = curl_init();
            curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 0);
            curl_setopt($c, CURLOPT_TIMEOUT, 3); // timeout in seconds
            curl_setopt($c, CURLOPT_URL, "https://viacep.com.br/ws/" . $this->input->post('cep') . "/json/");
            $dados = json_decode((string) curl_exec($c), true);
            curl_close($c);
            $dados['cidade'] = $dados['localidade'];
            $dados['estado'] = $dados['uf'];
            $dados['cep'] = $this->input->post('cep');
            exit(json_encode($dados));
        }
    }
}

/* End of file ajax.php */
/* Location: ./application/controllers/ajax.php */