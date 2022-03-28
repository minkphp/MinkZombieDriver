1.6.0 / 2022-03-28
==================

New features:

* Added support for `symfony/process` 6

Removed:

* Removed support for PHP <7.2
* Removed support for `symfony/process` <4.4

1.5.0 / 2021-10-11
==================

New features:

* Ensured that the zombie process is stopped no matter how the PHP shutdowns
* Added error to forbid accidental reconfiguration of a running zombie server
* Added validation of arguments when instantiating the zombie server
* Added support for configuring zombie.js options
* Added support for `symfony/process` 4 and 5

Removed:

* Removed support for PHP 5.3

Bug fixes:

* Fixed the compatibility with installing zombie.js with npm 3+

Testsuite:

* Added CI jobs running on PHP 7.2, 7.3, 7.4, 8.0 and 8.1
* Migrated to the shared testsuite in `mink/driver-testsuite`

1.4.0 / 2016-03-05
==================

New features:

* Refactored the node.js script running zombie to be a dedicated script configured
  through environment variables. Zombie can now be installed with npm in any parent
  of the ZombieDriver installation path without having to configure the node modules
  path in the driver (so for instance using `npm install zombie` in the root of the
  project)
* Added the `HOST` and `PORT` environment variables in the process running the server
  to expose the configuration

Bug fixes:

* Fixed the return value of `wait` to ensure it is always a boolean
* Fixed the handling of cookie values containing a semicolon
* Fixed `setNodeModulesPath` to be compatible with the usage of custom server scripts
* Added support for the Symfony 3 Process component (no change actually needed)
* Fixed the host and port setters which were not taken into account by the node.js code

Testsuite:

*  Disallowed failures on PHP 7 on Travis (tests were passing since a long time)

1.3.0 / 2015-09-21
==================

New features:

* Updated the driver to use findElementsXpaths for Mink 1.7 and forward compatibility with Mink 2

Bug fixes:

* Added a dependency on PHP's sockets extension
* Upgrade the authentication logic for Zombie 3
* Fixed header retrieval for Zombie 4.x+ versions
* Updated `triggerBrowserEvent` to include any output from `evalJs` in the exception message

Testsuite:

* Add testing on PHP 7
* Add testing for Zombie 4.x using IO.JS

Misc:

* Updated the repository structure to PSR-4

1.2.0 / 2014-09-26
==================

BC break:

* Rewrote the driver based on Zombie 2.0 rather than the old 1.x versions
* Changed the behavior of `getValue` for checkboxes according to the BC break in Mink 1.6

New features:

* Added the support of select elements in `setValue`
* Implemented `getOuterHtml`
* Added support for request headers
* Implemented `submitForm`
* Implemented `isSelected`

Bug fixes:

* Fixed the selection of options for multiple selects to ensure the change event is triggered only once
* Fixed the selection of options for radio groups
* Fixed `getValue` for radio groups
* Fixed the retrieval of response headers
* Fixed a leak of outdated references in the node server when changing page
* Fixed the resetting of the driver to reset everything
* Fixed the code to throw exceptions for invalid usages of the driver
* Fixed handling of errors to throw exceptions in the driver rather than crashing the node server
* Fixed `evaluateScript` and `executeScript` to support all syntaxes required by the Mink API
* Fixed `getContent` to return the source of the page without decoding entities
* Fixed the removal of cookies
* Fixed the basic auth implementation

Testing:

* Updated the testsuite to use the new Mink 1.6 driver testsuite
* Added testing on HHVM
