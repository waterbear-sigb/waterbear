<?php

include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."classes_ext/swiftmailer/swift_required.php");

function plugin_div_mail ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $sujet=$parametres["sujet"];
    $from=$parametres["from"]; // un string ou array
    $to=$parametres["to"];
    $body=$parametres["body"];
    $type=$parametres["type"];
    $sendmail=$parametres["sendmail"]; // '/usr/sbin/sendmail -bs'
    $smtp_server=$parametres["smtp_server"];
    $smtp_port=$parametres["smtp_port"];
    $smtp_user=$parametres["smtp_user"];
    $smtp_pwd=$parametres["smtp_pwd"];
    
    if ($type == "") {
        $type="text/html";
    }
    try {
        // Create the message
        $message = Swift_Message::newInstance();
    
          // Give the message a subject
          $message->setSubject($sujet);
        
          // Set the From address with an associative array
          $message->setFrom($from);
        
          // Set the To addresses with an associative array
          $message->setTo($to);
        
          // Give it a body
          $message->setBody($body, $type);
        
          // And optionally an alternative body
          //$message->addPart('<q>Here is the message itself</q>', 'text/html');
        
          // Optionally add any attachments
          //$message->attach(Swift_Attachment::fromPath('my-document.pdf'));
          
          if ($sendmail != "") {
            $transport = Swift_SendmailTransport::newInstance($sendmail);
          } else {
            $transport = Swift_SmtpTransport::newInstance($smtp_server, $smtp_port)
            ->setUsername($smtp_user)
            ->setPassword($smtp_pwd)
            ;
          }
          
          $mailer = Swift_Mailer::newInstance($transport);
          $result = $mailer->send($message);
      } catch (Exception $e) {
        $retour["succes"]=0;
        $retour["erreur"]=$e->getMessage();
      }
      
      $retour["resultat"]=$result;
      return($retour);
    
    
}


?>