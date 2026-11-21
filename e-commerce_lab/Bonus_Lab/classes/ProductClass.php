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

        public function deleteProduct($id)
    {
        $sql = "DELETE FROM products WHERE product_id = ?";

        try {

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

        } catch (mysqli_sql_exception $e) {

            error_log($e->getMessage());

            return false;
        }
    }
        public function recordView($customer_id, $product_id)
    {
        try {

            $stmt = $this->conn->prepare("
                INSERT INTO product_views (customer_id, product_id)
                VALUES (?, ?)
                ON DUPLICATE KEY UPDATE viewed_at = NOW()
            ");

            $stmt->bind_param("ii", $customer_id, $product_id);
            $stmt->execute();
            $stmt->close();

            return true;

        } catch (Throwable $e) {

            error_log($e->getMessage());

            return false;
        }
    }


    public function getRecentlyPurchased($customer_id, $limit = 6)
    {
        try {

            $sql = "
                SELECT p.*, c.cat_name, b.brand_name
                FROM products p
                JOIN categories c ON p.product_cat = c.cat_id
                JOIN brands b ON p.product_brand = b.brand_id
                JOIN (
                    SELECT od.product_id, MAX(o.order_id) AS last_order
                    FROM orderdetails od
                    JOIN orders o ON o.order_id = od.order_id
                    WHERE o.customer_id = ?
                    GROUP BY od.product_id
                ) bought ON bought.product_id = p.product_id
                ORDER BY bought.last_order DESC
                LIMIT ?
            ";

            return $this->fetchProducts($sql, "ii", (int) $customer_id, (int) $limit);

        } catch (Throwable $e) {

            error_log($e->getMessage());

            return [];
        }
    }


    public function getRecentlyViewed($customer_id, $limit = 6)
    {
        try {

            $sql = "
                SELECT p.*, c.cat_name, b.brand_name
                FROM product_views v
                JOIN products p ON p.product_id = v.product_id
                JOIN categories c ON p.product_cat = c.cat_id
                JOIN brands b ON p.product_brand = b.brand_id
                WHERE v.customer_id = ?
                ORDER BY v.viewed_at DESC
                LIMIT ?
            ";

            return $this->fetchProducts($sql, "ii", (int) $customer_id, (int) $limit);

        } catch (Throwable $e) {

            error_log($e->getMessage());

            return [];
        }
    }


    public function getBestSellers($limit = 6)
    {
        try {

            $sql = "
                SELECT p.*, c.cat_name, b.brand_name
                FROM products p
                JOIN categories c ON p.product_cat = c.cat_id
                JOIN brands b ON p.product_brand = b.brand_id
                LEFT JOIN (
                    SELECT product_id, SUM(qty) AS sold
                    FROM orderdetails
                    GROUP BY product_id
                ) s ON s.product_id = p.product_id
                ORDER BY COALESCE(s.sold, 0) DESC, p.product_id DESC
                LIMIT ?
            ";

            return $this->fetchProducts($sql, "i", (int) $limit);

        } catch (Throwable $e) {

            error_log($e->getMessage());

            return [];
        }
    }


    public function getRecommendations($customer_id, $limit = 6)
    {
        try {

            $stmt = $this->conn->prepare("
                SELECT DISTINCT p.product_cat, p.product_brand
                FROM products p
                WHERE p.product_id IN (
                    SELECT od.product_id
                    FROM orderdetails od
                    JOIN orders o ON o.order_id = od.order_id
                    WHERE o.customer_id = ?
                    UNION
                    SELECT product_id FROM product_views WHERE customer_id = ?
                    UNION
                    SELECT product_id FROM wishlist WHERE customer_id = ?
                )
            ");

            $stmt->bind_param("iii", $customer_id, $customer_id, $customer_id);
            $stmt->execute();

            $interests = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            $stmt->close();

            if (empty($interests)) {
                return $this->getBestSellers($limit);
            }

            $cats   = array_values(array_unique(array_map('intval', array_column($interests, 'product_cat'))));
            $brands = array_values(array_unique(array_map('intval', array_column($interests, 'product_brand'))));

            $catMarks   = implode(',', array_fill(0, count($cats), '?'));
            $brandMarks = implode(',', array_fill(0, count($brands), '?'));

            $sql = "
                SELECT p.*, c.cat_name, b.brand_name
                FROM products p
                JOIN categories c ON p.product_cat = c.cat_id
                JOIN brands b ON p.product_brand = b.brand_id
                LEFT JOIN (
                    SELECT product_id, SUM(qty) AS sold
                    FROM orderdetails
                    GROUP BY product_id
                ) s ON s.product_id = p.product_id
                WHERE (p.product_cat IN ($catMarks) OR p.product_brand IN ($brandMarks))
                AND p.product_id NOT IN (
                    SELECT od.product_id
                    FROM orderdetails od
                    JOIN orders o ON o.order_id = od.order_id
                    WHERE o.customer_id = ?
                )
                ORDER BY COALESCE(s.sold, 0) DESC, p.product_id DESC
                LIMIT ?
            ";

            $types  = str_repeat('i', count($cats) + count($brands)) . 'ii';
            $params = array_merge($cats, $brands, [(int) $customer_id, (int) $limit]);

            $rows = $this->fetchProducts($sql, $types, ...$params);

            return empty($rows) ? $this->getBestSellers($limit) : $rows;

        } catch (Throwable $e) {

            error_log($e->getMessage());

            return [];
        }
    }
}

?>
