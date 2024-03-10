<? 
@include_once('functions.inc.php'); 
ob_start();

function head($alt='') {
  global $globvars ;
  print '<!-- php version: ' . phpversion() . " -->\r\n";
  if(isset($globvars['charset']) && substr_count($globvars['charset'], 'utf8')) {
    ?>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <?
  }
  if(file_exists('favicon.ico')) {
    ?>
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
  <link rel="icon" href="favicon.ico" type="image/x-icon">
    <?
  }
  if(file_exists('head1.inc.php')) { @include_once('head1.inc.php'); }
  if(isset($globvars['stack']) && is_array($globvars['stack'])) { $alt .= 'y'; }
  ?>
  <meta http-equiv="X-UA-Compatible" content="<?= ie_edge() ; ?>">
  <script type="text/javascript">window.name='<?= $globvars['php_self']; ?>';</script>
  <? 
  @include_once('../scripts/jquery.inc.php');
  if(file_exists('../scripts/calendar.inc.php')) {
    @include_once('../scripts/calendar.inc.php');
  }
  else {
    @include_once('../scripts/jscalendar.inc.php');
  }
  @include_once('../scripts/chosen.inc.php');
  js_file('control.js');
  if(isarr($globvars['sq_keys'])) {
    $sq_str = safe_implode('',$globvars['sq_keys']) . $alt;
    if(substr_count($sq_str,'f')) { js_file('../scripts/mouselayer.js'); }
    if(substr_count($sq_str,'y')) { js_file('../scripts/ckeditor/ckeditor.js'); }
    if(substr_count($sq_str,'p')) { @include_once('../scripts/wcolpick.inc.php'); }
  }
  cs_file('styles.css');
  if(file_exists('leftmenu.inc.php')) { 
    cs_file('leftmenu.css');
  }
  if(file_exists('styles.css.php')) { 
    cs_file('styles.css.php');
  }
  cs_file('checkradio.css');
}
?>