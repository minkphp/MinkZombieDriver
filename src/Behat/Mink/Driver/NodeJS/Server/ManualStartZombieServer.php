<?php

namespace Behat\Mink\Driver\NodeJS\Server;

use Behat\Mink\Driver\NodeJS\Connection;
use Behat\Mink\Driver\NodeJS\Server\ZombieServer;

/**
 * Use this class when you want to use an existing zombie server
 *
 */
class ManualStartZombieServer extends ZombieServer
{
    /**
     * Starts the server process
     *
     * @param   Symfony\Component\Process\Process  $process  A process object
     *
     * @throws  \RuntimeException
     */
    public function start(Process $process = null)
    {
        $this->connection = new Connection($this->host, $this->port);
        $this->checkAvailability();
    }

    /**
     * Stops the server process
     * @link    https://github.com/symfony/Process
     */
    public function stop()
    {
        $this->connection = null;
    }

    /**
     * Restarts the server process
     */
    public function restart()
    {
        $this->stop();
        $this->start();
    }

    /**
     * Checks whether server connection and server process are still available
     * and running
     *
     * @throws  \RuntimeException
     */
    protected function checkAvailability()
    {
        if (null === $this->connection) {
            throw new \RuntimeException(
                "No connection available. Did you start the server?"
            );
        }
    }
}
