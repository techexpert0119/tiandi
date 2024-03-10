<?
// Copyright Wotnot Web Works Ltd.
if ((float)phpversion() >= 7.4 && file_exists($vendor = build_path(__DIR__, '../scripts/vendor/autoload.php'))) {
  include_once($vendor);
}
$globvars['local_dev'] = 0;
if (isset($_SERVER['PATH_INFO']) && $len = 0 - strlen($_SERVER['PATH_INFO'])) {
  header('location:' . substr($_SERVER['PHP_SELF'], 0, $len) . (isvar($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : ''));
}
if (substr_count(($sf = htmlspecialchars(isvar($_SERVER['SCRIPT_FILENAME'], ''))), '\\Sites\\')) {
  $globvars['local_dev'] = str_replace('\\', '/', substr($sf, 2, strpos($sf, '\\Sites\\') + 5));
}
if (substr_count($sf, '/control/') || substr_count($sf, '\\control\\')) {
  nocache();
}

$globvars['http_host']        =  htmlspecialchars(isvar($_SERVER['HTTP_HOST'], ''));
$globvars['server_name']      =  htmlspecialchars(isvar($_SERVER['SERVER_NAME'], ''));
$globvars['remote_addr']      =  htmlspecialchars(isvar($_SERVER['REMOTE_ADDR'], ''));
$globvars['query_string']     =  htmlspecialchars(isvar($_SERVER['QUERY_STRING'], ''));
$globvars['request_uri']      =  htmlspecialchars(isvar($_SERVER['REQUEST_URI'], ''));
$globvars['http_user_agent']  =  htmlspecialchars(isvar($_SERVER['HTTP_USER_AGENT'], ''));
$globvars['http_referer']     =  htmlspecialchars(isvar($_SERVER['HTTP_REFERER'], ''));
$globvars['php_self']         =  htmlspecialchars(substr($ps1 = substr($ps = isvar($_SERVER['PHP_SELF']), 0, (substr_count($ps, '.php') ? strpos($ps, '.php') + 4 : strlen($ps))), strrpos($ps1, '/') + 1));
if (isset($globvars['php_file'])) {
  $globvars['php_self'] = $globvars['php_file']; // set from pages.php
} else {
  $globvars['php_file'] = $globvars['php_self'];
}
$globvars['https']            = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443) ? true : false;
$globvars['request_url']      =  'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$globvars['http_host']}{$globvars['request_uri']}";
$globvars['php_path']         =  str_replace($globvars['php_self'], '', $ps1);
$globvars['php_serv']         =  $globvars['php_self'] == 'index.php' && !$globvars['local_dev'] ? $globvars['php_path'] : $globvars['php_self'];
$globvars['php_page']         =  str_replace('.php', '', $globvars['php_self']);
$globvars['argv']             =  isset($_SERVER['argv']) ? $_SERVER['argv'] : '';
$globvars['time_stamp']       =  time();
$globvars['browser_info']     =  browser_info();
$globvars['temp_folder']      = '_temp';

if ((!defined("MYSQL_ASSOC")) && defined("MYSQLI_ASSOC")) {
  define("MYSQL_ASSOC", MYSQLI_ASSOC);
  define("MYSQL_NUM", MYSQLI_NUM);
  define("MYSQL_BOTH", MYSQLI_BOTH);
}

extract($globvars);
@include_once('settings.inc.php');

if (!function_exists('str_ireplace')) {
  function str_ireplace($search, $replace, $subject)
  {
    return str_replace($search, $replace, $subject);
  }
}

if (!isset($globvars['file_msize'])) {
  $globvars['file_msize']  = 20 * 1024 * 1024;
}
if (!isset($globvars['file_mmake'])) {
  $globvars['file_mmake']  = 8 * 1024 * 1024;
}
if (!isset($globvars['doc_types'])) {
  $globvars['doc_types']   = array('txt', 'doc', 'docx', 'pdf');
}
if (!isset($globvars['image_types'])) {
  $globvars['image_types'] = array('jpg', 'jpeg', 'gif', 'bmp', 'png', 'tif', 'webp');
}
if (!isset($globvars['video_types'])) {
  $globvars['video_types'] = array('mp4', 'webm');
}
if (!isset($globvars['other_types'])) {
  $globvars['other_types'] = array('zip', 'swf', 'dwg', 'eps');
}
if (!isset($globvars['file_types'])) {
  $globvars['file_types']  =
    array_merge($globvars['doc_types'], $globvars['image_types'], $globvars['video_types'], $globvars['other_types']);
}

// ----------------------------------------------------HEAD-------------------------------------------------------------

function meta_tags($title = '', $desc = '', $keyw = '', $image = '', $keys = '', $url = '', $tags = '')
{
  // $keys is array of allowed keys for og:url querystring method (blank for any)
  global $globvars;
  if ($globvars['php_self'] && ($globvars['php_self'] != '404.php')) {
    if ($url) {
      // set specifically
      $og_url = $url;
    } elseif ($keys) {
      // from server variables
      $og_url = $globvars['sm_url'] . str_replace('index.php', '', $globvars['php_self']) . ($globvars['query_string'] ? clean_qstring($globvars['query_string'], $keys) : '');
    } else {
      // if $keys is not set
      $og_url = $globvars['request_url'];
    }
    $og_img = $image ? $image : (isset($globvars['comp_logo']) ? $globvars['comp_logo'] : '');
    if ($desc) { ?>
      <meta id="mt_descr" name="description" content="<?= $desc; ?>">
    <? }
    if ($keyw) { ?>
      <meta id="mt_keyw" name="keywords" content="<?= $keyw; ?>">
    <? }
    if ($title) { ?>
      <meta id="og_type" property="og:type" content="article">
      <meta id="og_title" property="og:title" content="<?= $title; ?>">
      <? if ($desc) { ?>
        <meta id="og_descr" property="og:description" content="<?= $desc; ?>">
      <? }
      if ($og_img) { ?>
        <meta id="og_image" property="og:image" content="<?= $og_img; ?>">
      <? }
      if ($og_url) { ?>
        <meta id="og_url" property="og:url" content="<?= $og_url; ?>">
      <? } ?>
      <meta id="tw_card" name="twitter:card" content="summary">
      <meta id="tw_title" name="twitter:title" content="<?= $title; ?>">
      <? if ($desc) { ?>
        <meta id="tw_descr" name="twitter:description" content="<?= $desc; ?>">
      <? }
      if ($og_img) { ?>
        <meta id="tw_image" name="twitter:image" content="<?= $og_img; ?>">
  <? }
    }
  }
  if ($tags) {
    print "\t" . $tags . "\r\n";
  }
}

function schema_org($url, $logo, $tel, $ctype = 'customer service')
{
  ?>
  <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Organization",
      "url": "<?= $url ?>",
      "logo": "<?= $logo ?>",
      "contactPoint": [{
        "@type": "ContactPoint",
        "telephone": "<?= $tel ?>",
        "contactType": "<?= $ctype ?>",
        "availableLanguage": "English"
      }]
    }
  </script>
  <?
}

// ----------------------------------------------------GLOBVARS-------------------------------------------------------------

function globvars()
{
  // globvars('name1','name2');
  // global $globvars; extract($globvars,EXTR_SKIP);
  global $globvars, $globvarr, $globvchk;
  $globvchk = array();
  for ($i = 0; $i < func_num_args(); $i++) {
    $k = func_get_arg($i);
    $g_arr = $g_chk = $g_str = false;
    if (substr_count($k, '(array)')) {
      $k = safe_trim(str_replace('(array)', '', $k));
      $g_arr = true; // expect array
    } elseif (substr_count($k, '(string)')) {
      $k = safe_trim(str_replace('(string)', '', $k));
      $g_str = true; // expect string
    } elseif (substr_count($k, '(checkbox)')) {
      $g_chk = true; // is a checkbox
      $k = safe_trim(str_replace('(checkbox)', '', $k));
      if (isset($_POST) && !isset($_POST[$k])) {
        $_POST[$k] = ''; // blank checkbox on post
      }
      if (isset($_GET) && !isset($_GET[$k])) {
        $_GET[$k] = ''; // blank checkbox on get
      }
    }
    $k = safe_trim($k);
    $v = '';
    if (isset($_POST[$k])) {
      $v = $_POST[$k];
    } elseif (isset($_GET[$k])) {
      $v = $_GET[$k];
    } elseif (isset($_SESSION[$k])) {
      $v = $_SESSION[$k];
    } elseif (isset($_COOKIE[$k])) {
      $v = $_COOKIE[$k];
    } elseif (isset($_FILES[$k]) && is_array($_FILES[$k])) {
      if (is_array($_FILES[$k]['name'])) {
        // multiple file[] upload
        $v = [];
        foreach ($_FILES[$k] as $fvn => $fva) {
          foreach ($fva as $fvk => $fvv) {
            $v[$fvk][$fvn] = $fvv;
            if ($fvn == 'name') {
              if ((getenv('OS') == 'Windows_NT') && isset($v[$fvk]['tmp_name'])) {
                $v[$fvk]['tmp_name'] = str_replace('\\', '/', $v[$fvk]['tmp_name']);
              }
              $v[$fvk]['fname'] = "{$k}[{$fvk}]";
              $v[$fvk]['ext'] = strtolower(pathinfo($fvv, PATHINFO_EXTENSION));
            }
          }
        }
      } else {
        // single file upload
        $v = $_FILES[$k];
        if ((getenv('OS') == 'Windows_NT') && isset($v['tmp_name'])) {
          $v['tmp_name'] = str_replace('\\', '/', $v['tmp_name']);
        }
        $v['fname'] = $k;
        $v['ext'] = strtolower(pathinfo($v['name'], PATHINFO_EXTENSION));
      }
    }
    if (!is_array($v)) {
      $v = idecode(clean_gcodes($v, 1));
      /*
      $v = urldecode(clean_gcodes($v,1));
      if( (substr($v,0,2) == '[[') && (substr($v,-2) == ']]') ) {
        if(! is_array($v = substr($v,2,-2))) {
          $v = json_decode(base64_decode($v),true);
        }
      }
      */
    }
    $r = $v;
    if (is_array($v)) {
      if (!$g_str) {
        array_walk_recursive($v, 'walk_vars');
        $globvars[$k] = (array) $v;
        array_walk_recursive($r, 'walk_varr');
        $globvarr[$k] = (array) $r;
      } else {
        // array not allowed by (string)
        $globvars[$k] = ''; // blank variable
        $globvarr[$k] = (array) clean_arr($r); // array of original values
      }
    } else {
      if ($v || ($v && is_numeric(safe_trim($v)))) {
        $globvars[$k] = item_vars($v, $k); // variable value
      } elseif ($g_arr) {
        $globvars[$k] = array(); // force blank array
      } else {
        $globvars[$k] = ''; // blank variable
      }
      $globvarr[$k] = (array) clean_arr($r); // array of original values
    }
    if ($g_arr && $g_chk && isset($globvars[$k]) && is_array($globvars[$k])) {
      foreach ($globvchk as $x => $y) {
        if (!isset($globvars[$k][$x])) {
          // make all possible checkbox options from earlier arrays blank
          $globvars[$k][$x] = '';
        }
      }
    }
  }
}

function walk_vars(&$v, $a)
{
  global $globvchk;
  $globvchk[$a] = $a;
  $v = item_vars($v, $a);
}

function walk_varr(&$v, $a)
{
  $v = clean_arr($a);
}

function item_vars($v, $k)
{
  global $globvars;
  if (!is_array($v)) {
    $v = clean_gcodes($v, 1);
    if ($d = idecode($v)) {
      $v = $d;
    } // in case [[]] isn't really encoded
    /*
    $vd = urldecode(clean_gcodes($v,1));
    if( (substr($vd,0,2) == '[[') && (substr($vd,-2) == ']]') ) {
      if(! is_array($vd = substr($vd,2,-2))) {
        $v = json_decode(base64_decode($vd),true);
      }
    }
    */
  }
  if (substr_count($k, 'html') || substr_count($k, 'sql')) {
    $v = str_ireplace("'", '&#39;', clean_gcodes($v, 2));
    if (!(isset($globvars['charset']) && substr_count($globvars['charset'], 'utf8'))) {
      $v = clean_bin($v);
    }
  } else {
    $v = clean_gcodes(clean_glob($v), 2);
  }
  return $v;
}

function get_query()
{
  global $globvars;
  extract($globvars, EXTR_SKIP);
  $strs = array();
  if (isvar($query_string)) {
    $strs[] = str_replace('&amp;', '&', $query_string);
  }
  $arr = parse_url($request_uri);
  if (isvar($arr['query'])) {
    $query_string = str_replace('&amp;', '&', $arr['query']);
    if (!in_array($query_string, $strs)) {
      $strs[] = $query_string;
    }
  }
  foreach ($strs as $str) {
    if (substr_count($str, '=')) {
      parse_str($str, $vars);
      foreach ($vars as $var => $val) {
        if (!isvar($globvars[$var])) {
          $globvars[$var] = $_GET[$var] = $val;
        }
      }
    } elseif (!isvar($globvars['query_string'])) {
      $globvars['query_string'] = $str;
    }
  }
}

function globvadd()
{
  global $globvars;
  if (func_num_args() == 0) {
    return false;
  } else {
    for ($i = 0; $i < func_num_args(); $i++) {
      $key = func_get_arg($i);
      $val = func_get_arg(++$i);
      if ($key) {
        $globvars[$key] = $val;
      }
    }
  }
}

function globval($in)
{
  global $globvars;
  // globval('field') or globval('field[arr1]')
  $out = null;
  if ($in) {
    if (substr_count($in, '[')) {
      $arr1 = substr($in, 0, strpos($in, '['));
      $arr2 = substr(substr($in, strpos($in, '[') + 1), 0, -1);
      if (isset($globvars[$arr1][$arr2])) {
        $out = $globvars[$arr1][$arr2];
      }
    } elseif (isset($globvars[$in])) {
      $out = $globvars[$in];
    }
  }
  return $out;
}

function isvar(&$in, $def = null)
{
  // isvar($field) returns value if set and exists or 2nd param (default blank) if not
  // eg. if($v=isvar($v)) { echo $v; } // if $v exists and set print it
  // eg. $s = isvar($s,'') // sets $s to ''
  // eg. $n = isvar($n,0) // sets $n to 0
  return (isset($in) && $in) ? $in : $def;
}

function isarr(&$in, $def = null)
{
  // isvar plus checks if array and returns
  // CAUTION if $in is an array part this creates the missing part set to NULL which will affect count of the array
  return (isset($in) && is_array($in) && $in) ? $in : $def;
}

function isnum(&$in, $def = null)
{
  // isvar plus checks if positive number and returns cleaned
  return (isset($in) && is_numeric($in) && ($in = (int)$in) && ($in > 0)) ? $in : $def;
}

function isfile($in, $ftype = '')
{
  // ftype: image/doc/file
  global $globvars;
  extract($globvars, EXTR_SKIP);
  $parts = safe_explode(".", strtolower($in));
  $out = array();
  // print_arr($parts,'parts');
  if (is_array($parts) && count($parts)) {
    $ext = end($parts);
    if (isset($image_types) && in_array($ext, $image_types)) {
      $out = array('ftype' => 'image', 'ext' => $ext);
    } elseif (isset($doc_types) && in_array($ext, $doc_types)) {
      $out = array('ftype' => 'doc', 'ext' => $ext);
    } elseif (isset($file_types) && in_array($ext, $file_types)) {
      $out = array('ftype' => 'file', 'ext' => $ext);
    }
  }
  // print_arr($out,'file');
  return (count($out) && ((!$ftype) || $ftype == $out['ftype'])) ? $out : false;
}

function isimg($in)
{
  return isfile($in, 'image');
}

function istype($path = '', $file = '', $type = 'file')
{
  $path = safe_trim($path);
  if ($type == 'file') {
    $fpath = build_path($path, $file = safe_trim($file), 'lastfile');
    if ($file && file_exists($fpath) && is_file($fpath)) {
      return true;
    }
  } elseif ($type == 'dir') {
    if ($path && is_dir($path)) {
      return true;
    }
  }
  return false;
}

// ----------------------------------------------------MYSQL----------------------------------------------------------------

function opendb($database = '', $username = '', $password = '', $hostname = '', $debug = 0)
{
  global $globvars;
  extract($globvars, EXTR_SKIP);
  if (isset($db_database) && !$database) {
    $database = $db_database;
  }
  if (isset($db_username) && !$username) {
    $username = $db_username;
  }
  if (isset($db_password) && !$password) {
    $password = $db_password;
  }
  if (isset($db_hostname) && !$hostname) {
    $hostname = $db_hostname;
  }
  if ((!(isset($db_open) && $db_open)) || !substr_count($db_open, $database)) {
    if (function_exists('mysqli_connect') && !(isset($db_mysqliOff) && $db_mysqliOff)) {
      $globvars['db_mysqli'] = @mysqli_connect($hostname, $username, $password, $database) or die("Error: MySQLi connection failure");
      $globvars['db_open'] = "mysqli: {$database}";
      if (isset($globvars['charset']) && substr_count($globvars['charset'], 'utf8')) {
        mysqli_set_charset($globvars['db_mysqli'], 'utf8');
      }
      mysqli_report(MYSQLI_REPORT_ERROR);
    } elseif (function_exists('mysql_connect')) {
      @mysql_connect($hostname, $username, $password) or die("Error: MySQL connection failure");
      @mysql_select_db($database) or die("Error: Failed to open database");
      $globvars['db_mysqli'] = false;
      $globvars['db_open'] = "mysql: {$database}";
      if (isset($globvars['charset']) && substr_count($globvars['charset'], 'utf8')) {
        mysql_set_charset('utf8');
      }
    } else {
      print 'Unable to open database';
      die;
    }
  }
  if ($debug == 2) {
    print_d($globvars['db_open'], __LINE__, __FILE__);
  } elseif ($debug) {
    logwrite($globvars['db_open']);
  }
}

function checkdb($table, $show)
{
  global $globvars;
  extract($globvars, EXTR_SKIP);
  $string = "SHOW tables";
  if ($table) {
    $string .= " WHERE `Tables_in_{$db_database}` = '$table'";
  }
  $op = "Checking mySQL<br><br>\r\n";
  $query = my_query($string);
  while ($t_row = my_array($query)) {
    $string1 = "CHECK TABLE `{$t_row[0]}`";
    $query1 = my_query($string1);
    if (my_rows($query1)) {
      $v_row = my_array($query1);
      $op .= $t_row[0] . ' = ' .  $v_row['Msg_text'];
      if ($v_row['Msg_text'] != 'OK') {
        $op .= ' - Repairing - ';
        $string2 = "REPAIR TABLE `{$t_row[0]}`";
        $query2 = my_query($string2);
        if (my_rows($query1)) {
          $r_row = my_array($query2);
          $op .= $r_row['Msg_text'];
        }
      }
      $op .= "<br>\r\n";
    }
  }
  my_free($query);
  if ($show) {
    echo $op;
  }
}

function checktable($table)
{
  $n = my_rows($query = my_query("SHOW TABLES LIKE '" . $table . "'"));
  my_free($query);
  return $n ? true : false;
}

function my_tables($exclude = '')
{
  global $globvars;
  if (!$exclude) {
    $exclude = array();
  } elseif (!is_array($exclude)) {
    $exclude = safe_explode(',', $exclude);
  }
  $out = array();
  $string = "SHOW tables from `{$globvars['db_database']}`";
  $query = my_query($string);
  if (my_rows($query)) {
    while ($table = my_array($query)) {
      if (!in_array($table[0], $exclude)) {
        $out[] = $table[0];
      }
    }
  }
  return $out;
}

function my_table($query)
{
  global $globvars;
  if ($query) {
    if ($globvars['db_mysqli']) {
      $fetch = mysqli_fetch_field_direct($query, 0);
    } else {
      $fetch = mysql_fetch_field($query, 0);
    }
    my_seek($query, 0);
    return $fetch->table;
  }
  return '';
}

function my_fields($table, $type = null)
{
  // type 'assoc' for table.field or (not in '') MYSQLI_ASSOC, MYSQLI_NUM, MYSQLI_BOTH (default)
  // returns array of fields in table
  global $globvars;
  $fields = array();
  $query = my_query("SHOW fields FROM `{$table}`");
  if (!my_rows($query)) {
    return false;
  }
  while ($c_row = my_array($query, $type)) {
    $fields[$c_row['Field']] = $c_row;
    $fields[$c_row['Field']]['Ftype'] = $c_row['Type'];
    $fields[$c_row['Field']]['Fprts'] = '';
    $fields[$c_row['Field']]['Fprms'] = '';
    if ($c_row['Type'] == 'datetime') {
      $fields[$c_row['Field']]['Fprms'] = 19;
    } elseif ($c_row['Type'] == 'date') {
      $fields[$c_row['Field']]['Fprms'] = 10;
    } elseif ($c_row['Type'] == 'time') {
      $fields[$c_row['Field']]['Fprms'] = 8;
    } elseif (substr_count($c_row['Type'], '(')) {
      $bps = strpos($c_row['Type'], '(');
      $bln = strpos($c_row['Type'], ')') - $bps;
      $fields[$c_row['Field']]['Ftype'] = substr($c_row['Type'], 0, $bps);
      $fsize = substr($c_row['Type'], $bps + 1, $bln - 1);
      $fields[$c_row['Field']]['Fprts'] = $fsize;
      if ($fields[$c_row['Field']]['Ftype'] == 'decimal' && substr_count($fsize, ',')) {
        $fsize = strtok($fsize, ',') + 1;
      }
      $fields[$c_row['Field']]['Fprms'] = $fsize;
    }
  }
  // print_arr($fields);
  my_free($query);
  return $fields;
}

function my_num_fields($query)
{
  return my_numfields($query);
}

function my_numfields($query)
{
  global $globvars;
  return $globvars['db_mysqli'] ? mysqli_num_fields($query) : mysql_num_fields($query);
}

function my_fetch_field($query, $n)
{
  return my_fetch($query, $n);
}

function my_fetch($query, $n)
{
  global $globvars;
  return $globvars['db_mysqli'] ? mysqli_fetch_field_direct($query, $n) : mysql_fetch_field($query, $n);
}

function my_insert($table, $string1, $string2)
{
  // string1 eg. `key` = '$id' (ie. primary key)
  // string2 eg. `name` = '$name', `address` = '$address' etc.
  if ($string2) {
    $string1 = "{$string1}, {$string2}";
  }
  $string = "INSERT INTO `{$table}` SET {$string1} ON DUPLICATE KEY UPDATE {$string2}";
  $query = my_query($string);
  my_free($query);
}

function my_query($string, $debug = 0)
{
  global $globvars;
  if (isset($globvars['db_open']) && $globvars['db_open']) {
    $res = $globvars['db_mysqli'] ? mysqli_query($globvars['db_mysqli'], $string) : mysql_query($string);
    if ($debug == 2) {
      print_d($string, __LINE__, __FILE__);
      print_arr($res, 'my_query');
    } elseif ($debug) {
      logwrite($string);
    }
    return $res;
  } else {
    print 'ERROR: Database not open';
    die;
  }
}

function my_real_escape_string($string)
{
  return my_escape_string($string);
}

function my_escape_string($string)
{
  global $globvars;
  if (isset($globvars['db_open']) && $globvars['db_open']) {
    $res = $globvars['db_mysqli'] ? mysqli_real_escape_string($globvars['db_mysqli'], $string) : mysql_real_escape_string($string);
    return $res;
  } else {
    print 'ERROR: Database not open';
    die;
  }
}

function my_isquery($query)
{
  global $globvars;
  if ($query !== false) {
    if ($globvars['db_mysqli']) {
      if (is_object($query) && get_class($query) == 'mysqli_result') {
        return true;
      }
    } elseif (is_resource($query)) {
      return true;
    }
  }
  return false;
}

function my_num_rows($query)
{
  return my_rows($query);
}

function my_rows($query)
{
  global $globvars;
  if (my_isquery($query)) {
    return $globvars['db_mysqli'] ? mysqli_num_rows($query) : mysql_num_rows($query);
  }
  return 0;
}

function my_row($query)
{
  global $globvars;
  return $globvars['db_mysqli'] ? mysqli_fetch_row($query) : mysql_fetch_row($query);
}

function my_data_seek(&$query, $start)
{
  return my_seek($query, $start);
}

function my_seek(&$query, $start)
{
  global $globvars;
  $go = 0;
  if ($max = my_rows($query)) {
    $max--;
    if (is_numeric($max) && ($max >= 0)) {
      $go = is_numeric($start) && ($start >= 0) && ($start <= $max) ? $start : 0;
      $globvars['db_mysqli'] ? mysqli_data_seek($query, $go) : mysql_data_seek($query, $go);
    }
  }
  return $go;
}

function my_next(&$query, $start, $disp)
{
  // if( ($next = my_next($query,$start,$disp)) >= 0 ) { ;}
  global $globvars;
  $start = $start + $disp;
  $max = my_rows($query) - 1;
  if ($start > $max) {
    return -1;
  }
  return $start;
}

function my_prev(&$query, $start, $disp)
{
  // if( $prev = my_prev($query,$start,$disp) >= 0 ) { ;}
  $start = $start - $disp;
  if ($start < 0) {
    return -1;
  }
  return $start;
}

function my_break(&$query, $disp)
{
  // if( my_break($query,$disp) ){ break; }
  global $globvars;
  $globvars['my_count'] = isset($globvars['my_count']) ? ++$globvars['my_count'] : 1;
  if ($globvars['my_count'] >= $disp) {
    return true;
  }
  return false;
}

function my_fetch_assoc($query)
{
  return my_array($query, MYSQL_ASSOC);
}

function my_assoc($query)
{
  return my_array($query, MYSQL_ASSOC);
}

function my_assocf($query)
{
  return my_array($query, 'assoc');
}

function my_fetch_array($query, $type = null)
{
  return my_array($query, $type);
}

function my_array($query, $type = null)
{
  // type 'assoc' for table.field or (not in '') MYSQL_ASSOC, MYSQL_NUM, MYSQL_BOTH (default)
  global $globvars;
  $out = array();
  if (my_isquery($query)) {
    if ($type == 'assoc') {
      if ($globvars['db_mysqli']) {
        if ($row = mysqli_fetch_array($query)) {
          $f = mysqli_num_fields($query);
          for ($n = 0; $n < $f; $n++) {
            $fetch = mysqli_fetch_field_direct($query, $n);
            $table = $fetch->table;
            $field = $fetch->name;
            $out["$table.$field"] = $row[$n];
          }
        }
      } else {
        if ($row = mysql_fetch_array($query)) {
          $f = mysql_num_fields($query);
          for ($n = 0; $n < $f; $n++) {
            $table = mysql_field_table($query, $n);
            $field = mysql_field_name($query, $n);
            $out["$table.$field"] = $row[$n];
          }
        }
      }
    } else {
      if ($type == MYSQL_ASSOC) {
        $out = $globvars['db_mysqli'] ? mysqli_fetch_array($query, MYSQLI_ASSOC) : mysql_fetch_array($query, MYSQL_ASSOC);
      } elseif ($type == MYSQL_NUM) {
        $out = $globvars['db_mysqli'] ? mysqli_fetch_array($query, MYSQLI_NUM) : mysql_fetch_array($query, MYSQL_NUM);
      } else {
        $out = $globvars['db_mysqli'] ? mysqli_fetch_array($query) : mysql_fetch_array($query);
      }
    }
  }
  return $out;
}

function my_result($query, $row, $field, $type = null)
{
  // type MYSQL_ASSOC, MYSQL_NUM, MYSQL_BOTH (default)
  global $globvars;
  if ($globvars['db_mysqli']) {
    mysqli_data_seek($query, $row);
    if (($type == MYSQL_ASSOC) || ((!$type) && (!is_numeric($field)))) {
      $arr = mysqli_fetch_array($query, MYSQLI_ASSOC);
    } elseif (($type == MYSQL_NUM) || ((!$type) && (is_numeric($field)))) {
      $arr = mysqli_fetch_array($query, MYSQLI_NUM);
    } else {
      $arr = mysqli_fetch_array($query);
    }
    $res = $arr[$field];
  } else {
    $res = mysql_result($query, $row, $field);
  }
  return $res;
}

function my_build($string, $maintable, $mainkey, $multi = '', $debug = 0)
{
  // make associative array from sql string (mysqli only)
  // multi is csv or array of join table where multi array required
  global $globvars;
  $out = array();
  if ($multi && !is_array($multi)) {
    $multi = safe_explode(",", $multi);
  }
  $query = my_query($string, $debug);
  if ($globvars['db_mysqli'] && my_isquery($query) && $rows = my_rows($query)) {
    $r = 0;
    while ($r < $rows) {
      if ($row = mysqli_fetch_array($query)) {
        $f = mysqli_num_fields($query);
        for ($n = 0; $n < $f; $n++) {
          $fetch = mysqli_fetch_field_direct($query, $n);
          $thistable = $fetch->table;
          $thisfield = $fetch->name;
          if ($maintable == $thistable && $mainkey == $thisfield) {
            $thiskey = $row[$n];
            break;
          }
        }
        if ($thiskey) {
          $prevtable = '';
          for ($n = 0; $n < $f; $n++) {
            $fetch = mysqli_fetch_field_direct($query, $n);
            $thistable = $fetch->table;
            $thisfield = $fetch->name;
            if (is_array($multi) && in_array($thistable, $multi)) {
              if ((!isset($count[$thiskey][$thistable]))) {
                $count[$thiskey][$thistable] = 0;
              } elseif ($thistable != $prevtable) {
                $count[$thiskey][$thistable]++;
              }
              $prevtable = $thistable;
              $out[$thiskey][$thistable][$count[$thiskey][$thistable]][$thisfield] = $row[$n];
            } else {
              $out[$thiskey][$thistable][$thisfield] = $row[$n];
            }
          }
        }
      }
      $r++;
    }
  }
  return $out;
}

function my_insert_id()
{
  return my_id();
}

function my_id()
{
  global $globvars;
  if ($globvars['db_mysqli']) {
    return mysqli_insert_id($globvars['db_mysqli']);
  } else {
    return mysql_insert_id();
  }
  return false;
}

function my_affected_rows()
{
  return my_affected();
}

function my_affected()
{
  global $globvars;
  if ($globvars['db_mysqli']) {
    return mysqli_affected_rows($globvars['db_mysqli']);
  } else {
    return mysql_affected_rows();
  }
  return false;
}

function my_error()
{
  global $globvars;
  if ($globvars['db_mysqli']) {
    return mysqli_error($globvars['db_mysqli']);
  } else {
    return mysql_error();
  }
  return false;
}

function my_free_result()
{
  my_free();
}

function my_free()
{
  // my_free('res1','res2');
  global $globvars;
  if ($n = func_num_args()) {
    for ($i = 0; $i < $n; $i++) {
      $res = func_get_arg($i);
      if (is_string($res)) {
        global $$res;
        $res = $$res;
      }
      if ($res && my_isquery($res)) {
        if ($globvars['db_mysqli']) {
          mysqli_free_result($res);
        } else {
          mysql_free_result($res);
        }
      }
    }
  }
}

function my_sel($query, $fname, $prev, $oval, $odisp, $onsel = '', $style = '')
{
  // selector from query or string
  if (is_string($query)) {
    $query = my_query($query);
  }
  if (my_rows($query) && $fname && $oval) {
    my_seek($query, 0);
  ?>
    <select name="<?= $fname; ?>" onchange="<?= $onsel; ?>" style="<?= $style; ?>">
      <option value="">***</option>
      <?
      while ($a_row = my_array($query)) {

        // Build value
        $tok = strtok($oval, '|');
        $ovalv = '';
        while ($tok !== false) {
          $ovalv .= $a_row[$tok] . '|';
          $tok = strtok('|');
        }
        $ovalv = substr($ovalv, 0, -1);

        // Build display
        $tok = strtok($odisp, '|');
        $odispv = '';
        while ($tok !== false) {
          $odispv .= $a_row[$tok] . ' : ';
          $tok = strtok('|');
        }
        $odispv = substr($odispv, 0, -3);

        if ($prev == $ovalv) {
      ?>
          <option value="<?= $ovalv; ?>" selected="selected"><?= clean_amp($odispv); ?></option>
        <?
        } else {
        ?>
          <option value="<?= $ovalv; ?>"><?= clean_amp($odispv); ?></option>
      <?
        }
      }
      ?>
    </select>
  <?
  }
  my_free($query);
}

function my_while($query, $key = '', $var = '', $type = null)
{
  // create array from $string or db query
  // key is db field key
  // var is one field or blank for array
  // type 'assoc' for table.field or (not in '') MYSQL_ASSOC, MYSQL_NUM, MYSQL_BOTH (default)
  if (is_string($query)) {
    $query = my_query($query);
  }
  $rows = array();
  if ($num = my_rows($query)) {
    my_seek($query, 0);
    while ($row = my_array($query, $type)) {
      $v = $var && isset($row[$var]) ? $row[$var] : $row;
      if ($key && isset($row[$key])) {
        $rows[$row[$key]] = $v;
      } else {
        $rows[] = $v;
      }
    }
  }
  my_free($query);
  return $rows;
}

function my_json($query, $fields = '', $return = 'array', $type = null)
{
  // create array from db query or string (clean up for json)
  // fields is array of specific fields or blank for all
  // return is 'array' to loop or 'single' for one result
  // type 'assoc' for table.field or (not in '') MYSQL_ASSOC, MYSQL_NUM, MYSQL_BOTH (default)
  $rows = array();
  if (is_string($query)) {
    $query = my_query($query);
  }
  if ($num = my_rows($query)) {
    my_seek($query, 0);
    $fc = my_numfields($query);
    while ($row = my_array($query, $type)) {
      for ($i = 0; $i < $fc; $i++) {
        $fetch = my_fetch($query, $i);
        $fname = $fetch->name;
        if ($type == 'assoc') {
          $fname = "{$fetch->table}.{$fetch->name}";
        } elseif ($type == MYSQL_NUM) {
          $fname = $i;
        }
        if ((!is_array($fields)) || in_array($fname, $fields)) {
          $ftype = $fetch->type;
          $row[$fname] = mb_convert_encoding($row[$fname], 'UTF-8', 'ISO-8859-1');
          if (in_array($ftype, array('real', 'float', 'double', 4, 5, 246))) {
            $row[$fname] = doubleval($row[$fname]);
          }
          if (in_array($ftype, array('tinyint', 'smallint', 'int', 1, 2, 3, 8, 9))) {
            $row[$fname] = intval($row[$fname]);
          }
        } elseif (isset($row[$fname])) {
          unset($row[$fname]);
        }
      }
      if ($return == 'array') {
        $rows[] = $row;
      } else {
        $rows = $row;
        break;
      }
      $i++;
    }
  }
  my_free($query);
  return $rows;
}

function my_ticks($field)
{
  if (!substr_count($field, "`")) {
    $field = "`" . str_replace('.', "`.`", $field) . "`";
  }
  return $field;
}

function my_csv($field, $search, $sep = ',')
{
  // where search is in text field of csv eg. 1,search,3,4
  // return " (`$field` = '$search' OR `$field` LIKE '{$search}{$sep}%' OR `$field` LIKE '%{$sep}{$search}' OR `$field` LIKE '%{$sep}{$search}{$sep}%') ";
  // return " (`$field` = '$search' OR `$field` REGEXP '^{$search}{$sep}' OR `$field` REGEXP '{$sep}{$search}$' OR `$field` REGEXP '.*{$sep}{$search}{$sep}.*') ";
  $field = my_ticks($field);
  return " CONCAT('{$sep}',{$field},'{$sep}') LIKE '%{$sep}{$search}{$sep}%' ";
}

