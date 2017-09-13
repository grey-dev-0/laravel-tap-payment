<?php namespace GreyDev\Tap\Models;

/**
 * Class Merchant
 * @package GreyDev\Tap
 *
 * Merchant representation as required in TAP payload
 */
class Merchant{
    /**
     * @var string $autoReturn Redirect user to provided URL or stay on TAP success page.
     */
    private $autoReturn;

    /**
     * @var string $hash TAP API authentication string.
     */
    private $hash;

    private $language;
    private $merchantId;
    private $referenceId;
    private $username;
    private $password;
    private $availableProperties = ['auto_return', 'error_url', 'hash_string', 'language', 'merchant_id', 'username', 'password', 'reference_id', 'post_url', 'return_url'];

    /**
     * @var string $returnUrl The URL which the user will be redirected to after payment.
     */
    private $returnUrl;

    /**
     * Merchant constructor.
     * @param string|array $hash Authentication hash string or full merchant data.
     * @param string $merchantId
     * @param string $username
     * @param string $password
     * @param string $language
     * @param string $autoReturn
     */
    public function __construct($hash, $merchantId = '1014', $username = 'test', $password = 'test', $language = 'EN', $autoReturn = 'Y'){
        $this->merchantId = $merchantId;
        $this->username = $username;
        $this->password = $password;
        $this->language = $language;
        $this->autoReturn = $autoReturn;
        if(!is_array($hash))
            $this->hash = $hash;
        else{
            foreach($hash as $property => $value)
                $this->$property = $value;
        }
    }

    public function __set($name, $value){
        if(in_array($name, $this->availableProperties))
            $this->$name = $value;
    }

    /**
     * Getting merchant payload
     * @return array
     */
    public function toArray(){
        $merchant = [];
        foreach($this->availableProperties as &$availableProperty){
            if(isset($this->$availableProperty) || isset($this->{camel_case($availableProperty)})){
                if($availableProperty == 'username')
                    $merchant['UserName'] = $this->$availableProperty;
                elseif($availableProperty == 'merchant_id' || $availableProperty == 'reference_id')
                    $merchant[str_replace('Id', 'ID', studly_case($availableProperty))] = $this->{camel_case($availableProperty)} = $this->$availableProperty;
                elseif($availableProperty == 'return_url')
                    $merchant[str_replace('Url', 'URL', studly_case($availableProperty))] = $this->{camel_case($availableProperty)} = $this->$availableProperty;
                elseif($availableProperty == 'post_url')
                    $merchant[str_replace('Url', 'URL', studly_case($availableProperty))] = $this->$availableProperty;
                elseif($availableProperty == 'language')
                    $merchant['LangCode'] = $this->$availableProperty;
                elseif($availableProperty == 'hash_string')
                    $merchant[studly_case($availableProperty)] = $this->hash = $this->$availableProperty;
                else
                    $merchant[studly_case($availableProperty)] = $this->{camel_case($availableProperty)} = $this->$availableProperty;
            }
        }
        return $merchant;
    }
}