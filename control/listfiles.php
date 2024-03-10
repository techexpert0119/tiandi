<? @include_once('functions.inc.php'); ?>
<!DOCTYPE html>
<html lang="en"> 
  <head> 
    <title>File Manager</title> 
    <link rel="stylesheet" type="text/css" href="styles.css"> 
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes, minimal-ui">
    <style type="text/css">
    BODY {
      font-family:Verdana, Arial, Helvetica, Sans-Serif;
      font-size:11px;
      font-weight:normal;
      color:#000000;
      background-color:#FFFFFF;
      margin:15px;
    }
    .lazyi {
      border:1px solid #F3F3F3; 
      max-width:200px;
    }
    </style>
    <?
    globvars('fpath','fid','fname','file','filter','delete');
    extract($globvars);
    $lazy = false ;
    if(file_exists($lazysrc = "../scripts/lazy/jquery.lazy.min.js")) {
      $lazy = true ;
      ?>
      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
      <script src="<?= $lazysrc ?>"></script>
      <script>
      $(function() { 
        $('.lazyi').Lazy({
          effect: "fadeIn",
          effectTime: 500,
          threshold: 0
        }); 
      });
      </script>
      <?
    }
    ?>
    <script src="control.js"></script> 
    <script>
    function optsel(fid,file) {
      if(typeof window.opener.change_chosen != 'undefined') {
        window.opener.change_chosen(fid,file);
      }
      else {
        window.opener.document.getElementById(fid).value = file;
      }
      window.close();
    }
    function listjump() {
      window.location.hash="<?= urlencode($file) ;?>" ;
      window.scrollBy(0,-20);      
    }
    </script>
