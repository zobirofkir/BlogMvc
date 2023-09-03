<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '/var/www/html/MvcPhp/app/models/ContactModel.php';

class ContactController
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function PostContact()
    {
        header('Content-Type: application/json');

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $SendData = file_get_contents("php://input");
            $SendDataArray = json_decode($SendData, true);

            if (
                isset($SendDataArray['name']) &&
                isset($SendDataArray['email']) &&
                isset($SendDataArray['subject']) &&
                isset($SendDataArray['message'])
            ) {
                $name = htmlspecialchars($SendDataArray['name'], FILTER_SANITIZE_SPECIAL_CHARS);
                $email = htmlspecialchars($SendDataArray['email'], FILTER_SANITIZE_EMAIL);
                $subject = htmlspecialchars($SendDataArray['subject'], FILTER_SANITIZE_SPECIAL_CHARS);
                $message = htmlspecialchars($SendDataArray['message'], FILTER_SANITIZE_SPECIAL_CHARS);

                $SendContactData = new ContactModels($this->database); // Corrected class name
                $VarContactData = $SendContactData->PostUserContact($name, $email, $subject, $message); // Corrected method name

                if ($VarContactData === true) {
                    $response = ["success" => true, "message" => "Contact has been sent"]; // Corrected message
                    echo json_encode($response);
                    return;
                }
            }
        }

        $response = ["success" => false, "message" => "Invalid request"];
        echo json_encode($response);
    }
}
?>
