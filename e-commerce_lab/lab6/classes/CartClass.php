<?php

require_once __DIR__ . '/../core/db_class.php';

class CartClass extends Database
{

    public function isInCart($product_id, $ip)
    {
        $sql = "
            SELECT p_id
            FROM cart
            WHERE p_id = ? AND ip_add = ?
            LIMIT 1
        ";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            error_log($this->conn->error);
            return false;
        }

        $stmt->bind_param("is", $product_id, $ip);
        $stmt->execute();

        $found = $stmt->get_result()->num_rows > 0;

        $stmt->close();

        return $found;
    }


    public function addToCart($product_id, $qty, $ip, $customer_id = null)
    {
        $sql = "
            INSERT INTO cart (p_id, ip_add, c_id, qty)
            VALUES (?, ?, ?, ?)
        ";

        try {

            $stmt = $this->conn->prepare($sql);

            if (!$stmt) {
                error_log($this->conn->error);
                return false;
            }

            $stmt->bind_param("isii", $product_id, $ip, $customer_id, $qty);

            $success = $stmt->execute();

            $stmt->close();

            return $success;

        } catch (mysqli_sql_exception $e) {

            error_log($e->getMessage());

            return false;
        }
    }


    public function getCartItems($ip)
    {
        $sql = "
            SELECT cart.*, products.product_title, products.product_price, products.product_image
            FROM cart
            JOIN products ON cart.p_id = products.product_id
            WHERE cart.ip_add = ?
            ORDER BY products.product_title ASC
        ";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            error_log($this->conn->error);
            return [];
        }

        $stmt->bind_param("s", $ip);
        $stmt->execute();

        $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $stmt->close();

        return $items ?: [];
    }


    public function getCartCount($ip)
    {
        $sql = "
            SELECT COUNT(*) AS total
            FROM cart
            WHERE ip_add = ?
        ";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            error_log($this->conn->error);
            return 0;
        }

        $stmt->bind_param("s", $ip);
        $stmt->execute();

        $row = $stmt->get_result()->fetch_assoc();

        $stmt->close();

        return (int) ($row['total'] ?? 0);
    }


    public function getCartTotal($ip)
    {
        $total = 0.0;

        foreach ($this->getCartItems($ip) as $item) {
            $total += (int) $item['qty'] * (float) $item['product_price'];
        }

        return $total;
    }


    public function removeFromCart($product_id, $ip)
    {
        $sql = "
            DELETE FROM cart
            WHERE p_id = ? AND ip_add = ?
        ";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            error_log($this->conn->error);
            return false;
        }

        $stmt->bind_param("is", $product_id, $ip);

        $success = $stmt->execute();

        $stmt->close();

        return $success;
    }


    public function updateQty($product_id, $new_qty, $ip)
    {
        if ($new_qty < 1) {
            return false;
        }

        $sql = "
            UPDATE cart
            SET qty = ?
            WHERE p_id = ? AND ip_add = ?
        ";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            error_log($this->conn->error);
            return false;
        }

        $stmt->bind_param("iis", $new_qty, $product_id, $ip);

        $success = $stmt->execute();

        $stmt->close();

        return $success;
    }

    public function processPayment($customer_id, $ip, $reference, $amount, $currency = 'GHS')
{
    $invoice_no = mt_rand(100000, 999999);

    try {
        $this->conn->begin_transaction();

        $stmt = $this->conn->prepare("
            INSERT INTO orders (customer_id, invoice_no, order_date, order_status, created_at)
            VALUES (?, ?, CURDATE(), 'paid', NOW())
        ");
        $stmt->bind_param("ii", $customer_id, $invoice_no);
        $stmt->execute();
        $stmt->close();

        $order_id = (int) $this->conn->insert_id;

        $stmt = $this->conn->prepare("
            INSERT INTO orderdetails (order_id, product_id, qty)
            SELECT ?, p_id, qty FROM cart WHERE ip_add = ?
        ");
        $stmt->bind_param("is", $order_id, $ip);
        $stmt->execute();
        $copied = $stmt->affected_rows;
        $stmt->close();

        if ($copied < 1) {
            throw new Exception('Cart was empty while recording the order.');
        }

        $stmt = $this->conn->prepare("
            INSERT INTO payment (amt, customer_id, order_id, currency, payment_date, payment_ref)
            VALUES (?, ?, ?, ?, CURDATE(), ?)
        ");
        $stmt->bind_param("diiss", $amount, $customer_id, $order_id, $currency, $reference);
        $stmt->execute();
        $stmt->close();

        $stmt = $this->conn->prepare("DELETE FROM cart WHERE ip_add = ?");
        $stmt->bind_param("s", $ip);
        $stmt->execute();
        $stmt->close();

        $this->conn->commit();
        return $order_id;

    } catch (Throwable $e) {
        $this->conn->rollback();
        error_log('processPayment failed: ' . $e->getMessage());
        return false;
    }
}


public function getOrderById($order_id, $customer_id = null)
{
    $sql = "
        SELECT o.order_id, o.customer_id, o.invoice_no, o.order_date, o.order_status,
               p.amt, p.currency, p.payment_ref
        FROM orders o
        LEFT JOIN payment p ON p.order_id = o.order_id
        WHERE o.order_id = ?
    ";

    if ($customer_id !== null) {
        $sql .= " AND o.customer_id = ?";
    }

    $stmt = $this->conn->prepare($sql);

    if (!$stmt) {
        error_log($this->conn->error);
        return null;
    }

    if ($customer_id !== null) {
        $stmt->bind_param("ii", $order_id, $customer_id);
    } else {
        $stmt->bind_param("i", $order_id);
    }

    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$order) {
        return null;
    }

    $stmt = $this->conn->prepare("
        SELECT od.product_id, od.qty, pr.product_title, pr.product_price, pr.product_image
        FROM orderdetails od
        JOIN products pr ON pr.product_id = od.product_id
        WHERE od.order_id = ?
        ORDER BY pr.product_title ASC
    ");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $total = 0.0;
    foreach ($items as &$item) {
        $item['line_total'] = (int) $item['qty'] * (float) $item['product_price'];
        $total += $item['line_total'];
    }
    unset($item);

    $order['items'] = $items;
    $order['total'] = $total;

    return $order;
}

}

?>
