<?php

require_once __DIR__ . '/../core/db_class.php';

class CustomerClass extends Database
{

    public function emailExists($email)
    {
        $sql = "
            SELECT customer_email
            FROM customer
            WHERE customer_email = ?
            LIMIT 1
        ";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            error_log($this->conn->error);
            return false;
        }

        $stmt->bind_param("s", $email);

        $stmt->execute();

        $stmt->store_result();

        $exists = $stmt->num_rows > 0;

        $stmt->close();

        return $exists;
    }


    
public function addCustomer(
    $name,
    $email,
    $pass,
    $country,
    $city,
    $contact
) {

    
    $hashedPassword = password_hash(
        $pass,
        PASSWORD_DEFAULT
    );

    if ($hashedPassword === false) {
        return false;
    }

    $sql = "
        INSERT INTO customer
        (
            customer_name,
            customer_email,
            customer_pass,
            customer_country,
            customer_city,
            customer_contact,
            user_role
        )
        VALUES (?, ?, ?, ?, ?, ?, 2)
    ";

    $stmt = $this->conn->prepare($sql);

    if (!$stmt) {
        error_log($this->conn->error);
        return false;
    }

    $stmt->bind_param(
        "ssssss",
        $name,
        $email,
        $hashedPassword,
        $country,
        $city,
        $contact
    );

    $success = $stmt->execute();

    $stmt->close();

    return $success;
}



    public function getCustomerByEmail($email)
    {
        $sql = "
            SELECT *
            FROM customer
            WHERE customer_email = ?
            LIMIT 1
        ";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            error_log($this->conn->error);
            return false;
        }

        $stmt->bind_param("s", $email);

        $stmt->execute();

        $result = $stmt->get_result();

        $customer = $result->fetch_assoc();

        $stmt->close();

        return $customer ?: false;
    }


    public function login($email, $pass)
    {
        $customer = $this->getCustomerByEmail($email);

        if (!$customer) {
            return false;
        }

        if (
            password_verify(
                $pass,
                $customer['customer_pass']
            )
        ) {
            return $customer;
        }

        return false;
    }
}

?>
