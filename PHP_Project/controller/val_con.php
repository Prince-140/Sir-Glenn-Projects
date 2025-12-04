<?php
session_start();
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$dbname = 'sensors';
$db_username = 'Prince';  
$db_password = '';
$db_port = 3307;



try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception("Invalid request method.");
    
    $fields = ['housename','firstname','lastname','username','password','confirm_password'];
    $data = array_map(fn($f) => trim($_POST[$f] ?? ''), $fields);
    [$homeName,$firstName,$lastName,$userName,$passWord,$confirmPassword] = $data;
    
    $errors = array_filter([
        empty($homeName) ? "Household Name is required" : null,
        empty($firstName) ? "First Name is required" : null,
        empty($lastName) ? "Last Name is required" : null,
        empty($userName) ? "Username is required" : null,
        empty($passWord) ? "Password is required" : null,
        empty($confirmPassword) ? "Confirm Password is required" : null,
        $passWord !== $confirmPassword ? "Passwords do not match" : null
    ]);
    
    if ($errors) throw new Exception(implode('<br>', $errors));
    
    $dsn = "mysql:host=$host;port=$db_port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $db_username, $db_password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    $check = $pdo->prepare("SELECT COUNT(*) FROM users WHERE userName = ?");
    $check->execute([$userName]);
    if ($check->fetchColumn() > 0) throw new Exception("Username already exists.");
    
    $stmt = $pdo->prepare("INSERT INTO users (homeName,firstName,lastName,userName,passWord) VALUES (?,?,?,?,?)");
    if ($stmt->execute([$homeName,$firstName,$lastName,$userName,password_hash($passWord,PASSWORD_DEFAULT)])) {
        echo json_encode(['success'=>true,'message'=>'Account created!','redirect'=>'../view/login.php']);
    } else {
        throw new Exception("Failed to execute insert.");
    }
    
} catch (PDOException $e) {
    $msg = $e->getMessage();
    if (strpos($msg, 'SQLSTATE[HY000] [1045]') !== false) $msg = "Database access denied.";
    elseif (strpos($msg, 'SQLSTATE[HY000] [1049]') !== false) $msg = "Database doesn't exist.";
    elseif (strpos($msg, 'SQLSTATE[42S02]') !== false) $msg = "Table 'users' doesn't exist.";
    elseif (strpos($msg, 'could not find driver') !== false) $msg = "PDO MySQL driver not installed.";
    elseif ($e->errorInfo[1] == 2002 || $e->errorInfo[1] == 2003) $msg = "Cannot connect to MySQL server.";
    
    echo json_encode(['success'=>false,'message'=>'Database Error: '.$msg]);
    
} catch (Exception $e) {
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
?>