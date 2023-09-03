<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

class ContactModels
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function CreateContactTable()
    {
        $CreateTable = "CREATE TABLE IF NOT EXISTS Contact(
            id INT PRIMARY KEY NOT NULL AUTO_INCREMENT, 
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL, 
            subject VARCHAR(255) NOT NULL,
            message VARCHAR(255) NOT NULL 
        )";

        $LanchTable = $this->database->prepare($CreateTable);
        $LanchTable->execute();
    }

    public function PostUserContact($name, $email, $subject, $message)
    {
        try {
            $this->CreateContactTable();
            $InsertDataIntoContact = "INSERT INTO Contact(name, email, subject, message) VALUES (:name, :email, :subject, :message)";
            $ExecuteInsert = $this->database->prepare($InsertDataIntoContact);
            $ExecuteInsert->bindParam(":name", $name);
            $ExecuteInsert->bindParam(":email", $email);
            $ExecuteInsert->bindParam(":subject", $subject);
            $ExecuteInsert->bindParam(":message", $message);

            if ($ExecuteInsert->execute()) {
                $response = ["success" => true];
                echo json_encode($response);
                return true; // Return true for success
            } else {
                $response = ["success" => false];
                echo json_encode($response);
                return false; // Return false for failure
            }
        } catch (PDOException $e) {
            $response = ["success" => false, "message" => $e->getMessage()];
            echo json_encode($response);
            return false; // Return false for exception
        }
    }
}

?>
