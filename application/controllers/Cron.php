<?php
use CANNALLogs\Logs;
use CANNALInscricoes\Entities\OperadorasEntity;
use CANNALInscricoes\Entities\RecebiveisEntity;
use CANNALInscricoes\Entities\OperadorasTransacoesEntity;

class Cron extends SYS_Controller
{

    var $logs;

    function __construct()
    {
        parent::__construct();
        //return;
        if (! is_cli() && ENVIRONMENT != 'development') {
            show_404();
        }
        $this->logs = new Logs('cron');
    }

    private function setLockFile($name)
    {
        $lockFile = APPPATH . 'cache/cron_' . $name . '.lock';

        // Verifica se já está em execução
        if (file_exists($lockFile)) {
            $lastModified = filemtime($lockFile);
            if ((time() - $lastModified) < 600) { // menos de 10 minutos
                $this->logs->write('ERROR', $name . ' já está em execução.');
                return;
            }
        }
        // Cria lock
        file_put_contents($lockFile, time());
    }

    private function unsetLockFile($name)
    {
        @unlink(APPPATH . 'cache/cron_' . $name . '.lock');
    }

    /*
     *
     * /usr/local/bin/php /home/grupotapa/public_html/grupos/index.php cron hora >> /home/grupotapa/public_html/grupos/application/logs/cron/cron-`date +\%Y-\%m-\%d`.log; echo "" >> /home/grupotapa/public_html/grupos/application/logs/cron/cron-`date +\%Y-\%m-\%d`.log
     */
    function cincoMinutos()
    {
        $name = 'cincoMinutos';
        $this->logs->setLogName($name . '-' . date('Y-m-d'));
        $this->setLockFile($name);
        try {
            $this->_atualizaTransacoesVencidas();
            $this->_atualizaTransacoesRecentes();
        } catch (Exception $e) {
            $this->logs->write('ERROR', $name . ' - ' . $e->getMessage());
        }
        $this->unsetLockFile($name);
    }

    function hora()
    {
        $name = 'hora';
        $this->logs->setLogName($name . '-' . date('Y-m-d'));
        $this->setLockFile($name);
        try {} catch (Exception $e) {
            $this->logs->write('ERROR', $name . ' - ' . $e->getMessage());
        }
        $this->unsetLockFile($name);
    }

    function dia()
    {
        $name = 'dia';
        $this->logs->setLogName($name . '-' . date('Y-m-d'));
        $this->setLockFile($name);
        try {
            $this->sincronizarPrevistosAteHoje();
        } catch (Exception $e) {
            $this->logs->write('ERROR', $name . ' - ' . $e->getMessage());
        }
        $this->unsetLockFile($name);
    }

    function semana()
    {
        $name = 'semana';
        $this->logs->setLogName($name . '-' . date('Y-m-d'));
        $this->setLockFile($name);
        try {} catch (Exception $e) {
            $this->logs->write('ERROR', $name . ' - ' . $e->getMessage());
        }
        $this->unsetLockFile($name);
    }

    function mes()
    {
        $name = 'mes';
        $this->logs->setLogName($name . '-' . date('Y-m-d'));
        $this->setLockFile($name);
        try {} catch (Exception $e) {
            $this->logs->write('ERROR', $name . ' - ' . $e->getMessage());
        }
        $this->unsetLockFile($name);
    }

    function _atualizaTransacoesVencidas()
    {
        $this->load->library('controllers/TransacoesLib', null, 'transacoes');
        $this->transacoes->sincronizaTransacoesVencidas();
    }

    function _atualizaTransacoesRecentes()
    {
        $this->load->library('controllers/TransacoesLib', null, 'transacoes');
        $this->transacoes->sincronizar(null, 2);
    }

    function sincronizarPrevistosAteHoje()
    {
        $this->load->library('controllers/RecebiveisLib', null, 'recebiveis');
        $this->recebiveis->sincronizarPrevistosAteHoje();
    }
}

/* End of file cron.php */
/* Location: ./application/controllers/cron.php */