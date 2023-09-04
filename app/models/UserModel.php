<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '/var/www/html/MvcPhp/vendor/phpmailer/src/Exception.php';
require '/var/www/html/MvcPhp/vendor/phpmailer/src/PHPMailer.php';
require '/var/www/html/MvcPhp/vendor/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class RegiUser {
    private $database;
    private $mailer; // Added mailer property

    public function __construct($database) {
        $this->database = $database;
        $this->mailer = new PHPMailer(true); // Create a new PHPMailer instance
        $this->configureMailer(); // Call configureMailer to set up the mailer
    }

    private function configureMailer() {
        try {
            $this->mailer->isSMTP();
            $this->mailer->Host = 'smtp.gmail.com';
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = 'zobirofkir30@gmail.com';
            $this->mailer->Password = 'ynyggrmezccijixq';
            $this->mailer->SMTPSecure = 'tls';
            $this->mailer->Port = 587;
            $this->mailer->setFrom('zobirofkir30@gmail.com', 'Zobir');
        } catch (Exception $e) {
            echo "Mailer Error: " . $this->mailer->ErrorInfo;
        }
    }

    public function userTable() {
        $createUserTable = "CREATE TABLE IF NOT EXISTS User(id INT PRIMARY KEY AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, date DATE NOT NULL)";
        $createTable = $this->database->prepare($createUserTable);
        $createTable->execute();
    }

    public function userRegister($username, $email, $password, $date) {
        try {
            $this->userTable();

            $insertUserData = "INSERT INTO User(username, email, password, date) VALUES (:username, :email, :password, :date)";
            $insertIntoData = $this->database->prepare($insertUserData);
            $insertIntoData->bindParam(":username", $username);
            $insertIntoData->bindParam(":email", $email);
            $insertIntoData->bindParam(":password", $password);
            $insertIntoData->bindParam(":date", $date);

            if ($insertIntoData->execute()) {
                // Send a registration confirmation email
                $this->mailer->addAddress($email, $username);
                $this->mailer->Subject = 'Registration Confirmation';
                $this->mailer->Body = 'Thank you for registering!';
                $this->mailer->send();

                return true;
            } else {
                return false; // Return false on failure
            }
        } catch (PDOException $e) {
            return "PDOException: " . $e->getMessage(); // Return the error message on failure
        }
    }

    public function loginUser($email, $password) {
        try {
            $this->userTable();

            $selectEmail = "SELECT * FROM User WHERE email=:email";
            $selectUserByEmail = $this->database->prepare($selectEmail);
            $selectUserByEmail->bindParam(':email', $email);
            $selectUserByEmail->execute();
            $user = $selectUserByEmail->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return "PDOException: " . $e->getMessage();
        }
    }

    public function CheckExestingUserEmail($email){ // Fixed method name typo
        $this->userTable();

        $CheckUserSql = "SELECT * FROM User WHERE email = :email";

        $Cheking = $this->database->prepare($CheckUserSql);
        $Cheking->bindParam(":email", $email);
        $Cheking->execute();

        $CheckEmail = $Cheking->fetchColumn();

        return $CheckEmail;
    }
}
?>
