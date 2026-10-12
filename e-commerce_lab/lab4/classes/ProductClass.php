<?php

require_once __DIR__ . '/../core/db_class.php';

class ProductClass extends Database
{

    public function addBrand($name)
    {
        $sql = "
            INSERT INTO brands (brand_name)
            VALUES (?)
        ";

        $stmt =
            $this->conn->prepare($sql);


        if (!$stmt) {

            error_log($this->conn->error);

            return false;
        }


        $stmt->bind_param(
            "s",
            $name
        );


        $success =
            $stmt->execute();


        $stmt->close();


        return $success;
    }


    public function getAllBrands()
    {
        $sql = "
            SELECT *
            FROM brands
            ORDER BY brand_name ASC
        ";

        $result =
            $this->conn->query($sql);


        if (!$result) {

            error_log($this->conn->error);

            return [];
        }


        return $result->fetch_all(
            MYSQLI_ASSOC
        );
    }


    public function getBrandById($id)
    {
        $sql = "
            SELECT *
            FROM brands
            WHERE brand_id = ?
            LIMIT 1
        ";

        $stmt =
            $this->conn->prepare($sql);


        if (!$stmt) {

            error_log($this->conn->error);

            return false;
        }


        $stmt->bind_param(
            "i",
            $id
        );


        $stmt->execute();


        $result =
            $stmt->get_result();


        $brand =
            $result->fetch_assoc();


        $stmt->close();


        return $brand ?: false;
    }


    public function updateBrand($id, $name)
    {
        $sql = "
            UPDATE brands
            SET brand_name = ?
            WHERE brand_id = ?
        ";

        $stmt =
            $this->conn->prepare($sql);


        if (!$stmt) {

            error_log($this->conn->error);

            return false;
        }


        $stmt->bind_param(
            "si",
            $name,
            $id
        );


        $success =
            $stmt->execute();


        $stmt->close();


        return $success;
    }
    public function deleteBrand($id)
{
    $sql = "DELETE FROM brands WHERE brand_id = ?";

    $stmt = $this->conn->prepare($sql);

    if (!$stmt) {
        error_log($this->conn->error);
        return false;
    }

    $stmt->bind_param("i", $id);

    $success = $stmt->execute();

    $deleted = $stmt->affected_rows > 0;

    $stmt->close();

    return $success && $deleted;
}
public function addCategory($name)
{
    $sql = "
        INSERT INTO categories (cat_name)
        VALUES (?)
    ";

    $stmt =
        $this->conn->prepare($sql);

    if (!$stmt) {

        error_log($this->conn->error);

        return false;
    }

    $stmt->bind_param(
        "s",
        $name
    );

    $success =
        $stmt->execute();

    $stmt->close();

    return $success;
}


public function getAllCategories()
{
    $sql = "
        SELECT *
        FROM categories
        ORDER BY cat_name ASC
    ";

    $result =
        $this->conn->query($sql);

    if (!$result) {

        error_log($this->conn->error);

        return [];
    }

    return $result->fetch_all(
        MYSQLI_ASSOC
    );
}


public function getCategoryById($id)
{
    $sql = "
        SELECT *
        FROM categories
        WHERE cat_id = ?
        LIMIT 1
    ";

    $stmt =
        $this->conn->prepare($sql);

    if (!$stmt) {

        error_log($this->conn->error);

        return false;
    }

    $stmt->bind_param(
        "i",
        $id
    );

    $stmt->execute();

    $result =
        $stmt->get_result();

    $category =
        $result->fetch_assoc();

    $stmt->close();

    return $category ?: false;
}


public function updateCategory($id, $name)
{
    $sql = "
        UPDATE categories
        SET cat_name = ?
        WHERE cat_id = ?
    ";

    $stmt =
        $this->conn->prepare($sql);

    if (!$stmt) {

        error_log($this->conn->error);

        return false;
    }

    $stmt->bind_param(
        "si",
        $name,
        $id
    );

    $success =
        $stmt->execute();

    $stmt->close();

    return $success;
}
public function deleteCategory($id)
{
    $sql = "
        DELETE FROM categories
        WHERE cat_id = ?
    ";

    $stmt =
        $this->conn->prepare($sql);

    if (!$stmt) {

        error_log($this->conn->error);

        return [
            'success' => false,
            'error' => 'Unable to prepare delete request.'
        ];
    }

    $stmt->bind_param(
        "i",
        $id
    );

    if (!$stmt->execute()) {

        error_log($stmt->error);

        $stmt->close();

        return [
            'success' => false,
            'error' => 'Unable to delete this category because it may be linked to existing products.'
        ];
    }

    if ($stmt->affected_rows === 0) {

        $stmt->close();

        return [
            'success' => false,
            'error' => 'Category not found.'
        ];
    }

    $stmt->close();

    return [
        'success' => true,
        'message' => 'Category deleted successfully.'
    ];
}
    public function addProduct($cat, $brand, $title, $price, $desc, $image_filename, $keywords)
    {
        $sql = "
            INSERT INTO products
                (product_cat, product_brand, product_title, product_price,
                 product_desc, product_image, product_keywords)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            error_log($this->conn->error);
            return false;
        }

