<?php
namespace App\Libraries;

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
     */
    private Logger $logger;

    /**
     * Diretório onde os logs serão armazenados.
     */
    private string $directory;

    /**
     * Nome base do arquivo de log.
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
     */
    public function setLogName(string $logName): void
    {
        $this->logName = $logName;
        $this->createLogger();
    }

    /**
     * Escreve uma mensagem no log.
     */
    public function write(string $level, string $message): void
    {
        $this->logger->log(Level::fromName(strtoupper($level)), $message);
    }

    /**
     * Cria a instância do logger com base no nome atual.
     */
    private function createLogger(): void
    {
        $this->logger = new Logger('app');
        $handler = new StreamHandler($this->directory . $this->logName . '.log', Level::Debug);
        $handler->setFormatter(new LineFormatter(null, null, true, true));
        $this->logger->setHandlers([$handler]);
    }
}
