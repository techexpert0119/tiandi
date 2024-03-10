<? @include_once('head.inc.php'); ?>
<!DOCTYPE html>
<html lang="en"> 
  <head>
  <?
  $globvars['debug'] = 0 ;
  cms_pages();
  cms_shopopts();
  $zf = ($globvars['go'] || ($globvars['action'] == 'add')) ? '' : ' ';
  $ro = "left join `shop_subs` on `shop_subs`.`s_id`=`shop_items`.`s_id` left join `shop_cats` on `shop_cats`.`c_id`=`shop_subs`.`c_id`";
  if($globvars['go']) {
    //$ro .= " where `shop_items`.`i_id` != '{$globvars['go']}'";
  }

  $globvars['sq_table'] = 'shop_items'; // table name
  // a=auto inc, b=break before (bb save), c=form edit, d=text disp, e=edit, f=file (+j=more), g=clean_urln, h=hide value, 
  // k=key, l=list, m=md5 entry, n=zero last, o=opts from table, p=color picker, q=filter, r=add similar, s=select multiple (ss sort), 
  // t=now(), u=link, v=view, w=null if blank, x=noshow, y=ckeditor, z=fake, 100=length/rows, _100=maxtext, (i not used)
  $globvars['sq_keys'] = array(
    'lkua','leo','exg','lec','le','le',
    "be_{$globvars['meta_max']['t']}","e_{$globvars['meta_max']['d']}","e_{$globvars['meta_max']['k']}",
    'be','e','eo',
    'ble','ey','be','ey','be','ey','be','ey',
    'bey','eo',
    'be','eo','e','e','eoj','e',
    'be','e','e','e','e','e','e','eo',
    'be','e',
    'be','e',
    'bes','es',
    'zlx','zlx',
  ); // field keys
  $globvars['sq_names'] = array(
    ' ','URL','URL','Order','Visible','Menu',
    'Meta Title','Meta Description','Meta Keywords',
    'Main Image','Mobile Image','Or Video',
    'Heading','Text','Heading','Text','Heading','Text','Heading','Text',
    'Introduction','Intro Video',
    'Ref','Brand','SKU','MPN','Google Cat','GTIN',
    'Standard Price','Discount Amount','<u>OR</u> Discount %','Calculated Price','Price Min','Price Max','Discount options','Shiping option',
    'Stock','Expected',
    'Option 1','Option 2',
    'Related Items','Other you might like',
    'Options',$zf
  ); // field names
  $globvars['sq_notes'] = array(
    '',$globvars['notebutt1'],'','preview()','','Enter to show in top menu',
    "Max {$globvars['meta_max']['t']} chars (company added)","Max {$globvars['meta_max']['d']} characters","Max {$globvars['meta_max']['k']} characters",
    '2560x800','540x220','<a href="video_map.php" target="video_map.php">VIDEOS</a>',
    '','','','','','','','',
    '','<a href="video_map.php" target="video_map.php">VIDEOS</a>',
    'Displayed on page','','N/A if there are options below','N/A if there are options below','','N/A if there are options below',
    '','Deduct amount','Deduct %','','','','If yes the discount applies to any different option prices below','<div style="float:left"><a href="ship_options.php" target="ship_options.php">Options</a><br>If none free everywhere</div>',
    'N/A if there are options below','N/A if there are options below',
    'For options below','For options below',
    '','',
    '',''
  ); // field notes
  $globvars['sq_notei'] = array(); // popup image
  $globvars['sq_lookt'] = array(
    '','shop_subs','','','','',
    '','','',
    '','','video_map',
    '','','','','','','','',
    '','video_map',
    '','shop_brands','','','googlecats','',
    '','','','','','','','ship_options',
    '','',
    '','',
    'shop_items','shop_items',
    '',''
  ); // opt tables
  $globvars['sq_lookk'] = array(
    '','s_id','','','','',
    '','','',
    '','','id',
    '','','','','','','','',
    '','id',
    '','b_id','','','g_id','',
    '','','','','','','','so_id',
    '','',
    '','',
    'i_id','i_id',
    '',''
  ); // opt keys
  $globvars['sq_lookv'] = array(
    '','s_url','','','','',
    '','','',
    '','','title',
    '','','','','','','','',
    '','title',
    '','b_name','','','g_cat1','',
    '','','','','','','','so_name',
    '','',
    '','',
    'i_menu','i_menu',
    '',''
  ); // opt values
  $globvars['sq_lookd'] = array(
    '','[[c_url]]/[[s_url]]','','','','',
    '','','',
    '','','[[id]]: [[note]]',
    '','','','','','','','',
    '','[[id]]: [[note]]',
    '','v','','','[[g_cat1]]/[[g_cat2]]/[[g_cat3]]/[[g_cat4]]/[[g_cat5]]/[[g_cat6]]/[[g_cat7]]','',
    '','','','','','','','v',
    '','',
    '','',
    '[[c_head]]/[[s_head]]/[[i_head]]','[[c_head]]/[[s_head]]/[[i_head]]',
    '',''
  ); // eg. 'k : v' or [[field]]
  $globvars['sq_lookl'] = array(); // ss multi link
  $globvars['sq_lookf'] = array(
    '',"left join `shop_cats` on `shop_cats`.`c_id`=`shop_subs`.`c_id` ORDER BY `shop_cats`.`c_order`, `shop_subs`.`s_order`",'','','','',
    '','','',
    '','','',
    '','','','','','','','',
    '','',
    '','','','',"where `g_visible` = 'yes' order by `g_order`",'',
    '','','','','','','',"order by `so_order`",
    '','',
    '','',
    $ro,$ro,
    '',''
  ); // opt query eg. "WHERE `key` = 'x'" (or "WHERE `key` = '[[value]]'" only where $go)
  $globvars['sq_joint'] = array(); // multi join tables
  $globvars['sq_joink'] = array(); // multi join keys
  $globvars['sq_joinv'] = array(); // multi join values
  $globvars['sq_joino'] = array(); // multi join order (if ss)
  $globvars['sq_fpath'] = array(
    '','','','','','',
    '','','',
    'head/main','head/mob','',
    '','','','','','','','',
    '','',
    '','','','','','',
    '','','','','','','','',
    '','',
    '','',
    '','',
    '','',
    '',''
  ); // extra file paths
  $globvars['sq_fmake'] = array(); // arrnum[v this]-width-height-[mecpf]-[qual,85]-[del,y1/n0]-[force overwrite,y1/n0]
  $globvars['sq_deflt'] = array(); // default values
  $globvars['sq_funct'] = array(
    '','url','','','','',
    '','','',
    'cms_imgmain','cms_imgmob','',
    '','','','','','','','',
    '','',
    '','','','','','',
    '','','','hidef','hidef','hidef','','',
    '','',
    '','',
    '','',
    'itemopts','listbutts'
  ); // call functions
  $globvars['sq_jcall'] = array(); // call jquery
  $globvars['sq_heads'] = array(
    '','','','','','',
    'META TAGS','','',
    'HEADER IMAGES','','',
    'CONTENT','','','','','','','',
    'INTRODUCTION','',
    'REFERENCES','','','','','',
    'ITEM PRICE','','','','','','','',
    'STOCK','',
    'OPTIONS','',
    'OTHER','',
    '',''
  ); // break headings (where 'b')
  $globvars['sq_style'] = array(
    '','','','','','',
    '','','',
    '','','',
    'font-weight:bold;','','','','','','','',
    '','',
    '','','','','','',
    '','','','','','','','',
    '','',
    '','',
    '','',
    '',''
  ); // style override

  $globvars['sq_export'] = array(
    'Ref','ROOT','URL','Order','Visible','Menu',
    'Meta Title','Meta Description','Meta Keywords',
    'Main Image','Mobile Image','Video',
    'Heading','Text','Heading','Text','Heading','Text','Heading','Text',
    'Introduction','Intro Video',
    'Ref','Brand','SKU','MPN','Google Cat','GTIN',
    'Standard Price','Discount Amount','<u>OR</u> Discount %','Calculated Price','Price Min','Price Max','Discount options','Shipping option',
    'Stock','Expected',
    'Option 1','Option 2',
    'Related','Other',
    'Options',''
  ); // export heads array or match keys eg. 'le' or '' for all
  $globvars['sq_exptot'] = ''; // export totals array or '' for none
  $globvars['sq_list'] = ''; // column order array or '' for default

  $globvars['sq_dsort'] = 'c_order,s_order,i_order'; // default sort (reverse _DESC)
  $globvars['sq_ajoin'] = "left join `shop_subs` on `shop_subs`.`s_id`=`shop_items`.`s_id` left join `shop_cats` on `shop_cats`.`c_id`=`shop_subs`.`c_id`" ; // join filter string for list
 
  $globvars['plogo'] = $globvars['admin_logo'] ; // logo
  $globvars['ptitle'] =  'Shop Products' ; // page title
  $globvars['adminm'] = 'index.php' ; // admin menu
  $globvars['public'] = '../' . $globvars['main_root']['pages_shop'] ; // public page
  $globvars['pubtext'] = '' ; // alternate button
  $globvars['publicid'] = '' ; // public page id
  $globvars['publicfld'] = array('c_url','s_url','i_url') ; // public page field or array
  $globvars['publicfjn'] = "left join `shop_subs` on `shop_subs`.`s_id` = `shop_items`.`s_id` left join `shop_cats` on `shop_cats`.`c_id` = `shop_subs`.`c_id`" ; // join for publicfld
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
  $globvars['allowdel'] = 0 ; // allow delete
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
  js_file('../scripts/mouselayer.js');
  ?>
    <title><?= $globvars['ptitle'] ; ?></title> 
  </head> 
  <body> 
  <?
  @include_once('mysql.inc.php');
  /* ?>
    <form><? */

  function hidef() {
    global $globvars; extract($globvars);
    if($globvars['action'] != 'export') {
      ?><span id="id_<?= $fname ?>"><?= $dval ?></span><?
    }
    else {
      print $dval;
    }
  }
  
  function get_subcats() {
    global $globvars; extract($globvars);
    if(! isset($globvars['subcats'])) {
      $globvars['subcats'] = [];
      $string = "select `c_url`, `s_url`, `i_id` from `shop_items` left join `shop_subs` on `shop_items`.`s_id` = `shop_subs`.`s_id` left join `shop_cats` on `shop_subs`.`c_id` = `shop_cats`.`c_id`";
      $query = my_query($string);
      while($row = my_assoc($query)) {
        $globvars['subcats'][$row['i_id']] = $row['c_url'] . '/' . $row['s_url'];
      }
    }
    // print_arv($globvars['subcats']);
  }
  
  function url() {
    global $globvars; extract($globvars);
    get_subcats();
    if($go || ($action == 'add')) {
      if(isset($globvars['save'])) { $globvars['save']++; }
      // print_arv($globvars['lookups']);
      ?>
      <span style="height:15px;display:inline-block;vertical-align:middle"><?= $globvars['main_root']['pages_shop'] . '/' ?></span>
      <select class="chosen-select" name="s_id" id="s_id" size="1" onchange="fldchg++;" style="width:250px;"> 
        <option value="">** Select **</option>
        <? foreach($globvars['lookups']['s_id']['sq_arr'] as $k => $a) { ?>
        <option value="<?= optsel($k,$i_row['s_id']) ?>"><?= $a['c_url'] . '/' . $a['s_url']  ?></option>
        <? } ?>
      </select> 
      <span style="height:15px;display:inline-block;vertical-align:middle">/</span>
      <input type="text" name="i_url" id="id_i_url" size="40" maxlength="50" value="<?= $i_row['i_url'] ?>" onchange="fldchg++;" autocomplete="off" style="display:inline-block;vertical-align:middle">
      <?
    }
    elseif($action == 'export') {
      print $globvars['main_root']['pages_shop'] . '/' . $globvars['subcats'][$i_row['i_id']];
    }
    else {
      $u = $globvars['main_root']['pages_shop'] . '/' . $globvars['subcats'][$i_row['i_id']] . '/' . $i_row['i_url'];
      ?>
      <a target="public" href="<?= $globvars['base_href'] . $u ?>"><?= $u ?></a>
      <?
    }
  }

  function preview() {
    global $globvars; extract($globvars);
    get_subcats();
    if($i_row['s_id'] && isset($globvars['subcats'][$i_row['s_id']])) {
      ?>
      <div style="float:right"><a style="width:110px;" target="public" href="<?= $globvars['base_href'] . $globvars['main_root']['pages_shop'] . '/' . $globvars['subcats'][$i_row['i_id']] . '/' . $i_row['i_url'] . "?preview={$globvars['sessmd']}" ?>">PREVIEW</a></div>
      <?
    }
  }

  function listbutts() {
    global $globvars; extract($globvars);
    if($i_row['s_id'] && isset($globvars['subcats'][$i_row['s_id']])) {
      ?>
      <span class="button">
        <a href="<?= $globvars['base_href'] . $globvars['main_root']['pages_shop'] . '/' . $globvars['subcats'][$i_row['i_id']] . '/' . $i_row['i_url'] . "?preview={$globvars['sessmd']}" ?>" target="public">PREVIEW</a> &nbsp;
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
    if($action == 'add') {
      ?>
      <br><br><H3>OPTIONS</h3>
      <p>Save new product once before adding images and options</p>
      <?
    }
    elseif($go && isset($globvars['templates'][$globvars['shop_cms']]) && $globvars['templates'][$globvars['shop_cms']]['t_cms']) {
      $globvars['page'] = $i_row;
      $ta = explode("\r\n", $globvars['templates'][$globvars['shop_cms']]['t_cms']);
      foreach($ta as $cms) {
        if($cms && function_exists($cms)) {
          $cms();
        }
      }
    }
  }

  function itemopts() {
    global $globvars; extract($globvars);
    if($go || ($action == 'add')) {
      return true ;
    }
    elseif($action == 'export') {
      if(isset($globvars['shopopts'][$i_row['i_id']])) { foreach($globvars['shopopts'][$i_row['i_id']] as $a) {
        $option = '';
        if($a['o_option1']) { $option = $a['o_option1']; }
        if($a['o_option2']) {
          if($a['o_option1']) { $option .= ' | '; }
          $option .= $a['o_option2'];
        }
        $price = $a['o_price'] > 0 ? $a['o_price'] : $i_row['i_price'];
        $visible = ($i_row['i_visible'] == 'no' ? 'no' : $a['o_visible']);
        print $option . ' - ' . $price . ' - ' . $a['o_stock'] . ' - ' . cdate($a['o_expected'],'d/m/Y','') . ' - ' . $visible . "\r\n" ;
      } }
    }
    else {
      ?>
      <table cellpadding="0" cellspacing="0" class="tableb" width="390" style="margin:3px">
        <tr class="thb">
          <td style="padding:0 3px 0 3px">Options</td>
          <td style="width:90px;padding:0 3px 0 3px;">SKU</td>
          <td style="width:50px;padding:0 3px 0 3px;text-align:right">Price</td>
          <td style="width:20px;padding:0 3px 0 3px;text-align:right">Stk</td>
          <td style="width:65px;padding:0 3px 0 3px;text-align:right">Expected</td>
          <td style="width:2px;padding:0 3px 0 3px;">V</td>
        </tr>
        <? 
        if(isset($globvars['shopopts'][$i_row['i_id']])) { foreach($globvars['shopopts'][$i_row['i_id']] as $a) { 
            $option = '';
            if($a['o_option1']) { $option = $a['o_option1']; }
            if($a['o_option2']) {
              if($a['o_option1']) { $option .= ' | '; }
              $option .= $a['o_option2'];
            }
            $visible = ($i_row['i_visible'] == 'yes' ? ($a['o_visible'] == 'yes' ? 'y' : 'n' ) : 'n');
          ?>
        <tr>
          <td style="padding:0 3px 0 3px;<? if(! $option) { print 'background-color:#FFC0C0'; } ?>"><?= $option ?></td>
          <td style="padding:0 3px 0 3px;<? if(! $a['o_sku']) { print 'background-color:#FFC0C0'; } ?>"><?= $a['o_sku'] ?></td>
          <td style="padding:0 3px 0 3px;text-align:right;<? if($a['o_pricecalc'] <= 0) { print 'background-color:#FFC0C0'; } ?>"><?= $a['o_pricecalc'] ?></td>
          <td style="padding:0 3px 0 3px;text-align:right;<? if($a['o_stock'] <= 0) { print 'background-color:#FFC0C0'; } ?>"><?= $a['o_stock'] ?></td>
          <td style="padding:0 3px 0 3px;text-align:right;"><?= cdate($a['o_expected'],'d/m/Y','') ?></td>
          <td style="padding:0 3px 0 3px;<? if($visible == 'n') { print 'background-color:#FFC0C0'; } ?>"><?= $visible ?></td>
        </tr>
        <? } } else { ?>
        <tr>
          <td style="padding:0 3px 0 3px">N/A</td>
          <td style="padding:0 3px 0 3px;text-align:right;<? if(!$i_row['i_sku']) { print 'background-color:#FFC0C0'; } ?>"><?= $i_row['i_sku'] ?></td>
          <td style="padding:0 3px 0 3px;text-align:right;<? if($i_row['i_price'] <= 0) { print 'background-color:#FFC0C0'; } ?>"><?= $i_row['i_price'] ?></td>
          <td style="padding:0 3px 0 3px;text-align:right;<? if($i_row['i_stock'] <= 0) { print 'background-color:#FFC0C0'; } ?>"><?= $i_row['i_stock'] ?></td>
          <td style="padding:0 3px 0 3px;text-align:right;"><?= cdate($i_row['i_expected'],'d/m/Y','') ?></td>
          <td style="padding:0 3px 0 3px;<? if($i_row['i_visible'] == 'n') { print 'background-color:#FFC0C0'; } ?>"><?= $i_row['i_visible'] == 'yes' ? 'y' : 'n' ?></td>
        </tr>
        <? } ?>
      </table>
      <?
    }
  }

  /* ?>
    </form><? */
  ?>
  </body>
</html>