</head> 
<body onload="listjump()"> 
<div style="text-align:left;"> 
  <form method="post" enctype="multipart/form-data" action="listfiles.php">
  <?
  if($fname && ! $fid) { $fid = $fname ; }
  $fpath = str_replace('|','/',$fpath);
  $fbase = str_replace('../','',$fpath);
  if($handle = opendir($fpath)) {
    $files = $thumbs = $media_id = $media_fp = [];
    // media database
    if(isset($globvars['db_medtable']) && $globvars['db_medtable']) {
      $string = "select * from `{$globvars['db_medtable']}` order by `id`";
      $query = my_query($string);
      if($nrows = my_rows($query)) {
        while($a_row = my_assoc($query)) {
          $a_row['fpath'] = "{$a_row['path']}/{$a_row['file']}";
          $media_id[$a_row['id']] = $a_row;
          $media_fp[$a_row['fpath']] = $a_row['id'];
        }
      }
    }
    // upload file
    if(isset($_FILES['up_file']['tmp_name']) && is_uploaded_file($_FILES['up_file']['tmp_name']) ) {
      if($_FILES['up_file']['name']) {
        if(substr_count('/', $_FILES['up_file']['name'])) {
          $start = strrpos( $_FILES['up_file']['name'], '/' ) + 1 ;
          $up_file = substr($_FILES['up_file']['name'],$start) ;
        }
        else {
          $up_file = $_FILES['up_file']['name'] ;
        }
      }
      else {
        $up_file = $_FILES['up_file']['tmp_name'] ;
      }
      if( upload_file($_FILES['up_file']['tmp_name'] , $fpath , $up_file) ) {
        closedir($handle); 
        $handle = opendir($fpath); 
      }
    }
    // delete
    if(is_array($delete)) {
      foreach($delete as $delfile) {
        del_file($fpath,$delfile) ;
      }
    }
    // list files
    $tpath = is_dir("{$fpath}/_th") ? "{$fpath}/_th" : '' ;
    while(false !== ($cfile = readdir($handle))) {
      if($cfile != "." && $cfile != ".." && $cfile != "Thumbs.db" && istype($fpath,$cfile,'file') && ( ( ! $filter ) || substr_count( strtolower($cfile), strtolower($filter) ) ) ) {
        $files[] = $cfile;
        $thumbs[] = $tpath && file_exists("{$tpath}/{$cfile}") && filemtime("{$tpath}/{$cfile}") == filemtime("{$fpath}{$cfile}") ? $cfile : '' ;
      }
    }
    closedir($handle);
    natcasesort($files);
    ihide('fpath',str_replace('/','|',$fpath),'fid',$fid,'file',$file,'filter',$filter); 
    ?>
    <table border="0" cellpadding="0" cellspacing="0" summary="" width="100%"> 
      <tr> 
        <td> 
          <div style="float:right;"> 
            <div class="fileUpload">
              <?
              $nm = 'up_file' ;
              $id = 'id_' . $nm ;
              $hd = 'hd_' . $nm ;
              $bt = 'bt_' . $nm ;
              $onc = "getId('{$hd}').value = getId('{$id}').value; onbrowse('{$fid}')" ;
              $omo = "getId('{$bt}').setAttribute('class', 'button1')";
              $omx = "getId('{$bt}').setAttribute('class', 'button')";
              ?>
              <input type="file" onchange="<?= $onc ; ?>" name="<?= $nm ; ?>" class="browserHidden" id="<?= $id ; ?>" onmouseover="<?= $omo ; ?>" onmouseout="<?= $omx ; ?>"> 
              <div class="browserVisible"> 
                <input type="text" class="input" id="<?= $hd ; ?>" style="width:110px;"> <span id="<?= $bt ?>" class="button"><a href="#">BROWSE</a></span> 
              </div>
            </div>
          </div>
          <div style="float:right; padding:3px 5px 0 0;"><b>Upload File:</b> </div>
          <p style="padding:5px 0; margin-bottom:20px;"><b>Folder:</b>&nbsp;&nbsp;<?= $fpath ; ?></p>
          <table border="0" cellpadding="6" cellspacing="0" class="tabler" summary="" width="100%">
            <tr class="th"> 
              <td align="left"><b class="phead">File</b></td>
              <? if($fid) { ?><td align="center"><b class="phead">Select</b></td><? } ?>
              <td align="center"><b class="phead">Delete</b></td> 
              <td align="center"><b class="phead">Image</b></td> 
            </tr>
            <?
            foreach($files as $n => $cfile) {
              $ipath = build_path($fpath,$cfile,'lastfile') ;
              $onc = "optsel('" . $fid . "','" . $cfile . "')";
              ?>
            <tr> 
              <td align="left" style="<?= ($file == $cfile)?'font-weight:bold;':''; ?>"> 
                <a target="_blank" href="<?= $ipath ; ?>" name="<?= '' . urlencode($cfile) ; ?>"><?= $cfile ; ?></a><?
                if(isset($globvars['db_medtable']) && $globvars['db_medtable'] && isset($media_fp["{$fbase}{$cfile}"])) {
                  print '<br><br>' . $media_id[$media_fp["{$fbase}{$cfile}"]]['note'];
                }
                ?></td>
              <? if($fid) { ?><td align="center" style="white-space: nowrap">
                <span class="button"><a href="#" onclick="<?= $onc ; ?>">Select</a></span>
              </td><? } ?>
              <td align="center"> <input type="checkbox" name="delete[]" value="<?= $cfile ; ?>"></td> 
              <td align="center"><?
              if( in_array(pathinfo($ipath, PATHINFO_EXTENSION), $image_types) && file_exists($ipath) && mime_check($ipath,'image') && ($arr = @getimagesize($ipath)) ) { 
                if($tpath && isset($thumbs[$n]) && $thumbs[$n]) {
                  $ipath = str_replace($cfile,"_th/{$cfile}",$ipath);
                }
                ?><a target="_blank" href="<?= $ipath ; ?>">
                  <? if($lazy) { ?><img class="lazyi" data-src="<?= clean_link($ipath) ; ?>" alt=""><? } else { ?><img class="lazyi" src="<?= clean_link($ipath) ; ?>" alt=""><? } ?>
                </a><br><? 
                print $arr[0] . ' x ' . $arr[1] ;
              }
              ?>&nbsp;
              </td> 
            </tr>
              <?
            }
            ?>
          </table>
          <br>
          <br> 
          <p align="center"><input type="submit" name="Submit1" value="SAVE CHANGES" class="submit"></p>&nbsp;
        </td> 
      </tr> 
    </table> 
  </form></div>
     <?
  }
  else {
    ?>
    <p><b>Invalid Directory</b></p>
    <?
  }                  
  ?>
</body>
</html>
