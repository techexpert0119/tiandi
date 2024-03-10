<!doctype html>
<html lang="en">
 <head>
  <title>tiandi_en</title>
 </head>
 <body>
  <table cellpadding="4" cellspacing="0">
 
  <tr><td colspan="3"><br><b>Daily mySQL</b></td></tr>
  <?
  $files = $ftime = array();
  if ($handle = opendir("daily")) {
    while(false !== ($file = readdir($handle))) {
      if(substr_count($file,'.gz')) {
        $files[] = $file;
        $ftime[] = date("d/m/Y H:i", filemtime('daily/' . $file));
      }
    }
    closedir($handle);
    array_multisort($ftime, SORT_DESC, $files, SORT_ASC);
    foreach($files as $k => $file) {
      ?>
      <tr><td><a href="<?= 'daily/' . $file ?>"><?= $file ?></a></td><td>&nbsp;</td><td><?= $ftime[$k] ?></td></tr>
      <?
    }
  }
  ?>  

  <tr><td colspan="3"><br><b>Weekly mySQL</b></td></tr>
  <?
  $files = array();
  if ($handle = opendir("mysql")) {
    while(false !== ($file = readdir($handle))) {
      if(substr_count($file,'.gz')) {
        $files[] = $file;
      }
    }
    closedir($handle);
    rsort($files);
    foreach($files as $file) {
      $ftime = date("d/m/Y H:i", filemtime('mysql/' . $file))
      ?>
      <tr><td><a href="<?= 'mysql/' . $file ?>"><?= $file ?></a></td><td>&nbsp;</td><td><?= $ftime ?></td></tr>
      <?
    }
  }
  ?>  

  <tr><td colspan="3"><br><b>Monthly Files</b></td></tr>
  <?
  $files = array();
  if ($handle = opendir("files")) {
    while(false !== ($file = readdir($handle))) {
      if(substr_count($file,'.gz')) {
        $files[] = $file;
      }
    }
    closedir($handle);
    rsort($files);
    foreach($files as $file) {
      $ftime = date("d/m/Y H:i", filemtime('files/' . $file))
      ?>
      <tr><td><a href="<?= 'files/' . $file ?>"><?= $file ?></a></td><td>&nbsp;</td><td><?= $ftime ?></td></tr>
      <?
    }
  }
  ?>  
  </table><br>
 </body>
</html>