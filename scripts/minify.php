<?
// https://github.com/matthiasmullie/minify
// https://www.minifier.org/
if(! file_exists('vendor/matthiasmullie/minify/src/JS.php')) {
  die;
}
@include_once('vendor/autoload.php');
use MatthiasMullie\Minify;
if(isset($_GET['file']) && $_GET['file'] && file_exists($_GET['file'])) {
  if(substr_count($_GET['file'],'.js')) {
    $minifier = new Minify\JS($_GET['file']);
    print $minifier->minify();
  }
  elseif(substr_count($_GET['file'],'.css')) {
    $minifier = new Minify\CSS($_GET['file']);
    print $minifier->minify();
  }
}
?>