Mink Zombie.js Driver
=====================

- [![Build Status](https://secure.travis-ci.org/Behat/MinkZombieDriver.png?branch=master)](http://travis-ci.org/Behat/MinkZombieDriver)

Usage Example
-------------

``` php
<?php

use Behat\Mink\Mink,
    Behat\Mink\Session,
    Behat\Mink\Driver\ZombieDriver,
    Behat\Mink\Driver\NodeJS\Server\ZombieServer;

$startUrl = 'http://example.com';

$mink = new Mink(array(
    'zombie' => new Session(new ZombieDriver(ZombieServer(
        $host, $port, $nodeBinary
    ))),
));

$mink->getSession('zombie')->getPage()->findLink('Chat')->click();
```

Installation
------------

``` bash
npm install -g zombie@0.12.15
```

(Due to unresolved compatibility issues with newer versions of
Zombie.js, **we strongly encourage you to not upgrade to a version newer
than v0.13.0** for now.)

``` json
{
    "require": {
        "behat/mink":               "1.4.*",
        "behat/mink-zombie-driver": "*"
    }
}
```

``` bash
curl http://getcomposer.org/installer | php
php composer.phar install
```

Copyright
---------

Copyright (c) 2012 Pascal Cremer <b00gizm@gmail.com>

Maintainers
-----------

* Pascal Cremer [b00gizm](http://github.com/b00gizm)
