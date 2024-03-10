<? @include_once('head.inc.php'); ?>
<!DOCTYPE html>
<html lang="en"> 
  <head>
  <?
  $zf = ($globvars['go'] || ($globvars['action'] == 'add')) ? '' : ' ';

  $globvars['debug'] = 0 ;
  $globvars['sq_table'] = 'video_map'; // table name
  // a=auto inc, b=break before (bb save), c=form edit, d=text disp, e=edit, f=file (+j=more), g=clean_urln, h=hide value, 
  // k=key, l=list, m=md5 entry, n=zero last, o=opts from table, p=color picker, q=filter, r=add similar, s=select multiple (ss sort), 
  // t=now(), u=link, v=view, w=null if blank, x=noshow, y=ckeditor, z=fake, 100=length/rows, _100=maxtext, (i not used)
  $globvars['sq_keys'] = array(
    'lkuae','le',
    'blef','lef',
    'ble','le',
    'ble','le',
    'be','e','le','e','e',
    'be','e','lzx'
  ); // field keys
  $globvars['sq_names'] = array(
    'Ref','Note',
    'Video','Image',
    'URL','Image',
    'Proportion','Sitemap',
    'Title','Description','Page URLs','Duration','Date',
    'Uploaded By','Uploader URL',$zf
  ); // field names
  $globvars['sq_notes'] = array(
    '','For reference only',
    '','',
    '<input style="width:100px;font-family:Arial;font-size:11px;padding:4px 2px 5px 2px" type="text" name="vidref" id="vidref" placeholder="ENTER REF"> &nbsp; <a style="width:60px" href="#" onclick="get_vid(\'youtube\');return false;">YOUTUBE</a> &nbsp; <a style="width:60px" href="#" onclick="get_vid(\'vimeo\');return false;">VIMEO</a>','',
    'Enter if NOT 56.25','No excludes from sitemap',
    '','','List of URLs to include in sitemap<br>including https://','Seconds','',
    '','Full URL including https://'
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
    'file','thm',
    '','',
    '','',
    '','','','','',
    '','',''
  ); // extra file paths
  $globvars['sq_fmake'] = array(); // arrnum[v this]-width-height-[mecpf]-[qual,85]-[del,y1/n0]-[force overwrite,y1/n0]
  $globvars['sq_deflt'] = array(); // default values
  $globvars['sq_funct'] = array(
    '','',
    '','simage',
    'vid_url','vid_thm',
    '','',
    '','','','','',
    '','','listbutts'
  ); // call functions
  $globvars['sq_jcall'] = array(); // call jquery
  $globvars['sq_heads'] = array(
    '','',
    'On Server','',
    '<u>OR</u> External','',
    'Options','',
    'Required for sitemap','','','','',
    'Optional for sitemap',''
  ); // break headings (where 'b')
  $globvars['sq_style'] = array(); // style override

  $globvars['sq_export'] = ''; // export heads array or match keys eg. 'le' or '' for all
  $globvars['sq_exptot'] = ''; // export totals array or '' for none
  $globvars['sq_list'] = ''; // column order array or '' for default

  $globvars['sq_dsort'] = 'note'; // default sort (reverse _DESC)
  $globvars['sq_ajoin'] = "" ; // join filter string for list
 
  $globvars['plogo'] = $globvars['admin_logo'] ; // logo
  $globvars['ptitle'] =  'Videos' ; // page title
  $globvars['adminm'] = 'index.php' ; // admin menu
  $globvars['public'] = 'video_map.php?generate' ; // public page
  $globvars['pubtext'] = 'GENERATE' ; // alternate button
  $globvars['publicid'] = '' ; // public page id
  $globvars['publicfld'] = '' ; // public page field or array
  $globvars['publicfjn'] = '' ; // join for publicfld
  $globvars['maxdisp'] = 50 ; // max display in list
  $globvars['maxbox'] = 80 ; // max edit box size
  $globvars['maxtext'] = 100 ; // max text length in list
  $globvars['maxbutts'] = 15 ; // max next links in list
  $globvars['mainwidth'] = 1300 ; // main width
  $globvars['listwidth'] = 1300 ; // list width
  $globvars['formwidth'] = 1300 ; // form width
  $globvars['formleftc'] = 150 ; // form left column
  $globvars['formrghtc'] = 270 ; // form right column
  $globvars['textarows'] = 3 ; // textarea rows
  $globvars['textacols'] = 55 ; // textarea cols

  $globvars['filepath'] = '../videos' ; // file path
  $globvars['fprefpadd'] = 0 ;// add record ref to filepath (number pad zeroes OR 0 = n/a)
  $globvars['filefilt'] = '' ; // filter filenames in selector
  $globvars['allowdel'] = 1 ; // allow delete
  $globvars['allowadd'] = 1 ; // allow add
  $globvars['allowsim'] = 0 ; // add similar (fields = r)
  $globvars['edlink'] = 1 ; // edit link (number pad zeroes OR text, '' default Edit)
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
    <script>
    function get_vid(t) {
      $('#id_exturl').val('');
      $('#id_exthm').val('');
      if(t == 'youtube') {
        // $('#id_exturl').val('https://www.youtube.com/watch?v=' + $('#vidref').val());
        $('#id_exturl').val('https://www.youtube.com/embed/' + $('#vidref').val().trim());
        $('#id_exthm').val('https://img.youtube.com/vi/' + $('#vidref').val().trim() + '/mqdefault.jpg');
      }
      else {
        $('#id_exturl').val('https://player.vimeo.com/video/' + $('#vidref').val().trim());
        $.ajax({
          type: 'GET',
          url: 'https://vimeo.com/api/v2/video/' +  $('#vidref').val().trim() + '.json',
          jsonp: 'callback',
          dataType: 'json',
          success: function(data) {
            $('#id_exthm').val(data[0].thumbnail_large)
            console.log(data[0]);
          },
          error: function() {
            alert('Error ref incorrect?');
          }
        });
      }
    }
    </script>
  </head> 
  <body> 
  <?
  if($globvars['query_string'] == 'generate') {
    $vidmap = [];
    $string = "select * from `video_map` where `include` = 'yes' order by `video_map`.`exturl`";
    $query = my_query($string);
    while($row = my_assoc($query)) {
      if($row['urls'] && count($urls = explode("\r\n", $row['urls']))) {
        foreach($urls as $url) {
          if($url) {
            $vid['player_loc'] = $row['vidfile'] ? $globvars['live_href'] . 'videos/file/' . $row['vidfile'] : $row['exturl'];
            $vid['exthm_loc'] = $row['vidthm'] ? $globvars['live_href'] . 'videos/thm/' . $row['vidthm'] : $row['exthm'];
            $vid['title'] = $row['title'];
            $vid['description'] = $row['description'];
            $vid['duration'] = $row['duration'];
            $vid['publication_date'] = cdate($row['date'],'Y-m-d');
            $vid['upname'] = $row['upname'];
            $vid['upurl'] = $row['upurl'];
            $vidmap[$url][$row['id']] = $vid ;
          }
        }
      }
    }
    // print_arv($vidmap);
    
    $handle = @fopen("../videos.xml", "w");
    fwrite($handle,'<'.'?xml version="1.0" encoding="UTF-8"?'.">\r\n");
    fwrite($handle,'<'.'urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:video="http://www.google.com/schemas/sitemap-video/1.1"'.">\r\n");
    foreach($vidmap as $url => $vids) {
      fwrite($handle,"\t<url>\r\n");
      fwrite($handle,"\t\t<loc>{$url}</loc>\r\n");
      foreach($vids as $vid) {
        fwrite($handle,"\t\t<video:video>\r\n");
        fwrite($handle,"\t\t\t<video:thumbnail_loc>{$vid['exthm_loc']}</video:thumbnail_loc>\r\n");
        fwrite($handle,"\t\t\t<video:title>" . clean_amp($vid['title']) . "</video:title>\r\n");
        fwrite($handle,"\t\t\t<video:description>" . clean_amp($vid['description']) . "</video:description>\r\n");
        fwrite($handle,"\t\t\t<video:player_loc>" . clean_amp($vid['player_loc']) . "</video:player_loc>\r\n");
        if($vid['duration']) {
          fwrite($handle,"\t\t\t<video:duration>{$vid['duration']}</video:duration>\r\n");
        }
        if($vid['publication_date']) {
          fwrite($handle,"\t\t\t<video:publication_date>{$vid['publication_date']}</video:publication_date>\r\n");
        }
        fwrite($handle,"\t\t\t<video:family_friendly>yes</video:family_friendly>\r\n");
        fwrite($handle,"\t\t\t<video:requires_subscription>no</video:requires_subscription>\r\n");
        if($vid['upname'] && $vid['upurl']) {
          fwrite($handle,"\t\t\t<video:uploader info=\"{$vid['upurl']}\">" . clean_amp($vid['upname']) . "</video:uploader>\r\n");
        }
        fwrite($handle,"\t\t\t<video:live>no</video:live>\r\n");
        fwrite($handle,"\t\t</video:video>\r\n");
      }
      fwrite($handle,"\t</url>\r\n");
    }
    fwrite($handle,'<'.'/urlset'.">\r\n");

    $globvars['msg'] = 'Video Sitemap Generated - <a href="../videos.xml" target="vmap">view here</a>';
  }
  @include_once('mysql.inc.php');
  /* ?>
    <form><? */

  function listbutts() {
    global $globvars; extract($globvars);
    ?>
    <span class="button">
    <?
    if($i_row['vidfile']) {
      ?>
      <a href="<?= $globvars['live_href'] . 'videos/file/' . $i_row['vidfile'] ?>" target="video">VIDEO</a><br>
      <?
    }
    elseif($i_row['exturl']) {
      ?>
      <a href="<?= $i_row['exturl'] ?>" target="video">VIDEO</a><br>
      <?
    }
    if($i_row['vidthm']) {
      ?>
      <a href="<?= $globvars['live_href'] . 'videos/thm/' . $i_row['vidthm'] ?>" target="image">IMAGE</a>
      <?
    }
    elseif($i_row['exthm']) {
      ?>
      <a href="<?= $i_row['exthm'] ?>" target="image">IMAGE</a>
      <?
    }
    ?>
    </span>
    <?
  }

  function vid_url() {
    global $globvars; extract($globvars);
    // fields:  dval, i_row, fname (or thiscol), c, s, fnamev (posted), c_row, ftype, fprms 
    if($go || ($action == 'add')) { // edit form
      if(isset($globvars['save'])) { $globvars['save']++; }
      ?>
      <span class="button"><input style="display:inline-block; vertical-align:middle;" type="text" name="exturl" id="id_exturl" size="80" maxlength="300" value="<?= $dval ?>" onchange="fldchg++;" autocomplete="off"> <a onclick="$(this).attr('href',$('#id_exturl').val())" id="id_exturl_c" href="<?= $dval ?>" target="_blank">CHECK</a></span>
      <?
    }
    else { // item list
      return true ; // for normal display
    }
  }
  
  function vid_thm() {
    global $globvars; extract($globvars);
    // fields:  dval, i_row, fname (or thiscol), c, s, fnamev (posted), c_row, ftype, fprms 
    if($go || ($action == 'add')) { // edit form
      if(isset($globvars['save'])) { $globvars['save']++; }
      ?>
      <span class="button"><input style="display:inline-block; vertical-align:middle;" type="text" name="exthm" id="id_exthm" size="80" maxlength="300" value="<?= $dval ?>" onchange="fldchg++;" autocomplete="off"> <a onclick="$(this).attr('href',$('#id_exthm').val())" id="id_exthm_c" href="<?= $dval ?>" target="_blank">CHECK</a></span>
      <?
    }
    elseif($dval && ($action != 'export')) { // item list
      ?>
      <img src="<?= $dval?>" height="50">
      <?
    }
    else {
      return true ; // for normal display
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

  /* ?>
    </form><? */
  ?>
  </body>
</html>