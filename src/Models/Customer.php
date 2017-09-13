<?php namespace GreyDev\Tap\Models;

/**
 * Class Customer
 * @package GreyDev\Tap
 *
 * Customer representation as required in TAP payload
 */
class Customer{
    private $name;
    private $mobile;
    private $email;
    private $availableProperties = ['apartment', 'area', 'avenue', 'block', 'building', 'civil_id', 'dob', 'email', 'floor', 'gender', 'id', 'mobile', 'name', 'nationality', 'street'];

    /**
     * Customer constructor.
     * @param string|array $name Customer full name or full customer data.
     * @param string $mobile Customer mobile number
     * @param string $email Customer emaill address
     */
    public function __construct($name, $mobile = '00000000', $email = 'not-submitted@email.com'){
        $this->mobile = $mobile;
        $this->email = $email;
        if(!is_array($name)){
            $this->name = $name;
        } else{
            foreach($name as $property => $value)
                $this->$property = $value;
        }
    }

    public function __set($name, $value){
        if(in_array($name, $this->availableProperties))
            $this->$name = $value;
    }

    /**
     * Getting customer payload
     * @return array
     */
    public function toArray(){
        $customer = [];
        foreach($this->availableProperties as &$availableProperty){
            if(isset($this->$availableProperty)){
                if($availableProperty == 'id' || $availableProperty == 'dob')
                    $customer[strtoupper($availableProperty)] = $this->$availableProperty;
                elseif($availableProperty == 'civil_id')
                    $customer['CivilID'] = $this->$availableProperty;
                else
                    $customer[ucfirst($availableProperty)] = $this->$availableProperty;
            }
        }
        return $customer;
    }
}