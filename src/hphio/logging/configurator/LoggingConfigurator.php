<?php
/**
 * {CLASS SUMMARY}
 *
 * Date: 10/6/18
 * Time: 2:43 PM
 * @author Michael Munger <mj@hph.io>
 */

namespace hphio\logging\configurator;


use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class LoggingConfigurator
{
    public $applicationRoot = null;
    public $configDir       = null;
    public $configs         = null;
    private $container      = null;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function setAppRoot($directory) {
        $this->applicationRoot = $directory;
    }

    public function setConfigDir($configDirectory) {
        $this->configDir = $this->applicationRoot . "/" . $configDirectory;

        if(file_exists($this->configDir) === false ) mkdir($this->configDir);

        return (file_exists($this->configDir) && is_writable($this->configDir));
    }

    public function loadLoggingConf() {
        $this->configs = json_decode(file_get_contents($this->configDir . '/logging.json'));
        return (json_last_error() === JSON_ERROR_NONE);
    }

    public function getServiceLogConfig($logName) {

        foreach($this->configs->services as $logObject) {

            $config = $this->container->get(LoggingConfig::class);
            $config->importJSON($logObject);

            if($logObject->name === $logName) return $config;
        }

        return false;
    }

    /**
     * @param LoggingConfig $config
     * @throws \Exception
     */

    public function getLogger(LoggingConfig $config) {

        $log = new Logger($config->name);
        $log->pushHandler(new StreamHandler($config->logPath(), Logger::INFO));
        return $log;
    }
}