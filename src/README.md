# Fuel Sdk - Connection with Fuel 
[![Total Downloads](https://img.shields.io/packagist/dt/tilomotion/sdk.svg)](https://packagist.org/packages/tilomotion/fuelsdk)
[![Latest Stable Version](https://img.shields.io/packagist/v/tilomotion/sdk.svg)](https://packagist.org/packages/tilomotion/fuelsdk)

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


```
## Examples
#### Example GET single result
```php
#REQUEST GET OBJECT
$params[] = new QueryParam("id", QueryRelation::FUEL_INT_QUERY_RELATION_EQUAL, 1);

//Method requestWilcardGet allow do request for single element and for list. 
$conn->requestWilcardGet("/usuarios/item", $params);

```

#### Example GET list, with pagination
```php
#REQUEST GET LIST OBJECT [FILTERED]
$params = [];

//QueryParam allow filter by Entity's attribute, and type filter
$params[] = new QueryParam("fechaAlta", QueryRelation::FUEL_INT_QUERY_RELATION_GROW, '2019-08-12');

//QueryPagination allow return specific page, and number results por page.  Also its possible send only max number results 
// by QueryParam with         $params[] = new QueryParam("maxResults", QueryRelation::FUEL_INT_QUERY_RELATION_EQUAL, 2);
$params[] = new QueryPagination(3,2);

//Method requestWilcardGet allow do request for single element and for list. 
$conn->requestWilcardGet("/usuarios/list", $params);
var_dump(json_encode($conn->getResponse()->getData()));

$iterations = $conn->getResponse()->getPagination()->getNumberPages();
while($iterations>1)
{
    $conn->requestNextPage();
    var_dump($conn->getResponse()->getData());
    $iterations--;
}
```

#### Example POST
```php
#REQUEST POST OBJECT
$data = array(
            "sede"=> 2000,
            "tipoCliente"=>3,
            "tratamiento"=>1,
            "nombre"=> "Ernesto",
            "apellidos"=> "Adroher Cuevas",
            "fechaNacimiento"=> "1980-04-20",
            "usuarioOriginal"=> 1,
            "usuarioResponsable"=> 1,
            "procedencia"=>3,
            "concesionariosPermitidos"=> [999,268],
            "fechaAlta"=> "2020-03-09 17:02:50"
        );
$conn->requestWilcardPost("/terceros/create", $data);
//Trow an exception if httpcode != 200

//Get Id new object
$responseData = $conn->getResponse()->getData();
if(isset($responseData['id']))
{
    $id = $responseData['id']
}

//Get object created
var_dump($conn->getResponse()->getData());

```

#### Example PUT
```php
$id = 1; //Id get by GET request item or list, or by previous POST request to create.
$data = array(
        "id" => $id,
        "nombre"=> "Carlos",
        "apellidos"=> "Santander Huelva",
);
$conn->requestWilcardPut("/terceros/update", $data);
 //Trow an exception if httpcode != 200 No update element
 
```

#### Example DELETE
```php
$id = 1; //Id get by GET request item or list, or by previous POST request to create.
$params[] = new QueryParam("id", QueryRelation::FUEL_INT_QUERY_RELATION_EQUAL, $id);

$conn->requestWilcardDelete("/terceros/delete", $params);   
//Trow an exception if httpcode != 200 No element deleted
 
```

## HTTP CODES
- 200: OK 
- 204: NOT FOUND. Element's Id not found for this license. Available on GET or DELETE for item or list. On Create or Update when related object's id not exists.
- 400: Insufficient parameters on request. Normally on PUT or POST request.
- 401: Invalid credentials, require refresh.
- 409: Already exists element. Usually on POST request.
- 500: Internal Server Error. Report to developers.

## Documentation

- [API Fuel](doc/01-usage.md)
- [API Examples](doc/02-handlers-formatters-processors.md)


## Third Party Packages
Third party handlers, formatters and processors are
[listed in the wiki](https://github.com/agagoenc/fuel-sdk/wiki/Third-Party-Packages). You
can also add your own there if you publish one.

## About

#### Requirements

- Fuel sdk works with  PHP 5.4 or above.

#### Submitting bugs and feature requests

Bugs and feature request are tracked on [GitHub](https://github.com/agagoenc/fuel-sdk/issues)

#### Framework Integrations

- Frameworks and libraries using [PSR-3](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md)
  can be used very easily with Monolog since it implements the interface.

#### Author

Alejandro Gago Encinas - <agago@tilomotion.com><br />
See also the list of [contributors](https://github.com/agagoenc/fuel-sdk/contributors) which participated in this project.

#### License

FuelSdk is licensed under the MIT License - see the `LICENSE` file for details


