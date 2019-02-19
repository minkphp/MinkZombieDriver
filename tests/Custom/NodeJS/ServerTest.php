<?php

namespace Behat\Mink\Tests\Driver\Custom\NodeJS;

use Behat\Mink\Driver\NodeJS\Connection;
use Behat\Mink\Driver\NodeJS\Server as BaseServer;
use Behat\Mink\Tests\Driver\TestCase;

class TestServer extends BaseServer
{
    public $serverScript = null;

    protected function doEvalJS(Connection $conn, $str, $returnType = 'js')
    {
        return '';
    }

    protected function getServerScript()
    {
        return <<<JS
var zombie  = require('zombie')
  , host    = process.env.HOST
  , port    = process.env.PORT
  , options = process.env.OPTIONS ? JSON.parse(process.env.OPTIONS) : {};
JS;
    }

    protected function createTemporaryServer()
    {
        $path = parent::createTemporaryServer();

        $this->serverScript = file_get_contents($path);
        unlink($path);

        return '/path/to/server';
    }
}

class LegacyTestServer extends TestServer
{
    protected function getServerScript()
    {
        return <<<JS
var zombie = require('%modules_path%zombie')
  , host   = '%host%'
  , port   = %port%;
JS;
    }
}

class ServerTest extends TestCase
{
    public function testCreateServerWithDefaults()
    {
        $server = new TestServer();

        $this->assertEquals('127.0.0.1', $server->getHost());
        $this->assertEquals(8124, $server->getPort());
        $this->assertEquals('node', $server->getNodeBin());
        $this->assertEquals('/path/to/server', $server->getServerPath());
        $this->assertEquals(2000000, $server->getThreshold());
        $this->assertEquals('', $server->getNodeModulesPath());
        $this->assertEquals(array(), $server->getOptions());

        $expected = <<<JS
var zombie  = require('zombie')
  , host    = process.env.HOST
  , port    = process.env.PORT
  , options = process.env.OPTIONS ? JSON.parse(process.env.OPTIONS) : {};
JS;
        $this->assertEquals($expected, $server->serverScript);
    }

    /**
     * @group legacy
     */
    public function testCreateServerWithPlaceholders()
    {
        $server = new LegacyTestServer();

        $expected = <<<JS
var zombie = require('zombie')
  , host   = '127.0.0.1'
  , port   = 8124;
JS;
        $this->assertEquals($expected, $server->serverScript);
    }

