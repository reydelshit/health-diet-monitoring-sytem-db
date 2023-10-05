<?php

include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];
$response = null;

switch ($method) {
    case "GET":

        if (isset($_GET['user_id'])) {
            $user_id_specific_user = $_GET['user_id'];
            $sql = "SELECT * FROM medical_history WHERE user_id = :user_id";
        }

        // Check if 'medical_id' parameter is present in the URL
        if (isset($_GET['medical_id'])) {
            $medical_specific_user = $_GET['medical_id'];
            $sql = "SELECT * FROM medical_history WHERE medical_id = :medical_id";
        }

        if (isset($sql)) {
            $stmt = $conn->prepare($sql);

            if (isset($user_id_specific_user)) {
                $stmt->bindParam(':user_id', $user_id_specific_user);
            }

            if (isset($medical_specific_user)) {
                $stmt->bindParam(':medical_id', $medical_specific_user);
            }

            $stmt->execute();
            $medical_history = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($medical_history);
        }
        break;

    case "POST":
        $medical_history = json_decode(file_get_contents('php://input'));

        $indicator = $medical_history->indicator;

        if ($indicator === 'post_medical_records_general') {
            $sql = "INSERT INTO medical_history (medical_id, medical_title, medical_desc, medical_date, user_id, created_at) VALUES (null, :medical_title, :medical_desc, :medical_date,  :user_id, :created_at)";
            $stmt = $conn->prepare($sql);
            $created_at = date('Y-m-d');
            $stmt->bindParam(':medical_title', $medical_history->medical_title);
            $stmt->bindParam(':medical_desc', $medical_history->medical_desc);
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
        }


        echo json_encode($response);
        break;

    case "PUT":
        $medical_history = json_decode(file_get_contents('php://input'));

        if ($medical_history->indicator === 'update_medical') {
            $sql = "UPDATE medical_history SET medical_title = :medical_title, medical_desc = :medical_desc, updated_at = :updated_at WHERE medical_id = :medical_id";
            $stmt = $conn->prepare($sql);
            $updated_at = date('Y-m-d');
            $stmt->bindParam(':medical_id', $medical_history->medical_id);
            $stmt->bindParam(':medical_title', $medical_history->medical_title);
            $stmt->bindParam(':medical_desc', $medical_history->medical_desc);
            $stmt->bindParam(':updated_at', $updated_at);

            if ($stmt->execute()) {
                $response = [
                    "status" => "success",
                    "message" => "Medical record updated successfully"
                ];
            } else {
                $response = [
                    "status" => "error",
                    "message" => "Medical record update failed"
                ];
            }
        }
        echo json_encode($response);
        break;

    case "DELETE":
        $path = explode('/', $_SERVER['REQUEST_URI']);
        $medical_id = $path[3];

        $sql = "DELETE FROM medical_history WHERE medical_id = :medical_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':medical_id', $medical_id);

        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "Medical record deleted successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "Medical record deletion failed"
            ];
        }

        echo json_encode($response);
}
