<?
function getToken($in) {
  $out = array();
  $arr1 = explode( "&", $in ) ;
  foreach($arr1 as $txt) {
    if(substr_count($txt, '=')) {
      $arr2 = explode( "=", $txt ) ;
      $out[$arr2[0]] = $arr2[1] ;
    }
  }
  return $out ;
}

function encryptAes($string, $key) {
  $string = addPKCS5Padding($string);
  $crypt = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $string, MCRYPT_MODE_CBC, $key);
  return "@" . strtoupper(bin2hex($crypt));
}

function decryptAes($strIn, $password) {
  $strInitVector = $password;
  $hex = substr($strIn, 1);
  $strIn = pack('H*', $hex);
  $string = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $password, $strIn, MCRYPT_MODE_CBC, $strInitVector);
  return removePKCS5Padding($string);
}

function addPKCS5Padding($input) {
  $blockSize = 16;
  $padd = "";
  $length = $blockSize - (strlen($input) % $blockSize);
  for ($i = 1; $i <= $length; $i++) {
    $padd .= chr($length);
  }
  return $input . $padd;
}

function removePKCS5Padding($input) {
  $blockSize = 16;
  $padChar = ord($input[strlen($input) - 1]);
  $unpadded = substr($input, 0, (-1) * $padChar);
  return $unpadded;
}

function pstring($descr,$net,$vat,$gross,$quant) {
  if($quant) {
    return(':' . ccolon($descr) . ':' . $quant . ':' . number_format($net,2) . ':' . number_format($vat,2) . ':' . number_format($gross,2) . ':' . number_format($gross*$quant,2));
  }
  else {
    return(':' . ccolon($descr) . ':---:---:---:---:' . number_format($gross,2));
  }
}

function ccolon($in) {
  return str_replace(':',';',cclean(clean_text($in)));
}

function cclean($in) {
  return str_replace('&','+',$in);
}

// New functions for php > 7.1

Define('SESS_CIPHER','AES-128-CBC');

function encryptAesNew($strIn,$strIV) {  
  $strIn = addPKCS5Padding($strIn);
  $strCrypt  = openssl_encrypt($strIn, SESS_CIPHER,$strIV,$options=OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,$strIV);   
  return "@" . strtoupper(bin2hex($strCrypt));
}

function decryptAesNew($strIn,$strIV) {
  $strIn = substr($strIn,1);      
  $strIn = pack('H*', $strIn);    
  return openssl_decrypt($strIn, SESS_CIPHER,$strIV,$options=OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,$strIV); 
}
?>
