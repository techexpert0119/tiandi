<?
// https://github.com/PHPMailer/PHPMailer/
// version 6
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$path = build_path(__DIR__,'phpmailer6/src/PHPMailer.php');
@require_once $path;
@require_once str_replace('PHPMailer.php','Exception.php',$path);
@require_once str_replace('PHPMailer.php','SMTP.php',$path);
@require_once str_replace('PHPMailer.php','OAuth.php',$path);

$return = false ;
global $globvars;
if(! (function_exists('clean_email') && class_exists('PHPMailer\PHPMailer\PHPMailer'))) { return ; }

$em_to = isset($em_to) ? clean_email($em_to) : '';
$em_fr = isset($em_fr) ? clean_email($em_fr) : '';
if(! ($em_to && $em_fr)) { return ; }
$reply = isset($reply) ? clean_email($reply) : '';
$em_cc = isset($em_cc) ? clean_email($em_cc) : '';
$em_bcc = isset($em_bcc) ? clean_email($em_bcc) : '';
if(! isset($em_fn)) { $em_fn = ''; }
if(! isset($subject)) { $subject = ''; }
if(! isset($html)) { $html = ''; }
if(! isset($text)) { $text = ''; }
if(! isset($style)) { $style = ''; }
if(! isset($file)) { $file = ''; }

$globvars['crlf'] = $crlf = (substr_count(isvar($_SERVER['SERVER_SOFTWARE']), 'Microsoft' )) ? "\r\n" : "\n" ;

$mail = new PHPMailer();
$mail->From = $em_fr;
$mail->Sender = $em_fr;
$mail->FromName = $em_fn;
if(is_array($em_to)) { foreach($em_to as $em) { $mail->AddAddress($em); } }
else { $mail->AddAddress($em_to); }
$n = 0 ;
if($em_cc && ! is_array($em_cc)) { $em_cc = array($em_cc); }
if($em_bcc && ! is_array($em_bcc)) { $em_bcc = array($em_bcc); }
if(is_array($em_cc)) {
  foreach($em_cc as $em) { 
    $mail->AddCC($em);
  } 
}
if(is_array($em_bcc)) {
  foreach($em_bcc as $em) { 
    $mail->AddBCC($em);
  } 
}
if($reply) { $mail->AddReplyTo($reply); }
$mail->WordWrap = 70;
$mail->Subject = $subject;
if($html) {
  if(!substr_count($style,'<style')) {
    $style = "<style type=\"text/css\">{$style}</style>";
  }
  $mail->IsHTML(true);
  $mail->Body = "<html>{$crlf}<head>{$crlf}<title>{$subject}</title>{$crlf}{$style}</head>{$crlf}<body>{$crlf}{$html}{$crlf}</body>{$crlf}</html>{$crlf}";
  $mail->AltBody = $text;
}
else {
  $mail->IsHTML(false);
  $mail->Body = $text;
}
if($file) {
  if(is_array($file) && isset($file['tmp_name']) && is_string($file['tmp_name'])) {
    // non utf-8 issue
    $file['tmp_name'] = html_entity_decode($file['tmp_name'], ENT_COMPAT, 'UTF-8'); 
  }
  if(is_array($file) && isset($file['tmp_name']) && is_string($file['tmp_name']) && is_file($file['tmp_name'])) {
     $mail->AddAttachment($file['tmp_name'],((isset($file['name']) && $file['name']) ? html_entity_decode($file['name'], ENT_COMPAT, 'UTF-8') : ''));
  }
  elseif(is_array($file)) {
    foreach($file as $filo) {
      if(is_array($filo) && isset($filo['tmp_name']) && is_string($filo['tmp_name'])) { 
        // non utf-8 issue
        $filo['tmp_name'] = html_entity_decode($filo['tmp_name'], ENT_COMPAT, 'UTF-8');
      }
      if(is_array($filo) && isset($filo['tmp_name']) && is_string($filo['tmp_name']) && is_file($filo['tmp_name'])) {
        $mail->AddAttachment($filo['tmp_name'],((isset($filo['name']) && $filo['name']) ? html_entity_decode($filo['name'], ENT_COMPAT, 'UTF-8') : ''));
      }
      elseif(is_string($filo) && is_file($filo)) {
        $mail->AddAttachment($filo);
      }
    }
  }
  elseif(is_file($file)) {
    $mail->AddAttachment($file);
  }
}

if( file_exists($psmtp = (substr_count($globvars['php_path'],'control/') ? 'smtp.inc.php' : 'control/smtp.inc.php')) || ($globvars['local_dev'] && (getenv('OS') == 'Windows_NT') && file_exists($psmtp = $globvars['local_dev'] . 'smtp.inc.php'))) {
  @include($psmtp);
}
if($mail->Send()) {
  $return = true ;
}
else {
  $return = $mail->ErrorInfo;
}
?>