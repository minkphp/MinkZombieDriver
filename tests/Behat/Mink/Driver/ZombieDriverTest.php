<?php

namespace Tests\Behat\Mink\Driver;

use Behat\Mink\Driver\ZombieDriver,
    Behat\Mink\Driver\NodeJS\Server\ManualStartZombieServer,
    Behat\Mink\Driver\NodeJS\Server\ZombieServer;

/**
 * @group zombiedriver
 */
class ZombieDriverTest extends JavascriptDriverTest
{
    protected static function getDriver()
    {
        if (getenv("NO_AUTO_START") == 1) {
            $server = new ManualStartZombieServer('127.0.0.1', 8124);
        } else {
            $server = new ZombieServer('127.0.0.1', 8124, 'node');
        }

        return new ZombieDriver($server);
    }

    /**
     * As of 0.10.1, zombie.js doesn't support drag'n'drop
     */
    public function testDragDrop() {}

    /**
     * Zombie.js doesn't handle selects without values
     */
    public function testIssue193() {}

    // Zombie.js doesn't support iFrames switching
    public function testIFrame() {}

    // Zombie.js doesn't support window switching
    public function testWindow() {}

    public function testSetUserAgent()
    {
        $this->getSession()->setRequestHeader('user-agent', 'foo bar');
        $this->getSession()->visit($this->pathTo('/headers.php'));
        $this->assertContains('foo bar', $this->getSession()->getPage()->getText());
    }

    /**
     * @expectedException Behat\Mink\Exception\UnsupportedDriverActionException
     */
    public function testNotAllowedHttpHeader()
    {
        $this->getSession()->setRequestHeader('foo', 'bar');
    }

    /**
     */
    public function testWait()
    {
        $this->markTestIncomplete("Zombie automatically waits for events to fire, so the wait test is irrelevant");
    }

    /**
     */
    public function testCookie()
    {
        $this->markTestIncomplete("Zombie adheres to a later cookie spec than PHP, so this test will fail forever");
    }
}
