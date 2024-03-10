<? 
// https://github.com/matthiasmullie/minify
// https://www.minifier.org/
use MatthiasMullie\Minify;
@include_once('functions.inc.php');
@include_once('../scripts/vendor/autoload.php');
?>
<!DOCTYPE html>
<html lang="en"> 
  <head> 
    <title><?= $globvars['comp_name'] ; ?> - Minify</title>
    <? if(file_exists('favicon.ico')) { ?>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <? } if(file_exists('head1.inc.php')) { @include_once('head1.inc.php'); } ?>
    <script type="text/javascript">window.name='<?= $globvars['php_self']; ?>';</script>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="stylesheet" type="text/css" href="leftmenu.css">
    <link rel="stylesheet" type="text/css" href="styles.css.php">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes, minimal-ui">
  </head> 
  <body> 
    <table border="0" cellpadding="0" cellspacing="0" summary="" id="maintable" width="1255"> 
      <tr valign="top"> 
        <td valign="top" id="leftcol"> 
          <div id="leftdiv" align="center">
            <div class="maintop">
              <? 
              $plink = isset($globvars['plink']) && $globvars['plink'] ? $globvars['plink'] : '../' ;
              if(file_exists($globvars['admin_logo'])) { ?>
                <a href="<?= $plink ; ?>" target="public"><img src="<?= $globvars['admin_logo'] ?>" border="0" alt="<?= $globvars['comp_name'] ; ?>" width="170"></a>
              <? } else { ?>
                <h1><?= $globvars['comp_name']; ?></h1>
              <? } ?>
            </div>
            <br>
            <? @include_once('leftmenu.inc.php'); ?>
          </div>
        </td> 
        <td valign="top" id="midcol"> 
        </td> 
        <td valign="top" id="rightcol"> 
          <div class="maintop1">
            <p class="button" style="float:right"><a href="index.php">ADMIN HOME</a></p>
            <h1 class="h1">MINIFY</h1><br>
          </div>
          <div class="maintop2">
            <p>Minifies and combines css/js files after editing</p>
          </div>
          <br>
          <?
          if(file_exists('../scripts/vendor/matthiasmullie/minify/src/JS.php')) {
            // open
            $fp = '../';
            $css = $js = [];
            if($globvars['minify']['css_combined']) { 
              $css['file'] = $fp . $globvars['minify']['css_combined']; 
              $css['res'] = fopen($fp . $globvars['minify']['css_combined'], "w"); 
            }
            if($globvars['minify']['js_combined'])  { 
              $js['file'] = $fp . $globvars['minify']['js_combined']; 
              $js['res']  = fopen($fp . $globvars['minify']['js_combined'],  "w"); 
            }
            // combine/minify
            $res = [];
            if(isset($globvars['minify']['css_files'])) { foreach($globvars['minify']['css_files'] as $file) { $res[] = minify($fp . $file,$css); } }
            if(isset($globvars['minify']['css_other'])) { foreach($globvars['minify']['css_other'] as $file) { $res[] = minify($fp . $file); } }
            if(isset($globvars['minify']['css_apple'])) { foreach($globvars['minify']['css_apple'] as $file) { $res[] = minify($fp . $file); } }
            if(isset($globvars['minify']['js_files']))  { foreach($globvars['minify']['js_files'] as $file)  { $res[] = minify($fp . $file,$js); } }
            if(isset($globvars['minify']['js_other']))  { foreach($globvars['minify']['js_other'] as $file)  { $res[] = minify($fp . $file); } }
            // close
            if(count($css)) { 
              fclose($css['res']); 
              $res[] = minify($css['file'],[],true);
            }
            if(count($js))  { 
              fclose($js['res']);  
              $res[] = minify($js['file'],[],true);
            }
            ?>
            <table width="100%" class="tabler" cellpadding="6" cellspacing="0"><tr class="thb"><td>FILE</td><td>COMBINED</td><td>MINIFIED</td></tr>
            <?
            foreach($res as $v) {
              ?>
              <tr><td><?= $v['file'] ?></td><td><?= $v['combined'] ?></td><td><?= $v['minified'] ?></td></tr>
              <?
            }
            ?>
            </table>
            <?
          }
          else {
            print_p('Minify not installed');
          }
          
          function minify($file,$comb=[],$del=false) {
            $flink = '<a target="_blank" href="' . $file . '">' . $file . '</a>';
            $out = ['file'=>$flink,'minified'=>'','combined'=>''];
            if(file_exists($file)) {
              if(count($comb)) {
                // combine
                fwrite($comb['res'], file_get_contents($file));
                $out['combined'] = $comb['file']; 
              }
              else {
                $min = $ftype = '';
                if(! substr_count($file,'.min')) {
                  // minify
                  if(substr_count($file,'.js')) {
                    $ftype = 'JS';
                    $min = str_replace('.js','.min.js',$file);
                  }
                  elseif(substr_count($file,'.css')) {
                    $ftype = 'CSS';
                    $min = str_replace('.css','.min.css',$file);
                  }
                  if($ftype && $min && ((! file_exists($min)) || (filemtime($file) > filemtime($min)))) {
                    if($ftype == 'JS') {
                      $minifier = new Minify\JS($file);
                    }
                    elseif($ftype == 'CSS') {
                      $minifier = new Minify\CSS($file);
                    }
                    $minifier->minify($min);
                  }
                }
                else {
                  $min = $file;
                }
                if($min) {
                  $out['minified'] = '<a target="_blank" href="' . $min . '">' . $min . '</a>'; 
                }
                if($del) { 
                  // delete original
                  $dfile = basename($file);
                  $dpath = str_replace($dfile, '', $file);
                  del_file($dpath,$dfile);
                  $out['file'] = $file; 
                }
              }
            }
            else {
              $out['minified'] = 'File not found';
            }
            return $out;
          }
          ?>
          </div>
        </td> 
      </tr>
    </table>
  </body>
</html>