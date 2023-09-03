<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// Define the base directory path
define('BASE_DIR', __DIR__);

// Include the UserModel class definition using an absolute path
require_once BASE_DIR . '/app/models/UserModel.php';

// Include the PostData controller using an absolute path
require_once BASE_DIR . '/app/controllers/AuthController.php';

// Replace with your actual database credentials
$db_host = 'localhost';
$db_name = 'BlogMvc';
$db_user = 'admin';
$db_pass = 'admin';

try {
    $database = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Data Connection Failed: " . $e->getMessage();
    exit(); // Exit the script on database connection failure
}

// Create a new instance of PostData
$postDataController = new PostData($database);

// Handle the POST request (if any)
$postDataController->PostDataa();
$postDataController->GetBlog();
?>
