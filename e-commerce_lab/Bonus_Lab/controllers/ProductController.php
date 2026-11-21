<?php

require_once __DIR__ . '/../classes/ProductClass.php';

class ProductController
{

    private $productModel;


    public function __construct()
    {
        $this->productModel =
            new ProductClass();
    }


    public function addBrand($name)
    {
        return $this->productModel->addBrand($name);
    }


    public function getAllBrands()
    {
        return $this->productModel->getAllBrands();
    }


    public function getBrandById($id)
    {
        return $this->productModel->getBrandById($id);
    }


    public function updateBrand($id, $name)
    {
        return $this->productModel->updateBrand(
            $id,
            $name
        );
    }
    public function deleteBrand($id)
{
    return $this->productModel->deleteBrand($id);
}
public function addCategory($name)
{
    return $this->productModel->addCategory($name);
}


public function getAllCategories()
{
    return $this->productModel->getAllCategories();
}


public function getCategoryById($id)
{
    return $this->productModel->getCategoryById($id);
}


public function updateCategory($id, $name)
{
    return $this->productModel->updateCategory(
        $id,
        $name
    );
}
public function deleteCategory($id)
{
    return $this->productModel->deleteCategory($id);
}
    public function addProduct($cat, $brand, $title, $price, $desc, $image_filename, $keywords)
    {
        return $this->productModel->addProduct(
            $cat, $brand, $title, $price, $desc, $image_filename, $keywords
        );
    }


    public function updateProduct($id, $cat, $brand, $title, $price, $desc, $image_filename, $keywords)
    {
        return $this->productModel->updateProduct(
            $id, $cat, $brand, $title, $price, $desc, $image_filename, $keywords
        );
    }


    public function getProductById($id)
    {
        return $this->productModel->getProductById($id);
    }


    public function getAllProducts()
    {
        return $this->productModel->getAllProducts();
    }
        public function getFeaturedProducts($limit = 6)
    {
        return $this->productModel->getFeaturedProducts($limit);
    }


    public function getProductsByCategory($cat_id)
    {
        return $this->productModel->getProductsByCategory($cat_id);
    }


    public function getProductsByBrand($brand_id)
    {
        return $this->productModel->getProductsByBrand($brand_id);
    }


    public function searchProducts($query)
    {
        return $this->productModel->searchProducts($query);
    }
        public function deleteProduct($id)
    {
        return $this->productModel->deleteProduct($id);
    }
        public function recordView($customer_id, $product_id)
    {
        return $this->productModel->recordView($customer_id, $product_id);
    }


    public function getRecentlyPurchased($customer_id, $limit = 6)
    {
        return $this->productModel->getRecentlyPurchased($customer_id, $limit);
    }


    public function getRecentlyViewed($customer_id, $limit = 6)
    {
        return $this->productModel->getRecentlyViewed($customer_id, $limit);
    }


    public function getBestSellers($limit = 6)
    {
        return $this->productModel->getBestSellers($limit);
    }


    public function getRecommendations($customer_id, $limit = 6)
    {
        return $this->productModel->getRecommendations($customer_id, $limit);
    }
}

?>
