<?
if (!function_exists('dc')) {
  die;
}
date_default_timezone_set('Europe/London');
header('Content-Type: text/html; charset=utf-8');
session_start();
$globvars['sessid'] = session_id();
$globvars['sessmd'] = md5(date('Ym') . $globvars['remote_addr']);

/*
SERVER SETTINGS FOR CLOUDFLARE TO RETURN REAL REMOTE_ADDR
Additional Apache directives for HTTPS: RemoteIPHeader CF-connecting-IP
Additional nginx directives: real_ip_header CF-Connecting-IP;
*/

$globvars['live_href'] = 'http://local.tiandi.com';

if ($globvars['local_dev']) {
  $globvars['base_href'] = "http://{$_SERVER['SERVER_NAME']}/tiandi/en/";
  $globvars['minify']['combined'] = false;
  $globvars['altlangs']['en'] = ['lang' => 'EN', 'url' => "http://{$_SERVER['SERVER_NAME']}/tiandi/en/"];
  $globvars['altlangs']['cn'] = ['lang' => 'CN', 'url' => "http://{$_SERVER['SERVER_NAME']}/tiandi/cn/"];
  $globvars['hosting'] = 'LOCAL';
} else {
  $globvars['base_href'] = $globvars['live_href'];
  $globvars['minify']['combined'] = true;
  $globvars['altlangs']['en'] = ['lang' => 'EN', 'url' => 'https://e-tiandi.com/'];
  $globvars['altlangs']['cn'] = ['lang' => 'CN', 'url' => 'https://e-tiandi.cn/'];
  $globvars['hosting'] = $_SERVER['SERVER_ADDR'] == '77.68.2.125' ? 'FAILOVER' : 'LIVE';
}
$globvars['altsites'] = false;

define('ALT_URL_NAME', 'Chinese URL');
define('ALT_URL_NOTE', 'If different (without domain)');
define('LANGUAGE', 'LANGUAGE');

$globvars['charset'] = 'utf8'; // include utf8 or defaults to latin
$globvars['htmlang'] = 'en';

// $globvars['minify']['combined'] = true ;
$globvars['minify']['css_combined'] = 'css/combined.css';
$globvars['minify']['css_files'] = ['css/fonts.css', 'css/styles.css', 'css/layout.css'];
$globvars['minify']['css_other'] = [];
$globvars['minify']['css_apple'] = ['css/apple.css'];
$globvars['minify']['js_combined'] = 'scripts/jquery/combined.js';
$globvars['minify']['js_files'] = ['scripts/jquery/jquery.js', 'scripts/slideshowback/slideshowback.js'];
$globvars['minify']['js_other'] = [];

$globvars['local_path'] = 'tiandi/en';   // local path
$globvars['param_table'] = 'params_tiandi';
$globvars['head_logo_b'] = 'svg/logo_light.svg';
$globvars['head_logo_w'] = 'svg/logo_light.svg';
$globvars['head_logo_m'] = 'svg/logo_light.svg';
$globvars['admin_logo'] = 'logo.png';
$globvars['admin_foot'] = 'hallo.png';
$globvars['design_url'] = 'https://www.hallodigital.co.uk';
$globvars['design_name'] = 'Hallo Digital';

$globvars['comp_logo'] = $globvars['live_href'] . 'images/logo_large.png';
$globvars['comp_logo_w'] = 800;
$globvars['comp_logo_h'] = 297;

$globvars['ftp_server']  = '127.0.0.1';
$globvars['ftp_username'] = 'tiandi';
$globvars['ftp_password'] = 'bKwmvv@t!R4';
$globvars['ftp_srvpath'] = 'httpdocs';    // from root eg. httpdocs (no start/end slash)
$globvars['ftp_passive'] = 'y';    // passive mode y/n
$globvars['temp_folder'] = '_temp';       // eg. _temp (no start/end slash)

$globvars['db_hostname'] = '3.77.6.107';
$globvars['db_database'] = 'tiandi_en';
$globvars['db_username'] = 'sendo';
$globvars['db_password'] = '12345678';
$globvars['db_logtable'] = 'log';
$globvars['db_imakelog'] = 'make';
$globvars['db_medtable'] = 'media';

$globvars['error_to'] = 'errors@wotnot.co.uk';
$globvars['error_fr'] = 'errors@wotnot.co.uk';

$globvars['sm_domain'] = 'e-tiandi.com';              // just domain no ending /
$globvars['sm_url']    = 'https://e-tiandi.com/';  // site url ending in /
$globvars['sm_pages']  = array();          // include these
$globvars['sm_prior']  = array();                    // priorirties (0 to 1)
$globvars['sm_excld']  = array('404', 'index.php', 'jquery.php', 'cron.php', 'testemail.php');                      // exclude these
$globvars['sm_allow']  = array();                      // allow these if added in sm_funct
$globvars['sm_subfs']  = array();                      // include subfolders
$globvars['sm_funct']  = 'sm_funct';                          // run function before creating
$globvars['sm_after']  = '';                          // run function after creating

$globvars['embed_csjs'] = array();                    // pages to embed css and js files
$globvars['blog_stack'] = array('head', 'html', 'image', 'link', 'vimeo', 'youtube');

$globvars['vat_rate'] = 0.2;

// localdev - overwritten by params
// live register at: https://www.google.com/recaptcha/admin (reCAPTCHA v2)
$globvars['recaptcha']['site'] = '6LctegoUAAAAADup7iFJEC9_O-iI6u0llD68n2c-';
$globvars['recaptcha']['secret'] = '6LctegoUAAAAAFrnATxXJUnHoink0ZWaOZcpKGle';
$globvars['recaptcha']['lang'] = 'en';
$globvars['recaptcha']['init'] = false;

// $globvars['sagepay']['URL'] = 'https://test.sagepay.com/gateway/service/vspform-register.vsp';
// $globvars['sagepay']['URL'] = $globvars['local_dev'] ? '_sagepay.php' : 'https://live.sagepay.com/gateway/service/vspform-register.vsp';
$globvars['sagepay']['URL'] = '_sagepay.php';

$globvars['sagepay']['password']    = ''; // live
$globvars['sagepay']['VPSProtocol'] = '3.00';
$globvars['sagepay']['Vendor']      = 'hallodigital-dev';
$globvars['sagepay']['Email']       = $globvars['email_fr'];
$globvars['sagepay']['Title']       = 'Order from hallodigital-dev.co.uk';
$globvars['sagepay']['Country']     = 'GB';
$globvars['sagepay']['Message']     = 'Thank you for ordering from wotdev.co.uk';
$globvars['sagepay']['Success']     = 'https://www.hallodigital-dev.co.uk/demo/payment/?success=y';
$globvars['sagepay']['Failure']     = 'https://www.hallodigital-dev.co.uk/demo/payment/?success=n';

$globvars['cntrl_user'] = '';
$globvars['cntrl_admin'] = '';
$globvars['admin_pages'] = false;

$globvars['mega_nav'] = true;

define('BASKET', 'tiandi_basket'); // session name
define('DBKEY', 'dS2GKj6h50rwLKj4j5djq4kjn2kj4kl5f2kV3'); // only numbers and letters

@include_once('includes.inc.php');
opendb();
parameters();
templates();

if (substr_count($globvars['php_path'], 'control/') && $globvars['php_self'] != 'login.php') {
  @include_once('login.php');
}
