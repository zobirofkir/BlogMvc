<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require '/var/www/html/MvcPhp/vendor/autoload.php'; // Include Composer's autoloader

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class ContactModels
{
    private $database;
    private $mailer; // Added mailer property

    public function __construct($database)
    {
        $this->database = $database;
        $this->mailer = new PHPMailer(true); // Create a new PHPMailer instance
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
                // Send an email confirmation
                try {
                    $this->mailer->isSMTP();
                    $this->mailer->Host = 'smtp.gmail.com';
                    $this->mailer->SMTPAuth = true;
                    $this->mailer->Username = 'zobirofkir30@gmail.com';
                    $this->mailer->Password = 'sckpsehqlpkwuuok';
                    $this->mailer->SMTPSecure = 'tls';
                    $this->mailer->Port = 587;
                    $this->mailer->setFrom($email, $name);
                } catch (Exception $e) {
                    echo "Mailer Error: " . $this->mailer->ErrorInfo;
                }
        
                $this->mailer->addAddress('zobirofkir19@gmail.com', 'Zobir'); // Changed $username to $name
                $this->mailer->Subject = 'Contact Confirmation'; // Changed Subject
                $this->mailer->Body = 'Name : ' . $name . ', email : ' . $email . ', subject : ' . $subject . ', message : ' . $message;
                $this->mailer->send();

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
