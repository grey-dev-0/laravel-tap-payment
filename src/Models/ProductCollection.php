<?php namespace GreyDev\Tap\Models;

use Iterator, ArrayIterator;
use Illuminate\Support\Collection;

/**
 * Class ProductCollection
 * @package GreyDev\Tap\Models
 *
 * Collection of products that will be paid for.
 */
class ProductCollection implements \Iterator{
    /**
     * @var Product[] $products List of products represented as TAP payload requires
     */
    private $products;

    /**
     * ProductCollection constructor.
     * @param array|Collection $products
     */
    public function __construct($products){
        if($products instanceof Collection)
            foreach($products as &$product)
                $this->products[] = new Product($product->toArray());
        else
            foreach($products as &$product)
                $this->products[] = new Product($product);
    }

    /**
     * Getting current product form the list
     * @return Product
     */
    public function current(){
        return current($this->products);
    }

    /**
     * Getting next product in the list
     * @return Product
     */
    public function next(){
        return next($this->products);
    }

    /**
     * Getting previous product in the list
     * @return Product
     */
    public function previous(){
        return prev($this->products);
    }

    /**
     * Resetting internal products pointer to the first one.
     * @return Product
     */
    public function rewind(){
        return reset($this->products);
    }

    /**
     * Getting a specific product form the list by its index.
     * @param int $index Index of the product to get
     * @return Product
     */
    public function get($index){
        $iterator = new ArrayIterator($this->products);
        $iterator->seek($index);
        return current($this->products);
    }


    /**
     * Getting total price of products to be purchased.
     * @return float
     */
    public function getTotalPrice(){
        $totalPrice = 0.0;
        $this->rewind();
        foreach($this->products as &$product)
            $totalPrice += $product->unitPrice * $product->quantity;
        return $totalPrice;
    }

    /**
     * Getting all products list payload.
     * @return array
     */
    public function toArray(){
        $products = [];
        foreach($this->products as &$product)
            $products[] = $product->toArray();
        return $products;
    }
}