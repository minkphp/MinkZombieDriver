#!/usr/bin/env node
var net = require('net');
var zombie = require('zombie');
var Tough = require('zombie/node_modules/tough-cookie');
var browser = null;
var pointers = [];
var buffer = '';
var host = process.env.HOST || '127.0.0.1';
var port = process.env.PORT || 8124;

Tough.Cookie.prototype.cookieString = function cookieString() {
    return this.key + '=' + (this.value == null ? '' : this.value);
};

var zombieVersionCompare = function (v2, op) {
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

    return version_compare(require('zombie/package').version, v2, op);
};

if (false == zombieVersionCompare('2.0.0', '>=')) {
    throw new Error("Your zombie.js version is not compatible with this driver. Please use a version >= 2.0.0");
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

        try {
            eval(buffer);
            buffer = '';
        } catch (e) {
            buffer = '';
            stream.end('CAUGHT_ERROR:' + JSON.stringify(e.message));
        }
    });
}).listen(port, host, function () {
    console.log('server started on ' + host + ':' + port);
});