function my_arr($field, $search, $sep = ',', $ord = false)
{
  // where field matches any item in search (csv string or array)
  // set $ord = true to order results by array
  $field = my_ticks($field);
  if (is_array($search)) {
    $search = safe_implode($sep, $search);
  }
  $search = "'" . str_replace($sep, "'{$sep}'", $search) . "'";
  $out = " {$field} = {$search} ";
  if (substr_count($search, $sep)) {
    $out = " {$field} IN ({$search})";
    if ($ord) {
      $out .= " ORDER BY field({$field},{$search})";
    }
  }
  return $out;
}

function my_match($search, $fields, $maxlen = 50)
{
  $words = safe_implode('|', str_getcsv(str_replace(array('&quot;', '[', ']', '?', '(', ')', '|'), array('"', '', '', '', '', '', ''), substr($search, 0, $maxlen)), ' '));
  $string = '';
  foreach ($fields as $field) {
    $string .= "`$field` REGEXP '{$words}' OR ";
  }
  return substr($string, 0, -4);
}

function my_dump($file, $debug = 0)
{
  global $globvars;
  if (substr_count($_SERVER["SCRIPT_FILENAME"], '\\')) {
    $file = str_replace('/', '\\', $file); // changes to windows slashes
  }
  $fpath = str_replace($globvars['php_self'], '', $_SERVER["SCRIPT_FILENAME"]) . $file;
  $string = "mysqldump --user={$globvars['db_username']} --password={$globvars['db_password']} --host={$globvars['db_hostname']} {$globvars['db_database']} > \"{$fpath}\"";
  if ($debug) {
    print_p($string);
  }
  $ret = exec($string, $retArr, $retVal);
  if ($debug) {
    print_p($ret);
    print_p($retVal);
    print_arr($retArr);
  }
}

function like_csv($field, $search, $sep = ',')
{
  return my_csv($field, $search, $sep);
}

// ----------------------------------------------------CLEAN----------------------------------------------------------------

// Test with these
// ‘ ’ “ ” ` € £
// ! " £ $ % ^ & * ( ) { } [ ] @ ' ~ # < > + - \ / = \\ // , . `
// „ … † ‡ ˆ ‰ Š ‹ ‘ ’ € ‚ “ ” • – — ˜ ™ › œ Ÿ ¡ ¢ £ ¤ ¥
// ¦ § ¨ © ª « ¬ ­ ® ¯ ° ± ² ³ ´ µ ¶ · ¸ ¹ º » ¼ ½ ¾ ¿
// À Á Â Ã Ä Å Æ Ç È É Ê Ë Ì Í Î Ï Ð Ñ Ò Ó Ô Õ Ö × Ø Ù Ú Û Ü Ý Þ ß
// à á â ã ä å æ ç è é ê ë ì í î ï ð ñ ò ó ô õ ö ÷ ø ù ú û ü ý þ ÿ ƒ
// Č č Ď ď Ě ě Ň ň Ř ř Š š Ť ť Ů ů Ž ž
// Ő ő Ű ű

function clean_glob($in)
{
  global $globvars;
  if (in_array(gettype($in), array('integer', 'double', 'string', 'float'))) {
    $fr = array('<', '>', '"', "'", '`', '\&quot;', '../', '£');
    $to = array('&lt;', '&gt;', '&quot;', '&#39;', '&#96;', '&quot;', '', '&pound;');
    if (isset($globvars['charset']) && substr_count($globvars['charset'], 'utf8')) {
      return safe_trim(strip_tags(str_ireplace($fr, $to, urldecode($in))));
    } else {
      return safe_trim(clean_bin(clean_html(strip_tags(clean_ent(str_ireplace($fr, $to, urldecode($in)))))));
    }
  }
  return '';
}

function clean_gcodes($in, $opt)
{
  // used in globvars to exclude certain characters from urldecode
  $a = array('+', '%26');
  $b = array('[&#43;]', '[&#38;]');
  if ($opt == 1) {
    return str_ireplace($a, $b, $in);
  } else {
    return str_ireplace($b, $a, $in);
  }
}

function clean_html($in)
{
  global $globvars;
  if (isset($globvars['charset']) && substr_count($globvars['charset'], 'utf8')) {
    return $in;
  } else {
    $fr = array('`', chr(96), chr(145), chr(146), chr(147), chr(148), chr(149), chr(132), '&#133;');
    $to = array('&lsquo;', '&lsquo;', '&lsquo;', '&rsquo;', '&ldquo;', '&rdquo;', '&bull;', '&bdquo;', '&hellip;');
    return str_ireplace($fr, $to, clean_amp($in));
  }
}

function clean_bin($in)
{
  $in = str_replace('&Aring;&ldquo;', '&oelig;', $in);
  return preg_replace('/[^\r\n\t\x20-\x7E\xA0-\xFF]/', '', $in);
}

function clean_amp($in)
{
  // return preg_replace('/&(?!(aacute|acirc|acute|aelig|agrave|amp|apos|aring|atilde|auml|bdquo|brvbar|bull|ccedil|cedil|cent|circ|copy|curren|dagger|deg|divide|eacute|ecirc|egrave|eth|euml|euro|fnof|frac12|frac14|frac34|frasl|gt|hellip|iacute|icirc|iexcl|igrave|iquest|iuml|laquo|ldquo|lsaquo|lsquo|lt|macr|mdash|micro|middot|nbsp|ndash|not|ntilde|oacute|ocirc|oelig|ograve|ordf|ordm|oslash|otilde|ouml|para|permil|plusmn|pound|quot|raquo|rdquo|reg|rsaquo|rsquo|sbquo|scaron|sect|shy|sup1|sup2|sup3|szlig|thorn|tilde|times|trade|uacute|ucirc|ugrave|uml|uuml|yacute|yen|yuml|#[0-9]*);)/i', '&amp;', $in);
  return preg_replace("/&(?!([\w\n]{2,7}|#[\d]{2,6});)/", "&amp;", $in);
}

function clean_text($in)
{
  global $ent_chr;
  $fr = array('&quot;', '&rsquo;', '&ldquo;', '&lsquo;', '&rdquo;', '&bull;', '&hellip;', '&amp;', '&pound;', '%20', '<br>', '&#39;');
  $to = array('"', '"', '"', "'", "'", '-', '...', '&', '£', ' ', "\r\n", "'");
  return $in ? str_ireplace(array('&lt;', '&gt;'), array('<', '>'), str_replace($ent_chr, '?', strip_tags(str_ireplace($fr, $to, $in)))) : '';
}

function clean_email($in)
{
  if (is_array($in)) {
    $out = array();
    foreach ($in as $em) {
      if ($em) {
        $out[] = clean_email($em);
      }
    }
    return $out;
  } elseif ($in) {
    global $ent_chr, $ent_htm;
    $fr = array('&quot;', '&rsquo;', '&ldquo;', '&lsquo;', '&rdquo;', '&bull;', '&hellip;', '&amp;', '&pound;', '%20', '<br>', '&#39;');
    $to = array('"', '"', '"', "'", "'", '-', '...', '&', '£', ' ', "\r\n", "'");
    return str_ireplace(array('&#34;', '&#39;', '&#47;', '`'), array('"', '"', '/', "'"), str_replace($ent_htm, $ent_chr, strip_tags(str_ireplace($fr, $to, $in))));
  }
  return $in;
}

function clean_meta($in)
{
  $fr = array('&nbsp;', '"', '&quot;', '&rsquo;', '&ldquo;', '&lsquo;', '&rdquo;', '&bull;', '&hellip;', '%20', '<br>', "\r\n", "\r", "\n", '  ');
  $to = array(' ', "'", "'", "'", "'", "'", "'", '-', '...', '&', '£', ' ', ' ', ' ', ' ');
  return $in ? safe_trim(preg_replace('/\s+/', ' ', preg_replace('/[^A-Za-z0-9\.&#,;\-\?\!:\)\(%\|\' ]/', '', str_ireplace(array('&lt;', '&gt;'), array('<', '>'), strip_tags(str_ireplace($fr, $to, $in)))))) : '';
}

function clean_chars($in)
{
  return preg_replace('/[^A-Za-z0-9_\-]/', '', $in);
}

function clean_quotes($in)
{
  return str_replace(["'", '"'], ['&#39;', '&quot;'], $in);
}

// https://www.uspto.gov/custom-page/standard-character-set-0
// http://www.utf8-chartable.de/unicode-utf8-table.pl?utf8=string-literal&unicodeinhtml=dec&htmlent=1
// http://www.utf8-chartable.de/unicode-utf8-table.pl?utf8=string-literal&unicodeinhtml=dec&htmlent=1&start=8192
// http://www.utf8-chartable.de/unicode-utf8-table.pl?utf8=string-literal&unicodeinhtml=dec&htmlent=1&start=8320

$ent_chr = array(
  '„', '…', '†', '‡', 'ˆ', '‰', 'Š', '‹', '‘', '’',
  '€', '‚', '“', '”', '•', '–', '—', '˜', '™', '›',
  'œ', 'Ÿ', '¡', '¢', '£', '¤', '¥', '¦', '§', '¨',
  '©', 'ª', '«', '¬', '­', '®', '¯', '°', '±', '²',
  '³', '´', 'µ', '¶', '·', '¸', '¹', 'º', '»', '¼',
  '½', '¾', '¿', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ',
  'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð',
  'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', '×', 'Ø', 'Ù', 'Ú',
  'Û', 'Ü', 'Ý', 'Þ', 'ß', 'à', 'á', 'â', 'ã', 'ä',
  'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î',
  'ï', 'ð', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', '÷', 'ø',
  'ù', 'ú', 'û', 'ü', 'ý', 'þ', 'ÿ', '&#537;', 'ƒ',
  'Č', 'č', 'Ď', 'ď', 'Ě', 'ě', 'Ň', 'ň', 'Ř', 'ř',
  'Š', 'š', 'Ť', 'ť', 'Ů', 'ů', 'Ž', 'ž',
  'Ő', 'ő', 'Ű', 'ű',
  '&#34;', '&#39;', '&#47;', '<', '>', '`', '&#92;'
);

$ent_uk = array(
  '', '', '', '', '', '', 'S', '', '', '',
  '', '', '', '', '', '-', '-', '', '', '',
  'oe', 'Y', '', '', '', '', '', '', '', '',
  '', '', '', '', '', '', '', '', '', '',
  '', '', 'u', '', '', '', '', '', '', '',
  '', '', '', 'A', 'A', 'A', 'A', 'A', 'A', '',
  'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D',
  'N', 'O', 'O', 'O', 'O', 'O', 'x', '0', 'U', 'U',
  'U', 'U', 'Y', '', 'B', 'a', 'a', 'a', 'a', 'a',
  'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i',
  'i', '', 'n', 'o', 'o', 'o', 'o', 'o', '-', '0',
  'u', 'u', 'u', 'u', 'y', 'p', 'y', '', 'f',
  'C', 'c', 'D', 'd', 'E', 'e', 'N', 'n', 'R', 'r',
  'S', 's', 'T', 't', 'U', 'u', 'Z', 'z',
  'O', 'o', 'U', 'u',
  '', '', '', '', '', '', ''
);

$ent_num = array(
  '&#132;', '&#133;', '&#134;', '&#135;', '&#136;', '&#137;', '&#138;', '&#139;', '&#145;', '&#146;',
  '&#128;', '&#130;', '&#147;', '&#148;', '&#149;', '&#150;', '&#151;', '&#152;', '&#153;', '&#155;',
  '&#156;', '&#159;', '&#161;', '&#162;', '&#163;', '&#164;', '&#165;', '&#166;', '&#167;', '&#168;',
  '&#169;', '&#170;', '&#171;', '&#172;', '&#173;', '&#174;', '&#175;', '&#176;', '&#177;', '&#178;',
  '&#179;', '&#180;', '&#181;', '&#182;', '&#183;', '&#184;', '&#185;', '&#186;', '&#187;', '&#188;',
  '&#189;', '&#190;', '&#191;', '&#192;', '&#193;', '&#194;', '&#195;', '&#196;', '&#197;', '&#198;',
  '&#199;', '&#200;', '&#201;', '&#202;', '&#203;', '&#204;', '&#205;', '&#206;', '&#207;', '&#208;',
  '&#209;', '&#210;', '&#211;', '&#212;', '&#213;', '&#214;', '&#215;', '&#216;', '&#217;', '&#218;',
  '&#219;', '&#220;', '&#221;', '&#222;', '&#223;', '&#224;', '&#225;', '&#226;', '&#227;', '&#228;',
  '&#229;', '&#230;', '&#231;', '&#232;', '&#233;', '&#234;', '&#235;', '&#236;', '&#237;', '&#238;',
  '&#239;', '&#240;', '&#241;', '&#242;', '&#243;', '&#244;', '&#245;', '&#246;', '&#247;', '&#248;',
  '&#249;', '&#250;', '&#251;', '&#252;', '&#253;', '&#254;', '&#255;', '&#537;', '&#131;',
  '&#268;', '&#269;', '&#270;', '&#271;', '&#282;', '&#283;', '&#327;', '&#328;', '&#344;', '&#345;',
  '&#352;', '&#353;', '&#356;', '&#357;', '&#366;', '&#367;', '&#381;', '&#382;',
  '&#336;', '&#337;', '&#368;', '&#369;',
  '&#34;', '&#39;', '&#47;', '&#60;', '&#62;', '&#96;', '&#92;'
);

$ent_ord = array(
  chr(132), chr(133), chr(134), chr(135), chr(136), chr(137), chr(138), chr(139), chr(145), chr(146),
  chr(128), chr(130), chr(147), chr(148), chr(149), chr(150), chr(151), chr(152), chr(153), chr(155),
  chr(156), chr(159), chr(161), chr(162), chr(163), chr(164), chr(165), chr(166), chr(167), chr(168),
  chr(169), chr(170), chr(171), chr(172), chr(173), chr(174), chr(175), chr(176), chr(177), chr(178),
  chr(179), chr(180), chr(181), chr(182), chr(183), chr(184), chr(185), chr(186), chr(187), chr(188),
  chr(189), chr(190), chr(191), chr(192), chr(193), chr(194), chr(195), chr(196), chr(197), chr(198),
  chr(199), chr(200), chr(201), chr(202), chr(203), chr(204), chr(205), chr(206), chr(207), chr(208),
  chr(209), chr(210), chr(211), chr(212), chr(213), chr(214), chr(215), chr(216), chr(217), chr(218),
  chr(219), chr(220), chr(221), chr(222), chr(223), chr(224), chr(225), chr(226), chr(227), chr(228),
  chr(229), chr(230), chr(231), chr(232), chr(233), chr(234), chr(235), chr(236), chr(237), chr(238),
  chr(239), chr(240), chr(241), chr(242), chr(243), chr(244), chr(245), chr(246), chr(247), chr(248),
  chr(249), chr(250), chr(251), chr(252), chr(253), chr(254), chr(255), '&#537;', 'ƒ',
  'Č', 'č', 'Ď', 'ď', 'Ě', 'ě', 'Ň', 'ň', 'Ř', 'ř',
  'Š', 'š', 'Ť', 'ť', 'Ů', 'ů', 'Ž', 'ž',
  'Ő', 'ő', 'Ű', 'ű',
  chr(34), chr(39), chr(47), chr(60), chr(62), chr(96), chr(92)
); // chr only 0-255

$ent_htm = array(
  '&bdquo;', '&hellip;', '&dagger;', '&Dagger;', '&circ;', '&permil;', '&Scaron;', '&lsaquo;', '&lsquo;', '&rsquo;',
  '&euro;', '&sbquo;', '&ldquo;', '&rdquo;', '&bull;', '&ndash;', '&mdash;', '&tilde;', '&trade;', '&rsaquo;',
  '&oelig;', '&Yuml;', '&iexcl;', '&cent;', '&pound;', '&curren;', '&yen;', '&brvbar;', '&sect;', '&uml;',
  '&copy;', '&ordf;', '&laquo;', '&not;', '&shy;', '&reg;', '&macr;', '&deg;', '&plusmn;', '&sup2;',
  '&sup3;', '&acute;', '&micro;', '&para;', '&middot;', '&cedil;', '&sup1;', '&ordm;', '&raquo;', '&frac14;',
  '&frac12;', '&frac34;', '&iquest;', '&Agrave;', '&Aacute;', '&Acirc;', '&Atilde;', '&Auml;', '&Aring;', '&AElig;',
  '&Ccedil;', '&Egrave;', '&Eacute;', '&Ecirc;', '&Euml;', '&Igrave;', '&Iacute;', '&Icirc;', '&Iuml;', '&ETH;',
  '&Ntilde;', '&Ograve;', '&Oacute;', '&Ocirc;', '&Otilde;', '&Ouml;', '&times;', '&Oslash;', '&Ugrave;', '&Uacute;',
  '&Ucirc;', '&Uuml;', '&Yacute;', '&THORN;', '&szlig;', '&agrave;', '&aacute;', '&acirc;', '&atilde;', '&auml;',
  '&aring;', '&aelig;', '&ccedil;', '&egrave;', '&eacute;', '&ecirc;', '&euml;', '&igrave;', '&iacute;', '&icirc;',
  '&iuml;', '&eth;', '&ntilde;', '&ograve;', '&oacute;', '&ocirc;', '&otilde;', '&ouml;', '&divide;', '&oslash;',
  '&ugrave;', '&uacute;', '&ucirc;', '&uuml;', '&yacute;', '&thorn;', '&yuml;', '&#537;', '&fnof;',
  '&#268;', '&#269;', '&#270;', '&#271;', '&#282;', '&#283;', '&#327;', '&#328;', '&#344;', '&#345;',
  '&Scaron;', '&scaron;', '&#356;', '&#357;', '&#366;', '&#367;', '&#381;', '&#382;',
  '&#336;', '&#337;', '&#368;', '&#369;',
  '&quot;', '&#39;', '/', '&lt;', '&gt;', '&lsquo;', '&#92;'
);

$ent_utf = array(
  "\xe2\x80\x9e", "\xe2\x80\xa6", "\xe2\x80\xa0", "\xe2\x80\xa1", "\xcb\x86", "\xe2\x80\xb0", "\xc5\xa0", "\xe2\x80\xb9", "\xe2\x80\x98", "\xe2\x80\x99",
  "\xe2\x82\xac", "\xe2\x80\x9a", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\xa2", "\xe2\x80\x93", "\xe2\x80\x94", "\xcb\x9c", "\xe2\x8\xa2", "\xe2\x80\xba",
  "\xc5\x92", "\xc5\xb8", "\xc2\xa1", "\xc2\xa2", "\xc2\xa3", "\xc2\xa4", "\xc2\xa5", "\xc2\xa6", "\xc2\xa7", "\xc2\xa8",
  "\xc2\xa9", "\xc2\xaa", "\xc2\xab", "\xc2\xac", "\xc2\xad", "\xc2\xae", "\xc2\xaf", "\xc2\xb0", "\xc2\xb1", "\xc2\xb2",
  "\xc2\xb3", "\xc2\xb4", "\xc2\xb5", "\xc2\xb6", "\xc2\xb7", "\xc2\xb8", "\xc2\xb9", "\xc2\xba", "\xc2\xbb", "\xc2\xbc",
  "\xc2\xbd", "\xc2\xbe", "\xc2\xbf", "\xc3\x80", "\xc3\x81", "\xc3\x82", "\xc3\x83", "\xc3\x84", "\xc3\x85", "\xc3\x86",
  "\xc3\x87", "\xc3\x88", "\xc3\x89", "\xc3\x8a", "\xc3\x8b", "\xc3\x8c", "\xc3\x8d", "\xc3\x8e", "\xc3\x8f", "\xc3\x90",
  "\xc3\x91", "\xc3\x92", "\xc3\x93", "\xc3\x94", "\xc3\x95", "\xc3\x96", "\xc3\x97", "\xc3\x98", "\xc3\x99", "\xc3\x9a",
  "\xc3\x9b", "\xc3\x9c", "\xc3\x9d", "\xc3\x9e", "\xc3\x9f", "\xc3\xa0", "\xc3\xa1", "\xc3\xa2", "\xc3\xa3", "\xc3\xa4",
  "\xc3\xa5", "\xc3\xa6", "\xc3\xa7", "\xc3\xa8", "\xc3\xa9", "\xc3\xaa", "\xc3\xab", "\xc3\xac", "\xc3\xad", "\xc3\xae",
  "\xc3\xaf", "\xc3\xb0", "\xc3\xb1", "\xc3\xb2", "\xc3\xb3", "\xc3\xb4", "\xc3\xb5", "\xc3\xb6", "\xc3\xb7", "\xc3\xb8",
  "\xc3\xb9", "\xc3\xba", "\xc3\xbb", "\xc3\xbc", "\xc3\xbd", "\xc3\xbe", "\xc3\xbf", "\xc8\x99", "\xc6\x92",
  "\xc4\x8c", "\xc4\x8d", "\xc4\x8e", "\xc4\x8f", "\xc4\x9a", "\xc4\x9b", "\xc5\x87", "\xc5\x88", "\xc5\x98", "\xc5\x99",
  "\xc5\xa0", "\xc5\xa1", "\xc5\xa4", "\xc5\xa5", "\xc5\xae", "\xc5\xaf", "\xc5\xbd", "\xc5\xbe",
  "\xc5\x90", "\xc5\x91", "\xc5\xb0", "\xc5\xb1",
  "\x22", "\x27", "\x2f", "\x3c", "\x3e", "\x60", "\\"
);

function clean_ent($in)
{
  global $ent_ord, $ent_chr, $ent_htm, $ent_num, $ent_utf;
  // convert characters to keep
  return str_replace($ent_ord, $ent_htm, str_replace($ent_chr, $ent_htm, str_replace($ent_num, $ent_htm, str_replace($ent_utf, $ent_htm, $in))));
}

function clean_arr($in)
{
  global $ent_chr, $ent_htm, $ent_num, $ent_utf;
  $out = array('original' => $in, 'clean_ent' => '', 'clean_bin' => '', 'clean_ent_bin' => '', 'clean_glob' => '');
  if (!is_array($in)) {
    $in = $in ? strip_tags(urldecode($in)) : '';
    $out['clean_ent'] = clean_ent($in);
    $out['clean_bin'] = clean_bin($in);
    $out['clean_ent_bin'] = clean_bin($out['clean_ent']);
    $out['clean_glob'] = clean_glob($in);
  }
  return $out;
}

function clean_lower($in)
{
  global $ent_chr, $ent_htm, $ent_num;
  return $in ? str_replace($ent_num, $ent_htm, strtolower(str_replace($ent_htm, $ent_num, $in))) : $in;
}

function clean_upper($in)
{
  global $ent_chr, $ent_htm, $ent_num;
  return $in ? str_replace($ent_num, $ent_htm, strtoupper(str_replace($ent_htm, $ent_num, $in))) : $in;
}

function clean_ucwords($in)
{
  return $in ? str_replace('A-z', 'A-Z', ucwords(clean_lower($in))) : $in;
}

function clean_int($in)
{
  $in = filter_var(safe_trim($in), FILTER_SANITIZE_NUMBER_INT);
  if (!$in) {
    $in = 0;
  }
  return $in;
}

function clean_float($in)
{
  $opts = array('flags' => FILTER_FLAG_ALLOW_FRACTION);
  $in = filter_var(safe_trim($in), FILTER_SANITIZE_NUMBER_FLOAT, $opts);
  if (!$in) {
    $in = 0;
  }
  return $in;
}

function clean_explode($delim, $in)
{
  if (!$delim) {
    $delim = ',';
  }
  if (is_string($in) && $in && strlen($in = safe_trim($in))) {
    return array_map('trim', explode($delim, $in));
  } else {
    return array();
  }
}

function clean_http($in)
{
  $in = $in ? str_replace('https://', '', str_replace('http://', '', $in)) : $in;
  if (substr($in, -1) == '/') {
    $in = substr($in, 0, -1);
  }
  return $in;
}

function list_arr($in)
{
  $in = $in ? str_replace("\r", "\n", $in) : $in;
  $in = $in ? str_replace("\n\n", "\n", $in) : $in;
  return clean_explode("\n", $in);
}

function safe_explode($delim, $in)
{
  // $delim can't be blank for explode
  if (!$delim) {
    $delim = ',';
  }
  if (is_string($in) && $in) {
    return explode($delim, $in);
  } else {
    return [];
  }
}

function safe_implode($delim, $in)
{
  // $delim can be blank for implode
  if (is_array($in) && count($in)) {
    return implode($delim, $in);
  } else {
    return '';
  }
}

function safe_trim($in, $chrs = '')
{
  if ($in) {
    $in = $chrs ? trim($in, $chrs) : trim($in);
  }
  return $in;
}

function safe_rtrim($in, $chrs = '')
{
  if ($in) {
    $in = $chrs ? rtrim($in, $chrs) : rtrim($in);
  }
  return $in;
}

function safe_ltrim($in, $chrs = '')
{
  if ($in) {
    $in = $chrs ? ltrim($in, $chrs) : ltrim($in);
  }
  return $in;
}

function safe_floor($in)
{
  return (floor(clean_float($in)));
}

function safe_ceil($in)
{
  return (ceil(clean_float($in)));
}

function array_sort($array, $field, $sort = '', $zerolast = false)
{
  /* deprecated 7.2
  $code = "return strnatcmp(\$a['$field'], \$b['$field']);";
  usort($array, create_function('$a,$b', $code));
  */
  uasort($array, function ($a, $b) use ($field, $zerolast) {
    if (is_array($field)) {
      $af = $bf = '';
      foreach ($field as $f) {
        $af .= $a[$f] . '_';
        $bf .= $b[$f] . '_';
      }
      $af = substr($af, 0, -1);
      $bf = substr($bf, 0, -1);
    } else {
      $af = $a[$field];
      $bf = $b[$field];
    }
    $x = $zerolast && !$af ? 'zzz' : $af;
    $y = $zerolast && !$bf ? 'zzz' : $bf;
    return strnatcmp($x, $y);
  });
  if ($sort == 'DESC') {
    $array = array_reverse($array, true);
  }
  return $array;
}

if (!function_exists('array_key_first')) {
  function array_key_first($a)
  {
    $f = null;
    if (is_array($a)) {
      foreach ($a as $k => $v) {
        $f = $k;
        break;
      }
    }
    return $f;
  }
}

if (!function_exists('array_key_last')) {
  function array_key_last($a)
  {
    $f = null;
    if (is_array($a)) {
      $a = array_reverse($a, true);
      foreach ($a as $k => $v) {
        $f = $k;
        break;
      }
    }
    return $f;
  }
}

function array_prev_next($arr, $key, $loop = true)
{
  $out = array('prev' => '', 'this' => '', 'next' => '');
  $keys = array_keys($arr);
  // print array_search($key,$keys) ;
  if (($n = array_search($key, $keys)) !== false) {
    $out['this'] = $keys[$n];
    if (isset($keys[$n - 1])) {
      // previous
      $out['prev'] = $keys[$n - 1];
    } elseif ($loop) {
      // last
      $out['prev'] = $keys[count($keys) - 1];
    }
    if (isset($keys[$n + 1])) {
      $out['next'] = $keys[$n + 1];
    } elseif ($loop) {
      // first
      $out['next'] = $keys[0];
    }
  }
  return $out;
}

function filter_str($in, $d1 = '|', $d2 = '^')
{
  // returns filter as string
  if (is_array($in)) {
    $out = '';
    if (isset($in[0])) {
      $out = $in[0];
    } else {
      foreach ($in as $f_key => $f_arr1) {
        if (!is_array($f_arr1)) {
          $f_arr1 = safe_explode(',', $f_arr1);
        }
        foreach ($f_arr1 as $f_str) {
          $out .= "{$f_key}{$d1}{$f_str}{$d2}";
        }
      }
      $out = substr($out, 0, -1);
    }
  } else {
    $out = $in;
  }
  return $out;
}

function filter_arr($in, $d1 = '|', $d2 = '^')
{
  if (!is_array($in)) {
    $arr = safe_explode($d2, $in);
    $in = [];
    foreach ($arr as $v) {
      if (substr_count($v, $d1) && count($v = safe_explode($d1, $v)) > 1 && $v[0] && $v[1] && !isset($in[$v[0]])) {
        $in[$v[0]] = $v[1];
      }
    }
  }
  return $in;
}

function filter_chk($search = '')
{
  // returns filter as string if no search string or search match
  globvars('filter');
  global $globvars;
  $string = filter_str($globvars['filter']);
  return ((!$search) || substr_count($string, $search)) ? $string : '';
}

function delim_count($string, $search, $delim = ',')
{
  return substr_count($delim . $string . $delim, $delim . $search . $delim);
}

function noc_url($in)
{
  return file_exists($in) ? "$in?" . filemtime($in) : $in;
}

if (!function_exists('clean_url')) {
  function clean_url($in)
  {
    return clean_link($in);
  }
}

function clean_link($in, $sc = '||^||')
{
  if ($in) {
    $in = $in ? str_replace(array('?', '&amp;'), $sc, clean_amp($in)) : $in;
    $in_arr = safe_explode($sc, $in);
    $in = $in_arr[0];
    if (($cn = count($in_arr)) > 1) {
      $in .= '?';
      for ($c = 1; $c < $cn; $c++) {
        if ($in_arr[$c]) {
          if ($c > 1) {
            $in .= '&amp;';
          }
          if (strpos($in_arr[$c], '=')) {
            $in .= strtok($in_arr[$c], '=') . '=' . urlencode(strtok('='));
          } else {
            $in .= $in_arr[$c];
          }
        }
      }
    }
    return str_ireplace(' ', '%20', clean_amp($in));
  } else {
    return '#';
  }
}

function clean_link_old($in)
{
  if ($in) {
    if (($fp = strpos($in, '&amp;')) && !substr_count($in, '?')) {
      $in = substr_replace($in, '?', $fp, 5);
    }
    if (($fp = strpos($in, '&')) && !substr_count($in, '?')) {
      $in = substr_replace($in, '?', $fp, 1);
    }
    if (strpos($in, '?') && strpos($in, '=')) {
      $in_arr = preg_split("/[?&]/", str_ireplace('&amp;', '&', $in));
      $in = $in_arr[0] . '?';
      for ($c = 1; $c < count($in_arr); $c++) {
        $in .= strtok($in_arr[$c], '=') . '=' . urlencode(strtok('=')) . '&amp;';
      }
      $in = substr($in, 0, -5);
    }
    return str_ireplace(' ', '%20', clean_amp($in));
  } else {
    return '#';
  }
}

function clean_sql($in)
{
  if (isset($globvars['db_open']) && $globvars['db_open']) {
    global $globvars;
    $in = $globvars['db_mysqli'] ? mysqli_real_escape_string($globvars['db_mysqli'], $in) : mysql_real_escape_string($in);
  } else {
    $in = addslashes($in);
  }
  return $in;
}

function clean_strt($in)
{
  $in = strtotime($in);
  if ((!$in) || ($in < 0)) {
    $in = 0;
  }
  return $in;
}

function clean_urln($in, $max = 0)
{
  global $ent_htm, $ent_uk;
  $in = $in ? clean_text(strtolower(str_replace($ent_htm, $ent_uk, clean_ent($in)))) : $in;
  $in = preg_replace('/-+/', '-', preg_replace('/[^a-z0-9-\.]/', '', preg_replace('/[\s+_+]/', '-', $in)));
  if ($max) {
    $in = substr($in, 0, $max);
  }
  return $in;
}

function clean_qstring($string, $keys = '')
{
  // string is query string
  // keys is array of keys to allow (blank for any)
  global $globvars;
  $out = '';
  if ($keys && !is_array($keys)) {
    $keys = safe_explode(",", $keys);
  }
  // parse query string to array
  parse_str(str_replace('&amp;', '&', $string), $arr);
  if (is_array($arr) && count($arr)) {
    foreach ($arr as $key => $val) {
      if ($key && is_string($key)) {
        if ((!$keys) || in_array($key, $keys)) {
          // allowed keys or any if blank
          if (isset($globvars[$key]) && $globvars[$key]) {
            // override from globvars if set
            $val = $globvars[$key];
          } else {
            // cleanup if from query string
            $val = clean_glob($val);
          }
          if ($val && (is_string($val) || is_int($val) || is_float($val))) {
            // if value add to string
            $out .= "{$key}={$val}&";
          }
        }
      }
    }
  }
  $out = $out ? ('?' . str_replace('&', '&amp;', substr($out, 0, -1))) : '';
  return $out;
}

function clean_index($in = '')
{
  return ($in == 'index.php' || $in == 'index') ? '' : $in;
}

function make_qstring($in = '', $add = '')
{
  // $in is array of keys to build query string from globvars
  // $add is array of key => val to add to query string
  global $globvars;
  $arr = array();
  $out = '';
  // from globvars
  if ($in && is_array($in)) {
    foreach ($in as $key) {
      if ($key && is_string($key) && isset($globvars[$key]) && $globvars[$key] && (is_float($globvars[$key]) || is_int($globvars[$key]) || is_string($globvars[$key]))) {
        $arr[$key] = $globvars[$key];
      }
    }
  }
  // add these
  if ($add && is_array($add)) {
    foreach ($add as $key => $val) {
      $arr[$key] = $val;
    }
  }
  // array to query string
  foreach ($arr as $key => $val) {
    if ($key && $val) {
      $out .= "{$key}={$val}&";
    }
  }
  if ($out) {
    $out = '?' . str_replace('&', '&amp;', substr($out, 0, -1));
  }
  return $out;
}

function str_right($in, $len, $pad = ' ')
{
  return (str_pad($in, $len, $pad, STR_PAD_RIGHT));
}

function str_left($in, $len, $pad = ' ')
{
  return (str_pad($in, $len, $pad, STR_PAD_LEFT));
}

function cdate($date, $fmt = '', $nad = 'N/A')
{
  if ($date = safe_trim($date)) {
    $d = 0;
    $m = 0;
    $y = 0;
    $h = 0;
    $i = 0;
    $s = 0;
    $format = '';
    $p3 = strlen($date);
    if (substr_count($date, ' ')) {
      $p0 = strpos($date, ' ');
      if (substr_count($date, ':')) {
        // Input includes time
        $p1 = strpos($date, ':');
        $p2 = strrpos($date, ':');
        $h = str_left(substr($date, $p0 + 1, $p1 - $p0 - 1), 2, '0');
        if ($p1 != $p2) {
          $format = " H:i:s";
          $i = str_left(substr($date, $p1 + 1, $p2 - $p1 - 1), 2, '0');
          $s = str_left(substr($date, $p2 + 1, $p3 - $p2 - 1), 2, '0');
        } else {
          $format = " H:i";
          $i = str_left(substr($date, $p1 + 1, $p3 - $p1 - 1), 2, '0');
        }
      }
      $date = substr($date, 0, $p0);
      $p3 = strlen($date);
    }
    if (substr_count($date, '-') == 2) {
      // Input Y-m-d
      $format = "d/m/Y" . $format;
      $p1 = strpos($date, '-');
      $p2 = strrpos($date, '-');
      $d = str_left(substr($date, $p2 + 1, $p3 - $p2 - 1), 2, '0');
      $m = str_left(substr($date, $p1 + 1, $p2 - $p1 - 1), 2, '0');
      $y = substr($date, 0, $p1);
    } elseif (substr_count($date, '/') == 2) {
      // Input d/m/Y
      $format = "Y-m-d" . $format;
      $p1 = strpos($date, '/');
      $p2 = strrpos($date, '/');
      $d = str_left(substr($date, 0, $p1), 2, '0');
      $m = str_left(substr($date, $p1 + 1, $p2 - $p1 - 1), 2, '0');
      $y = substr($date, $p2 + 1, $p3 - $p2 - 1);
    }
    if ($fmt) {
      // specified format output
      $format = $fmt;
    }
    if (strlen($y) == 2) {
      $y = '20' . $y;
    }
    if ($d && $m && (strlen($y) == 4) && is_numeric($d) && is_numeric($m) && is_numeric($y) && $format && checkdate($m, $d, $y) && (date("Y-m-d", strtotime("$y-$m-$d")) == "$y-$m-$d")) {
      return date($format, mktime($h, $i, $s, $m, $d, $y));
    }
  }
  return $nad;
}

