<?php
/**
 * {CLASS SUMMARY}
 *
 * Date: 10/6/18
 * Time: 2:34 PM
 * @author Michael Munger <mj@hph.io>
 */

namespace hphio\logging\configurator;


use Monolog\Logger;
use org\bovigo\vfs\vfsStream;
use League\Container\Container;
use PHPUnit\Framework\TestCase;
use Monolog\Handler\StreamHandler;

class loggingConfigReaderTest extends TestCase
{

    protected $container;
    protected $applicationRoot;

    public function setUp() {
        $this->container = new Container;
        $this->container->add(Logger::class);
        $this->container->add(LoggingConfigurator::class)->addArgument($this->container);
        $this->container->add(LoggingConfig::class);

        $this->applicationRoot = vfsStream::setup("appRoot");
    }

    public function testReady() {
        $this->assertContains("vfs", stream_get_wrappers());
    }

    public function testFindService() {
        $this->assertFalse($this->applicationRoot->hasChild('config'));

        $LogConfigurator = $this->container->get(LoggingConfigurator::class);

        $this->assertInstanceOf(LoggingConfigurator::class, $LogConfigurator);

        $LogConfigurator->setAppRoot(vfsStream::url("appRoot"));
        $result = $LogConfigurator->setConfigDir("config");
        $this->assertTrue($result);
        $this->assertTrue($this->applicationRoot->hasChild('config'));
    }

    public function testServiceLoggingConfig() {

        $LogConfigurator = $this->container->get(LoggingConfigurator::class);
        $LogConfigurator->setAppRoot(vfsStream::url("appRoot"));
        $result = $LogConfigurator->setConfigDir("config");

        $this->applicationRoot = vfsStream::copyFromFileSystem(__DIR__ . '/fixtures/appRoot', $this->applicationRoot);

        $this->assertTrue($result);
        
        $this->assertTrue($LogConfigurator->loadLoggingConf());

        $obj = $LogConfigurator->getServiceLogConfig("auth");

        $this->assertInstanceOf(LoggingConfig::class, $obj);

        $this->assertNotFalse($obj);

        $this->assertSame("auth", $obj->name);
        $this->assertSame(true, $obj->enabled);
        $this->assertSame("auth.log", $obj->filename);
        $this->assertSame("vfs://appRoot/log/", $obj->path);

        $obj = $LogConfigurator->getServiceLogConfig("doesnotexist");

        $this->assertFalse($obj);
    }

    public function testGetLogger() {

        $LogConfigurator = $this->container->get(LoggingConfigurator::class);
        $LogConfigurator->setAppRoot(vfsStream::url("appRoot"));
        $LogConfigurator->setConfigDir("config");

        $this->applicationRoot = vfsStream::copyFromFileSystem(__DIR__ . '/fixtures/appRoot', $this->applicationRoot);

        $LogConfigurator->loadLoggingConf();

        $config = $LogConfigurator->getServiceLogConfig("auth");

        $logger = $LogConfigurator->getLogger($config);

//        var_dump($config);
//        var_dump($logger);
//        die(__FILE__ . ":" . __LINE__);

        $logger->info("Test");

        $this->assertFileExists(vfsStream::url("appRoot/log/auth.log"));

        $this->assertGreaterThan(0, stripos(file_get_contents(vfsStream::url("appRoot/log/auth.log")), "Test"));

    }

    public function testConfigNotExistFailure() {
        $LogConfigurator = $this->container->get(LoggingConfigurator::class);
        $LogConfigurator->setAppRoot(vfsStream::url("/"));
        $LogConfigurator->setConfigDir("/home/user/config");

        $this->assertFalse($LogConfigurator->loadLoggingConf());

        $config = $LogConfigurator->getServiceLogConfig("auth");
        $this->assertFalse($config);
    }
}