    public function testCreateCustomServer()
    {
        $server = new TestServer(
            '123.123.123.123',
            1234,
            null,
            null,
            5000000,
            '../../',
            array('waitDuration' => '15s')
        );

        $this->assertEquals('123.123.123.123', $server->getHost());
        $this->assertEquals(1234, $server->getPort());
        $this->assertEquals('node', $server->getNodeBin());
        $this->assertEquals('/path/to/server', $server->getServerPath());
        $this->assertEquals(5000000, $server->getThreshold());
        $this->assertEquals('../../', $server->getNodeModulesPath());
        $this->assertEquals(array('waitDuration' => '15s'), $server->getOptions());

        $expected = <<<JS
var zombie  = require('zombie')
  , host    = process.env.HOST
  , port    = process.env.PORT
  , options = process.env.OPTIONS ? JSON.parse(process.env.OPTIONS) : {};
JS;
        $this->assertEquals($expected, $server->serverScript);
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Unable to change host of a running server.
     */
    public function testSetHostOnRunningServer()
    {
        $server = $this->getRunningServer();
        $server->setHost('123.123.123.123');
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Unable to change port of a running server.
     */
    public function testSetPortOnRunningServer()
    {
        $server = $this->getRunningServer();
        $server->setPort(1234);
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Unable to change node bin of a running server.
     */
    public function testSetNodeBinOnRunningServer()
    {
        $server = $this->getRunningServer();
        $server->setNodeBin('test');
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Unable to change server path of a running server.
     */
    public function testSetServerPathOnRunningServer()
    {
        $server = $this->getRunningServer();
        $server->setServerPath('test');
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Unable to change threshold of a running server.
     */
    public function testSetThresholdOnRunningServer()
    {
        $server = $this->getRunningServer();
        $server->setThreshold('test');
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Unable to change options of a running server.
     */
    public function testSetOptionsOnRunningServer()
    {
        $server = $this->getRunningServer();
        $server->setOptions(array('waitDuration' => '15s'));
    }

    /**
     * @group legacy
     */
    public function testCreateCustomServerWithPlaceholders()
    {
        $server = new LegacyTestServer(
            '123.123.123.123',
            1234,
            null,
            null,
            5000000,
            '../../'
        );

        $expected = <<<JS
var zombie = require('../../zombie')
  , host   = '123.123.123.123'
  , port   = 1234;
JS;
        $this->assertEquals($expected, $server->serverScript);

    }

    public function testSetNodeModulesPath()
    {
        $server = new TestServer();
        $server->setNodeModulesPath('../../');

        $this->assertEquals('../../', $server->getNodeModulesPath());
    }

    /**
     * @expectedException  \InvalidArgumentException
     */
    public function testSetNodeModulesPathWithInvalidPath()
    {
        $server = new TestServer();
        $server->setNodeModulesPath('/does/not/exist/');
    }

    /**
     * @expectedException  \InvalidArgumentException
     */
    public function testSetNodeModulesPathWithoutTrailingSlash()
    {
        $server = new TestServer();
        $server->setNodeModulesPath('../..');
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Unable to change node modules path of a running server.
     */
    public function testSetNodeModulesPathOnRunningServer()
    {
        $server = $this->getRunningServer();
        $server->setNodeModulesPath('../../');
    }

    /**
     * @expectedException  \InvalidArgumentException
     */
    public function testCreateServerWithInvalidNodeModulesPath()
    {
        new TestServer('127.0.0.1', 8124, null, null, 2000000, '../..');
    }

    /**
     * @expectedException  \RuntimeException
     */
    public function testStartServerWithNonExistingServerScript()
    {
        $server = new TestServer('127.0.0.1', 8124, null, '/does/not/exist');
        $server->start();
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Server did not respond in time: (1) [Stopped]
     */
    public function testStartServerThatDoesNotRespondInTime()
    {
        $process = $this->getNotRespondingServerProcessMock();
        $process->expects($this->once())
                ->method('start');

        $serverPath = __DIR__.'/server-fixtures/test_server.js';
        $server = new TestServer('127.0.0.1', 8124, null, $serverPath, 10000);

        $server->start($process);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Server process has been terminated: (1) [TROLOLOLO]
     */
    public function testStartServerThatWasTerminated()
    {
        $process = $this->getTerminatedServerProcessMock();
        $process->expects($this->once())
                ->method('start');

        $serverPath = __DIR__.'/server-fixtures/test_server.js';
        $server = new TestServer('127.0.0.1', 8124, null, $serverPath, 10000);

        $server->start($process);
    }

    public function testStartServerSuccessfully()
    {
        try {
            $server = $this->getRunningServer();

            $this->assertInstanceOf(
                'Behat\Mink\Driver\NodeJS\Connection',
                $server->getConnection()
            );
        } catch (\RuntimeException $ex) {
            $this->fail('No exception should have been thrown here');
        }
    }

    public function testStopServer()
    {
        $host = '127.0.0.1';
        $port = 8124;

        $process = $this->getWorkingServerProcessMock($host, $port);
        $process->expects($this->atLeastOnce())
                ->method('stop');

        $serverPath = __DIR__.'/server-fixtures/test_server.js';
        $server = new TestServer($host, $port, null, $serverPath, 10000);
        $server->start($process);
        $server->stop();
    }

    public function testIsRunning()
    {
        $host = '127.0.0.1';
        $port = 8124;

        $serverPath = __DIR__.'/server-fixtures/test_server.js';
        $server = new TestServer($host, $port, null, $serverPath, 10000);

        $this->assertFalse($server->isRunning());

        $process = $this->getWorkingServerProcessMock($host, $port);
        $server->start($process);

        $this->assertTrue($server->isRunning());

        $process = $this->getTerminatedServerProcessMock();

        try {
            $server->start($process);
        } catch (\RuntimeException $ex) {
            // ignore error
        }

        $this->assertFalse($server->isRunning());
    }

    protected function getNotRespondingServerProcessMock()
    {
        $process = $this->getMockBuilder('Symfony\Component\Process\Process')
                        ->disableOriginalConstructor()
                        ->getMock();

        $process->expects($this->any())
                ->method('isRunning')
                ->will($this->returnValue(true));

        $process->expects($this->any())
                ->method('getOutput')
                ->will($this->returnValue(''));

        $process->expects($this->any())
                ->method('getExitCode')
                ->will($this->returnValue(1));

        return $process;
    }

    protected function getTerminatedServerProcessMock()
    {
        $process = $this->getMockBuilder('Symfony\Component\Process\Process')
                        ->disableOriginalConstructor()
                        ->getMock();

        $process->expects($this->any())
                ->method('isRunning')
                ->will($this->returnValue(false));

        $process->expects($this->any())
                ->method('getErrorOutput')
                ->will($this->returnValue('TROLOLOLO'));

        $process->expects($this->any())
                ->method('getExitCode')
                ->will($this->returnValue(1));

        return $process;
    }

    protected function getRunningServer()
    {
        $host = '127.0.0.1';
        $port = 8124;

        $process = $this->getWorkingServerProcessMock($host, $port);
        $process->expects($this->once())
            ->method('start');

        $serverPath = __DIR__.'/server-fixtures/test_server.js';
        $server = new TestServer($host, $port, null, $serverPath, 10000);
        $server->start($process);

        return $server;
    }

    protected function getWorkingServerProcessMock($host, $port)
    {
        $process = $this->getMockBuilder('Symfony\Component\Process\Process')
                        ->disableOriginalConstructor()
                        ->getMock();

        $process->expects($this->any())
                ->method('isRunning')
                ->will($this->returnValue(true));

        $process->expects($this->once())
                ->method('getOutput')
                ->will($this->returnValue(sprintf("server started on %s:%s", $host, $port)));

        return $process;
    }
}