function ctime($time, $fmt = 'H:i:s', $nat = 'N/A')
{
  if ($time = safe_trim(str_replace('.', ':', $time))) {
    $arr = array('H', 'i', 's');
    foreach ($arr as $tb) {
      $$tb = 0;
      if (substr_count($time, ':')) {
        $$tb = substr($time, 0, $p1 = strpos($time, ':'));
        $time = substr($time, $p1 + 1);
      } else {
        $$tb = substr($time, 0, 2);
        $time = substr($time, 2);
      }
      if (!is_numeric($$tb)) {
        $$tb = 0;
      }
    }
    if ($H + $i + $s > 0) {
      return (date($fmt, mktime($H, $i, $s)));
    }
  }
  return $nat;
}

function ddates($date2, $date1 = '')
{
  // returns days difference (positive if date2 > date1)
  // dates default to today if not set & valid
  $date2 = cdate($date2, 'Y-m-d', date('Y-m-d'));
  $date1 = cdate($date1, 'Y-m-d', date('Y-m-d'));
  $interval = date_diff(date_create($date1), date_create($date2));
  return (int)$interval->format('%r%a');
}

function vdate($date)
{
  // date to return visbible true/false
  if ($date === NULL) {
    // field not exist so allow true
    return true;
  } elseif ((!$date) || ($date == '0000-00-00')) {
    // zero set so make false
    return false;
  } elseif (ddates($date) <= 0) {
    // today or past is true
    return true;
  } else {
    // date in future is false
    return false;
  }
}

// ----------------------------------------------------PRINT----------------------------------------------------------------

function disp($in, $clean_html = 1)
{
  $fr = array('[o]', '[b]', '[/b]', '[i]', '[/i]', '[u]', '[/u]', '[s]', '[/s]', '[r]', '[/r]');
  $to = array('&bull; ', '<b>', '</b>', '<i>', '</i>', '<u>', '</u>', '<sup>', '</sup>', '<span class="red">', '</span>');
  if ($clean_html) {
    $in = str_replace(array("\r\n", "\r", "\n"), '<br>', str_ireplace($fr, $to, clean_html($in)));
  } else {
    $in = str_ireplace($fr, $to, $in);
  }

  // email [mail:name@domain.co.uk|text|subject]
  $in = preg_replace_callback("/\[mail:([^\]\|]*)\|([^\]\|]*)\|([^\]\|]*)\]/i", "mailto", $in);

  // email [mail:name@domain.co.uk|text]
  $in = preg_replace_callback("/\[mail:([^\]\|]*)\|([^\]\|]*)\]/i", "mailto", $in);

  // email [mail:name@domain.co.uk]
  $in = preg_replace_callback("/\[mail:([^\]\|]*)\]/i", "mailto", $in);

  // links [link:url|text]
  $in = preg_replace("/\[link:([^\]\|]*)\|([^\]\|]*)\]/i", "<a href=\"$1\">$2</a>", $in);

  // links [link:url]
  $in = preg_replace("/\[link:([^\]\|]*)\]/i", "<a href=\"$1\">$1</a>", $in);

  return $in;
}

function print_p($in, $class = '', $style = '')
{
  if (is_array($in)) {
    print_arr($in);
  } else {
    $class = ($class) ? " class=\"{$class}\"" : '';
    $style = ($style) ? " style=\"{$style}\"" : '';
    echo "<p{$class}{$style}>{$in}</p>";
  }
}

function print_arv($in, $name = null, $class = '')
{
  $atype = gettype($in);
  if (function_exists('dump')) {
    if ($name) {
      print_pre($name);
    }
    dump($in);
  } else {
    print_arr($in, $name, $class);
  }
}

function print_arr($in, $name = null, $class = '')
{
  $atype = gettype($in);
  $style = ($class) ? " class=\"{$class}\"" : ' style="background-color:#FFFFFF; color:#000000; text-align:left"';
  echo "<pre{$style}>";
  if ($name) {
    echo "<b>{$atype}: {$name}</b>\r\n\r\n";
  }
  print_r($in);
  echo '</pre>';
}

function objectToArray($o)
{
  $a = [];
  if (is_array($o) || is_object($o)) {
    foreach ($o as $k => $v) {
      $a[$k] = (is_array($v) || is_object($v)) ? objectToArray($v) : $v;
    }
  }
  return $a;
}

function print_pre($in, $class = '')
{
  $style = ($class) ? " class=\"{$class}\"" : ' style="background-color:#FFFFFF; color:#000000; text-align:left"';
  echo "<pre{$style}>{$in}</pre>";
}

function print_d($in, $line = '', $file = '')
{
  // debug usage: print_d($string,__LINE__,__FILE__);
  $marker = ($line && $file) ? "$file Line: $line" : "__FILE__ Line: __LINE__";
  if (is_array($in)) {
    print_p($marker, 'red');
    print_arr($in, '', 'red');
  } else {
    print_p("{$marker}<br>{$in}", 'red');
  }
}

function print_n($in)
{
  echo "{$in}\r\n";
}

function print_c($in, $dp = 2, $symb = '')
{
  if (is_numeric($in)) {
    if (!$symb) {
      $symb = '&pound;';
    }
    $num = floatval(preg_replace('/[^\d.]/', '', $in));
    if (is_float($num) || is_int($num)) {
      if ($in >= 0) {
        echo $symb . number_format($num, $dp);
      } else {
        echo "-$symb" . number_format($num, $dp);
      }
    }
  }
}

function dprice($price, $nprice = 0, $discp = 0, $discf = 0, $number = 1, $disp = 0, $classp = '', $classd = '', $spacer = '&nbsp; ')
{
  // original price, new price, percentage disc, fixed disc, display, original class, discounted class
  if ($nprice > 0) {
    $price1 = $nprice;
  } elseif ($discp > 0 && $discp < 1) {
    $price1 = round($price * (1 - $discp), 2);
  } elseif ($discf > 0) {
    $price1 = round($price - $discf, 2);
  } else {
    $price1 = $price;
  }
  $price = $price * $number;
  $price1 = $price1 * $number;
  if ($disp) {
    if ($price == $price1) {
      print_c($price);
    } else {
      $sp2 = $sd2 = $sp1 = $sd1 = '';
      if ($classp) {
        $sp1 = "<span class=\"{$classp}\">";
        $sp2 = '</span>';
      } else {
        $sp1 = "<span style=\"text-decoration: line-through\">";
        $sp2 = '</span>';
      }
      if ($classd) {
        $sd1 = "<span class=\"{$classd}\">";
        $sd2 = '</span>';
      }
      print $sp1;
      print_c($price);
      print $sp2 . $spacer . $sd1;
      print_c($price1);
      print $sd2;
    }
  } else {
    return $price1;
  }
}

function cliptext($in, $max = 100, $add = ' ...')
{
  // cliptext('long text here',200)
  if ($max > 0 && strlen($in) > $max) {
    $clip = substr($in, 0, $max);
    $last = strlen($clip);
    $brks = array(' ', '_', '-', '|');
    foreach ($brks as $brk) {
      if (substr_count($clip, $brk)) {
        $last = strrpos($clip, $brk);
        break;
      }
    }
    $in = safe_trim(substr($clip, 0, $last)) . $add;
  }
  return clean_amp($in);
}

function get_include($in, $gi_start, $gi_end, $debug = 0)
{
  // get_include(file.inc.html,'<!-- start -->','<!-- end -->');
  global $globvars;
  if (substr_count($in, '?')) {
    $qs = str_replace('&amp;', '&', substr($in, strpos($in, '?') + 1));
    $in = substr($in, 0, strpos($in, '?'));
    parse_str($qs, $vars);
    foreach ($vars as $var => $val) {
      $globvars[$var] = $val;
    }
  }
  if ($incfile = isvar($globvars['incfile'][$in])) {
    $spos = strpos($incfile, $gi_start) + ($debug ? 0 : strlen($gi_start));
    $slen = strpos($incfile, $gi_end) - $spos + ($debug ? strlen($gi_end) : 0);
    $out = substr($incfile, $spos, $slen);
    $out = str_replace('-->', ob_get_level() . ' -->', $out);
    echo "\r\n{$out}\r\n";
  } elseif (is_file($in)) {
    $globvars['incfile'][$in] = $incfile = ob_include($in);
    $spos = strpos($incfile, $gi_start) + ($debug ? 0 : strlen($gi_start));
    $slen = strpos($incfile, $gi_end) - $spos + ($debug ? strlen($gi_end) : 0);
    $out = substr($incfile, $spos, $slen);
    $out = str_replace('-->', ob_get_level() . ' -->', $out);
    echo "\r\n{$out}\r\n";
  }
}

function rep_var($str, &$arr, $fr = '[[', $to = ']]')
{
  if (substr_count($str, $fr) == substr_count($str, $to)) {
    $frl = strlen($fr);
    $tol = strlen($to);
    while ($vpos = substr_count($str, $fr)) {
      $vfrm = strpos($str, $fr);
      $vlen = strpos($str, $to, $vfrm) - $vfrm;
      $vstr = substr($str, $vfrm + $frl, $vlen - $frl);
      $rep = '';
      if (isset($arr[$vstr])) {
        $rep = $arr[$vstr];
      }
      $str = substr_replace($str, $rep, $vfrm, $vlen + $tol);
    }
  }
  return $str;
}

// ----------------------------------------------------EMAIL----------------------------------------------------------------

function checkemail($email)
{
  $email = clean_email($email);
  /*
  if( $email && (strlen($email) > 5) && preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i", $email)) {
    return true ;
  }
  */
  if ($email && (strlen($email) > 5) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
    return true;
  }
  return false;
}

function mailto($email = '', $text = '', $image = '', $subject = '', $print = 1)
{
  if (is_array($email)) {
    // used for disp callback
    $em_arr = $email;
    $email = isset($em_arr[1]) ? $em_arr[1] : '';
    $text = isset($em_arr[2]) ? $em_arr[2] : '';
    $subject = isset($em_arr[3]) ? $em_arr[3] : '';
    $print = 0;
  }
  $email = clean_email($email);
  if (checkemail($email)) {
    $a = "'" . strtok($email, '@') . "'";
    $b = "'" . strtok('@') . "'";
    $cr = "\r\n";
    $disp = 'c+"';
    if ($text) {
      $disp = '"' . str_replace('"', '\"', $text);
    }
    if ($image) {
      $disp = '"<im"+"g src=\"' . str_replace('"', '\"', $image) . '\" border=\"0\" alt=\"\">';
    }
    if ($subject) {
      $subject = '?subject=' . str_replace('"', '\"', $subject);
    }
    $out = '<script>' . $cr;
    $out .= 'var c = ' . $a . ' + "@";' . $cr;
    $out .= 'c += ' . $b . ';' . $cr;
    $out .= 'document.write("<a title=\"Send Email\" href=\"mailto:"+c+"' . $subject . '\">");' . $cr;
    $out .= 'document.write(' . $disp . '<\/A>");' . $cr;
    $out .= '</script>';
    if ($print) {
      echo $out;
    } else {
      return ($out);
    }
  } else {
    return false;
  }
}

function em_para($in)
{
  // make global in calling script
  global $em_text, $em_html;
  $em_text .= $in . "\n\n";
  if (substr_count($in, ':')) {
    // bold before :
    $in = '<b>' . substr($in, 0, strpos($in, ':')) . ':</b>' . substr($in, strpos($in, ':') + 1);
  }
  $p_mrg = 0;
  if (strrpos($in, "\n") && (strrpos($in, "\n") == (strlen($in) - 1))) {
    $p_mrg = 12;
    $in = substr($in, 0, strlen($in) - 1);
  }
  $em_html .= "<p style=\"margin:0 0 {$p_mrg}px 0\">" . str_replace("\n", '<br>', $in) . "</p>\n";
}

function em_table()
{
  // make global in calling script
  global $em_text, $em_html, $em_cols;
  $em_html .= "<tr>\n";
  for ($i = 0; $i < func_num_args(); $i++) {
    $em_html .=  '<td>' . func_get_arg($i) . "</td>\n";
    $em_col = isvar($em_cols[$i], 20);
    $em_text .= str_pad(func_get_arg($i), $em_col, ' ', STR_PAD_RIGHT);
  }
  $em_html .= "</tr>\n";
  $em_text .= "\n\n";
}

function em_html($in)
{
  $out = "<html>\n<head>\n<style type=\"text/css\">\n";
  $out .= "BODY, P, TD { font-family:Arial; font-size:12px; }\n";
  if (substr_count($in, '<tr>')) {
    $out .= ".tableb, .tableb td, .tableb th { border: 1px solid #BBBBBB; border-collapse: collapse; }\n";
    $out .= "</style\n</head>\n<body>\n<div style=\"width:600px;\">\n";
    $out .= "<table class=\"tableb\" cellpadding=\"4\" cellspacing=\"0\" summary=\"\">\n{$in}\n</table>";
    $out .= "</div>\n</body>\n</html>\n";
  } else {
    $out .= "</style\n</head>\n<body>\n<div style=\"width:600px;\">\n{$in}</div>\n</body>\n</html>\n";
  }
  return ($out);
}

function em_text($in)
{
  $fr = array('[o]', '[b]', '[/b]', '[i]', '[/i]', '[u]', '[/u]', '[s]', '[/s]');
  $to = array('', '', '', '', '', '', '', '', '');
  $in = str_ireplace($fr, $to, $in);

  // links [link|text]
  $in = preg_replace("/\[(.*)\|(.*)\]/i", "$1", $in);

  // links [link]
  $in = preg_replace("/\[(.*)\]/i", "$1", $in);

  return $in;
}

function sendmail($em_to, $em_fr, $subject, $content, $reply = null, $em_cc = null, $em_bcc = null, $headers = null, $em_fn = null)
{
  // sendmail('to@test.com', 'from@test.com', 'subject', $content, '', '', '', '', 'Sender Name');
  // attempt using phpmailer
  $em_to = clean_email($em_to);
  $em_fr = clean_email($em_fr);
  $reply = clean_email($reply);
  $em_cc = clean_email($em_cc);
  $em_bcc = clean_email($em_bcc);
  $head_arr = safe_explode("\r\n", $headers);
  foreach ($head_arr as $head_itm) {
    $head = safe_explode(':', $head_itm);
    if (isset($head[1])) {
      $head[0] = strtolower(safe_trim($head[0]));
      $head[1] = safe_trim($head[1]);
      if ($head[0] == 'reply-to') {
        $reply = $head[1];
      } elseif ($head[0] == 'bcc') {
        $em_bcc = $head[1];
      }
    }
  }
  if (!$em_fn) {
    $em_fn = substr($em_fr, 0, strpos($em_fr, '@'));
  }
  $res = htmlmail($em_to, $em_fr, $em_fn, $subject, $content, '', '', $reply, $em_cc, $em_bcc);
  if ($res === true) {
    return true;
  }
  // use old method
  $globvars['crlf'] = $crlf = (substr_count(isvar($_SERVER['SERVER_SOFTWARE']), 'Microsoft')) ? "\r\n" : "\n";
  $head_arr = array();
  $head_arr[] = "From: $em_fr";
  if ($reply) {
    $head_arr[] = "Reply-To: $reply";
  }
  $head_arr[] = "X-Sender: $em_fr";
  $head_arr[] = 'X-Mailer: PHP/' . phpversion();
  $head_arr[] = "Return-Path: $em_fr";
  if (is_array($em_to)) {
    $em_to = safe_implode(',', $em_to);
  }
  if ($em_cc) {
    if (is_array($em_cc)) {
      $em_cc = safe_implode(',', $em_cc);
    }
    $head_arr[] = "cc: $em_cc";
  }
  if ($em_bcc) {
    if (is_array($em_bcc)) {
      $em_bcc = safe_implode(',', $em_bcc);
    }
    $head_arr[] = "bcc: $em_bcc";
  }
  if ($headers) {
    $head_arr[] = $headers;
  }
  return mail($em_to, $subject, $content, safe_implode($crlf, $head_arr));
}

function phpmailer($em_to, $subject, $content, $headers)
{
  // phpmail('to@test.com', 'subject', $content, $headers);
  // alternative to mail() using phpmailer
  $em_to = clean_email($em_to);
  $em_fr = $em_cc = $em_bcc = $reply = '';
  $head_arr = safe_explode("\r\n", $headers);
  foreach ($head_arr as $head_itm) {
    $head = safe_explode(':', $head_itm);
    if (isset($head[1])) {
      $head[0] = strtolower(safe_trim($head[0]));
      $head[1] = safe_trim($head[1]);
      if ($head[0] == 'from') {
        $em_fr = $head[1];
      } elseif ($head[0] == 'reply-to') {
        $reply = $head[1];
      } elseif ($head[0] == 'cc') {
        $em_cc = $head[1];
      } elseif ($head[0] == 'bcc') {
        $em_bcc = $head[1];
      }
    }
  }
  return htmlmail($em_to, $em_fr, '', $subject, $content, '', '', $reply, $em_cc, $em_bcc);
}

function make_email($template_file, $em_to, $em_fr, $em_fn, $subject, $reply = null, $em_cc = null, $em_bcc = null, $file = null)
{
  global $globvars;
  extract($globvars, EXTR_SKIP);
  $em_to = clean_email($em_to);
  $em_fr = clean_email($em_fr);
  $reply = clean_email($reply);
  $em_cc = clean_email($em_cc);
  $em_bcc = clean_email($em_bcc);
  $globvars['crlf'] = $crlf = (substr_count(isvar($_SERVER['SERVER_SOFTWARE']), 'Microsoft')) ? "\r\n" : "\n";
  if (!file_exists($template_file)) {
    $template_file = '../' . $template_file;
  }
  if (!(is_file($template_file) && $em_to && $em_fr)) {
    return;
  }
  $contents = ob_include($template_file);
  $sections = array('style', 'html', 'text');
  foreach ($sections as $section) {
    $spos = strpos($contents, "<!-- {$section} start -->") + strlen("<!-- {$section} start -->");
    $slen = strpos($contents, "<!-- {$section} end -->") - $spos;
    ${$section} = substr($contents, $spos, $slen);
  }
  /*
  echo $style ;
  print_p("$em_to, $em_fr, $em_fn, $subject, $reply, $em_cc, $em_bcc, $file") ;
  print_pre(str_replace('>','&gt;',str_replace('<','&lt;',$style))) ;
  echo $html ;
  print_pre($text) ;
  */
  return htmlmail($em_to, $em_fr, $em_fn, $subject, $text, $html, $style, $reply, $em_cc, $em_bcc, $file);
}

function htmlmail($em_to, $em_fr, $em_fn, $subject, $text, $html = null, $style = null, $reply = null, $em_cc = null, $em_bcc = null, $file = null)
{
  // htmlmail('to@test.com', 'from@test.com', 'name', 'subject', $text, $html);
  // https://github.com/PHPMailer/PHPMailer/
  global $globvars;
  $return = false;
  if (file_exists($mpath = build_path(__DIR__, '../scripts/phpmailer6.inc.php'))) {
    require $mpath;
  } elseif (file_exists($mpath = build_path(__DIR__, '../scripts/phpmailer.inc.php'))) {
    require $mpath;
  }
  return $return;
}

// ----------------------------------------------------FORMS----------------------------------------------------------------

function iencode($in, $force = false)
{
  if (is_array($in) || ($in && ($force || !((substr($in, 0, 2) == '[[') && (substr($in, -2) == ']]'))))) {
    $in = '[[' . urlencode(base64_encode(json_encode($in))) . ']]';
  }
  return $in;
}

function idecode($in)
{
  if (!is_array($in)) {
    $in = urldecode($in);
    if ((substr($in, 0, 2) == '[[') && (substr($in, -2) == ']]') && !is_array($in = substr($in, 2, -2))) {
      $in = json_decode(base64_decode($in), true);
    }
  }
  return $in;
}

function ihide()
{
  // ihide('field1',$value1,'field2',$value2)
  $k = $out = '';
  $c = func_num_args();
  for ($i = 0; $i < $c; $i++) {
    if (!$k) {
      $k = func_get_arg($i);
    } else {
      $out .= '<input type="hidden" name="' . $k . '" value="' . iencode(func_get_arg($i)) . '">' . "\r\n";
      $k = '';
    }
  }
  print $out;
}

function idhide()
{
  // idhide('field1',$value1,'field2',$value2)
  $k = $out = '';
  $c = func_num_args();
  for ($i = 0; $i < $c; $i++) {
    if (!$k) {
      $k = func_get_arg($i);
    } else {
      $out .= '<input type="hidden" name="' . $k . '" id="' . $k . '" value="' . iencode(func_get_arg($i)) . '">' . "\r\n";
      $k = '';
    }
  }
  echo $out;
}

function ishide()
{
  // ihide('field1',$value1,'field2',$value2)
  $k = $out = '';
  for ($i = 0; $i < func_num_args(); $i++) {
    if (!$k) {
      $k = func_get_arg($i);
    } else {
      if (func_get_arg($i) && !is_array(func_get_arg($i))) {
        $out .= '<input type="hidden" name="' . $k . '" value="' . func_get_arg($i) . '">' . "\r\n";
      }
      $k = '';
    }
  }
  echo $out;
}

function optchk($value, $prev, $print = 1)
{
  $ok = 0;
  if (is_array($prev)) {
    if (in_array($value, $prev)) {
      $ok = 1;
    }
  } elseif (substr_count($prev, ',')) {
    if (in_array($value, safe_explode(",", $prev))) {
      $ok = 1;
    }
  } elseif ($value == $prev) {
    $ok = 1;
  }
  if ($ok) {
    $value .= chr(34) . ' checked=' . chr(34) . 'CHECKED';
  }
  if (!$print) {
    return $value;
  }
  echo $value;
}

function optsel($value, $prev, $print = 1)
{
  $ok = 0;
  if (is_array($prev)) {
    if (in_array($value, $prev)) {
      $ok = 1;
    }
  } elseif (substr_count($prev, ',')) {
    if (in_array($value, safe_explode(",", $prev))) {
      $ok = 1;
    }
  } elseif (substr_count($prev, '^')) {
    if (in_array($value, safe_explode("^", $prev))) {
      $ok = 1;
    }
  } elseif ($value == $prev) {
    $ok = 1;
  }
  if ($ok) {
    $value .= chr(34) . ' selected=' . chr(34) . 'SELECTED';
  }
  if (!$print) {
    return $value;
  }
  echo $value;
}

function sel_date($fname, $prev, $min, $max, $arr)
{
  $mths = array('', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
  $disp = $$arr;
  ?>
  <select name="<?= $fname; ?>">
    <?
    for ($n = $min; $n <= $max; $n++) {
      $c = $n;
      if ($arr) {
        $c = $disp[$n];
      }
      if ($n == $prev) {
    ?>
        <option value="<?= $n; ?>" selected="selected"><?= $c; ?></option>
      <?
      } else {
      ?>
        <option value="<?= $n; ?>"><?= $c; ?></option>
    <?
      }
    }
    ?>
  </select>
<?
}

function secur_image()
{
  // remember session_start() at top
  $scrpath = root_rel() . 'scripts/';
  $qs = session_name() . '=' . session_id() . '&amp;sid=';
  $onc = "document.getElementById('secur_image').src = '{$scrpath}securimage/securimage_show.php?{$qs}'  + Math.random() ; return false";
  $src = "{$scrpath}securimage/securimage_show.php?{$qs}" . md5(uniqid(time()));
  $alt = "Enter Verification Code. Click to reload if you can't read it";
  /* ?>
    <p><? */ ?>
  <span class="nobr"><input type="hidden" name="<?= session_name(); ?>" value="<?= session_id(); ?>">
    <a href="#" onclick="<?= $onc; ?>" title="<?= $alt; ?>"> <img id="secur_image" alt="<?= $alt; ?>" src="<?= $src; ?>" border="0" style="vertical-align:middle;"><b style="vertical-align:middle; line-height:30px; padding:0 4px">?</b></a></span>
<? /* ?></p><? */
}

function secur_set($class = '', $style = 'font-family:Arial; font-size:13px; color:#717171;')
{
  secur_image(); ?>
  <input type="text" name="secur_input" id="secur_input" size="8" maxlength="10" value="" style="<?= ($style) ? $style : ''; ?>" class="<?= ($class) ? $class : ''; ?>" placeholder="Enter Code">
  <?
}

function secur_check()
{
  // remember session_start() at top (method not used in new sites)
  if (!isset($GLOBALS['secur_check'])) {
    $scrpath = root_rel() . 'scripts/';
    @include_once($scrpath . 'securimage/securimage.php');
    $img = new Securimage();
    $secur_input = isset($_POST['secur_input']) ? $_POST['secur_input'] : '';
    $GLOBALS['secur_check'] = $img->check(strtoupper($secur_input));
  }
  return ($GLOBALS['secur_check']);
}

// ----------------------------------------------------FILES----------------------------------------------------------------

function build_path()
{
  // eg. build_path('/files/','/path/') = /files/path/
  // eg. build_path('/files/','file.txt') = /files/file.txt
  $path = $lastfile = '';
  for ($i = 0; $i < func_num_args(); $i++) {
    $k = func_get_arg($i);
    if ($k == 'lastfile') {
      $lastfile = 'y';
      break;
    }
    $k = str_replace(chr(92), '/', $k);
    // remove all // except after :
    $k = preg_replace('/([^:])(\/{2,})/', '$1/', $k);
    if ($k) {
      if ($i && (!substr_count($k, '://')) && $dn = substr_count($k, '../')) {
        // if not first part or http:// then traverse ../
        for ($n = 0; $n < $dn; $n++) {
          // remove end slash if exists
          $path = safe_rtrim($path, '/');
          // remove last folder if exists
          if (strrpos($path, '/')) {
            $path = substr($path, 0, strrpos($path, '/') + 1);
          } else {
            $path = '';
          }
        }
        // remove all ../ from this part
        $k = str_replace('../', '', $k);
      }
      // add slash and part to path
      if ($k) {
        if ($path) {
          $path .= '/';
        }
        $path .= $k;
      }
      // add trailing slash if not file (with a dot)
      if (!($lastfile || substr_count(str_replace('../', '', $k), '.'))) {
        $path .= '/';
      }
    }
  }
  // remove all // except after :
  $path = preg_replace('/([^:])(\/{2,})/', '$1/', $path);
  if ($lastfile && (substr($path, -1) == '/')) {
    $path = substr($path, 0, -1);
  }
  return $path;
}

function base_path($ssl = true)
{
  global $globvars;
  extract($globvars, EXTR_SKIP);
  $path = build_path($http_host, $php_path);
  for ($i = 0; $i < func_num_args(); $i++) {
    $k = func_get_arg($i);
    $path = build_path($path, $k);
  }
  $path = ($ssl == true ? 'https://' : 'http://') . $path;
  return ($path);
}

function get_files($file_path, $mkey = 0)
{
  // mkey = 1 to make array key be filename
  if (file_exists($file_path) && is_dir($file_path)) {
    $handle = opendir($file_path);
    if ($handle) {
      $files = array();
      while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != ".." && $file != "Thumbs.db") {
          if ($mkey) {
            $files[$file] = $file;
          } else {
            $files[] = $file;
          }
        }
      }
      closedir($handle);
      natcasesort($files);
      return ($files);
    }
  }
  return false;
}

function ftp_conlog()
{
  global $globvars;
  extract($globvars, EXTR_SKIP);
  if (isset($ftp_server) && isset($ftp_username) && isset($ftp_password) && $ftp_server && $ftp_username && $ftp_password) {
    if (!isset($ftp_port)) {
      $ftp_port = 21;
    }
    if (($connection = ftp_connect($ftp_server, $ftp_port)) && @ftp_login($connection, $ftp_username, $ftp_password)) {
      if ((isset($ftp_passive) && $ftp_passive == 'y')) {
        ftp_pasv($connection, true);
      }
      return $connection;
    }
  }
  return false;
}

function make_dir($file_path, $new_dir, $debug = 0)
{
  global $globvars;
  extract($globvars, EXTR_SKIP);
  if (isset($globvars['debug']) && !$debug) {
    $debug = $globvars['debug'];
  }
  $ret = false;
  if ((!$local_dev) && $connection = ftp_conlog()) {
    $basepath = str_replace($ftp_srvpath, '', basename($php_path));
    $file_fpath = str_replace('//', '/', build_path('/', $ftp_srvpath, $basepath, $file_path));
    if ($debug) {
      print_d("build_path: /, $ftp_srvpath, " . $basepath . ", $file_path", __LINE__, __FILE__);
      print_d("ftp_chdir: $file_fpath", __LINE__, __FILE__);
    }
    ftp_chdir($connection, $file_fpath);
    if (ftp_mkdir($connection, $new_dir)) {
      ftp_site($connection, "chmod 0755 $new_dir");
      $ret = true;
    }
    ftp_close($connection);
  }
  if ((!$ret) && @mkdir(build_path($file_path, $new_dir))) {
    $ret = true;
  }
  return $ret;
}

function make_path($file_path)
{
  global $globvars;
  extract($globvars, EXTR_SKIP);
  $dirs = explode('/', $file_path);
  $file_path = '';
  foreach ($dirs as $key => $dir) {
    $file_path = build_path($file_path, $dir);
    if ($dir && ($file_path != '../') && isset($dirs[$key + 1]) && $dirs[$key + 1]) {
      $next_path = build_path($file_path, $dirs[$key + 1]);
      if (!file_exists($next_path)) {
        make_dir($file_path, $dirs[$key + 1]);
      }
    }
  }
}

function del_file($file_path, $del_file, $debug = 0)
{
  global $globvars;
  extract($globvars, EXTR_SKIP);
  if (isset($globvars['debug']) && !$debug) {
    $debug = $globvars['debug'];
  }
  $ret = false;
  if ($del_file) {
    if ((!$local_dev) && $connection = ftp_conlog()) {
      $basepath = str_replace($ftp_srvpath, '', basename($php_path));
      $file_fpath = str_replace('//', '/', build_path('/', $ftp_srvpath, $basepath, $file_path));
      if ($debug) {
        print_d("build_path: /, $ftp_srvpath, " . $basepath . ", $file_path", __LINE__, __FILE__);
        print_d("ftp_chdir: $file_fpath", __LINE__, __FILE__);
      }
      ftp_chdir($connection, $file_fpath);
      ftp_delete($connection, $del_file);
      ftp_close($connection);
      $ret = true;
    }
    if ((!$ret) && ($del_path = build_path($file_path, $del_file)) && file_exists($del_path) && @unlink($del_path)) {
      $ret = true;
    }
  }
  return $ret;
}

function set_chmod($file_path, $chnum, $debug = 0)
{
  global $globvars;
  extract($globvars, EXTR_SKIP);
  if (isset($globvars['debug']) && !$debug) {
    $debug = $globvars['debug'];
  }
  $ret = false;
  if ((!$local_dev) && $connection = ftp_conlog()) {
    $$basepath = str_replace($ftp_srvpath, '', basename($php_path));
    $file_fpath = str_replace('//', '/', build_path('/', $ftp_srvpath, $basepath, $file_path));
    if ($debug) {
      print_d("build_path: /, $ftp_srvpath, " . $basepath . ", $file_path", __LINE__, __FILE__);
      print_d("ftp_chdir: $file_fpath", __LINE__, __FILE__);
    }
    ftp_site($connection, "chmod $chnum $file_fpath");
    ftp_close($connection);
    $ret = true;
  }
  return $ret;
}

function upload_move($field, $ftype, $fpath, $fname = '', $preg = '', $max_w = 0, $max_h = 0, $qual = 90, $wm_file = '', $wm_x = 0, $wm_y = 0)
{
  global $globvars;
  // upload_move('upload','image','images','12345')
  // field : just form field name
  // ftype : image/doc or blank for any type
  // fpath : final folder from root
  // fname : actual if blank, adds ext if needed
  // preg  : permitted characters
  $out = array('res' => '', 'fname' => '');
  if ($field && isset($_FILES[$field]['tmp_name'])) {
    $tmp_name = $_FILES[$field]['tmp_name'];
    if (!$preg) {
      $preg = '/[^A-Za-z0-9_\.]/';
    }
    $chk = upload_check($field, $ftype);
    if (!$chk['ufail']) {
      if (!$fname) {
        $fname = $chk['uname'];
      } elseif (!substr_count($fname, '.')) {
        $fname .= '.' . $chk['ext'];
      }
      // cleanup
      $src_im = $dst_im = $rotate = $src_do = '';
      if (function_exists('exif_imagetype') && (exif_imagetype($tmp_name) == 2) && ($exif = exif_read_data($tmp_name)) && isset($exif['Orientation']) && ($exif['Orientation'] != 1)) {
        $rotate = 'y';
      }
      if ($max_w || $max_h || $rotate || $wm_file) {
        $src_im = $src_w = $src_h = 0;
        if (mime_check($tmp_name, 'image')) {
          list($src_w, $src_h, $itype) = @getimagesize($tmp_name);
          switch ($itype) {
            case 1:
              $src_im = imagecreatefromgif($tmp_name);
              break;
            case 2:
              $src_im = imagecreatefromjpeg($tmp_name);
              break;
            case 3:
              $src_im = imagecreatefrompng($tmp_name);
              break;
            case 6:
              $src_im = imagecreatefrombmp($tmp_name);
              break;
            case 18:
              $src_im = imagecreatefromwebp($tmp_name);
              break;
          }
        }
      }
      if ($src_im && $src_w && $src_h) {
        // max size
        if ($max_w || $max_h) {
          $dst_w = $src_w;
          $dst_h = $src_h;
          if ($max_w && $dst_w > $max_w) {
            $dst_w = $max_w;
            $dst_h = $dst_h * $dst_w / $src_w;
          }
          if ($max_h && $dst_h > $max_h) {
            $dst_h = $max_h;
            $dst_w = $dst_w * $dst_h / $src_h;
          }
          if (($dst_w != $src_w) && ($dst_im = imagecreatetruecolor($dst_w, $dst_h))) {
            $bg = imagecolorallocate($dst_im, 255, 255, 255);
            imagecolortransparent($dst_im, $bg);
            imagefilledrectangle($dst_im, 0, 0, $dst_w, $dst_h, $bg);
            if (imagecopyresampled($dst_im, $src_im, 0, 0, 0, 0, floor($dst_w), floor($dst_h), floor($src_w), floor($src_h))) {
              $src_im = $dst_im;
              $src_do = 'y';
            }
          }
        }
        // rotate
        if ($rotate) {
          $deg = 0;
          switch ($exif['Orientation']) {
            case 3:
              $deg = 180;
              break;
            case 6:
              $deg = 270;
              break;
            case 8:
              $deg = 90;
              break;
          }
          if ($deg) {
            $src_im = imagerotate($src_im, $deg, 0);
            $src_do = 'y';
          }
        }
        // watermark
        if ($wm_file) {
          $wat_im = '';
          if (mime_check($wm_file, 'image')) {
            list($wm_w, $wm_h, $wtype) = @getimagesize($wm_file);
            switch ($wtype) {
              case 1:
                $wat_im = imagecreatefromgif($wm_file);
                break;
              case 2:
                $wat_im = imagecreatefromjpeg($wm_file);
                break;
              case 3:
                $wat_im = imagecreatefrompng($wm_file);
                break;
              case 6:
                $wat_im = imagecreatefrombmp($wm_file);
                break;
              case 18:
                $wat_im = imagecreatefromwebp($wm_file);
                break;
            }
          }
          if ($wat_im) {
            $px = $wm_x;
            $py = $wm_y;
            if ($wm_x < 0) {
              $px = $src_w - $wm_w + $wm_x;
            }
            if ($wm_y < 0) {
              $py = $src_h - $wm_h + $wm_y;
            }
            if (imagecopy($src_im, $wat_im, $px, $py, 0, 0, $wm_w, $wm_h)) {
              $src_do = 'y';
            }
          }
        }
        // save changed image
        if ($src_do) {
          switch ($itype) {
            case 1:
              imagegif($src_im, $tmp_name);
              break;
            case 2:
              imagejpeg($src_im, $tmp_name, $qual);
              break;
            case 3:
              imagepng($src_im, $tmp_name, round((100 - $qual) / 10, 0));
              break;
            case 6:
              imagejpeg($src_im, $tmp_name);
              break;
            case 18:
              imagewebp($src_im, $tmp_name);
              break;
          }
        }
        if ($dst_im && is_resource($dst_im)) {
          imagedestroy($dst_im);
        }
        if ($src_im && is_resource($src_im)) {
          imagedestroy($src_im);
        }
      }
      if ($chk = upload_file($tmp_name, $fpath, $fname, $preg, $globvars['debug'])) {
        $out['res'] = 'ok';
        $out['fname'] = $chk;
      } else {
        $out['res'] = 'File move failed';
      }
    } else {
      $out['res'] = 'File upload failed (' . $chk['ufail'] . ')';
    }
  } else {
    $out['res'] = 'File upload no data';
  }
  if (isset($globvars['debug']) && $globvars['debug']) {
    print_d('Upload Move', __LINE__, __FILE__);
    print_arr($out, 'Upload Move');
  }
  return $out;
}

