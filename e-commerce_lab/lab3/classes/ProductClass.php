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
}

?>
