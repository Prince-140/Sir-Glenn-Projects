<?php
header('Content-Type: application/json');
$con = mysqli_connect("localhost", "Prince", "", "sensors", "3307");
if (!$con) die(json_encode(['error' => 'DB connection failed']));

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $id = $_GET['id'] ?? null;
    $status = $_GET['status'] ?? null;
    
    if ($id) {
        $result = mysqli_query($con, "SELECT * FROM appliances WHERE Id = " . intval($id));
        $data = mysqli_fetch_assoc($result);
        echo json_encode($data ?: ['error' => 'Not found']);
    } elseif ($status === 'on' || $status === 'off') {
        $statusVal = $status === 'on' ? 1 : 0;
        $result = mysqli_query($con, "SELECT * FROM appliances WHERE Status = $statusVal");
        $appliances = [];
        while($row = mysqli_fetch_assoc($result)) $appliances[] = $row;
        echo json_encode(['appliances' => $appliances, 'count' => count($appliances)]);
    } else {
        $result = mysqli_query($con, "SELECT * FROM appliances");
        $appliances = [];
        while($row = mysqli_fetch_assoc($result)) $appliances[] = $row;
        echo json_encode(['appliances' => $appliances, 'count' => count($appliances)]);
    }
} elseif ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $name = mysqli_real_escape_string($con, $input['name'] ?? '');
    $type = mysqli_real_escape_string($con, $input['type'] ?? 'Light');
    $power = intval($input['power'] ?? 100);
    $status = isset($input['status']) ? ($input['status'] ? 1 : 0) : 0;
    
    $query = "INSERT INTO appliances (Name, Type, Status, WattUsage) VALUES ('$name', '$type', $status, $power)";
    mysqli_query($con, $query);
    $id = mysqli_insert_id($con);
    echo json_encode(['success' => true, 'id' => $id]);
} elseif ($method === 'PUT') {
    parse_str(file_get_contents('php://input'), $input);
    $id = intval($input['id'] ?? $_GET['id'] ?? 0);
    $status = isset($input['status']) ? ($input['status'] ? 1 : 0) : 0;
    
    mysqli_query($con, "UPDATE appliances SET Status = $status WHERE Id = $id");
    echo json_encode(['success' => true, 'updated' => mysqli_affected_rows($con)]);
} else {
    echo json_encode(['error' => 'Method not allowed']);
}
mysqli_close($con);
?>