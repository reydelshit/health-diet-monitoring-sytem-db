<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":

        $user_id_specific_user = $_GET['id'];

        $sql = "SELECT * FROM meal_diary";
        $path = explode('/', $_SERVER['REQUEST_URI']);
        if (isset($path[2]) && is_numeric($path[2])) {
            $sql .= " WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $path[2]);
            $stmt->execute();
            $users = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        if ($user_id_specific_user) {
            $sql .= " WHERE user_id = :user_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':user_id', $user_id_specific_user);
            $stmt->execute();
            $meal = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $meal = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        echo json_encode($meal);
        break;

    case "POST":
        $meal = json_decode(file_get_contents('php://input'));
        $sql = "INSERT INTO meal_diary (meal_id, meal_name, meal_time, calorie_intake, macro_fats, macro_proteins, macro_carbs, created_at, user_id) VALUES (null, :meal_name, :meal_time, :calorie, :macro_fats, :macro_proteins, :macro_carbs, :created_at, :user_id)";
        $stmt = $conn->prepare($sql);
        $created_at = date('Y-m-d');
        // $meal_time = date('Y-m-d H:i:s');
        $stmt->bindParam(':meal_name', $meal->meal_name);
        $stmt->bindParam(':meal_time', $meal->meal_time);
        $stmt->bindParam(':calorie', $meal->calorie);
        $stmt->bindParam(':macro_fats', $meal->macro_fats);
        $stmt->bindParam(':macro_proteins', $meal->macro_proteins);
        $stmt->bindParam(':macro_carbs', $meal->macro_carbs);
        $stmt->bindParam(':user_id', $meal->user_id);
        // $stmt->bindParam(':nutriInfo', $meal->nutriInfo);
        $stmt->bindParam(':created_at', $created_at);

        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "User successfully added meal"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "User added meal failed"
            ];
        }

        echo json_encode($response);
        break;

    case "PUT":
        $user = json_decode(file_get_contents('php://input'));
        $sql = "UPDATE users SET name= :name, email=:email, phone=:phone, updated_at=:updated_at WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $updated_at = date('Y-m-d');
        $stmt->bindParam(':id', $user->id);
        $stmt->bindParam(':name', $user->name);
        $stmt->bindParam(':email', $user->email);
        $stmt->bindParam(':phone', $user->phone);
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
        $sql = "DELETE FROM users WHERE id = :id";
        $path = explode('/', $_SERVER['REQUEST_URI']);

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $path[2]);

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
