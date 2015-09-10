# odooclient

## Install

Via Composer

``` bash
$ composer require harunyasar/odooclient
```

## Usage

``` php
$client = new Odoo\Client\OdooClient($host, $port, $db, $username, $password);
$client->create('modelName', $dataArr);
```

## TODO

* Write tests
* Write the whole usage examples