<?php
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use CANNALInscricoes\Entities\OperadorasEntity;
use CANNALInscricoes\Entities\RecebiveisEntity;
use CANNALInscricoes\Entities\OperadorasTransacoesEntity;

class Cron extends SYS_Controller
{

    /**
     * Instância do logger da classe.
     *
     * @var Logger
     */
    private Logger $logger;

    function __construct()
    {
        parent::__construct();
        // return;
        if (! is_cli() && ENVIRONMENT != 'development') {
            show_404();
        }
        $this->logger = new Logger('cron', [
            new StreamHandler(APPPATH . 'logs/cron/' . date('Y-m-d') . '.log', Level::Debug)
        ]);
    }

    /**
     * Cria arquivo de lock para evitar execuções simultâneas.
     *
     * @param string $name
     *            Nome do processo.
     *            
     * @return void
     */
    private function setLockFile(string $name): void
    {
        $lockFile = APPPATH . 'cache/cron_' . $name . '.lock';

        // Verifica se já está em execução
        if (file_exists($lockFile)) {
            $lastModified = filemtime($lockFile);
            if ((time() - $lastModified) < 600) { // menos de 10 minutos
                $this->logger->error($name . ' já está em execução.');
                return;
            }
        }
        // Cria lock
        file_put_contents($lockFile, time());
    }

    /**
     * Remove o arquivo de lock do processo.
     *
     * @param string $name
     *            Nome do processo.
     *            
     * @return void
     */
    private function unsetLockFile(string $name): void
    {
        @unlink(APPPATH . 'cache/cron_' . $name . '.lock');
    }

    /*
     *
     * /usr/local/bin/php /home/grupotapa/public_html/grupos/index.php cron hora >> /home/grupotapa/public_html/grupos/application/logs/cron/cron-`date +\%Y-\%m-\%d`.log; echo "" >> /home/grupotapa/public_html/grupos/application/logs/cron/cron-`date +\%Y-\%m-\%d`.log
     */
    function cincoMinutos(): void
    {
        $name = 'cincoMinutos';
        $this->setLogFile($name . '-' . date('Y-m-d'));
        $this->setLockFile($name);
        try {
            $this->_atualizaTransacoesVencidas();
            $this->_atualizaTransacoesRecentes();
        } catch (Exception $e) {
            $this->logger->error($name . ' - ' . $e->getMessage());
        }
        $this->unsetLockFile($name);
    }

    function hora(): void
    {
        $name = 'hora';
        $this->setLogFile($name . '-' . date('Y-m-d'));
        $this->setLockFile($name);
        try {} catch (Exception $e) {
            $this->logger->error($name . ' - ' . $e->getMessage());
        }
        $this->unsetLockFile($name);
    }

    function dia(): void
    {
        $name = 'dia';
        $this->setLogFile($name . '-' . date('Y-m-d'));
        $this->setLockFile($name);
        try {
            $this->sincronizarPrevistosAteHoje();
        } catch (Exception $e) {
            $this->logger->error($name . ' - ' . $e->getMessage());
        }
        $this->unsetLockFile($name);
    }

    function semana(): void
    {
        $name = 'semana';
        $this->setLogFile($name . '-' . date('Y-m-d'));
        $this->setLockFile($name);
        try {} catch (Exception $e) {
            $this->logger->error($name . ' - ' . $e->getMessage());
        }
        $this->unsetLockFile($name);
    }

    function mes(): void
    {
        $name = 'mes';
        $this->setLogFile($name . '-' . date('Y-m-d'));
        $this->setLockFile($name);
        try {} catch (Exception $e) {
            $this->logger->error($name . ' - ' . $e->getMessage());
        }
        $this->unsetLockFile($name);
    }

    function _atualizaTransacoesVencidas(): void
    {
        $this->load->library('controllers/TransacoesLib', null, 'transacoes');
        $this->transacoes->sincronizaTransacoesVencidas();
    }

    function _atualizaTransacoesRecentes(): void
    {
        $this->load->library('controllers/TransacoesLib', null, 'transacoes');
        $this->transacoes->sincronizar(null, 2);
    }

    function sincronizarPrevistosAteHoje(): void
    {
        $this->load->library('controllers/RecebiveisLib', null, 'recebiveis');
        $this->recebiveis->sincronizarPrevistosAteHoje();
    }
}

/* End of file cron.php */
/* Location: ./application/controllers/cron.php */
