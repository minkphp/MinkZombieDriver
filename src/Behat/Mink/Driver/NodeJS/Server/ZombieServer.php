<?php

namespace Behat\Mink\Driver\NodeJS\Server;

use Behat\Mink\Driver\NodeJS\Connection;
use Behat\Mink\Driver\NodeJS\Server;

class ZombieServer extends Server
{
    protected function doEvalJS(Connection $conn, $str, $returnType = 'js')
    {
        $result = null;
        switch ($returnType) {
            case 'js':
                $result = $conn->socketSend($str);
                break;
            case 'json':
                $result = json_decode($conn->socketSend("stream.end(JSON.stringify({$str}))"));
                break;
            default:
                break;
        }

        return $result;
    }

    protected function getServerScript()
    {
        $js = <<<'JS'
var net      = require('net')
  , zombie   = require('%modules_path%zombie')
  , browser  = null
  , pointers = []
  , buffer   = ""
  , host     = '%host%'
  , port     = %port%;

var zombieVersionCompare = function(v2, op) {
  var version_compare = function (v1, v2, operator) {
    var i = 0,
        x = 0,
        compare = 0,
        vm = {
          'dev': -6,
          'alpha': -5,
          'a': -5,
          'beta': -4,
          'b': -4,
          'RC': -3,
          'rc': -3,
          '#': -2,
          'p': 1,
          'pl': 1
        },
        prepVersion = function (v) {
          v = ('' + v).replace(/[_\-+]/g, '.');
          v = v.replace(/([^.\d]+)/g, '.$1.').replace(/\.{2,}/g, '.');

          return (!v.length ? [-8] : v.split('.'));
        },
        numVersion = function (v) {
          return !v ? 0 : (isNaN(v) ? vm[v] || -7 : parseInt(v, 10));
        };

      v1 = prepVersion(v1);
      v2 = prepVersion(v2);
      x = Math.max(v1.length, v2.length);

      for (i = 0; i < x; i++) {
        if (v1[i] == v2[i]) {
          continue;
        }

        v1[i] = numVersion(v1[i]);
        v2[i] = numVersion(v2[i]);

        if (v1[i] < v2[i]) {
          compare = -1;
          break;
        } else if (v1[i] > v2[i]) {
          compare = 1;
          break;
        }
      }

      if (!operator) {
        return compare;
      }

      switch (operator) {
        case '>':
        case 'gt':
          return (compare > 0);

        case '>=':
        case 'ge':
          return (compare >= 0);

        case '<=':
        case 'le':
          return (compare <= 0);

        case '==':
        case '=':
        case 'eq':
          return (compare === 0);

        case '<>':
        case '!=':
        case 'ne':
          return (compare !== 0);

        case '':
        case '<':
        case 'lt':
          return (compare < 0);
      }

      return null;
  };

  return version_compare(require('%modules_path%zombie/package').version, v2, op);
};

if (false == zombieVersionCompare('2.0.0alpha1', '>=')) {
  throw new Error("Your zombie.js version is not compatible with this driver. Please use a version >= 2.0.0alpha1");
}

net.createServer(function (stream) {
  stream.setEncoding('utf8');
  stream.allowHalfOpen = true;

  stream.on('data', function (data) {
    buffer += data;
  });

  stream.on('end', function () {
    if (browser == null) {
      browser = new zombie();

      // Clean up old pointers
      pointers = [];
    }

    eval(buffer);
    buffer = "";
  });
}).listen(port, host, function() {
  console.log('server started on ' + host + ':' + port);
});
JS;

        return $js;
    }
}
