<?php

namespace lib;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Log
{
    private $logDir;

    private $mode = 0777;

    private $types = ['debug', 'info', 'warning', 'error'];

    public function __construct()
    {
        $this->logDir = \Init::getConfig('log');
        $this->checkLogFiles();
    }

    private function checkLogFiles()
    {
        if (!is_dir($this->logDir)) {
            @mkdir($this->logDir, $this->mode, true);
        }
        foreach ($this->types as $value) {
            $path = $this->logDir . $value . '.log';
            if (!is_file($path)) {
                $handle = fopen($path, 'a');
                fwrite($handle, '');
                fclose($handle);
            }
        }
    }

    public static function debug($message, $context = [])
    {
        $path   = ROOT_PATH . \Init::getConfig('log') . 'debug-' . date('Ymd', time()) . '.log';
        $logger = new Logger('debug');
        $logger->pushHandler(new StreamHandler($path, Logger::DEBUG));
        return $logger->addRecord(Logger::DEBUG, $message, $context);
    }

    public static function info($message, $context = [])
    {
        $path   = ROOT_PATH . \Init::getConfig('log') . 'info-' . date('Ymd', time()) . '.log';
        $logger = new Logger('info');
        $logger->pushHandler(new StreamHandler($path, Logger::INFO));
        return $logger->addRecord(Logger::INFO, $message, $context);
    }

    public static function warn($message, $context = [])
    {
        $path   = ROOT_PATH . \Init::getConfig('log') . 'warn-' . date('Ymd', time()) . '.log';
        $logger = new Logger('warn');
        $logger->pushHandler(new StreamHandler($path, Logger::WARNING));
        return $logger->addRecord(Logger::WARNING, $message, $context);
    }

    public static function error($message, $context = [])
    {
        $path   = ROOT_PATH . \Init::getConfig('log') . 'error-' . date('Ymd', time()) . '.log';
        $logger = new Logger('error');
        $logger->pushHandler(new StreamHandler($path, Logger::ERROR));
        return $logger->addRecord(Logger::ERROR, $message, $context);
    }
}