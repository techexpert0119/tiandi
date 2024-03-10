<? @include_once('head.inc.php'); ?>
<!DOCTYPE html>
<html lang="en"> 
  <head>
  <?
  cms_pages();
  $zf = ($globvars['go'] || ($globvars['action'] == 'add')) ? 'Options' : ' ';
  $notebutt1 = '';
  $globvars['allowdel'] = 1 ; // allow delete
  if($globvars['action'] == 'edit' && $globvars['go']) {
    $notebutt1 = '<a style="width:110px" href="pages_subp.php?action=edit&amp;go=[[q_id]]" target="pages_subp.php">EDIT SUB 1</a>';
    $string = "select * from `pages_subs` left join `pages_subp` on `pages_subs`.`q_id` = `pages_subp`.`q_id` where `pages_subs`.`r_id` = '{$globvars['go']}' limit 1";
    $query = my_query($string);
    if(my_rows($query)) {
      $row = my_assoc($query);
      if($row['p_id'] == 3) {
        // systems products
        $globvars['allowdel'] = 0 ; // no delete
      }
    }
  }

  $globvars['debug'] = 0;
  $globvars['sq_table'] = 'pages_subs'; // table name
  // a=auto inc, b=break before (bb save), c=form edit, d=text disp, e=edit, f=file (+j=more), g=clean_urln, h=hide value, 
  // k=key, l=list, m=md5 entry, n=zero last, o=opts from table, p=color picker, q=filter, r=add similar, s=select multiple (ss sort), 
  // t=now(), u=link, v=view, w=null if blank, x=noshow, y=ckeditor, z=fake, 100=length/rows, _100=maxtext, (i not used)
  $globvars['sq_keys'] = array(
    'lkua','loe','exg','e','x','lecn','le','leo',
    'ble',
    "ble_{$globvars['meta_max']['t']}","e_{$globvars['meta_max']['d']}","e_{$globvars['meta_max']['k']}",
    'bef','ef','be','ep','e','ef',
    'ble','ey','e','e',
    'be','ey','e','e',
    'be','ey','e','e'
  ); // field keys
  $globvars['sq_names'] = array(
    ' ','URL','URL',ALT_URL_NAME,'Redirect','Order','Visible','Template',
    'Menu',
    'Meta Title','Meta Description','Meta Keywords',
    'Main Image','Mobile Image','Banner 1','Colour','Banner 2','Banner Graphic',
    'Heading 1','Text 1','Button 1','URL 1',
    'Heading 2','Text 2','Button 2','URL 2',
    'Heading 3','Text 3','Button 3','URL 3'
  ); // field names
  $globvars['sq_notes'] = array(
    '',$notebutt1,'',ALT_URL_NOTE,'preview()','prodlink()','','',
    'Blank doesn\'t show',
    "Max {$globvars['meta_max']['t']} chars (company added)","Max {$globvars['meta_max']['d']} characters","Max {$globvars['meta_max']['k']} characters",
    '2560x800','540x220','','','','',
    '','','','',
    '','','','',
    '','','',''
  ); // field notes
  $globvars['sq_notei'] = array(); // popup image
  $globvars['sq_lookt'] = array(
    '','pages_subp','','','','','','templates',
    '',
    '','','',
    '','','','','','',
    '','','','',
    '','','','',
    '','','',''
    ); // opt tables
  $globvars['sq_lookk'] = array(
    '','q_id','','','','','','t_id',
    '',
    '','','',
    '','','','','','',
    '','','','',
    '','','','',
    '','','',''
  ); // opt keys
  $globvars['sq_lookv'] = array(
    '','q_url','','','','','','t_name',
    '',
    '','','',
    '','','','','','',
    '','','','',
    '','','','',
    '','','',''
  ); // opt values
  $globvars['sq_lookd'] = array(
    '','[[p_url]]/[[q_url]]','','','','','','v',
    '',
    '','','',
    '','','','','','',
    '','','','',
    '','','','',
    '','','',''
  ); // eg. 'k : v' or [[field]]
  $globvars['sq_lookl'] = array(); // ss multi link
  $globvars['sq_lookf'] = array(
    '',"left join `pages_main` on `pages_main`.`p_id` = `pages_subp`.`p_id` order by `pages_main`.`p_order`, `pages_subp`.`q_order`",'','','','','',"WHERE `t_select` ORDER BY `t_select`",
    '',
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
    '','','','','','','','',
    '',
    '','','',
    'head/main','head/mob','','','','product/symb/page',
    '','','','',
    '','','','',
    '','','',''
  ); // extra file paths
  $globvars['sq_fmake'] = array(); // arrnum[v this]-width-height-[mecpf]-[qual,85]-[del,y1/n0]-[force overwrite,y1/n0]
  $globvars['sq_deflt'] = array(); // default values
  $globvars['sq_funct'] = array(
    '','url','','','','','','',
    '',
    '','','',
    '','','','','','',
    '','','','',
    '','','','',
    '','','',''
  ); // call functions
  $globvars['sq_jcall'] = array(); // call jquery
  $globvars['sq_heads'] = array(
    '','','','','','','','',
    'MENU',
    'META TAGS','','',
    'HEADER','','','','','',
    'GENERAL CONTENT','',
    '','','','',
    '','','','',
    '','','',''
  ); // break headings (where 'b')
  $globvars['sq_style'] = array(); // style override

  $globvars['sq_export'] = array(
    'Ref','ROOT','URL',ALT_URL_NAME,'Redirect','Order','Visible','Template',
    'Menu',
    'Meta Title','Meta Description','Meta Keywords',
    'Main Image','Mobile Image','Banner 1','Colour','Banner 2','Banner Graphic',
    'Heading 1','Text 1','Button 1','URL 1',
    'Heading 2','Text 2','Button 2','URL 2',
    'Heading 3','Text 3','Button 3','URL 3'
  ); // export heads array or match keys eg. 'le' or '' for all
  $globvars['sq_exptot'] = ''; // export totals array or '' for none
  $globvars['sq_list'] = ''; // column order array or '' for default

  $globvars['sq_dsort'] = 'q_id,r_order'; // default sort (reverse _DESC)
  $globvars['sq_ajoin'] = "" ; // join filter string for list
 
  $globvars['plogo'] = $globvars['admin_logo'] ; // logo
  $globvars['ptitle'] =  'Sub Pages 2' ; // page title
  $globvars['adminm'] = 'index.php' ; // admin menu
  $globvars['public'] = '../' ; // public page
  $globvars['pubtext'] = '' ; // alternate button
  $globvars['publicid'] = '' ; // public page id
  $globvars['publicfld'] = array('p_url','q_url','r_url') ; // public page field or array
  $globvars['publicfjn'] = "left join `pages_subp` on `pages_subp`.`q_id` = `pages_subs`.`q_id` left join `pages_main` on `pages_main`.`p_id` = `pages_subp`.`p_id`" ; // join for publicfld
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

  function prodlink() {
    global $globvars; extract($globvars);
    $res = 'REQUIRED';
    if($i_row['r_template'] == 4) {
      // products template
      $res = '';
      $string = "select * from `products` where `r_id` = '{$i_row['r_id']}' and `q_id` = '{$i_row['q_id']}' limit 1";
      $query = my_query($string);
      if(my_rows($query)) {
        $row = my_assoc($query);
        $res = '<div class="buttonr"><a style="width:110px;background-color:#D71A23" href="products.php?action=edit&amp;go=' . $row['r_id'] . '" target="products.php">PRODUCT DETAILS</a></div>';
      }
      else {
        $res = '<div class="buttonr"><a style="width:110px;background-color:#D71A23" href="products.php?action=add" target="products.php">ADD DETAILS</a></div>';
      }
    }
    print $res;
  }

  function get_subcats() {
    global $globvars; extract($globvars);
    if(! isset($globvars['subcats'])) {
      $globvars['subcats'] = [];
      $string = "select `p_url`, `q_url`, `r_url`, `r_id` from `pages_subs` left join `pages_subp` on `pages_subp`.`q_id` = `pages_subs`.`q_id` left join `pages_main` on `pages_main`.`p_id` = `pages_subp`.`p_id`";
      $query = my_query($string);
      while($row = my_assoc($query)) {
        $globvars['subcatp'][$row['r_id']] = $row['p_url'];
        $globvars['subcats'][$row['r_id']] = $row['q_url'];
      }
    }
    // print_arv($globvars['subcats']);
  }
  
  function url() {
    global $globvars; extract($globvars);
    get_subcats();
    // print_arv($globvars['lookups']);
    if($go || ($action == 'add')) {
      if(isset($globvars['save'])) { $globvars['save']++; }
      ?>
      <select class="chosen-select" name="q_id" id="q_id" size="1" onchange="fldchg++;" style="width:300px;"> 
        <option value="">** Select **</option>
        <? foreach($globvars['lookups']['q_id']['sq_arr'] as $k => $a) { ?>
        <option value="<?= optsel($k,$i_row['q_id']) ?>"><?= $a['p_url'] . '/' . $a['q_url'] ?></option>
        <? } ?>
      </select> 
      <span style="height:15px;display:inline-block;vertical-align:middle">/</span>
      <input type="text" name="r_url" id="id_r_url" size="30" maxlength="30" value="<?= $i_row['r_url'] ?>" onchange="fldchg++;" autocomplete="off" style="display:inline-block;vertical-align:middle">
      <?
    }
    elseif($action == 'export') {
      return true ;
    }
    else {
      $u = $globvars['subcatp'][$i_row['r_id']] . '/' . $globvars['subcats'][$i_row['r_id']] . '/' . $i_row['r_url'];
      ?>
      <a target="public" href="<?= $globvars['base_href'] . $u ?>"><?= $u ?></a>
      <?
    }
  }
  
  function preview() {
    global $globvars; extract($globvars);
    if($i_row['r_id'] && isset($globvars['subcats'][$i_row['r_id']])) {
      ?>
      <div style="float:right"><a style="width:110px;" target="public" href="<?= $globvars['base_href'] . $globvars['subcatp'][$i_row['r_id']] . '/' . $globvars['subcats'][$i_row['r_id']] . '/' . $i_row['r_url'] . "?preview={$globvars['sessmd']}" ?>">PREVIEW</a></div>
      <?
    }
  }

  function pageopts() {
    global $globvars; extract($globvars);
    if($globvars['go'] || ($globvars['action'] == 'add')) {
      cms_options('r');
    }
    elseif($i_row['r_id'] && isset($globvars['subcats'][$i_row['r_id']])) {
      ?>
      <span class="button">
        <a href="<?= $globvars['base_href'] . $globvars['subcatp'][$i_row['r_id']] . '/' . $globvars['subcats'][$i_row['r_id']] . '/' . $i_row['r_url'] . "?preview={$globvars['sessmd']}" ?>" target="public">PREVIEW</a> &nbsp;
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
    if($go && $i_row['r_template'] && isset($globvars['templates'][$i_row['r_template']]) && $globvars['templates'][$i_row['r_template']]['t_cms']) {
      $string = "select 
        `pages_main`.`p_id`, 
        `pages_subp`.`q_id`, 
        `pages_subs`.`r_id` 
        from `pages_subs` 
        left join `pages_subp` on `pages_subp`.`q_id` = `pages_subs`.`q_id`
        left join `pages_main` on `pages_main`.`p_id` = `pages_subp`.`p_id`
        where `pages_subs`.`r_id` = '{$i_row['r_id']}' limit 1
      ";
      // print_p($string);
      $query = my_query($string);
      if(my_rows($query)) {
        $globvars['page'] = my_assoc($query);
        $ta = explode("\r\n", $globvars['templates'][$i_row['r_template']]['t_cms']);
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