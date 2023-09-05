<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    require_once '/var/www/html/MvcPhp/app/models/CommentModel.php';

    class CommentController{
        private $database;
        public function __construct($database){
            $this->database = $database;
        }

        public function PostCommentController(){

            try{
                header('Content-Type: application/json');

                if ($_SERVER["REQUEST_METHOD"] === "POST"){
                    $PostData = file_get_contents("php://input");
                    $PostDataArray = json_decode($PostData, true);
    
                    if (isset($PostDataArray["comment"])&& isset($PostDataArray["name"]) && isset($PostDataArray["email"])&& isset($PostDataArray["website"])){
                        $comment = htmlspecialchars($PostDataArray["comment"], FILTER_SANITIZE_SPECIAL_CHARS);
                        $name = htmlspecialchars($PostDataArray["name"], FILTER_SANITIZE_SPECIAL_CHARS);
                        $email = htmlspecialchars($PostDataArray["email"], FILTER_SANITIZE_EMAIL);
                        $website = htmlspecialchars($PostDataArray["website"], FILTER_SANITIZE_SPECIAL_CHARS);
    
                        $InsertIntoCommentModels = new CommentModels($this->database);
                        $ExecComment = $InsertIntoCommentModels->PostCommentModel($comment, $name, $email, $website);
                        
                        if ($ExecComment === true){
                            $response = ["seccess"=>true];
                            echo json_encode($response);
                            return;
                        }else if ($ExecComment === false){
                            $response = ["seccess"=>false];
                            echo json_encode($response);
                            return;
                        }
                    }
                }
    
            }catch(PDOException $e){
                return false . $e->getMessage();
            }

        }

        public function GetAllComment()
        {
            header('Content-Type: application/json');
    
            $GetComment = new CommentModels($this->database);
            $GetAllComment = $GetComment->GetComment();
            
            if (!empty($GetAllComment)) { // Check if $GetAllComment is not empty
                echo json_encode(["success" => true, "comments" => $GetAllComment]);
            } else {
                echo json_encode(["success" => false, "message" => "No comments found"]);
            }
        }

        public function DeleteCommentControllers(){
            header("Content-Type: application/json");
        
            try {
                if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
                    $PostData = file_get_contents("php://input");
                    $DeteleDataArray = json_decode($PostData, true);
        
                    if (isset($DeteleDataArray["commentId"])) {
                        $commentId = $DeteleDataArray["commentId"];
        
                        $GetClass = new CommentModels($this->database);
                        $DeleteFunction = $GetClass->DeleteCommentModels($commentId);
        
                        if ($DeleteFunction === true) {
                            $response = ["success" => true];
                            echo json_encode($response);
                        } else {
                            $response = ["success" => false];
                            echo json_encode($response);
                        }
                    }
                }
            } catch (PDOException $S) {
                // Handle any PDO exceptions
                $response = ["error" => $S->getMessage()];
                http_response_code(500); // Internal Server Error
                echo json_encode($response);
            }
        }                    
    }
?>