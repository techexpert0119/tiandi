<? @include_once('head.inc.php'); ?>
<!DOCTYPE html>
<html lang="en"> 
  <head>
  <?
  cms_pages();
  $zf = ($globvars['go'] || ($globvars['action'] == 'add')) ? '' : ' ';
  $notebutt1 = '';
  if($globvars['action'] == 'edit' && $globvars['go']) {
    $notebutt1 = '<a style="width:110px" href="blog_main.php?filter=b_id|[[b_id]]" target="blog_main.php">EDIT ARTICLES</a>';
  }

  $globvars['debug'] = 0 ;
  $globvars['sq_table'] = 'blog_cats'; // table name
  // a=auto inc, b=break before (bb save), c=form edit, d=text disp, e=edit, f=file (+j=more), g=clean_urln, h=hide value, 
  // k=key, l=list, m=md5 entry, n=zero last, o=opts from table, p=color picker, q=filter, r=add similar, s=select multiple (ss sort), 
  // t=now(), u=link, v=view, w=null if blank, x=noshow, y=ckeditor, z=fake, 100=length/rows, _100=maxtext, (i not used)
  $globvars['sq_keys'] = array(
    'lkua','leg','lec','le','le',
    "ble_{$globvars['meta_max']['t']}","e_{$globvars['meta_max']['d']}","e_{$globvars['meta_max']['k']}",
    'be','e','eo',
    'ble','ey',
    'lzx'
  ); // field keys
  $globvars['sq_names'] = array(
    ' ','URL','Order','Visible','Menu',
    'Meta Title','Meta Description','Meta Keywords',
    'Main Image','Mobile Image','Or Video',
    'Heading','Text',
    $zf
  ); // field names
  $globvars['sq_notes'] = array(
    '',$notebutt1,'','preview()','',
    "Max {$globvars['meta_max']['t']} chars (company added)","Max {$globvars['meta_max']['d']} characters","Max {$globvars['meta_max']['k']} characters",
    '2560x800','540x220','<a href="video_map.php" target="video_map.php">VIDEOS</a>',
    '','',
    ''
  ); // field notes
  $globvars['sq_notei'] = array(); // popup image
  $globvars['sq_lookt'] = array(
    '','','','','',
    '','','',
    '','','video_map',
    '','',
    ''
  ); // opt tables
  $globvars['sq_lookk'] = array(
    '','','','','',
    '','','',
    '','','id',
    '','',
    ''
  ); // opt keys
  $globvars['sq_lookv'] = array(
    '','','','','',
    '','','',
    '','','title',
    '','',
    ''
  ); // opt values
  $globvars['sq_lookd'] = array(
    '','','','','',
    '','','',
    '','','[[id]]: [[note]]',
    '','',
    ''
  ); // eg. 'k : v' or [[field]]
  $globvars['sq_lookl'] = array(); // ss multi link
  $globvars['sq_lookf'] = array(); // opt query eg. "WHERE `key` = 'x'" (or "WHERE `key` = '[[value]]'" only where $go)
  $globvars['sq_joint'] = array(); // multi join tables
  $globvars['sq_joink'] = array(); // multi join keys
  $globvars['sq_joinv'] = array(); // multi join values
  $globvars['sq_joino'] = array(); // multi join order (if ss)
  $globvars['sq_fpath'] = array(
    '','','','','',
    '','','',
    'blog/main','blog/mob','',
    '','',
    ''
  ); // extra file paths
  $globvars['sq_fmake'] = array(
    '','','','','',
    '','','',
    '','','',
    '','',
    ''
  ); // arrnum[v this]-width-height-[mecpf]-[qual,85]-[del,y1/n0]-[force overwrite,y1/n0]
  $globvars['sq_deflt'] = array(); // default values
  $globvars['sq_funct'] = array(
    '','url','','','',
    '','','',
    'cms_imgmain','cms_imgmob','',
    '','',
    'listbutts'
  ); // call functions
  $globvars['sq_jcall'] = array(); // call jquery
  $globvars['sq_heads'] = array(
    '','','','','',
    'META TAGS','','',
    'HEADER IMAGES','','',
    'CONTENT','',
    ''
  ); // break headings (where 'b')
  $globvars['sq_style'] = array(); // style override

  $globvars['sq_export'] = array(
    'Ref','URL','Order','Visible','Menu',
    'Meta Title','Meta Description','Meta Keywords',
    'Main Image','Mobile Image','Video',
    'Heading','Text',
    ''
  ); // export heads array or match keys eg. 'le' or '' for all
  $globvars['sq_exptot'] = ''; // export totals array or '' for none
  $globvars['sq_list'] = ''; // column order array or '' for default

  $globvars['sq_dsort'] = 'b_order'; // default sort (reverse _DESC)
  $globvars['sq_ajoin'] = "" ; // join filter string for list
 
  $globvars['plogo'] = $globvars['admin_logo'] ; // logo
  $globvars['ptitle'] =  'Blog Categories' ; // page title
  $globvars['adminm'] = 'index.php' ; // admin menu
  $globvars['public'] = '../' . $globvars['main_root']['pages_blog'] ; // public page
  $globvars['pubtext'] = '' ; // alternate button
  $globvars['publicid'] = '' ; // public page id
  $globvars['publicfld'] = 'b_url' ; // public page field or array
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

  function url() {
    global $globvars; extract($globvars);
    // fields:  dval, i_row, fname (or thiscol), c, s, fnamev (posted), c_row, ftype, fprms 
    if($go || ($action == 'add')) { // edit form
      if(isset($globvars['save'])) { $globvars['save']++; }
      print $globvars['main_root']['pages_blog'] . '/';
      ?>
      <input type="text" name="b_url" id="id_b_url" size="30" maxlength="30" value="<?= $dval ?>" onchange="fldchg++;" autocomplete="off">
      <?
    }
    elseif($action == 'export') {
      print $globvars['main_root']['pages_blog'] . '/' . $dval ;
    }
    else { // item list
      ?>
      <a target="public" href="<?= $globvars['base_href'] . $globvars['main_root']['pages_blog'] . '/' . $dval ?>"><?= $globvars['main_root']['pages_blog'] . '/' . $dval ?></a>
      <?
    }
  }

  function preview() {
    global $globvars; extract($globvars);
    if($i_row['b_url']) {
      ?>
      <div style="float:right"><a style="width:110px;" target="public" href="<?= $globvars['base_href'] . $globvars['main_root']['pages_blog'] . '/' . $i_row['b_url'] . "?preview={$globvars['sessmd']}" ?>">PREVIEW</a></div>
      <?
    }
  }

  function listbutts() {
    global $globvars; extract($globvars);
    ?>
    <span class="button">
      <a href="<?= $globvars['base_href'] . $globvars['main_root']['pages_blog'] . '/' . $i_row['b_url'] . "?preview={$globvars['sessmd']}" ?>" target="public">PREVIEW</a> &nbsp; 
      <a href="blog_main.php?filter=b_id|<?= $i_row['b_id'] ?>" target="blog_main.php">EDIT ARTICLES</a> &nbsp;
    </span>
    <?
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

 /* ?>
    </form><? */
  ?>
  </body>
</html>