<? header('Content-Type: text/css');
if(isset($_SERVER['HTTP_USER_AGENT']) && $ua = strtolower($_SERVER['HTTP_USER_AGENT'])) {
if (preg_match('/windows nt|win32/i', $ua)) { 
$fr = strpos($ua, 'windows nt') + 11 ;
$ln = strpos($ua, ';', $fr) - $fr;
$wv = floatval(substr($ua, $fr, $ln));
if($wv >= 10) { // win 10+ ?>
.mbutt, .submit, .submit:hover, .button A:link, .button A:visited, .button A:hover, .buttonr A:link, .buttonr A:visited, .buttonr A:hover, .button1 A:link, .button1 A:visited, .button1 A:hover, .buttont A:link, .buttont A:visited, .buttont A:hover {
  padding:4px 6px 7px 6px;
}
<? } else { // win ?>
.mbutt, .submit, .submit:hover, .button A:link, .button A:visited, .button A:hover, .buttonr A:link, .buttonr A:visited, .buttonr A:hover, .button1 A:link, .button1 A:visited, .button1 A:hover, .buttont A:link, .buttont A:visited, .buttont A:hover {
  padding:6px 6px 5px 6px;
}
<? } } elseif (preg_match('/applewebkit|macintosh|mac os|iphone|ipad/i', $ua)) { // apple ?>
.mbutt, .submit, .submit:hover, .button A:link, .button A:visited, .button A:hover, .buttonr A:link, .buttonr A:visited, .buttonr A:hover, .button1 A:link, .button1 A:visited, .button1 A:hover, .buttont A:link, .buttont A:visited, .buttont A:hover {
  padding:6px 6px 5px 6px; 
}
<? } } ?>