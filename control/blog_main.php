<? @include_once('head.inc.php'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?
  cms_pages();
  $zf = ($globvars['go'] || ($globvars['action'] == 'add')) ? '' : ' ';
  $notebutt1 = '';
  if ($globvars['action'] == 'edit' && $globvars['go']) {
    $notebutt1 = '<a style="width:110px" target="blog_cats.php" href="blog_cats.php?action=edit&amp;go=[[b_id]]">EDIT CATEGORY</a>';
  }

  $globvars['debug'] = 0;
  $globvars['sq_table'] = 'blog_main'; // table name
  // a=auto inc, b=break before (bb save), c=form edit, d=text disp, e=edit, f=file (+j=more), g=clean_urln, h=hide value,
  // k=key, l=list, m=md5 entry, n=zero last, o=opts from table, p=color picker, q=filter, r=add similar, s=select multiple (ss sort),
  // t=now(), u=link, v=view, w=null if blank, x=noshow, y=ckeditor, z=fake, 100=length/rows, _100=maxtext, (i not used)
  $globvars['sq_keys'] = array(
    'lkua', 'leo', 'exg', 'ecnx', 'le', 'ex', 'le', 'le', 'es',
    "be_{$globvars['meta_max']['t']}", "e_{$globvars['meta_max']['d']}", "e_{$globvars['meta_max']['k']}",
    'bex', 'ex', 'eox',
    'ble', 'ef', 'e_300',
    'lzx'
  ); // field keys
  $globvars['sq_names'] = array(
    ' ', 'URL', 'URL', 'Order', 'Date', 'Author', 'Visible', 'Menu', 'Tags',
    'Meta Title', 'Meta Description', 'Meta Keywords',
    'Main Image', 'Mobile Image', 'Or Video',
    'Heading', 'Grid Image', 'Intro Text',
    $zf
  ); // field names
  $globvars['sq_notes'] = array(
    '', $notebutt1, '', '', 'preview()', '', '', 'Enter to show in top menu', '',
    "Max {$globvars['meta_max']['t']} chars (company added)", "Max {$globvars['meta_max']['d']} characters", "Max {$globvars['meta_max']['k']} characters",
    '2560x800', '540x220', '<a href="video_map.php" target="video_map.php">VIDEOS</a>',
    '', '680x364', '',
    ''
  ); // field notes
  $globvars['sq_notei'] = array(); // popup image
  $globvars['sq_lookt'] = array(
    '', 'blog_cats', '', '', '', '', '', '', 'blog_tags',
    '', '', '',
    '', '', 'video_map',
    '', '', '',
    ''
  ); // opt tables
  $globvars['sq_lookk'] = array(
    '', 'b_id', '', '', '', '', '', '', 't_id',
    '', '', '',
    '', '', 'id',
    '',
    ''
  ); // opt keys
  $globvars['sq_lookv'] = array(
    '', 'b_menu', '', '', '', '', '', '', 't_name',
    '', '', '',
    '', '', 'title',
    '', '', '',
    ''
  ); // opt values
  $globvars['sq_lookd'] = array(
    '', 'v', '', '', '', '', '', '', 'v',
    '', '', '',
    '', '', '[[id]]: [[note]]',
    '', '', '',
    ''
  ); // eg. 'k : v' or [[field]]
  $globvars['sq_lookl'] = array(); // ss multi link
  $globvars['sq_lookf'] = array(); // opt query eg. "WHERE `key` = 'x'" (or "WHERE `key` = '[[value]]'" only where $go)
  $globvars['sq_joint'] = array(); // multi join tables
  $globvars['sq_joink'] = array(); // multi join keys
  $globvars['sq_joinv'] = array(); // multi join values
  $globvars['sq_joino'] = array(); // multi join order (if ss)
  $globvars['sq_fpath'] = array(
    '', '', '', '', '', '', '', '', '',
    '', '', '',
    '../main', '../mob', '',
    '', '../intro', '',
    ''
  ); // extra file paths
  $globvars['sq_fmake'] = array(
    '', '', '', '', '', '', '', '', '',
    '', '', '',
    '', '', '',
    '', '', '',
    ''
  ); // arrnum[v this]-width-height-[mecpf]-[qual,85]-[del,y1/n0]-[overwrite,y1/n0]
  $globvars['sq_deflt'] = array(); // default values
  $globvars['sq_funct'] = array(
    '', 'url', '', '', '', '', '', '', '',
    '', '', '',
    'cms_imgmain', 'cms_imgmob', '',
    '', '', '',
    'listbutts'
  ); // call functions
  $globvars['sq_jcall'] = array(); // call jquery
  $globvars['sq_heads'] = array(
    '', '', '', '', '', '', '', '', '',
    'META TAGS', '', '',
    'HEADER IMAGES', '', '',
    'CONTENT', '', '',
    ''
  ); // break headings (where 'b')
  $globvars['sq_style'] = array(); // style override

  $globvars['sq_export'] = array(
    'Ref', 'ROOT', 'URL', 'Order', 'Date', 'Author', 'Visible', 'Menu', 'Tags',
    'Meta Title', 'Meta Description', 'Meta Keywords',
    'Main Image', 'Mobile Image', 'Video',
    'Heading', 'Grid Image', 'Intro Text',
    ''
  ); // export heads array or match keys eg. 'le' or '' for all
  $globvars['sq_exptot'] = ''; // export totals array or '' for none
  $globvars['sq_list'] = ''; // column order array or '' for default

  $globvars['sq_dsort'] = 'm_date_DESC'; // default sort (reverse _DESC)
  $globvars['sq_ajoin'] = ""; // join filter string for list

  $globvars['plogo'] = $globvars['admin_logo']; // logo
  $globvars['ptitle'] =  'Blog Articles'; // page title
  $globvars['adminm'] = 'index.php'; // admin menu
  $globvars['public'] = '../' . $globvars['main_root']['pages_blog']; // public page
  $globvars['pubtext'] = ''; // alternate button
  $globvars['publicid'] = ''; // public page id
  $globvars['publicfld'] = array('b_url', 'm_url'); // public page field or array
  $globvars['publicfjn'] = "left join `blog_cats` on `blog_cats`.`b_id` = `blog_main`.`b_id`"; // join for publicfld
  $globvars['maxdisp'] = 50; // max display in list
  $globvars['maxbox'] = 50; // max edit box size
  $globvars['maxtext'] = 100; // max text length in list
  $globvars['maxbutts'] = 15; // max next links in list
  $globvars['mainwidth'] = 1300; // main width
  $globvars['listwidth'] = 1300; // list width
  $globvars['formwidth'] = 1300; // form width
  $globvars['formleftc'] = 150; // form left column
  $globvars['formrghtc'] = 200; // form right column
  $globvars['textarows'] = 5; // textarea rows
  $globvars['textacols'] = 70; // textarea cols

  $globvars['filepath'] = '../images/blog/article'; // file path
  $globvars['fprefpadd'] = 0; // add record ref to filepath (number pad zeroes OR 0 = n/a)
  $globvars['filefilt'] = ''; // filter filenames in selector
  $globvars['allowdel'] = 1; // allow delete
  $globvars['allowadd'] = 1; // allow add
  $globvars['allowsim'] = 0; // add similar (fields = r)
  $globvars['edlink'] = 'Edit'; // edit link (number pad zeroes OR text, '' default Edit)
  $globvars['makefile'] = ''; // array(arrnum, 'pageb.inc.php', 'param1,param2,etc' )
  $globvars['makeurln'] = ''; // array(arrto , arrfrom)
  $globvars['fnosuff'] = 1; // 1 if no suffix on image make
  $globvars['hidesearch'] = 0; // 1 to hide search
  $globvars['hidefilter'] = 0; // 1 to hide filter
  $globvars['hidexport'] = 0; // 1 to hide export
  $globvars['multchange'] = 0; // 1 to show list multiple change
  $globvars['listedgo'] = 0; // 1 to show edit go
  $globvars['prevnext'] = 0; // 1 to show preious/next
  $globvars['rangefilt'] = ''; // '' none or array|type (date or length)
  $globvars['mfilter'] = ''; // add master filter for query
  $globvars['listcols'] = array(); // array of extra columns/functions
  $globvars['expvars'] = array('dstamp' => 1, 'maxlen' => 50, 'maxtext' => 70, 'xformat' => 'xlsx', 'lookv' => 'v');

  $globvars['stack'] = array('head', 'html', 'image', 'link', 'vimeo', 'youtube'); // enable stack (create tables)
  $globvars['smake'] = '1000-4000-m-85'; // width-height-[mecpf]-[qual,85]

  head();
  js_file('../scripts/mouselayer.js');
  ?>
  <title><?= $globvars['ptitle']; ?></title>
</head>

<body>
  <?
  @include_once('mysql.inc.php');
  /* ?>
    <form><? */

  function get_subcats()
  {
    global $globvars;
    extract($globvars);
    if (!isset($globvars['subcats'])) {
      $globvars['subcats'] = [];
      $string = "select `b_url`, `m_id` from `blog_main` left join `blog_cats` on `blog_main`.`b_id` = `blog_cats`.`b_id`";
      $query = my_query($string);
      while ($row = my_assoc($query)) {
        $globvars['subcats'][$row['m_id']] = $row['b_url'];
      }
    }
    // print_arv($globvars['subcats']);
  }

  function url()
  {
    global $globvars;
    extract($globvars);
    get_subcats();
    // print_arv($globvars['lookups']);
    if ($go || ($action == 'add')) {
      if (isset($globvars['save'])) {
        $globvars['save']++;
      }
  ?>
      <span style="height:15px;display:inline-block;vertical-align:middle"><?= $globvars['main_root']['pages_blog'] . '/' ?></span>
      <select class="chosen-select" name="b_id" id="b_id" size="1" onchange="fldchg++;" style="width:250px;">
        <option value="">** Select **</option>
        <? foreach ($globvars['lookups']['b_id']['sq_arr'] as $k => $a) { ?>
          <option value="<?= optsel($k, $i_row['b_id']) ?>"><?= $a['b_url']  ?></option>
        <? } ?>
      </select>
      <span style="height:15px;display:inline-block;vertical-align:middle">/</span>
      <input type="text" name="m_url" id="id_m_url" size="30" maxlength="30" value="<?= $i_row['m_url'] ?>" onchange="fldchg++;" autocomplete="off" style="display:inline-block;vertical-align:middle">
    <?
    } elseif ($action == 'export') {
      print $globvars['main_root']['pages_blog'] . '/' . $globvars['subcats'][$i_row['m_id']];
    } else {
      $u = $globvars['main_root']['pages_blog'] . '/' . $globvars['subcats'][$i_row['m_id']] . '/' . $i_row['m_url'];
    ?>
      <a target="public" href="<?= $globvars['base_href'] . $u ?>"><?= $u ?></a>
    <?
    }
  }

  function preview()
  {
    global $globvars;
    extract($globvars);
    if ($i_row['m_id'] && isset($globvars['subcats'][$i_row['m_id']])) {
    ?>
      <div style="float:right"><a style="width:110px;" target="public" href="<?= $globvars['base_href'] . $globvars['main_root']['pages_blog'] . '/' . $globvars['subcats'][$i_row['m_id']] . '/' . $i_row['m_url'] . "?preview={$globvars['sessmd']}" ?>">PREVIEW</a></div>
    <?
    }
  }

  function listbutts()
  {
    global $globvars;
    extract($globvars);
    if ($i_row['m_id'] && isset($globvars['subcats'][$i_row['m_id']])) {
    ?>
      <span class="button">
        <a href="<?= $globvars['base_href'] . $globvars['main_root']['pages_blog'] . '/' . $globvars['subcats'][$i_row['m_id']] . '/' . $i_row['m_url'] . "?preview={$globvars['sessmd']}" ?>" target="public">PREVIEW</a> &nbsp;
      </span>
      <?
    }
  }

  function simage()
  {
    global $globvars;
    extract($globvars);
    // fields: c_row, i_row, c, s, fname (or thiscol), fnamev (posted), ftype, fprms, dval
    $pthp = $pthv = $fpath; // both default to file path
    $imgp = $imgv = $dval; // both default to file name
    if ((!$action) && $imgv && file_exists($imgv1 = $pthv . $imgv)) {
      $imgh = 50;
      if ($imgp && file_exists($imgp1 = $pthp . $imgp)) {
        $poph = 200;
        $offx = 50;
        $offy = $poph / 2;
        $omo = "ShowContent('id_grid_{$fname}{$s}',$offx,$offy); return true;";
        $omx = "HideContent('id_grid_{$fname}{$s}'); return true;";
      ?>
        <a onmousemove="<?= $omo ?>" onmouseover="<?= $omo ?>" onmouseout="<?= $omx ?>" onclick="<?= $omo ?>" href="#" style="display:block;">
        <? } else {
        $poph = 0;
      } ?>
        <img src="<?= clean_url($imgv1); ?>" style="<?= 'max-height:' . $imgh . 'px; max-width:' . ($imgh * 2) . 'px'; ?>" alt="" border="">
        <? if ($poph) { ?>
        </a>
        <div id="<?= 'id_grid_' . $fname . $s; ?>" style="display:none; position:absolute; border: solid 1px black; background-color: white; padding:5px; z-index:999">
          <img alt="" border="0" src="<?= clean_url($imgp1); ?>" height="<?= $poph; ?>">
        </div>
  <? }
      } else {
        // return true for normal display
        return true;
      }
    }

    /* ?>
    </form><? */
  ?>
</body>

</html>