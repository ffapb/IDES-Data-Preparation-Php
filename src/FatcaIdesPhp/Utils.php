<?php

namespace FatcaIdesPhp;

class Utils {


  public static function array2shuffledLetters($di,$exceptFields=array()) {
# di: 2D array
# exceptFields: array of strings of field names that should not be shuffled
#
# Example: var_dump(array2shuffledLetters(array(array('bla'=>'bla','bli'=>'bli'))));

    if(!is_array($di)) throw new Exception("Only arrays of arrays supported");
    array_map(function($x) { if(!is_array($x)) throw new Exception("Only arrays of arrays supported"); }, $di);

    return array_map(function($x) use($exceptFields) {
      $k=array_keys($x);
      $k=array_diff($k,$exceptFields);
      foreach($k as $k2) {
        $x[$k2]=str_shuffle($x[$k2]);
      }
      return $x;
    }, $di);
  }

  public static function checkConfig() {
    foreach(array("FatcaKeyPrivate","FatcaXsd","MetadataXsd","ffaid","ffaidReceiver","FatcaCrt") as $x) {
      if(!defined($x)) throw new Exception(sprintf("Missing variable in config: '%s'",$x));
    }

    foreach(array(FatcaXsd,MetadataXsd,FatcaCrt) as $x) {
      if(!file_exists($x)) throw new Exception(sprintf("Missing file defined in config: '%s'",$x));
    }
  }

  public static function libxml_display_error($error) {
      $return = "<br/>\n";
      switch ($error->level) {
          case LIBXML_ERR_WARNING:
              $return .= "<b>Warning $error->code</b>: ";
              break;
          case LIBXML_ERR_ERROR:
              $return .= "<b>Error $error->code</b>: ";
              break;
          case LIBXML_ERR_FATAL:
              $return .= "<b>Fatal Error $error->code</b>: ";
              break;
      }
      $return .= trim($error->message);
      if ($error->file) {
          $return .=    " in <b>$error->file</b>";
      }
      $return .= " on line <b>$error->line</b>\n";

      return $return;
  }

  public static function libxml_display_errors() {
      $errors = libxml_get_errors();
      foreach ($errors as $error) {
          print Utils::libxml_display_error($error);
      }
      libxml_clear_errors();
  }


  // From http://stackoverflow.com/a/13459244
  public static function mail_attachment($files, $mailto, $from_mail, $from_name, $replyto, $subject, $message) {
    if(!is_array($files)) throw new Exception("Please pass an array for files");
    $uid = md5(uniqid(time()));
    
    $header = "From: ".$from_name." <".$from_mail.">".PHP_EOL;
    $header .= "Reply-To: ".$replyto.PHP_EOL;
    $header .= "MIME-Version: 1.0".PHP_EOL;
    $header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"".PHP_EOL.PHP_EOL;
    // split header from message
    // as in http://stackoverflow.com/a/30897790/4126114
    $msg = "This is a multi-part message in MIME format.".PHP_EOL;
    $msg .= "--".$uid.PHP_EOL;
    $msg .= "Content-type:text/html; charset=iso-8859-1".PHP_EOL;
    $msg .= "Content-Transfer-Encoding: 7bit".PHP_EOL.PHP_EOL;
    $msg .= $message.PHP_EOL.PHP_EOL;

    foreach ($files as $filename) { 

        $file = $filename; //$path.$filename;
        //$name = basename($file);
        $file_size = filesize($file);

        $handle = fopen($file, "r");
    if(!$handle) die("Failed to open file $file");
        $content = fread($handle, $file_size);
        fclose($handle);
        $content = chunk_split(base64_encode($content));

        $msg .= "--".$uid.PHP_EOL;
        $msg .= "Content-Type: ".mime_content_type($filename)."; name=\"".basename($filename)."\"".PHP_EOL; // use different content types here, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet
        $msg .= "Content-Transfer-Encoding: base64".PHP_EOL;
        $msg .= "Content-Disposition: attachment; filename=\"".basename($filename)."\"".PHP_EOL.PHP_EOL;
        $msg .= $content.PHP_EOL.PHP_EOL;
    }

    $msg .= "--".$uid."--";
    return mail($mailto, $subject, $msg, $header);
  }

  // http://phpgoogle.blogspot.com/2007/08/four-ways-to-generate-unique-id-by-php.html
  // Generate Guid 
  public static function newGuid() { 
  //return rand(1,9999999);
      $s = strtolower(md5(uniqid(rand(),true))); 
      $guidText = 
          substr($s,0,8) . '-' . 
          substr($s,8,4) . '-' . 
          substr($s,12,4). '-' . 
          substr($s,16,4). '-' . 
          substr($s,20); 
      // the following str_replace might not be necessary, but it was one of the things that I changed when our submission passed
      $guidText=str_replace("-","",$guidText);

      // done
      return $guidText;
  }
  // End Generate Guid 



} // end class
