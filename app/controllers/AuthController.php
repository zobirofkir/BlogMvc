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

    public function GetUserController(){
        $GetUserDataController = new RegiUser($this->database);
        $GetUserJson = $GetUserDataController->GetUserModel();
        if (!empty($GetUserJson)){
            $response = ["success"=>true, "data"=>$GetUserJson];
            echo json_encode($response);
            return;
        }else{
            $response = ["success"=>false];
            echo json_encode($response);
            return;
        }
    }
    public function UpdateUser() {
        try{
            if ($_SERVER["REQUEST_METHOD"] === "PUT") {
                $PostData = file_get_contents("php://input");
                $PostDataArray = json_decode($PostData, true);
                
                if (isset($PostDataArray["userId"]) && isset($PostDataArray["newUsername"]) && isset($PostDataArray["newEmail"]) && isset($PostDataArray["newPassword"]) && isset($PostDataArray["newDate"])) {
                    $userId = intval($PostDataArray["userId"]);
                    $newUsername = htmlspecialchars($PostDataArray["newUsername"], FILTER_SANITIZE_SPECIAL_CHARS);
                    $newEmail = filter_var($PostDataArray["newEmail"], FILTER_VALIDATE_EMAIL);
                    $newPassword = htmlspecialchars($PostDataArray["newPassword"], FILTER_SANITIZE_SPECIAL_CHARS);
                    $newDate = intval($PostDataArray["newDate"]);
            
                    $UpdateClass = new RegiUser($this->database);
                    $UpdateFunction = $UpdateClass->UpdateUserById($userId, $newUsername, $newEmail, $newPassword, $newDate);
            
                    if ($UpdateFunction === true) {
                        $response = ["success" => true];
                        http_response_code(200); // Success
                    }else if ($UpdateFunction === false){
                        $response = ["success" => false, "message" => "Failed to update user"];
                        http_response_code(400); // Bad Request
                    }
                    else {
                        $response = ["success" => false];
                        http_response_code(400); // Bad Request
                    }
                } else {
                    $response = ["success" => false];
                    http_response_code(400); // Bad Request
                }
            
                header('Content-Type: application/json'); // Set the response content type to JSON
                echo json_encode($response);
            }    
        }catch(PDOException $e){
            return false . $e->getMessage();
        }
    }    
}
?>
