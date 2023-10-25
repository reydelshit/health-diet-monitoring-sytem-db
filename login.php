<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":
        $email = $_GET['email'];
        $password = $_GET['password'];

        $sql = "SELECT * FROM users WHERE email = :email AND password = :password";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($users) {
            $sql = "UPDATE users SET isLoggedIn = 1 WHERE email = :email AND password = :password";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);
            if ($stmt->execute()) {
                $response = [
                    "status" => "success",
                    "message" => "User login successful"
                ];
            } else {
                $response = [
                    "status" => "error",
                    "message" => "Failed to update user login status"
                ];
            }
        } else {
            // User not found, send an error response
            $response = [
                "status" => "error",
                "message" => "User not found"
            ];
        }

        echo json_encode($users);

        break;
}
