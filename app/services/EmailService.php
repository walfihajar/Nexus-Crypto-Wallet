<?php
namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService {
    private $mailer;

    public function __construct() {
        $this->mailer = new PHPMailer(true);
        
        try {
            // Server settings
            $this->mailer->isSMTP();
            $this->mailer->Host = 'smtp.gmail.com';
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = 'ashrafchehboun@gmail.com';
            $this->mailer->Password = 'bwqo uged espj guxe';
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $this->mailer->Port = 465;
            
            // Default settings
            $this->mailer->isHTML(true);
            $this->mailer->setFrom('ashrafchehboun@gmail.com', 'NexusCryp');
            $this->mailer->CharSet = 'UTF-8';
        } catch (Exception $e) {
            error_log("Email setup error: " . $e->getMessage());
        }
    }
}