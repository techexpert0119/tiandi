<? @include_once('head.inc.php'); ?>
<!DOCTYPE html>
<html lang="en"> 
  <head>
  <?
  $globvars['debug'] = 0 ;
  $globvars['sq_table'] = 'ticker'; // table name
  // a=auto inc, b=break before (bb save), c=form edit, d=text disp, e=edit, f=file (+j=more), g=clean_urln, h=hide value, 
  // k=key, l=list, m=md5 entry, n=zero last, o=opts from table, p=color picker, q=filter, r=add similar, s=select multiple (ss sort), 
  // t=now(), u=link, v=view, w=null if blank, x=noshow, y=ckeditor, z=fake, 100=length/rows, _100=maxtext, (i not used)
  $globvars['sq_keys'] = array('lkua','lecn','le','le'); // field keys
  $globvars['sq_names'] = array(' ','','','URL'); // field names
  $globvars['sq_notes'] = array(); // field notes
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
  $globvars['sq_fpath'] = array(); // extra file paths
  $globvars['sq_fmake'] = array(); // arrnum[v this]-width-height-[mecpf]-[qual,85]-[del,y1/n0]-[force overwrite,y1/n0]
  $globvars['sq_deflt'] = array(); // default values
  $globvars['sq_funct'] = array(); // call functions
  $globvars['sq_jcall'] = array(); // call jquery
  $globvars['sq_heads'] = array(); // break headings (where 'b')
  $globvars['sq_style'] = array(); // style override

  $globvars['sq_export'] = ''; // export heads array or match keys eg. 'le' or '' for all
  $globvars['sq_exptot'] = ''; // export totals array or '' for none
  $globvars['sq_list'] = ''; // column order array or '' for default

  $globvars['sq_dsort'] = 'order'; // default sort (reverse _DESC)
  $globvars['sq_ajoin'] = "" ; // join filter string for list
 
  $globvars['plogo'] = isset($globvars['admin_logo']) ? $globvars['admin_logo'] : 'logo.png' ; // logo
  $globvars['ptitle'] =  'Ticker' ; // page title
  $globvars['adminm'] = 'index.php' ; // admin menu
  $globvars['public'] = '../' ; // public page
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
  $globvars['textarows'] = 3 ; // textarea rows
  $globvars['textacols'] = 55 ; // textarea cols

  $globvars['filepath'] = '../images/' ; // file path
  $globvars['fprefpadd'] = 0 ;// add record ref to filepath (number pad zeroes OR 0 = n/a)
  $globvars['filefilt'] = '' ; // filter filenames in selector
  $globvars['allowdel'] = 1 ; // allow delete
  $globvars['allowadd'] = 1 ; // allow add
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
  </head> 
  <body> 
  <?
  @include_once('mysql.inc.php');
  /* ?>
    <form><? */

  function _simage() {
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
        <img src="<?= clean_url($imgv1); ?>" style="<?= 'max-height:' . $imgh . 'px; max-width:' . ( $imgh * 2 ) . 'px' ; ?>" alt="" border=""> &nbsp; <?= $imgv ; ?>
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