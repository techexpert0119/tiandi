<? @include_once('head.inc.php'); ?>
<!DOCTYPE html>
<html lang="en"> 
  <head>
  <?
  pages_main(false);
  globvars('f_ids');
  $zf = ($globvars['go'] || ($globvars['action'] == 'add')) ? '' : ' ';
  if($globvars['f_ids']) {
    $f_ids = explode( "|", $globvars['f_ids'] ) ;
    $_POST['p_id'] = isset($f_ids[0]) ? $f_ids[0] : '';
    $_POST['q_id'] = isset($f_ids[1]) ? $f_ids[1] : '';
    $_POST['r_id'] = isset($f_ids[2]) ? $f_ids[2] : '';
  }

  $globvars['debug'] = 0 ;
  $globvars['sq_table'] = 'footer'; // table name
  // a=auto inc, b=break before (bb save), c=form edit, d=text disp, e=edit, f=file (+j=more), g=clean_urln, h=hide value, 
  // k=key, l=list, m=md5 entry, n=zero last, o=opts from table, p=color picker, q=filter, r=add similar, s=select multiple (ss sort), 
  // t=now(), u=link, v=view, w=null if blank, x=noshow, y=ckeditor, z=fake, 100=length/rows, _100=maxtext, (i not used)
  $globvars['sq_keys'] = array('lkua','lec','le','ex','ex','le','le','lzx'); // field keys
  $globvars['sq_names'] = array(' ','Order','PAGE','','','Or URL','Menu',$zf); // field names
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
  $globvars['sq_funct'] = array('','','f_url','f_url1','f_url2','','','listbutts'); // call functions
  $globvars['sq_jcall'] = array(); // call jquery
  $globvars['sq_heads'] = array(); // break headings (where 'b')
  $globvars['sq_style'] = array(); // style override

  $globvars['sq_export'] = array('Ref','Order','URL','URL2','URL3','External','Menu'); // export heads array or match keys eg. 'le' or '' for all
  $globvars['sq_exptot'] = ''; // export totals array or '' for none
  $globvars['sq_list'] = ''; // column order array or '' for default

  $globvars['sq_dsort'] = 'f_order'; // default sort (reverse _DESC)
  $globvars['sq_ajoin'] = "" ; // join filter string for list
 
  $globvars['plogo'] = $globvars['admin_logo'] ; // logo
  $globvars['ptitle'] =  'Footer Menu' ; // page title
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

  function listbutts() {
    global $globvars; extract($globvars);
    if(($url = footer_url($i_row)) !== false) {
      ?>
      <span class="button">
        <a href="<?= $globvars['base_href'] . $url ?>" target="public">PUBLIC</a>
      </span>
      <?
    }
  }
  
  function f_url() {
    global $globvars; extract($globvars);
    // print_arv($pages_main,'pages_main');
    // print_arv($pages_pids,'pages_pids');
    if($go || ($action == 'add')) { 
      $dchk = '' ;
      if($go) {
        $dchk = $i_row['p_id'] ;
        if($i_row['q_id']) { $dchk .= '|' . $i_row['q_id'] ; }
        if($i_row['r_id']) { $dchk .= '|' . $i_row['r_id'] ; }
      }
      ?>
      <select name="f_ids" id="id_f_ids">
        <? foreach($pages_main as $u0 => $a0) { if($a0['t_pages'] == 'pages_main' && $u0 != '404') { ?>
        <option value="<?= optsel($a0['p_id'],$dchk) ?>"><?= $u0 ?></option>
        <? if(isset($a0['subs'])) { foreach($a0['subs'] as $u1 => $a1) { ?>
          <option value="<?= optsel($a0['p_id'] . '|' . $a1['q_id'],$dchk) ?>"><?= $u0 . '/' . $u1 ?></option>
            <? if(isset($a1['subs'])) { foreach($a1['subs'] as $u2 => $a2) { ?>
              <option value="<?= optsel($a0['p_id'] . '|' . $a1['q_id'] . '|' . $a2['r_id'],$dchk) ?>"><?= $u0 . '/' . $u1 . '/' . $u2 ?></option>
        <? } } } } } } ?>
      </select>
      <?
    }
    elseif($action == 'export') {
      // print_arv($pages_pids);
      if($i_row['p_id'] && isset($pages_pids[$i_row['p_id']])) {
        print $pages_pids[$i_row['p_id']]['url'];
      }
    }
    elseif(($url = footer_url($i_row)) !== false) {
      print $url;
    }
  }

  function f_url1() {
    global $globvars; extract($globvars);
    if($action == 'export') { 
      if($i_row['q_id'] && isset($pages_pids[$i_row['p_id']][$i_row['q_id']])) {
        print $pages_pids[$i_row['p_id']][$i_row['q_id']]['url'];
      }
    }
  }
  
  function f_url2() {
    global $globvars; extract($globvars);
    if($action == 'export') { 
      if($i_row['r_id'] && isset($pages_pids[$i_row['p_id']][$i_row['q_id']][$i_row['r_id']])) {
        print $pages_pids[$i_row['p_id']][$i_row['q_id']][$i_row['r_id']]['url'];
      }
    }
  }
  
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

  /* ?>
    </form><? */
  ?>
  </body>
</html>