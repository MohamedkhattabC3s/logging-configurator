# loggingConfigurator

Loads and manages logging configuration files for an API service.

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mjmunger/loggingConfigurator/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mjmunger/loggingConfigurator/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/mjmunger/loggingConfigurator/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/mjmunger/loggingConfigurator/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/mjmunger/loggingConfigurator/badges/build.png?b=master)](https://scrutinizer-ci.com/g/mjmunger/loggingConfigurator/build-status/master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/mjmunger/loggingConfigurator/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)

## Installation

`composer require hphio/logging-configurator`

## Requirements

This was developed on, and intended for PHP v7.0+. It *may* work on lower versions, but YMMV.

## How to use this package

This package is designed to load and configure Monolog instances for use inside your classes. It will look for a file called `config/logging.json`, which should have the following structure:

````
{
  "services" : [
    {
      "name" : "auth",
      "enabled" : true,
      "path" : "/var/log/apache2/foo/",
      "filename" : "auth.log"
    },
    {
      "name" : "accounts",
      "enabled" : false,
      "path" : "/home/baruser/log/",
      "filename" : "accounts.log"
    }
  ]
}
```` 

Once you have configured a service that requires logging, this package will configure and instantiate a Monolog instance according to the settings you've setup. If you attempt to load a service that is not configured, it will simply return false.