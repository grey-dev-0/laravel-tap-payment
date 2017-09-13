<?php namespace GreyDev\Tap;

use Illuminate\Support\Facades\Facade;

class TapFacade extends Facade{
    protected static function getFacadeAccessor(){
        return 'tap-payment';
    }
}