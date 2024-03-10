<? @include_once('head.inc.php'); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<HTML> 
  <HEAD>
  <? 
  globvars('vauto','vcode');
  global $globvars; extract($globvars,EXTR_SKIP);
  if(($action == 'add') && $done && $vauto) {
    $string = "SELECT * FROM `vouchers` WHERE `vcode` LIKE '{$vcode}%' ORDER BY `vcode`";
    $query = my_query($string);
    $n = 99 ;
    if(my_rows($query)) {
      while($a_row = my_array($query)) {
        $x = substr( $a_row['vcode'], strlen($vcode) ) ;
        if($x && is_numeric($x) && ($x > $n)) {
          $n = $x ;
        }
      }
    }
    $_POST['vcode'] = $vcode . ++$n ;
  }

  $globvars['debug'] = 0;
  $globvars['sq_table'] = 'vouchers'; // table name
  // l=list, u=link, a=auto inc, k=key, e=edit, v=view, h=hide value, x=noshow, t=now() on insert, p=color picker
  // m=md5 entry, o=opts from table, s=select multiple, f=file, w=wysiwig, y=ckeditor, d=text disp, b=break before
  $globvars['sq_keys'] = array('lkua','le','le','le','xle','le','le','le','le','le','lvt','lv','lv','es'); // field keys
  $globvars['sq_names'] = array(' ','Voucher','','','','','','',''); // field names
  $globvars['sq_notes'] = array('','Unique code','','','no = do not allow finance if used','&pound; discount','<u>or</u> % discount (ex shipping)','Min &pound; spend (ex shipping)','Blank for any','Blank for any','Created date','No. times used','Last used date','Restrict voucher to specific product(s) only'); // field notes
  $globvars['sq_lookt'] = array('','','','','','','','','','','','','','shop_items'); // opt tables
  $globvars['sq_lookk'] = array('','','','','','','','','','','','','','i_id'); // opt keys
  $globvars['sq_lookv'] = array('','','','','','','','','','','','','','i_head'); // opt values
  $globvars['sq_lookd'] = array('','','','','','','','','','','','','','[[c_head]]/[[s_head]]/[[i_head]]'); // eg. 'k : v'
  $globvars['sq_lookf'] = array('','','','','','','','','','','','','',"left join `shop_subs` on `shop_subs`.`s_id` = `shop_items`.`s_id` left join `shop_cats` on `shop_subs`.`c_id` = `shop_cats`.`c_id`"); // opt query eg. "WHERE `key` = 'x'" or "WHERE `key` = '[[value]]'"
  $globvars['sq_fpath'] = array(); // extra file paths
  $globvars['sq_fmake'] = array(); // arrnum-width-height-[c|m|f]-[qual]-[del]
  $globvars['sq_deflt'] = array(); // default values
  $globvars['sq_funct'] = array('','vcode'); // call functions
  $globvars['sq_heads'] = array(); // break headings (where 'b')
  
  $globvars['sq_export'] = ''; // Export heads array or '' variable for all
  $globvars['sq_dsort'] = 'vcode'; // default sort (reverse _DESC)
 
  $globvars['plogo'] = $globvars['admin_logo'] ; // logo
  $globvars['ptitle'] =  'Discount Vouchers' ; // page title
  $globvars['adminm'] = 'index.php' ; // admin menu
  $globvars['public'] = '../' ; // public page
  $globvars['pubtext'] = '' ; // alternate button
  $globvars['publicid'] = '' ; // public page id
  $globvars['maxdisp'] = 50 ; // max display in list
  $globvars['maxbox'] = 50 ; // max edit box size
  $globvars['maxtext'] = 100 ; // max text length in list
  $globvars['mainwidth'] = 1300 ; // main width
  $globvars['listwidth'] = 1300 ; // list width
  $globvars['formwidth'] = 1300 ; // form width
  $globvars['formleftc'] = 120 ; // form left column
  $globvars['formrghtc'] = 270 ; // form right column
  $globvars['textarows'] = 5 ; // textarea rows
  $globvars['textacols'] = 55 ; // textarea cols
  $globvars['filepath'] = '' ; // file path
  $globvars['fprefpadd'] = 0 ;// add record ref to filepath (number pad zeroes OR 0 = n/a)
  $globvars['filefilt'] = '' ; // filter filenames in selector
  $globvars['allowdel'] = 1 ; // allow delete
  $globvars['allowadd'] = 1 ; // allow add
  $globvars['edlink'] = '' ; // edit link (number pad zeroes OR text, blank default Edit)
  $globvars['makefile'] = '' ; // array(arrnum, 'pageb.inc.php', 'param1,param2,etc' )
  $globvars['hidexport'] = 1 ; // 1 to hide export

  head();
  ?>
    <TITLE><?= $globvars['ptitle'] ; ?></TITLE> 
  </HEAD> 
  <BODY> 
  <?
  @include_once('mysql.inc.php');
  /* ?>
    <FORM><? */

  function vcode() {
    global $globvars; extract($globvars,EXTR_SKIP);
    if($go || ($action == 'add')) {
    ?>
    <INPUT TYPE="TEXT" ID="id_vcode" NAME="vcode" SIZE="14" MAXLENGTH="14" VALUE="<?  print $i_row['vcode'] ?>" ONCHANGE="fldchg++">
    <?  if($action=='add') { ?> 
    Auto add next number using prefix <INPUT TYPE="CHECKBOX" ID="id_vauto" NAME="vauto" VALUE="1" ONCHANGE="fldchg++">
    <? 
    } } else { return true; }
  }

  function _simage() {
    global $globvars; extract($globvars);
    // fields: c_row, i_row, c, s, fname (or thiscol), fnamev (posted), ftype, fprms, dval
    $pthp = $pthv = $fpath ; // both default to file path
    $imgp = $imgv = $dval ; // both default to file name
    if((! $action) && $imgv && file_exists($imgv1 = $pthv . $imgv)) {
      $imgh = 50 ;
      if($imgp && file_exists($imgp1 = $pthp . $imgp)) {
        $poph = 300 ;
        $offx = 50 ;
        $offy = $poph / 2 ;
        $omo = "ShowContent('id_grid_{$fname}{$s}',$offx,$offy); return true;";
        $omx = "HideContent('id_grid_{$fname}{$s}'); return true;";
        ?>
        <A ONMOUSEMOVE="<?= $omo ?>" ONMOUSEOVER="<?= $omo ?>" ONMOUSEOUT="<?= $omx ?>" ONCLICK="<?= $omo ?>" HREF="#" style="display:block;">
        <? } else { $poph = 0 ; } ?>
        <IMG SRC="<?= $imgv1 ?>" HEIGHT="<?= $imgh ?>" ALT="" BORDER=""> &nbsp; <?= $imgv ; ?>
        <? if($poph) { ?>
        </A><DIV ID="<?= 'id_grid_' . $fname . $s ; ?>" STYLE="display:none; position:absolute; border: solid 1px black; background-color: white; padding:5px; z-index:999">
        <IMG ALT="" BORDER="0" SRC="<?= $imgp1 ?>" HEIGHT="<?= $poph ; ?>">
        </DIV>
        <? } 
    }
    else {
      // return true for normal display
      return true ; 
    }
  }

  /* ?>
    </FORM><? */
  ?>
  </BODY>
</HTML>