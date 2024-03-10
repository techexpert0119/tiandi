<? @include_once('head.inc.php'); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<HTML> 
  <HEAD>
  <?
  $globvars['debug'] = 0 ;
  $globvars['sq_table'] = 'currencies'; // table name
  // l=list, u=link, a=auto inc, k=key, e=edit, v=view, h=hide value, x=noshow, t=now(), p=color picker, r=add similar, q=filter, z=fake
  // m=md5 entry, o=opts from table, s=select multiple (ss sort), f=file (+j=more), y=ckeditor, d=text disp, b=break before (bb save), c=form edit, (ginwz)
  $globvars['sq_keys'] = array('lkua','lv','le','lv','lv','lef','leo','lv','lv'); // field keys
  $globvars['sq_names'] = array(' ','Order','Visible','Code','Symbol','Flag','Ship Region','Updated Date','Exchange Rate'); // field names
  $globvars['sq_notes'] = array('','','','','','16x11px'); // field notes
  $globvars['sq_notei'] = array(); // popup image
  $globvars['sq_lookt'] = array('','','','','','','ship_regions'); // opt tables
  $globvars['sq_lookk'] = array('','','','','','','sr_id'); // opt keys
  $globvars['sq_lookv'] = array('','','','','','','sr_name'); // opt values
  $globvars['sq_lookd'] = array('','','','','','','v'); // eg. 'k : v' or [[field]]
  $globvars['sq_lookl'] = array(); // ss multi link
  $globvars['sq_lookf'] = array(); // opt query eg. "WHERE `key` = 'x'" (or "WHERE `key` = '[[value]]'" only where $go)
  $globvars['sq_joint'] = array(); // multi join tables
  $globvars['sq_joink'] = array(); // multi join keys
  $globvars['sq_joinv'] = array(); // multi join values
  $globvars['sq_joino'] = array(); // multi join order (if ss)
  $globvars['sq_fpath'] = array(); // extra file paths
  $globvars['sq_fmake'] = array('','','','','','v-16-11-m-85-y'); // arrnum[v this]-width-height-[mecpf]-[qual,85]-[del,y1/n0]-[overwrite,y1/n0]
  $globvars['sq_deflt'] = array(); // default values
  $globvars['sq_funct'] = array('','','','','','simage'); // call functions
  $globvars['sq_jcall'] = array(); // call jquery
  $globvars['sq_heads'] = array(); // break headings (where 'b')
  $globvars['sq_style'] = array('','','','','','vertical-align:middle'); // style override

  $globvars['sq_export'] = ''; // export heads array or match keys eg. 'le' or '' for all
  $globvars['sq_exptot'] = ''; // export totals array or '' for none
  $globvars['sq_list'] = ''; // column order array or '' for default

  $globvars['sq_dsort'] = 'order'; // default sort (reverse _DESC)
  $globvars['sq_ajoin'] = "" ; // join filter string for list
 
  $globvars['plogo'] = $globvars['admin_logo'] ; // logo
  $globvars['ptitle'] =  'Currencies' ; // page title
  $globvars['adminm'] = 'index.php' ; // admin menu
  $globvars['public'] = '../' ; // public page
  $globvars['pubtext'] = '' ; // alternate button
  $globvars['publicid'] = '' ; // public page id
  $globvars['maxdisp'] = 50 ; // max display in list
  $globvars['maxbox'] = 50 ; // max edit box size
  $globvars['maxtext'] = 100 ; // max text length in list
  $globvars['maxbutts'] = 15 ; // max next links in list
  $globvars['mainwidth'] = 1228 ; // main width
  $globvars['listwidth'] = 1228 ; // list width
  $globvars['formwidth'] = 1228 ; // form width
  $globvars['formleftc'] = 120 ; // form left column
  $globvars['formrghtc'] = 250 ; // form right column
  $globvars['textarows'] = 5 ; // textarea rows
  $globvars['textacols'] = 55 ; // textarea cols

  $globvars['filepath'] = '../images/currency' ; // file path
  $globvars['fprefpadd'] = 0 ;// add record ref to filepath (number pad zeroes OR 0 = n/a)
  $globvars['filefilt'] = '' ; // filter filenames in selector
  $globvars['allowdel'] = 0 ; // allow delete
  $globvars['allowadd'] = 0 ; // allow add
  $globvars['allowsim'] = 0 ; // add similar (fields = r)
  $globvars['edlink'] = 'Edit' ; // edit link (number pad zeroes OR text, '' default Edit)
  $globvars['makefile'] = '' ; // array(arrnum, 'pageb.inc.php', 'param1,param2,etc' )
  $globvars['makeurln'] = '' ; // array(arrto , arrfrom)
  $globvars['fnosuff'] = 1 ; // 1 if no suffix on image make
  $globvars['hidesearch'] = 0 ; // 1 to hide search
  $globvars['hidefilter'] = 0 ; // 1 to hide filter
  $globvars['hidexport'] = 1 ; // 1 to hide filter
  $globvars['multchange'] = 0 ; // 1 to show list multiple change
  $globvars['listedgo'] = 0 ; // 1 to show edit go
  $globvars['prevnext'] = 0 ; // 1 to show preious/next
  $globvars['rangefilt'] = '' ; // '' none or array|type (date or length)
  $globvars['mfilter'] = '' ; // add master filter for query
  $globvars['listcols'] = array(); // array of extra columns/functions
  $globvars['expvars'] = array('dstamp' => 1, 'maxlen' => 50, 'maxtext' => 70, 'xformat' => 'xlsx', 'lookv' => 'v');

  head();

  function simage() {
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
        <IMG SRC="<?= clean_url($imgv1); ?>" STYLE="<?= 'max-height:' . $imgh . 'px; max-width:' . ( $imgh * 2 ) . 'px' ; ?>" ALT="" BORDER="">
        <? if($poph) { ?>
        </A><DIV ID="<?= 'id_grid_' . $fname . $s ; ?>" STYLE="display:none; position:absolute; border: solid 1px black; background-color: white; padding:5px; z-index:999">
        <IMG ALT="" BORDER="0" SRC="<?= clean_url($imgp1); ?>" HEIGHT="<?= $poph ; ?>">
        </DIV>
        <? } 
    }
    else {
      // return true for normal display
      return true ; 
    }
  }

  ?>
    <TITLE><?= $globvars['ptitle'] ; ?></TITLE> 
  </HEAD> 
  <BODY> 
  <?
  @include_once('mysql.inc.php');
  /* ?>
    <FORM><? */

  /* ?>
    </FORM><? */
  ?>
  </BODY>
</HTML>