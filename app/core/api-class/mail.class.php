<?php

/*
  error_reporting(E_ALL);
  ini_set('display_errors', false); */

 require_once dirname(__FILE__)."/../../../libs/mailer524/class.phpmailer.php";

/**
 * Envoi Mail
 */
class Mail {

    protected $adminName = "TONGO-TECHNOLOGIES-SYSTEMS";
    
    protected $server = 'smtp.gmail.com';
    
    protected $account = 'tongotechnologie@gmail.com';
    
    protected $accountPwd = '&buildenberger&';

   
    /**
     * Constructeur
     * @param array $options
     */
    public function __construct() {
        
    }

    /**
     * 
     * @param type $To a qui on envoi
     * @param type $subject sujet du message
     * @param type $message message a envoye
     * @param type $altMessage alt
     * @param type $replyMail adresse a utiliser pour repondre
     * @param type $replyName nom a utiliser pour repondre
     * @param type $replyTo a sur la quelle on doit repondre
     * @param type $attach piece jointe
     * @return boolean
     */
    public function sendEmail($To, $subject, $message, $altMessage, $replyMail, $replyName, $replyTo, $attach = "") {

        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->SMTPDebug = 0;  // debugging: 1 = errors and messages, 2 = messages only
        $mail->SMTPAuth = true;  // authentication enabled
        $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for GMail
        $mail->Host = $this->server;
        $mail->Port = 465;
        $mail->Username = $this->account;
        $mail->Password = $this->accountPwd;
        $mail->SetFrom($this->account, $this->adminName);

        if (!isset($replyMail) || ($replyMail == '')) {
            $replyMail = $this->account;
            $replyName = $this->adminName;
        }

        if ($attach != "")
            $mail->AddAttachment($attach);

        $mail->AddReplyTo($replyMail, $replyName);
        $mail->IsHTML(true);
        //$mail->Body = $message;
        $body = $message;
        $mail->MsgHTML($body);
        $mail->AltBody = $altMessage;
        $mail->Subject = $subject;
        $mail->AddReplyTo($replyTo);
        $mail->AddAddress($To);

        if (!$mail->Send()) {
            return false;
        } else {
            return true;
        }
    }

}

