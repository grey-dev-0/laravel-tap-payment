<?php namespace GreyDev\Tap\Models;

/**
 * Class Payment
 * @package GreyDev\Tap
 *
 * Payment request payload
 */
class Payment{
    /**
     * @var Customer $customer Customer related data
     */
    private $customer;

    /**
     * @var Merchant $merchant Merchant related data
     */
    private $merchant;

    /**
     * @var array $gateways List of available payment methods
     */
    private $gateways;

    /**
     * @var ProductCollection $products List of products to be paid for.
     */
    private $products;

    /**
     * Payment constructor.
     * @param Merchant $merchant
     * @param Customer $customer
     * @param ProductCollection $products
     * @param array $gateways
     */
    public function __construct($merchant, $customer, $products, $gateways = [['Name' => 'ALL']]){
        $this->merchant = $merchant;
        $this->customer = $customer;
        $this->products = $products;
        $this->gateways = $gateways;
    }

    /**
     * Getting full payment payload to be sent to TAP payment API.
     * @return array
     */
    public function toArray(){
        return [
            'CustomerDC' => $this->customer->toArray(),
            'lstProductDC' => $this->products->toArray(),
            'lstGateWayDC' => $this->gateways,
            'MerMastDC' => $this->merchant->toArray()
        ];
    }
}