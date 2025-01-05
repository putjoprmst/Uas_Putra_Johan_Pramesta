<?php
header("Content-Type: application/json");
include 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $conn = connect();
        $result = $conn->query("SELECT * FROM todo_list");
        $tasks = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($tasks);
        $conn->close();
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        $conn = connect();
        $stmt = $conn->prepare("INSERT INTO todo_list (task) VALUES (?)");
        $stmt->bind_param("s", $data->task);
        $stmt->execute();
        $stmt->close();
        $conn->close();
        echo json_encode(["message" => "Task added successfully"]);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        $conn = connect();
        $stmt = $conn->prepare("UPDATE todo_list SET task = ? WHERE id = ?");
        $stmt->bind_param("si", $data->task, $data->id);
        $stmt->execute();
        $stmt->close();
        $conn->close();
        echo json_encode(["message" => "Task updated successfully"]);
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));
        $conn = connect();
        $stmt = $conn->prepare("DELETE FROM todo_list WHERE id = ?");
        $stmt->bind_param("i", $data->id);
        $stmt->execute();
        $stmt->close();
        $conn->close();
        echo json_encode(["message" => "Task deleted successfully"]);
        break;

    default:
        echo json_encode(["message" => "Method not allowed"]);
        break;
}
?>
