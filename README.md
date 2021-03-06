[![Build Status](https://travis-ci.org/easyhrworld/php-queuesadilla.svg?branch=master)](https://travis-ci.org/easyhrworld/php-queuesadilla)
[![Coverage Status](https://coveralls.io/repos/github/easyhrworld/php-queuesadilla/badge.svg?branch=master)](https://coveralls.io/github/easyhrworld/php-queuesadilla?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/josegonzalez/queuesadilla.svg?style=flat-square)](https://packagist.org/packages/josegonzalez/queuesadilla)
[![Latest Stable Version](https://img.shields.io/packagist/v/josegonzalez/queuesadilla.svg?style=flat-square)](https://packagist.org/packages/josegonzalez/queuesadilla)
[![Gratipay](https://img.shields.io/gratipay/josegonzalez.svg?style=flat-square)](https://gratipay.com/~josegonzalez/)

# Queuesadilla

A job/worker system built to support various queuing systems.

## Requirements

- PHP 5.5+

## Installation

_[Using [Composer](http://getcomposer.org/)]_

Add the plugin to your project's `composer.json` - something like this:

```composer
{
  "require": {
    "josegonzalez/queuesadilla": "dev-master"
  }
}
```

## Usage

- [Installation](/docs/installation.md)
- [Supported Systems](/docs/supported-systems.md)
- [Simple Usage](/docs/simple-usage.md)
- [Defining Jobs](/docs/defining-jobs.md)
- [Job Options](/docs/job-options.md)
- [Available Callbacks](/docs/callbacks.md)

## Tests

Tests are run via `phpunit` and depend upon multiple datastores. You may also run tests using the included `Dockerfile`:

```shell
docker build .
```
