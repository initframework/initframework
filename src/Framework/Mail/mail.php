<?php
namespace Framework\Mail;
use Framework\Mail\PHPMailer;
use Framework\Mail\SMTP;
use Framework\Handler\IException;

class Mail 
{

   private static $mail;
   private static $body;

   public function setup()
   {
      self::$mail = new PHPMailer();

      // set mailer
      switch (MAIL_DRIVER) {
         case 'mail':
            self::$mail->IsMail();
            break;
         case 'smtp':
            self::$mail->IsSMTP();
            self::$mail->Host = MAIL_SMTP_HOST;
            self::$mail->Port = MAIL_SMTP_PORT;
            self::$mail->SMTPAuth = MAIL_SMTP_AUTH;
            self::$mail->Username = MAIL_SMTP_USERNAME;
            self::$mail->Password = MAIL_SMTP_PASSWORD;
            self::$mail->SMTPSecure = MAIL_SMTP_SECURE != 'none' ? MAIL_SMTP_SECURE : '';
            self::$mail->Timeout = MAIL_SMTP_TIMEOUT;
            break;
         case 'sendmail':
            self::$mail->IsSendmail();
            break;
         default:
            break;
      }
      
   }

   public static function asHTML(string $message)
   {
      self::setup();
      self::$body = $message;
      self::$mail->IsHTML(true);
      return new Mail;
   }

   public static function asText(string $message, $wrap = 50)
   {
      self::setup();
      self::$body = $message;
      self::$mail->WordWrap = $wrap;
      self::$mail->IsHTML(false);
      return new Mail;
   }

   public static function send(string $from, string $to, string $subject, string $reply = ':')
   {
      $from = explode(":",$from); $to = explode(":",$to); $reply = explode(":",$reply);
      self::$mail->SetFrom($from[0], $from[1] ?? '');
      self::$mail->AddAddress($to[0], $to[1] ?? '');
      self::$mail->AddReplyTo($reply[0] ?? '', $reply[1] ?? '');
      self::$mail->Subject = $subject;
      self::$mail->Body = self::$body ?? '';
      try {
         if (self::$mail->Send()) {
            return true;
         } else {
            throw new IException("Error: mail not sent.");
            return false;
         }
      } catch (IException $ex) {
         $ex->handle();
      }
   }

   public static function sendMultiple(string $from, array $to, string $subject, string $reply = ':')
   {
      $from = explode(":",$from); $reply = explode(":",$reply);
      foreach ($to as $person) {
         $addressTo = explode(":",$person); 
         self::$mail->AddAddress($addressTo[0], $addressTo[1] ?? '');
      }
      self::$mail->SetFrom($from[0], $from[1] ?? '');
      self::$mail->AddReplyTo($reply[0] ?? '', $reply[1] ?? '');
      self::$mail->Subject = $subject;
      self::$mail->Body = self::$body ?? '';
      try {
         if (self::$mail->Send()) {
            return true;
         } else {
            throw new IException("Error: mail not sent.");
            return false;
         }
      } catch (IException $ex) {
         $ex->handle();
      }
   }

   public static function withAttachment($path, $name = '', $encoding = 'base64', $type = '', $disposition = 'attachment')
   {
      self::$mail->AddAttachment($path, $name = '', $encoding = 'base64', $type = '', $disposition = 'attachment');
      return new Mail;
   }

}
