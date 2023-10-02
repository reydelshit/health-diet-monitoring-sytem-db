<?php

include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":

        $user_id_specific_user = $_GET['user_id'];


        $sql = "SELECT * FROM medical_history";
        $path = explode('/', $_SERVER['REQUEST_URI']);
        if (isset($path[3]) && is_numeric($path[3])) {
            $sql .= " WHERE medical_id = :medical_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':medical_id', $path[3]);
            $stmt->execute();
            $medical_history = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        if ($user_id_specific_user) {
            $sql .= " WHERE user_id = :user_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':user_id', $user_id_specific_user);
            $stmt->execute();
            $medical_history = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $medical_history = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        echo json_encode($medical_history);
        break;

    case "POST":
        $medical_history = json_decode(file_get_contents('php://input'));
        $sql = "INSERT INTO medical_history (medical_id, medical_title, medical_date, user_id, created_at) VALUES (null, :medical_title, :medical_date,  :user_id, :created_at)";
        $stmt = $conn->prepare($sql);
        $created_at = date('Y-m-d');
        $stmt->bindParam(':medical_title', $medical_history->medical_title);
        $stmt->bindParam(':medical_date', $medical_history->medical_date);
        $stmt->bindParam(':user_id', $medical_history->user_id);
        $stmt->bindParam(':created_at', $created_at);

        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "User added medical history successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "User added medical history failed"
            ];
        }

        echo json_encode($response);
        break;

    case "PUT":
        $medical_history = json_decode(file_get_contents('php://input'));
        $sql = "UPDATE medical_history SET name= :name, email=:email, birthday=:birthday, gender=:gender, weight=:weight, height=:height, updated_at=:updated_at WHERE workout_id = :workout_id";
        $stmt = $conn->prepare($sql);
        $updated_at = date('Y-m-d');
        $stmt->bindParam(':id', $medical_history->id);
        $stmt->bindParam(':name', $medical_history->name);
        $stmt->bindParam(':email', $medical_history->email);
        $stmt->bindParam(':birthday', $medical_history->birthday);
        $stmt->bindParam(':gender', $medical_history->gender);

        $stmt->bindParam(':height', $medical_history->height);
        $stmt->bindParam(':weight', $medical_history->weight);
        $stmt->bindParam(':updated_at', $updated_at);

        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "User updated successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "User update failed"
            ];
        }

        echo json_encode($response);
        break;

    case "DELETE":
        $sql = "DELETE FROM medical_history WHERE medical_id = :medical_id";
        $path = explode('/', $_SERVER['REQUEST_URI']);

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':medical_id', $path[2]);

        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "User deleted successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "User deletion failed"
            ];
        }
}
