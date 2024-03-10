<? @include_once('head.inc.php'); ?>
<!DOCTYPE html>
<html lang="en"> 
  <head>
  <?
  globvars('action','go','sub_page','done','del');

  if(($globvars['action'] == 'delete') && $globvars['del']) {
    $string = "delete from `products` where `r_id` = '{$globvars['del']}' limit 1";
    // print_p($string);
    $query = my_query($string);
    $globvars['msg'] = "Record {$globvars['del']} deleted<br>";
    $_POST['action'] = $_POST['del'] = '';
  }

  $globvars['allowadd'] = 0 ; // allow add
  $globvars['public'] = '../' ; // public page
  $_POST['sort'] = $_GET['sort'] = '';

  $string = "select * from `products`";
  $query = my_query($string);
  while($row = my_assoc($query)) {
    $globvars['products'][$row['r_id']] = $row;
  }

  $string = "select * from `pages_subs` left join `pages_subp` on `pages_subs`.`q_id` = `pages_subp`.`q_id` where `pages_subp`.`p_id` = 3 order by `pages_subs`.`q_id`, `pages_subs`.`r_id`";
  $query = my_query($string);
  while($row = my_assoc($query)) {
    $globvars['sub_pages'][$row['q_id'] . '_' . $row['r_id']] = $row;
    if(! isset($globvars['products'][$row['r_id']])) {
      $globvars['allowadd'] = 1 ; // allow add
    }
  }
  
  if(($globvars['action'] == 'edit') && isset($globvars['products'][$globvars['go']])) {
    $sp = $globvars['products'][$globvars['go']]['q_id'] . '_' . $globvars['products'][$globvars['go']]['r_id'];
    if(isset($globvars['sub_pages'][$sp])) {
      $globvars['public'] = '../systems/' . $globvars['sub_pages'][$sp]['q_url'] . '/' . $globvars['sub_pages'][$sp]['r_url'] ; 
    }
  }

  if($globvars['action'] == 'edit' || $globvars['action'] == 'add') {
    if($globvars['sub_page']) {
      $keys = explode( "_", $globvars['sub_page']);
      $_POST['r_id'] = $keys[1];
      $_POST['q_id'] = $keys[0];
    }
    else {
      $globvars['action'] = '';
    }
  }

  // print_arv($globvars['products']);
  // print_arv($globvars['sub_pages']);

  $globvars['debug'] = 0 ;
  $globvars['sq_table'] = 'products'; // table name
  // a=auto inc, b=break before (bb save), c=form edit, d=text disp, e=edit, f=file (+j=more), g=clean_urln, h=hide value, 
  // k=key, l=list, m=md5 entry, n=zero last, o=opts from table, p=color picker, q=filter, r=add similar, s=select multiple (ss sort), 
  // t=now(), u=link, v=view, w=null if blank, x=noshow, y=ckeditor, z=fake, 100=length/rows, _100=maxtext, (i not used)
  $globvars['sq_keys'] = array(
    'lkexu','le',
    'ble','e','ey','ef','e',
    'bbe','ey','ey','ey',
    'bey','ef',
    'bbef','e','e',
    'bef','e','e',
    'bef','e','e',
    'bef','e','e',
    'bbe','e','ex','e','ef','ey',
    'bbe','e','ex','e','ef','ey',
    'bbe','e','ex','e','ef','ey',
    'bbe','e','ex','e','ef','ey',
    'bbe','e','ex','e','ef','ey',
    'bbex','ex','ex','ex','efx','eyx',
    'bbe','ef','e','ef'
  ); // field keys
  $globvars['sq_names'] = array(
    ' ','Sub Page 2',
    'Intro Head','Sub-heading','Text','Image','Button',
    'Main Head','Column 1','Column 2','Column 3',
    'Detail Text','Detail Image',
    'Image 1','Text 1','Data 1',
    'Image 2','Text 2','Data 2',
    'Image 3','Text 3','Data 3',
    'Image 4','Text 4','Data 4',
    'Title','Pointer','','Data','Image','Text',
    'Title','Pointer','','Data','Image','Text',
    'Title','Pointer','','Data','Image','Text',
    'Title','Pointer','','Data','Image','Text',
    'Title','Pointer','','Data','Image','Text',
    'Title','Pointer','','Data','Image','Text',
    'Specification','File','Certificate','File'
  ); // field names
  $globvars['sq_notes'] = array(
    '','pagelink()',
    '','','','1000x530','',
    '','','','',
    '','1000x530',
    '70x65','','',
    '70x65','','',
    '70x65','','',
    '70x65','','',
    '','','','Separate left/right with colon','1000x1000','',
    '','','','Separate left/right with colon','1000x1000','',
    '','','','Separate left/right with colon','1000x1000','',
    '','','','Separate left/right with colon','1000x1000','',
    '','','','Separate left/right with colon','1000x1000','',
    '','','','Separate left/right with colon','1000x1000','',
    '','','',''
  ); // field notes
  $globvars['sq_notei'] = array(); // popup image
  $globvars['sq_lookt'] = array(); // opt tables
  $globvars['sq_lookk'] = array(); // opt keys
  $globvars['sq_lookv'] = array(); // opt values
  $globvars['sq_lookd'] = array(); // eg. 'k : v' or [[field]]
  $globvars['sq_lookl'] = array(); // ss multi link
  $globvars['sq_lookf'] = array(); // opt query eg. "WHERE `key` = 'x'" (or "WHERE `key` = '[[value]]'" only where $go)
  $globvars['sq_joint'] = array(); // multi join tables
  $globvars['sq_joink'] = array(); // multi join keys
  $globvars['sq_joinv'] = array(); // multi join values
  $globvars['sq_joino'] = array(); // multi join order (if ss)
  $globvars['sq_fpath'] = array(
    '','',
    '','','','intro','',
    '','','','',
    '','detail',
    'icon','','',
    'icon','','',
    'icon','','',
    'icon','','',
    '','','','','specs','',
    '','','','','specs','',
    '','','','','specs','',
    '','','','','specs','',
    '','','','','specs','',
    '','','','','specs','',
    '','pdf','','pdf'
  ); // extra file paths
  $globvars['sq_fmake'] = array(); // arrnum[v this]-width-height-[mecpf]-[qual,85]-[del,y1/n0]-[force overwrite,y1/n0]
  $globvars['sq_deflt'] = array(); // default values
  $globvars['sq_funct'] = array(
    '','sub_page',
    '','','','','',
    '','','','',
    '','',
    '','','',
    '','','',
    '','','',
    '','','',
    '','pointer','','','','',
    '','pointer','','','','',
    '','pointer','','','','',
    '','pointer','','','','',
    '','pointer','','','','',
    '','pointer','','','','',
    '','','',''
  ); // call functions
  $globvars['sq_jcall'] = array(); // call jquery
  $globvars['sq_heads'] = array(
    '','',
    'Summary on Models page','','','','',
    'Introduction on Products Page','','','',
    'Details with parts image','',
    'Icon Strip','','',
    '','','',
    '','','',
    '','','',
    'Specifications 1','','','','','',
    'Specifications 2','','','','','',
    'Specifications 3','','','','','',
    'Specifications 4','','','','','',
    'Specifications 5','','','','','',
    'Specifications 6','','','','','',
    'PDF Files','','',''
  ); // break headings (where 'b')
  $globvars['sq_style'] = array(); // style override

  $globvars['sq_export'] = ''; // export heads array or match keys eg. 'le' or '' for all
  $globvars['sq_exptot'] = ''; // export totals array or '' for none
  $globvars['sq_list'] = ''; // column order array or '' for default

  $globvars['sq_dsort'] = ''; // default sort (reverse _DESC)
  $globvars['sq_ajoin'] = "left join `pages_subs` on `pages_subs`.`r_id` = `products`.`r_id` order by `pages_subs`.`q_id`, `pages_subs`.`r_order`" ; // join filter string for list
 
  $globvars['plogo'] = isset($globvars['admin_logo']) ? $globvars['admin_logo'] : 'logo.png' ; // logo
  $globvars['ptitle'] =  'Product Details' ; // page title
  $globvars['adminm'] = 'index.php' ; // admin menu
  $globvars['pubtext'] = '' ; // alternate button
  $globvars['publicid'] = '' ; // public page id
  $globvars['publicfld'] = '' ; // public page field or array
  $globvars['publicfjn'] = '' ; // join for publicfld
  $globvars['maxdisp'] = 50 ; // max display in list
  $globvars['maxbox'] = 50 ; // max edit box size
  $globvars['maxtext'] = 100 ; // max text length in list
  $globvars['maxbutts'] = 15 ; // max next links in list
  $globvars['mainwidth'] = 1300 ; // main width
  $globvars['listwidth'] = 1300 ; // list width
  $globvars['formwidth'] = 1300 ; // form width
  $globvars['formleftc'] = 150 ; // form left column
  $globvars['formrghtc'] = 200 ; // form right column
  $globvars['textarows'] = 6 ; // textarea rows
  $globvars['textacols'] = 55 ; // textarea cols

  $globvars['filepath'] = '../images/product/' ; // file path
  $globvars['fprefpadd'] = 0 ;// add record ref to filepath (number pad zeroes OR 0 = n/a)
  $globvars['filefilt'] = '' ; // filter filenames in selector
  $globvars['allowdel'] = 1 ; // allow delete
  $globvars['allowsim'] = 0 ; // add similar (fields = r)
  $globvars['edlink'] = 'Edit' ; // edit link (number pad zeroes OR text, '' default Edit)
  $globvars['makefile'] = '' ; // array(arrnum, 'pageb.inc.php', 'param1,param2,etc' )
  $globvars['makeurln'] = '' ; // array(arrto , arrfrom)
  $globvars['fnosuff'] = 1 ; // 1 if no suffix on image make
  $globvars['hidesearch'] = 0 ; // 1 to hide search
  $globvars['hidefilter'] = 0 ; // 1 to hide filter
  $globvars['hideflink'] = 1 ; // 1 to hide filter links
  $globvars['hidexport'] = 0 ; // 1 to hide export
  $globvars['multchange'] = 0 ; // 1 to show list multiple change
  $globvars['listedgo'] = 0 ; // 1 to show edit go
  $globvars['prevnext'] = 0 ; // 1 to show preious/next
  $globvars['rangefilt'] = '' ; // '' none or array|type (date or length)
  $globvars['mfilter'] = '' ; // add master filter for query
  $globvars['listcols'] = array(); // array of extra columns/functions
  $globvars['expvars'] = array('dstamp' => 1, 'maxlen' => 50, 'maxtext' => 70, 'xformat' => 'xlsx', 'lookv' => 'v');

  head();
  ?>
    <title><?= $globvars['ptitle'] ; ?></title> 
    <script type="text/javascript">
    $(document).ready(function(){
      $(".specs_img").click(function(e) {
        var img = $(this);
        var spn = $(this).attr('data-spn');
        var originalWidth = img[0].naturalWidth;
        var originalHeight = img[0].naturalHeight;
        var displayWidth = img.width() - 1;
        var displayHeight = img.height() - 1;
        var pageoffset = img.offset();
        var imagescale = originalWidth/displayWidth;
        var relativeX = Math.round((e.pageX - pageoffset.left) * imagescale);
        var relativeY = Math.round((e.pageY - pageoffset.top) * imagescale);
        $('#s_specs_xpos'+spn).val(relativeX);
        $('#s_specs_ypos'+spn).val(relativeY);

        px = (relativeX - (49/2)) / Number($('#point_prop').val());
        py = (relativeY - 70) / Number($('#point_prop').val());
        $(this).parent().children('div').css('top',py).css('left',px).show();
      });
    });
    </script>
  </head> 
  <body> 
  <?
  @include_once('mysql.inc.php');
  /* ?>
    <form><? */

  function sub_page() {
    global $globvars; extract($globvars);
    // fields:  dval, i_row, fname (or thiscol), c, s, fnamev (posted), c_row, ftype, fprms 
    // print $i_row['q_id'] . '_' . $i_row['r_id'] . ' &nbsp; ';
    if($go || ($action == 'add')) { // edit form
      ?>
      <select name="sub_page">
        <? 
        foreach($globvars['sub_pages'] as $k => $a) { 
          if($i_row['r_id'] == $a['r_id'] || ! isset($products[$a['r_id']])) {
            if($i_row['q_id'] == $a['q_id'] && $i_row['r_id'] == $a['r_id']) { ?>
              <option value="<?= $k ?>" selected="selected"><?= 'systems/' . $a['q_url'] . '/' . $a['r_url'] ?></option>
            <? } else { ?>
              <option value="<?= $k ?>"><?= 'systems/' . $a['q_url'] . '/' . $a['r_url'] ?></option>
        <? } } } ?>
      </select>
      <?
    }
    else { // item list
      if(isset($globvars['sub_pages'][$i_row['q_id'] . '_' . $i_row['r_id']])) {
        $pge = $globvars['sub_pages'][$i_row['q_id'] . '_' . $i_row['r_id']];
        print 'systems/' . $pge['q_url'] . '/' . $pge['r_url'];
      }
    }

  }
  
  function pagelink() {
    global $globvars; extract($globvars);
    $res = '';
    $string = "select * from `pages_subs` where `r_id` = '{$i_row['r_id']}' and `q_id` = '{$i_row['q_id']}' limit 1";
    $query = my_query($string);
    if(my_rows($query)) {
      $row = my_assoc($query);
      $res = '<div class="buttonr"><a style="width:110px;background-color:#D71A23" href="pages_subs.php?action=edit&amp;go=' . $row['r_id'] . '" target="pages_subs.php">EDIT PAGE</a></div>';
    }
    print $res;
  }

  function pointer() {
    global $globvars; extract($globvars);
    $n = str_replace('s_specs_xpos','',$fname);
    $f = '../images/product/detail/' . $i_row['s_detail_image'];    
    if($i_row['s_detail_image'] && file_exists($f)) {
      $d = 600 ;
      if(! isset($globvars['point_img'])){
        $globvars['point_img'] = get_image($f);
        $w = $globvars['point_img']['width'];
        $globvars['point_prop'] = $globvars['point_img']['width'] / $d ;
        ?>
        <input type="hidden" id="point_prop" value="<?= $globvars['point_prop'] ?>">
        <?
      }
      if($globvars['point_prop']) {
        $i = 49 / $globvars['point_prop'] ;
        $x = ($i_row['s_specs_xpos'.$n] - (49/2)) / $globvars['point_prop'];
        $y = ($i_row['s_specs_ypos'.$n] - 70) / $globvars['point_prop'];
        $v = $i_row['s_specs_xpos'.$n] + $i_row['s_specs_ypos'.$n] ? 'display:block' : 'display:none';
        ?>
        <div style="display:inline-block; vertical-align:middle; position:relative">
          <img class="specs_img" data-spn="<?= $n ?>" src="<?= $f ?>" style="width:<?= $d ?>px;border:1px solid #C0C0C0">
          <div style="<?= $v ?>; position:absolute; top:<?= $y ?>px; left:<?= $x ?>px; width:<?= $i ?>px"><img src="../images/expand_icon.png" style="width:100%"></div>
        </div>
        <div style="display:inline-block; vertical-align:middle; margin-left:20px;">
          Click image to position the pointer<br><br>
          Check on page to ensure no overlap<br><br><br>
          X <input type="text" name="s_specs_xpos<?= $n ?>" id="s_specs_xpos<?= $n ?>" size="4" maxlength="4" value="<?= $i_row['s_specs_xpos'.$n] ?>" onchange="fldchg++;" autocomplete="off"> &nbsp; 
          Y <input type="text" name="s_specs_ypos<?= $n ?>" id="s_specs_ypos<?= $n ?>" size="4" maxlength="4" value="<?= $i_row['s_specs_ypos'.$n] ?>" onchange="fldchg++;" autocomplete="off">
        <?
      }
    }
    else {
      print 'No detail image chosen above';
    }
  }
  
  function link_model() {
    global $globvars; extract($globvars);
    // print_arr($a_row);
    // print $a_row['pages_subp.q_menu'];
    if(isset($globvars['products'][$i_row['s_id']])) {
      ?>
      <div class="buttonr"><a style="width:110px" href="models.php?action=edit&amp;go=<?= $globvars['products'][$i_row['s_id']]['q_id'] ?>" target="models.php">EDIT MODEL</a></div>
      <?
    }
  }

  function rel_model() {
    global $globvars; extract($globvars);
    // print_arr($a_row);
    // print $a_row['pages_subp.q_menu'];
    if(isset($globvars['products'][$i_row['s_id']])) {
      print $globvars['products'][$i_row['s_id']]['q_menu'];
    }
  }
  
  function rel_product() {
    global $globvars; extract($globvars);
    // print $a_row['pages_subs.r_menu'];
    if(isset($globvars['products'][$i_row['s_id']])) {
      print $globvars['products'][$i_row['s_id']]['r_menu'] ? $globvars['products'][$i_row['s_id']]['r_menu'] : $globvars['products'][$i_row['s_id']]['r_url'];
    }
  }
  
  function simage() {
    global $globvars; extract($globvars);
    // fields: c_row, i_row, c, s, fname (or thiscol), fnamev (posted), ftype, fprms, dval
    $pthp = $pthv = $fpath ; // both default to file path
    $imgp = $imgv = $dval ; // both default to file name
    if((! $action) && $imgv && file_exists($imgv1 = $pthv . $imgv)) {
      $imgh = 50 ;
      if($imgp && file_exists($imgp1 = $pthp . $imgp)) {
        $poph = 200 ;
        $offx = 50 ;
        $offy = $poph / 2 ;
        $omo = "ShowContent('id_grid_{$fname}{$s}',$offx,$offy); return true;";
        $omx = "HideContent('id_grid_{$fname}{$s}'); return true;";
        ?>
        <a onmousemove="<?= $omo ?>" onmouseover="<?= $omo ?>" onmouseout="<?= $omx ?>" onclick="<?= $omo ?>" href="#" style="display:block;">
        <? } else { $poph = 0 ; } ?>
        <img src="<?= clean_url($imgv1); ?>" style="<?= 'max-height:' . $imgh . 'px; max-width:' . ( $imgh * 2 ) . 'px' ; ?>" alt="" border="">
        <? if($poph) { ?>
        </a><div id="<?= 'id_grid_' . $fname . $s ; ?>" style="display:none; position:absolute; border: solid 1px black; background-color: white; padding:5px; z-index:999">
        <img alt="" border="0" src="<?= clean_url($imgp1); ?>" height="<?= $poph ; ?>">
        </div>
        <? } 
    }
    else {
      // return true for normal display
      return true ; 
    }
  }

  function _list_row_style($i_row) {
    $bgc = '';
    if($i_row['done'] == 'N') {
      $bgc = '#FFCC99';
    }
    elseif($i_row['done'] == 'Y') {
      $bgc = '#EFDEDE';
    }
    if($bgc) {
      print "background-color:$bgc;";
    }
  }
  
  function _filter_all() {
    global $globvars; extract($globvars);
    $string = "SELECT * FROM `cat_sub` LEFT JOIN `cat_main` ON `cat_sub`.`c_id`=`cat_main`.`c_id` ORDER BY `cat_main`.`c_id`, `cat_sub`.`s_name` ";
    $query = my_query($string);
    $cat_sel = array();
    while($a_row = my_array($query)) { 
      $cat_sel[$a_row['s_id']] = $a_row;
    }
    globvadd('cat_sel',$cat_sel);        
    $prev = ''; $optg = 0 ;
    ?>
      <select class="chosen-select" id="filter" name="filter" size="1" style="font-size:11px; width:150px;"
       onclick="$('#search').val('');" onchange="$('#lform').submit();"> 
        <option value="">*** ALL ***</option> 
        <? 
        foreach($cat_sel as $a_row) { 
          if($prev != $a_row['c_id']) { ?>
        <optgroup label="<?= clean_upper($a_row['c_name']); ?>">
          <? $optg++; } ?>
        <option value="<?= optsel('s_id|'.$a_row['s_id'],filter_str($filter)); ?>"><?= $a_row['s_name']; ?></option>
        <? $prev = $a_row['c_id']; } 
          if($optg) { ?>
        </optgroup>
        <? } ?>
      </select> 
    <? 
  }

  function _cat_sel() {
    global $globvars; extract($globvars);
    if(! (isset($cat_sel) && is_array($cat_sel)) ) {
      $string = "SELECT * FROM `cat_sub` LEFT JOIN `cat_main` ON `cat_sub`.`c_id`=`cat_main`.`c_id` ORDER BY `cat_main`.`c_id`, `cat_sub`.`s_name` ";
      $query = my_query($string);
      $cat_sel = array();
      while($a_row = my_array($query)) { 
        $cat_sel[$a_row['s_id']] = $a_row;
      }
      globvadd('cat_sel',$cat_sel);        
    }
    if( (isset($i_row[0]) && $i_row[0] == $go) || ($action == 'add')) {
      // edit form
      $prev = '';
      ?>
      <select class="chosen-select" id="<?= 'id_' . $fname ?>" name="<?= $fname ?>"
       size="1" onchange="fldchg++"> 
        <option value="">*** Select ***</option> 
        <? foreach($cat_sel as $a_row) { 
          if($prev != $a_row['c_id']) { if($prev) { ?>
        <option value="">&nbsp;</option> 
          <? } ?>
        <option style="text-decoration:underline;"
         value="<?= $a_row['s_id']; ?>"><?= clean_upper($a_row['c_name']); ?></option>
          <? } ?>
        <option class="link" value="<?= optsel($a_row['s_id'],$dval); ?>">
          &raquo;  <?= $a_row['s_name']; ?></option>
        <? $prev = $a_row['c_id']; } ?>
      </select>      
      <?
    }
    else {
      // item list
      // print_arr($cat_sel);
      if(isset($cat_sel[$i_row[$thiscol]]['c_name'])) {
        print $cat_sel[$i_row[$thiscol]]['c_name'] . ' &raquo; ' ;
      }
      if(isset($cat_sel[$i_row[$thiscol]]['s_name'])) {
        print $cat_sel[$i_row[$thiscol]]['s_name'] ;
      }
    }
  }

  function _filt_opts() {
    global $globvars; extract($globvars);
  }

  function _form_foot() {
    global $globvars; extract($globvars);
  }

  function _list_foot() {
    global $globvars; extract($globvars);
  }

  function _sq_funct() {
    global $globvars; extract($globvars);
    // fields:  dval, i_row, fname (or thiscol), c, s, fnamev (posted), c_row, ftype, fprms 
    if($go || ($action == 'add')) { // edit form
      if(isset($globvars['save'])) { $globvars['save']++; }
      print $dval;
    }
    elseif($action == 'export') { // export
      print $i_row[$fname]; // same as dval
    }
    else { // item list
      return true ; // for normal display
    }
  }

  /* ?>
    </form><? */
  ?>
  </body>
</html>