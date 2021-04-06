<?php
namespace Services;
use PHPMailer\PHPMailer\PHPMailer;

class Mail 
{

   private static $mail;
   private static $body;

   private static function setup()
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

   public static function send(string $from, string $to, string $subject, string $reply = ':') : bool
   {
      $sentfrom = explode(":",$from); $sentto = explode(":",$to); $reply = explode(":",$reply);
      self::$mail->SetFrom($sentfrom[0], $sentfrom[1] ?? '');
      self::$mail->AddAddress($sentto[0], $sentto[1] ?? '');
      self::$mail->AddReplyTo($reply[0] ?? '', $reply[1] ?? '');
      self::$mail->Subject = $subject;
      self::$mail->Body = self::$body ?? '';
      try {
         if (@self::$mail->Send()) {
            return true;
         } else {
            throw new \Exception(self::$mail->ErrorInfo);
            return false;
         }
      } catch (\Throwable $e) {
         return false;
         trigger_error($e->getMessage());
      }
   }

   public static function sendMultiple(string $from, array $to, string $subject, string $reply = ':')
   {
      $sentfrom = explode(":",$from); $reply = explode(":",$reply);
      foreach ($to as $person) {
         $addressTo = explode(":",$person); 
         self::$mail->AddAddress($addressTo[0], $addressTo[1] ?? '');
      }
      self::$mail->SetFrom($sentfrom[0], $from[1] ?? '');
      self::$mail->AddReplyTo($reply[0] ?? '', $reply[1] ?? '');
      self::$mail->Subject = $subject;
      self::$mail->Body = self::$body ?? '';
      try {
         if (@self::$mail->Send()) {
            return true;
         } else {
            throw new \Exception(self::$mail->ErrorInfo);
            return false;
         }
      } catch (\Throwable $e) {
         trigger_error($e->getMessage());
      }
   }

   public static function withAttachment($path, $name = '', $encoding = 'base64', $type = '', $disposition = 'attachment')
   {
      self::$mail->AddAttachment($path, $name, $encoding, $type, $disposition);
      return new Mail;
   }

}
