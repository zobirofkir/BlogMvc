<?php
class RegiUser {
    private $database;

    public function __construct($database) {
        $this->database = $database;
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

    public function CheckExestingUserEmail($email){
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
