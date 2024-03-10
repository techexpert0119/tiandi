<?
// https://github.com/devpelux/wcolpick
// https://github.com/devpelux/wcolpick/wiki
$scrpath = include_path(pathinfo(__FILE__)) ;
@include_once("{$scrpath}jquery.inc.php");
cs_file("{$scrpath}wcolpick/wcolpick/wcolpick.css");
cs_file("{$scrpath}wcolpick/wcolpick.css");
js_file("{$scrpath}wcolpick/wcolpick/wcolpick.js");
js_file("{$scrpath}wcolpick/wcolpick.js");

function color_picker($fname,$dval,$flen=7,$mlen=7) {
  $fid = 'id_' . $fname ;
  if($dval && substr($dval, 0, 1 ) != '#') {
    $dval = '#' . $dval;
  }
  ?>
  <input class="color_field" type="text" onkeyup="changepicker('<?= $fid ; ?>');" name="<?= $fname ; ?>" id="<?= $fid ; ?>" size="<?= $flen ; ?>" maxlength="<?= $flen ; ?>" value="<?= $dval ; ?>" onchange="fldchg++">
  <a title="Colour Picker" class="color_click" href="#" onclick="return showpicker('<?= $fid ; ?>');" style="background-color:<?= $dval ; ?>"></a>
  <? 
}
?>