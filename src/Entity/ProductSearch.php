<?php
namespace App\Entity;

class ProductSearch
{
    /**
     * @var string|null
     */
    private $productName;


    /**
     * Get the value of productName
     *
     * @return  string|null
     */ 
    public function getProductName()
    {
        return $this->productName;
    }

    /**
     * Set the value of productName
     *
     * @param  string|null  $productName
     *
     * @return  ProductSearch
     */ 
    public function setProductName( string $productName) : ProductSearch
    {
        $this->productName = $productName;

        return $this;
    }
}