function upload_check($field, $ftype = '')
{
  // field : just form field name (can now be field[0] etc. for multiple selector)
  // ftype : image/doc or blank for any type
  global $globvars;
  $file_msize = isset($globvars['file_msize']) ? $globvars['file_msize'] : 0;
  $out = array('array' => '', 'name' => '', 'type' => '', 'tmp_name' => '', 'error' => '', 'size' => '', 'uname' => '', 'ext' => '', 'ftype' => '', 'mtype' => '', 'ufail' => '', 'msg' => '', 'fld' => false, 'sel' => false, 'res' => false);

  $farr = $ftyp = '';
  if (substr_count($field, '[') && substr_count($field, ']')) {
    // for multiple selector, globvars needs to have been called before to create array structure which is different to $_FILES
    $fname = substr($field, 0, strpos($field, '['));
    $fanum = intval(substr($field, strpos($field, '[') + 1, -1));
    if (isset($_FILES[$fname]) && isset($globvars[$fname][$fanum])) {
      $farr = $globvars[$fname][$fanum];
      $ftyp = 'm';
    }
  } elseif (isset($_FILES[$field])) {
    $farr = $_FILES[$field];
    $ftyp = 's';
  }

  if ($farr) {
    $out['array'] = $farr;
    $out['fld'] = true;
    $f_arr = $farr;
    $out = array_merge($out, $f_arr);
    $out['msg'] = ($farr['name'] ? "File ({$farr['name']}) " : 'File ');
    $err = array(
      0 => 'uploaded',
      1 => 'exceeded php setting size',
      2 => 'exceeded html setting size',
      3 => 'only partially uploaded',
      4 => 'file not uploaded',
      6 => 'invalid temp folder',
      7 => 'failed to write file',
      8 => 'upload blocked by php'
    );
    if ($farr['error'] != 4) {
      // file selected
      $out['sel'] = true;
      if ((!$farr['error']) && is_uploaded_file($farr['tmp_name'])) {
        if ($f_arr['tmp_name'] && ($f_arr['size'] < $file_msize) && !$f_arr['error']) {
          $out['uname'] = $f_arr['tmp_name'];
          if ($f_arr['name']) {
            $out['uname'] = $f_arr['name'];
            if (substr_count('/', $f_arr['name'])) {
              $start = strrpos($f_arr['name'], '/') + 1;
              $out['uname'] = substr($f_arr['name'], $start);
            }
          }
          $out['uname'] = strtolower(preg_replace('/[^A-Za-z0-9_\.]/', '_', $out['uname']));
          if ($out['uname']) {
            if ($chk = isfile($out['uname'], $ftype)) {
              $out['ftype'] = $chk['ftype'];
              $out['ext'] = $chk['ext'];
              if (function_exists('finfo')) {
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $out['mtype'] = $finfo->buffer(file_get_contents($f_arr['tmp_name']));
                if ($out['mtype'] != $f_arr['type']) {
                  $out['ufail'] = 'invalid type of file';
                }
              } elseif ($ftype == 'image' && !substr_count($f_arr['type'], 'image')) {
                $out['ufail'] = 'file is not an image';
              }
            } else {
              $out['ufail'] = 'invalid file type';
            }
          } else {
            $out['ufail'] = 'invalid file name';
          }
        } else {
          $out['ufail'] = 'exceeds ' . disp_filesize($file_msize, 0);
        }
      } else {
        $out['ufail'] = isset($err[$farr['error']]) ? $err[$farr['error']] : 'unspecified error';
      }
    } else {
      $out['ufail'] = 'no file selected';
    }
  } else {
    $out['ufail'] = 'field is not a file';
  }

  $msg = "{$out['msg']} " . ($out['ufail'] ? $out['ufail'] : $err[0]);
  if ($ftyp == 'm') {
    $_FILES[$fname][$fanum]['msg'] = $msg;
  } elseif ($ftyp == 's') {
    $_FILES[$field]['msg'] = $msg;
  }

  if (!$out['ufail']) {
    $out['res'] = true;
  } // success
  return $out;
}

function upload_file($tmp_name, $file_path, $fname, $preg = '', $debug = 0)
{
  // use $_FILES['field']['tmp_name'] not $globvars['field']['tmp_name']
  global $globvars;
  extract($globvars, EXTR_SKIP);
  if (isset($globvars['debug']) && !$debug) {
    $debug = $globvars['debug'];
  }
  $chk = false;
  if (!$preg) {
    $preg = '/[^A-Za-z0-9_\-\.]/';
  }
  $fname = strtolower(preg_replace($preg, '-', $fname));
  // $new_fpath = build_path($file_path,$fname);
  // if($globvars['local_dev']) {
  if (file_exists($file_path)) {
    $file_fpath = build_path($file_path, $fname);
    if ($debug) {
      print_d("Move Local: {$tmp_name}, {$file_fpath}", __LINE__, __FILE__);
    }
    $chk = move_uploaded_file($tmp_name, $file_fpath);
  }
  // }
  // else {
  //   if(file_exists($temp_folder)) {
  //     $temp_fpath = build_path($temp_folder,$fname);
  //     if(move_uploaded_file($tmp_name,$temp_fpath)) {
  //       // try to move to temp_folder first
  //       if($debug) { print_d("Move Uploaded: {$tmp_name}, {$temp_fpath}",__LINE__,__FILE__); }
  //       if($chk = move_temp($file_path,$fname,'',$debug)) {
  //         if(file_exists($temp_fpath)) {
  //           @unlink($temp_fpath);
  //         }
  //       }
  //     }
  //     else {
  //       // move direct from tmp_file
  //       if($debug) { print_d("Move Temp: {$file_path}, {$fname}, {$tmp_name}",__LINE__,__FILE__); }
  //       $chk = move_temp($file_path,$fname,$tmp_name,$debug);
  //     }
  //   }
  // }
  $out = $chk ? $fname : false;
  if ($debug) {
    print_d("Move Result: {$out}", __LINE__, __FILE__);
  }
  return ($out);
}

function upload_isup($field)
{
  // returns only true/false
  $chk = upload_check($field);
  return $chk['ufail'] ? false : true;
}

function move_temp($file_path, $fname, $tmp_name = '', $debug = 0)
{
  global $globvars;
  extract($globvars, EXTR_SKIP);
  // use tmp_name if set else file in temp_folder
  if (isset($globvars['debug']) && !$debug) {
    $debug = $globvars['debug'];
  }
  $temp_fpath = $tmp_name ? $tmp_name : build_path($temp_folder, $fname);
  $new_fpath = build_path($file_path, $fname);
  $ret = false;
  if (!$local_dev) {
    // @chmod($temp_fpath, 0777);
    if ($connection = ftp_conlog()) {
      $file_fpath = str_replace('//', '/', build_path('/', $ftp_srvpath, basename($php_path), $file_path));
      if ($debug) {
        print_d("build_path: /, $ftp_srvpath, " . basename($php_path) . ", $file_path", __LINE__, __FILE__);
        print_d("ftp_chdir: $file_fpath", __LINE__, __FILE__);
      }
      ftp_chdir($connection, $file_fpath);
      if (file_exists($new_fpath)) {
        if ($debug) {
          print_d("del_file: $file_path, $fname", __LINE__, __FILE__);
        }
        del_file($file_path, $fname);
      }
      if ($debug) {
        print_d("ftp_put: $fname, $temp_fpath", __LINE__, __FILE__);
      }
      if (ftp_put($connection, $fname, $temp_fpath, FTP_BINARY)) {
        if (file_exists($temp_fpath)) {
          @unlink($temp_fpath);
        }
        $ret = true;
      }
      ftp_close($connection);
    }
  }
  if (!$ret) {
    if (file_exists($new_fpath)) {
      @unlink($new_fpath);
    }
    if (rename($temp_fpath, $new_fpath)) {
      $ret = true;
    }
  }
  return $ret;
}

function copy_file($old_path, $new_path, $fname, $debug = 0)
{
  global $globvars;
  extract($globvars, EXTR_SKIP);
  if (isset($globvars['debug']) && !$debug) {
    $debug = $globvars['debug'];
  }
  $old_fpath = build_path($old_path, $fname);
  $new_fpath = build_path($new_path, $fname);
  $ret = false;
  if (file_exists($old_fpath)) {
    if (file_exists($new_fpath)) {
      $dot = strrpos($fname, '.');
      $fname = substr($fname, 0, $dot) . '_' . time() . substr($fname, $dot);
      $new_fpath = build_path($new_path, $fname);
    }
    if (!$local_dev) {
      @chmod($oldf_path, 0777);
      if ($connection = ftp_conlog()) {
        $new_path = str_replace('//', '/', build_path('/', $ftp_srvpath, basename($php_path), $new_path));
        if ($debug) {
          print_d("new_path: /, $ftp_srvpath, " . basename($php_path) . ", $new_path", __LINE__, __FILE__);
          print_d("ftp_chdir: $new_path", __LINE__, __FILE__);
        }
        ftp_chdir($connection, $new_path);
        if ($debug) {
          print_d("ftp_put: $fname, $old_fpath", __LINE__, __FILE__);
        }
        ftp_put($connection, $fname, $old_fpath, FTP_BINARY);
        ftp_close($connection);
        $ret = $fname;
      }
    }
    if ((!$ret) && copy($old_fpath, $new_fpath)) {
      $ret = $fname;
    }
  }
  return $ret;
}

function checkfile($in)
{
  global $globvars;
  $msg = '';
  if (isset($in['name']) && $in['name']) {
    if (!in_array(pathinfo($in['name'], PATHINFO_EXTENSION), $file_types)) {
      $msg = "File ({$in}) invalid type<br>";
    } elseif ($in['size'] > $file_msize) {
      $msg = "File ({$in}) too large<br>";
    } elseif ($in['error']) {
      $msg = "Upload error ({$in['error']})<br>";
    }
  }
  if ($msg) {
    $globvars['checkfile_err'] = $msg;
    return false;
  }
  return true;
}

function makefile($file_path, $fname, $template, $arr_vars)
{
  global $globvars;
  extract($globvars, EXTR_SKIP);
  if (!($template && file_exists($template))) {
    return "File ({$fname}) missing template";
  }
  if (substr_count($fname, '?')) {
    $fname = substr($fname, 0, strpos($fname, '?'));
  }
  if (!($fname && preg_match('/^[A-Za-z0-9-_\.]{1,255}\.[A-Za-z]{3,4}$/', $fname) && in_array(substr($fname, strrpos($fname, '.') + 1), array('php', 'inc', 'txt', 'html', 'htm')))) {
    return "File ({$fname}) invalid name";
  }
  $temp_fpath = build_path($temp_folder, $fname);
  $mfok = 0;
  if ($write = @fopen($temp_fpath, "w")) {
    if ($read = @fopen($template, "r")) {
      while (!feof($read)) {
        $mfok = 1;
        $buffer = fgets($read, 4096);
        if (is_array($arr_vars) && count($arr_vars)) {
          foreach ($arr_vars as $key => $val) {
            $buffer = str_replace("<!-- $key -->", $val, $buffer);
          }
        }
        fwrite($write, $buffer);
      }
      fclose($read);
    }
    fclose($write);
  }
  if ($mfok) {
    $file_fpath = build_path($file_path, $fname);
    if (file_exists($file_fpath)) {
      $mfok = 0;
      if ($handle = @fopen($file_fpath, "r")) {
        while (!feof($handle)) {
          $buffer = fgets($handle, 4096);
          if (substr_count($buffer, $template)) {
            $mfok = 1;
            break;
          }
        }
        fclose($handle);
      }
    }
  } else {
    return "File temp failure ({$temp_fpath})";
  }
  $ret = '';
  if ($mfok) {
    if (move_temp($file_path, $fname)) {
      $ret = "File ({$fname}) written";
    } else {
      $ret = "File ({$fname}) move failure";
    }
  } else {
    $ret = "File ({$fname}) already exists";
  }
  if (file_exists($temp_fpath)) {
    del_file($temp_folder, $fname);
  }
  return $ret;
}

function get_orphans($path, $array)
{
  // $array[1] = array('table' = $table1, 'fields' => $fields1);
  // $array[2] = array('table' = $table2, 'fields' => $fields2);
  // fields comma string if more than one
  if (!is_array($array)) {
    return ("Invalid Main Array");
  }
  if (!$arr = get_files($path, 1)) {
    return ("No files in {$path}");
  }
  print("Path: $path<br>");
  $s = count($arr);
  $d = 0;
  foreach ($array as $tab) {
    if (!isset($tab['table']) && isset($tab['fields'])) {
      return ("Invalid Table Array");
    }
    $fields = safe_explode(',', $tab['fields']);
    $string = str_replace(',', ' , ', $tab['fields']);
    $string = "SELECT $string FROM `{$tab['table']}`";
    print("Query: $string<br>");
    $query = my_query($string);
    while ($a_row = my_array($query)) {
      foreach ($fields as $field) {
        if (isset($arr[$a_row[$field]])) {
          unset($arr[$a_row[$field]]);
        }
      }
    }
    my_free($query);
  }
  $f = count($arr);
  $r = $s - $f;
  print("Found: $s<br>Delete: $f<br>Leave: $r<br><br>");
  foreach ($arr as $file) {
    print("rm $file<br>");
  }
}

function dirToArray($dir, $root = '', $paths = [])
{
  global $paths;
  $result = array();
  $cdir = scandir($dir);
  if (!$root) {
    $root = $dir;
  }
  $c = 0;
  foreach ($cdir as $key => $value) {
    if (!in_array($value, array(".", "..", "Thumbs.db"))) {
      if (is_dir($dir . '/' . $value)) {
        $result[$value] = dirToArray($dir . '/' . $value, $root, $paths);
      } else {
        $path = substr(str_replace($root, '', $dir), 1);
        if ($path && !$c++) {
          $paths[] = $path;
        }
        $result[] = $value;
      }
    }
  }
  return $result;
}

// ----------------------------------------------------IMAGES---------------------------------------------------------------

function get_image($in, $mw = 0, $mh = 0)
{
  if (file_exists($in) && isimg($in) && is_file($in) && mime_check($in, 'image') && ($arr = @getimagesize($in))) {
    $img_types = array(1 => 'gif', 2 => 'jpg', 3 => 'png', 6 => 'bmp');
    $arr[2] = isvar($img_types[$arr[2]], '');
    $channels = (isset($arr['channels'])) ? $arr['channels'] : '';
    if ($mw && ($arr[0] > $mw)) {
      $arr[1] = floor($arr[1] * $mw / $arr[0]);
      $arr[0] = $mw;
    }
    if ($mh && ($arr[1] > $mh)) {
      $arr[0] = floor($arr[0] * $mh / $arr[1]);
      $arr[1] = $mh;
    }
    $arr[3] = 'width="' . $arr[0] . '" height="' . $arr[1] . '"';
    return array('src' => str_ireplace(' ', '%20', $in), 'width' => $arr[0], 'height' => $arr[1], 'type' => $arr[2], 'str' => $arr[3], 'bits' => isset($arr['bits']) ? $arr['bits'] : 0, 'channels' => $channels, 'mime' => $arr['mime'], 'pathinfo' => pathinfo($in), 'filemtime' => filemtime($in));
  }
  return false;
}

