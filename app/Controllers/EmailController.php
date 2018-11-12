<?php
namespace App\Controllers;

use App\Controllers\Controller;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
class EmailController extends Controller{
     
    public function sendMail($template, $email = 'keyladrian7@gmail.com'){
        $mail = new PHPMailer(true);
        try{
            $mail->isSMTP(); 
            $mail->Host = 'mail.alla.ph';   
            $mail->SMTPAuth = true;
            $mail->Username = 'dealahos';
            $mail->Password = 'Poi650909^^';
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;
            $mail->setFrom('sevendeal@alla.ph', '7Deal Philippines');
            $mail->isHTML(true);
            $mail->Subject = 'SEVENDEAL VOUCHER';
            $mail->addAddress($email, 'SEVENDEAL');
            $mail->Body = $template;
            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
            $mail->send();
            return true;
        }catch(Exception $e){
            return $mail->ErrorInfo ;
        }
    }
}
