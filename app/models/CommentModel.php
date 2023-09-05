<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    class CommentModels{
        private $database;
        public function __construct($database){
            $this->database = $database;
        }

        public function CreateCommentTable(){
            $CreateTable = "CREATE TABLE IF NOT EXISTS Comment(
                id INT PRIMARY KEY NOT NULL AUTO_INCREMENT, 
                comment VARCHAR(255) NOT NULL, 
                name VARCHAR(255) NOT NULL, 
                email VARCHAR(255) NOT NULL,
                website VARCHAR(255) NOT NULL
            )";

            $Create = $this->database->prepare($CreateTable);
            $Create->execute();
        }

        public function PostCommentModel($comment, $name, $email, $website) {

            try{
                                // Create the comment table if it doesn't exist
                $this->CreateCommentTable();
            
                // Define the SQL query for inserting data
                $insertDataIntoSql = "INSERT INTO Comment(comment, name, email, website) VALUES (:comment, :name, :email, :website)";
            
                // Prepare the SQL query
                $bindInsert = $this->database->prepare($insertDataIntoSql);
            
                // Bind parameters and execute the query
                $bindInsert->bindParam(':comment', $comment);
                $bindInsert->bindParam(':name', $name);
                $bindInsert->bindParam(':email', $email);
                $bindInsert->bindParam(':website', $website);
            
                // Execute the query
                $result = $bindInsert->execute();
            
                // Check if the query was successful
                if ($result) {
                    $response = ["seccess"=>true];
                    echo json_encode($response);
                    return;

                } else {
                    $response = ["seccess"=>false];
                    echo json_encode($response);
                    return;
                }

            }catch(PDOException $e){
                return "Bad Request" . $e->getMessage();
            }
        }

        public function GetComment()
        {
            try {
                $GetCommentSql = "SELECT * FROM Comment";
                $GetCommentIntoData = $this->database->prepare($GetCommentSql);
                $GetCommentIntoData->execute(); // Execute the query
    
                $GetCommentIntoDataBase = $GetCommentIntoData->fetchAll(PDO::FETCH_ASSOC);
    
                return $GetCommentIntoDataBase;
            } catch (PDOException $e) {
                return false; // Return false instead of a string
            }
        }
    
        public function DeleteCommentModels($commentId){
            try {
                $DeleteMySql = "DELETE FROM Comment WHERE id = :commentId";
                $DeleteUsingPrepare = $this->database->prepare($DeleteMySql);
                $DeleteUsingPrepare->bindParam(":commentId", $commentId, PDO::PARAM_INT);
        
                if ($DeleteUsingPrepare->execute()) {
                    return true; // Return true on success
                } else {
                    return false; // Return false on failure
                }
            } catch (PDOException $e) {
                // Handle any PDO exceptions
                return false . $e->getMessage();
            }
        }        
    }
?>