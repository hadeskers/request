<?php

namespace Hadesker\Request;

use Illuminate\Support\ServiceProvider;

class RequestServiceProvider extends ServiceProvider
{
    public function boot()
    {

    }

    public function register()
    {
        $this->app->make('Hadesker\Request\Request');
    }
}
