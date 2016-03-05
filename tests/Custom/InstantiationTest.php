<?php

namespace Behat\Mink\Tests\Driver\Custom;

use Behat\Mink\Driver\ZombieDriver;

class InstantiationTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiateWithServer()
    {
        $server = $this->prophesize('\Behat\Mink\Driver\NodeJS\Server\ZombieServer')->reveal();

        $driver = new ZombieDriver($server);

        $this->assertSame($server, $driver->getServer());
    }

    public function testInstantiateWithHost()
    {
        $driver = new ZombieDriver('10.0.0.1');

        $this->assertInstanceOf('\Behat\Mink\Driver\NodeJS\Server\ZombieServer', $driver->getServer());
        $this->assertEquals('10.0.0.1', $driver->getServer()->getHost());
        $this->assertSame(8124, $driver->getServer()->getPort());
    }

    public function testInstantiateWithHostAndExplicitNullPort()
    {
        $driver = new ZombieDriver('127.0.0.1', null);

        $this->assertInstanceOf('\Behat\Mink\Driver\NodeJS\Server\ZombieServer', $driver->getServer());
        $this->assertEquals('127.0.0.1', $driver->getServer()->getHost());
        $this->assertSame(8124, $driver->getServer()->getPort());
    }

    public function testInstantiateWithHostAndPort()
    {
        $driver = new ZombieDriver('127.0.0.1', 8125);

        $this->assertInstanceOf('\Behat\Mink\Driver\NodeJS\Server\ZombieServer', $driver->getServer());
        $this->assertEquals('127.0.0.1', $driver->getServer()->getHost());
        $this->assertSame(8125, $driver->getServer()->getPort());
    }
}
