<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the UserModel class definition
require_once '/var/www/html/MvcPhp/app/models/UserModel.php';

class PostData {
    private $database;

    public function __construct($database) {
        $this->database = $database;
    }

    public function PostDataa() {

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            // Get the JSON data from the request body
            $postData = file_get_contents("php://input");
            $postDataArray = json_decode($postData, true);

            // Check if the keys "title," "username," and "text_title" exist in the JSON data
            if (isset($postDataArray["title"]) && isset($postDataArray["username"]) && isset($postDataArray["text_title"])) {
                // Retrieve the values from the JSON data
                $title = htmlspecialchars($postDataArray["title"], FILTER_SANITIZE_SPECIAL_CHARS);
                $username = htmlspecialchars($postDataArray["username"], FILTER_SANITIZE_SPECIAL_CHARS);
                $text_title = htmlspecialchars($postDataArray["text_title"], FILTER_SANITIZE_SPECIAL_CHARS);

                // Create a CreateUserBlog instance and call the PostUser method to insert data
                $userModel = new CreateUserBlog($this->database);
                $CheckingBlogs = $userModel->PostUser($title, $username, $text_title);

                // Create a JSON response indicating successful registration
                $response = ["message" => true];
                echo json_encode($response);
                return; // Add the return statement here

                
        
                if ($CheckingBlogs > 0){
                    $response = ["EXIST" => false];
                    return $response;
                }

                
            }else {
                // Handle the case when "title," "username," or "text_title" keys are missing in the JSON data
                $response = ["error" => false];
                echo json_encode($response);
                return; // Add the return statement here
            }
        }
    }
    public function GetBlog(){
            if ($_SERVER["REQUEST_METHOD"] === "GET"){
            $GetBlog = new CreateUserBlog($this->database);
            $GetBlog->GetBlogData();
        }

    }
}
?>
