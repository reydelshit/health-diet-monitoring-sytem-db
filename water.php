<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":

        // $user_id_specific_user = $_GET['user_id'];

        $sql = "SELECT * FROM water";
        $path = explode('/', $_SERVER['REQUEST_URI']);
        if (isset($path[3]) && is_numeric($path[3])) {
            $sql .= " WHERE water_id = :water_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':workout_id', $path[3]);
            $stmt->execute();
            $water_log = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $water_log = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        if (isset($user_id_specific_user)) {
            $sql .= " WHERE user_id = :user_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':user_id', $user_id_specific_user);
            $stmt->execute();
            $water_log = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        // else {
        //     $stmt = $conn->prepare($sql);
        //     $stmt->execute();
        //     $water_log = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // }

        echo json_encode($water_log);
        break;

    case "POST":
        $water_log = json_decode(file_get_contents('php://input'));
        $sql = "INSERT INTO water (water_id, water_glasses, water_date, created_at, user_id) VALUES (NULL, :water_glasses, :water_date, :created_at, :user_id)";
        $stmt = $conn->prepare($sql);
        $created_at = date('Y-m-d');
        $stmt->bindParam(':water_glasses', $water_log->water_glasses);
        $stmt->bindParam(':water_date', $water_log->water_date);
        $stmt->bindParam(':created_at', $created_at);
        $stmt->bindParam(':user_id', $water_log->user_id);

        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "User added water successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "User added water failed"
            ];
        }

        echo json_encode($response);
        break;

    case "PUT":


        $water_log = json_decode(file_get_contents('php://input'));
        $indicator = $water_log->indicator;

        if ($water_log->indicator === 'update_workout') {
            $sql = "UPDATE water_log SET water_log_name=:water_log_name, workout_description=:workout_description, workout_mins=:workout_mins, updated_at=:updated_at WHERE workout_id = :workout_id";
            $stmt = $conn->prepare($sql);
            $updated_at = date('Y-m-d');
            $stmt->bindParam(':workout_id', $water_log->id);
            $stmt->bindParam(':water_log_name', $water_log->water_log_name);
            $stmt->bindParam(':workout_description', $water_log->workout_description);
            $stmt->bindParam(':workout_mins', $water_log->workout_mins);
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
            $sql = "UPDATE water_log SET workout_status = :workout_status WHERE workout_id = :workout_id";
            $stmt2 = $conn->prepare($sql); // Use a different variable for the second query's prepared statement
            $stmt2->bindParam(':workout_status', $water_log->workout_status);
            $stmt2->bindParam(':workout_id', $water_log->workout_id);

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
        $sql = "DELETE FROM water_log WHERE workout_id = :workout_id";
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
