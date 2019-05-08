#Make client request
=======================

Install: 
==========
```shell
composer require hadesker/request
```

Config: 
```shell
Add Hadesker\Request\Request::class to config/app.php
```

How
==========
Quick start

```php

$req = new \App\Helpers\Request();
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

```

Website: https://hadesker.net _or_ http://hadesker.uk