        $stmt->bind_param(
            "iisdsss",
            $cat,
            $brand,
            $title,
            $price,
            $desc,
            $image_filename,
            $keywords
        );

        $success = $stmt->execute();

        if (!$success) {
            error_log($stmt->error);
        }

        $stmt->close();

        return $success;
    }


    public function updateProduct($id, $cat, $brand, $title, $price, $desc, $image_filename, $keywords)
    {
        $sql = "
            UPDATE products
            SET product_cat = ?,
                product_brand = ?,
                product_title = ?,
                product_price = ?,
                product_desc = ?,
                product_image = ?,
                product_keywords = ?
            WHERE product_id = ?
        ";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            error_log($this->conn->error);
            return false;
        }

        $stmt->bind_param(
            "iisdsssi",
            $cat,
            $brand,
            $title,
            $price,
            $desc,
            $image_filename,
            $keywords,
            $id
        );

        $success = $stmt->execute();

        if (!$success) {
            error_log($stmt->error);
        }

        $stmt->close();

        return $success;
    }


    public function getProductById($id)
    {
        $sql = "
            SELECT p.*, c.cat_name, b.brand_name
            FROM products p
            JOIN categories c ON p.product_cat = c.cat_id
            JOIN brands b ON p.product_brand = b.brand_id
            WHERE p.product_id = ?
            LIMIT 1
        ";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            error_log($this->conn->error);
            return false;
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();

        $product = $stmt->get_result()->fetch_assoc();

        $stmt->close();

        return $product ?: false;
    }


    public function getAllProducts()
    {
        $sql = "
            SELECT p.*, c.cat_name, b.brand_name
            FROM products p
            JOIN categories c ON p.product_cat = c.cat_id
            JOIN brands b ON p.product_brand = b.brand_id
            ORDER BY p.product_id DESC
        ";

        $result = $this->conn->query($sql);

        if (!$result) {
            error_log($this->conn->error);
            return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }
        private function fetchProducts($sql, $types, ...$params)
    {
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            error_log($this->conn->error);
            return [];
        }

        $stmt->bind_param($types, ...$params);

        if (!$stmt->execute()) {
            error_log($stmt->error);
            $stmt->close();
            return [];
        }

        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $stmt->close();

        return $rows ?: [];
    }


    public function getFeaturedProducts($limit = 6)
    {
        $sql = "
            SELECT p.*, c.cat_name, b.brand_name
            FROM products p
            JOIN categories c ON p.product_cat = c.cat_id
            JOIN brands b ON p.product_brand = b.brand_id
            ORDER BY RAND()
            LIMIT ?
        ";

        return $this->fetchProducts($sql, "i", (int) $limit);
    }


    public function getProductsByCategory($cat_id)
    {
        $sql = "
            SELECT p.*, c.cat_name, b.brand_name
            FROM products p
            JOIN categories c ON p.product_cat = c.cat_id
            JOIN brands b ON p.product_brand = b.brand_id
            WHERE p.product_cat = ?
            ORDER BY p.product_title ASC
        ";

        return $this->fetchProducts($sql, "i", (int) $cat_id);
    }


    public function getProductsByBrand($brand_id)
    {
        $sql = "
            SELECT p.*, c.cat_name, b.brand_name
            FROM products p
            JOIN categories c ON p.product_cat = c.cat_id
            JOIN brands b ON p.product_brand = b.brand_id
            WHERE p.product_brand = ?
            ORDER BY p.product_title ASC
        ";

        return $this->fetchProducts($sql, "i", (int) $brand_id);
    }


    public function searchProducts($query)
    {
        $like = '%' . addcslashes($query, '%_\\') . '%';

        $sql = "
            SELECT p.*, c.cat_name, b.brand_name
            FROM products p
            JOIN categories c ON p.product_cat = c.cat_id
            JOIN brands b ON p.product_brand = b.brand_id
            WHERE p.product_title LIKE ?
               OR p.product_keywords LIKE ?
            ORDER BY p.product_title ASC
        ";

        return $this->fetchProducts($sql, "ss", $like, $like);
    }
}

?>
