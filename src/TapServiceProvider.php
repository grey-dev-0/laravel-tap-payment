<?php namespace GreyDev\Tap;

use Illuminate\Support\ServiceProvider;

class TapServiceProvider extends ServiceProvider{
    protected $defer = true;

    public function boot(){}

    public function register(){
        $this->app->singleton('tap-payment', function(){
            return new Gateway();
        });
    }

    public function provides(){
        return ['tap-payment'];
    }
}