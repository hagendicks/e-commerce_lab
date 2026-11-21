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
        public function getCustomerById($id)
    {
        $stmt = $this->conn->prepare("
            SELECT customer_id, customer_name, customer_email, customer_pass,
                   customer_country, customer_city, customer_contact, user_role
            FROM customer
            WHERE customer_id = ?
            LIMIT 1
        ");

        if (!$stmt) {
            error_log($this->conn->error);
            return false;
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();

        $customer = $stmt->get_result()->fetch_assoc();

        $stmt->close();

        return $customer ?: false;
    }


    public function updateCustomer($id, $name, $country, $city, $contact)
    {
        $stmt = $this->conn->prepare("
            UPDATE customer
            SET customer_name = ?, customer_country = ?, customer_city = ?, customer_contact = ?
            WHERE customer_id = ?
        ");

        if (!$stmt) {
            error_log($this->conn->error);
            return false;
        }

        $stmt->bind_param("ssssi", $name, $country, $city, $contact, $id);

        $success = $stmt->execute();

        $stmt->close();

        return $success;
    }


    public function checkPassword($id, $pass)
    {
        $customer = $this->getCustomerById($id);

        return $customer && password_verify($pass, $customer['customer_pass']);
    }


    public function changePassword($id, $newPass)
    {
        $hash = password_hash($newPass, PASSWORD_DEFAULT);

        $stmt = $this->conn->prepare("UPDATE customer SET customer_pass = ? WHERE customer_id = ?");

        if (!$stmt) {
            error_log($this->conn->error);
            return false;
        }

        $stmt->bind_param("si", $hash, $id);

        $success = $stmt->execute();

        $stmt->close();

        return $success;
    }


    public function deleteCustomer($id)
    {
        try {

            $stmt = $this->conn->prepare("DELETE FROM customer WHERE customer_id = ?");
            $stmt->bind_param("i", $id);

            $success = $stmt->execute();

            $stmt->close();

            return $success;

        } catch (mysqli_sql_exception $e) {

            error_log($e->getMessage());

            return false;
        }
    }
    
}


?>
