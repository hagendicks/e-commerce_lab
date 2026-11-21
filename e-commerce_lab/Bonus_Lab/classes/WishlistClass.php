<?php

require_once __DIR__ . '/../core/db_class.php';

class WishlistClass extends Database
{

    public function isInWishlist($customer_id, $product_id)
    {
        try {

            $stmt = $this->conn->prepare("
                SELECT 1 FROM wishlist
                WHERE customer_id = ? AND product_id = ?
                LIMIT 1
            ");

            $stmt->bind_param("ii", $customer_id, $product_id);
            $stmt->execute();

            $found = $stmt->get_result()->num_rows > 0;

            $stmt->close();

            return $found;

        } catch (Throwable $e) {

            error_log($e->getMessage());

            return false;
        }
    }


    public function addToWishlist($customer_id, $product_id)
    {
        try {

            $stmt = $this->conn->prepare("
                INSERT IGNORE INTO wishlist (customer_id, product_id)
                VALUES (?, ?)
            ");

            $stmt->bind_param("ii", $customer_id, $product_id);

            $success = $stmt->execute();

            $stmt->close();

            return $success;

        } catch (Throwable $e) {

            error_log($e->getMessage());

            return false;
        }
    }


    public function removeFromWishlist($customer_id, $product_id)
    {
        try {

            $stmt = $this->conn->prepare("
                DELETE FROM wishlist
                WHERE customer_id = ? AND product_id = ?
            ");

            $stmt->bind_param("ii", $customer_id, $product_id);

            $success = $stmt->execute();

            $stmt->close();

            return $success;

        } catch (Throwable $e) {

            error_log($e->getMessage());

            return false;
        }
    }


    public function getProductIds($customer_id)
    {
        try {

            $stmt = $this->conn->prepare("SELECT product_id FROM wishlist WHERE customer_id = ?");

            $stmt->bind_param("i", $customer_id);
            $stmt->execute();

            $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            $stmt->close();

            return array_map('intval', array_column($rows, 'product_id'));

        } catch (Throwable $e) {

            error_log($e->getMessage());

            return [];
        }
    }


    public function getItems($customer_id)
    {
        try {

            $stmt = $this->conn->prepare("
                SELECT p.*, c.cat_name, b.brand_name
                FROM wishlist w
                JOIN products p ON p.product_id = w.product_id
                JOIN categories c ON p.product_cat = c.cat_id
                JOIN brands b ON p.product_brand = b.brand_id
                WHERE w.customer_id = ?
                ORDER BY w.created_at DESC
            ");

            $stmt->bind_param("i", $customer_id);
            $stmt->execute();

            $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            $stmt->close();

            return $items ?: [];

        } catch (Throwable $e) {

            error_log($e->getMessage());

            return [];
        }
    }


    public function getCount($customer_id)
    {
        return count($this->getProductIds($customer_id));
    }

}

?>
