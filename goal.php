<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":

        $user_id_specific_user = $_GET['user_id'];

        $sql = "SELECT * FROM goal";
        $path = explode('/', $_SERVER['REQUEST_URI']);
        if (isset($path[2]) && is_numeric($path[2])) {
            $sql .= " WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $path[2]);
            $stmt->execute();
            $goal = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        if ($user_id_specific_user) {
            $sql .= " WHERE user_id = :user_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':user_id', $user_id_specific_user);
            $stmt->execute();
            $goal = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $goal = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        echo json_encode($goal);
        break;

    case "POST":
        $goal = json_decode(file_get_contents('php://input'));
        $sql = "INSERT INTO goal (goal_id, goal_type, goal_target, goal_month, created_at, user_id) VALUES (null, :goal_type, :goal_target, :goal_month, :created_at, :user_id)";
        $stmt = $conn->prepare($sql);
        $created_at = date('Y-m-d');
        $goal_month = date('Y-m');
        $stmt->bindParam(':goal_type', $goal->goal_type);
        $stmt->bindParam(':goal_target', $goal->goal_target);
        $stmt->bindParam(':goal_month', $goal_month);
        $stmt->bindParam(':user_id', $goal->user_id);
        $stmt->bindParam(':created_at', $created_at);


        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "User created successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "User creation failed"
            ];
        }

        echo json_encode($response);
        break;

    case "PUT":
        $goal = json_decode(file_get_contents('php://input'));
        $sql = "UPDATE goal SET goal_target=:goal_target WHERE goal_id = :goal_id";
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':goal_id', $goal->goal_id);
        $stmt->bindParam(':goal_target', $goal->goal_target);

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
}
