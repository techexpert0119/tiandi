<? @include_once('functions.inc.php'); ?>
<!DOCTYPE html>
<html lang="en"> 
  <head> 
    <title><?= $globvars['comp_name'] ; ?> - Media Files</title>
    <? 
    $mainwidth = 1300 ;
    $allowdel = 'y';
    $maxdisp = 50;
    $root = '../images/';
    $paths = [];

    $arr = dirToArray($root);
    // print_arv($arr);
    // print_arv($paths);

    /*
    $files = array();
    foreach(glob($root . '*', GLOB_ONLYDIR) as $d1) {
      $d1 = str_replace($root, '', $d1);
      foreach(glob($root . $d1 . '/*', GLOB_ONLYDIR) as $d2) {
        $d2 = str_replace($root . $d1, '', $d2);
        // echo $d1 . $d2 . '<br>';
        $paths[] = $d1 . $d2 ;
      }
    }
    */
    globvars('path','fsort','start','date_fr','date_to','width_fr','width_to','height_fr','height_to','search','(array) notes');
    // path
    $path = $globvars['path'];
    if(count($paths) && ! in_array( $path, $paths)) {
      $path = $paths[array_key_first($paths)];
    }
    // upload
    if(isset($_FILES['up_file']['tmp_name']) && is_uploaded_file($_FILES['up_file']['tmp_name']) ) {
      if($_FILES['up_file']['name']) {
        if(substr_count('/', $_FILES['up_file']['name'])) {
          $frs = strrpos( $_FILES['up_file']['name'], '/' ) + 1 ;
          $up_file = substr($_FILES['up_file']['name'],$frs) ;
        }
        else {
          $up_file = $_FILES['up_file']['name'] ;
        }
      }
      else {
        $up_file = $_FILES['up_file']['tmp_name'] ;
      }
      upload_file($_FILES['up_file']['tmp_name'], "{$root}{$path}" ,$up_file);
      $globvars['fsort'] = 'date_DESC';
    }
    // sort
    $sorts = array('file','note','type','date','size','width','height');
    $fsort = $globvars['fsort'];
    if(! in_array( str_replace('_DESC','',$fsort), $sorts)) {
      $fsort = $sorts[0];
    }
    // filters
    if($globvars['width_fr'] && ! is_numeric($globvars['width_fr'])) { $globvars['width_fr'] = ''; }
    if($globvars['width_to'] && ! is_numeric($globvars['width_to'])) { $globvars['width_to'] = ''; }

    if($globvars['height_fr'] && ! is_numeric($globvars['height_fr'])) { $globvars['height_fr'] = ''; }
    if($globvars['height_to'] && ! is_numeric($globvars['height_to'])) { $globvars['height_to'] = ''; }

    $time_fr = $time_to = 0 ;
    if($globvars['date_fr'] && ! is_numeric($time_fr = strtotime(cdate($globvars['date_fr'],'Y-m-d'))) ) { $globvars['date_fr'] = ''; }
    if($globvars['date_to'] && ! is_numeric($time_to = strtotime(cdate($globvars['date_to'],'Y-m-d')) + (24*60*60) ) ) { $globvars['date_to'] = ''; }

    // database
    $media_id = $media_fp = [];
    $string = "select * from `{$globvars['db_medtable']}` order by `id`";
    $query = my_query($string);
    if($nrows = my_rows($query)) {
      while($a_row = my_assoc($query)) {
        $a_row['fpath'] = "{$a_row['path']}/{$a_row['file']}";
        $media_id[$a_row['id']] = $a_row;
        $media_fp[$a_row['fpath']] = $a_row['id'];
      }
    }

    // notes
    /*
    foreach($globvars['notes'] as $id => $note) {
      if(isset($media_id[$id]) && $media_id[$id]['note'] != $note) {
        $string = "UPDATE `{$globvars['db_medtable']}` SET `note` = '$note' WHERE `id` = '$id' LIMIT 1";
        $media_id[$id]['note'] = $note;
        // print_p($string);
        my_query($string);
      }
    }
    */

    // files
    $files = $thumbs = [];
    $tpath = is_dir("{$root}{$path}/_th") ? "{$root}{$path}/_th" : '' ;
    if($handle = opendir("{$root}{$path}")) {
      while(false !== ($file = readdir($handle))) {
        if($file == "." || $file == ".." || $file == "Thumbs.db" || is_dir("{$root}{$path}/{$file}")) {
          continue;
        }
        $fpath = "{$path}/{$file}";
        $rpath = "{$root}{$fpath}";
        $time = filemtime($rpath) ;
        $date = date('Y-m-d H:i:s',$time) ;
        if(($time_fr && ($time < $time_fr)) || ($time_to && ($time > $time_to))) {
          continue;
        }
        $type = 'file';
        if(isset($media_fp[$fpath]) && $media_id[$media_fp[$fpath]]['time'] == $time) {
          // found and time matches db
          $arr = $media_id[$media_fp[$fpath]];
          $id = $arr['id'] ;
          if($globvars['search'] && ! (substr_count(strtolower($file), strtolower($globvars['search'])) || substr_count(strtolower($arr['note']), strtolower($globvars['search'])))) {
            continue;
          }
          if($arr['type'] == 'image') {
            $type = 'image';
            if(($globvars['width_fr'] && ($arr['width'] < $globvars['width_fr'])) || ($globvars['width_to'] && ($arr['width'] > $globvars['width_to']))) {
              continue;
            }
            if(($globvars['height_fr'] && ($arr['height'] < $globvars['height_fr'])) || ($globvars['height_to'] && ($arr['height'] > $globvars['height_to']))) {
              continue;
            }
          }
          $files[$id] = array('file'=>$arr['file'],'note'=>$arr['note'],'type'=>$arr['type'],'size'=>$arr['size'],'time'=>$arr['time'],'date'=>$arr['date'],'width'=>$arr['width'],'height'=>$arr['height'],'thumb'=>false);
        }
        else {
          // read params from file
          if($globvars['search'] && ! substr_count(strtolower($file), strtolower($globvars['search']))) {
            continue;
          }
          $size = filesize($rpath);
          $width = $height = 0 ;
          if( in_array(pathinfo($rpath, PATHINFO_EXTENSION), $globvars['image_types']) && mime_check($rpath,'image') && ($arr = @getimagesize($rpath)) ) {
            if(($globvars['width_fr'] && ($arr[0] < $globvars['width_fr'])) || ($globvars['width_to'] && ($arr[0] > $globvars['width_to']))) {
              continue;
            }
            if(($globvars['height_fr'] && ($arr[1] < $globvars['height_fr'])) || ($globvars['height_to'] && ($arr[1] > $globvars['height_to']))) {
              continue;
            }
            $type = 'image';
            $width = $arr[0];
            $height = $arr[1];
          }
          $string = "`{$globvars['db_medtable']}` set `path` = '{$path}', `file` = '{$file}', `time` = '{$time}', `date` = '{$date}', `type` = '{$type}', `size` = '{$size}', `width` = '{$width}', `height` = '{$height}'";
          if(isset($media_fp[$fpath])) {
            // update database
            $id = $media_fp[$fpath] ;
            $string = "UPDATE {$string} WHERE `id` = '$id' LIMIT 1";
            // print_p($string);
            my_query($string);
          }
          else {
            // add to database
            $string = "INSERT INTO {$string}";
            // print_p($string);
            my_query($string);
            $id = my_id();
          }
          $files[$id] = array('file'=>$file,'note'=>'','type'=>$type,'size'=>$size,'time'=>$time,'date'=>$date,'width'=>$width,'height'=>$height,'thumb'=>false);
        }
        // thumbnails
        if($type == 'image' && $tpath) {
          $hpath = "{$tpath}/{$file}";
          if(file_exists($hpath) && $time == filemtime($hpath)) {
            $files[$id]['thumb'] = true ;
          }
          elseif(make_image("{$root}{$path}",$file,$tpath,'',200,100,'m',85)) {
            // make new thumb
            $files[$id]['thumb'] = true;
            touch($hpath,$time);
          }
        }
      }

      closedir($handle);
      foreach($sorts as $sort) {
        if(str_replace('_DESC','',$fsort) == $sort) {
          $files = array_sort($files,$sort,(substr_count($fsort,'_DESC') ? 'DESC':''));
          break;
        }
      }
      // print_arr($files);
    }
    $filetot = count($files);
    function linkvars($path,$fsort) {
      global $globvars ;
      $out = $globvars['php_self'] . '?path=' . $path . '&amp;fsort=' . $fsort ;
      if($globvars['date_fr']) { $out .= '&amp;date_fr=' . $globvars['date_fr'] ; }
      if($globvars['date_to']) { $out .= '&amp;date_to=' . $globvars['date_to'] ; }
      if($globvars['width_fr']) { $out .= '&amp;width_fr=' . $globvars['width_fr'] ; }
      if($globvars['width_to']) { $out .= '&amp;width_to=' . $globvars['width_to'] ; }
      if($globvars['height_fr']) { $out .= '&amp;height_fr=' . $globvars['height_fr'] ; }
      if($globvars['height_to']) { $out .= '&amp;height_to=' . $globvars['height_to'] ; }
      return clean_url($out) ;
    }
    @include_once('../scripts/jquery.inc.php');
    js_file('control.js');
    js_file('../scripts/mouselayer.js');
    if(file_exists('head1.inc.php')) { @include_once('head1.inc.php'); } ?>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="stylesheet" type="text/css" href="leftmenu.css">
    <link rel="stylesheet" type="text/css" href="styles.css.php">
    <? if(isapple()) { cs_file('apple.css'); } ?>
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes, minimal-ui">
   </head> 
  <body> 
    <?
    // print_arr(browser_info());
    ?>
    <form action="<?= $globvars['php_self'] ?>" method="post" id="mform" enctype="multipart/form-data">
    <table border="0" cellpadding="0" cellspacing="0" summary="" id="maintable"> 
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
          <img src="blank.gif" alt="" width="10" height="1">
        </td> 

        <td id="rightcol" width="<?= $mainwidth ?>" valign="top"> 

          <div class="maintop1">
            <table summary="" width="100%" cellspacing="0" cellpadding="0" border="0"> 
              <tr valign="middle"> 
                <td valign="middle">
                  <h1 style="display:inline-block;">MEDIA FILES: <?= $filetot; ?></h1>
                </td> 
                <td valign="middle" class="nobr" align="right" style="position:relative;">
                </td> 
              </tr> 
            </table> 
          </div> 

          <div class="maintop2">
            <table summary="" width="100%" cellspacing="0" cellpadding="0" border="0"> 
              <tr valign="middle"> 
                <td valign="middle">
                  <select style="display:inline-block;" name="path" id="path" onchange="document.getElementById('mform').submit()">
                    <? foreach($paths as $mpath) { ?>
                    <option value="<?= optsel($mpath,$path) ?>"><?= $mpath ?></option>
                    <? } ?>
                  </select>
                </td> 
                <td valign="middle" align="center">
                  <b class="h2">SEARCH:</b> <input style="width:120px" type="text" placeholder="SEARCH" name="search" value="<?= $globvars['search'] ?>">
                </td> 
                <td valign="middle" align="center">
                  <b class="h2">DATE:</b> <input style="width:70px" type="text" placeholder="FROM" name="date_fr" value="<?= $globvars['date_fr'] ?>"> <input style="width:70px" type="text" placeholder="TO" name="date_to" value="<?= $globvars['date_to'] ?>">
                </td> 
                <td valign="middle" align="center">
                  <b class="h2">WIDTH:</b> <input style="width:40px" type="text" placeholder="FROM" name="width_fr" value="<?= $globvars['width_fr'] ?>"> <input style="width:40px" type="text" placeholder="TO" name="width_to" value="<?= $globvars['width_to'] ?>">
                </td> 
                <td valign="middle" align="center">
                  <b class="h2">HEIGHT:</b> <input style="width:40px" type="text" placeholder="FROM" name="height_fr" value="<?= $globvars['height_fr'] ?>"> <input style="width:40px" type="text" placeholder="TO" name="height_to" value="<?= $globvars['height_to'] ?>">
                </td> 
                <td valign="middle" class="nobr" style="position:relative;">
                  <div class="fileUpload" style="float:right;">
                    <?
                    $nm = 'up_file' ;
                    $id = 'id_' . $nm ;
                    $hd = 'hd_' . $nm ;
                    $bt = 'bt_' . $nm ;
                    $onc = "getId('{$hd}').value = getId('{$id}').value;" ;
                    $omo = "getId('{$bt}').setAttribute('class', 'button1')";
                    $omx = "getId('{$bt}').setAttribute('class', 'button')";
                    ?>
                    <input type="file" onchange="<?= $onc ; ?>" name="<?= $nm ; ?>" class="browserHidden" id="<?= $id ; ?>" onmouseover="<?= $omo ; ?>" onmouseout="<?= $omx ; ?>"> 
                    <div class="browserVisible"> <input type="text" class="input" id="<?= $hd ; ?>" style="width:120px;"> <span id="<?= $bt ?>" class="button"><a href="#">BROWSE</a></span> </div>
                  </div>
                  <div style="float:right; padding:3px 5px 0 0;"><b class="h2">UPLOAD FILE:</b></div>
                  <div style="position:absolute; z-index:99999; right:0; top:4px"><input type="hidden" name="fsort" value="<?= $fsort ?>"><input class="submit" type="submit" name="submit1" value="GO"></div>  
                </td> 
              </tr> 
            </table> 
          </div>
          <br>

          <table class="tabler" summary="" id="ilist" width="100%" cellspacing="0" cellpadding="4" border="0"> 
            <tr class="thb">
              <td class="button nobr" valign="top">
                <a href="<?= linkvars($path,'file' . (substr_count($fsort,'file') && ! substr_count($fsort,'_DESC') ? '_DESC':'')) ?>">FILE</a>
                <? if(substr_count($fsort,'file')) { if(substr_count($fsort,'file_DESC')) { print '&uarr;'; } else print '&darr;'; } ?>
              </td>
              <? /* ?>
              <td class="button nobr" valign="top" width="300">
                <a href="<?= linkvars($path,'note' . (substr_count($fsort,'note') && ! substr_count($fsort,'_DESC') ? '_DESC':'')) ?>">NOTE</a>
                <? if(substr_count($fsort,'note')) { if(substr_count($fsort,'note_DESC')) { print '&uarr;'; } else print '&darr;'; } ?>
              </td>
              <? */ ?>
              <td class="button nobr" valign="top">
                <a href="<?= linkvars($path,'type' . (substr_count($fsort,'type') && ! substr_count($fsort,'_DESC') ? '_DESC':'')) ?>">TYPE</a>
                <? if(substr_count($fsort,'type')) { if(substr_count($fsort,'type_DESC')) { print '&uarr;'; } else print '&darr;'; } ?>
              </td>
              <td class="button nobr" valign="top">
                <a href="<?= linkvars($path,'date' . (substr_count($fsort,'date') && ! substr_count($fsort,'_DESC') ? '_DESC':'')) ?>">DATE</a>
                <? if(substr_count($fsort,'date')) { if(substr_count($fsort,'date_DESC')) { print '&uarr;'; } else print '&darr;'; } ?>
              </td>
              <td class="button nobr" valign="top">
                <a href="<?= linkvars($path,'size' . (substr_count($fsort,'size') && ! substr_count($fsort,'_DESC') ? '_DESC':'')) ?>">SIZE</a>
                <? if(substr_count($fsort,'size')) { if(substr_count($fsort,'size_DESC')) { print '&uarr;'; } else print '&darr;'; } ?>
              </td>
              <td class="button nobr" valign="top">
                <a href="<?= linkvars($path,'width' . (substr_count($fsort,'width') && ! substr_count($fsort,'_DESC') ? '_DESC':'')) ?>">WIDTH</a>
                <? if(substr_count($fsort,'width')) { if(substr_count($fsort,'width_DESC')) { print '&uarr;'; } else print '&darr;'; } ?>
              </td>
              <td class="button nobr" valign="top">
                <a href="<?= linkvars($path,'height' . (substr_count($fsort,'height') && ! substr_count($fsort,'_DESC') ? '_DESC':'')) ?>">HEIGHT</a>
                <? if(substr_count($fsort,'height')) { if(substr_count($fsort,'height_DESC')) { print '&uarr;'; } else print '&darr;'; } ?>
              </td>
              <td class="button nobr" valign="top" width="100">
              </td>
            </tr> 
            <? 
            $n = $c = 0 ;
            $r = count($files);
            if((! is_numeric($globvars['start'])) || $globvars['start'] > $r || $globvars['start'] < 0) {
              $globvars['start'] = 0 ;
            }
            foreach($files as $id => $file) { 
              if($c++ < $globvars['start']) {
                continue;
              }
              $imgf = "{$root}{$path}/{$file['file']}" ;
              $imgd = $file['thumb'] ? "{$root}{$path}/_th/{$file['file']}" : $imgf ;
              ?>
            <tr style="height:60px;">
              <td valign="middle">
                <a title="<?= $file['file']; ?>" href="<?= $imgf ?>" target="_blank"><?= cliptext($file['file'],80) ?></a>
              </td>
              <? /* ?>
              <td valign="middle">
                <input type="text" name="notes[<?= $id; ?>]" value="<?= $file['note'] ?>" style="margin:2px;width:300px;border-color:#DDDDDD" maxlength="200">
              </td>
              <? */ ?>
              <td valign="middle">
                <?= $file['type'] ?>
              </td>
              <td valign="middle">
                <?= date('d/m/Y',strtotime($file['date'])) ?>
              </td>
              <td valign="middle">
                <?= $file['size'] > 0 ? disp_filesize($file['size']) : 'ERROR' ?>
              </td>
              <td valign="middle">
                <?= $file['width'] > 0 ? $file['width'] : '' ?>
              </td>
              <td valign="middle">
                <?= $file['height'] > 0 ? $file['height'] : '' ?>
              </td>
              <td valign="middle">
                <?
                if($file['type'] == 'image') {
                  $imgh = 50 ;
                  $poph = 200 ;
                  $offx = -500 ;
                  $offy = $poph / 2 ;
                  $omo = "ShowContent('img_{$id}',$offx,$offy); return true;";
                  $omx = "HideContent('img_{$id}'); return true;";
                  ?>
                  <a title="<?= $file['file']; ?>" onmousemove="<?= $omo ?>" onmouseover="<?= $omo ?>" onmouseout="<?= $omx ?>" href="<?= $imgf; ?>" style="display:block;" target="_blank">
                    <img src="<?= $imgf; ?>" style="<?= 'max-height:' . $imgh . 'px; max-width:' . ( $imgh * 2 ) . 'px' ; ?>" alt="" border="">
                  </a>
                  <div id="<?= 'img_' . $id ; ?>" style="display:none; position:absolute; border: solid 1px black; background-color: white; padding:5px; z-index:999">
                    <img alt="<?= $file['file']; ?>" style="max-width:400px;" alt="" border="0" src="<?= $imgd; ?>">
                  </div>
                  <?
                }
                /*
                if($file['type'] == 'image') {
                  $imgh = 50 ;
                  ?>
                  <a title="<?= $file['file']; ?>" href="<?= $imgf; ?>" target="_blank">
                    <img alt="<?= $file['file']; ?>" src="<?= $imgd; ?>" style="display:block;<?= 'max-height:' . $imgh . 'px; max-width:' . ( $imgh * 2 ) . 'px' ; ?>" alt="" border="">
                  </a>
                  <?
                }
                */
                ?>
              </td>
            </tr> 
            <? 
            if( ++$n >= $maxdisp ) { break ; } 
          } 
          ?>
          </table>

          <?
          $arr = start_arr($filetot,$maxdisp,$globvars['start']);
          // print_arr($arr);
          if( isset($arr['prev']) || isset($arr['next']) ) { 
            $params = '?path=' . $path . '&amp;fsort=' . $fsort ;
            if($globvars['width_fr']) { $params .= '&amp;width_fr=' . $globvars['width_fr'] ; }
            if($globvars['width_to']) { $params .= '&amp;width_to=' . $globvars['width_to'] ; }
            if($globvars['height_fr']) { $params .= '&amp;height_fr=' . $globvars['height_fr'] ; }
            if($globvars['height_to']) { $params .= '&amp;height_to=' . $globvars['height_to'] ; }
            if($globvars['date_fr']) { $params .= '&amp;date_fr=' . $globvars['date_fr'] ; }
            if($globvars['date_to']) { $params .= '&amp;date_to=' . $globvars['date_to'] ; }
            if($globvars['search']) { $params .= '&amp;search=' . $globvars['search'] ; }
            ?>
          <br>
          <br>
          <div style="float:right; margin:2px 0 20px 20px;">
            <input type="submit" name="Submit" id="Submit" value="SAVE" class="submit"> 
          </div>
          <table border="0" cellpadding="2" cellspacing="0" align="center" summary=""> 
            <tr> 
              <td class="button">
                  <? if( isset($arr['prev']) ) { ?>
                <a href="<?= $globvars['php_self'] . $params . '&amp;start=' . $arr['prev'] ; ?>">Previous</a>
                  <? } ?> </td> 
              <td align="center">
                  <? if( isset($arr['nums']) ) { 
                     foreach($arr['nums'] as $key => $val) {?>
                <span class="<?= (isset($arr['this']) && ($val == $arr['this'])) ? 'button1' : 'button'; ?>"> 
                <a href="<?= $globvars['php_self'] . $params . '&amp;start=' . $val ; ?>"><?= $key ; ?></a></span>
                      <? } } ?></td> 
              <td align="right" class="button">
                    <? if( isset($arr['next']) ) { ?>
                <a href="<?= $globvars['php_self'] . $params . '&amp;start=' . $arr['next'] ; ?>">Next</a>
                  <? } ?></td> 
            </tr> 
          </table>
          <? } ?>
          <br>
          <br>

        </td>     
     </tr>
    </table>
    </form>
 
  </body>
</html>