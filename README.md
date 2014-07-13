Mink Zombie.js Driver
=====================

[![Latest Stable Version](https://poser.pugx.org/behat/mink-zombie-driver/v/stable.png)](https://packagist.org/packages/behat/mink-zombie-driver)
[![Total Downloads](https://poser.pugx.org/behat/mink-zombie-driver/downloads.png)](https://packagist.org/packages/behat/mink-zombie-driver)
[![Build Status](https://travis-ci.org/Behat/MinkZombieDriver.png?branch=master)](http://travis-ci.org/Behat/MinkZombieDriver)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/Behat/MinkZombieDriver/badges/quality-score.png?s=2e166ed0bc0d8bfde427fb9af2a93aaabbc09723)](https://scrutinizer-ci.com/g/Behat/MinkZombieDriver/)
[![Code Coverage](https://scrutinizer-ci.com/g/Behat/MinkZombieDriver/badges/coverage.png?s=f271ed5a203ed036c6ce093e5269b60a76951f4f)](https://scrutinizer-ci.com/g/Behat/MinkZombieDriver/)

Installation & Compatibility
----------------------------

You need a working installation of [NodeJS](http://nodejs.org/) and
[npm](https://npmjs.org/). Install the
[zombie.js](http://zombie.labnotes.org) library through npm:

``` bash
npm install -g zombie
```

There are some compatibility issues with older versions of zombie.js.
Some are more or less PHP specific and kinda hard to resolve. If you
want to be 100% on the safe side, please use __version 2.0.0-alpha1 or
higher__.

Use [Composer](http://getcomposer.org/) to install all required PHP dependencies:

``` json
{
    "require": {
        "behat/mink":               "~1.5",
        "behat/mink-zombie-driver": "~1.1"
    }
}
```

``` bash
$> curl http://getcomposer.org/installer | php
$> php composer.phar install
```

Usage Example
-------------

``` php
<?php

use Behat\Mink\Mink,
    Behat\Mink\Session,
    Behat\Mink\Driver\ZombieDriver,
    Behat\Mink\Driver\NodeJS\Server\ZombieServer;

$host       = '127.0.0.1';
$port       = '8124';
$nodeBinary = '/usr/local/bin/node';

$mink = new Mink(array(
    'zombie' => new Session(new ZombieDriver(new ZombieServer(
        $host, $port, $nodeBinary
    ))),
));

$mink->setDefaultSessionName('zombie');

$session = $mink->getSession();
$session->visit('http://example.org');

$page = $session->getPage();
$elem = $page->find('css', 'h1');

echo $elem->getText();
```

Copyright
---------

Copyright (c) 2011-2012 Pascal Cremer <b00gizm@gmail.com>

Maintainers
-----------

* Alexander Obuhovich [aik099](http://github.com/aik099)
