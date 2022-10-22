#Make request from php
=======================

Install: 
==========
```shell
composer require hadesker/request
```

Config: 
```shell
If you are using Laravel 5.5 or above, the package will make the service provider available for auto-discovery.
If you are using an earlier version of Laravel, add Hadesker\Request\RequestServiceProvider::class to provider block of config/app.php file
```

How to use:
==========
Quick start

```php

$req = new \Hadesker\Request\Request();
$req->setUrl('https://abc.com/login');
$req->setFormType(\Hadesker\Request\Request::$FORM_TYPE_ENCODED) // $FORM_TYPE_DATA or $FORM_TYPE_JSON or $FORM_TYPE_ENCODED (default)
$req->setHeaders([
    'Auth'=> 'Bearer sgafaksdfasd',
    'User-Agent'=> 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.35 (KHTML, like Gecko) Chrome/106.0.0.0 Safari/537.35',
]);
$req->setCookie('name', 'value');
$req->setBodies([
    'username' => 'something',
    'password' => '123456',
]);
$req->post();
var_dump($req->getCookies());
var_dump($req->getHeaders());
var_dump($req->getResponse());
die();

```

Author: https://hadesker.net
