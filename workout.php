<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":
        if (isset($_GET['user_id'])) {
            $user_id_specific_user = $_GET['user_id'];
            $sql = "SELECT * FROM workout_plans WHERE user_id = :user_id";
        }

        if (isset($_GET['workout_id'])) {
            $workout_specific_user = $_GET['workout_id'];
            $sql = "SELECT * FROM workout_plans WHERE workout_id = :workout_id";
        }

        if (isset($sql)) {
            $stmt = $conn->prepare($sql);

            if (isset($user_id_specific_user)) {
                $stmt->bindParam(':user_id', $user_id_specific_user);
            }

            if (isset($workout_specific_user)) {
                $stmt->bindParam(':workout_id', $workout_specific_user);
            }

            $stmt->execute();
            $medical_history = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($medical_history);
        }
        break;

    case "POST":
        $workout_plans = json_decode(file_get_contents('php://input'));
        $sql = "INSERT INTO workout_plans (workout_id, workout_plans_name, workout_description, workout_mins, workout_when, created_at, workout_status, user_id) VALUES (null, :workout_plans_name, :workout_description, :workout_mins, :workout_when, :created_at, :workout_status, :user_id)";
        $stmt = $conn->prepare($sql);
        $created_at = date('Y-m-d');
        $workout_status = 'Ongoing';
        $stmt->bindParam(':workout_plans_name', $workout_plans->workout_plans_name);
        $stmt->bindParam(':workout_description', $workout_plans->workout_description);
        $stmt->bindParam(':workout_mins', $workout_plans->workout_mins);
        $stmt->bindParam(':workout_when', $workout_plans->workout_when);
        $stmt->bindParam(':created_at', $created_at);
        $stmt->bindParam(':workout_status', $workout_status);
        $stmt->bindParam(':user_id', $workout_plans->user_id);

        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "User added workout successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "User added workout failed"
            ];
        }

        echo json_encode($response);
        break;

    case "PUT":


        $workout_plans = json_decode(file_get_contents('php://input'));
        $indicator = $workout_plans->indicator;

        if ($workout_plans->indicator === 'update_workout') {
            $sql = "UPDATE workout_plans SET workout_plans_name=:workout_plans_name, workout_description=:workout_description, workout_mins=:workout_mins, updated_at=:updated_at WHERE workout_id = :workout_id";
            $stmt = $conn->prepare($sql);
            $updated_at = date('Y-m-d');
            $stmt->bindParam(':workout_id', $workout_plans->id);
            $stmt->bindParam(':workout_plans_name', $workout_plans->workout_plans_name);
            $stmt->bindParam(':workout_description', $workout_plans->workout_description);
            $stmt->bindParam(':workout_mins', $workout_plans->workout_mins);
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
        }


        if ($indicator === 'update_workout_status') {
            $sql = "UPDATE workout_plans SET workout_status = :workout_status WHERE workout_id = :workout_id";
            $stmt2 = $conn->prepare($sql); // Use a different variable for the second query's prepared statement
            $stmt2->bindParam(':workout_status', $workout_plans->workout_status);
            $stmt2->bindParam(':workout_id', $workout_plans->workout_id);

            if ($stmt2->execute()) {
                $response = [
                    "status" => "success",
                    "message" => "Workout status update successfully"
                ];
            } else {
                $response = [
                    "status" => "error",
                    "message" => "Workout status update failed"
                ];
            }
            echo json_encode($response);
        }

        break;

    case "DELETE":
        $sql = "DELETE FROM workout_plans WHERE workout_id = :workout_id";
        $path = explode('/', $_SERVER['REQUEST_URI']);

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':workout_id', $path[3]);

        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "Workout plans deleted successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "Workout plans deletion failed"
            ];
        }
}
