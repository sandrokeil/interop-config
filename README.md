# Interoperability Configuration

> You want to configure your factories?

> You want to reduce your factory boilerplate code?

> You want to check automatically for mandatory options or merge default options?

> You want to have a valid config structure?

> You want to generate your configuration files from factory classes?

> This library comes to the rescue!

[![Build Status](https://travis-ci.org/sandrokeil/interop-config.png?branch=master)](https://travis-ci.org/sandrokeil/interop-config)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/sandrokeil/interop-config/badges/quality-score.png?s=cdef161c14156e3e36ed0ce3d6fd7979d38d916c)](https://scrutinizer-ci.com/g/sandrokeil/interop-config/)
[![Coverage Status](https://coveralls.io/repos/sandrokeil/interop-config/badge.svg?branch=master)](https://coveralls.io/r/sandrokeil/interop-config?branch=master)
[![HHVM Status](http://hhvm.h4cc.de/badge/sandrokeil/interop-config.svg?style=flat)](http://hhvm.h4cc.de/package/sandrokeil/interop-config)
[![PHP 7 ready](http://php7ready.timesplinter.ch/sandrokeil/interop-config/badge.svg)](https://travis-ci.org/sandrokeil/interop-config)
[![Latest Stable Version](https://poser.pugx.org/sandrokeil/interop-config/v/stable.png)](https://packagist.org/packages/sandrokeil/interop-config)
[![Dependency Status](https://www.versioneye.com/user/projects/53615c75fe0d0720eb00009e/badge.png)](https://www.versioneye.com/php/sandrokeil:interop-config/0.3.1)
[![Total Downloads](https://poser.pugx.org/sandrokeil/interop-config/downloads.png)](https://packagist.org/packages/sandrokeil/interop-config)
[![Reference Status](https://www.versioneye.com/php/sandrokeil:interop-config/reference_badge.svg?style=flat)](https://www.versioneye.com/php/sandrokeil:interop-config/references)
[![License](https://poser.pugx.org/sandrokeil/interop-config/license.png)](https://packagist.org/packages/sandrokeil/interop-config)

`interop-config` provides interfaces and a concrete implementation to create instances depending on configuration via
factory classes and ensures a valid config structure. It can also be used to auto discover factories
and to create configuration files.

 * **Well tested.** Besides unit test and continuous integration/inspection this solution is also ready for production use.
 * **Framework agnostic** This PHP library does not depends on any framework but you can use it with your favourite framework.
 * **Every change is tracked**. Want to know whats new? Take a look at [CHANGELOG.md](https://github.com/sandrokeil/interop-config/blob/master/CHANGELOG.md)
 * **Listen to your ideas.** Have a great idea? Bring your tested pull request or open a new issue. See [CONTRIBUTING.md](CONTRIBUTING.md)

You should have coding conventions and you should have config conventions. If not, you should think about that.
`interop-config` is universally applicable! See further documentation for more details.

## Installation

Installation of this module uses composer. For composer documentation, please refer to
[getcomposer.org](http://getcomposer.org/).

Put the following into your composer.json or run `composer require sandrokeil/interop-config`.

    {
        "require": {
            "sandrokeil/interop-config": "^1.0"
        }
    }

## Documentation
For the latest online documentation visit [http://sandrokeil.github.io/interop-config/](http://sandrokeil.github.io/interop-config/ "Latest interop-config documentation").
Refer the *Quick Start* section for a detailed explanation. 

Documentation is [in the doc tree](doc/), and can be compiled using [bookdown](http://bookdown.io) or [Docker](https://www.docker.com/)

```console
$ docker run -it --rm -v $(pwd):/app sandrokeil/bookdown doc/bookdown.json
$ docker run -it --rm -p 8080:8080 -v $(pwd):/app php:7.0-cli php -S 0.0.0.0:8080 -t /app/doc/html
```

or run *bookdown* via Composer and `$HOME/.composer/vendor/bin` is on your `$PATH`.

```console
$ bookdown doc/bookdown.json
$ php -S 0.0.0.0:8080 -t doc/html/
```

Then browse to [http://localhost:8080/](http://localhost:8080/)

## Projects
This is a list of projects who are using `interop-config` interfaces (incomplete).

* [prooph/service-bus](https://github.com/prooph/service-bus) - PHP Lightweight Message Bus supporting CQRS
* [prooph/event-store](https://github.com/prooph/event-store) - PHP EventStore Implementation 
* [prooph/event-store-doctrine-adapter](https://github.com/prooph/event-store-doctrine-adapter) - Doctrine Adapter for ProophEventStore
* [prooph/event-store-mongodb-adapter](https://github.com/prooph/event-store-mongodb-adapter) - MongoDB Adapter for ProophEventStore
* [prooph/snapshotter](https://github.com/prooph/snapshotter) - snapshot tool for prooph event-store 
* [prooph/snapshot-memcached-adapter](https://github.com/prooph/snapshot-memcached-adapter) - Snapshot Memcached Adapter for ProophEventStore 
* [prooph/snapshot-doctrine-adapter](https://github.com/prooph/snapshot-doctrine-adapter) - Snapshot Doctrine Adapter for ProophEventStore 

## Benchmarks
The benchmarks uses [PHPBench](http://phpbench.readthedocs.org/en/latest/) and can be started by the following command:
 
```console
$ ./vendor/bin/phpbench run --report=aggregate
```
 
or with [Docker](https://www.docker.com/)
 
```console
$ docker run --rm -it --volume $(pwd):/app prooph/php:7.0-cli-opcache php ./vendor/bin/phpbench run --report=aggregate
```
