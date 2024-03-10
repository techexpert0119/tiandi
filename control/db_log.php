<? @include_once('head.inc.php'); ?>
<!DOCTYPE html>
<html lang="en"> 
  <head>
  <?
  $globvars['debug'] = 0 ;
  $globvars['sq_table'] = 'log'; // table name
  // a=auto inc, b=break before (bb save), c=form edit, d=text disp, e=edit, f=file (+j=more), g=clean_urln, h=hide value, 
  // k=key, l=list, m=md5 entry, n=zero last, o=opts from table, p=color picker, q=filter, r=add similar, s=select multiple (ss sort), 
  // t=now(), u=link, v=view, w=null if blank, x=noshow, y=ckeditor, z=fake, 100=length/rows, _100=maxtext, (i not used)
  $globvars['sq_keys'] = array('lkua','lv','lv','lv','lv','lv','lv','v','v','zl'); // field keys
  $globvars['sq_names'] = array(' ','','','','','','','','','Compare'); // field names
  $globvars['sq_notes'] = array(' ','<div class="button"><a class="nobr" href="#" onclick="copyid(\'actionraw\') ; return false">COPY ACTION</a></div>','','','','','','','','');
  $globvars['sq_notei'] = array(); // popup image
  $globvars['sq_lookt'] = array(); // opt tables
  $globvars['sq_lookk'] = array(); // opt keys
  $globvars['sq_lookv'] = array(); // opt values
  $globvars['sq_lookd'] = array(); // eg. 'k : v' or [[field]]
  $globvars['sq_lookl'] = array(); // ss multi link
  $globvars['sq_lookf'] = array(); // opt query eg. "WHERE `key` = 'x'" (or "WHERE `key` = '[[value]]'" only where $go)
  $globvars['sq_fpath'] = array(); // extra file paths
  $globvars['sq_fmake'] = array(); // arrnum[v this]-width-height-[mecpf]-[qual,85]-[del,y1/n0]
  $globvars['sq_deflt'] = array(); // default values
  $globvars['sq_funct'] = array('','','','','','','','actiont','','compsel'); // call functions
  $globvars['sq_jcall'] = array(); // call jquery
  $globvars['sq_heads'] = array(); // break headings (where 'b')
  $globvars['sq_style'] = array(); // style override

  $globvars['sq_export'] = ''; // export heads array or match keys eg. 'le' or '' for all
  $globvars['sq_exptot'] = ''; // export totals array or '' for none
  $globvars['sq_list'] = ''; // column order array or '' for default

  $globvars['sq_limit'] = 8000 ; // limit results
  $globvars['sq_dsort'] = 'id_DESC'; // default sort (reverse _DESC)
  $globvars['sq_ajoin'] = "" ; // join filter string for list
 
  $globvars['plogo'] = $globvars['admin_logo'] ; // logo
  $globvars['ptitle'] = 'Database Log' ; // page title
  $globvars['adminm'] = $globvars['action'] == 'compare' ? '' : 'index.php' ; // admin menu
  $globvars['public'] = '' ; // public page
  $globvars['pubtext'] = '' ; // alternate button
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
  $globvars['formleftc'] = 120 ; // form left column
  $globvars['formrghtc'] = 80 ; // form right column
  $globvars['textarows'] = 3 ; // textarea rows
  $globvars['textacols'] = 55 ; // textarea cols

  $globvars['filepath'] = '' ; // file path
  $globvars['fprefpadd'] = 0 ;// add record ref to filepath (number pad zeroes OR 0 = n/a)
  $globvars['filefilt'] = '' ; // filter filenames in selector
  $globvars['allowdel'] = 0 ; // allow delete
  $globvars['allowadd'] = 0 ; // allow add
  $globvars['allowsim'] = 0 ; // add similar (fields = r)
  $globvars['edlink'] = 'View' ; // edit link (number pad zeroes OR text, '' default Edit)
  $globvars['makefile'] = '' ; // array(arrnum, 'pageb.inc.php', 'param1,param2,etc' )
  $globvars['makeurln'] = '' ; // array(arrto , arrfrom)
  $globvars['fnosuff'] = 1 ; // 1 if no suffix on image make
  $globvars['hidesearch'] = 0 ; // 1 to hide search
  $globvars['hidefilter'] = 0 ; // 1 to hide filter
  $globvars['hidemchange'] = 1 ; // 1 to hide list change top row
  $globvars['mfilter'] = '' ; // add master filter for query
  $globvars['listcols'] = array(); // array of extra columns/functions
  $globvars['expvars'] = array('dstamp' => 1, 'maxlen' => 50, 'maxtext' => 70, 'xformat' => 'xls', 'lookv' => 'v');

  head();
  ?>
    <title><?= $globvars['ptitle'] ; ?></title> 
    <script>
    function compsel() {
      var array = [];
      var c = 0 ;
      $("input:checkbox[name=compare]:checked").each(function() {
        if(c++ < 5) {
          array.push($(this).val());
        }
        else {
          $(this).prop('checked', false);
          alert('Select maximum 5');
          return false;
        }
      });
      $('#head_9').attr('href','<?= $globvars['php_self'] . '?action=compare&go=' ?>' + array.toString());
      $('#head_9').attr('onclick','');
      $('#head_9').attr('target','compare');
    }

    function copyid(i) {
      copyclip(document.getElementById(i).innerText);
    }

    function copyclip(c) {
      navigator.clipboard
          .writeText(c)
          .then(() => alert("Copied to clipboard"))
          .catch((e) => alert(e.message));
    }
    </script>
  </head> 
  <body> 
  <?
  @include_once('mysql.inc.php');
  /* ?>
    <form><? */

  function actiont() {
    global $globvars; extract($globvars);
    if(strpos($dval,'Array',0) === 0) {
      ?>
      <pre style="width:1000px;overflow-wrap:break-word;white-space:pre-wrap;"><? print_r($dval); ?></pre>
      <?
    }
    else {
      print actiond($dval);
      ?>
      <textarea id="actionraw" style="display:none;"><?= $dval ?></textarea>
      <?
    }
  }

  function actiond($in) {
    $in = '<span style="font-family:Verdana;">' . str_replace(
      ['  ' , '>'    , '<'    , "' where `"             , "' WHERE `"             , ' `'             , "\r\n"],
      [' '  , '&gt;' , '&lt;' , "' <br><br><br>where `" , "' <br><br><br>WHERE `" , '<br><br><br> `' , '<br>']
    ,$in) . '</span>';
    $in = preg_replace('/\'([^\']*)\'/', '\'<a onclick="copyclip(this.innerText)" style="color:#CC0000" href="#">${1}</a>\'', $in );
    return $in ;
  }
  
  function compsel() {
    global $globvars; extract($globvars);
    ?>
    <input type="checkbox" name="compare" value="<?= $i_row['id'] ?>" onchange="compsel()">
    <?
  }

  function compare() {
    global $globvars; extract($globvars);
    $n = 0 ;
    ?>
    <div class="maintop2"><h2>COMPARE CHANGES</h2></div><br>
    <?
    if($go) {
      $string = "select * from `{$globvars['sq_table']}` WHERE `id` IN ({$go}) ORDER BY `datetime` ASC";
      $query = my_query($string);
      if($n = my_rows($query)) {
        while($row = my_assoc($query)) {
          $arr[$row['id']] = $row ;
        }
        ?>
        <div style="padding-right:20px;"><table cellpadding="8" cellspacing="0" class="tabler" width="100%">
        <?
        print '<tr>';
        foreach($arr as $n => $row) {
          print '<td class="button"><a class="nobr" href="#" onclick="copyid(\'actionraw' . $n . '\') ; return false">COPY ACTION</a></td>';
        }
        print '</tr><tr>';
        print '<tr>';
        foreach($arr as $n => $row) {
          print '<td>' . cdate($row['datetime']) . '</td>';
        }
        print '</tr><tr>';
        foreach($arr as $n => $row) {
          print '<td>' . $row['user'] . '</td>';
        }
        print '</tr><tr>';
        foreach($arr as $n => $row) {
          print '<td>' . $row['type'] . '</td>';
        }
        print '</tr><tr>';
        foreach($arr as $n => $row) {
          print '<td valign="top">' . actiond($row['action']) . '<br><div style="height:1px;overflow:hidden"><img src="blank.gif" width="400"></div><br><textarea id="actionraw' . $n . '" style="display:none;">' .  $row['action'] . '</textarea></td>';
        }
        print '</tr>';
        ?>
        </table></div>
        <?
      }
    }
    if(! $n) {
      print_p('No matches found');
    }
  }

  /* ?>
    </form><? */
  ?>
  </body>
</html>