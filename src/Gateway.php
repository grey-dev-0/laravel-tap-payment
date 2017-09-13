<?php namespace GreyDev\Tap;

use GreyDev\Tap\Models\Customer;
use GreyDev\Tap\Models\Merchant;
use GreyDev\Tap\Models\Payment;
use GreyDev\Tap\Models\ProductCollection;
use Illuminate\Support\Collection;

/**
 * Class Gateway
 * @package GreyDev\Tap
 *
 * TAP payment gateway integration.
 */
class Gateway{
    /**
     * @var Payment $payment The payment payload to be submitted to TAP.
     */
    private $payment;

    /**
     * @var boolean $sandbox Indicates whether the gateway is in production or sandbox mode.
     */
    private $sandbox = false;

    /**
     * Gateway constructor.
     * @return Gateway
     */
    public function __construct(){
        return $this;
    }

    /**
     * Initialize new TAP gateway instance with payment payload.
     * @see http://example.com Refer to this link for supported parameters listing for each entity.
     *
     * @param array $merchant Merchant data
     * @param array $customer Customer data
     * @param array|Collection $products Products list
     * @param array $methods Credit / Debit cards to be supported in this charge request.
     */
    public function create($merchant, $customer, $products, $methods = [['Name' => 'ALL']]){
        if(!isset($merchant['merchant_id'])){
            $this->sandbox = true;
            $merchant['merchant_id'] = '1014';
        }
        if(!isset($merchant['username']))
            $merchant['username'] = 'test';
        if(!isset($customer['mobile']))
            $customer['mobile'];
        $products = new ProductCollection($products);
        $totalAmount = $products->get(0)->totalPrice;
        if(empty($totalAmount) || $totalAmount == 0){
            $totalAmount = $products->getTotalPrice();
            foreach($products as &$product)
                $product->totalPrice = $totalAmount;
        }
        $merchant['hash_string'] = hash_hmac('sha256', "X_MerchantID{$merchant[merchant_id]}X_UserName{$merchant[username]}X_ReferenceID{$merchant[reference_id]}X_Mobile{$customer[mobile]}X_CurrencyCodeKWDX_Total{$totalAmount}", env('TAP_API_KEY', '1tap7'));
        $this->payment = new Payment(new Merchant($merchant), new Customer($customer), $products, $methods);
        return $this;
    }

    /**
     * Getting TAP payment URL or false on failure.
     * @return bool|string
     */
    public function getPaymentURL(){
        $tapUrl = ($this->sandbox)? 'http://tapapi.gotapnow.com/TapWebConnect/Tap/WebPay' : 'https://www.gotapnow.com/TapWebConnect/Tap/WebPay';
        $request = curl_init($tapUrl);
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERPWD => env('TAP_API_KEY', '1tap7'),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json'
            ],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($this->payment->toArray())
        ]);
        $response = json_decode(curl_exec($request));
        if($response['ResponseCode'] == 0)
            return $response['PaymentURL'];
        else{
            \Log::warning("TAP payment URL could NOT be generated:\n\n".json_encode($response, JSON_PRETTY_PRINT));
            return false;
        }
    }
}