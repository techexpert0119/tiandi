<? 
@include_once('head.inc.php'); 
if(! $globvars['cntrl_admin']) { header('location:index.php'); }
?>
<!DOCTYPE html>
<html lang="en"> 
  <head>
  <?
  globvars('administrator');
  if($globvars['done']) {
    $string = "select * from `admin_users` where `administrator` = 'y'";
    if( ($globvars['action'] == 'delete') && $globvars['del'] ) {
      $query = my_query($string);
      $c = 0 ;
      while($a_row = my_assoc($query)) {
        if($a_row['id'] != $globvars['del']) {
          $c++;
        }
      }
      if($c < 1) {
        $globvars['msg'] = "Make another user Administrator before deleting this user";
        $_POST['action'] = 'edit';
        $_POST['go'] = $globvars['del'];
        $_POST['del'] = '';
      }
    }
    elseif( ($globvars['action'] == 'edit') && $globvars['go'] ) {
      $query = my_query($string);
      $c = 0 ;
      while($a_row = my_assoc($query)) {
        if($a_row['id'] != $globvars['go'] || $globvars['administrator'] == 'y') {
          $c++; 
        }
      }
      if($c < 1) {
        $globvars['msg'] = "Make another user Administrator before removing from this user<br>";
        $_POST['action'] = 'edit';
        $_POST['administrator'] = 'y';
      }
    }
  }

  $globvars['debug'] = 0 ;
  $globvars['sq_table'] = 'admin_users'; // table name
  // a=auto inc, b=break before (bb save), c=form edit, d=text disp, e=edit, f=file (+j=more), g=clean_urln, h=hide value, 
  // k=key, l=list, m=md5 entry, n=zero last, o=opts from table, p=color picker, q=filter, r=add similar, s=select multiple (ss sort), 
  // t=now(), u=link, v=view, w=null if blank, x=noshow, y=ckeditor, z=fake, 100=length/rows, _100=maxtext, (i not used)
  $globvars['sq_keys'] = array('lkuah','le','em','le','es','v'); // field keys
  $globvars['sq_names'] = array(' ','','','','<u>OR</u> Only Pages','Session'); // field names
  $globvars['sq_notes'] = array('','','','Gives access to all admin pages','User can only access these pages'); // field notes
  $globvars['sq_notei'] = array(); // popup image
  $globvars['sq_lookt'] = array('','','','','admin_pages'); // opt tables
  $globvars['sq_lookk'] = array('','','','','p_page'); // opt keys
  $globvars['sq_lookv'] = array('','','','','p_name'); // opt values
  $globvars['sq_lookd'] = array('','','','','v'); // eg. 'k : v' or [[field]]
  $globvars['sq_lookf'] = array(); // opt query eg. "WHERE `key` = 'x'" (or "WHERE `key` = '[[value]]'" only where $go)
  $globvars['sq_fpath'] = array(); // extra file paths
  $globvars['sq_fmake'] = array(); // arrnum[v this]-width-height-[mecpf]-[qual,85]-[del,y1/n0]
  $globvars['sq_deflt'] = array(); // default values
  $globvars['sq_funct'] = array('','','','','selpages',''); // call functions
  $globvars['sq_jcall'] = array(); // call jquery
  $globvars['sq_heads'] = array(); // break headings (where 'b')
  $globvars['sq_style'] = array(); // style override

  $globvars['sq_export'] = ''; // export heads array or '' for all
  $globvars['sq_exptot'] = ''; // export totals array or '' for none
  $globvars['sq_list'] = ''; // column order array or '' for default

  $globvars['sq_dsort'] = 'username'; // default sort (reverse _DESC)
  $globvars['sq_ajoin'] = "" ; // join filter string for list
 
  $globvars['plogo'] = $globvars['admin_logo'] ; // logo
  $globvars['ptitle'] =  'Admin Users' ; // page title
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
  $globvars['formleftc'] = 120 ; // form left column
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
  $globvars['fnosuff'] = 0 ; // 1 if no suffix on image make
  $globvars['hidesearch'] = 0 ; // 1 to hide search
  $globvars['hidemchange'] = 0 ; // 1 to hide list change top row
  $globvars['mfilter'] = '' ; // add master filter for query
  $globvars['listcols'] = array(); // array of extra columns/functions
  $globvars['expvars'] = array('dstamp' => 1, 'maxlen' => 50, 'maxtext' => 70, 'xformat' => 'xls', 'lookv' => 'v');

  head();
  ?>
    <title><?= $globvars['ptitle'] ; ?></title> 
  </head> 
  <body> 
  <?
  @include_once('mysql.inc.php');
  /* ?>
    <form><? */

  function selpages() {
    global $globvars; extract($globvars);
    // fields:  dval, i_row, fname (or thiscol), c, s, fnamev (posted), c_row, ftype, fprms 
    $string = "select * from `admin_pages` left join `admin_groups` on `admin_pages`.`g_id` = `admin_groups`.`g_id` order by `admin_groups`.`g_order`, `admin_pages`.`p_order`, `admin_pages`.`p_name`";
    $query = my_query($string);
    if($go || ($action == 'add')) { // edit form
      ?>
      <select class="chosen-select" name="<?= $fname ?>[]" id="<?= $fname ?>" multiple="multiple" onchange="fldchg++;" size="1" style="width:500px">
        <? 
        $ogroup = 0;
        while($a_row = my_assoc($query)) {
          if($a_row['g_id'] != $ogroup) {
            if($ogroup) {
              print '</optgroup>';
            }
            print '<optgroup label="' . clean_upper($a_row['g_name']) . '">' ;
            $ogroup = $a_row['g_id'] ;
          }
          ?>
          <option value="<?= optsel($a_row['p_page'],$dval); ?>"><?= clean_upper($a_row['p_name']); ?></option>
          <?
        } 
        ?>
      </select>
      <?
    }
    else { // item list
      $ogroup = $oline = 0;
      $pages = explode( ",", $dval );
      while($a_row = my_assoc($query)) {
        if(in_array( $a_row['p_page'], $pages )) {
          if($a_row['g_id'] != $ogroup) {
            if($ogroup) {
              print '<br>';
            }
            print clean_upper($a_row['g_name']) . ': ' ;
            $oline = $ogroup = $a_row['g_id'] ;
          }
          if(! $oline) {
            print ', ';
          }
          print $a_row['p_name'] ;
          $oline = '' ;
        }
      }
    }
  }

  function _simage() {
    global $globvars; extract($globvars);
    // fields: c_row, i_row, c, s, fname (or thiscol), fnamev (posted), ftype, fprms, dval
    $pthp = $pthv = '../images/' ;
    $imgp = $imgv = $i_row['image'] ;
    if((! $action) && $imgv && file_exists($imgv = $pthv . $imgv)) {
      $imgh = 50 ;
      if($imgp && file_exists($imgp = $pthp . $imgp)) {
        $poph = 200 ;
        $offx = 50 ;
        $offy = $poph / 2 ;
        $omo = "ShowContent('id_grid_{$s}',$offx,$offy); return true;";
        $omx = "HideContent('id_grid_{$s}'); return true;";
        ?>
        <a onmousemove="<?= $omo ?>" onmouseover="<?= $omo ?>" onmouseout="<?= $omx ?>" onclick="<?= $omo ?>" href="#" style="display:block;">
        <? } else { $poph = 0 ; } ?>
        <img src="<?= $imgv ?>" height="<?= $imgh ?>" alt="" border="">
        <? if($poph) { ?>
        </a><div id="<?= 'id_grid_' . $s ; ?>" style="display:none; position:absolute; border: solid 1px black; background-color: white; padding:5px; z-index:999">
        <img alt="" border="0" src="<?= $imgp ?>" height="<?= $poph ; ?>">
        </div>
        <? } 
    }
    else {
      // return true for normal display
      return true ; 
    }
  }

  /* ?>
    </form><? */
  ?>
  </body>
</html>