<?php
namespace CANNALInscricoes\Libraries;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Level;
use Monolog\Formatter\LineFormatter;

/**
 * Gerencia registros de log utilizando o Monolog.
 */
class Logs
{
    /**
     * Instância do logger.
     *
     * @var Logger
     */
    private Logger $logger;

    /**
     * Diretório onde os logs serão armazenados.
     *
     * @var string
     */
    private string $directory;

    /**
     * Nome base do arquivo de log.
     *
     * @var string
     */
    private string $logName;

    /**
     * Construtor.
     *
     * @param string $folder  Pasta dentro de application/logs.
     * @param string $logName Nome base do arquivo de log.
     */
    public function __construct(string $folder = '', string $logName = '')
    {
        $base = rtrim(APPPATH . 'logs/', '/') . '/';
        if ($folder !== '') {
            $base .= trim($folder, '/') . '/';
        }
        if (! is_dir($base)) {
            mkdir($base, 0777, true);
        }
        $this->directory = $base;
        $this->logName = $logName !== '' ? $logName : date('Y-m-d');
        $this->createLogger();
    }

    /**
     * Define o nome do arquivo de log.
     *
     * @param string $logName Nome do arquivo de log.
     *
     * @return void
     */
    public function setLogName(string $logName): void
    {
        $this->logName = $logName;
        $this->createLogger();
    }

    /**
     * Escreve uma mensagem no log.
     *
     * @param string $level   Nível do log.
     * @param string $message Mensagem a ser registrada.
     *
     * @return void
     */
    public function write(string $level, string $message): void
    {
        $this->logger->log(Level::fromName(strtoupper($level)), $message);
    }

    /**
     * Cria a instância do logger com base no nome atual.
     *
     * @return void
     */
    private function createLogger(): void
    {
        $this->logger = new Logger('app');
        $handler = new StreamHandler($this->directory . $this->logName . '.log', Level::Debug);
        $handler->setFormatter(new LineFormatter(null, null, true, true));
        $this->logger->setHandlers([$handler]);
    }
}
