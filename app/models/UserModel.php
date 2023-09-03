<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require './vendor/autoload.php';

class CreateUserBlog {
    private $database;

    public function __construct($database) {
        $this->database = $database;
    }

    public function createBlogTable() {
        try {
            // Create the 'Blog' table if it doesn't exist
            $createTable = "CREATE TABLE IF NOT EXISTS BLOG (
                id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
                title VARCHAR(120) NOT NULL,
                username VARCHAR(120) NOT NULL,
                text_title VARCHAR(120) NOT NULL
            )";
            $stmt = $this->database->prepare($createTable);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Error creating table: " . $e->getMessage();
        }
    }

    public function postUser($title, $username, $text_title) {
        try {
            // Call the createBlogTable method to create the 'Blog' table
            $this->createBlogTable();
    
            // Check if a blog with the same title already exists
            $checkExisting = "SELECT COUNT(*) FROM BLOG WHERE title=:title";
            $checkExistingStmt = $this->database->prepare($checkExisting);
            $checkExistingStmt->bindParam(':title', $title);
            $checkExistingStmt->execute();
    
            $existingCount = $checkExistingStmt->fetchColumn();
    
            if ($existingCount == 0) {
                // Insert data into the 'Blog' table only if the blog doesn't exist
                $postData = "INSERT INTO BLOG (title, username, text_title) VALUES (:title, :username, :text_title)";
                $stmt = $this->database->prepare($postData);
                $stmt->bindParam(":title", $title);
                $stmt->bindParam(":username", $username);
                $stmt->bindParam(":text_title", $text_title);
                $stmt->execute();
                
                echo "Blog inserted successfully.";
            } else {
                echo "Blog with title '$title' already exists.";
            }
        } catch (PDOException $e) {
            echo "Error inserting data: " . $e->getMessage();
        }
    }
    
    public function getBlogData() {
        try {
            // Call the createBlogTable method to ensure the table exists
            $this->createBlogTable();
    
            // Query to retrieve data from the 'Blog' table
            $getData = "SELECT * FROM BLOG";
            $stmt = $this->database->prepare($getData);
            $stmt->execute();
    
            // Fetch data as an associative array
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Check if there is any data
            if ($result) {
                // Return the data as JSON
                echo json_encode($result);
            } else {
                // Return a message if no data is found
                echo json_encode(["message" => "No data found"]);
            }
        } catch (PDOException $e) {
            echo "Error retrieving data: " . $e->getMessage();
        }
    }

    
}
?>
