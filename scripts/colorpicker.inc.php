<? 
// http://www.dynamicdrive.com/dynamicindex11/ddcolorpicker/ (using this)
?>
<!-- YUI Dependencies -->  
<script type="text/javascript" src="../scripts/colorpicker/utilities.js"></script> 
<script type="text/javascript" src="../scripts/colorpicker/slider-min.js" ></script> 

<!-- Color Picker source files for CSS and JavaScript --> 
<link rel="stylesheet" type="text/css" href="../scripts/colorpicker/colorpicker.css"> 
<script type="text/javascript" src="../scripts/colorpicker/colorpicker-min.js" ></script>

<? if(! substr_count( $globvars['php_path'], 'control/' )) { ?>
<script type="text/javascript" src="../scripts/colorpicker/windowfiles/dhtmlwindow.js"></script>
<? } ?>

<link rel="stylesheet" type="text/css" href="../scripts/colorpicker/windowfiles/dhtmlwindow.css" />

<script type="text/javascript" src="../scripts/colorpicker/ddcolorpicker.js"></script>
<style type="text/css">
* html .yui-picker-bg{
filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='../scripts/colorpicker/picker_mask.png',sizingMethod='scale');
}
.colorpreview{
  border: 1px solid black;
  padding: 1px 10px;
  cursor: hand;
  cursor: pointer;
}
</style>
<? 
function color_body() {
  ?>
<script type="text/javascript" src="../scripts/colorpicker/windowfiles/dhtmlwindow.js"></script>
  <?
}

function color_picker($fname,$dval,$flen=7,$mlen=7) {
  global $cp_arr ;
  if(! isarr($cp_arr)) { $cp_arr = array() ; }
  $cp_arr[] = $fname ;
  $fid = 'id_' . $fname ;
  $cid = 'ic_' . $fname ;
  ?>
  <div>
  <input type="text" id="<?= $fid ; ?>" name="<?= $fname ; ?>" size="<?= $flen ; ?>" maxlength="<?= $flen ; ?>" value="<?= $dval ; ?>" onchange="fldchg++">
  <span id="<?= $cid ; ?>" class="colorpreview">&nbsp;</span>
  </div>
  <? 
}

function color_widget() {
  global $cp_arr ;
  if(isarr($cp_arr)) {
    $fields = '';
    foreach($cp_arr as $fname) {
      $fields .= "'id_{$fname}:ic_{$fname}', " ;
    }
    $fields = substr($fields,0,-2) ;
    ?>
    <div id="ddcolorwidget">
    <div id="ddcolorpicker" style="position:relative;"></div>
    </div>
    <script type="text/javascript">
  ddcolorpicker.init({
    colorcontainer: ['ddcolorwidget', 'ddcolorpicker'], //id of widget DIV, id of inner color picker DIV
    displaymode: 'float', //'float' or 'inline'
    floatattributes: ['Select Colour', 'width=345px,height=190px,resize=1,scrolling=1,center=1'], //'float' window attributes
    fields: [<?= $fields ; ?>] //[fieldAid[:optionalcontrolAid], fieldBid[:optionalcontrolBid], etc]
  })  
    </script>
    <?
  }
}

?>