function image_cls($src = '', $alt = '', $title = '', $did = '', $dcls = '', $iid = '', $icls = '', $iw = '', $ih = '', $isty = '')
{
  if ($src) {
    if ((!($iw && $ih)) && $img = get_image($src)) {
      $iw = $img['width'];
      $ih = $img['height'];
      $src = $img['src'];
    }
    if ($src && $iw && $ih) {
      $dsty = "position:relative; height:0; overflow:hidden; padding-top:calc({$ih} / {$iw} * 100%);";
      $isty = "position:absolute; top:0; left:0; width:100%; height:100%;{$isty}";
  ?><div id="<?= $did ?>" class="<?= $dcls ?>" style="<?= $dsty ?>"><img src="<?= $src ?>" alt="<?= $alt ?>" title="<?= $title ?>" id="<?= $iid ?>" class="<?= $icls ?>" style="<?= $isty ?>"></div><?
                                                                                                                                                                                                      }
                                                                                                                                                                                                    }
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function video_cls($src = '', $did = '', $dcls = '', $vid = '', $vcls = '', $vp = '56.25', $type = 'video/mp4', $opts = 'autoplay loop muted')
                                                                                                                                                                                                  {
                                                                                                                                                                                                    if ($src && $vp) {
                                                                                                                                                                                                      $dsty = "position:relative; height:0; overflow:hidden; padding-top:calc({$vp}%);";
                                                                                                                                                                                                      $vsty = "position:absolute; top:0; left:0; width:100%; height:100%;";
                                                                                                                                                                                                        ?>
    <div id="<?= $did ?>" class="<?= $dcls ?>" style="<?= $dsty ?>">
      <video id="<?= $vid ?>" class="<?= $vcls ?>" style="<?= $vsty ?>" <?= $opts ?>>
        <source src="<?= $src ?>" type="<?= $type ?>">
      </video>
    </div>
  <?
                                                                                                                                                                                                    }
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function mime_check($in, $match)
                                                                                                                                                                                                  {
                                                                                                                                                                                                    if (function_exists('mime_content_type')) {
                                                                                                                                                                                                      $mimetype = mime_content_type($in);
                                                                                                                                                                                                      return substr_count($mimetype, $match) ? true : false;
                                                                                                                                                                                                    } elseif (function_exists('finfo_open')) {
                                                                                                                                                                                                      $finfo = finfo_open(FILEINFO_MIME);
                                                                                                                                                                                                      $mimetype = finfo_file($finfo, $in);
                                                                                                                                                                                                      finfo_close($finfo);
                                                                                                                                                                                                      return substr_count($mimetype, $match) ? true : false;
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                      // default true if no mime functions
                                                                                                                                                                                                      return true;
                                                                                                                                                                                                    }
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function make_image($orig_path, $orig_file, $new_path, $suffix, $width, $height, $opt, $qual = 90, $del = 0, $forceopt = false, $errorhide = false)
                                                                                                                                                                                                  {
                                                                                                                                                                                                    // $opt = m(ax),e(xact),c(rop),p(ad),f(it) - ec/cc to centre crop
                                                                                                                                                                                                    // eg. make_image('../upload/','image.jpg','../images/','_1m2',800,600,'m',75,0)
                                                                                                                                                                                                    // echo "$orig_path, $orig_file, $new_path, $suffix, $width, $height, $opt, $qual, $del";
                                                                                                                                                                                                    global $globvars;
                                                                                                                                                                                                    $globvars['make_errors'] = [];

                                                                                                                                                                                                    if (!substr_count('meccpf', $opt)) {
                                                                                                                                                                                                      return $orig_file;
                                                                                                                                                                                                    }
                                                                                                                                                                                                    global $globvars;
                                                                                                                                                                                                    extract($globvars, EXTR_SKIP);
                                                                                                                                                                                                    if (!($orig_file && file_exists($orig_fpath = build_path($orig_path, $orig_file)))) {
                                                                                                                                                                                                      $globvars['make_errors'][] = "Make ({$orig_file}) not found";
                                                                                                                                                                                                      if (!$errorhide) {
                                                                                                                                                                                                        print_arr($globvars['make_errors'], 'make_errors');
                                                                                                                                                                                                      }
                                                                                                                                                                                                      return false;
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $file_max = isset($file_mmake) ? $file_mmake : (isset($file_msize) ? $file_msize : 10000);
                                                                                                                                                                                                    if (($osize = filesize($orig_fpath)) > $file_max) {
                                                                                                                                                                                                      $globvars['make_errors'][] = "Make ({$orig_path}/{$orig_file}) over " . disp_filesize($file_max, 0) . ' limit';
                                                                                                                                                                                                      if (!$errorhide) {
                                                                                                                                                                                                        print_arr($globvars['make_errors'], 'make_errors');
                                                                                                                                                                                                      }
                                                                                                                                                                                                      return false;
                                                                                                                                                                                                    }

                                                                                                                                                                                                    // file type and sizes
                                                                                                                                                                                                    $src_w = $src_h = 0;
                                                                                                                                                                                                    if (mime_check($orig_fpath, 'image')) {
                                                                                                                                                                                                      list($src_w, $src_h, $itype) = @getimagesize($orig_fpath);
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if (!($src_w && $src_h)) {
                                                                                                                                                                                                      $globvars['make_errors'][] = "Make ({$orig_path}/{$orig_file}) invalid source dimensions";
                                                                                                                                                                                                      if (!$errorhide) {
                                                                                                                                                                                                        print_arr($globvars['make_errors'], 'make_errors');
                                                                                                                                                                                                      }
                                                                                                                                                                                                      return false;
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if ((!$width) && (!$height)) {
                                                                                                                                                                                                      return $orig_file; // don't make file if both are zero
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if (($width >= $src_w) && ($height >= $src_h) && ($orig_path == $new_path) && !$forceopt) {
                                                                                                                                                                                                      return $orig_file; // don't make file unless resize needed (or $forceopt is true eg. on first upload)
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $dst_w = $src_w;
                                                                                                                                                                                                    $dst_h = $src_h;
                                                                                                                                                                                                    $dst_x = $dst_y = $src_x = $src_y = $dst_m = 0;
                                                                                                                                                                                                    $src_p = $src_w / $src_h;
                                                                                                                                                                                                    if ($opt == 'f') {
                                                                                                                                                                                                      if ($width && $height) {
                                                                                                                                                                                                        // stretch to fixed result
                                                                                                                                                                                                        $dst_w = $width;
                                                                                                                                                                                                        $dst_h = $height;
                                                                                                                                                                                                      } elseif ($width) {
                                                                                                                                                                                                        // resize in proportion to width
                                                                                                                                                                                                        $dst_w = $width;
                                                                                                                                                                                                        $dst_h = floor($width / $src_p);
                                                                                                                                                                                                      } elseif ($height) {
                                                                                                                                                                                                        // resize in proportion to height
                                                                                                                                                                                                        $dst_h = $height;
                                                                                                                                                                                                        $dst_w = floor($height * $src_p);
                                                                                                                                                                                                      }
                                                                                                                                                                                                    } elseif ($width && $height) {
                                                                                                                                                                                                      $dst_p = $width / $height;
                                                                                                                                                                                                      if (($opt == 'm') || ($opt == 'p')) {
                                                                                                                                                                                                        // resizes within max params
                                                                                                                                                                                                        if ($dst_w > $width) {
                                                                                                                                                                                                          $dst_w = $width;
                                                                                                                                                                                                          $dst_h = floor($width / $src_p);
                                                                                                                                                                                                        }
                                                                                                                                                                                                        if ($dst_h > $height) {
                                                                                                                                                                                                          $dst_h = $height;
                                                                                                                                                                                                          $dst_w = floor($height * $src_p);
                                                                                                                                                                                                        }
                                                                                                                                                                                                        if ($opt == 'p') {
                                                                                                                                                                                                          // pads to max params
                                                                                                                                                                                                          if ($dst_w < $width) {
                                                                                                                                                                                                            $dst_x = ($width - $dst_w) / 2;
                                                                                                                                                                                                            $dst_m = 2;
                                                                                                                                                                                                          }
                                                                                                                                                                                                          if ($dst_h < $height) {
                                                                                                                                                                                                            $dst_y = ($height - $dst_h) / 2;
                                                                                                                                                                                                            $dst_m = 2;
                                                                                                                                                                                                          }
                                                                                                                                                                                                        }
                                                                                                                                                                                                      } elseif ($opt == 'e' || $opt == 'ec') {
                                                                                                                                                                                                        // create to exact size with crop if necessary
                                                                                                                                                                                                        if ($dst_p < $src_p) {
                                                                                                                                                                                                          // crop source width
                                                                                                                                                                                                          $src_w = floor($src_w * $dst_p / $src_p);
                                                                                                                                                                                                        } else {
                                                                                                                                                                                                          // crop source height
                                                                                                                                                                                                          $src_h = floor($src_h / $dst_p * $src_p);
                                                                                                                                                                                                        }
                                                                                                                                                                                                        if ($opt == 'ec') {
                                                                                                                                                                                                          // centre crop
                                                                                                                                                                                                          if ($dst_w > $src_w) {
                                                                                                                                                                                                            $src_x = floor(($dst_w - $src_w) / 2);
                                                                                                                                                                                                          }
                                                                                                                                                                                                          if ($dst_h > $src_h) {
                                                                                                                                                                                                            $src_y = floor(($dst_h - $src_h) / 2);
                                                                                                                                                                                                          }
                                                                                                                                                                                                        }
                                                                                                                                                                                                        $dst_w = $width;
                                                                                                                                                                                                        $dst_h = $height;
                                                                                                                                                                                                      } elseif ($opt == 'c' || $opt == 'cc') {
                                                                                                                                                                                                        // just crop to dimensions without resizing
                                                                                                                                                                                                        if ($opt == 'cc') {
                                                                                                                                                                                                          // centre crop
                                                                                                                                                                                                          if ($dst_w > $width) {
                                                                                                                                                                                                            $src_x = floor(($dst_w - $width) / 2);
                                                                                                                                                                                                          }
                                                                                                                                                                                                          if ($dst_h > $height) {
                                                                                                                                                                                                            $src_y = floor(($dst_h - $height) / 2);
                                                                                                                                                                                                          }
                                                                                                                                                                                                        }
                                                                                                                                                                                                        $dst_w = $src_w = $width;
                                                                                                                                                                                                        $dst_h = $src_h = $height;
                                                                                                                                                                                                      }
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if (!($dst_w && $dst_h)) {
                                                                                                                                                                                                      $globvars['make_errors'][] = "Make ({$orig_path}/{$orig_file}) invalid create dimensions";
                                                                                                                                                                                                      if (!$errorhide) {
                                                                                                                                                                                                        print_arr($globvars['make_errors'], 'make_errors');
                                                                                                                                                                                                      }
                                                                                                                                                                                                      return false;
                                                                                                                                                                                                    }

                                                                                                                                                                                                    $dot = strrpos($orig_file, '.');
                                                                                                                                                                                                    $new_file = substr($orig_file, 0, $dot) . $suffix . substr($orig_file, $dot);
                                                                                                                                                                                                    if ($itype == 6) {
                                                                                                                                                                                                      $new_file = str_replace('.bmp', '.bmp.jpg', $new_file);
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $temp_fpath = build_path($temp_folder, $new_file);
                                                                                                                                                                                                    $mke_w = $dst_w + ($dst_x * $dst_m);
                                                                                                                                                                                                    $mke_h = $dst_h + ($dst_y * $dst_m);
                                                                                                                                                                                                    // print_p("$mke_w, $mke_h") ;

                                                                                                                                                                                                    // start make image
                                                                                                                                                                                                    $id = '';
                                                                                                                                                                                                    if (isset($globvars['db_imakelog']) && $globvars['db_imakelog']) {
                                                                                                                                                                                                      // check last entry if stuck on START
                                                                                                                                                                                                      $string = "SELECT * FROM `{$globvars['db_imakelog']}` WHERE
      `opath` = '$orig_path' AND
      `ofile` = '$orig_file' AND
      `osize` = '$osize' AND
      `owidth` = '$src_w' AND
      `oheight` = '$src_h'
      ORDER BY `id` DESC limit 1";
                                                                                                                                                                                                      $query = my_query($string);
                                                                                                                                                                                                      if (my_rows($query)) {
                                                                                                                                                                                                        $a_row = my_array($query);
                                                                                                                                                                                                        if ($a_row['status'] == 'START') {
                                                                                                                                                                                                          $globvars['make_errors'][] = "Make ({$orig_path}/{$orig_file}) failed previously";
                                                                                                                                                                                                          if (!$errorhide) {
                                                                                                                                                                                                            print_arr($globvars['make_errors'], 'make_errors');
                                                                                                                                                                                                          }
                                                                                                                                                                                                          return false;
                                                                                                                                                                                                        }
                                                                                                                                                                                                      }
                                                                                                                                                                                                      my_free($query);
                                                                                                                                                                                                      // create make log entry
                                                                                                                                                                                                      $string = "insert into `{$globvars['db_imakelog']}` set
      `datetime` = NOW(),
      `opath` = '$orig_path',
      `ofile` = '$orig_file',
      `osize` = '$osize',
      `owidth` = '$src_w',
      `oheight` = '$src_h',
      `npath` = '$new_path',
      `nfile` = '$new_file',
      `nwidth` = '$mke_w',
      `nheight` = '$mke_h',
      `status` = 'START' ";
                                                                                                                                                                                                      // print_p($string);
                                                                                                                                                                                                      $query = my_query($string);
                                                                                                                                                                                                      $id = my_id();
                                                                                                                                                                                                      my_free($query);
                                                                                                                                                                                                    }

                                                                                                                                                                                                    $dst_im = imagecreatetruecolor($mke_w, $mke_h);
                                                                                                                                                                                                    $bg = imagecolorallocate($dst_im, 255, 255, 255);
                                                                                                                                                                                                    imagecolortransparent($dst_im, $bg);
                                                                                                                                                                                                    imagefilledrectangle($dst_im, 0, 0, $mke_w, $mke_h, $bg);
                                                                                                                                                                                                    // print_p("$dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h") ;

                                                                                                                                                                                                    // png transparency
                                                                                                                                                                                                    if ($itype == 3 && png_transparent($orig_fpath)) {
                                                                                                                                                                                                      imagealphablending($dst_im, false);
                                                                                                                                                                                                      imagesavealpha($dst_im, true);
                                                                                                                                                                                                    }

                                                                                                                                                                                                    // original image
                                                                                                                                                                                                    switch ($itype) {
                                                                                                                                                                                                      case 1:
                                                                                                                                                                                                        $src_im = imagecreatefromgif($orig_fpath);
                                                                                                                                                                                                        break;
                                                                                                                                                                                                      case 2:
                                                                                                                                                                                                        $src_im = imagecreatefromjpeg($orig_fpath);
                                                                                                                                                                                                        break;
                                                                                                                                                                                                      case 3:
                                                                                                                                                                                                        $src_im = imagecreatefrompng($orig_fpath);
                                                                                                                                                                                                        break;
                                                                                                                                                                                                      case 6:
                                                                                                                                                                                                        $src_im = imagecreatefrombmp($orig_fpath);
                                                                                                                                                                                                        break;
                                                                                                                                                                                                      case 18:
                                                                                                                                                                                                        $src_im = imagecreatefromwebp($orig_fpath);
                                                                                                                                                                                                        break;
                                                                                                                                                                                                      default:
                                                                                                                                                                                                        return make_image_return($id, "Make ({$orig_file}) format invalid");
                                                                                                                                                                                                        break;
                                                                                                                                                                                                    }

                                                                                                                                                                                                    // resize image
                                                                                                                                                                                                    imagecopyresampled($dst_im, $src_im, floor($dst_x), floor($dst_y), floor($src_x), floor($src_y), floor($dst_w), floor($dst_h), floor($src_w), floor($src_h));
                                                                                                                                                                                                    imagedestroy($src_im);

                                                                                                                                                                                                    // save new image
                                                                                                                                                                                                    switch ($itype) {
                                                                                                                                                                                                      case 1:
                                                                                                                                                                                                        imagegif($dst_im, $temp_fpath);
                                                                                                                                                                                                        break;
                                                                                                                                                                                                      case 2:
                                                                                                                                                                                                        imagejpeg($dst_im, $temp_fpath, $qual);
                                                                                                                                                                                                        break;
                                                                                                                                                                                                      case 3:
                                                                                                                                                                                                        imagepng($dst_im, $temp_fpath, round((100 - $qual) / 10, 0));
                                                                                                                                                                                                        break;
                                                                                                                                                                                                      case 6:
                                                                                                                                                                                                        imagejpeg($dst_im, $temp_fpath);
                                                                                                                                                                                                        break;
                                                                                                                                                                                                      case 18:
                                                                                                                                                                                                        imagewebp($dst_im, $temp_fpath);
                                                                                                                                                                                                        break;
                                                                                                                                                                                                      default:
                                                                                                                                                                                                        return make_image_return($id, "Make ({$new_file}) image creation failure");
                                                                                                                                                                                                        break;
                                                                                                                                                                                                    }
                                                                                                                                                                                                    imagedestroy($dst_im);
                                                                                                                                                                                                    // move from temp
                                                                                                                                                                                                    if (file_exists($temp_fpath)) {
                                                                                                                                                                                                      if (file_exists($new_fpath = build_path($new_path, $new_file))) {
                                                                                                                                                                                                        del_file($new_path, $new_file);
                                                                                                                                                                                                      }
                                                                                                                                                                                                      move_temp($new_path, $new_file);
                                                                                                                                                                                                      if ($del && ($del != 'n') && ($new_fpath != build_path($orig_path, $orig_file))) {
                                                                                                                                                                                                        del_file($orig_path, $orig_file);
                                                                                                                                                                                                      }
                                                                                                                                                                                                      return make_image_return($id, "Make ({$new_file}) created and saved", $new_file);
                                                                                                                                                                                                    }
                                                                                                                                                                                                    return make_image_return($id, "Make ({$new_file}) image not created");
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function make_image_return($id, $msg, $return = false)
                                                                                                                                                                                                  {
                                                                                                                                                                                                    global $globvars;
                                                                                                                                                                                                    if ($id && isset($globvars['db_imakelog']) && $globvars['db_imakelog']) {
                                                                                                                                                                                                      $status = $return ? 'DONE' : 'FAIL';
                                                                                                                                                                                                      $string = "update `{$globvars['db_imakelog']}` set `status` = '$status', `message` = '$msg' where `id` = '$id' limit 1";
                                                                                                                                                                                                      // print_p($string);
                                                                                                                                                                                                      $query = my_query($string);
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if (!$return) {
                                                                                                                                                                                                      print_p($msg, 'red');
                                                                                                                                                                                                    }
                                                                                                                                                                                                    return $return;
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function png_transparent($filename)
                                                                                                                                                                                                  {
                                                                                                                                                                                                    if (strlen($filename) == 0 || !file_exists($filename)) {
                                                                                                                                                                                                      return false;
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if (ord(file_get_contents($filename, false, null, 25, 1)) & 4) {
                                                                                                                                                                                                      return true;
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $contents = file_get_contents($filename);
                                                                                                                                                                                                    if (stripos($contents, 'PLTE') !== false && stripos($contents, 'tRNS') !== false) {
                                                                                                                                                                                                      return true;
                                                                                                                                                                                                    }
                                                                                                                                                                                                    return false;
                                                                                                                                                                                                  }

                                                                                                                                                                                                  if (!function_exists('imagecreatefrombmp')) {
                                                                                                                                                                                                    function imagecreatefrombmp($filename)
                                                                                                                                                                                                    {
                                                                                                                                                                                                      if (!$f1 = fopen($filename, "rb")) {
                                                                                                                                                                                                        return false;
                                                                                                                                                                                                      }

                                                                                                                                                                                                      $FILE = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread($f1, 14));
                                                                                                                                                                                                      if ($FILE['file_type'] != 19778) {
                                                                                                                                                                                                        return false;
                                                                                                                                                                                                      }

                                                                                                                                                                                                      $BMP = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel' .
                                                                                                                                                                                                        '/Vcompression/Vsize_bitmap/Vhoriz_resolution' .
                                                                                                                                                                                                        '/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1, 40));
                                                                                                                                                                                                      $BMP['colors'] = pow(2, $BMP['bits_per_pixel']);
                                                                                                                                                                                                      if ($BMP['size_bitmap'] == 0) $BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];
                                                                                                                                                                                                      $BMP['bytes_per_pixel'] = $BMP['bits_per_pixel'] / 8;
                                                                                                                                                                                                      $BMP['bytes_per_pixel2'] = ceil($BMP['bytes_per_pixel']);
                                                                                                                                                                                                      $BMP['decal'] = ($BMP['width'] * $BMP['bytes_per_pixel'] / 4);
                                                                                                                                                                                                      $BMP['decal'] -= floor($BMP['width'] * $BMP['bytes_per_pixel'] / 4);
                                                                                                                                                                                                      $BMP['decal'] = 4 - (4 * $BMP['decal']);
                                                                                                                                                                                                      if ($BMP['decal'] == 4) $BMP['decal'] = 0;

                                                                                                                                                                                                      $PALETTE = array();
                                                                                                                                                                                                      if ($BMP['colors'] < 16777216 && $BMP['colors'] != 65536) {
                                                                                                                                                                                                        $PALETTE = unpack('V' . $BMP['colors'], fread($f1, $BMP['colors'] * 4));
                                                                                                                                                                                                      }
                                                                                                                                                                                                      $IMG = fread($f1, $BMP['size_bitmap']);
                                                                                                                                                                                                      $VIDE = chr(0);

                                                                                                                                                                                                      $res = imagecreatetruecolor($BMP['width'], $BMP['height']);
                                                                                                                                                                                                      $P = 0;
                                                                                                                                                                                                      $Y = $BMP['height'] - 1;
                                                                                                                                                                                                      while ($Y >= 0) {
                                                                                                                                                                                                        $X = 0;
                                                                                                                                                                                                        while ($X < $BMP['width']) {
                                                                                                                                                                                                          if ($BMP['bits_per_pixel'] == 24) {
                                                                                                                                                                                                            $COLOR = unpack("V", substr($IMG, $P, 3) . $VIDE);
                                                                                                                                                                                                          } elseif ($BMP['bits_per_pixel'] == 16) {
                                                                                                                                                                                                            $COLOR = unpack("v", substr($IMG, $P, 2));
                                                                                                                                                                                                            $blue  = (($COLOR[1] & 0x001f) << 3) + 7;
                                                                                                                                                                                                            $green = (($COLOR[1] & 0x03e0) >> 2) + 7;
                                                                                                                                                                                                            $red   = (($COLOR[1] & 0xfc00) >> 7) + 7;
                                                                                                                                                                                                            $COLOR[1] = $red * 65536 + $green * 256 + $blue;
                                                                                                                                                                                                          } elseif ($BMP['bits_per_pixel'] == 8) {
                                                                                                                                                                                                            $COLOR = unpack("n", $VIDE . substr($IMG, $P, 1));
                                                                                                                                                                                                            $COLOR[1] = $PALETTE[$COLOR[1] + 1];
                                                                                                                                                                                                          } elseif ($BMP['bits_per_pixel'] == 4) {
                                                                                                                                                                                                            $COLOR = unpack("n", $VIDE . substr($IMG, floor($P), 1));
                                                                                                                                                                                                            if (($P * 2) % 2 == 0) $COLOR[1] = ($COLOR[1] >> 4);
                                                                                                                                                                                                            else $COLOR[1] = ($COLOR[1] & 0x0F);
                                                                                                                                                                                                            $COLOR[1] = $PALETTE[$COLOR[1] + 1];
                                                                                                                                                                                                          } elseif ($BMP['bits_per_pixel'] == 1) {
                                                                                                                                                                                                            $COLOR = unpack("n", $VIDE . substr($IMG, floor($P), 1));
                                                                                                                                                                                                            if (($P * 8) % 8 == 0) $COLOR[1] =  $COLOR[1]        >> 7;
                                                                                                                                                                                                            elseif (($P * 8) % 8 == 1) $COLOR[1] = ($COLOR[1] & 0x40) >> 6;
                                                                                                                                                                                                            elseif (($P * 8) % 8 == 2) $COLOR[1] = ($COLOR[1] & 0x20) >> 5;
                                                                                                                                                                                                            elseif (($P * 8) % 8 == 3) $COLOR[1] = ($COLOR[1] & 0x10) >> 4;
                                                                                                                                                                                                            elseif (($P * 8) % 8 == 4) $COLOR[1] = ($COLOR[1] & 0x8) >> 3;
                                                                                                                                                                                                            elseif (($P * 8) % 8 == 5) $COLOR[1] = ($COLOR[1] & 0x4) >> 2;
                                                                                                                                                                                                            elseif (($P * 8) % 8 == 6) $COLOR[1] = ($COLOR[1] & 0x2) >> 1;
                                                                                                                                                                                                            elseif (($P * 8) % 8 == 7) $COLOR[1] = ($COLOR[1] & 0x1);
                                                                                                                                                                                                            $COLOR[1] = $PALETTE[$COLOR[1] + 1];
                                                                                                                                                                                                          } else {
                                                                                                                                                                                                            return false;
                                                                                                                                                                                                          }
                                                                                                                                                                                                          imagesetpixel($res, $X, $Y, $COLOR[1]);
                                                                                                                                                                                                          $X++;
                                                                                                                                                                                                          $P += $BMP['bytes_per_pixel'];
                                                                                                                                                                                                        }
                                                                                                                                                                                                        $Y--;
                                                                                                                                                                                                        $P += $BMP['decal'];
                                                                                                                                                                                                      }

                                                                                                                                                                                                      fclose($f1);
                                                                                                                                                                                                      return $res;
                                                                                                                                                                                                    }
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function disp_filesize($bytes, $decimals = 2)
                                                                                                                                                                                                  {
                                                                                                                                                                                                    $size = array('', 'K', 'M', 'G', 'T', 'P', 'E', 'Z', 'Y');
                                                                                                                                                                                                    if (is_numeric($bytes) && ($bytes !== 0) && ($bytes > 0)) {
                                                                                                                                                                                                      $digits = floor(log10($bytes) + 1);
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                      $digits = $bytes = 0;
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $factor = floor(($digits - 1) / 3);
                                                                                                                                                                                                    if (!(is_numeric($factor) && isset($size[$factor]))) {
                                                                                                                                                                                                      $factor = 0;
                                                                                                                                                                                                    }
                                                                                                                                                                                                    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . $size[$factor] . 'B';
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function select_image($files, $fpath, $fname, $fid, $dval = '', $och = '')
                                                                                                                                                                                                  {
  ?>
  <table border="0" cellpadding="1" cellspacing="0" class="tablen">
    <tr>
      <td class="nobr">Server&nbsp;</td>
      <td class="nobr">
        <select class="chosen-select" name="<?= $fname ?>" id="<?= $fid ?>" size="1" style="max-width:560px;" onchange="<?= $och ?>">
          <option value="">** Select **</option>
          <? foreach ($files as $file) { ?>
            <option value="<?= optsel($file, $dval); ?>"><?= cliptext($file, 200); ?></option>
          <? }
                                                                                                                                                                                                    $href = 'listfiles.php?fpath=' . str_replace('/', '|', $fpath) . '&amp;file=' . $dval . '&amp;fid=' . $fid . '&amp;filter=';
                                                                                                                                                                                                    $onc  = "; listfiles('" . str_replace('/', '|', $fpath) . "','" . $dval . "','" . $fid . "','');";
          ?>
        </select>
        <span class="button"><a href="<?= $href ?>" onclick="<?= $och . $onc . 'return false;'; ?>" target="_blank">POP</a></span>
      </td>
    </tr>
    <tr>
      <td class="nobr">Upload&nbsp;</td>
      <td colspan="2">
        <div class="fileUpload">
          <?
                                                                                                                                                                                                    $nm = 'up_' . $fid;
                                                                                                                                                                                                    $id = 'id_' . $nm;
                                                                                                                                                                                                    $hd = 'hd_' . $nm;
                                                                                                                                                                                                    $bt = 'bz_' . $nm;
                                                                                                                                                                                                    $och = "getId('{$hd}').value = getId('{$id}').value.split('\\\').pop(); onbrowse('{$fid}'); {$och};";
                                                                                                                                                                                                    $omo = "getId('{$bt}').setAttribute('class', 'button1')";
                                                                                                                                                                                                    $omx = "getId('{$bt}').setAttribute('class', 'button')";
          ?>
          <input type="file" onchange="<?= $och; ?>" name="up_<?= $fid; ?>" id="<?= $id; ?>" class="browserHidden" onmouseover="<?= $omo; ?>" onmouseout="<?= $omx; ?>">
          <div class="browserVisible">
            <input type="text" class="input" id="<?= $hd; ?>" style="width:110px;" autocomplete="off"> <span id="<?= $bt ?>" class="button"><a href="#">BROWSE</a></span>
          </div>
        </div>
      </td>
    </tr>
  </table>
  <?
                                                                                                                                                                                                  }

                                                                                                                                                                                                  // ----------------------------------------------------BASKET---------------------------------------------------------------

                                                                                                                                                                                                  function countries($tbl = 'countries', $id = 'code', $ord = 'name')
                                                                                                                                                                                                  {
                                                                                                                                                                                                    return csarray($tbl, $id, $ord);
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function states($tbl = 'states', $id = 'code', $ord = 'code')
                                                                                                                                                                                                  {
                                                                                                                                                                                                    return csarray($tbl, $id, $ord);
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function csarray($tbl, $id, $ord)
                                                                                                                                                                                                  {
                                                                                                                                                                                                    $out = null;
                                                                                                                                                                                                    if ($tbl && $id) {
                                                                                                                                                                                                      if (!$ord) {
                                                                                                                                                                                                        $ord = $id;
                                                                                                                                                                                                      }
                                                                                                                                                                                                      $string = "SELECT * FROM `{$tbl}` ORDER BY `{$ord}`";
                                                                                                                                                                                                      $query = my_query($string);
                                                                                                                                                                                                      if (my_rows($query)) {
                                                                                                                                                                                                        $out = array();
                                                                                                                                                                                                        while ($row = my_assoc($query)) {
                                                                                                                                                                                                          $out[$row[$id]] = $row;
                                                                                                                                                                                                        }
                                                                                                                                                                                                      }
                                                                                                                                                                                                      my_free($query);
                                                                                                                                                                                                    }
                                                                                                                                                                                                    return $out;
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function make_address()
                                                                                                                                                                                                  {
                                                                                                                                                                                                    // first field is break eg. \r\n or <br>
                                                                                                                                                                                                    // other fields set as values like @address1
                                                                                                                                                                                                    $out = '';
                                                                                                                                                                                                    $lbr = func_get_arg(0);
                                                                                                                                                                                                    for ($i = 1; $i < func_num_args(); $i++) {
                                                                                                                                                                                                      $k = func_get_arg($i);
                                                                                                                                                                                                      if ($k) {
                                                                                                                                                                                                        if ($out) {
                                                                                                                                                                                                          $out .= $lbr;
                                                                                                                                                                                                        }
                                                                                                                                                                                                        $out .= $k;
                                                                                                                                                                                                      }
                                                                                                                                                                                                    }
                                                                                                                                                                                                    return $out;
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function chkout_set($var, $fields, $optional = array())
                                                                                                                                                                                                  {
                                                                                                                                                                                                    // remember session_start()
                                                                                                                                                                                                    // for checkboxes ensure a hidden field_alt with value 'n' for unchcked
                                                                                                                                                                                                    // var is variable name, fields and required are arrays
                                                                                                                                                                                                    // returns array of missing fields
                                                                                                                                                                                                    // if post sets session
                                                                                                                                                                                                    // creates globvars[$var]
                                                                                                                                                                                                    global $globvars;
                                                                                                                                                                                                    $out = array();
                                                                                                                                                                                                    if ($var) {
                                                                                                                                                                                                      if (count($optional)) {
                                                                                                                                                                                                        $fields = array_merge($fields, $optional);
                                                                                                                                                                                                      }
                                                                                                                                                                                                      foreach ($fields as $field) {
                                                                                                                                                                                                        if (!isset($_SESSION[$var][$field])) {
                                                                                                                                                                                                          $_SESSION[$var][$field] = '';
                                                                                                                                                                                                        }
                                                                                                                                                                                                        if (isset($_POST[$var][$field])) {
                                                                                                                                                                                                          $_SESSION[$var][$field] = clean_glob($_POST[$var][$field]);
                                                                                                                                                                                                        } elseif (isset($_POST[$var][$field . '_alt'])) {
                                                                                                                                                                                                          // for checkbox set to value of hidden field_alt
                                                                                                                                                                                                          $_SESSION[$var][$field] = $_POST[$var][$field . '_alt'];
                                                                                                                                                                                                        }
                                                                                                                                                                                                        if (!(in_array($field, $optional) || $_SESSION[$var][$field])) {
                                                                                                                                                                                                          $out[] = $field;
                                                                                                                                                                                                        }
                                                                                                                                                                                                      }
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $globvars[$var] = $_SESSION[$var];
                                                                                                                                                                                                    return $out;
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function chkout_arr($var, $tbl, $id, $price, $str1 = null, $str2 = null)
                                                                                                                                                                                                  {
                                                                                                                                                                                                    // chkout_arr('giftbox','giftref','')
                                                                                                                                                                                                    // returns array of options
                                                                                                                                                                                                    // if post sets session
                                                                                                                                                                                                    // creates globvars[$var]['id'] and ['price']
                                                                                                                                                                                                    global $globvars;
                                                                                                                                                                                                    if (isset($_POST[$var]) || !isset($_SESSION[$var])) {
                                                                                                                                                                                                      $_SESSION[$var]['id'] = '';
                                                                                                                                                                                                      $_SESSION[$var]['price'] = 0;
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $out = false;
                                                                                                                                                                                                    if ($var && $tbl && $id) {
                                                                                                                                                                                                      if (!$str1) {
                                                                                                                                                                                                        $str1 = '*';
                                                                                                                                                                                                      }
                                                                                                                                                                                                      $string = "SELECT {$str1} FROM `{$tbl}` {$str2}";
                                                                                                                                                                                                      $query = my_query($string);
                                                                                                                                                                                                      if (my_rows($query)) {
                                                                                                                                                                                                        $out = array();
                                                                                                                                                                                                        while ($row = my_array($query)) {
                                                                                                                                                                                                          $out[$row[$id]] = $row;
                                                                                                                                                                                                          if (isset($_POST[$var]) && ($row[$id] == $_POST[$var])) {
                                                                                                                                                                                                            $_SESSION[$var]['id'] = $row[$id];
                                                                                                                                                                                                            $_SESSION[$var]['price'] = $row[$price];
                                                                                                                                                                                                          }
                                                                                                                                                                                                        }
                                                                                                                                                                                                      }
                                                                                                                                                                                                      my_free($query);
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $globvars[$var] = $_SESSION[$var];
                                                                                                                                                                                                    return $out;
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function basket_add($in)
                                                                                                                                                                                                  {
                                                                                                                                                                                                    // new function
                                                                                                                                                                                                    // $in is array of id's and quantity eg. basket_add( array( $prd1 => $num1 , $prd2 => $num2 ) );
                                                                                                                                                                                                    // or send array instead of num to add eg. basket_add( array( $prd1 => array('num' => $num, 'size' => $size, 'price' => $price) ) );
                                                                                                                                                                                                    // or just send $id number to add quant 1 (no options possible)
                                                                                                                                                                                                    // returns number of items added
                                                                                                                                                                                                    if (!isarr($in)) {
                                                                                                                                                                                                      $in = array($in => 1);
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $c = 0;
                                                                                                                                                                                                    foreach ($in as $id => $arr) {
                                                                                                                                                                                                      $num = is_array($arr) ? $arr['num'] : $arr;
                                                                                                                                                                                                      $num = isnum($num);
                                                                                                                                                                                                      if ($id && $num) {
                                                                                                                                                                                                        if (!isset($_SESSION['basket'][$id])) {
                                                                                                                                                                                                          $_SESSION['basket'][$id] = 0;
                                                                                                                                                                                                          if (isset($_SESSION['basket_opts'][$id])) {
                                                                                                                                                                                                            unset($_SESSION['basket_opts'][$id]);
                                                                                                                                                                                                          }
                                                                                                                                                                                                        }
                                                                                                                                                                                                        $_SESSION['basket'][$id] += $num;
                                                                                                                                                                                                        $c += $num;
                                                                                                                                                                                                        if (is_array($arr)) {
                                                                                                                                                                                                          if (isset($_SESSION['basket_opts'][$id])) {
                                                                                                                                                                                                            // found matching product
                                                                                                                                                                                                            $optmatch = -1;
                                                                                                                                                                                                            if (is_array($_SESSION['basket_opts'][$id])) {
                                                                                                                                                                                                              foreach ($_SESSION['basket_opts'][$id] as $optkey => $optarr) {
                                                                                                                                                                                                                // loop exiting entries for product
                                                                                                                                                                                                                $chk = 1;
                                                                                                                                                                                                                foreach ($arr as $key => $newopt) {
                                                                                                                                                                                                                  // loop product options array
                                                                                                                                                                                                                  if (is_array($newopt)) {
                                                                                                                                                                                                                    foreach ($newopt as $key1 => $newopt1) {
                                                                                                                                                                                                                      // loop options sub-array
                                                                                                                                                                                                                      if ((!isset($optarr[$key][$key1])) || ($optarr[$key][$key1] != $newopt1)) {
                                                                                                                                                                                                                        // option is different
                                                                                                                                                                                                                        $chk = 0;
                                                                                                                                                                                                                        break;
                                                                                                                                                                                                                      }
                                                                                                                                                                                                                    }
                                                                                                                                                                                                                    if (!$chk) {
                                                                                                                                                                                                                      break;
                                                                                                                                                                                                                    }
                                                                                                                                                                                                                  } elseif (($key != 'num') && ((!isset($optarr[$key])) || ($optarr[$key] != $newopt))) {
                                                                                                                                                                                                                    // option is different
                                                                                                                                                                                                                    $chk = 0;
                                                                                                                                                                                                                    break;
                                                                                                                                                                                                                  }
                                                                                                                                                                                                                }
                                                                                                                                                                                                                if ($chk) {
                                                                                                                                                                                                                  $optmatch = $optkey;
                                                                                                                                                                                                                  break;
                                                                                                                                                                                                                }
                                                                                                                                                                                                              }
                                                                                                                                                                                                            }
                                                                                                                                                                                                            if ($optmatch >= 0) {
                                                                                                                                                                                                              // add quant to existing option
                                                                                                                                                                                                              $_SESSION['basket_opts'][$id][$optkey]['num'] += $num;
                                                                                                                                                                                                            } elseif (is_array($_SESSION['basket_opts'][$id])) {
                                                                                                                                                                                                              // add new option
                                                                                                                                                                                                              $_SESSION['basket_opts'][$id][] = $arr;
                                                                                                                                                                                                            }
                                                                                                                                                                                                          } else {
                                                                                                                                                                                                            // first time option
                                                                                                                                                                                                            $_SESSION['basket_opts'][$id][0] = $arr;
                                                                                                                                                                                                          }
                                                                                                                                                                                                        }
                                                                                                                                                                                                      }
                                                                                                                                                                                                    }
                                                                                                                                                                                                    // print_arr($_SESSION['basket'],'basket');
                                                                                                                                                                                                    // print_arr($_SESSION['basket_opts'],'basket_opts');
                                                                                                                                                                                                    if ($c) {
                                                                                                                                                                                                      if (isset($globvars['basket_params'])) {
                                                                                                                                                                                                        basket_read();
                                                                                                                                                                                                      }
                                                                                                                                                                                                      return $c;
                                                                                                                                                                                                    }
                                                                                                                                                                                                    return false;
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function basket_change($prd, $opt, $num, $arr = '')
                                                                                                                                                                                                  {
                                                                                                                                                                                                    // use with new basket_add function
                                                                                                                                                                                                    // eg. basket_change(1234,1,2,array('size' => $size));
                                                                                                                                                                                                    global $globvars;
                                                                                                                                                                                                    if ($prd && isset($_SESSION['basket_opts'][$prd]) && !isset($_SESSION['basket'][$prd])) {
                                                                                                                                                                                                      // remove orphaned options
                                                                                                                                                                                                      unset($_SESSION['basket_opts'][$prd]);
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if ($prd && isset($_SESSION['basket'][$prd])) {
                                                                                                                                                                                                      if ($num <= 0) {
                                                                                                                                                                                                        // delete
                                                                                                                                                                                                        if (isset($_SESSION['basket_opts'][$prd][$opt]['num'])) {
                                                                                                                                                                                                          // has option
                                                                                                                                                                                                          $onum = $_SESSION['basket_opts'][$prd][$opt]['num'];
                                                                                                                                                                                                          unset($_SESSION['basket_opts'][$prd][$opt]);
                                                                                                                                                                                                          $_SESSION['basket'][$prd] -= $onum;
                                                                                                                                                                                                          if ($_SESSION['basket'][$prd] <= 0) {
                                                                                                                                                                                                            unset($_SESSION['basket'][$prd]);
                                                                                                                                                                                                          }
                                                                                                                                                                                                        } elseif (isset($_SESSION['basket'][$prd])) {
                                                                                                                                                                                                          // no option
                                                                                                                                                                                                          unset($_SESSION['basket'][$prd]);
                                                                                                                                                                                                        }
                                                                                                                                                                                                      } else {
                                                                                                                                                                                                        // change number
                                                                                                                                                                                                        if (isset($_SESSION['basket_opts'][$prd][$opt]['num'])) {
                                                                                                                                                                                                          // has option
                                                                                                                                                                                                          $bnum = $_SESSION['basket'][$prd];
                                                                                                                                                                                                          $onum = $_SESSION['basket_opts'][$prd][$opt]['num'];
                                                                                                                                                                                                          $_SESSION['basket_opts'][$prd][$opt]['num'] = $num;
                                                                                                                                                                                                          $_SESSION['basket'][$prd] = $bnum + $num - $onum;
                                                                                                                                                                                                        } else {
                                                                                                                                                                                                          // no option
                                                                                                                                                                                                          $_SESSION['basket_opts'][$prd] = $num;
                                                                                                                                                                                                        }
                                                                                                                                                                                                      }
                                                                                                                                                                                                      if (isset($_SESSION['basket_opts'][$prd][$opt]) && is_array($arr)) {
                                                                                                                                                                                                        // change options
                                                                                                                                                                                                        foreach ($arr as $key => $val) {
                                                                                                                                                                                                          $_SESSION['basket_opts'][$prd][$opt][$key] = $val;
                                                                                                                                                                                                        }
                                                                                                                                                                                                      }
                                                                                                                                                                                                      if (isset($globvars['basket_params'])) {
                                                                                                                                                                                                        basket_read();
                                                                                                                                                                                                      }
                                                                                                                                                                                                    }
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function basket_read($tbl = null, $fld = null, $str1 = null, $str2 = null, $str3 = null)
                                                                                                                                                                                                  {
                                                                                                                                                                                                    // basket_read('items','id','','')
                                                                                                                                                                                                    // creates $globvars['bsk_arr'] basket array
                                                                                                                                                                                                    // creates $globvars['bsk_count'] basket count
                                                                                                                                                                                                    // if $tbl & $fld puts db fields in array
                                                                                                                                                                                                    // returns number of items in basket
                                                                                                                                                                                                    global $globvars;
                                                                                                                                                                                                    if (isset($globvars['basket_params']) && is_array($globvars['basket_params'])) {
                                                                                                                                                                                                      // read params from settings (allows calling function without params)
                                                                                                                                                                                                      $tbl = $globvars['basket_params']['tbl'];
                                                                                                                                                                                                      $fld = $globvars['basket_params']['fld'];
                                                                                                                                                                                                      $str1 = $globvars['basket_params']['str1'];
                                                                                                                                                                                                      $str2 = $globvars['basket_params']['str2'];
                                                                                                                                                                                                      $str3 = $globvars['basket_params']['str3'];
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if (isset($globvars['basket_params']['before']) && function_exists($globvars['basket_params']['before'])) {
                                                                                                                                                                                                      $globvars['basket_params']['before']();
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $bsk_arr = array();
                                                                                                                                                                                                    $bsk_count = 0;
                                                                                                                                                                                                    if (($sb = isarr($_SESSION['basket'])) && count($sb)) {
                                                                                                                                                                                                      // print_arr($_SESSION['basket']);
                                                                                                                                                                                                      if ($tbl && $fld) {
                                                                                                                                                                                                        if (!(isset($db_open) && $db_open)) {
                                                                                                                                                                                                          opendb();
                                                                                                                                                                                                        }
                                                                                                                                                                                                        // new method strips extra code after _ to get product id for checking db
                                                                                                                                                                                                        $keys = '';
                                                                                                                                                                                                        foreach ($sb as $key => $num) {
                                                                                                                                                                                                          if (substr_count($key, '_')) {
                                                                                                                                                                                                            $key = substr($key, 0, strpos($key, '_'));
                                                                                                                                                                                                          }
                                                                                                                                                                                                          if (!substr_count($keys, "'" . $key . "'")) {
                                                                                                                                                                                                            $keys .= "'" . $key . "',";
                                                                                                                                                                                                          }
                                                                                                                                                                                                        }
                                                                                                                                                                                                        $keys = substr($keys, 0, -1);
                                                                                                                                                                                                        // $keys = "'" . safe_implode("','",array_keys($sb)) . "'";
                                                                                                                                                                                                        if (!$str1) {
                                                                                                                                                                                                          $str1 = '*';
                                                                                                                                                                                                        }
                                                                                                                                                                                                        $string = "SELECT {$str1} FROM `{$tbl}` {$str2} WHERE `{$tbl}`.`{$fld}` IN ({$keys}) {$str3} ";
                                                                                                                                                                                                        // print_p($string);
                                                                                                                                                                                                        $query = my_query($string);
                                                                                                                                                                                                        // print_p(echo memory_get_usage());
                                                                                                                                                                                                        if (my_rows($query)) {
                                                                                                                                                                                                          while ($i_row = my_array($query, MYSQL_ASSOC)) {
                                                                                                                                                                                                            $bsk_chk[$i_row[$fld]] = $i_row;
                                                                                                                                                                                                          }
                                                                                                                                                                                                          foreach ($sb as $key => $num) {
                                                                                                                                                                                                            if (($quant = isvar($sb[$key])) && is_numeric($quant) && ($quant > 0)) {
                                                                                                                                                                                                              $id = substr_count($key, '_') ? substr($key, 0, strpos($key, '_')) : $key;
                                                                                                                                                                                                              if (isset($bsk_chk[$id]) && $bsk_chk[$id]) {
                                                                                                                                                                                                                $bsk_arr[$key] = $bsk_chk[$id];
                                                                                                                                                                                                                $bsk_count += $quant;
                                                                                                                                                                                                                $bsk_arr[$key]['bsk_quant'] = $quant;
                                                                                                                                                                                                                if (isset($_SESSION['basket'][$key]) && isset($_SESSION['basket_opts'][$key])) {
                                                                                                                                                                                                                  $bsk_arr[$key]['bsk_opts'] = $_SESSION['basket_opts'][$key];
                                                                                                                                                                                                                }
                                                                                                                                                                                                              } else {
                                                                                                                                                                                                                // item in basket not found in product database
                                                                                                                                                                                                                if (isset($_SESSION['basket'][$key])) {
                                                                                                                                                                                                                  unset($_SESSION['basket'][$key]);
                                                                                                                                                                                                                }
                                                                                                                                                                                                                if (isset($_SESSION['basket_opts'][$key])) {
                                                                                                                                                                                                                  unset($_SESSION['basket_opts'][$key]);
                                                                                                                                                                                                                }
                                                                                                                                                                                                              }
                                                                                                                                                                                                            } else {
                                                                                                                                                                                                              // item in basket has no quantity
                                                                                                                                                                                                              if (isset($_SESSION['basket'][$key])) {
                                                                                                                                                                                                                unset($_SESSION['basket'][$key]);
                                                                                                                                                                                                              }
                                                                                                                                                                                                              if (isset($_SESSION['basket_opts'][$key])) {
                                                                                                                                                                                                                unset($_SESSION['basket_opts'][$key]);
                                                                                                                                                                                                              }
                                                                                                                                                                                                            }
                                                                                                                                                                                                          }
                                                                                                                                                                                                        } else {
                                                                                                                                                                                                          // nothing in basket found in product database
                                                                                                                                                                                                          if (isset($_SESSION['basket'])) {
                                                                                                                                                                                                            unset($_SESSION['basket']);
                                                                                                                                                                                                          }
                                                                                                                                                                                                          if (isset($_SESSION['basket_opts'])) {
                                                                                                                                                                                                            unset($_SESSION['basket_opts']);
                                                                                                                                                                                                          }
                                                                                                                                                                                                        }
                                                                                                                                                                                                        my_free($query);
                                                                                                                                                                                                      } else {
                                                                                                                                                                                                        foreach ($sb as $key => $quant) {
                                                                                                                                                                                                          if (is_numeric($quant) && ($quant > 0)) {
                                                                                                                                                                                                            $bsk_count += $quant;
                                                                                                                                                                                                            $bsk_arr[$key]['bsk_quant'] = $quant;
                                                                                                                                                                                                            if (isset($_SESSION['basket'][$key]) && isset($_SESSION['basket_opts'][$key])) {
                                                                                                                                                                                                              $bsk_arr[$key]['bsk_opts'] = $_SESSION['basket_opts'][$key];
                                                                                                                                                                                                            }
                                                                                                                                                                                                          } else {
                                                                                                                                                                                                            // item in basket has no quantity
                                                                                                                                                                                                            if (isset($_SESSION['basket'][$key])) {
                                                                                                                                                                                                              unset($_SESSION['basket'][$key]);
                                                                                                                                                                                                            }
                                                                                                                                                                                                            if (isset($_SESSION['basket_opts'][$key])) {
                                                                                                                                                                                                              unset($_SESSION['basket_opts'][$key]);
                                                                                                                                                                                                            }
                                                                                                                                                                                                          }
                                                                                                                                                                                                        }
                                                                                                                                                                                                      }
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                      // nothing in basket so delete options if exist
                                                                                                                                                                                                      if (isset($_SESSION['basket_opts'])) {
                                                                                                                                                                                                        unset($_SESSION['basket_opts']);
                                                                                                                                                                                                      }
                                                                                                                                                                                                    }
                                                                                                                                                                                                    // print_arr($bsk_arr,'bsk_arr');
                                                                                                                                                                                                    globvadd('bsk_arr', $bsk_arr, 'bsk_count', $bsk_count);
                                                                                                                                                                                                    if (isset($globvars['basket_params']['return']) && function_exists($globvars['basket_params']['return'])) {
                                                                                                                                                                                                      return $globvars['basket_params']['return']();
                                                                                                                                                                                                    }
                                                                                                                                                                                                    return $bsk_count;
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function basket_clr()
                                                                                                                                                                                                  {
                                                                                                                                                                                                    // returns true if basket was set
                                                                                                                                                                                                    if (isset($_SESSION['basket'])) {
                                                                                                                                                                                                      unset($_SESSION['basket']);
                                                                                                                                                                                                      if (isset($_SESSION['basket_opts'])) {
                                                                                                                                                                                                        unset($_SESSION['basket_opts']);
                                                                                                                                                                                                      }
                                                                                                                                                                                                      if (isset($globvars['basket_params'])) {
                                                                                                                                                                                                        basket_read();
                                                                                                                                                                                                      }
                                                                                                                                                                                                      return true;
                                                                                                                                                                                                    }
                                                                                                                                                                                                    return false;
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function basket_chk($id)
                                                                                                                                                                                                  {
                                                                                                                                                                                                    // returns number in basket for item
                                                                                                                                                                                                    if (isset($_SESSION['basket'][$id]) && $_SESSION['basket'][$id]) {
                                                                                                                                                                                                      return $_SESSION['basket'][$id];
                                                                                                                                                                                                    }
                                                                                                                                                                                                    return 0;
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function basket_set($in)
                                                                                                                                                                                                  {
                                                                                                                                                                                                    // old function - basked_add has better handling of options
                                                                                                                                                                                                    // $in is array of id's and quantity eg. basket_set( array( $prd1 => $num1 , $prd2 => $num2 ) );
                                                                                                                                                                                                    // or send array instead of num to set options basket_set( array( $prd1 => array('num' => $num, 'size' => $size) ) );
                                                                                                                                                                                                    // or just send $id number to add quant 1 (no options possible)
                                                                                                                                                                                                    // returns number of items saved (add/updated)
                                                                                                                                                                                                    if (!isarr($in)) {
                                                                                                                                                                                                      $in = array($in => 1);
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $c = 0;
                                                                                                                                                                                                    foreach ($in as $id => $val) {
                                                                                                                                                                                                      $prv = basket_chk($id);
                                                                                                                                                                                                      $num = is_array($val) ? $val['num'] : $val;
                                                                                                                                                                                                      $num = isnum($num);
                                                                                                                                                                                                      if (isset($_SESSION['basket'][$id]) && !$num) {
                                                                                                                                                                                                        // delete if less than 0
                                                                                                                                                                                                        unset($_SESSION['basket'][$id]);
                                                                                                                                                                                                        if (isset($_SESSION['basket_opts'][$id])) {
                                                                                                                                                                                                          unset($_SESSION['basket_opts'][$id]);
                                                                                                                                                                                                        }
                                                                                                                                                                                                      } elseif ($id && $num) {
                                                                                                                                                                                                        $c += $num;
                                                                                                                                                                                                        $_SESSION['basket'][$id] = $num;
                                                                                                                                                                                                        if (is_array($val)) {
                                                                                                                                                                                                          foreach ($val as $key => $opt) {
                                                                                                                                                                                                            // loop options add entries from previous num to new num
                                                                                                                                                                                                            if ($key != 'num') {
                                                                                                                                                                                                              for ($i = $prv + 1; $i <= $num; $i++) {
                                                                                                                                                                                                                $_SESSION['basket_opts'][$id][$i][$key] = $opt;
                                                                                                                                                                                                              }
                                                                                                                                                                                                            }
                                                                                                                                                                                                          }
                                                                                                                                                                                                        }
                                                                                                                                                                                                      }
                                                                                                                                                                                                    }
                                                                                                                                                                                                    // print_arr($_SESSION['basket'],'basket');
                                                                                                                                                                                                    // print_arr($_SESSION['basket_opts'],'basket_opts');
                                                                                                                                                                                                    if ($c) {
                                                                                                                                                                                                      return $c;
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                      return false;
                                                                                                                                                                                                    }
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function basket_del($in)
                                                                                                                                                                                                  {
                                                                                                                                                                                                    // old function for basket_set - use basket_change with basket_add
                                                                                                                                                                                                    // $in is array of id's eg. basket_del( array( $prd1 => 1 , $prd2 => 1 ) );
                                                                                                                                                                                                    // or just send $id number to delete
                                                                                                                                                                                                    // returns number of items deleted
                                                                                                                                                                                                    if (!isarr($in)) {
                                                                                                                                                                                                      $in = array($in => 1);
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $c = 0;
                                                                                                                                                                                                    foreach ($in as $id => $chk) {
                                                                                                                                                                                                      if ($chk && isset($_SESSION['basket'][$id])) {
                                                                                                                                                                                                        $c++;
                                                                                                                                                                                                        unset($_SESSION['basket'][$id]);
                                                                                                                                                                                                        if (isset($_SESSION['basket_opts'][$id])) {
                                                                                                                                                                                                          unset($_SESSION['basket_opts'][$id]);
                                                                                                                                                                                                        }
                                                                                                                                                                                                      }
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if ($c) {
                                                                                                                                                                                                      return $c;
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                      return false;
                                                                                                                                                                                                    }
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function bask_items($quant, $word = '')
                                                                                                                                                                                                  {
                                                                                                                                                                                                    $out = ($quant >= 0) ? $quant : 0;
                                                                                                                                                                                                    if ($word) {
                                                                                                                                                                                                      $out .= ' ' . $word;
                                                                                                                                                                                                      if ($quant != 1) {
                                                                                                                                                                                                        $out .= 's';
                                                                                                                                                                                                      }
                                                                                                                                                                                                    }
                                                                                                                                                                                                    return $out;
                                                                                                                                                                                                  }

                                                                                                                                                                                                  // ----------------------------------------------------SEO------------------------------------------------------------------

                                                                                                                                                                                                  function addsitemap($url, $prior = 0, $freq = '', $name = '', $cat = '')
                                                                                                                                                                                                  {
                                                                                                                                                                                                    global $globvars;
                                                                                                                                                                                                    if (!$prior) {
                                                                                                                                                                                                      $prior = 0.8;
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if (!isset($globvars['sm_pages'])) {
                                                                                                                                                                                                      $globvars['sm_pages'] = [];
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if (!isset($globvars['sm_prior'])) {
                                                                                                                                                                                                      $globvars['sm_prior'] = [];
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if (!isset($globvars['sm_allow'])) {
                                                                                                                                                                                                      $globvars['sm_allow'] = [];
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if (!isset($globvars['sm_subfs'])) {
                                                                                                                                                                                                      $globvars['sm_subfs'] = [];
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if (!isset($globvars['sm_excld'])) {
                                                                                                                                                                                                      $globvars['sm_excld'] = [];
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if (!isset($globvars['sm_array'])) {
                                                                                                                                                                                                      $globvars['sm_array'] = [];
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if (!isset($globvars['sm_cfreq'])) {
                                                                                                                                                                                                      $globvars['sm_cfreq'] = [];
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if (!(in_array($url, $globvars['sm_pages']) || in_array($url, $globvars['sm_excld']))) {
                                                                                                                                                                                                      $globvars['sm_pages'][] = $url;
                                                                                                                                                                                                      $globvars['sm_prior'][] = $prior;
                                                                                                                                                                                                      $globvars['sm_cfreq'][] = $freq;
                                                                                                                                                                                                      if ($name) {
                                                                                                                                                                                                        $globvars['sm_array'][$cat][$name] = $url;
                                                                                                                                                                                                      }
                                                                                                                                                                                                    }
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function makesitemap()
                                                                                                                                                                                                  {
                                                                                                                                                                                                    global $globvars;
                                                                                                                                                                                                    if ((!file_exists('sitemap.xml')) || (fileperms('sitemap.xml') == '33188') || (date("Y-m-d", filemtime('sitemap.xml')) == date("Y-m-d") && ($globvars['query_string'] != 'makesitemap'))) {
                                                                                                                                                                                                      return;
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $globvars['sm_prior'] = array_pad($globvars['sm_prior'], count($globvars['sm_pages']), 0.8);
                                                                                                                                                                                                    if (isvar($globvars['sm_funct']) && function_exists($globvars['sm_funct'])) {
                                                                                                                                                                                                      $globvars['sm_funct']();
                                                                                                                                                                                                    }
                                                                                                                                                                                                    extract($globvars);
                                                                                                                                                                                                    $dirs = array('.');
                                                                                                                                                                                                    if (isset($sm_subfs) && is_array($sm_subfs)) {
                                                                                                                                                                                                      foreach ($sm_subfs as $sm_subf) {
                                                                                                                                                                                                        if ($sm_subf && is_dir($sm_subf)) {
                                                                                                                                                                                                          $dirs[] = $sm_subf;
                                                                                                                                                                                                        }
                                                                                                                                                                                                      }
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $files = array();
                                                                                                                                                                                                    foreach ($dirs as $dir) {
                                                                                                                                                                                                      if (($handle = opendir($dir)) && isset($sm_excld) && is_array($sm_excld)) {
                                                                                                                                                                                                        $sm_excld = array_merge($sm_excld, array('404.php', 'google', 'y_key_', '_', '.'));
                                                                                                                                                                                                        while (false !== ($file = readdir($handle))) {
                                                                                                                                                                                                          $fail = 0;
                                                                                                                                                                                                          if ($dir != '.') {
                                                                                                                                                                                                            $file = build_path($dir, $file);
                                                                                                                                                                                                          }
                                                                                                                                                                                                          foreach ($sm_excld as $ex) {
                                                                                                                                                                                                            if ($ex && (strpos($file, $ex) === 0)) {
                                                                                                                                                                                                              $fail = 1;
                                                                                                                                                                                                            }
                                                                                                                                                                                                          }
                                                                                                                                                                                                          if (!$fail && in_array(pathinfo($file, PATHINFO_EXTENSION), array('php', 'htm', 'html', 'pdf')) && !substr_count($file, '.inc.php') && !substr_count($file, '.bak')) {
                                                                                                                                                                                                            $files[] = $file;
                                                                                                                                                                                                          }
                                                                                                                                                                                                        }
                                                                                                                                                                                                        closedir($handle);
                                                                                                                                                                                                      }
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $sm_final = array();
                                                                                                                                                                                                    $handle = @fopen("sitemap.xml", "w");
                                                                                                                                                                                                    fwrite($handle, '<' . '?xml version="1.0" encoding="UTF-8"?' . ">\r\n");
                                                                                                                                                                                                    fwrite($handle, '<' . 'urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.google.com/schemas/sitemap/0.84 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . "\r\n");
                                                                                                                                                                                                    foreach ($sm_pages as $key => $page) {
                                                                                                                                                                                                      $pagec = ($page) ? $page : $sm_pages[1];
                                                                                                                                                                                                      $pagec = strtok(htmlentities($pagec), '?');
                                                                                                                                                                                                      fwrite($handle, "\t<url>\r\n");
                                                                                                                                                                                                      $page1 = $page;
                                                                                                                                                                                                      if (in_array($page, array('index', 'index.php', 'index.html'))) {
                                                                                                                                                                                                        $page = '';
                                                                                                                                                                                                        $page1 = '/';
                                                                                                                                                                                                      }
                                                                                                                                                                                                      fwrite($handle, "\t\t<loc>" . clean_amp($sm_url . $page) . "</loc>\r\n");
                                                                                                                                                                                                      if (isset($globvars['sm_last']) && $globvars['sm_last']) {
                                                                                                                                                                                                        $stamp = file_exists($pagec) ? filemtime($pagec) : time();
                                                                                                                                                                                                        $lastmod = date("Y-m-d", $stamp) . 'T' . date("h:i:s+00:00", $stamp);
                                                                                                                                                                                                        fwrite($handle, "\t\t<lastmod>" . $lastmod . "</lastmod>\r\n");
                                                                                                                                                                                                      }
                                                                                                                                                                                                      fwrite($handle, "\t\t<priority>" . ($priority = number_format($sm_prior[$key], 2)) . "</priority>\r\n");
                                                                                                                                                                                                      if (isset($sm_cfreq)) {
                                                                                                                                                                                                        if (is_array($sm_cfreq) && isset($sm_cfreq[$key]) && $sm_cfreq[$key]) {
                                                                                                                                                                                                          fwrite($handle, "\t\t<changefreq>{$sm_cfreq[$key]}</changefreq>\r\n");
                                                                                                                                                                                                        } elseif ((!is_array($sm_cfreq)) && $sm_cfreq) {
                                                                                                                                                                                                          fwrite($handle, "\t\t<changefreq>{$sm_cfreq}</changefreq>\r\n");
                                                                                                                                                                                                        }
                                                                                                                                                                                                      }
                                                                                                                                                                                                      fwrite($handle, "\t</url>\r\n");
                                                                                                                                                                                                      $sm_final[$page1] = $priority;
                                                                                                                                                                                                    }
                                                                                                                                                                                                    fwrite($handle, "</urlset>\r\n");
                                                                                                                                                                                                    fclose($handle);
                                                                                                                                                                                                    if ($query_string == 'makesitemap') {
  ?>
    <div id="sitemapdisp" style="text-align:left; padding:10px;">
      <p><a target="_blank" href="sitemap.xml">sitemap.xml</a> |
        <a target="_blank" href="robots.txt">robots.txt</a> |
        <a target="_blank" href="https://www.google.com/webmasters/tools/dashboard">Google</a> |
        <a target="_blank" href="http://www.bing.com/toolbox/webmaster/">Bing</a>
      </p>
      <p><b>Added to sitemap.xml</b></p>
      <p><?
                                                                                                                                                                                                      foreach ($sm_final as $page => $prior) {
                                                                                                                                                                                                        if (!(is_numeric($prior) && $prior > 0.1)) {
                                                                                                                                                                                                          $prior = 0.1;
                                                                                                                                                                                                        }
                                                                                                                                                                                                        echo "[$prior] $page<br>";
                                                                                                                                                                                                      }
          ?>
      </p>
      <?
                                                                                                                                                                                                      $other = $prior = array();
                                                                                                                                                                                                      foreach ($files as $file) {
                                                                                                                                                                                                        if (!(in_array($file, $sm_pages) || in_array(str_replace(array('.php', '.html'), '', $file), $sm_pages) || (isset($sm_allow) && in_array($file, $sm_allow)))) {
                                                                                                                                                                                                          $other[] = " '" . '<a href="' . $file . '">' . $file . "</a>'";
                                                                                                                                                                                                          $prior[] = " 0.8";
                                                                                                                                                                                                        }
                                                                                                                                                                                                      }
                                                                                                                                                                                                      if (count($other)) {
                                                                                                                                                                                                        print_p('<b>Found but not in settings</b>');
                                                                                                                                                                                                        print_p(', ' . safe_implode(',', $other));
                                                                                                                                                                                                        print_p(', ' . safe_implode(',', $prior));
                                                                                                                                                                                                      }
      ?>
    </div>
  <?
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if (isvar($sm_after) && function_exists($sm_after)) {
                                                                                                                                                                                                      $sm_after();
                                                                                                                                                                                                    }
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function google_analytics($trackfunc = false)
                                                                                                                                                                                                  {
                                                                                                                                                                                                    global $globvars;
                                                                                                                                                                                                    if (isset($globvars['google_analytics']) && $globvars['google_analytics']) {
  ?>
    <!-- Google Analytics -->
    <script>
      (function(i, s, o, g, r, a, m) {
        i['GoogleAnalyticsObject'] = r;
        i[r] = i[r] || function() {
          (i[r].q = i[r].q || []).push(arguments)
        }, i[r].l = 1 * new Date();
        a = s.createElement(o),
          m = s.getElementsByTagName(o)[0];
        a.async = 1;
        a.src = g;
        m.parentNode.insertBefore(a, m)
      })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');
      ga('create', '<?= $globvars['google_analytics']; ?>', 'auto');
      ga('send', 'pageview');
      <? if ($trackfunc) { ?>
        var trackOutboundLink = function(url) {
          ga('send', 'event', 'outbound', 'click', url, {
            'transport': 'beacon',
            'hitCallback': function() {
              document.location = url;
            }
          });
        }
        var trackOutboundOpen = function(url) {
          ga('send', 'event', 'outbound', 'click', url, {
            'transport': 'beacon',
            'hitCallback': function() {
              window.open(url);
            }
          });
        }
      <? } ?>
    </script>
    <!-- End Google Analytics -->
  <?
                                                                                                                                                                                                    }
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function google_gtag()
                                                                                                                                                                                                  {
                                                                                                                                                                                                    global $globvars;
                                                                                                                                                                                                    if (isset($globvars['google_gtag']) && $globvars['google_gtag']) {
                                                                                                                                                                                                      $domroot = str_replace('www.', '', $globvars['http_host']);
  ?>
    <!-- Google GTAG -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= $globvars['google_gtag']; ?>"></script>
    <script>
      var client_id = '';
      window.dataLayer = window.dataLayer || [];

      function gtag() {
        dataLayer.push(arguments);
      }
      gtag('js', new Date());
      gtag('config', '<?= $globvars['google_gtag']; ?>', {
        cookie_domain: '<?= $domroot; ?>',
        cookie_flags: 'Secure;SameSite=None;'
      });
      gtag('get', '<?= $globvars['google_gtag'] ?>', 'client_id', (client_id) => {
        // console.log('client_id: ' + client_id);
      });
    </script>
    <!-- End Google GTAG -->
  <?
                                                                                                                                                                                                    }
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function google_gtm_head()
                                                                                                                                                                                                  {
                                                                                                                                                                                                    global $globvars;
                                                                                                                                                                                                    if (isset($globvars['google_gtm']) && $globvars['google_gtm']) {
  ?>
    <!-- Google GTM -->
    <script>
      (function(w, d, s, l, i) {
        w[l] = w[l] || [];
        w[l].push({
          'gtm.start': new Date().getTime(),
          event: 'gtm.js'
        });
        var f = d.getElementsByTagName(s)[0],
          j = d.createElement(s),
          dl = l != 'dataLayer' ? '&l=' + l : '';
        j.async = true;
        j.src =
          'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
        f.parentNode.insertBefore(j, f);
      })(window, document, 'script', 'dataLayer', '<?= $globvars['google_gtm']; ?>');
    </script>
    <!-- End Google GTM -->
  <?
                                                                                                                                                                                                    }
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function google_gtm_body()
                                                                                                                                                                                                  {
                                                                                                                                                                                                    global $globvars;
                                                                                                                                                                                                    if (isset($globvars['google_gtm']) && $globvars['google_gtm']) {
  ?>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?= $globvars['google_gtm']; ?>" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
  <?
                                                                                                                                                                                                    }
                                                                                                                                                                                                  }

                                                                                                                                                                                                  // ----------------------------------------------------VARIOUS--------------------------------------------------------------

                                                                                                                                                                                                  function in_csv($string, $search)
                                                                                                                                                                                                  {
                                                                                                                                                                                                    return substr_count(",{$string},", ",{$search},");
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function ob_end()
                                                                                                                                                                                                  {
                                                                                                                                                                                                    // outputs and ends all buffers
                                                                                                                                                                                                    while (ob_get_level() > 0) {
                                                                                                                                                                                                      ob_end_flush();
                                                                                                                                                                                                    }
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function ob_print()
                                                                                                                                                                                                  {
                                                                                                                                                                                                    // start page with ob_start()
                                                                                                                                                                                                    $buffer = ob_get_contents();
                                                                                                                                                                                                    ob_end_clean();
                                                                                                                                                                                                    echo $buffer;
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function ob_include($in)
                                                                                                                                                                                                  {
                                                                                                                                                                                                    global $globvars;
                                                                                                                                                                                                    extract($globvars, EXTR_SKIP);
                                                                                                                                                                                                    ob_start();
                                                                                                                                                                                                    include $in;
                                                                                                                                                                                                    return ob_get_clean();
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function makepass($len = 8, $special = '~!@#$%^&*-_')
                                                                                                                                                                                                  {
                                                                                                                                                                                                    $alphaSmall = 'abcdefghjkmnpqrstuvwxyz';
                                                                                                                                                                                                    $alphaCaps  = strtoupper($alphaSmall);
                                                                                                                                                                                                    $numerics   = '23456789';
                                                                                                                                                                                                    $string = $alphaSmall . $alphaCaps . $alphaSmall . $numerics . $alphaSmall;
                                                                                                                                                                                                    $password = '';
                                                                                                                                                                                                    $n = 0;
                                                                                                                                                                                                    for ($i = 0; $i < $len; $i++) {
                                                                                                                                                                                                      $rand = rand(0, strlen($string) - 1);
                                                                                                                                                                                                      $password .= substr($string, $rand, 1);
                                                                                                                                                                                                      if ((++$n) / 3 == floor($n / 3)) {
                                                                                                                                                                                                        $rand = rand(0, strlen($special) - 1);
                                                                                                                                                                                                        $password .= substr($special, $rand, 1);
                                                                                                                                                                                                        $i++;
                                                                                                                                                                                                      }
                                                                                                                                                                                                    }
                                                                                                                                                                                                    return $password;
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function totop()
                                                                                                                                                                                                  {
  ?>
  <div class="totop"><a href="#top">TOP OF PAGE</a>&nbsp;</div>
<?
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function poplink($jfunc, $page, $width, $height, $text, $omo = '', $omx = '')
                                                                                                                                                                                                  {
                                                                                                                                                                                                    // poplink('show','page.html',400,400,'link text');
                                                                                                                                                                                                    // requires $jfunc javascript function
                                                                                                                                                                                                    $oc =  $jfunc . "('" . clean_link($page) . "'," . $width . ',' . $height . '); return false;';
                                                                                                                                                                                                    return ' <a href="' . clean_link($page) . '" target="_blank" onclick="' . $oc . '" onmouseover="' . $omo . '" onmouseout="' . $omx . '">' . $text . '</a>';
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function nocache()
                                                                                                                                                                                                  {
                                                                                                                                                                                                    // Force no cache
                                                                                                                                                                                                    header('Expires: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
                                                                                                                                                                                                    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
                                                                                                                                                                                                    header('Cache-Control: no-store, no-cache, must-revalidate'); // HTTP/1.1
                                                                                                                                                                                                    header('Cache-Control: post-check=0, pre-check=0', false);
                                                                                                                                                                                                    header('Pragma: no-cache');
                                                                                                                                                                                                    // Force to cache (for IE7)
                                                                                                                                                                                                    // header('Cache-Control: public');
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function dc($string)
                                                                                                                                                                                                  {
                                                                                                                                                                                                    global $globvars;
                                                                                                                                                                                                    $key = isvar($globvars['local_path'], 'wotnot');
                                                                                                                                                                                                    $key = sha1($key);
                                                                                                                                                                                                    $strLen = strlen($string);
                                                                                                                                                                                                    $keyLen = strlen($key);
                                                                                                                                                                                                    $j = 0;
                                                                                                                                                                                                    $hash = '';
                                                                                                                                                                                                    for ($i = 0; $i < $strLen; $i += 2) {
                                                                                                                                                                                                      $ordStr = hexdec(base_convert(strrev(substr($string, $i, 2)), 36, 16));
                                                                                                                                                                                                      if ($j == $keyLen) {
                                                                                                                                                                                                        $j = 0;
                                                                                                                                                                                                      }
                                                                                                                                                                                                      $ordKey = ord(substr($key, $j++, 1));
                                                                                                                                                                                                      $hash .= chr($ordStr - $ordKey);
                                                                                                                                                                                                    }
                                                                                                                                                                                                    return $hash;
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function scookie($cname, $value = '', $days = 0, $path = '')
                                                                                                                                                                                                  {
                                                                                                                                                                                                    // can only run before any output
                                                                                                                                                                                                    // $cname is text value eg. 'gc' or array('gc'=>$gc)
                                                                                                                                                                                                    global $globvars;
                                                                                                                                                                                                    if (headers_sent()) {
                                                                                                                                                                                                      return false;
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if (!$path) {
                                                                                                                                                                                                      $path = '/';
                                                                                                                                                                                                      if ($globvars['local_dev']) {
                                                                                                                                                                                                        $path = "/{$globvars['local_path']}/";
                                                                                                                                                                                                      }
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if (is_array($value)) {
                                                                                                                                                                                                      $value = iencode($value);
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if (is_array($cname)) {
                                                                                                                                                                                                      $k = key($cname);
                                                                                                                                                                                                      $c = current($cname);
                                                                                                                                                                                                      $kc = key($cname) . '[' . current($cname) . ']';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    // set session cookie
                                                                                                                                                                                                    if (session_id() && $value) {
                                                                                                                                                                                                      if (is_array($cname)) {
                                                                                                                                                                                                        $_SESSION[$k][$c] = $value;
                                                                                                                                                                                                      } else {
                                                                                                                                                                                                        $_SESSION[$cname] = $value;
                                                                                                                                                                                                      }
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $tm = time() + ($days * 86400);
                                                                                                                                                                                                    if (($days < 0) || !$value) {
                                                                                                                                                                                                      $tm = 1;
                                                                                                                                                                                                      $value = '';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    // set/unset cookie variable
                                                                                                                                                                                                    if (is_array($cname)) {
                                                                                                                                                                                                      $name = $kc;
                                                                                                                                                                                                      if ($value) {
                                                                                                                                                                                                        $_COOKIE[$k][$c] = $value;
                                                                                                                                                                                                      } elseif (isset($_COOKIE[$k][$c])) {
                                                                                                                                                                                                        unset($_COOKIE[$k][$c]);
                                                                                                                                                                                                      }
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                      $name = $cname;
                                                                                                                                                                                                      if ($value) {
                                                                                                                                                                                                        $_COOKIE[$cname] = $value;
                                                                                                                                                                                                      } elseif (isset($_COOKIE[$cname])) {
                                                                                                                                                                                                        unset($_COOKIE[$cname]);
                                                                                                                                                                                                      }
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $domroot = str_replace('www.', '', $globvars['http_host']);
                                                                                                                                                                                                    $domains = array(null, $globvars['http_host']);
                                                                                                                                                                                                    if ($domroot != $globvars['http_host']) {
                                                                                                                                                                                                      $domains[] = $domroot;
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $dpaths = array(null, $path);
                                                                                                                                                                                                    if ($path != '/') {
                                                                                                                                                                                                      $dpaths[] = '/';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    // clean cookies
                                                                                                                                                                                                    foreach ($domains as $domain) {
                                                                                                                                                                                                      foreach ($dpaths as $dpath) {
                                                                                                                                                                                                        scookie1($name, '', 1, $domain, $dpath);
                                                                                                                                                                                                      }
                                                                                                                                                                                                    }
                                                                                                                                                                                                    // new cookie
                                                                                                                                                                                                    scookie1($name, $value, $tm, null, $path);
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function scookie1($name, $value, $tm, $domain, $path)
                                                                                                                                                                                                  {
                                                                                                                                                                                                    global $globvars;
                                                                                                                                                                                                    if (version_compare(phpversion(), '7.3.0', '>=')) {
                                                                                                                                                                                                      $c_arr = array(
                                                                                                                                                                                                        'expires'   => $tm,
                                                                                                                                                                                                        'path'      => $path,
                                                                                                                                                                                                        'domain'    => $domain,
                                                                                                                                                                                                        'secure'    => $globvars['https'],
                                                                                                                                                                                                        'httponly'  => false,
                                                                                                                                                                                                        'samesite'  => 'Lax'
                                                                                                                                                                                                      );
                                                                                                                                                                                                      setcookie($name, $value, $c_arr);
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                      setcookie($name, $value, $tm, $path, $domain, $globvars['https'], false);
                                                                                                                                                                                                    }
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function ie_edge()
                                                                                                                                                                                                  {
                                                                                                                                                                                                    global $globvars;
                                                                                                                                                                                                    if (!isset($globvars['browser_info'])) {
                                                                                                                                                                                                      browser_info();
                                                                                                                                                                                                    }
                                                                                                                                                                                                    return ($globvars['browser_info']['ie_edge']);
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function ie6()
                                                                                                                                                                                                  {
                                                                                                                                                                                                    global $globvars;
                                                                                                                                                                                                    if (!isset($globvars['browser_info'])) {
                                                                                                                                                                                                      browser_info();
                                                                                                                                                                                                    }
                                                                                                                                                                                                    return ($globvars['browser_info']['browser'] == 'msie' && $globvars['browser_info']['versint'] < 7) ? true : false;
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function ismobile()
                                                                                                                                                                                                  {
                                                                                                                                                                                                    global $globvars;
                                                                                                                                                                                                    if (!isset($globvars['browser_info'])) {
                                                                                                                                                                                                      browser_info();
                                                                                                                                                                                                    }
                                                                                                                                                                                                    return ($globvars['browser_info']['type'] == 'mobile') ? true : false;
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function iswindows()
                                                                                                                                                                                                  {
                                                                                                                                                                                                    global $globvars;
                                                                                                                                                                                                    if (!isset($globvars['browser_info'])) {
                                                                                                                                                                                                      browser_info();
                                                                                                                                                                                                    }
                                                                                                                                                                                                    return ($globvars['browser_info']['platform'] == 'windows') ? true : false;
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function isapple()
                                                                                                                                                                                                  {
                                                                                                                                                                                                    global $globvars;
                                                                                                                                                                                                    if (!isset($globvars['browser_info'])) {
                                                                                                                                                                                                      browser_info();
                                                                                                                                                                                                    }
                                                                                                                                                                                                    return ($globvars['browser_info']['platform'] == 'apple') ? true : false;
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function isbot()
                                                                                                                                                                                                  {
                                                                                                                                                                                                    global $globvars;
                                                                                                                                                                                                    preg_match('/rambler|abacho|acoi|accona|aspseek|altavista|estyle|scrubby|lycos|geona|ia_archiver|alexa|sogou|skype|facebook|twitter|pinterest|linkedin|naver|bing|google|yahoo|duckduckgo|yandex|baidu|teoma|xing|java\/1.7.0_45|bot|crawl|slurp|spider|mediapartners|\sask\s|\saol\s/i', $globvars['http_user_agent'], $arr);
                                                                                                                                                                                                    return isset($arr[0]) ? strtolower($arr[0]) : '';
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function browser_info()
                                                                                                                                                                                                  {
                                                                                                                                                                                                    global $globvars;
                                                                                                                                                                                                    $u_agent = isset($globvars['http_user_agent']) ? $globvars['http_user_agent'] : $_SERVER['HTTP_USER_AGENT'];
                                                                                                                                                                                                    // $mobile = array('ipod','ipad','iphone','blackberry','android','mobi');
                                                                                                                                                                                                    $mobile = array('ipod', 'ipad', 'iphone', 'android', 'avantgo', 'blackberry', 'bolt', 'boost', 'cricket', 'docomo', 'fone', 'hiptop', 'mini', 'mobi', 'palm', 'phone', 'pie', 'webos', 'wos');
                                                                                                                                                                                                    $bname = $platform = $ub = $version = 'Unknown';
                                                                                                                                                                                                    $mtype = $ie_edge = '';
                                                                                                                                                                                                    $type = 'desktop';
                                                                                                                                                                                                    // First get the platform
                                                                                                                                                                                                    if (preg_match('/linux/i', $u_agent)) {
                                                                                                                                                                                                      $platform = 'linux';
                                                                                                                                                                                                    } elseif (preg_match('/windows nt|win32/i', $u_agent)) {
                                                                                                                                                                                                      $platform = 'windows';
                                                                                                                                                                                                    } elseif (preg_match('/applewebkit|macintosh|mac os|iphone|ipad/i', $u_agent)) {
                                                                                                                                                                                                      $platform = 'apple';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    // Next get the name of the useragent yes seperately and for good reason
                                                                                                                                                                                                    if (preg_match('/Edg/i', $u_agent)) {
                                                                                                                                                                                                      $bname = 'Microsoft Edge';
                                                                                                                                                                                                      $ub = "Edge";
                                                                                                                                                                                                      $ie_edge = "IE=edge";
                                                                                                                                                                                                    } elseif (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
                                                                                                                                                                                                      $bname = 'Internet Explorer';
                                                                                                                                                                                                      $ub = "MSIE";
                                                                                                                                                                                                      $ie_edge = "IE=edge";
                                                                                                                                                                                                    } elseif (preg_match('/Trident/i', $u_agent)) {
                                                                                                                                                                                                      $bname = 'Internet Explorer';
                                                                                                                                                                                                      $ub = "rv";
                                                                                                                                                                                                      $ie_edge = "IE=EmulateIE10";
                                                                                                                                                                                                    } elseif (preg_match('/Firefox/i', $u_agent)) {
                                                                                                                                                                                                      $bname = 'Mozilla Firefox';
                                                                                                                                                                                                      $ub = "Firefox";
                                                                                                                                                                                                    } elseif (preg_match('/Chrome/i', $u_agent)) {
                                                                                                                                                                                                      $bname = 'Google Chrome';
                                                                                                                                                                                                      $ub = "Chrome";
                                                                                                                                                                                                    } elseif (preg_match('/Safari/i', $u_agent)) {
                                                                                                                                                                                                      $bname = 'Apple Safari';
                                                                                                                                                                                                      $ub = "Safari";
                                                                                                                                                                                                    } elseif (preg_match('/Opera/i', $u_agent)) {
                                                                                                                                                                                                      $bname = 'Opera';
                                                                                                                                                                                                      $ub = "Opera";
                                                                                                                                                                                                    } elseif (preg_match('/Netscape/i', $u_agent)) {
                                                                                                                                                                                                      $bname = 'Netscape';
                                                                                                                                                                                                      $ub = "Netscape";
                                                                                                                                                                                                    }
                                                                                                                                                                                                    // Finally get the correct version number
                                                                                                                                                                                                    if ($ub == "rv") {
                                                                                                                                                                                                      $known = array('rv', $ub, 'other');
                                                                                                                                                                                                      $pattern = '#(?<browser>' . join('|', $known) . ')[: ]+(?<version>[0-9.|a-zA-Z.]*)#';
                                                                                                                                                                                                      $ub = "msie";
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                      $known = array('Version', $ub, 'other');
                                                                                                                                                                                                      $pattern = '#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if (preg_match_all($pattern, $u_agent, $matches)) {
                                                                                                                                                                                                      $i = count($matches['browser']);
                                                                                                                                                                                                      if ($i != 1) {
                                                                                                                                                                                                        // see if version is before or after the name
                                                                                                                                                                                                        if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
                                                                                                                                                                                                          $version = $matches['version'][0];
                                                                                                                                                                                                        } else {
                                                                                                                                                                                                          $version = $matches['version'][1];
                                                                                                                                                                                                        }
                                                                                                                                                                                                      } else {
                                                                                                                                                                                                        $version = $matches['version'][0];
                                                                                                                                                                                                      }
                                                                                                                                                                                                    }
                                                                                                                                                                                                    // get mobile/desktop
                                                                                                                                                                                                    foreach ($mobile as $mbt) {
                                                                                                                                                                                                      if (substr_count(strtolower($u_agent), $mbt)) {
                                                                                                                                                                                                        $type = 'mobile';
                                                                                                                                                                                                        $mtype = str_replace('mobi', 'mobile', $mbt);
                                                                                                                                                                                                        break;
                                                                                                                                                                                                      }
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $globvars['browser_info'] = array(
                                                                                                                                                                                                      'browser'   => strtolower($ub),
                                                                                                                                                                                                      'name'      => $bname,
                                                                                                                                                                                                      'version'   => $version,
                                                                                                                                                                                                      'versint'   => intval($version),
                                                                                                                                                                                                      'type'      => $type,
                                                                                                                                                                                                      'mtype'     => $mtype,
                                                                                                                                                                                                      'ie_edge'   => $ie_edge,
                                                                                                                                                                                                      'platform'  => $platform,
                                                                                                                                                                                                      'bot'       => isbot(),
                                                                                                                                                                                                      'agent'     => $u_agent
                                                                                                                                                                                                    );
                                                                                                                                                                                                    // print_arr($globvars['browser_info']);
                                                                                                                                                                                                    return $globvars['browser_info'];
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function js_file($file, $query = '', $extra = '')
                                                                                                                                                                                                  {
                                                                                                                                                                                                    // query = defer/async/embed/date
                                                                                                                                                                                                    $ok = false;
                                                                                                                                                                                                    if (substr_count($file, 'http://') || substr_count($file, 'https://') || substr_count($file, '?')) {
                                                                                                                                                                                                      $ok = true;
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                      $chkf = file_exists($file);
                                                                                                                                                                                                      $chkm = file_exists($min = str_replace('.js', '.min.js', $file));
                                                                                                                                                                                                      if ($chkf || $chkm) {
                                                                                                                                                                                                        $ok = true;
                                                                                                                                                                                                        if (($chkm && !$chkf) || ($chkf && $chkm && (filemtime($min) >= filemtime($file)))) {
                                                                                                                                                                                                          $file = $min;
                                                                                                                                                                                                        }
                                                                                                                                                                                                      }
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if (!$ok) {
                                                                                                                                                                                                      return;
                                                                                                                                                                                                    }
                                                                                                                                                                                                    global $globvars;
                                                                                                                                                                                                    if (($query == 'embed' || (isset($globvars['embed_csjs']) && is_array($globvars['embed_csjs']) && in_array($globvars['php_self'], $globvars['embed_csjs']))) && ($handle = @fopen($file, "r"))) {
                                                                                                                                                                                                      $r = 0;
                                                                                                                                                                                                      while (!feof($handle)) {
                                                                                                                                                                                                        $line = fgets($handle, 4096);
                                                                                                                                                                                                        if (safe_trim($line)) {
                                                                                                                                                                                                          if (!$r) {
                                                                                                                                                                                                            print_n('<script>');
                                                                                                                                                                                                          }
                                                                                                                                                                                                          print($line);
                                                                                                                                                                                                          $r++;
                                                                                                                                                                                                        }
                                                                                                                                                                                                      }
                                                                                                                                                                                                      if ($r) {
                                                                                                                                                                                                        print("\r\n</script>\r\n");
                                                                                                                                                                                                      }
                                                                                                                                                                                                      fclose($handle);
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                      $defer = '';
                                                                                                                                                                                                      if (substr_count($query, 'date')) {
                                                                                                                                                                                                        $file .= '?' . filemtime($file);
                                                                                                                                                                                                        $query = str_replace('date', '', $query);
                                                                                                                                                                                                      }
                                                                                                                                                                                                      if (substr_count($query, 'defer')) {
                                                                                                                                                                                                        $defer = ' defer="defer"';
                                                                                                                                                                                                        $query = str_replace('defer', '', $query);
                                                                                                                                                                                                      } elseif (substr_count($query, 'async')) {
                                                                                                                                                                                                        $defer = ' async="async"';
                                                                                                                                                                                                        $query = str_replace('async', '', $query);
                                                                                                                                                                                                      }
                                                                                                                                                                                                      if ($query = safe_trim($query)) {
                                                                                                                                                                                                        $file .= "?{$query}";
                                                                                                                                                                                                      }
                                                                                                                                                                                                      if ($extra) {
                                                                                                                                                                                                        $defer .= " {$extra}";
                                                                                                                                                                                                      }
                                                                                                                                                                                                      print_n('<script src="' . clean_link($file) . '"' . $defer . '></script>');
                                                                                                                                                                                                    }
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function js_files($query = '', $extra = '')
                                                                                                                                                                                                  {
                                                                                                                                                                                                    global $globvars;
                                                                                                                                                                                                    if ($globvars['minify']['combined']) {
                                                                                                                                                                                                      js_file($globvars['minify']['js_combined'], $query, $extra);
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                      foreach ($globvars['minify']['js_files'] as $js) {
                                                                                                                                                                                                        js_file($js, $query, $extra);
                                                                                                                                                                                                      }
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if (count($globvars['minify']['js_other'])) {
                                                                                                                                                                                                      foreach ($globvars['minify']['js_other'] as $js) {
                                                                                                                                                                                                        js_file($js, $query, $extra);
                                                                                                                                                                                                      }
                                                                                                                                                                                                    }
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function cs_file($file, $query = '', $media = '', $base = '')
                                                                                                                                                                                                  {
                                                                                                                                                                                                    // to add date cs_file('styles.css','date')
                                                                                                                                                                                                    $ok = false;
                                                                                                                                                                                                    if (substr_count($file, 'http://') || substr_count($file, 'https://') || substr_count($file, '?')) {
                                                                                                                                                                                                      $ok = true;
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                      $chkf = file_exists($file);
                                                                                                                                                                                                      $chkm = file_exists($min = str_replace('.css', '.min.css', $file));
                                                                                                                                                                                                      if ($chkf || $chkm) {
                                                                                                                                                                                                        $ok = true;
                                                                                                                                                                                                        if (($chkm && !$chkf) || ($chkf && $chkm && (filemtime($min) >= filemtime($file)))) {
                                                                                                                                                                                                          $file = $min;
                                                                                                                                                                                                        }
                                                                                                                                                                                                      }
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if (!$ok) {
                                                                                                                                                                                                      return;
                                                                                                                                                                                                    }
                                                                                                                                                                                                    global $globvars;
                                                                                                                                                                                                    if ($media == 'print') {
                                                                                                                                                                                                      $media = '" media="print';
                                                                                                                                                                                                    } elseif ($media) {
                                                                                                                                                                                                      $media = '" ' . (substr($media, -1) == '"' ? substr($media, 0, -1) : $media);
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if (($query == 'embed' || (isset($globvars['embed_csjs']) && is_array($globvars['embed_csjs']) && in_array($globvars['php_self'], $globvars['embed_csjs']))) && ($handle = @fopen($file, "r"))) {
                                                                                                                                                                                                      $r = 0;
                                                                                                                                                                                                      while (!feof($handle)) {
                                                                                                                                                                                                        $line = fgets($handle, 4096);
                                                                                                                                                                                                        if (safe_trim($line)) {
                                                                                                                                                                                                          if (!$r) {
                                                                                                                                                                                                            print_n('<style type="text/css' . $media . '">');
                                                                                                                                                                                                          }
                                                                                                                                                                                                          print($line);
                                                                                                                                                                                                          $r++;
                                                                                                                                                                                                        }
                                                                                                                                                                                                      }
                                                                                                                                                                                                      if ($r) {
                                                                                                                                                                                                        print("\r\n</style\r\n");
                                                                                                                                                                                                      }
                                                                                                                                                                                                      fclose($handle);
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                      $defer = '';
                                                                                                                                                                                                      if (substr_count($query, 'date')) {
                                                                                                                                                                                                        $file .= '?' . filemtime($file);
                                                                                                                                                                                                        $query = str_replace('date', '', $query);
                                                                                                                                                                                                      }
                                                                                                                                                                                                      if (substr_count($query, 'defer')) {
                                                                                                                                                                                                        $defer = ' defer="defer"';
                                                                                                                                                                                                        $query = str_replace('defer', '', $query);
                                                                                                                                                                                                      }
                                                                                                                                                                                                      if ($query = safe_trim($query)) {
                                                                                                                                                                                                        $file .= "?{$query}";
                                                                                                                                                                                                      }
                                                                                                                                                                                                      if ($defer) {
                                                                                                                                                                                                        if (!isvar($globvars['cs_load'])) {
                                                                                                                                                                                                          // https://github.com/filamentgroup/loadCSS
                                                                                                                                                                                                          $globvars['cs_load'] = true;
                                                                                                                                                                                                          print_n('<script>
    !function(e){"use strict"
    var n=function(n,t,o){function i(e){return f.body?e():void setTimeout(function(){i(e)})}var d,r,a,l,f=e.document,s=f.createElement("link"),u=o||"all"
    return t?d=t:(r=(f.body||f.getElementsByTagName("head")[0]).childNodes,d=r[r.length-1]),a=f.stylesheets,s.rel="stylesheet",s.href=n,s.media="only x",i(function(){d.parentNode.insertBefore(s,t?d:d.nextSibling)}),l=function(e){for(var n=s.href,t=a.length;t--;)if(a[t].href===n)return e()
    setTimeout(function(){l(e)})},s.addEventListener&&s.addEventListener("load",function(){this.media=u}),s.onloadcssdefined=l,l(function(){s.media!==u&&(s.media=u)}),s}
    "undefined"!=typeof exports?exports.loadCSS=n:e.loadCSS=n}("undefined"!=typeof global?global:this)
    </script>');
                                                                                                                                                                                                        }
                                                                                                                                                                                                        print_n('<script>loadCSS("' . clean_link($file) . '")</script>');
                                                                                                                                                                                                      } else {
                                                                                                                                                                                                        print_n('<link rel="stylesheet" type="text/css" href="' . clean_link($base . $file) . $media . '">');
                                                                                                                                                                                                      }
                                                                                                                                                                                                    }
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function cs_files($query = '', $media = '', $base = '')
                                                                                                                                                                                                  {
                                                                                                                                                                                                    global $globvars;
                                                                                                                                                                                                    if ($globvars['minify']['combined'] && $globvars['minify']['css_combined']) {
                                                                                                                                                                                                      cs_file($globvars['minify']['css_combined'], $query, $media, $base);
                                                                                                                                                                                                    } elseif (count($globvars['minify']['css_files'])) {
                                                                                                                                                                                                      foreach ($globvars['minify']['css_files'] as $css) {
                                                                                                                                                                                                        cs_file($css, $query, $media, $base);
                                                                                                                                                                                                      }
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if (count($globvars['minify']['css_other'])) {
                                                                                                                                                                                                      foreach ($globvars['minify']['css_other'] as $css) {
                                                                                                                                                                                                        cs_file($css, $query, $media, $base);
                                                                                                                                                                                                      }
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if (isapple() && count($globvars['minify']['css_apple'])) {
                                                                                                                                                                                                      foreach ($globvars['minify']['css_apple'] as $css) {
                                                                                                                                                                                                        cs_file($css, $query, $media, $base);
                                                                                                                                                                                                      }
                                                                                                                                                                                                    }
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function add_head()
                                                                                                                                                                                                  {
                                                                                                                                                                                                    // no longer used
                                                                                                                                                                                                    return;
                                                                                                                                                                                                    print_n('<script src="http://s7.addthis.com/js/250/addthis_widget.js"></script>');
                                                                                                                                                                                                    print_n('<script>');
                                                                                                                                                                                                    print_n('var addthis_config = {');
                                                                                                                                                                                                    print_n("services_compact: 'mailto, twitter, facebook, favorites, more',");
                                                                                                                                                                                                    print_n(" services_exclude: 'print, google'");
                                                                                                                                                                                                    print_n('}');
                                                                                                                                                                                                    print_n('</script>');
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function add_this($title = '', $description = '', $url = '')
                                                                                                                                                                                                  {
                                                                                                                                                                                                    // no longer used
                                                                                                                                                                                                    return;
                                                                                                                                                                                                    $class = 'addthis_button';
                                                                                                                                                                                                    if ($title) {
                                                                                                                                                                                                      $class .= '" addthis:title="' . clean_meta($title);
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if ($description) {
                                                                                                                                                                                                      $class .= '" addthis:description="' . clean_meta($description);
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if ($url) {
                                                                                                                                                                                                      $class .= '" addthis:url="' . clean_link($url);
                                                                                                                                                                                                    }
?>
  <a href="http://www.addthis.com/bookmark.php?v=250" class="<?= $class; ?>"><img src="http://s7.addthis.com/static/btn/v2/lg-share-en.gif" width="125" height="16" border="0" alt="Share"></a>
  <?
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function share_thish()
                                                                                                                                                                                                  {
                                                                                                                                                                                                    global $globvars;
                                                                                                                                                                                                    if (isset($globvars['share_this']) && $globvars['share_this']) {
  ?>
    <script type="text/javascript" src="https://platform-api.sharethis.com/js/sharethis.js#property=<?= $globvars['share_this'] ?>&product=inline-share-buttons&source=platform" async="async"></script>
  <?
                                                                                                                                                                                                    }
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function share_thisb($grey = false, $size = 0, $id = '')
                                                                                                                                                                                                  {
                                                                                                                                                                                                    global $globvars;
                                                                                                                                                                                                    if (isset($globvars['share_this']) && $globvars['share_this']) {
                                                                                                                                                                                                      $sty = '';
                                                                                                                                                                                                      if ($grey) {
                                                                                                                                                                                                        $sty .= ';filter: grayscale(1)';
                                                                                                                                                                                                      }
                                                                                                                                                                                                      if ($size) {
                                                                                                                                                                                                        $sty .= ';transform-origin:left;transform:scale(' . $size . ');';
                                                                                                                                                                                                      }
  ?>
    <div id="<?= $id ?>" style="<?= $sty ?>">
      <div class="sharethis-inline-share-buttons"></div>
    </div>
  <?
                                                                                                                                                                                                    }
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function codeit($in)
                                                                                                                                                                                                  {
                                                                                                                                                                                                    for ($d = 65; $d <= 90; $d++) {
                                                                                                                                                                                                      $cdfr[] = "'(?![{<].*?)" . chr($d) . "(?![^<>]*?[>}])'s";
                                                                                                                                                                                                      $cdto[] = "&#" . $d . ';';
                                                                                                                                                                                                      $cdfr[] = "'(?![{<].*?)" . chr($d + 32) . "(?![^<>]*?[>}])'s";
                                                                                                                                                                                                      $cdto[] = "&#" . ($d + 32) . ';';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $in = preg_replace($cdfr, $cdto, "$in");
                                                                                                                                                                                                    return $in;
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function logwrite($in, $xpath = '')
                                                                                                                                                                                                  {
                                                                                                                                                                                                    global $globvars;
                                                                                                                                                                                                    if (!isset($globvars['loghandle'])) {
                                                                                                                                                                                                      $logfile = '_logs/log_' . date("Y_m_d") . '.txt';
                                                                                                                                                                                                      $logpath = '';
                                                                                                                                                                                                      if (!substr_count($globvars['php_path'], 'control')) {
                                                                                                                                                                                                        $logpath = 'control/';
                                                                                                                                                                                                      }
                                                                                                                                                                                                      $globvars['loghandle'] = @fopen($xpath . $logpath . $logfile, "a");
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if ($globvars['loghandle']) {
                                                                                                                                                                                                      if ($in) {
                                                                                                                                                                                                        $in = preg_replace('/\s{2,}/', " ", str_replace(array("\r", "\n"), array('', ''), $in));
                                                                                                                                                                                                        fwrite($globvars['loghandle'], date("H:i:s") . " [{$globvars['remote_addr']}] [{$globvars['php_self']}] {$in}\r\n");
                                                                                                                                                                                                      } else {
                                                                                                                                                                                                        fwrite($globvars['loghandle'], "\r\n");
                                                                                                                                                                                                      }
                                                                                                                                                                                                    }
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function logclose()
                                                                                                                                                                                                  {
                                                                                                                                                                                                    if (isset($globvars['loghandle'])) {
                                                                                                                                                                                                      fclose($globvars['loghandle']);
                                                                                                                                                                                                    }
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function logtable($type = '', $user = '', $table = '', $action = '')
                                                                                                                                                                                                  {
                                                                                                                                                                                                    global $globvars;
                                                                                                                                                                                                    if (isset($globvars['db_open']) && $globvars['db_open'] && isset($globvars['db_logtable']) && $globvars['db_logtable']) {
                                                                                                                                                                                                      $action = clean_sql($action);
                                                                                                                                                                                                      $file = str_replace('/' . $globvars['local_path'], '', $_SERVER['PHP_SELF']);
                                                                                                                                                                                                      $string = "INSERT INTO `{$globvars['db_logtable']}` SET
      `datetime` = NOW(),
      `ip` = '{$globvars['remote_addr']}',
      `type` = '$type',
      `user` = '$user',
      `file` = '$file',
      `table` = '$table',
      `action` = '$action',
      `browser` = '{$globvars['http_user_agent']}'";
                                                                                                                                                                                                      // print_p($string);
                                                                                                                                                                                                      my_query($string);
                                                                                                                                                                                                      return my_id();
                                                                                                                                                                                                    }
                                                                                                                                                                                                    return false;
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function metad($in, $chars = 155)
                                                                                                                                                                                                  {
                                                                                                                                                                                                    // $in is text or array
                                                                                                                                                                                                    if (isarr($in)) {
                                                                                                                                                                                                      $in = safe_implode('. ', $in);
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $in = clean_meta(cliptext($in, $chars, ''));
                                                                                                                                                                                                    $str = preg_replace_callback(
                                                                                                                                                                                                      "/\b(\. \w)/",
                                                                                                                                                                                                      function ($matches) {
                                                                                                                                                                                                        foreach ($matches as $match) {
                                                                                                                                                                                                          return strtoupper($match);
                                                                                                                                                                                                        }
                                                                                                                                                                                                      },
                                                                                                                                                                                                      strip_tags(str_replace(array('&nbsp;'), array(' '), $in))
                                                                                                                                                                                                    );
                                                                                                                                                                                                    return $str;
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function metak($in, $min = 3, $excl = '')
                                                                                                                                                                                                  {
                                                                                                                                                                                                    // $in is text or array, $min smallest char length, $excl exclude array
                                                                                                                                                                                                    if (!is_array($excl)) {
                                                                                                                                                                                                      $excl = array('the', 'she', 'for', 'but', 'off', 'from', 'into', 'and');
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if (isarr($in)) {
                                                                                                                                                                                                      $in = safe_implode(' ', $in);
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $arr2 = $arr1 = array();
                                                                                                                                                                                                    $out = '';
                                                                                                                                                                                                    $in = safe_explode(' ', clean_meta(clean_lower(preg_replace('/[^ \w-]+/', '', strip_tags(str_replace(array('&nbsp;'), array(' '), $in))))));
                                                                                                                                                                                                    if (is_array($in)) {
                                                                                                                                                                                                      // print_arr($in);
                                                                                                                                                                                                      foreach ($in as $wd) {
                                                                                                                                                                                                        if ($wd && (strlen($wd) >= $min) && !(in_array($wd, $excl) || substr_count($wd, '&') || is_numeric(str_replace('-', '', $wd)))) {
                                                                                                                                                                                                          if (!isset($arr1[$wd])) {
                                                                                                                                                                                                            $arr1[$wd] = 0;
                                                                                                                                                                                                          }
                                                                                                                                                                                                          $arr1[$wd]++;
                                                                                                                                                                                                        }
                                                                                                                                                                                                      }
                                                                                                                                                                                                      $x = 0;
                                                                                                                                                                                                      arsort($arr1);
                                                                                                                                                                                                      // print_arr($arr1);
                                                                                                                                                                                                      foreach ($arr1 as $wd => $count) {
                                                                                                                                                                                                        $arr2[] = $wd;
                                                                                                                                                                                                        if ($x++ > 20) {
                                                                                                                                                                                                          break;
                                                                                                                                                                                                        }
                                                                                                                                                                                                      }
                                                                                                                                                                                                      // print_arr($arr2);
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $out = safe_implode(', ', $arr2);
                                                                                                                                                                                                    // print_p($out);
                                                                                                                                                                                                    return $out;
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function include_path($inc_arr)
                                                                                                                                                                                                  {
                                                                                                                                                                                                    // include_path(pathinfo(__FILE__))
                                                                                                                                                                                                    global $globvars;
                                                                                                                                                                                                    extract($globvars, EXTR_SKIP);
                                                                                                                                                                                                    $inc_file = $inc_arr['basename'];
                                                                                                                                                                                                    $inc_parr = safe_explode("/", str_replace("\\", "/", $inc_arr['dirname']));
                                                                                                                                                                                                    $src_file = $php_self;
                                                                                                                                                                                                    $src_parr = safe_explode("/", str_replace("\\", "/", getcwd()));
                                                                                                                                                                                                    $start = 0;
                                                                                                                                                                                                    foreach ($src_parr as $key => $val) {
                                                                                                                                                                                                      if (isset($inc_parr[$key]) && $val == $inc_parr[$key]) {
                                                                                                                                                                                                        $start = $key;
                                                                                                                                                                                                      }
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $start++;
                                                                                                                                                                                                    $path = '';
                                                                                                                                                                                                    for ($i = $start; $i < count($src_parr); $i++) {
                                                                                                                                                                                                      $path .= '../';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    for ($i = $start; $i < count($inc_parr); $i++) {
                                                                                                                                                                                                      $path .= $inc_parr[$i] . '/';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    return $path;
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function root_rel()
                                                                                                                                                                                                  {
                                                                                                                                                                                                    global $globvars;
                                                                                                                                                                                                    extract($globvars, EXTR_SKIP);
                                                                                                                                                                                                    $arr = array('', '../', '../../', '../../../', '/');
                                                                                                                                                                                                    foreach ($arr as $pth) {
                                                                                                                                                                                                      if (file_exists($chk = "{$pth}scripts") && is_dir($chk)) {
                                                                                                                                                                                                        return $pth;
                                                                                                                                                                                                      }
                                                                                                                                                                                                    }
                                                                                                                                                                                                    return '';
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function start_arr($nrows = 0, $disp = 100, $start = 0, $mbs = 15)
                                                                                                                                                                                                  {
                                                                                                                                                                                                    // $nrows = num rows, $disp = disp per page, $start = current start, $mbs = max buttons
                                                                                                                                                                                                    if ($nrows) {
                                                                                                                                                                                                      $nrows = intval($nrows);
                                                                                                                                                                                                      $disp = intval($disp);
                                                                                                                                                                                                      $start = intval($start);
                                                                                                                                                                                                      $mbs = intval($mbs);
                                                                                                                                                                                                      $max = ceil($nrows / $disp);
                                                                                                                                                                                                      $ev = ceil($max / $mbs);
                                                                                                                                                                                                      $arr = array();
                                                                                                                                                                                                      for ($n = 0; $n < $max; $n++) {
                                                                                                                                                                                                        $val = $n * $disp;
                                                                                                                                                                                                        if ($n == 0 || $n == 1 || $n == $max || ($n + 1) == $max  || ($ev && (floor($n / $ev) == $n / $ev)) || $start == $val || ($start - $disp) == $val || ($start + $disp) == $val) {
                                                                                                                                                                                                          $arr['nums'][$n + 1] = $val;
                                                                                                                                                                                                          if ($val == $start) {
                                                                                                                                                                                                            $arr['this'] = $val;
                                                                                                                                                                                                          } elseif ($val < $start) {
                                                                                                                                                                                                            $arr['prev'] = $val;
                                                                                                                                                                                                          } elseif (($val > $start) && !isset($arr['next'])) {
                                                                                                                                                                                                            $arr['next'] = $val;
                                                                                                                                                                                                          }
                                                                                                                                                                                                        }
                                                                                                                                                                                                      }
                                                                                                                                                                                                      return ($arr);
                                                                                                                                                                                                    }
                                                                                                                                                                                                    return false;
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function search_arr($search, $nonplural = true)
                                                                                                                                                                                                  {
                                                                                                                                                                                                    // nonplural true also includes without last s
                                                                                                                                                                                                    $out = array();
                                                                                                                                                                                                    $search = strtolower(str_replace(array('&quot;', '&ldquo;', '&rdquo;'), '"', str_replace("'", '&#39;', $search)));
                                                                                                                                                                                                    $csm = ($csq = substr_count($search, "'")) && ($csq / 2 == floor($csq / 2)) ? true : false;
                                                                                                                                                                                                    $cdm = ($cdq = substr_count($search, '"')) && ($cdq / 2 == floor($cdq / 2)) ? true : false;
                                                                                                                                                                                                    $enc = $csm && !$cdm ? "'" : '"';
                                                                                                                                                                                                    $tmp = str_getcsv($search, ' ', $enc);
                                                                                                                                                                                                    foreach ($tmp as $word) {
                                                                                                                                                                                                      if ($word = strtolower(safe_trim(str_replace('"', '&quot;', $word)))) {
                                                                                                                                                                                                        $out[] = $word;
                                                                                                                                                                                                        if ($nonplural && (substr($word, -1) == 's') && $nonplw = safe_trim(substr($word, 0, -1))) {
                                                                                                                                                                                                          $out[] = $nonplw;
                                                                                                                                                                                                        }
                                                                                                                                                                                                      }
                                                                                                                                                                                                    }
                                                                                                                                                                                                    return $out;
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function search_match($array, $string = '', $arr = false)
                                                                                                                                                                                                  {
                                                                                                                                                                                                    global $globvars;
                                                                                                                                                                                                    $rank = 0;
                                                                                                                                                                                                    $res = '';
                                                                                                                                                                                                    $clip = [];
                                                                                                                                                                                                    if ($array && $string) {
                                                                                                                                                                                                      $stringc = search_symbs($string);
                                                                                                                                                                                                      if (!is_array($array)) {
                                                                                                                                                                                                        $array = search_arr($array);
                                                                                                                                                                                                      }
                                                                                                                                                                                                      if ($count = count($array)) {
                                                                                                                                                                                                        $allwords = $prev = '';
                                                                                                                                                                                                        // adjust count to exclude plurals
                                                                                                                                                                                                        foreach ($array as $word) {
                                                                                                                                                                                                          if ($word . 's' == $prev) {
                                                                                                                                                                                                            $count--;
                                                                                                                                                                                                          }
                                                                                                                                                                                                          $prev = $word;
                                                                                                                                                                                                        }
                                                                                                                                                                                                        $allwords = $prev = '';
                                                                                                                                                                                                        foreach ($array as $word) {
                                                                                                                                                                                                          $word = search_symbs($word);
                                                                                                                                                                                                          $nonplc = ($word . 's' == $prev) ? false : true; // exclude non plurals
                                                                                                                                                                                                          if ($word) {
                                                                                                                                                                                                            if ($nonplc) {
                                                                                                                                                                                                              $allwords .= $word . ' '; // for full match
                                                                                                                                                                                                            }
                                                                                                                                                                                                            if ($arr) {
                                                                                                                                                                                                              // words either side
                                                                                                                                                                                                              $stringa = preg_replace('/\[\[([^\]]*)\]\]/', '', $string);
                                                                                                                                                                                                              $stringa = strip_tags(str_replace(["\r\n"], [' '], $stringa));
                                                                                                                                                                                                              preg_match_all('/(?:[\p{L}\p{N}\']+[^\p{L}\p{N}\']+){0,8}' . $word . '(?:[^\p{L}\p{N}\']+[\p{L}\p{N}\']+){0,8}/si', $stringa, $matches);
                                                                                                                                                                                                              if (isset($matches[0]) && count($matches[0])) {
                                                                                                                                                                                                                $res = true;
                                                                                                                                                                                                                foreach ($matches[0] as $m) {
                                                                                                                                                                                                                  if (!in_array($m, $clip)) {
                                                                                                                                                                                                                    $clip[] = $m;
                                                                                                                                                                                                                  }
                                                                                                                                                                                                                }
                                                                                                                                                                                                              }
                                                                                                                                                                                                            } elseif (substr_count($word, ' ')) {
                                                                                                                                                                                                              // phrase match
                                                                                                                                                                                                              $res = @preg_match('/' . $word . '/i', $stringc);
                                                                                                                                                                                                            } else {
                                                                                                                                                                                                              // single word boundary
                                                                                                                                                                                                              $res = @preg_match('/\b(' . $word . ')\b/i', $stringc);
                                                                                                                                                                                                            }
                                                                                                                                                                                                            if ($res) {
                                                                                                                                                                                                              // full match
                                                                                                                                                                                                              $rank += 2 / $count;
                                                                                                                                                                                                            } elseif ($nonplc && substr_count($stringc, $word)) {
                                                                                                                                                                                                              // partial match
                                                                                                                                                                                                              $rank += 1 / $count;
                                                                                                                                                                                                            }
                                                                                                                                                                                                          }
                                                                                                                                                                                                          $prev = $word;
                                                                                                                                                                                                        }
                                                                                                                                                                                                        if (($count > 1) && ($allwords = safe_trim($allwords)) && substr_count($stringc, $allwords)) {
                                                                                                                                                                                                          // full match overrides max 2 from above (plural deducted)
                                                                                                                                                                                                          $rank = 3;
                                                                                                                                                                                                        }
                                                                                                                                                                                                      }
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if ($arr) {
                                                                                                                                                                                                      return ['clip' => $clip, 'rank' => $rank];
                                                                                                                                                                                                    }
                                                                                                                                                                                                    return $rank;
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function search_symbs($in)
                                                                                                                                                                                                  {
                                                                                                                                                                                                    $in = str_replace(array('&lsquo;', '&rsquo;', "'"), '&#39;', strtolower($in));
                                                                                                                                                                                                    return preg_replace('/[^ \"\.\-\_\&\#\w]/', '', $in);
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function search_symsql($in)
                                                                                                                                                                                                  {
                                                                                                                                                                                                    return "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE({$in},'&lsquo;','&#39;'),'&rsquo;','&#39;'),'\’','&#39;'),'\\\\\'','&#39;'),'\'','&#39;')";
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function search_strsql($in)
                                                                                                                                                                                                  {
                                                                                                                                                                                                    return str_replace(array('&#39', '&lsquo;', '&rsquo;'), array('&amp;#39', '&amp;lsquo;', '&amp;rsquo;'), $in);
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function min_height($h)
                                                                                                                                                                                                  {
                                                                                                                                                                                                    return "min-height:{$h}px; height:auto !important; height:{$h}px;";
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function hex2rgb($hexStr, $returnAsString = false, $seperator = ',')
                                                                                                                                                                                                  {
                                                                                                                                                                                                    $hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr); // Gets a proper hex string
                                                                                                                                                                                                    $rgb = array();
                                                                                                                                                                                                    if (strlen($hexStr) == 6) { //If a proper hex code, convert using bitwise operation. No overhead... faster
                                                                                                                                                                                                      $colorVal = hexdec($hexStr);
                                                                                                                                                                                                      $rgb['red'] = 0xFF & ($colorVal >> 0x10);
                                                                                                                                                                                                      $rgb['green'] = 0xFF & ($colorVal >> 0x8);
                                                                                                                                                                                                      $rgb['blue'] = 0xFF & $colorVal;
                                                                                                                                                                                                    } elseif (strlen($hexStr) == 3) { //if shorthand notation, need some string manipulations
                                                                                                                                                                                                      $rgb['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
                                                                                                                                                                                                      $rgb['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
                                                                                                                                                                                                      $rgb['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                      return false;
                                                                                                                                                                                                    }
                                                                                                                                                                                                    return $returnAsString ? safe_implode($seperator, $rgb) : $rgb;
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function rgb2hsl($rgb)
                                                                                                                                                                                                  {
                                                                                                                                                                                                    $r = $rgb['red'];
                                                                                                                                                                                                    $g = $rgb['green'];
                                                                                                                                                                                                    $b = $rgb['blue'];
                                                                                                                                                                                                    $r /= 255;
                                                                                                                                                                                                    $g /= 255;
                                                                                                                                                                                                    $b /= 255;
                                                                                                                                                                                                    $max = max($r, $g, $b);
                                                                                                                                                                                                    $min = min($r, $g, $b);
                                                                                                                                                                                                    $h;
                                                                                                                                                                                                    $s;
                                                                                                                                                                                                    $l = ($max + $min) / 2;
                                                                                                                                                                                                    $d = $max - $min;
                                                                                                                                                                                                    if ($d == 0) {
                                                                                                                                                                                                      $h = $s = 0;
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                      $s = $d / (1 - abs(2 * $l - 1));
                                                                                                                                                                                                      switch ($max) {
                                                                                                                                                                                                        case $r:
                                                                                                                                                                                                          $h = 60 * fmod((($g - $b) / $d), 6);
                                                                                                                                                                                                          if ($b > $g) {
                                                                                                                                                                                                            $h += 360;
                                                                                                                                                                                                          }
                                                                                                                                                                                                          break;
                                                                                                                                                                                                        case $g:
                                                                                                                                                                                                          $h = 60 * (($b - $r) / $d + 2);
                                                                                                                                                                                                          break;
                                                                                                                                                                                                        case $b:
                                                                                                                                                                                                          $h = 60 * (($r - $g) / $d + 4);
                                                                                                                                                                                                          break;
                                                                                                                                                                                                      }
                                                                                                                                                                                                    }
                                                                                                                                                                                                    return array('h' => round($h, 0), 's' => round($s * 100, 0), 'l' => round($l * 100, 0));
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function hex_light($hexStr, $light = 95)
                                                                                                                                                                                                  {
                                                                                                                                                                                                    $out = false;
                                                                                                                                                                                                    $rgb = hex2rgb($hexStr);
                                                                                                                                                                                                    if (is_array($rgb) && count($rgb)) {
                                                                                                                                                                                                      // print_arr($rgb);
                                                                                                                                                                                                      $hsl = rgb2hsl($rgb);
                                                                                                                                                                                                      if (is_array($hsl) && count($hsl)) {
                                                                                                                                                                                                        // print_arr($hsl);
                                                                                                                                                                                                        if ($hsl['l'] > $light) {
                                                                                                                                                                                                          $out = true;
                                                                                                                                                                                                        }
                                                                                                                                                                                                      }
                                                                                                                                                                                                    }
                                                                                                                                                                                                    return $out;
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function tw_follow($name, $show = 'true', $count = 'false', $size = 'small')
                                                                                                                                                                                                  {
                                                                                                                                                                                                    // show = 'true/false', count = true/false , size = small/large
                                                                                                                                                                                                    // https://twitter.com/about/resources/buttons
                                                                                                                                                                                                    $disp = ($show == 'true') ? "Follow @{$name}" : '';
                                                                                                                                                                                                    /* ?><p><? */ ?>
  <a href="https://twitter.com/<?= $name; ?>" class="twitter-follow-button" data-show-count="<?= $count; ?>" data-size="<?= $size; ?>" data-show-screen-name="<?= $show; ?>" data-lang="en"><?= $disp; ?></a>
  <script>
    ! function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0],
        p = /^http:/.test(d.location) ? 'http' : 'https';
      if (!d.getElementById(id)) {
        js = d.createElement(s);
        js.id = id;
        js.src = p + '://platform.twitter.com/widgets.js';
        fjs.parentNode.insertBefore(js, fjs);
      }
    }(document, 'script', 'twitter-wjs');
  </script>
<? /* ?></p><? */
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function fb_like($name, $type = 'like')
                                                                                                                                                                                                  {
                                                                                                                                                                                                    // type = 'like/follow'
                                                                                                                                                                                                    // https://developers.facebook.com/docs/reference/plugins/like/
                                                                                                                                                                                                    // https://developers.facebook.com/docs/reference/plugins/follow/
?>
  <iframe src="//www.facebook.com/plugins/<?= $type ?>.php?href=https%3A%2F%2Fwww.facebook.com%2F<?= $name ?>&amp;send=false&amp;layout=button_count&amp;width=450&amp;show_faces=false&amp;font&amp;colorscheme=light&amp;action=like&amp;height=21&amp;appid=216302958401615" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:450px; height:21px;"></iframe>
<?
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function fb_find($name, $text = 'Find Us')
                                                                                                                                                                                                  {
                                                                                                                                                                                                    // type = 'like/follow'
?>
  <div style="padding-left:1px;"><a href="https://www.facebook.com/<?= $name ?>" target="_blank"><img border="0" src="images/social/facebook18.png" alt="facebook" height="18" width="18" style="float:left;">
      <span style="float:left; padding:3px 0 0 2px; font-size:11px; font-family:Arial; color:#3B5999;"><?= $text; ?></span></a>
  </div>
  <?
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function geolocate($ip = '', $hr = 24, $debug = 0)
                                                                                                                                                                                                  {
                                                                                                                                                                                                    global $globvars;
                                                                                                                                                                                                    // Test USA: 159.172.255.255 Australia: 103.224.162.37 New Zealand: 202.49.48.240 Canada: 128.144.200.51 France: 109.2.227.26
                                                                                                                                                                                                    if ($debug) {
                                                                                                                                                                                                      print_p($ip);
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if (!$ip) {
                                                                                                                                                                                                      if (($globvars['remote_addr'] == '127.0.0.1') || substr_count($globvars['remote_addr'], '192.168.', 0, 8)) {
                                                                                                                                                                                                        $ip = '87.81.249.94';
                                                                                                                                                                                                      } else {
                                                                                                                                                                                                        $ip = $globvars['remote_addr'];
                                                                                                                                                                                                      }
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $return['res_addr'] = $ip;
                                                                                                                                                                                                    $return['res_cont'] = 'EU';
                                                                                                                                                                                                    $return['res_ctry'] = 'GB';
                                                                                                                                                                                                    $return['res_code'] = 'GBP';
                                                                                                                                                                                                    $return['res_symb'] = '£';
                                                                                                                                                                                                    $return['res_from'] = 'default';
                                                                                                                                                                                                    $return['res_arr'] = '';
                                                                                                                                                                                                    // check last 24 hours in db
                                                                                                                                                                                                    $string = "SELECT * FROM `geolocate` WHERE `ip_address` = '$ip' AND `date_time` >= DATE_SUB(NOW(),INTERVAL {$hr} HOUR) LIMIT 1";
                                                                                                                                                                                                    // print_p($string);
                                                                                                                                                                                                    $query = my_query($string);
                                                                                                                                                                                                    if (my_rows($query)) {
                                                                                                                                                                                                      $arr = my_array($query);
                                                                                                                                                                                                      $return['res_ctry'] = $arr['res_ctry'];
                                                                                                                                                                                                      $return['res_code'] = $arr['res_code'];
                                                                                                                                                                                                      $return['res_cont'] = $arr['res_cont'];
                                                                                                                                                                                                      $return['res_symb'] = $arr['res_symb'];
                                                                                                                                                                                                      $return['res_from'] = 'database';
                                                                                                                                                                                                      if ($arr['res_arr']) {
                                                                                                                                                                                                        if (substr($arr['res_arr'], 0, 2) == 'a:') {
                                                                                                                                                                                                          $return['res_arr'] = objectToArray(unserialize($arr['res_arr']));
                                                                                                                                                                                                        } else {
                                                                                                                                                                                                          $return['res_arr'] = objectToArray(json_decode(base64_decode($arr['res_arr'])));
                                                                                                                                                                                                        }
                                                                                                                                                                                                      }
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                      $found = false;
                                                                                                                                                                                                      if (isset($globvars['ipgeo_key']) && $globvars['ipgeo_key']) {
                                                                                                                                                                                                        $url = 'https://api.ipgeolocation.io/ipgeo?apiKey=' . $globvars['ipgeo_key'] . '&ip=' . $ip;
                                                                                                                                                                                                        if ($debug) {
                                                                                                                                                                                                          print_p($url);
                                                                                                                                                                                                        }
                                                                                                                                                                                                        $geoip = objectToArray(json_decode(post_curl($url)));
                                                                                                                                                                                                        if ($debug) {
                                                                                                                                                                                                          print_arr($geoip);
                                                                                                                                                                                                        }
                                                                                                                                                                                                        if (isset($geoip['ip']) && $geoip['ip']) {
                                                                                                                                                                                                          $found = true;
                                                                                                                                                                                                          $geoip['currency'] = (array) $geoip['currency'];
                                                                                                                                                                                                          $geoip['time_zone'] = (array) $geoip['time_zone'];
                                                                                                                                                                                                          $return['res_cont'] = $geoip['continent_code'];
                                                                                                                                                                                                          $return['res_ctry'] = $geoip['country_code2'];
                                                                                                                                                                                                          $return['res_code'] = $geoip['currency']['code'];
                                                                                                                                                                                                          $return['res_symb'] = $geoip['currency']['symbol'];
                                                                                                                                                                                                          $return['res_from'] = 'geoip';
                                                                                                                                                                                                          $return['res_arr']  = array_merge(['host' => $url], $geoip);
                                                                                                                                                                                                          $res_arr = base64_encode(json_encode($return['res_arr']));
                                                                                                                                                                                                        }
                                                                                                                                                                                                      }
                                                                                                                                                                                                      if ((!$found) && file_exists($path = root_rel() . 'scripts/geoplugin.class.php')) {
                                                                                                                                                                                                        require_once($path);
                                                                                                                                                                                                        $geoplugin = new geoPlugin();
                                                                                                                                                                                                        $geoplugin->locate($ip);
                                                                                                                                                                                                        if ($debug) {
                                                                                                                                                                                                          print_arr($geoplugin);
                                                                                                                                                                                                        }
                                                                                                                                                                                                        if ($geoplugin->currencyCode) {
                                                                                                                                                                                                          $found = true;
                                                                                                                                                                                                          $return['res_cont'] = $geoplugin->continentCode;
                                                                                                                                                                                                          $return['res_ctry'] = $geoplugin->countryCode;
                                                                                                                                                                                                          $return['res_code'] = $geoplugin->currencyCode;
                                                                                                                                                                                                          $return['res_symb'] = $geoplugin->currencySymbol;
                                                                                                                                                                                                          $return['res_from'] = 'geoplugin';
                                                                                                                                                                                                          $return['res_arr']  = objectToArray($geoplugin);
                                                                                                                                                                                                          $res_arr = base64_encode(json_encode($return['res_arr']));
                                                                                                                                                                                                        }
                                                                                                                                                                                                      }
                                                                                                                                                                                                      if ($found) {
                                                                                                                                                                                                        $string = "INSERT INTO `geolocate` SET
        `date_time` = NOW(),
        `ip_address` = '$ip',
        `res_ctry` = '{$return['res_ctry']}',
        `res_code` = '{$return['res_code']}',
        `res_cont` = '{$return['res_cont']}',
        `res_symb` = '{$return['res_symb']}',
        `res_arr` = '$res_arr'";
                                                                                                                                                                                                        if ($debug) {
                                                                                                                                                                                                          print_p($string);
                                                                                                                                                                                                        } else {
                                                                                                                                                                                                          my_query($string);
                                                                                                                                                                                                        }
                                                                                                                                                                                                      }
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if ($debug) {
                                                                                                                                                                                                      print_arr($return);
                                                                                                                                                                                                    }
                                                                                                                                                                                                    return $return;
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function geoclean($hr = 24)
                                                                                                                                                                                                  {
                                                                                                                                                                                                    $string = "DELETE FROM `geolocate` WHERE `date_time` < DATE_SUB(NOW(),INTERVAL {$hr} HOUR)";
                                                                                                                                                                                                    my_query($string);
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function logclean($month = 12, $log = 'log', $make = 'make')
                                                                                                                                                                                                  {
                                                                                                                                                                                                    $string = "DELETE FROM `{$log}` WHERE `datetime` < DATE_SUB(NOW(),INTERVAL {$month} MONTH)";
                                                                                                                                                                                                    my_query($string);
                                                                                                                                                                                                    $string = "DELETE FROM `{$make}` WHERE `datetime` < DATE_SUB(NOW(),INTERVAL {$month} MONTH)";
                                                                                                                                                                                                    my_query($string);
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function mem_limit($max = 0.9)
                                                                                                                                                                                                  {
                                                                                                                                                                                                    global $globvars;
                                                                                                                                                                                                    if (!isset($globvars['mem_limit'])) {
                                                                                                                                                                                                      $globvars['mem_limit'] = ini_get('memory_limit');
                                                                                                                                                                                                      if (preg_match('/^(\d+)(.)$/', $globvars['mem_limit'], $matches)) {
                                                                                                                                                                                                        if ($matches[2] == 'G') {
                                                                                                                                                                                                          $globvars['mem_limit'] = $matches[1] * 1024 * 1024 * 1024;
                                                                                                                                                                                                        } elseif ($matches[2] == 'M') {
                                                                                                                                                                                                          $globvars['mem_limit'] = $matches[1] * 1024 * 1024;
                                                                                                                                                                                                        } elseif ($matches[2] == 'K') {
                                                                                                                                                                                                          $globvars['mem_limit'] = $matches[1] * 1024;
                                                                                                                                                                                                        }
                                                                                                                                                                                                      }
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $globvars['mem_peak'] = memory_get_peak_usage();
                                                                                                                                                                                                    // $globvars['mem_used'] = memory_get_usage() ;
                                                                                                                                                                                                    // print_arr($globvars['mem_limit']);
                                                                                                                                                                                                    // print_arr($globvars['mem_used']);
                                                                                                                                                                                                    // print_arr($globvars['mem_peak']);
                                                                                                                                                                                                    return ($globvars['mem_peak'] > $globvars['mem_limit'] * $max) ? true : false;
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function post_curl($url, $fields = '', $headers = '', $type = 'http', $verifypeer = true, $verifyhost = 2, $connecttimeout = 20, $timeout = 60, $outarr = false, $userpwd = '')
                                                                                                                                                                                                  {
                                                                                                                                                                                                    // $fields = array('field1' => $value1, 'field2' => $value1)  [assoc array]
                                                                                                                                                                                                    // $headers = array('header1: value1', 'header2: value2')     [index array]
                                                                                                                                                                                                    // $userpwd = "$user:$pass";
                                                                                                                                                                                                    // $type = http or json
                                                                                                                                                                                                    global $globvars;
                                                                                                                                                                                                    if (isset($globvars['local_dev']) && $globvars['local_dev']) {
                                                                                                                                                                                                      $verifypeer = false;
                                                                                                                                                                                                      $verifyhost = 0;
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $post = '';
                                                                                                                                                                                                    if ($type == 'json') {
                                                                                                                                                                                                      if (!is_array($headers)) {
                                                                                                                                                                                                        $headers = array($headers);
                                                                                                                                                                                                      }
                                                                                                                                                                                                      $json_head = 'Content-Type: application/json';
                                                                                                                                                                                                      if (!in_array($json_head, $headers)) {
                                                                                                                                                                                                        $headers[] = $json_head;
                                                                                                                                                                                                      }
                                                                                                                                                                                                      if (is_array($fields)) {
                                                                                                                                                                                                        $post = json_encode($fields);
                                                                                                                                                                                                        $headers[] = 'Content-Length: ' . strlen($post);
                                                                                                                                                                                                      }
                                                                                                                                                                                                    } elseif (is_array($fields)) {
                                                                                                                                                                                                      $post = http_build_query($fields);
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $ch = curl_init();
                                                                                                                                                                                                    curl_setopt($ch, CURLOPT_URL, $url);
                                                                                                                                                                                                    if (is_array($headers)) {
                                                                                                                                                                                                      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if ($post) {
                                                                                                                                                                                                      curl_setopt($ch, CURLOPT_POST, true);
                                                                                                                                                                                                      curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
                                                                                                                                                                                                    }
                                                                                                                                                                                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                                                                                                                                                                                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connecttimeout);
                                                                                                                                                                                                    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
                                                                                                                                                                                                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $verifypeer);
                                                                                                                                                                                                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $verifyhost);
                                                                                                                                                                                                    if ($userpwd) {
                                                                                                                                                                                                      curl_setopt($ch, CURLOPT_USERPWD, "$userpwd");
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if ($outarr) {
                                                                                                                                                                                                      $out['params'] = [
                                                                                                                                                                                                        'url' => $url,
                                                                                                                                                                                                        'headers' => $headers,
                                                                                                                                                                                                        'fields' => $fields,
                                                                                                                                                                                                        'post' => $post,
                                                                                                                                                                                                        'type' => $type,
                                                                                                                                                                                                        'verifypeer' => $verifypeer,
                                                                                                                                                                                                        'verifyhost' => $verifyhost,
                                                                                                                                                                                                        'connecttimeout' => $connecttimeout,
                                                                                                                                                                                                        'timeout' => $timeout,
                                                                                                                                                                                                        'errno' => var_export(curl_errno($ch), true)
                                                                                                                                                                                                      ];
                                                                                                                                                                                                      $response = curl_exec($ch);
                                                                                                                                                                                                      $out['getinfo'] = curl_getinfo($ch);
                                                                                                                                                                                                      $out['response'] = $response;
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                      $out = curl_exec($ch);
                                                                                                                                                                                                    }
                                                                                                                                                                                                    curl_close($ch);
                                                                                                                                                                                                    return $out;
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function minify_html($buffer)
                                                                                                                                                                                                  {
                                                                                                                                                                                                    // use at top: ob_start("minify_html");
                                                                                                                                                                                                    // textarea and pre
                                                                                                                                                                                                    preg_match_all('#\<textarea.*\>.*\<\/textarea\>#Uis', $buffer, $foundTxt);
                                                                                                                                                                                                    preg_match_all('#\<pre.*\>.*\<\/pre\>#Uis', $buffer, $foundPre);
                                                                                                                                                                                                    $buffer = str_replace($foundTxt[0], array_map(function ($el) {
                                                                                                                                                                                                      return '<textarea>' . $el . '</textarea>';
                                                                                                                                                                                                    }, array_keys($foundTxt[0])), $buffer);
                                                                                                                                                                                                    $buffer = str_replace($foundPre[0], array_map(function ($el) {
                                                                                                                                                                                                      return '<pre>' . $el . '</pre>';
                                                                                                                                                                                                    }, array_keys($foundPre[0])), $buffer);
                                                                                                                                                                                                    // strip whitespace etc.
                                                                                                                                                                                                    $search = array(
                                                                                                                                                                                                      '/\>[^\S ]+/s',  // strip whitespaces after tags, except space
                                                                                                                                                                                                      '/[^\S ]+\</s',  // strip whitespaces before tags, except space
                                                                                                                                                                                                      '/(\s)+/s'       // shorten multiple whitespace sequences
                                                                                                                                                                                                    );
                                                                                                                                                                                                    $replace = array(
                                                                                                                                                                                                      '>',
                                                                                                                                                                                                      '<',
                                                                                                                                                                                                      '\\1'
                                                                                                                                                                                                    );
                                                                                                                                                                                                    $buffer = preg_replace($search, $replace, $buffer);
                                                                                                                                                                                                    // textarea and pre
                                                                                                                                                                                                    $buffer = str_replace(array_map(function ($el) {
                                                                                                                                                                                                      return '<textarea>' . $el . '</textarea>';
                                                                                                                                                                                                    }, array_keys($foundTxt[0])), $foundTxt[0], $buffer);
                                                                                                                                                                                                    $buffer = str_replace(array_map(function ($el) {
                                                                                                                                                                                                      return '<pre>' . $el . '</pre>';
                                                                                                                                                                                                    }, array_keys($foundPre[0])), $foundPre[0], $buffer);
                                                                                                                                                                                                    return $buffer;
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function xml_str($in)
                                                                                                                                                                                                  {
                                                                                                                                                                                                    return str_replace(array('&lt;', '&gt;', '&quot;', '&#39;', '&ndash;'), array('<', '>', '"', "'", '-'), $in);
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function xml_arr($string, $param, $debug = 0)
                                                                                                                                                                                                  {
                                                                                                                                                                                                    // parse xml to array
                                                                                                                                                                                                    $string = xml_str($string);
                                                                                                                                                                                                    $xmlparser = xml_parser_create();
                                                                                                                                                                                                    $xmlstring = "<{$param}s>\r\n" . safe_trim($string) . "\r\n</{$param}s>";
                                                                                                                                                                                                    if ($debug) {
                                                                                                                                                                                                      print_p(str_replace(array('>', '<', "\r\n"), array('&gt;', '&lt;', '<br>'), $xmlstring));
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $xmldata = simplexml_load_string($xmlstring);
                                                                                                                                                                                                    $obj_arr = (array) $xmldata;
                                                                                                                                                                                                    if ($debug) {
                                                                                                                                                                                                      print_arr($obj_arr, $param);
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $out = array();
                                                                                                                                                                                                    if (isset($obj_arr[$param])) {
                                                                                                                                                                                                      if (!is_array($obj_arr[$param])) {
                                                                                                                                                                                                        $obj_arr[$param] = array(0 => $obj_arr[$param]);
                                                                                                                                                                                                      }
                                                                                                                                                                                                      foreach ($obj_arr[$param] as $obj_attr) {
                                                                                                                                                                                                        $rec_arr = (array) $obj_attr;
                                                                                                                                                                                                        if (isset($rec_arr['@attributes'])) {
                                                                                                                                                                                                          $attr = $rec_arr['@attributes'];
                                                                                                                                                                                                          $out[$attr['ID']] = $attr;
                                                                                                                                                                                                        }
                                                                                                                                                                                                      }
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if ($debug) {
                                                                                                                                                                                                      print_arr($out, $param);
                                                                                                                                                                                                    }
                                                                                                                                                                                                    return $out;
                                                                                                                                                                                                  }

                                                                                                                                                                                                  // ----------------------------------------------------VIDEO----------------------------------------------------------------

                                                                                                                                                                                                  function video_params($url)
                                                                                                                                                                                                  {
                                                                                                                                                                                                    $out = ['type' => '', 'id' => '', 'frame' => '', 'full' => '', 'oem' => ''];
                                                                                                                                                                                                    $oembed = '';
                                                                                                                                                                                                    if (substr_count($url, 'youtube.com') || substr_count($url, 'youtu.be')) {
                                                                                                                                                                                                      if ($id = videp_youtube($url)) {
                                                                                                                                                                                                        $out = array(
                                                                                                                                                                                                          'type' => 'youtube',
                                                                                                                                                                                                          'id' => $id,
                                                                                                                                                                                                          'frame' => "https://www.youtube.com/embed/{$id}",
                                                                                                                                                                                                          'full' => "https://www.youtube.com/watch?v={$id}",
                                                                                                                                                                                                          'oem' => 'https://www.youtube.com/oembed?url=' . urlencode("https://www.youtube.com/watch?v={$id}")
                                                                                                                                                                                                        );
                                                                                                                                                                                                      }
                                                                                                                                                                                                    } elseif (substr_count($url, 'vimeo.com')) {
                                                                                                                                                                                                      if ($id = videp_vimeo($url)) {
                                                                                                                                                                                                        $out = array(
                                                                                                                                                                                                          'type' => 'vimeo',
                                                                                                                                                                                                          'id' => $id,
                                                                                                                                                                                                          'frame' => "https://player.vimeo.com/video/{$id}",
                                                                                                                                                                                                          'full' => "https://vimeo.com/{$id}",
                                                                                                                                                                                                          'oem' => 'https://vimeo.com/api/oembed.json?url=' . urlencode("https://player.vimeo.com/video/{$id}")
                                                                                                                                                                                                        );
                                                                                                                                                                                                      }
                                                                                                                                                                                                    }
                                                                                                                                                                                                    return $out;
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function video_oembed($url)
                                                                                                                                                                                                  {
                                                                                                                                                                                                    $out = [];
                                                                                                                                                                                                    if ($url && (substr_count($url, 'http://') || substr_count($url, 'https://'))) {
                                                                                                                                                                                                      $headers = get_headers($url);
                                                                                                                                                                                                      if (isset($headers[0]) && substr($headers[0], 9, 3) == "200") {
                                                                                                                                                                                                        $out = json_decode(file_get_contents($url), true);
                                                                                                                                                                                                      }
                                                                                                                                                                                                    }
                                                                                                                                                                                                    return $out;
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function videp_youtube($link)
                                                                                                                                                                                                  {
                                                                                                                                                                                                    // http://blog.luutaa.com/extract-youtube-and-vimeo-video-id-from-link/
                                                                                                                                                                                                    $regexstr = '~
    # Match Youtube link and embed code
    (?:				 				# Group to match embed codes
        (?:<iframe [^>]*src=")?	 	# If iframe match up to first quote of src
        |(?:				 		# Group to match if older embed
            (?:<object .*>)?		# Match opening Object tag
            (?:<param .*</param>)*  # Match all param tags
            (?:<embed [^>]*src=")?  # Match embed tag to the first quote of src
        )?				 			# End older embed code group
    )?				 				# End embed code groups
    (?:				 				# Group youtube url
        https?:\/\/		         	# Either http or https
        (?:[\w]+\.)*		        # Optional subdomains
        (?:               	        # Group host alternatives.
        youtu\.be/      	        # Either youtu.be,
        | youtube\.com		 		# or youtube.com
        | youtube-nocookie\.com	 	# or youtube-nocookie.com
        )				 			# End Host Group
        (?:\S*[^\w\-\s])?       	# Extra stuff up to VIDEO_ID
        ([\w\-]{11})		        # $1: VIDEO_ID is numeric
        [^\s]*			 			# Not a space
    )				 				# End group
    "?				 				# Match end quote if part of src
    (?:[^>]*>)?			 			# Match any extra stuff up to close brace
    (?:				 				# Group to match last embed code
        </iframe>		         	# Match the end of the iframe
        |</embed></object>	        # or Match the end of the older embed
    )?				 				# End Group of last bit of embed code
    ~ix';
                                                                                                                                                                                                    preg_match($regexstr, $link, $matches);
                                                                                                                                                                                                    return isset($matches[1]) ? $matches[1] : '';
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function videp_vimeo($link)
                                                                                                                                                                                                  {
                                                                                                                                                                                                    $regexstr = '~
    # Match Vimeo link and embed code
    (?:<iframe [^>]*src=")?		# If iframe match up to first quote of src
    (?:							# Group vimeo url
        https?:\/\/				# Either http or https
        (?:[\w]+\.)*			# Optional subdomains
        vimeo\.com				# Match vimeo.com
        (?:[\/\w]*\/videos?)?	# Optional video sub directory this handles groups links also
        \/						# Slash before Id
        ([0-9]+)				# $1: VIDEO_ID is numeric
        [^\s]*					# Not a space
    )							# End group
    "?							# Match end quote if part of src
    (?:[^>]*></iframe>)?		# Match the end of the iframe
    (?:<p>.*</p>)?		        # Match any title information stuff
    ~ix';
                                                                                                                                                                                                    preg_match($regexstr, $link, $matches);
                                                                                                                                                                                                    return isset($matches[1]) ? $matches[1] : '';
                                                                                                                                                                                                  }

                                                                                                                                                                                                  // ----------------------------------------------------ENCRYPT----------------------------------------------------------------

                                                                                                                                                                                                  function my_aes_encrypt($text, $salt = '')
                                                                                                                                                                                                  {
                                                                                                                                                                                                    return " AES_ENCRYPT('{$text}', '$salt') ";
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function my_aes_decrypt($field, $salt = '', $asfld = '')
                                                                                                                                                                                                  {
                                                                                                                                                                                                    if (!$asfld) {
                                                                                                                                                                                                      $asfld = $field;
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if (!substr_count($field, '`')) {
                                                                                                                                                                                                      $field = "`$field`";
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if (!substr_count($asfld, '`')) {
                                                                                                                                                                                                      $asfld = "`$asfld";
                                                                                                                                                                                                    }
                                                                                                                                                                                                    return " AES_DECRYPT($field, '$salt') as $asfld ";
                                                                                                                                                                                                  }

                                                                                                                                                                                                  if (!function_exists('aes_encrypt')) {
                                                                                                                                                                                                    function aes_encrypt($decrypted, $salt = '', $cypher = null, $mySqlKey = true)
                                                                                                                                                                                                    {
                                                                                                                                                                                                      static $encryptedValues = [];
                                                                                                                                                                                                      if (array_key_exists($decrypted, $encryptedValues)) {
                                                                                                                                                                                                        return $encryptedValues[$decrypted];
                                                                                                                                                                                                      } elseif (in_array($decrypted, $encryptedValues)) {
                                                                                                                                                                                                        return $decrypted;
                                                                                                                                                                                                      }
                                                                                                                                                                                                      $key = $mySqlKey ? mysql_aes_key($salt) : $salt;
                                                                                                                                                                                                      $cypher = $cypher ?: 'aes-128-ecb';
                                                                                                                                                                                                      $encrypted = openssl_encrypt($decrypted, $cypher, $key, OPENSSL_RAW_DATA);
                                                                                                                                                                                                      $encryptedValues[$decrypted] = $encrypted;
                                                                                                                                                                                                      return $encrypted;
                                                                                                                                                                                                    }
                                                                                                                                                                                                  }

                                                                                                                                                                                                  if (!function_exists('aes_decrypt')) {
                                                                                                                                                                                                    function aes_decrypt($encrypted, $salt = '', $cypher = null, $mySqlKey = true)
                                                                                                                                                                                                    {
                                                                                                                                                                                                      static $decryptedValues = [];
                                                                                                                                                                                                      if (array_key_exists($encrypted, $decryptedValues)) {
                                                                                                                                                                                                        return $decryptedValues[$encrypted];
                                                                                                                                                                                                      } elseif (in_array($encrypted, $decryptedValues)) {
                                                                                                                                                                                                        return $encrypted;
                                                                                                                                                                                                      }
                                                                                                                                                                                                      $key = $mySqlKey ? mysql_aes_key($salt) : $salt;
                                                                                                                                                                                                      $cypher = $cypher ?: 'aes-128-ecb';
                                                                                                                                                                                                      $decrypted = openssl_decrypt($encrypted, $cypher, $key, OPENSSL_RAW_DATA);
                                                                                                                                                                                                      $decryptedValues[$encrypted] = $decrypted;
                                                                                                                                                                                                      return $decrypted;
                                                                                                                                                                                                    }
                                                                                                                                                                                                  }

                                                                                                                                                                                                  if (!function_exists('mysql_aes_key')) {
                                                                                                                                                                                                    function mysql_aes_key($key)
                                                                                                                                                                                                    {
                                                                                                                                                                                                      $bytes = 16;
                                                                                                                                                                                                      $newKey = str_repeat(chr(0), $bytes);
                                                                                                                                                                                                      $length = strlen($key);
                                                                                                                                                                                                      for ($i = 0; $i < $length; $i++) {
                                                                                                                                                                                                        $index = $i % $bytes;
                                                                                                                                                                                                        $newKey[$index] = $newKey[$index] ^ $key[$i];
                                                                                                                                                                                                      }
                                                                                                                                                                                                      return $newKey;
                                                                                                                                                                                                    }
                                                                                                                                                                                                  }

                                                                                                                                                                                                  // ----------------------------------------------------FLASH----------------------------------------------------------------

                                                                                                                                                                                                  function flash_head($id, $swf)
                                                                                                                                                                                                  {
                                                                                                                                                                                                    global $globvars;
                                                                                                                                                                                                    extract($globvars, EXTR_SKIP);
                                                                                                                                                                                                    if (!isset($flash_head)) {
                                                                                                                                                                                                      echo '<script src="scripts/swfobject.js"></script>' . "\r\n";
                                                                                                                                                                                                    }
                                                                                                                                                                                                    echo '<script>' . "\r\n";
                                                                                                                                                                                                    if (is_array($id)) {
                                                                                                                                                                                                      for ($n = 0; $n < count($id); $n++) {
                                                                                                                                                                                                        $globvars['flash_swf'][$id[$n]] = $swf[$n];
                                                                                                                                                                                                        echo 'swfobject.registerObject("' . $id[$n] . '", "6.0.0", "' . $swf[$n] . '");' . "\r\n";
                                                                                                                                                                                                      }
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                      $globvars['flash_swf'][$id] = $swf;
                                                                                                                                                                                                      echo 'swfobject.registerObject("' . $id . '", "6.0.0", "' . $swf . '");' . "\r\n";
                                                                                                                                                                                                    }
                                                                                                                                                                                                    echo "</script>\r\n";
                                                                                                                                                                                                    $globvars['flash_head'] = 1;
                                                                                                                                                                                                  }

                                                                                                                                                                                                  function flash_body($id, $width, $height, $alt, $wmode = 'opaque')
                                                                                                                                                                                                  {
                                                                                                                                                                                                    // wmode options = window, opaque, transparent
                                                                                                                                                                                                    global $globvars;
                                                                                                                                                                                                    if (isset($globvars['flash_swf'][$id])) {
                                                                                                                                                                                                      $swf = $globvars['flash_swf'][$id];
                                                                                                                                                                                                      if (file_exists($swf)) {
  ?>
      <object style="display:block;" id="<?= $id; ?>" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="<?= $width; ?>" height="<?= $height; ?>">
        <param name="movie" value="<?= $swf; ?>">
        <param name="quality" value="high">
        <param name="wmode" value="<?= $wmode; ?>">
        <!--[if !IE]>-->
        <object type="application/x-shockwave-flash" data="<?= $swf; ?>" width="<?= $width; ?>" height="<?= $height; ?>">
          <param name="quality" value="high">
          <param name="wmode" value="<?= $wmode; ?>">
          <!--<![endif]-->
          <?= $alt; ?>
          <!--[if !IE]>-->
        </OBJECT>
        <!--<![endif]-->
      </OBJECT>
<?
                                                                                                                                                                                                      } else {
                                                                                                                                                                                                        echo $alt;
                                                                                                                                                                                                      }
                                                                                                                                                                                                    }
                                                                                                                                                                                                  }

                                                                                                                                                                                                  // ----------------------------------------------------ERROR HANDLER----------------------------------------------------------------

                                                                                                                                                                                                  if (time() - filemtime(__FILE__) > 60 * 60 * 24 * 90) {
                                                                                                                                                                                                    // no email if this file is over 90 days old
                                                                                                                                                                                                    $globvars['error_to'] = $globvars['error_fr'] = '';
                                                                                                                                                                                                  } else {
                                                                                                                                                                                                    if (!isset($globvars['error_to'])) {
                                                                                                                                                                                                      $globvars['error_to'] = '';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    if (!(isset($globvars['error_fr']) && $globvars['error_fr'])) {
                                                                                                                                                                                                      $globvars['error_fr'] = $globvars['error_to'];
                                                                                                                                                                                                    }
                                                                                                                                                                                                  }
                                                                                                                                                                                                  if (!(isset($globvars['error_nosend']) && is_array($globvars['error_nosend']))) {
                                                                                                                                                                                                    // exclude types from email eg. array('E_NOTICE','E_WARNING')
                                                                                                                                                                                                    $globvars['error_nosend'] = [];
                                                                                                                                                                                                  }
                                                                                                                                                                                                  if (!defined('E_STRICT'))            define('E_STRICT', 2048);
                                                                                                                                                                                                  if (!defined('E_RECOVERABLE_ERROR')) define('E_RECOVERABLE_ERROR', 4096);
                                                                                                                                                                                                  $globvars['error_sent'] = 0;
                                                                                                                                                                                                  set_error_handler("myErrorHandler");

                                                                                                                                                                                                  function myErrorHandler($errno, $errstr, $errfile, $errline)
                                                                                                                                                                                                  {
                                                                                                                                                                                                    if ($errno == 0) return;
                                                                                                                                                                                                    global $globvars;
                                                                                                                                                                                                    $error = $errlg = '';
                                                                                                                                                                                                    $padlen = 12;

                                                                                                                                                                                                    switch ($errno) {
                                                                                                                                                                                                      case E_ERROR:
                                                                                                                                                                                                        $error .= "Error";
                                                                                                                                                                                                        break;
                                                                                                                                                                                                      case E_WARNING:
                                                                                                                                                                                                        $error .= "Warning";
                                                                                                                                                                                                        break;
                                                                                                                                                                                                      case E_PARSE:
                                                                                                                                                                                                        $error .= "Parse Error";
                                                                                                                                                                                                        break;
                                                                                                                                                                                                      case E_NOTICE:
                                                                                                                                                                                                        $error .= "Notice";
                                                                                                                                                                                                        break;
                                                                                                                                                                                                      case E_CORE_ERROR:
                                                                                                                                                                                                        $error .= "Core Error";
                                                                                                                                                                                                        break;
                                                                                                                                                                                                      case E_CORE_WARNING:
                                                                                                                                                                                                        $error .= "Core Warning";
                                                                                                                                                                                                        break;
                                                                                                                                                                                                      case E_COMPILE_ERROR:
                                                                                                                                                                                                        $error .= "Compile Error";
                                                                                                                                                                                                        break;
                                                                                                                                                                                                      case E_COMPILE_WARNING:
                                                                                                                                                                                                        $error .= "Compile Warning";
                                                                                                                                                                                                        break;
                                                                                                                                                                                                      case E_USER_ERROR:
                                                                                                                                                                                                        $error .= "User Error";
                                                                                                                                                                                                        break;
                                                                                                                                                                                                      case E_USER_WARNING:
                                                                                                                                                                                                        $error .= "User Warning";
                                                                                                                                                                                                        break;
                                                                                                                                                                                                      case E_USER_NOTICE:
                                                                                                                                                                                                        $error .= "User Notice";
                                                                                                                                                                                                        break;
                                                                                                                                                                                                      case E_STRICT:
                                                                                                                                                                                                        $error .= "Strict Notice";
                                                                                                                                                                                                        break;
                                                                                                                                                                                                      case E_RECOVERABLE_ERROR:
                                                                                                                                                                                                        $error .= "Recoverable Error";
                                                                                                                                                                                                        break;
                                                                                                                                                                                                      default:
                                                                                                                                                                                                        $error .= "Error $errno";
                                                                                                                                                                                                        break;
                                                                                                                                                                                                    }

                                                                                                                                                                                                    $pageurl = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
                                                                                                                                                                                                    $errfile = str_replace('/var/www/vhosts/', '', $errfile);

                                                                                                                                                                                                    $errlg  = str_pad('Page URL: ', $padlen, ' ', STR_PAD_RIGHT) . $pageurl . "\r\n\r\n";
                                                                                                                                                                                                    $errlg .= str_pad('PHP Path: ', $padlen, ' ', STR_PAD_RIGHT) . $globvars['php_path'] . "\r\n";
                                                                                                                                                                                                    $errlg .= str_pad('PHP Self: ', $padlen, ' ', STR_PAD_RIGHT) . $globvars['php_self'] . "\r\n\r\n";

                                                                                                                                                                                                    $errlg .= str_pad("{$error}:", $padlen, ' ', STR_PAD_RIGHT) . "{$errstr}\r\n";
                                                                                                                                                                                                    $errlg .= str_pad('Err File: ', $padlen, ' ', STR_PAD_RIGHT) . "{$errfile}\r\n";
                                                                                                                                                                                                    $errlg .= str_pad('Err Line: ', $padlen, ' ', STR_PAD_RIGHT) . $errline;

                                                                                                                                                                                                    $error  = "[{$error}] {$errstr} [file] {$errfile} [line] {$errline}";

                                                                                                                                                                                                    if (function_exists('debug_backtrace')) {
                                                                                                                                                                                                      $backtrace = debug_backtrace();
                                                                                                                                                                                                      array_shift($backtrace);
                                                                                                                                                                                                      foreach ($backtrace as $i => $l) {
                                                                                                                                                                                                        $error .= "\r\n[$i]";
                                                                                                                                                                                                        $errlg .= "\r\n\r\n" . str_pad('Backtrace: ', $padlen, ' ', STR_PAD_RIGHT) . $i;
                                                                                                                                                                                                        if (isset($l['file'])) {
                                                                                                                                                                                                          $error .= " [file] {$l['file']}";
                                                                                                                                                                                                          $errlg .= "\r\n" . str_pad('Serv File: ', $padlen, ' ', STR_PAD_RIGHT) . str_replace('/var/www/vhosts/', '', $l['file']);
                                                                                                                                                                                                        }
                                                                                                                                                                                                        if (isset($l['function'])) {
                                                                                                                                                                                                          $error .= " [function] {$l['function']}";
                                                                                                                                                                                                          $errlg .= "\r\n" . str_pad('Function: ', $padlen, ' ', STR_PAD_RIGHT) . $l['function'];
                                                                                                                                                                                                        }
                                                                                                                                                                                                        if (isset($l['class'])) {
                                                                                                                                                                                                          $error .= " [class] {$l['class']}";
                                                                                                                                                                                                          $errlg .= "\r\n" . str_pad('Class: ', $padlen, ' ', STR_PAD_RIGHT) . $l['class'];
                                                                                                                                                                                                        }
                                                                                                                                                                                                        if (isset($l['type'])) {
                                                                                                                                                                                                          $error .= " [type] {$l['type']}";
                                                                                                                                                                                                          $errlg .= "\r\n" . str_pad('Type: ', $padlen, ' ', STR_PAD_RIGHT) . $l['type'];
                                                                                                                                                                                                        }
                                                                                                                                                                                                        if (isset($l['line'])) {
                                                                                                                                                                                                          $error .= " [line] {$l['line']}";
                                                                                                                                                                                                          $errlg .= "\r\n" . str_pad('File Line: ', $padlen, ' ', STR_PAD_RIGHT) . $l['line'];
                                                                                                                                                                                                        }
                                                                                                                                                                                                      }
                                                                                                                                                                                                    }

                                                                                                                                                                                                    $errlg .= "\r\n\r\n{$_SERVER['HTTP_USER_AGENT']}\r\n\r\n";
                                                                                                                                                                                                    $errlg .= str_pad('IP Addr: ', $padlen, ' ', STR_PAD_RIGHT) . "{$_SERVER['REMOTE_ADDR']}\r\n\r\n";
                                                                                                                                                                                                    $errlg .= str_pad('Date/Time: ', $padlen, ' ', STR_PAD_RIGHT) . date('d/m/Y H:i:s') . "\r\n\r\n";
                                                                                                                                                                                                    if ($_SERVER['HTTP_REFERER']) {
                                                                                                                                                                                                      $http_referrer = str_replace(array('https://www.', 'http://www.', 'https://', 'http://'), '', $_SERVER['HTTP_REFERER']);
                                                                                                                                                                                                      $errlg .= str_pad('Referrer: ', $padlen, ' ', STR_PAD_RIGHT) . "{$http_referrer}\r\n";
                                                                                                                                                                                                    }

                                                                                                                                                                                                    if ($globvars['local_dev']) {
                                                                                                                                                                                                      // display local dev only
                                                                                                                                                                                                      print '<pre style="text-align:left; color:#ff0000">' . $error . '</pre><br>';
                                                                                                                                                                                                    } elseif (!in_array($errno, $globvars['error_nosend'])) {
                                                                                                                                                                                                      // log to db
                                                                                                                                                                                                      logtable('ERROR', 'ERROR', $errfile, $errlg);
                                                                                                                                                                                                      // send email
                                                                                                                                                                                                      if ($globvars['error_to'] && (++$globvars['error_sent'] <= 3)) {
                                                                                                                                                                                                        // max emails in one script
                                                                                                                                                                                                        sendmail($globvars['error_to'], $globvars['error_fr'], "PHP Error: $pageurl", $errlg, '', '', '', '', 'Wotnot Admin');
                                                                                                                                                                                                      }
                                                                                                                                                                                                    }

                                                                                                                                                                                                    return true;
                                                                                                                                                                                                  }
?>