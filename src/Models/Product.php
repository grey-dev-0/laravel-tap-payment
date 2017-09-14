<?php namespace GreyDev\Tap\Models;

/**
 * Class Product
 * @package GreyDev\Tap\Models
 *
 * Product representation as required in TAP payload
 */
class Product{
    private $currencyCode;
    public $quantity;
    public $totalPrice;
    private $unitDesc;
    private $unitName;
    public $unitPrice;
    private $availableProperties = ['currency_code', 'quantity', 'total_price', 'unit_desc', 'unit_name', 'unit_price', 'unit_id', 'img_url', 'vnd_id'];

    /**
     * Product constructor.
     * @param array $product Single product data.
     */
    public function __construct($product){
        $this->currencyCode = config('tap-payment.currency', 'KWD');
        $this->unitName = $product[config('tap-payment.name-field', 'name')];
        $this->unitPrice = $product[config('tap-payment.price-field', 'unit_price')];
        $this->unitDesc = $product[config('tap-payment.description-field', 'description')];
        $this->quantity = $product[config('tap-payment.quantity-field', 'quantity')];
        $extraProperties = ['total_price', 'unit_id', 'img_url', 'vnd_id'];
        foreach($extraProperties as &$extraProperty)
            if(isset($product[$extraProperty]))
                $this->{camel_case($extraProperty)} = $product[$extraProperty];
    }

    public function __set($name, $value){
        $this->$name = $value;
    }

    /**
     * Getting product payload
     * @return array
     */
    public function toArray(){
        $product = [];
        foreach($this->availableProperties as &$availableProperty){
            if(isset($this->{camel_case($availableProperty)})){
                if(strpos($availableProperty, '_') == -1)
                    $product[ucfirst($availableProperty)] = $this->{camel_case($availableProperty)};
                elseif(strpos($availableProperty, '_id') != -1)
                    $product[str_replace('Id', 'ID', studly_case($availableProperty))] = $this->{camel_case($availableProperty)};
                else
                    $product[studly_case($availableProperty)] = $this->{camel_case($availableProperty)};
            }
        }
        return $product;
    }
}