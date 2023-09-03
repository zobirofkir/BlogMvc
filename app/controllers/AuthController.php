<?php
require_once BASE_DIR . '/app/models/UserModel.php';

class AuthController {
    private $database;

    public function __construct($database) {
        $this->database = $database;
    }

    public function PostUserRegister() {
        header('Content-Type: application/json'); // Set the response content type to JSON
    
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $PostData = file_get_contents("php://input");
            $PostDataArray = json_decode($PostData, true);
    
            // Check if the required fields are present in the request
            if (
                isset($PostDataArray['username']) &&
                isset($PostDataArray['email']) &&
                isset($PostDataArray['password']) &&
                isset($PostDataArray['date'])
            ) {
                $username = htmlspecialchars($PostDataArray['username'], FILTER_SANITIZE_SPECIAL_CHARS);
                $email = filter_var($PostDataArray['email'], FILTER_VALIDATE_EMAIL);
                $password = htmlspecialchars($PostDataArray['password'], FILTER_SANITIZE_SPECIAL_CHARS);
                $date = htmlspecialchars($PostDataArray['date'], FILTER_VALIDATE_INT); // Keep it as a string
    
                // Check if the email is already registered
                $existingUser = new RegiUser($this->database);
                $existingEmailCount = $existingUser->CheckExestingUserEmail($email);
    
                if ($existingEmailCount > 0) {
                    $response = ["success" => false, "message" => "Email address already exists"];
                    echo json_encode($response);
                    return;
                }
    
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
                $userModel = new RegiUser($this->database);
                $registrationResult = $userModel->userRegister($username, $email, $hashedPassword, $date);
    
                if ($registrationResult === true) { // Check for successful registration
                    $response = ["success" => true, "message" => "User registered successfully"];
                    echo json_encode($response);
                    return; // Return is not necessary here
                } else {
                    $response = ["success" => false, "message" => "User registration failed: " . $registrationResult];
                    echo json_encode($response);
                    return; // Return is not necessary here
                }
            }
        }
    
        $response = ["success" => false, "message" => "Invalid request"];
        echo json_encode($response);
    }
        

    public function LoginUserData() {
        header('Content-Type: application/json'); // Set the response content type to JSON

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $PostData = file_get_contents("php://input");
            $PostDataArray = json_decode($PostData, true);

            if (isset($PostDataArray["email"]) && isset($PostDataArray["password"])) {
                $email = htmlspecialchars($PostDataArray["email"], FILTER_SANITIZE_EMAIL);
                $password = htmlspecialchars($PostDataArray["password"], FILTER_SANITIZE_SPECIAL_CHARS);

                $LogiUser = new RegiUser($this->database);
                $ExecLoginUser = $LogiUser->loginUser($email, $password);

                if ($ExecLoginUser) {
                    $response = ["success" => true, "message" => "Login successfully"];
                    echo json_encode($response);
                    return;
                } else {
                    $response = ["success" => false, "message" => "Invalid Email Or Password"];
                    echo json_encode($response);
                    return;
                }
            }
        }

        $response = ["success" => false, "message" => "Invalid request"];
        echo json_encode($response); // Echo the JSON response
    }
}
?>
