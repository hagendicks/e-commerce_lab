<?php

require_once __DIR__ . '/../classes/WishlistClass.php';

class WishlistController
{

    private $wishlistModel;


    public function __construct()
    {
        $this->wishlistModel = new WishlistClass();
    }


    public function isInWishlist($customer_id, $product_id)
    {
        return $this->wishlistModel->isInWishlist($customer_id, $product_id);
    }


    public function toggle($customer_id, $product_id)
    {
        if ($this->wishlistModel->isInWishlist($customer_id, $product_id)) {

            $ok = $this->wishlistModel->removeFromWishlist($customer_id, $product_id);

            return ['success' => $ok, 'added' => false];
        }

        $ok = $this->wishlistModel->addToWishlist($customer_id, $product_id);

        return ['success' => $ok, 'added' => $ok];
    }


    public function remove($customer_id, $product_id)
    {
        return $this->wishlistModel->removeFromWishlist($customer_id, $product_id);
    }


    public function getProductIds($customer_id)
    {
        return $this->wishlistModel->getProductIds($customer_id);
    }


    public function getItems($customer_id)
    {
        return $this->wishlistModel->getItems($customer_id);
    }


    public function getCount($customer_id)
    {
        return $this->wishlistModel->getCount($customer_id);
    }

}

?>
