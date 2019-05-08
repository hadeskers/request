#Make client request
=======================

Install: 
==========
```shell
composer require hadesker/request
```

Config: 
```shell
Add Hadesker\Request\RequestServiceProvider::class to provider block of config/app.php file
```

How
==========
Quick start

```php

$req = new \Hadesker\Request\Request();
$req->setUrl('https://abc.com/login');
$req->setHeaders([
    'Auth'=> 'Bearer sgafaksdfasd',
]);
$req->setBodies([
    'username' => 'something',
    'password' => '123456',
]);
$response = $req->post()->getResponseArray();
print_r($response);
die();

```

Website: https://hadesker.net _or_ http://hadesker.uk
