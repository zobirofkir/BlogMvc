<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, PUT"); // Allow POST, GET, and PUT requests
header("Access-Control-Allow-Headers: Content-Type");

header('Content-Type: application/json'); // Set the response content type to JSON

error_reporting(E_ALL);
ini_set('display_errors', 1);

define('BASE_DIR', __DIR__);

require_once BASE_DIR . '/app/models/UserModel.php';
require_once BASE_DIR . '/app/controllers/AuthController.php';

require_once BASE_DIR . '/app/models/ContactModel.php';
require_once BASE_DIR . '/app/controllers/ContactController.php';

require_once BASE_DIR . '/app/models/CommentModel.php';
require_once BASE_DIR . '/app/controllers/CommentController.php';

// Define your database credentials
$dbHost = 'localhost';
$dbName = 'BLOGMVC';
$dbUsername = 'admin';
$dbPassword = 'admin';

try {
    $database = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8", $dbUsername, $dbPassword);
    $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $response = ["success" => false, "message" => "Database Error: " . $e->getMessage()];
    echo json_encode($response);
    exit();
}

// Determine the requested action based on the HTTP request method and URL
$authController = new AuthController($database);

// Contact Table
$CreateTable = new ContactController($database);

// Comment Table
$CreateCommentTable = new CommentController($database);

// Define routes for login, register, contact, comment, and user actions
$baseUri = "/MvcPhp/index.php"; // The base URI where your application is hosted

$routes = [
    "{$baseUri}/login" => "LoginUserData",
    "{$baseUri}/register" => "PostUserRegister",
    "{$baseUri}/contact" => "PostContact",
    "{$baseUri}/comment" => "PostCommentController",
    "{$baseUri}/get/comment" => "GetAllComment",
    "{$baseUri}/get/user" => "GetUserController",
    "{$baseUri}/update" => "UpdateUser",
    "{$baseUri}/delete" => "DeleteCommentControllers",
];

$requestUri = $_SERVER['REQUEST_URI'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    foreach ($routes as $route => $action) {
        if (strpos($requestUri, $route) !== false) {
            if ($action === "PostContact") {
                $CreateTable->$action();
                exit();
            } elseif ($action === "PostCommentController") {
                $CreateCommentTable->$action();
                exit();
            } else {
                // Call the corresponding controller method
                $authController->$action();
                exit(); // Stop processing after handling the request
            }
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    foreach ($routes as $route => $action) {
        if (strpos($requestUri, $route) !== false) {
            if ($action === "GetAllComment") {
                $CreateCommentTable->$action();
                exit();
            } elseif ($action === "GetUserController") {
                $authController->$action();
                exit();
            }
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] === "PUT") {
    foreach ($routes as $route => $action) {
        if (strpos($requestUri, $route) !== false) {
            if ($action === "UpdateUser") {
                $authController->$action();
                exit();
            }
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
    foreach ($routes as $route => $action) {
        if (strpos($requestUri, $route) !== false) {
            if ($action === "DeleteCommentControllers") {
                $CreateCommentTable->$action();
                exit();
            }
        }
    }
}


$response = ["success" => false, "message" => "Invalid request"];
echo json_encode($response);
?>
