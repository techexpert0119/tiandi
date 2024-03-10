<? @include_once('head.inc.php'); ?>
<!DOCTYPE html>
<html lang="en"> 
  <head>
  <?
  cms_pages();
  $zf = ($globvars['go'] || ($globvars['action'] == 'add')) ? 'Options' : ' ';

  $seltemp = "ORDER BY `t_select`";
  $editurl = 'leg' ; 
  $editvis = 'le' ; 
  $allowdel = 1 ;
  $edittmp = 'leo'; 
  $notebutt = '';
  if($globvars['action'] == 'edit' && $globvars['go']) {
    $notebutt = '<a style="width:110px" target="pages_subp.php" href="pages_subp.php?filter=p_id|[[p_id]]">EDIT PAGES</a>';
    if(isset($globvars['lock_temp'][$globvars['go']])) {
      // pages locked to a template
      $edittmp = 'lvo';  
      $allowdel = 0 ;
      if(isset($globvars['main_root']['pages_shop']) && $globvars['main_root']['pages_shop'] && ($globvars['lock_temp'][$globvars['go']] == $globvars['main_root']['pages_shop'])) {
        $notebutt = '<a style="width:110px" target="shop_cats.php" href="shop_cats.php">SHOP CATS</a>';
      }
      elseif(isset($globvars['main_root']['pages_blog']) && $globvars['main_root']['pages_blog'] && ($globvars['lock_temp'][$globvars['go']] == $globvars['main_root']['pages_blog'])) {
        $notebutt = '<a style="width:110px" target="blog_cats.php" href="blog_cats.php">BLOG CATS</a>';
      }
      else {
        $notebutt = '';
      }
    }
    if(isset($globvars['lock_del'][$globvars['go']])) {
      // can't delete
      $allowdel = 0 ;
    }
    if(isset($globvars['lock_url'][$globvars['go']])) {
      // can't edit URL
      $editurl = 'lv' ; 
    }  
    if(isset($globvars['lock_vis'][$globvars['go']])) {
      // can't edit URL
      $editvis = 'lv' ; 
    }
    // check template
    /*
    $string = "select * from `pages_main` where `p_id` = '{$globvars['go']}' limit 1";
    $query = my_query($string);
    if(my_rows($query)) {
      $row = my_assoc($query);
    }
    */
  }
  if($globvars['action'] == 'add' || ($globvars['action'] == 'edit' && ! isset($globvars['lock_temp'][$globvars['go']]) )) {
    // hide locked templates
    $seltemp = "WHERE `t_select` " . $seltemp;
  }

  $globvars['debug'] = 0 ;
  $globvars['sq_table'] = 'pages_main'; // table name
  // a=auto inc, b=break before (bb save), c=form edit, d=text disp, e=edit, f=file (+j=more), g=clean_urln, h=hide value, 
  // k=key, l=list, m=md5 entry, n=zero last, o=opts from table, p=color picker, q=filter, r=add similar, s=select multiple (ss sort), 
  // t=now(), u=link, v=view, w=null if blank, x=noshow, y=ckeditor, z=fake, 100=length/rows, _100=maxtext, (i not used)
  if($globvars['go'] == 1) {
    $globvars['sq_keys'] = array(
      'lkua',$editurl,'x','x','lecn',$editvis,$edittmp,
      'x','x','x','x',
      'ble','x','x','x',
      "ble_{$globvars['meta_max']['t']}","e_{$globvars['meta_max']['d']}","e_{$globvars['meta_max']['k']}",
      'x','x',
      'x','x','x','x',
      'ble','ey','e','e',
      'be','ey','e','e',
      'be','ey','e','e'
    ); // field keys
  }
  else {
    $globvars['sq_keys'] = array(
      'lkua',$editurl,'e','e','lecn',$editvis,$edittmp,
      'x','x','x','x',
      'ble','e','ef','e',
      "ble_{$globvars['meta_max']['t']}","e_{$globvars['meta_max']['d']}","e_{$globvars['meta_max']['k']}",
      'bef','ef','ble','ep','e','ef',
      'ble','ey','e','e',
      'be','ey','e','e',
      'be','ey','e','e'
    ); // field keys
  }
  $globvars['sq_names'] = array(
    ' ','URL',ALT_URL_NAME,'Redirect','Order','Visible','Template',
    '','','','',
    'Main Menu','Drop Menu','Menu Image','Image Caption',
    'Meta Title','Meta Description','Meta Keywords',
    'Main Image','Mobile Image','Banner 1','Colour','Banner 2','Banner Graphic',
    'Heading 1','Text 1','Button 1','URL 1',
    'Heading 2','Text 2','Button 2','URL 2',
    'Heading 3','Text 3','Button 3','URL 3'
  ); // field names
  $globvars['sq_notes'] = array(
    '','preview()',ALT_URL_NOTE,$notebutt,($globvars['go'] == 1)?'<a style="width:110px" target="models.php" href="models.php">HOME SLIDES</a>':'','','REQUIRED unless redirect',
    '','','','',
    'Blank doesn\'t show','','Size 240x240<br>Resizes if too large','',
    "Max {$globvars['meta_max']['t']} chars (company added)","Max {$globvars['meta_max']['d']} characters","Max {$globvars['meta_max']['k']} characters",
    '2560x800','540x220','','','','',
    '','','','',
    '','','','',
    '','','',''
  ); // field notes
  $globvars['sq_notei'] = array(); // popup image
  $globvars['sq_lookt'] = array(
    '','','','','','','templates',
    '','','','',
    '','','','',
    '','','',
    '','','','','','',
    '','','','',
    '','','','',
    '','','',''
  ); // opt tables
  $globvars['sq_lookk'] = array(
    '','','','','','','t_id',
    '','','','',
    '','','','',
    '','','',
    '','','','','','',
    '','','','',
    '','','','',
    '','','',''
  ); // opt keys
  $globvars['sq_lookv'] = array(
    '','','','','','','t_name',
    '','','','',
    '','','','',
    '','','',
    '','','','','','',
    '','','','',
    '','','','',
    '','','',''
  ); // opt values
  $globvars['sq_lookd'] = array(
    '','','','','','','v',
    '','','','',
    '','','','',
    '','','',
    '','','','','','',
    '','','','',
    '','','','',
    '','','',''
  ); // eg. 'k : v' or [[field]]
  $globvars['sq_lookl'] = array(); // ss multi link
  $globvars['sq_lookf'] = array(
    '','','','','','',$seltemp,
    '','','','',
    '','','','',
    '','','',
    '','','','','','',
    '','','','',
    '','','','',
    '','','',''
  ); // opt query eg. "WHERE `key` = 'x'" (or "WHERE `key` = '[[value]]'" only where $go)
  $globvars['sq_joint'] = array(); // multi join tables
  $globvars['sq_joink'] = array(); // multi join keys
  $globvars['sq_joinv'] = array(); // multi join values
  $globvars['sq_joino'] = array(); // multi join order (if ss)
  $globvars['sq_fpath'] = array(
    '','','','','','','',
    '','','','',
    '','','menu','',
    '','','',
    'head/main','head/mob','','','','product/symb/page',
    '','','','',
    '','','','',
    '','','',''
  ); // extra file paths
  $globvars['sq_fmake'] = array(
    '','','','','','','',
    '','','','',
    '','','v-240-240-m-85-y','',
    '','','',
    '','','','','','',
    '','','','',
    '','','','',
    '','','',''
  ); // arrnum[v this]-width-height-[mecpf]-[qual,85]-[del,y1/n0]-[force overwrite,y1/n0]
  $globvars['sq_deflt'] = array(); // default values
  $globvars['sq_funct'] = array(
    '','url','','','','','',
    '','','','',
    '','','','',
    '','','',
    '','','','','','',
    '','','','',
    '','','','',
    '','','',''
  ); // call functions
  $globvars['sq_jcall'] = array(); // call jquery
  $globvars['sq_heads'] = array(
    '','','','','','','',
    '','','','',
    'MENU','','','',
    'META TAGS','','',
    'HEADER IMAGES','','','','','',
    'GENERAL CONTENT','',
    '','','','',
    '','','','',
    '','','',''
  ); // break headings (where 'b')
  $globvars['sq_style'] = array(); // style override

  $globvars['sq_export'] = array(
    'Ref','URL',ALT_URL_NAME,'Redirect','Order','Visble','Template',
    '','','','',
    'Main Menu','Drop Menu','Menu Image','Image Caption',
    'Meta Title','Meta Description','Meta Keywords',
    'Main Image','Mobile Image','Banner 1','Colour','Banner 2','Banner Graphic',
    'Heading 1','Text 1','Button 1','URL 1',
    'Heading 2','Text 2','Button 2','URL 2',
    'Heading 3','Text 3','Button 3','URL 3'
  ); // export heads array or match keys eg. 'le' or '' for all
  $globvars['sq_exptot'] = ''; // export totals array or '' for none
  $globvars['sq_list'] = ''; // column order array or '' for default

  $globvars['sq_dsort'] = 'p_order'; // default sort (reverse _DESC)
  $globvars['sq_ajoin'] = "" ; // join filter string for list
 
  $globvars['plogo'] = $globvars['admin_logo'] ; // logo
  $globvars['ptitle'] =  'Main Pages' ; // page title
  $globvars['adminm'] = 'index.php' ; // admin menu
  $globvars['public'] = '../' ; // public page
  $globvars['pubtext'] = '' ; // alternate button
  $globvars['publicid'] = '' ; // public page id
  $globvars['publicfld'] = 'p_url' ; // public page field or array
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
  $globvars['allowdel'] = $allowdel ; // allow delete
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
    if($go || $action) {
      return true;
    }
    else {
      ?>
      <a target="public" href="<?= $base_href . $dval ?>"><?= $dval ?></a>
      <?
    }
  }

  function preview() {
    global $globvars; extract($globvars);
    if($go) {
      ?>
      <div style="float:right"><a style="width:110px;" target="public" href="<?= $globvars['base_href'] . $i_row['p_url'] . "?preview={$globvars['sessmd']}" ?>">PREVIEW</a></div>
      <?
    }
  }

  function pageopts() {
    global $globvars; extract($globvars);
    if($globvars['go'] || ($globvars['action'] == 'add')) {
      cms_options('p');
    }
    else {
      ?>
      <span class="button">
        <a href="<?= $globvars['base_href'] . $i_row['p_url'] . "?preview={$globvars['sessmd']}" ?>" target="public">PREVIEW</a> &nbsp; 
        <? if(! isset($globvars['lock_temp'][$i_row['p_id']])) { ?>
        <a target="pages_subp.php" href="pages_subp.php?filter=p_id|<?= $i_row['p_id'] ?>">EDIT SUB PAGES</a> 
        <? } elseif(isset($globvars['main_root']['pages_shop']) && $globvars['main_root']['pages_shop'] && ($globvars['lock_temp'][$i_row['p_id']] == $globvars['main_root']['pages_shop'])) { ?>
        <a target="shop_cats.php" href="shop_cats.php">SHOP CATS</a>
        <? } elseif(isset($globvars['main_root']['pages_blog']) && $globvars['main_root']['pages_blog'] && ($globvars['lock_temp'][$i_row['p_id']] == $globvars['main_root']['pages_blog'])) { ?>
        <a target="blog_cats.php" href="blog_cats.php">BLOG CATS</a><? } ?>
      </span>
      <?
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

  function form_foot() {
    global $globvars; extract($globvars);
    if($go && $i_row['p_template'] && isset($globvars['templates'][$i_row['p_template']]) && $globvars['templates'][$i_row['p_template']]['t_cms']) {
      $string = "select 
        `pages_main`.`p_id` 
        from `pages_main` 
        where `pages_main`.`p_id` = '{$i_row['p_id']}' limit 1
      ";
      // print_p($string);
      $query = my_query($string);
      if(my_rows($query)) {
        $globvars['page'] = my_assoc($query);
        $ta = explode("\r\n", $globvars['templates'][$i_row['p_template']]['t_cms']);
        foreach($ta as $cms) {
          if($cms && function_exists($cms)) {
            $cms();
          }
        }
      }
    }
  }
  
  /* ?>
    </form><? */
  ?>
  </body>
</html>