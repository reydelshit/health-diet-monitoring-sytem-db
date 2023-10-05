<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":

        // $user_id_specific_user = $_GET['user_id'];

        $sql = "SELECT * FROM sleep";
        $path = explode('/', $_SERVER['REQUEST_URI']);
        if (isset($path[3]) && is_numeric($path[3])) {
            $sql .= " WHERE sleep_id = :sleep_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':sleep_id', $path[3]);
            $stmt->execute();
            $sleep_log = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $sleep_log = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        if (isset($user_id_specific_user)) {
            $sql .= " WHERE user_id = :user_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':user_id', $user_id_specific_user);
            $stmt->execute();
            $sleep_log = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }


        echo json_encode($sleep_log);
        break;

    case "POST":
        $sleep_log = json_decode(file_get_contents('php://input'));
        $sql = "INSERT INTO sleep (sleep_hours, sleep_time, user_id, created_at) VALUES (:sleep_hours, :sleep_time, :user_id,:created_at)";
        $stmt = $conn->prepare($sql);
        $created_at = date('Y-m-d');
        $stmt->bindParam(':sleep_hours', $sleep_log->sleep_hours);
        $stmt->bindParam(':sleep_time', $sleep_log->sleep_time);
        $stmt->bindParam(':user_id', $sleep_log->user_id);
        $stmt->bindParam(':created_at', $created_at);

        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "User added sleep successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "User added sleep failed"
            ];
        }

        echo json_encode($response);
        break;

    case "PUT":
        $sleep_log = json_decode(file_get_contents('php://input'));
        $indicator = $sleep_log->indicator;

        if ($sleep_log->indicator === 'update_workout') {
            $sql = "UPDATE sleep_log SET sleep_log_name=:sleep_log_name, workout_description=:workout_description, workout_mins=:workout_mins, updated_at=:updated_at WHERE workout_id = :workout_id";
            $stmt = $conn->prepare($sql);
            $updated_at = date('Y-m-d');
            $stmt->bindParam(':workout_id', $sleep_log->id);
            $stmt->bindParam(':sleep_log_name', $sleep_log->sleep_log_name);
            $stmt->bindParam(':workout_description', $sleep_log->workout_description);
            $stmt->bindParam(':workout_mins', $sleep_log->workout_mins);
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
            $sql = "UPDATE sleep_log SET workout_status = :workout_status WHERE workout_id = :workout_id";
            $stmt2 = $conn->prepare($sql); // Use a different variable for the second query's prepared statement
            $stmt2->bindParam(':workout_status', $sleep_log->workout_status);
            $stmt2->bindParam(':workout_id', $sleep_log->workout_id);

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
        $sql = "DELETE FROM sleep_log WHERE workout_id = :workout_id";
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
