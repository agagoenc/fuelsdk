# Fuel Sdk - Connection with Fuel 
[![Total Downloads](https://img.shields.io/packagist/dt/fuel/sdk.svg)](https://packagist.org/packages/fuel/sdk)
[![Latest Stable Version](https://img.shields.io/packagist/v/fuel/sdk.svg)](https://packagist.org/packages/fuel/sdk)

This library allow use Fuel's API with all kind of authentication.

## Installation

Install the latest version with

```bash
$ composer require fuel/sdk
```

## Login with Email and Pass

```php
<?php

use FuelSdk\ConnectionEmailPass;
use FuelSdk\Connection;


// create connection
$conn = new ConnectionEmailPass("passUser","emailUser", "./folder/file.log", Connection::FUEL_NAME_DEV);

// create request with wilcard values


```

## Authenticated IP

```php
<?php

use FuelSdk\ConnectionIpAuthenticated;
use FuelSdk\Connection;

//Id License/cliente
$licenseId = 1;

// create connection. On create try to retrieve client list.
$conn = new ConnectionIpAuthenticated($licenseId, "./folder/file.log", Connection::FUEL_NAME_DEV);

//Also set the user who makes the request
$conn->setUserId(1);

//Also set the user who makes the request using the old Maxterauto id
$conn->setUserIdOld(9012);

// create request with wilcard values


```

## Documentation

- [API Fuel](doc/01-usage.md)
- [API Examples](doc/02-handlers-formatters-processors.md)


## Third Party Packages

Third party handlers, formatters and processors are
[listed in the wiki](https://github.com/agagoenc/fuel-sdk/wiki/Third-Party-Packages). You
can also add your own there if you publish one.

## About

### Requirements

- Fuel sdk works with  PHP 5.4 or above.

### Submitting bugs and feature requests

Bugs and feature request are tracked on [GitHub](https://github.com/agagoenc/fuel-sdk/issues)

### Framework Integrations

- Frameworks and libraries using [PSR-3](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md)
  can be used very easily with Monolog since it implements the interface.

### Author

Alejandro Gago Encinas - <agago@tilomotion.com><br />
See also the list of [contributors](https://github.com/agagoenc/fuel-sdk/contributors) which participated in this project.

### License

FuelSdk is licensed under the MIT License - see the `LICENSE` file for details

### Acknowledgements


