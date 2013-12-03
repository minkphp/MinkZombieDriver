<?php

namespace Tests\Behat\Mink\Driver;

use Behat\Mink\Driver\ZombieDriver,
    Behat\Mink\Driver\NodeJS\Server\ZombieServer;

/**
 * @group zombiedriver
 */
class ZombieDriverTest extends JavascriptDriverTest
{
    protected static function getDriver()
    {
        $server = new ZombieServer('127.0.0.1', 8124, 'node');

        return new ZombieDriver($server);
    }

    public function testWait()
    {
        $this->markTestSkipped('Zombie automatically waits for events to fire, so the wait test is irrelevant');
    }

    public function testVisibility()
    {
        $this->markTestSkipped('Zombie.js doesn\'t support visibility checking');
    }

    public function testDragDrop()
    {
        $this->markTestSkipped('Zombie.js doesn\'t support drag-n-drop operations since v0.10.1');
    }

    public function testIssue193()
    {
        $this->markTestSkipped('Zombie.js doesn\'t handle SELECT without values');
    }

    public function testIFrame()
    {
        $this->markTestSkipped('Zombie.js doesn\'t support iFrames switching');
    }

    public function testSetUserAgent()
    {
        $session = $this->getSession();

        $session->setRequestHeader('user-agent', 'foo bar');
        $session->visit($this->pathTo('/headers.php'));
        $this->assertContains('foo bar', $session->getPage()->getText());
    }

    public function testSetRequestHeader()
    {
        $this->getSession()->setRequestHeader('foo', 'bar');
        $this->getSession()->visit($this->pathTo('/headers.php'));
        $this->assertContains('[HTTP_FOO] => bar', $this->getSession()->getPage()->getText());
    }
}
