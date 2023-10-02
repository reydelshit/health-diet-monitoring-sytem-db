<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":

        $user_id_specific_user = $_GET['user_id'];


        $sql = "SELECT * FROM workout_plans";
        $path = explode('/', $_SERVER['REQUEST_URI']);
        if (isset($path[3]) && is_numeric($path[3])) {
            $sql .= " WHERE workout_id = :workout_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':workout_id', $path[3]);
            $stmt->execute();
            $users = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        if ($user_id_specific_user) {
            $sql .= " WHERE user_id = :user_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':user_id', $user_id_specific_user);
            $stmt->execute();
            $workout_plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $workout_plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        echo json_encode($workout_plans);
        break;

    case "POST":
        $workout_plans = json_decode(file_get_contents('php://input'));
        $sql = "INSERT INTO workout_plans (workout_id, workout_plans_name, workout_mins, workout_when, created_at, user_id) VALUES (null, :workout_plans_name, :workout_mins, :workout_when, :created_at, :user_id)";
        $stmt = $conn->prepare($sql);
        $created_at = date('Y-m-d');
        $stmt->bindParam(':workout_plans_name', $workout_plans->workout_plans_name);
        $stmt->bindParam(':workout_mins', $workout_plans->workout_mins);
        $stmt->bindParam(':workout_when', $workout_plans->workout_when);
        $stmt->bindParam(':created_at', $created_at);
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
        $user = json_decode(file_get_contents('php://input'));
        $sql = "UPDATE workout_plans SET name= :name, email=:email, birthday=:birthday, gender=:gender, weight=:weight, height=:height, updated_at=:updated_at WHERE workout_id = :workout_id";
        $stmt = $conn->prepare($sql);
        $updated_at = date('Y-m-d');
        $stmt->bindParam(':id', $user->id);
        $stmt->bindParam(':name', $user->name);
        $stmt->bindParam(':email', $user->email);
        $stmt->bindParam(':birthday', $user->birthday);
        $stmt->bindParam(':gender', $user->gender);

        $stmt->bindParam(':height', $user->height);
        $stmt->bindParam(':weight', $user->weight);
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
        $sql = "DELETE FROM workout_plans WHERE workout_id = :workout_id";
        $path = explode('/', $_SERVER['REQUEST_URI']);

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':workout_id', $path[2]);

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
