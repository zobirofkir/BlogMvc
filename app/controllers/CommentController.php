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
    }
?>