<? 
// Copyright Wotnot Web Works Ltd. 
// a=auto inc, b=break before (bb save), c=form edit, d=text disp, e=edit, f=file (+j=more), g=clean_urln, h=hide value, 
// k=key, l=list, m=md5 entry, n=zero last, o=opts from table, p=color picker, q=filter, r=add similar, s=select multiple (ss sort), 
// t=now(), u=link, v=view, w=null if blank, x=noshow, y=ckeditor, z=fake, 100=length/rows, _100=maxtext, (i not used)
opendb();
if(isset($_COOKIE['search'])) { unset($_COOKIE['search']); }
if(isset($_SESSION['search'])) { unset($_SESSION['search']); }
globvars('action','done','go','del','start','sort','page','filter','xfilter','search','rngfr','rngto','vars','uform','sform','bd','nd','addsim','xstart','xmax','(array) upfields');

extract($globvars);
if(! (isset($globvars['cntrl_user']) && $globvars['cntrl_user'])) { $globvars['cntrl_user'] = 'ADMIN' ; }
if(! isset($globvars['debug'])) { $globvars['debug'] = 0 ; }
$globvars['debug_arr'] = array();

if(isset($globvars['admin_pages']) && $globvars['admin_pages']) {
  $string = "select * from `admin_pages` where `p_page` = '{$globvars['php_self']}' limit 1";
  $query = my_query($string);
  if($query && ! my_rows($query)) {
    $p_name = $globvars['ptitle'];
    if(($dp = strpos($p_name, '-')) > 0) {
      $p_name = safe_trim(substr($p_name,$dp + 1));
    }
    $string = "insert into `admin_pages` set `p_page` = '{$globvars['php_self']}', `p_name` = '{$p_name}'";
    my_query($string);
  }
}

$globvars['chk_arr'] = array('sq_keys', 'sq_names', 'sq_namel', 'sq_notes', 'sq_notei', 'sq_lookt', 'sq_lookk', 'sq_lookv', 'sq_lookd', 'sq_looku', 'sq_lookl', 'sq_lookf', 'sq_joint', 'sq_joink', 'sq_joinv', 'sq_joino', 'sq_fpath', 'sq_fmake', 'sq_deflt', 'sq_funct', 'sq_jcall', 'sq_heads', 'sq_style') ;

$globvars['chk_var'] = array('ptitle' => 'Admin', 'adminm' => 'index.php', 'public' => '../', 'maxdisp' => 50, 'maxbox' => 50, 'maxtext' => 100, 'mainwidth' => 800, 'listwidth' => 800, 'formwidth' => 800, 'formleftc' => 155, 'formrghtc' => 100, 'textarows' => 5, 'textacols' =>  55, 'filepath' => '../images/', 'fprefpadd' => 0, 'filefilt' => '', 'allowdel' => 1, 'allowadd' => 1, 'allowsim' => 0, 'edlink', 'makefile' => '', 'makeurln' => '', 'fnosuff' => 0, 'dformat' => 'd/m/Y', 'mfilter' => '', 'hashmethod' => 'md5');

if(isarr($globvars['sq_keys'])) {
  $sq_str = safe_implode('',$globvars['sq_keys']);
  if(substr_count($sq_str,'p') && function_exists('color_body') && function_exists('color_picker')) { 
    color_body(); 
  }
}

if( ( ! file_exists('../scripts/chosen/chosen.js') ) || ( ! isvar($globvars['chosen_inc']) ) ) {
  $globvars['chosen_inc'] = '';
}

if(count($globvars['upfields'])) {
  foreach($globvars['upfields'] as $upfield) {
    $v = [];
    globvars('(array) pth_'.$upfield,'(array) img_'.$upfield,'(array) ord_'.$upfield,'(array) del_'.$upfield);
    if(isset($globvars['ord_'.$upfield])) {
      asort($globvars['ord_'.$upfield]);
      foreach($globvars['ord_'.$upfield] as $k => $o) {
        if(isset($globvars['del_'.$upfield][$k])) {
          del_file($globvars['pth_'.$upfield][$k],$globvars['img_'.$upfield][$k]);
        }
        elseif($o && isset($globvars['img_'.$upfield][$k]) && $globvars['img_'.$upfield][$k]) {
          $v[] = $globvars['img_'.$upfield][$k];
        }
      }
    }
    $_POST[$upfield] = safe_implode(",",$v);
  }
}

$globvars['sq_tjoin'][0] = '';
if(isset($globvars['sq_ajoin']) && $globvars['sq_ajoin']) { 
  preg_match_all('/left join `([^`]+)` on/i', $globvars['sq_ajoin'], $tjoin_arr);
  if(isset($tjoin_arr[1]) && count($tjoin_arr[1])) {
    $globvars['sq_tjoin'] = array_reverse($tjoin_arr[1]);
  }
  if($globvars['debug']) { print_arr($globvars['sq_tjoin'],'sq_tjoin'); }
}
if(! isset($globvars['msg'])) { $globvars['msg'] = ''; }

$func = '';
if($sq_table) {
  get_cols();
  $globvars['filter'] = filter_ars($filter);
  $globvars['lookups'] = get_lookups();
  $func = 'ilist';
  if($action) {
    if($action == 'compare' && function_exists('compare')) {
      $func = 'compare';
    }
    elseif($action == 'export') {
      $export_bsp   = str_replace('.php','',$php_self) . '_export.inc.php';
      if(file_exists($export_bsp)) {
        @include_once($export_bsp);
      }
      elseif(file_exists('export.inc.php')) {
        @include_once('export.inc.php');
      }      
    }
    else {
      ob_end();
      if( ($action == 'delete') && $allowdel ) {
        delete();
        $globvars['action'] = '';
      }
      else {
        if( ( $action == 'edit' ) && $done ) {
          update();
        }
        elseif( ( $action == 'add' ) && $done ) {
          add();
        }
        if( ($action == 'edit') || ( ($action == 'add') && $allowadd ) ) {
          $func = 'form';
        }
      }
    }
  }
  if( $func == 'ilist' && $done ) {
    fupdate();
  }
}
else {
  $globvars['msg'] .= 'No table specified<br>';
}
if(isvar($plogo) && file_exists($plogo) && $pimg = get_image($plogo,0,60)) {
  $pwid = $pimg['width'];
  $phgt = $pimg['height'];
  $globvars['ptitle'] = str_replace( '', '', $globvars['ptitle'] );
  $plink = isset($globvars['plink']) && $globvars['plink'] ? $globvars['plink'] : '../' ;
}
?>
  <table border="0" cellpadding="0" cellspacing="0" summary="" id="maintable"> 
    <tr valign="top"> 
      <? if(file_exists('leftmenu.inc.php')) { ?>
      <td valign="top" id="leftcol"> 
        <div id="leftdiv" align="center">
        <? if(isset($pimg) && $pimg) { ?>
          <div class="maintop">
            <a target="public" href="<?= $plink ; ?>" title="WEB SITE">
            <img src="<?= $pimg['src'] ?>" height="<?= $phgt; ?>" width="<?= $pwid; ?>" border="0" alt="<?= $globvars['comp_name'] ; ?>"> 
            </a>
          </div>
          <br>
          <? unset($pimg);
        }
        @include_once('leftmenu.inc.php');
        ?>
        <img src="blank.gif" width="170" height="1" alt="">
        </div>
      </td> 
      <td valign="top" id="midcol"><img src="blank.gif" width="10" height="1" alt=""></td>
      <? } ?>
      <td valign="top" width="<?= isvar($mainwidth) ? $mainwidth : ''; ?>" id="rightcol"> 
        <div class="maintop1">
          <table border="0" cellpadding="0" cellspacing="0" summary="" width="100%"> 
            <tr valign="top"> 
              <td width="30%"> 
                <table border="0" cellpadding="0" cellspacing="0" summary=""> 
                  <tr> 
                    <? if(isset($pimg) && $pimg) { ?>
                    <td style="padding-right:15px;"><img src="<?= $pimg['src'] ?>" height="<?= $phgt; ?>" width="<?= $pwid; ?>" border="0" alt="<?= $globvars['comp_name'] ; ?>"></td>
                    <? } ?>
                    <td class="nobr"> 
                      <h1 class="h1" style="position:relative;"><?= $globvars['ptitle'] ; ?></h1></td> 
                  </tr> 
                </table>
              </td> 
              <td width="40%" align="center" class="red" style="padding-left:10px;padding-right:10px;"><?= $globvars['msg'] ; ?></td> 
              <td width="30%" align="right">
                <? 
                $publicurl = isvar($globvars['public']) ? isvar($globvars['public']) : '' ;
                if(($action == 'edit') && $go) {
                  if(isset($globvars['publicfld']) && $globvars['publicfld'] && isset($globvars['fields'][0]) && $globvars['fields'][0]) {
                    if(! is_array($globvars['publicfld'])) {
                      $globvars['publicfld'] = safe_explode(",",$globvars['publicfld']);
                    }
                    $publicfjn = isset($globvars['publicfjn']) ? $globvars['publicfjn'] : '' ;
                    $string = "select * from `{$globvars['sq_table']}` {$publicfjn} where `{$globvars['sq_table']}`.`{$globvars['fields'][0]}` = '$go' limit 1";
                    if($debug) { print_d($string,__LINE__,__FILE__); }
                    $query = my_query($string);
                    if(my_rows($query)) {
                      $l_row = my_assoc($query);
                      if(isset($l_row[$globvars['publicfld'][0]]) && $l_row[$globvars['publicfld'][0]]) {
                        if(isvar($globvars['publicid'])) {
                          $publicurl .= "&amp;{$globvars['publicid']}={$l_row[$globvars['publicfld']]}" ;
                        }
                        else {
                          $publicurl = $globvars['public'] ;
                          foreach($globvars['publicfld'] as $pfl) {
                            if(substr($publicurl, -1 ) != '/') {
                              $publicurl .= '/';
                            }
                            $publicurl .= $l_row[$pfl];
                          }
                          $globvars['pubtarg'] = 'public';
                        }
                      }
                    }
                  }
                  elseif(isvar($globvars['publicid'])) {
                    $publicurl .= "&amp;{$globvars['publicid']}={$go}" ;
                  }
                }
                if( $publicurl || isvar($globvars['adminm']) ) { ?>
                <table border="0" cellpadding="0" cellspacing="0" summary=""> 
                  <tr> 
                  <? 
                  if($publicurl) {
                    $publictext = isvar($globvars['pubtext']) ? $globvars['pubtext'] : 'Public Page' ;
                    $publictarg = isvar($globvars['pubtarg']) ? $globvars['pubtarg'] : ( isvar($globvars['pubtext']) ? '_self' : 'public' ) ;
                    ?>
                    <td align="right" width="100" class="buttont"><a target="<?= $publictarg; ?>" href="<?= clean_url($publicurl) ; ?>"><?= $publictext ; ?></a></td>
                  <? } if(isvar($globvars['adminm'])) { ?>
                    <td align="right" width="100" class="buttont"><a href="<?= $globvars['adminm'] ; ?>"><?= isvar($globvars['admntext']) ? $globvars['admntext'] : 'Admin Home' ; ?></a></td>
                  <? } ?>
                  </tr> 
                </table>
                <? } ?>
              </td> 
            </tr> 
          </table><br> 
        </div> 
<?
if($globvars['debug']) { 
  print_d('mfilter: ' . $globvars['mfilter'],__LINE__,__FILE__);
  print_arr($globvars['filter'],'filter');
}
($func) ? $func() : '';

function get_lookups() {
  global $globvars; extract($globvars,EXTR_SKIP);
  $out = array();
  $gstrings = [];
  foreach($c_arr as $c_row) {
    $c = $c_row['col'];
    if( sq_keys($c,'os') && (in_array($globvars['action'], ['edit','add','delete','export']) || (! sq_keys($c,'j')) ) ) {
      if(strtolower(substr($c_row['sq_lookf'],0,6)) == 'select') {
        $gstring = $c_row['sq_lookf'];
      }
      else {
        $gstring = "SELECT * FROM `{$c_row['sq_lookt']}` {$c_row['sq_lookf']}";
      }
      if( ! (substr_count(strtolower($gstring),'order by') || substr_count(strtolower($gstring),' join ')) ) {
        $dsp = $c_row['sq_lookd'] ;
        $pm = 0 ;
        if(substr_count($dsp,'[[')) {
          preg_match_all("/\[\[([^\]]*)\]\]/i", $dsp, $ords);
          if(isset($ords[1])) {
            $ords = $ords[1];
            if(count($ords)) {
              $gstring .= " ORDER BY ";
              foreach($ords as $ord) {
                $gstring .= " `$ord`, ";
                $pm++;
              }
              $gstring = substr($gstring,0,-2);
            }
          }
        }
        if(! $pm) {
          if( substr_count($dsp,'v') && ( ( ! substr_count($dsp,'k') ) || ( strpos($dsp,'v') < strpos($dsp,'k') ) ) ) {
            $gstring .= " ORDER BY `{$c_row['sq_lookv']}`" ; // sort by value
          }
          else {
            $gstring .= " ORDER BY `{$c_row['sq_lookk']}`" ; // sort by key
          }
        }
      }
      if($f = array_search($gstring, $gstrings) && isset($out[$f])) {
        // get from previous matching lookup instead of doing again
        $out[$c_row['fname']] = $out[$f];
      }
      else {
        $gstrings[$c_row['fname']] = $gstring ;
        if($debug) { print_d($gstring,__LINE__,__FILE__); }
        $look = my_query($gstring);
        if($look && my_rows($look)) {
          while($opt_arr = my_array($look,MYSQL_ASSOC)) {
            $out[$c_row['fname']]['sq_keys'] = $c_row['sq_keys'] ;
            $out[$c_row['fname']]['sq_def'] = $c_row['sq_lookv'] ;
            $out[$c_row['fname']]['sq_dsp'] = $c_row['sq_lookd'] ;
            $out[$c_row['fname']]['sq_arr'][$opt_arr[$c_row['sq_lookk']]] = $opt_arr ;
          }
        }
      }
    }
    elseif($c_row['ftype'] == 'enum' && $c_row['fprms']) {
      $eopta = safe_explode(',', str_replace("'",'',$c_row['fprms'])) ;
      $eoptc = 0;
      foreach($eopta as $eopt) {
        $eopt = str_replace("'",'',$eopt);
        if($eopt) {
          $out[$c_row['fname']]['sq_arr'][$eopt] = $eopt ;
          if(substr($c_row['fprms'], 0, 3 ) == "''," && ! $eoptc) {
            // adds not option for first blank enum
            $out[$c_row['fname']]['sq_arr']['!' . $eopt] = 'Not ' . $eopt ;
            $eoptc++;
          }
        }
      }
    }
  }
  // print_arr($out,'lookups');
  return $out;
}

function ilist() {
  global $globvars; extract($globvars,EXTR_SKIP);
  if(isset($globvars['mquery']) && $globvars['mquery']) {
    $qstring = $globvars['mquery'] ;
    $fstring = '';
  }
  else {
    // returns $fstring and creates $globvars['order'] and $globvars['order_arr']
    $fstring = get_fstring();
    $astar = isset($globvars['sq_astar']) && $globvars['sq_astar'] ? $globvars['sq_astar'] : '*';
    // aes_decrypt
    if(defined('DBKEY')) {
      foreach($l_arr as $c_row) {
        if(sq_keys($c_row['col'],'i')) {
          $astar .= ", AES_DECRYPT(`{$c_row['fname']}`, '" . DBKEY . "') as `{$c_row['fname']}_decrypted`";
        }
      }
    }
    $qstring = "SELECT {$astar} FROM `$sq_table` $fstring {$globvars['order']}" ;
    if(isset($sq_limit) && $sq_limit) { $qstring .= " LIMIT {$sq_limit}"; }
  }
  if($debug) { 
    print_d('',__LINE__,__FILE__); 
    print_pre(search_strsql($qstring)); 
  }
  $globvars['list_query'] = my_query($qstring);
  $nrows = my_rows($globvars['list_query']);
  ?>
    <form method="post" action="<?= $php_self ; ?>" enctype="multipart/form-data" name="lform" id="lform" autocomplete="off"> 
      <div class="maintop2">
      
        <table border="0" cellpadding="0" cellspacing="0" width="100%" summary=""> 
          <tr valign="middle"> 
            <td valign="middle"> 
              <table border="0" cellpadding="0" cellspacing="0" summary=""> 
                <tr valign="middle"> 
                  <td class="button"><?= $nrows ; ?>
                    <?
                    $foff = $php_self ;
                    if(isvar($vars)) { $foff .= "?vars={$vars}"; }
                    if( ! ($search || $filter || $rngfr || $rngto) ) { $foff .= '?xfilter=off'; }
                    $foff = clean_link($foff);
                    if($rngfr || $rngto) {
                      ?> (range) <a href="<?= $foff ; ?>">ALL</a> <? 
                      $filter = $search = '';
                    }
                    elseif($search) {
                      ?> (search) <a href="<?= $foff ; ?>">ALL</a> <? 
                      $rngfr = $rngto = '';
                    }
                    elseif(! ( isset($globvars['hideallbutt']) && $globvars['hideallbutt'] )) {
                      if(substr_count($fstring,'WHERE')  ) {
                        print ($mfilter && substr_count($foff,'xfilter')) ? '(mfilter)' : '(filter)' ;
                        ?> <a href="<?= $foff ; ?>">ALL</a> <? 
                        $rngfr = $rngto = '';
                      }
                      else {
                        print '(all)';
                      }                      
                    }
                    else {
                      print 'RECORDS';
                    }
                    ?>
                  </td>
                    <? 
                    if(function_exists('filter_all')) { 
                      ?>
                  <td align="right" valign="middle" style="padding-left:15px;"> 
                    <table border="0" cellpadding="2" cellspacing="0" summary=""> 
                      <tr valign="middle"> 
                        <td valign="middle"><b class="h2">FILTER </b></td> 
                        <td valign="middle"><? filter_all(); ?></td> 
                      </tr> 
                    </table>
                  </td>
                      <?
                    }
                    elseif(! isvar($hidefilter)) {
                      $df = 0; 
                      foreach($c_arr as $c => $c_row) {
                        if( isset($lookups[$c_row['fname']]) && ( sq_keys($c,'lev') ) && ! ( sq_keys($c,'xh') ) ) {
                          if( ! substr_count($c_row['sq_lookf'],'ORDER BY') ) {
                            $dsp = $c_row['sq_lookd'] ;
                            if( substr_count($dsp,'v') && ( ( ! substr_count($dsp,'k') ) || ( strpos($dsp,'v') < strpos($dsp,'k') ) ) ) {
                              asort($lookups[$c_row['fname']]); // sort by value
                            }
                            else {
                              ksort($lookups[$c_row['fname']]); // sort by key
                            }
                          }
                          if(! $df) {
                            ?>
                  <td align="right" valign="middle" style="padding-left:15px;"> 
                    <table border="0" cellpadding="2" cellspacing="0" summary=""> 
                      <tr valign="middle"> 
                        <td valign="middle"><b class="h2">FILTER </b></td> 
                        <td valign="middle"> 
                          <?
                          $cls = 'chosen-select';
                          if($chosen_inc) {
                            $cls = 'chosen-select" multiple="multiple';
                          }
                          ?>
                          <select class="<?= $cls ?>" name="filter[]" id="filter" style="width:240px;" size="1" onchange="$('#rngfr').val('');$('#rngto').val('');$('#go').val('');$('#action').val('');$('#lform').submit();"> 
                            <? if(! $chosen_inc) { ?>
                            <option value="">*** ALL ***</option>
                            <? 
                            }
                            $df = 1 ;
                            if(function_exists('filt_top')) {
                              filt_top();
                            }
                          }
                          $chead = $c_row['fname'] ;
                          if(isset($globvars['cleanfname']) && $globvars['cleanfname'] && substr($chead,1,1) == '_') {
                            $chead = substr($chead,2);
                          }
                          $chead = str_replace('_',' ',$chead);
                          $ogroup =  clean_upper( isvar($c_row['sq_names']) ? $c_row['sq_names'] : $chead ) ;
                          ?>
                          <optgroup label="<?= $ogroup ?>">
                          <?
                          foreach($lookups[$c_row['fname']]['sq_arr'] as $key => $opt_arr) {
                            $opt = "{$c_row['fname']}|{$key}";
                            $showv = '';
                            if($c_row['ftype'] == 'enum' && ! is_array($opt_arr)) {
                              $showv = $opt_arr ;
                            }
                            elseif(isset($lookups[$c_row['fname']]['sq_def'])) {
                              $showv = $val = $opt_arr[$lookups[$c_row['fname']]['sq_def']] ;
                              $dsp = $c_row['sq_lookd'] ;
                              if( substr_count($dsp,'k') || substr_count($dsp,'v') || substr_count($dsp,'[[') ) {
                                $showv = $dsp ;
                                $showv = rep_var($showv,$opt_arr);
                                if($showv == $dsp) {
                                  $showv = str_replace('k',$key,$showv);
                                  $showv = str_replace('v',$val,$showv);
                                }
                              }
                            }
                            $osel = '';
                            if(isset($filter[$c_row['fname']])) {
                              if(is_array($filter[$c_row['fname']])) {
                                if(in_array( $key, $filter[$c_row['fname']] )) {
                                  $osel = '" selected="selected';
                                  $showv = "$ogroup - $showv";
                                }
                              }
                              else {
                                if($filter[$c_row['fname']] == $key) {
                                  $osel = '" selected="selected';
                                  $showv = "$ogroup - $showv";
                                }
                                elseif(isset($filter[$c_row['fname']])) {
                                  $osel = '" disabled="disabled';
                                }
                              }
                            }
                            if($showv) {
                            ?>
                            <option value="<?= $opt . $osel ; ?>"><?= cliptext(clean_amp($showv),80,'...') ; ?></option>
                            <?
                            }
                          }
                          ?>
                          </optgroup>
                          <?
                        }
                      } 
                      if($df) { 
                        if(function_exists('filt_opts')) { 
                          filt_opts();
                        }
                        ?>
                          </select></td> 
                      </tr> 
                    </table>
                  </td>
                      <?
                    }
                  }
                  if( isvar($vars) ) { 
                    ?>
                  <td>
                    <? ihide('vars',$vars) ; ?>
                  </td>
                    <?
                  } 
                  if(! isvar($hidesearch)) {
                    $oncs = "$('#search').val('');" . ( $search ? "$('#lform').submit();" : '' ) . "return false;";
                    ?>
                  <td align="right" valign="middle" style="padding-left:15px;"> 
                      <b class="h2">SEARCH</b> 
                      <input type="text" name="search" id="search" value="<?= $search ; ?>" class="corners"
                      style="width:110px;" size="12"  maxlength="200" autocomplete="off" 
                      onclick="$('#rngfr').val('');$('#rngto').val('');$('#go').val('');$('#action').val('');" 
                      onfocus="$('#rngfr').val('');$('#rngto').val('');$('#go').val('');$('#action').val('');"
                      oninput="if($('#search').val()){ $('#searchx').css('display','inline'); } else { $('#searchx').css('display','none'); }"> 
                      <input type="submit" name="go_s" id="go_s" value="GO" class="submit">
                      <span id="searchx" class="black" style="<?= $search ? '' : 'display:none;' ?>">
                      <a onclick="<?= $oncs ?>" href="#">&#10006;</a></span> 
                  </td>
                    <? 
                  }
                  if(isvar($listedgo)) {
                    ?>
                  <td align="right" valign="middle" style="padding-left:15px;"> 
                      <b class="h2">EDIT</b> 
                      <input type="text" name="go" id="go" 
                      style="width:30px;" size="12" maxlength="10" autocomplete="off"  
                      onclick="$('#rngfr').val('');$('#rngto').val('');$('#search').val('');$('#action').val('edit');$('#filter').val('');$('#filter').trigger('chosen:updated');" 
                      onfocus="$('#rngfr').val('');$('#rngto').val('');$('#search').val('');$('#action').val('edit');$('#filter').val('');$('#filter').trigger('chosen:updated');"> 
                      <input type="hidden" name="action" id="action" value="edit">
                      <input type="submit" name="go_e" id="go_e" value="GO" class="submit">
                  </td>
                    <?
                  }
                  if(isvar($rangefilt)) {
                    $rfarr = substr( $rangefilt, 0, strpos( $rangefilt, '|' ) );
                    $rftyp = substr( $rangefilt, strpos( $rangefilt, '|' ) + 1 );
                    $rflen = ($rftyp == 'date') ? 8 : $rftyp ;
                    $rfmax = ($rftyp == 'date') ? 10 : $rftyp ;
                    ?>
                  <td align="right" valign="middle" style="padding-left:15px;"> 
                    <table border="0" cellpadding="2" cellspacing="0" summary=""> 
                      <tr valign="middle"> 
                        <td valign="middle"><b class="h2">FROM </b></td> 
                        <td valign="middle">
                          <input type="text" name="rngfr" id="id_rngfr" value="<?= $rngfr ; ?>" 
                            size="<?= $rflen ; ?>" maxlength="<?= $rfmax ; ?>" autocomplete="off"
                            onclick="$('#search').val('');$('#go').val('');$('#action').val('');$('#filter').val('');$('#filter').trigger('chosen:updated');"
                            onfocus="$('#search').val('');$('#go').val('');$('#action').val('');$('#filter').val('');$('#filter').trigger('chosen:updated');">
                          <? 
                          if($rftyp == 'date') { 
                            if(file_exists('../scripts/calendar.inc.php')) {
                              ?>
                              <script type="text/javascript">
                                $(function($) {
                                  $("#id_rngfr").datepicker({
                                    firstDay: 1,
                                    dateFormat: 'dd/mm/yy'
                                  });
                                });
                              </script>
                              <?
                            }
                            else {
                              ?>
                              <img src="../scripts/jscalendar/cal.gif" id="bt_rngto" style="cursor:pointer; vertical-align:bottom;" title="Date selector" alt="Date selector" width="16" height="16" onclick="$('#search').val('');$('#go').val('');$('#action').val('');"> 
                              <script type="text/javascript">
                                Calendar.setup({
                                    inputField     :    "id_rngfr",
                                    ifFormat       :    "%d/%m/%Y",
                                    button         :    "bt_rngto",
                                    showsTime      :    false
                                });
                              </script>                            
                              <? 
                            }
                          }
                          ?>
                        </td> 
                        <td valign="middle"><b class="h2"> &nbsp; TO </b>
                        </td> 
                        <td valign="middle">
                          <input type="text" name="rngto" id="id_rngto" value="<?= $rngto ; ?>" 
                            size="<?= $rflen ; ?>" maxlength="<?= $rfmax ; ?>" autocomplete="off"
                            onclick="$('#search').val('');$('#go').val('');$('#action').val('');$('#filter').val('');$('#filter').trigger('chosen:updated');"
                            onfocus="$('#search').val('');$('#go').val('');$('#action').val('');$('#filter').val('');$('#filter').trigger('chosen:updated');">
                          <? 
                          if($rftyp == 'date') { 
                            if(file_exists('../scripts/calendar.inc.php')) {
                              ?>
                              <script type="text/javascript">
                                $(function($) {
                                  $("#id_rngto").datepicker({
                                     firstDay: 1,
                                   dateFormat: 'dd/mm/yy'
                                  });
                                });
                              </script>
                              <?
                            }
                            else {
                              ?>
                              <img src="../scripts/jscalendar/cal.gif" id="bt_rngto" style="cursor:pointer; vertical-align:bottom;" title="Date selector" alt="Date selector" width="16" height="16" onclick="$('#search').val('');$('#go').val('');$('#action').val('');"> 
                              <script type="text/javascript">
                                Calendar.setup({
                                    inputField     :    "id_rngto",
                                    ifFormat       :    "%d/%m/%Y",
                                    button         :    "bt_rngto",
                                    showsTime      :    false
                                });
                              </script>                            
                              <? 
                            }
                          }
                          ?>
                        </td> 
                        <td valign="middle">
                          <input type="submit" name="rnggo" id="rnggo" value="GO" style="width:32px;" class="submit">
                        </td> 
                    </tr> 
                  </table>
                  </td>
                    <? 
                  } 
                  ?>
              </tr> 
              </table> 
            <input type="hidden" name="xfilter" value="<?= $xfilter ?>"> 
          </td> 
          <? 
          $allowexport = ( file_exists('export.inc.php') && ! (isvar($hidexport)||isvar($hideexport)) ) ? true : false ;
          if( $allowadd || $allowexport ) { ?>
          <td align="right" valign="middle">
            <table border="0" cellpadding="0" cellspacing="0" summary=""> 
              <tr valign="middle"> 
                <? if( $allowexport ) { $d = 2000 ; if($nrows > $d) { ?>
                <td valign="middle" width="100">
                <script>
                  function exportgo() {
                    if($('#xstart').val()) {
                      window.location.href = '<?= linkvars($sort,$start,'export') . "&xstart=" ; ?>' + $('#xstart').val() + '&xmax=<?= $d ?>';
                    }
                  }
                </script>
                <select id="xstart" class="chosen-select" style="width:107px;font-size:11px;" onchange="exportgo()">
                  <option value="">EXPORT</option>
                  <? for($e=1;$e<$nrows;$e=$e+$d) { $f = $e + $d - 1 ; if($f > $nrows) { $f = $nrows; } ?>
                  <option value="<?= $e ?>"><?= "$e - $f" ?></option>
                  <? } if($start) { ?>
                  <option value="<?= $start + 1 ?>">From this page</option>
                  <? } ?>
                  <option value="csv">All CSV</option>
                </select>
                </td>
                <? } else { ?>
                <td align="right" valign="middle" width="100" class="buttont">
                  <a href="<?= linkvars($sort,$start,'export') ; ?>">Export</a>
                </td>
                <? } } if( $allowadd ) { ?>
                <td align="right" valign="middle" width="100" class="buttont"><a href="<?= linkvars($sort,$start,'add') ; ?>">Add New</a></td>
                <? } ?>
              </tr> 
            </table>
          </td>
          <? } ?>
        </tr> 
        </table> 
      </div>
    </form>
    
    <br>
    <? if($debug && isset($globvars['order_arr'])){ print_arr($globvars['order_arr'],'order_arr'); } ?>
    <form method="post" name="uform" id="uform" action="<?= $php_self ; ?>" autocomplete="off"> 
      <table border="0" cellpadding="4" cellspacing="0" width="<?= isvar($listwidth) ? $listwidth : '100%'; ?>" class="tabler" summary="" id="ilist"> 
        <tr class="thb">
<?
// header row for list
$fms = 0 ;
foreach($l_arr as $c_row) {
  $c = $c_row['col'];
  if( sq_keys($c,'l') ) {
      ?>
          <td valign="top" style="<?= $c_row['align']; ?>" class="button nobr"> 
          <?
          // sort order
          $sortf = $c_row['fname'] ;
          $arrow = '';
          if(isset($globvars['order_arr'][$c_row['fname']])) {
            $arrow = '&uarr;';
            if($globvars['order_arr'][$c_row['fname']] != 'DESC') {
              $sortf .= '_DESC' ;
              $arrow = '&darr;';
            }
          }
          $chead = $c_row['fname'] ;
          if(isset($globvars['cleanfname']) && $globvars['cleanfname'] && substr($chead,1,1) == '_') {
            $chead = substr($chead,2);
          }
          $chead = str_replace('_',' ',$chead);
          if(safe_trim($c_row['sq_namel'])) {
            $chead = $c_row['sq_namel'] ;
          }
          elseif($c_row['sq_names']) {
            $chead = $c_row['sq_names'] ;
          }
          if( sq_keys($c,'k') && ! is_numeric($globvars['edlink']) ) {
            $chead = '#';
          }
          if($chead && $chead != ' ') {
            $href = linkvars($sortf,$start) ;
            $onc = '';
            if(sq_keys($c,'z')) {
              $href = '#';
              $onc = 'return false';
            }
            ?>
            <a id="head_<?= $c ?>" style="display:inline-block;" href="<?= $href ; ?>" onclick="<?= $onc ; ?>"><?= clean_upper($chead) ?></a>
            <? 
            print $arrow; 
          }
          ?>
          </td>
     <?
  }
  if( sq_keys($c,'c') ) {
    $fms++;
  }
}
if(isset($listcols) && is_array($listcols)) { foreach($listcols as $lhead => $lfunct) { if (function_exists($lfunct)) {
  ?>
          <td valign="top" class="button nobr"><a href="#"><?= $lhead ?></a></td>
<? } } } ?>
        </tr> 
<? if($fms && isvar($multchange) ) { ?>
        <tr>
  <?
  foreach($c_arr as $c_row) {
    $c = $c_row['col'];
    if( sq_keys($c,'l') ) {
      ?>
          <td valign="top" style="<?= $c_row['align'] ; ?>">
          <?
          if(sq_keys($c,'u')) {
            echo '<b>ALL &raquo;</b>';
          }
          elseif(sq_keys($c,'c')) {
            $fname = $c_row['fname'] ;
            $ftype = get_sqlft($c_row['ftype']) ;
            $fprms = $c_row['fprms'] ;
            if( ( $ftype == 'enum' ) && $fprms ) {
              // enum
              if($fprms == "'','y'") { 
                // checkbox  
                $onc = "return(acform('{$fname}','{$maxdisp}'))";
                ?>
                <label class="checklabel"><input type="checkbox" name="<?= 'acform[' . $fname . ']' ?>" id="<?= 'af_' . $fname; ?>" onchange="<?= $onc ; ?>" autocomplete="off"><span class="checkcust"></span></label>
                <? 
              } 
              else { 
                // radio
                $t = 0 ;
                $eopta = safe_explode(',', str_replace("'",'',$fprms)) ;
                $eoptl = strlen($fprms);
                foreach($eopta as $eopt) {
                  $eoptv = $eopt ? $eopt : 'N/A';
                  $onc = "return(arform('{$fname}_{$t}','{$maxdisp}'))";
                  ?>
                  <span style="white-space:nowrap;"><label class="radiolabel"><input type="radio" name="<?= 'arform[' . $fname . ']' ?>" id="<?= 'af_' . $fname . '_' . $t; ?>" onchange="<?= $onc ; ?>" autocomplete="off"><?= $eoptv ?><span class="radiocust"></span></label></span>
                  <? 
                  if($eoptl > $globvars['maxdisp'] / 2) { print '<br>'; }
                  $t++;
                }
              }
            }
            else {
              $onc = "return(aform('{$fname}','{$maxdisp}'))";
              ?>
              <input type="text" name="<?= 'aform[' . $fname . ']'; ?>" id="<?= 'af_' . $fname; ?>" size="<?= $c_row['flen']; ?>" maxlength="<?= $c_row['mlen']; ?>" style="<?= $c_row['align'] ; ?>" onkeyup="<?= $onc ; ?>" autocomplete="off">
              <?
            }
          }
          ?>
          </td>
      <?
    }
  }
  if(isset($listcols) && is_array($listcols)) { foreach($listcols as $lhead => $lfunct) { if (function_exists($lfunct)) {
    ?>
          <td valign="top">&nbsp;</td>
  <? } } } ?>
        </tr>
  <?
}
if(my_rows($globvars['list_query'])) {
  if(is_numeric($start) && $start >= 0 && $start <= $nrows) {
    my_seek($globvars['list_query'],$start);
  }
  $n = 0 ;
  while($a_row = my_array($globvars['list_query'],'assoc')) {
    $fgo = '';
    $n++;
    // strip table. from assoc array and exclude fake fields
    foreach($l_arr as $c_row) {
      $i_row[$c_row['fname']] = ! substr_count($c_row['sq_keys'],'z') ? $a_row[$sq_table.'.'.$c_row['fname']] : ''; 
      // aes_decrypt
      if(sq_keys($c_row['col'],'i') && isset($a_row['.'.$c_row['fname'] . '_decrypted'])) {
        $i_row[$c_row['fname']] = $a_row['.'.$c_row['fname'] . '_decrypted'];
      }
    }
    ?>
        <tr style="<? function_exists('list_row_style') ? list_row_style($i_row) : '' ; ?>">
    <?
    $chkchgc = 0 ;
    foreach($l_arr as $c_row) {
      $c = $c_row['col'];
      $fname = $c_row['fname'] ;
      if(sq_keys($c,'k')) {
        $fgo = $i_row[$fname] ;
      }
      if( sq_keys($c,'l') ) {
        $sty = $c_row['align'] ;
        if( sq_keys($c,'u') && ($fgo >= 0) ) {
          $sty = 'width:40px; white-space:nowrap; ' . $sty ;
        }
        ?>
          <td valign="top" style="<?= $sty ; ?>">
        <?
        $fn = true;  
        if( ($funct = isvar($c_row['sq_funct'])) && function_exists($funct) ) {
          // globvars for sq_funct in list
          globvadd( 
            'c_row', $c_row,
            'i_row', $i_row,
            'a_row', $a_row,
            'c', $c,
            's', $n,
            'thiscol', $fname,
            'fname', $fname,
            'fnamev', isvar($globvars[$fname]),
            'ftype', $c_row['ftype'],
            'fpath', getfpath($c_row['sq_fpath']),
            'fprms', $c_row['fprms'],
            'dval', $i_row[$fname]);
          $fn = $funct();
        }      
        if( sq_keys($c,'u') && ($fgo >= 0) ) {
          ?>           
          <input class="chkchg" type="hidden" name="<?= 'uform[' . $fgo . '][chkchg]' ?>" id="<?= 'uf_chkchg_' . $n ; ?>" value="">                
          <span style="white-space:nowrap;" class="button">
          <a style="display:block;max-width:200px;" href="<?= linkvars($sort,$start,'edit',$fgo) ; ?>"><?
          if(is_numeric($edlink) && ($edlink >= 0)) {
            if($i_row[$fname]) {
              echo str_pad ( $i_row[$fname], $edlink, '0', STR_PAD_LEFT ) ;
            }
            else {
              echo '?';
            }
          }
          elseif( $edlink && ! is_numeric($edlink) ) {
            echo $edlink . ' ' ;
          }
          else {
            echo 'Edit ' ;
          }
          ?>
            </a></span>
          <?
        }
        elseif( sq_keys($c,'e') && sq_keys($c,'c') && (! (sq_keys($c,'o') || (sq_keys($c,'s')) )) && ($fgo >= 0) && $fn == true ) {
          $ftype = get_sqlft($c_row['ftype']) ;
          $fprms = $c_row['fprms'] ;
          if( ! $dval = dformat($i_row[$fname],$ftype,'vw') ) {
            $dval = $i_row[$fname] ;
          }
          if( ( $ftype == 'enum' ) && $fprms ) {
            // enum
            if($fprms == "'','y'") { // checkbox  
              $onc = "$('#uf_chkchg_{$n}').val('y');";
              ?>
              <label class="checklabel"><input type="checkbox" name="<?= 'uform[' . $fgo . '][' . $fname . ']' ?>" id="<?= 'uf_' . $fname . '_' . $n; ?>" value="<?= optchk('y',$dval) ; ?>" onclick="<?= $onc ?>"><span class="checkcust"></span></label>
              <? 
            } 
            else { 
              // radio
              $t = 0 ;
              $eopta = safe_explode(',', str_replace("'",'',$fprms)) ;
              $eoptl = strlen($fprms);
              foreach($eopta as $eopt) {
                $eoptv = $eopt ? $eopt : 'N/A';
                ?>
                <span style="white-space:nowrap;">
                <?
                if( ( ! ($dval || $t) ) || ($dval == $eopt) ) { 
                  ?><label class="radiolabel"><input type="radio" name="<?= 'uform[' . $fgo . '][' . $fname . ']' ?>" id="<?= 'uf_' . $fname . '_' . $t . '_' . $n; ?>" value="<?= $eopt ; ?>" checked="CHECKED"><?= $eoptv ?><span class="radiocust"></span></label>
                  <? 
                }
                else { 
                  ?><label class="radiolabel"><input type="radio" name="<?= 'uform[' . $fgo . '][' . $fname . ']' ?>" id="<?= 'uf_' . $fname . '_' . $t . '_' . $n; ?>" value="<?= $eopt ; ?>"><?= $eoptv ?><span class="radiocust"></span></label>
                  <? 
                }
                ?></span>
                <?
                if($eoptl > $globvars['maxdisp'] / 2) { print '<br>'; }
                $t++ ;
              }
            }
          }
          else {
            $calf = $calj = '';
            if($ftype == 'datetime') { 
              $calf = str_replace('d','%d',str_replace('m','%m',str_replace('Y','%Y',$dformat))) . " %H:%M:00";
              $calj = str_replace('d','dd',str_replace('m','mm',str_replace('Y','yy',$dformat)));
              $calt = true;
              $dval = cdate($dval,"$dformat H:i:s",'');
            }
            elseif($ftype == 'date') { 
              $calf = str_replace('d','%d',str_replace('m','%m',str_replace('Y','%Y',$dformat)));
              $calj = str_replace('d','dd',str_replace('m','mm',str_replace('Y','yy',$dformat)));
              $calt = false;
              $dval = cdate($dval,"$dformat",'');
            }
            elseif($ftype == 'time') { 
              $dval = ctime($dval,"H:i:s",'');
            }
            ?>
            <span class="nobr">
              <input type="text" name="<?= 'uform['.$fgo.']['.$fname.']' ?>" id="<?= 'uf_'.$fname.'_'.$n; ?>" size="<?= $c_row['flen']; ?>" 
              maxlength="<?= $c_row['mlen']; ?>" style="<?= $c_row['align'] ?>" value="<?= safe_trim($dval); ?>" autocomplete="off">
              <?
              if($calj && file_exists('../scripts/calendar.inc.php')) {
                ?>
                <script type="text/javascript">
                  $(function($) {
                    $("#<?= 'uf_'.$fname.'_'.$n ; ?>").<?= $calt ? 'datetimepicker' : 'datepicker' ?>({
                      firstDay: 1,
                      dateFormat: '<?= $calj ?>', 
                      timeFormat: "HH:mm:ss",
                      showSecond:false
                    });
                  });
                </script>
                <?
              }
              elseif($calf && file_exists('../scripts/jscalendar.inc.php')) { ?>
                <img src="../scripts/jscalendar/cal.gif" id="<?= 'bt_'.$fname.'_'. $n ; ?>" style="cursor:pointer; vertical-align:bottom;" title="Date selector" alt="Date selector" width="16" height="16"> 
                <script type="text/javascript">
                  Calendar.setup({
                      inputField     :    "<?= 'uf_'.$fname.'_'.$n ; ?>",
                      ifFormat       :    "<?= $calf ; ?>",
                      button         :    "<?= 'bt_'.$fname.'_'.$n ; ?>",
                      showsTime      :    "<?= $calt ; ?>"
                  });
                </script>
                <? 
              }
              ?>
            </span>
            <?
          }
        }
        else {
          $flink = '';
          if(! ((isset($globvars['hideflink']) && $globvars['hideflink']) || $c_row['sq_funct'] || (sq_keys($c,'y') && ! $maxtext)) ) {
            $flink = $globvars['php_self'] . '?filter=' . $fname . '|' . str_replace('&amp;','&',$i_row[$fname]) ;
            if($globvars['xfilter']) {
              $flink .= '&amp;xfilter=' . $globvars['xfilter'] ;
            }
            $flink = clean_link($flink);
          }
          if($fn == true) {
            if( sq_keys($c,'o') ) {
              // lookup
              if(substr_count($i_row[$fname],'|')) {
                $i_row[$fname] = substr($i_row[$fname],0,strpos($i_row[$fname],'|'));
              }
              if( sq_keys($c,'c') ) {
                $dval = $i_row[$fname] ;
                ?>
            <select class="chosen-select" name="<?= 'uform[' . $fgo . '][' . $fname . ']' ?>" id="<?= 'uf_' . $fname . '_' . $n; ?>" size="1" style="width:130px;"> 
              <option value="">** Select **</option>
                  <?
                  if(isset($lookups[$fname]['sq_arr'])) {
                    foreach($lookups[$fname]['sq_arr'] as $key => $opt_arr) {
                      $showv = $val = $opt_arr[$c_row['sq_lookv']] ;
                      $dsp = $c_row['sq_lookd'] ;
                      if( substr_count($dsp,'k') || substr_count($dsp,'v') || substr_count($dsp,'[[') ) {
                        $showv = $dsp ;
                        $showv = rep_var($showv,$opt_arr);
                        if($showv == $dsp) {
                          $showv = str_replace('k',$key,$showv);
                          $showv = str_replace('v',$val,$showv);
                        }
                      }
                      if($gdt = dformat($showv,$c_row['ftype'],'vw')) {
                        $showv = $gdt ;
                      }
                      if($showv) {
                        $chk = join_multi($c,$fgo,$dval);
                        if(in_array($key, $chk)) {  
                          ?>
              <option value="<?= $key ; ?>" selected="selected"><?= cliptext(clean_amp($showv),80) ; ?></option>
                        <? } else { ?>
              <option value="<?= $key ; ?>"><?= cliptext(clean_amp($showv),80) ; ?></option>
                        <?
                      }
                    }
                  }
                }
                ?>
            </select>
                <?
              }
              else {
                $dval = $i_row[$fname] ;
                $dval = join_multi($c,$fgo,$dval);
                $dval = (is_array($dval) && count($dval) && $dval[0]) ? $dval[0] : $i_row[$fname] ;
                $showv = $dval ? clean_amp($dval) : '' ;
                if(isset($lookups[$fname]['sq_arr'][$dval])) {
                  $opt_arr = $lookups[$fname]['sq_arr'][$dval] ;
                  $key = $opt_arr[$c_row['sq_lookk']] ;
                  $showv = $val = $opt_arr[$c_row['sq_lookv']] ;
                  $dsp = $c_row['sq_lookd'] ;
                  if( substr_count($dsp,'k') || substr_count($dsp,'v') || substr_count($dsp,'[[') ) {
                    $showv = $dsp ;
                    $showv = rep_var($showv,$opt_arr);
                    if($showv == $dsp) {
                      $showv = str_replace('k',$key,$showv);
                      $showv = str_replace('v',$val,$showv);
                    }
                  }
                }
                if($flink) {
                  ?>
            <span class="black"><a href="<?= $flink ; ?>">
                  <?
                }
                if($gdt = dformat($showv,$c_row['ftype'],'vw')) {
                  echo $gdt;
                }
                else {
                  echo cliptext(clean_amp($showv),$maxtext) ;
                }
                if($flink) {
                  ?>
            </a></span>
                  <?
                }
              }
            }
            elseif( sq_keys($c,'s') && isset($lookups[$fname]['sq_arr']) ) {
              if( sq_keys($c,'c') && substr_count( $c_row['sq_keys'],'ss') ) {
                // multiple order options
                $dval = $i_row[$fname] ;
                ?>
            <div style="max-height:90px; width:200px; overflow:auto;">
                <?
                $arr = $urls = array();
                foreach($lookups[$fname]['sq_arr'] as $key => $opt_arr) {
                  $showv = $val = $opt_arr[$c_row['sq_lookv']] ;
                  $dsp = $c_row['sq_lookd'] ;
                  if( substr_count($dsp,'k') || substr_count($dsp,'v') || substr_count($dsp,'[[') ) {
                    $showv = $dsp ;
                    $showv = rep_var($showv,$opt_arr);
                    if($showv == $dsp) {
                      $showv = str_replace('k',$key,$showv);
                      $showv = str_replace('v',$val,$showv);
                    }
                  }
                  if($showv) {
                    $arr[$key] = cliptext(clean_amp($showv),110);
                  }
                  $urls[$key] = $sq_lookl[$c] ? rep_var($sq_lookl[$c],$opt_arr) : '';
                }
                if(count($arr)) {
                  $dvals = join_multi($c,$fgo,$dval);
                  debug_arr();
                  $sn = 0 ;
                  foreach($dvals as $optsel) {
                    if(isset($arr[$optsel])) {
                      if(! $sn++) {
                        ?>
              <div style="margin-bottom:5px">
                <u>SELECTED</u> 
              </div>
                        <?
                      }
                      ?>
              <div style="margin-bottom:5px">
                <div style="display:inline-block; vertical-align:middle; margin-right:3px">
                  <input name="<?= 'uform[' . $fgo . '][' . $fname . '_ssord][]' ?>" value="<?= $sn; ?>" autocomplete="off" size="2" type="text"> <input type="hidden" name="<?= 'uform[' . $fgo . '][' . $fname . '][]' ; ?>" value="<?= $optsel; ?>"> 
                </div>
                <div style="display:inline-block; vertical-align:middle">
                            <?
                            $url = $optsel && isset($urls[$optsel]) && $urls[$optsel] ? clean_url($urls[$optsel]) : '';
                            if($url) {
                              ?>
                  <a target="popfile" href="<?= $url ?>">
                              <?
                            }
                            print $arr[$optsel];
                            if($url) {
                              ?>
                  </a>
                              <?
                            }
                            ?>
                </div>
              </div>
                      <?
                    }
                  }
                  $nn = 0 ;
                  foreach($arr as $notkey => $notval) {
                    if(! in_array( $notkey, $dvals )) {
                      if(! $nn++) {
                        ?>
              <div style="margin-bottom:5px; <?= $sn ? 'margin-top:10px;' : ''; ?>">
                <u>NOT SELECTED</u> 
              </div>
                        <?
                      }
                      ?>
              <div style="margin-bottom:5px">
                <div style="display:inline-block; vertical-align:middle; margin-right:3px">
                  <input name="<?= 'uform[' . $fgo . '][' . $fname . '_ssord][]' ?>" value="" autocomplete="off" size="2" type="text"> <input type="hidden" name="<?= 'uform[' . $fgo . '][' . $fname . '][]' ; ?>" value="<?= $notkey; ?>"> 
                </div>
                <div style="display:inline-block; vertical-align:middle">
                            <?
                            $url = $notkey && isset($urls[$notkey]) && $urls[$notkey] ? clean_url($urls[$notkey]) : '';
                            if($url) {
                              ?>
                  <a target="popfile" href="<?= $url ?>">
                              <?
                            }
                            print $notval;
                            if($url) {
                              ?>
                  </a>
                              <?
                            }
                            ?>
                </div>
              </div>
                      <?
                    }
                  }
                }
                ?>
            </div>
                <?                  
              }
              elseif( sq_keys($c,'c') && $chosen_inc) {
                // multiple chosen
                $dval = $i_row[$fname] ;
                ?>
            <select class="chosen-select" name="<?= 'uform[' . $fgo . '][' . $fname . '][]' ?>" id="<?= 'uf_' . $fname . '_' . $n; ?>" multiple="multiple" size="3" style="width:130px">
                  <?
                  if(isset($lookups[$fname]['sq_arr'])) {
                    foreach($lookups[$fname]['sq_arr'] as $key => $opt_arr) {
                      $showv = $val = $opt_arr[$c_row['sq_lookv']] ;
                      $dsp = $c_row['sq_lookd'] ;
                      if( substr_count($dsp,'k') || substr_count($dsp,'v') || substr_count($dsp,'[[') ) {
                        $showv = $dsp ;
                        $showv = rep_var($showv,$opt_arr);
                        if($showv == $dsp) {
                          $showv = str_replace('k',$key,$showv);
                          $showv = str_replace('v',$val,$showv);
                        }
                      }
                      if($showv) {
                        $chk = join_multi($c,$fgo,$dval);
                        if(in_array($key, $chk)) { 
                          ?>
              <option value="<?= $key ; ?>" selected="selected"><?= cliptext(clean_amp($showv),80) ; ?></option>
                          <? } else { ?>
              <option value="<?= $key ; ?>"><?= cliptext(clean_amp($showv),80) ; ?></option>
                          <?
                        }
                      }
                    }
                  }
                  ?>
            </select>
              <?
                debug_arr();                
              }
              else {
                // multiple display
                $showv = $i_row[$fname] ? clean_amp($i_row[$fname]) : '' ;
                if(isset($lookups[$fname]['sq_arr'])) {
                  print '<ul class="ul">';
                  $vals = join_multi($c,$fgo,$i_row[$fname]);
                  foreach($vals as $tval) {
                    if(isset($lookups[$fname]['sq_arr'][$tval])) {
                      $opt_arr = $lookups[$fname]['sq_arr'][$tval] ;
                      $key = $opt_arr[$c_row['sq_lookk']] ;
                      $showv = $val = $opt_arr[$c_row['sq_lookv']] ;
                      $dsp = $c_row['sq_lookd'] ;
                      if( substr_count($dsp,'k') || substr_count($dsp,'v') || substr_count($dsp,'[[') ) {
                        $showv = $dsp ;
                        $showv = rep_var($showv,$opt_arr);
                        if($showv == $dsp) {
                          $showv = str_replace('k',$key,$showv);
                          $showv = str_replace('v',$val,$showv);
                        }
                      }
                      $url = $php_self . '?filter=' . $fname . '|' . $opt_arr[$c_row['sq_lookk']] ;
                      $targ = '';
                      if($c_row['sq_looku']) {
                        $url =  rep_var($c_row['sq_looku'],$opt_arr);
                        $targ = '_blank';
                      }
                      print('<li class="black"><a target="' . $targ . '" href="' . $url . '">' . cliptext(clean_amp($showv),$maxtext) . "</a></li>\r\n") ;
                    }
                  }
                  print '</ul>';
                  debug_arr();
                }
                else {
                  print cliptext(clean_amp($showv),$maxtext) ;
                } 
              }
            }
            elseif( ( strpos( $i_row[$fname], 'http://' ) === 0 || strpos( $i_row[$fname], 'https://' ) === 0 ) || ( substr_count( $i_row[$fname], '@' ) && (! substr_count( $i_row[$fname], ' ' )) && checkemail( $i_row[$fname] ) ) ) {
              if($flink) {
              ?>
            <span class="black"><a href="<?= $flink ; ?>">
                <? } print disp($i_row[$fname]) ; if($flink) { ?>
            </a></span>
              <?
              }
            }
            elseif( $gdt = dformat($i_row[$fname],$c_row['ftype'],'vw') ) {
              if($flink) {
              ?>
            <span class="black"><a href="<?= $flink ; ?>">
                <? } print $gdt ; if($flink) { ?>
            </a></span>
              <?
              }
            }
            elseif( (substr($i_row[$fname],0,1) == '#') && (strlen($i_row[$fname]) == 7 ) ) {
              if($flink) {
              ?>
            <span class="black"><a href="<?= $flink ; ?>">
              <? } ?>
            <span style="<?= 'color:' . $i_row[$fname] ?>"><?= $i_row[$fname] ; ?></span>
              <? if($flink) { ?>
            </a></span>
              <?
              }
            }
            else {
              if($flink) {
                ?>
            <span class="black"><a href="<?= $flink ; ?>">
                <?
              }
              $ftype = get_sqlft($c_row['ftype']) ;
              if(sq_keys($c,'y') && ! $maxtext) {
                print clean_amp($i_row[$fname]) ;
              }
              elseif($ftype == 'decimal') {
                $decps = $c_row['fprts'] ? intval(substr($c_row['fprts'], strpos($c_row['fprts'], ',' ) + 1 )) : 2 ;
                print number_format(clean_float($i_row[$fname]),$decps) ;
              }
              else {                                  
                print cliptext($i_row[$fname],$maxtext) ;
              }
              if($flink) {
                ?>
            </a></span>
                <?
              }
            }
          }
        }
        ?>
        </td>
      <?
      }
    }
    if(isset($listcols) && is_array($listcols)) { foreach($listcols as $lhead => $lfunct) { if (function_exists($lfunct)) {
      globvadd( 
        'c_row', $c_row,
        'i_row', $i_row,
        'a_row', $a_row,
        'c', $c,
        's', $n,
        'thiscol', $fname,
        'fname', $fname,
        'fnamev', isvar($globvars[$fname]),
        'ftype', $c_row['ftype'],
        'fprms', $c_row['fprms'],
        'dval', $i_row[$fname],
        'lookups',$lookups
      );
      ?>
          <td valign="top"><?= $lfunct() ; ?></td>
    <? } } } ?>
        </tr> 
    <?
    if( $n >= $maxdisp ) { break ; }
  }
}
?>
      </table> <br>
        <?
        $mbs = (isset($maxbutts) && ($maxbutts > 0)) ? $maxbutts : 15 ;
        $arr = start_arr($nrows,$maxdisp,$start,$mbs);
        if($debug) { print_arr($arr,'start_arr'); }          
        ihide('action',$action,'go','','start',$start,'sort',$sort,'filter',filter_str($filter),'search',$search,'rngfr',$rngfr,'rngto',$rngto,'vars',$vars,'done',1); 
        if($fms) {
          ?>
      <div style="float:right; margin:2px 0 20px 20px;">
        <input type="submit" name="Submit" id="Submit" value="SAVE" class="submit"> 
      </div>
        <? } if( isset($arr['prev']) || isset($arr['next']) ) { ?>
      <table border="0" cellpadding="2" cellspacing="0" align="center" summary=""> 
        <tr> 
          <td class="button">
              <? if( isset($arr['prev']) ) { ?>
            <a href="<?= linkvars($sort,$arr['prev']) ; ?>">Previous</a>
              <? } ?> </td> 
          <td align="center">
              <? if( isset($arr['nums']) ) { 
                 foreach($arr['nums'] as $key => $val) {?>
            <span
            class="<?= (isset($arr['this']) && ($val == $arr['this'])) ? 'button1' : 'button'; ?>"> <a
            href="<?= linkvars($sort,$val) ; ?>"><?= $key ; ?></a></span>
                  <? } } ?></td> 
          <td align="right" class="button">
                <? if( isset($arr['next']) ) { ?>
            <a href="<?= linkvars($sort,$arr['next']) ; ?>">Next</a>
              <? } ?></td> 
        </tr> 
      </table>
          <? } ?>
      <br>
          <?
          if(function_exists('list_foot')) {
            list_foot();
          }
          ?>
    </form>
  <?
}

// ------------------------------------------------------------------------------

function fupdate() {
  global $globvars; extract($globvars,EXTR_SKIP);
  if($done && is_array($uform) && count($uform)) {
  if($debug) { print_arr($uform,'uform'); }  
    $udarr = array();
    $keyf = '';
    // current values
    foreach($c_arr as $c_row) {
      $c = $c_row['col'];
      if(sq_keys($c,'k')) {
        $keyf = $c_row['fname'];
        break ;
      }
    }
    if($keyf) {
      $string = "SELECT * FROM `$sq_table`";
      $query = my_query($string);
      $cvcheck = array() ;
      while($a_row = my_array($query)) {
        $cvcheck[$a_row[$keyf]] = $a_row ;
      }
      // update string
      foreach($uform as $uedit => $uarr) {
        $chkchg = isset($uarr['chkchg']) && $uarr['chkchg'] ? true : false ;
        if($uedit && ( isset($cvcheck[$uedit]) || $chkchg ) ) {
          $string = '' ;
          foreach($c_arr as $c_row) {
            $c = $c_row['col'];
            if(sq_keys($c,'c') && ( isset($uarr[$c_row['fname']]) || $chkchg ) ) {
              $uvalue = isset($uarr[$c_row['fname']]) ? $uarr[$c_row['fname']] : '' ;
              if(sq_keys($c,'s')) {
                $ss_field = $c_row['fname'] . '_ssord' ;
                if(isset($uarr[$ss_field])) {
                  $ss_ord = $uarr[$ss_field] ;
                  if(is_array($ss_ord) && count($ss_ord)) {
                    // multiple order sort
                    $arr = array();
                    asort($ss_ord);
                    foreach($ss_ord as $k => $v) {
                      if($v) {
                        $arr[] = $uvalue[$k];
                      }
                    }
                    $uvalue = $arr;
                  }                  
                }
                $chk1 = join_multi($c,$uedit,$uvalue);
                debug_arr();
                $chk2 = join_multi($c,$uedit,$uvalue,$uedit);
                debug_arr();
                if(is_array($chk1)) {
                  $chk1 = safe_implode( ",", $chk1 );
                }
                if(is_array($chk2)) {
                  $chk2 = safe_implode( ",", $chk2 );
                }
                if($chk1 != $chk2) {
                  $udarr[$uedit] = (isset($udarr[$uedit]) ? $udarr[$uedit] . 'm' : 'm') . $c ;
                }
              }
              if(is_array($uvalue)) {
                $uvalue = safe_implode( ",", $uvalue );
              }
              if( $gdt = dformat($uvalue,$c_row['ftype'],'db') ) {
                $uvalue = $gdt ;
              }
              // only if change or chkchg from checkbox
              $ucheck = isset($c_row['fname']) && isset($cvcheck[$uedit][$c_row['fname']]) ? $cvcheck[$uedit][$c_row['fname']] : '';
              if(defined('DBKEY') && sq_keys($c,'i')) {
                // aes_decrypt
                $ucheck = aes_decrypt($ucheck, DBKEY);
              }
              if($ucheck != $uvalue) {
                if(defined('DBKEY') && sq_keys($c,'i')) {
                  // aes_encrypt
                  $string .= " `{$c_row['fname']}` = AES_ENCRYPT('{$uvalue}','" . DBKEY . "') ,";
                }
                else {
                  $string .= " `{$c_row['fname']}` = '{$uvalue}', ";
                }
                $udarr[$uedit] = (isset($udarr[$uedit]) ? $udarr[$uedit] . 's' : 's') . $c ;
              }
            }
          }
          if($keyf && $uedit && $string) {
            $string = "UPDATE `$sq_table` SET " . substr( $string, 0, -2 ) . " WHERE `{$keyf}` = '{$uedit}' LIMIT 1";
            if($debug) { print_d($string,__LINE__,__FILE__); }
            my_query($string);
            logtable('UPDATE',$cntrl_user,$sq_table,$string);
          }

        }
      }
    }
    // print_arr($udarr);
    $udone = count($udarr);
    if($udone) {
      $udone .= ($udone == 1) ? ' record' : ' records';
      $globvars['msg'] .= "Updated $udone<br>" ;
    }
  }
}

function delete() {
  global $globvars; extract($globvars,EXTR_SKIP);
  if($allowdel) {
    $keyf = '';
    foreach($c_arr as $c_row) {
      $c = $c_row['col'];
      if(sq_keys($c,'k') ) {
        $string = "DELETE FROM `$sq_table` WHERE `{$c_row['fname']}` = '$del' LIMIT 1" ;
        logtable('DELETE',$cntrl_user,$sq_table,$string);
        if($debug) { print_d($string,__LINE__,__FILE__); }
        if($chk = my_query($string)) {
          $globvars['msg'] .= "Record $del deleted<br>";
        }
        else {
          $globvars['msg'] .= 'ERROR: ' . my_error() . '<br>' ;
        }
        break ;
      }
    }
    if(isset($stack) && is_array($stack)) {
      $prefix = str_replace('_main','_',$sq_table);
      foreach($stack as $table) {
        $string = "DELETE FROM `{$prefix}{$table}` WHERE `m_id` = '$del'";
        // print_p($string);
        $query = my_query($string);
        logtable('DELETE',$cntrl_user,"{$prefix}{$table}",$string);
      }
    }
  }
}

// ------------------------------------------------------------------------------

function add() {
  global $globvars; extract($globvars,EXTR_SKIP);
  $string1 = '';
  $jm_arr = array();
  foreach($c_arr as $c_row) {
    $c = $c_row['col'];
    $fname = $c_row['fname'] ;
    $ftype = get_sqlft($c_row['ftype']) ;
    $fprms = $c_row['fprms'] ;
    if(isset($_POST[$fname . '_encoded']) && ! isset($_POST[$fname])) {
      $_POST[$fname] = $_POST[$fname . '_encoded'];
    }
    globvars($fname) ;
    $fnamev = '' ;
    if(is_array($globvars[$fname])) {
      $globvars[$fname] = safe_implode(',',$globvars[$fname]) ;
    }
    if($gdt = dformat($globvars[$fname],$ftype,'db')) {
      $fnamev = $gdt ;
    }
    elseif($ftype == 'decimal') {
      $fnamev = clean_float($globvars[$fname]);
    }
    elseif($ftype == 'int') {
      $fnamev = clean_int($globvars[$fname]);
    }
    else {
      $fnamev = $globvars[$fname] ;
    }
    if(sq_keys($c,'g')){
      $fnamev = clean_urln($fnamev);
    }
    if( ! sq_keys($c,'a') ){
      if(defined('DBKEY') && sq_keys($c,'i')) {
        // aes_encrypt
        $string1 .= " `$fname` = AES_ENCRYPT('{$fnamev}','" . DBKEY . "') ,";
      }
      elseif(sq_keys($c,'f') && isset($_FILES['up_'.$fname])) {
        // upload file
        $up_chk = upload_check('up_'.$fname);
        if($debug) { 
          print_d('Field: up_'.$fname,__LINE__,__FILE__);
          print_d($up_chk,__LINE__,__FILE__);
        } 
        if($up_chk['res']) {
          if($_FILES['up_'.$fname]['name']) {
            if(substr_count('/', $_FILES['up_'.$fname]['name'])) {
              $start = strrpos($_FILES['up_'.$fname]['name'], '/' ) + 1 ;
              $up_file = substr($_FILES['up_'.$fname]['name'],$start) ;
            }
            else {
              $up_file = $_FILES['up_'.$fname]['name'] ;
            }
          }
          else {
            $up_file = $_FILES['up_'.$fname]['tmp_name'] ;
          }
          if($debug) { 
            print_d('File: ' . $up_file,__LINE__,__FILE__);
            print_d($_FILES['up_'.$fname],__LINE__,__FILE__);
          } 
          globvadd('up_file_'.$fname,$up_file);  
          $fpath = getfpath($c_row['sq_fpath']) ;
          $up_fname = upload_file($_FILES['up_'.$fname]['tmp_name'] , $fpath , $up_file, '', $debug ? 2 : 0) ;
          $string1 .= " `$fname` = '$up_fname' ,";
        }
        $globvars['msg'] .= isset($_FILES['up_'.$fname]['msg']) ? $_FILES['up_'.$fname]['msg'] . '<br>' : '' ;
      }
      elseif(sq_keys($c,'m')){
        if(isset($_POST[$fname]) && $_POST[$fname]) {
          // hash only if entered and not disabled
          $string1 .= " `$fname` = '" . hash($hashmethod,$_POST[$fname]) . "' ,";
        }
      }
      elseif(sq_keys($c,'t') && ( sq_keys($c,'v') || sq_keys($c,'x') || ! $fnamev || substr_count( $fnamev, '0000-00-00' ) ) ) {
        // timestamp if no value set
        $string1 .= " `$fname` = NOW() ,";
      }
      elseif( sq_keys($c,'e') || (sq_keys($c,'v') && $fnamev) || ( sq_keys($c,'k') && ! sq_keys($c,'a') ) ) {
        // standard field
        $ss_ord = $fname . '_ssord' ;
        if((sq_keys($c,'s') || sq_keys($c,'f')) && isset($ss_ord)) {
          // multiple order sort
          globvars($ss_ord);
          if(is_array($globvars[$ss_ord]) && count($globvars[$ss_ord])) {
            $arr = array();
            asort($globvars[$ss_ord]);
            $vals = safe_explode( ",", $fnamev );
            foreach($globvars[$ss_ord] as $k => $v) {
              if($v) {
                $arr[] = $vals[$k];
              }
            }
            $fnamev = count($arr) ? safe_implode( ",", $arr ) : '' ;
          }
          $jm_arr[$c] = $fnamev ;
        }
        if(! sq_keys($c,'z')) {
          if(sq_keys($c,'w') && ! $fnamev) {
            // null if blank
            $string1 .= " `$fname` = null ,";
          }
          else {
            $string1 .= " `$fname` = '" . $fnamev . "' ,";
          }
        }
      }
    }
    if(sq_keys($c,'k')){
      $go = $fnamev ;
    }
  }
  if($string1) {
    $string = "INSERT INTO `$sq_table` SET" . substr($string1,0,(strlen($string1)-1)) ;
    logtable('INSERT',$cntrl_user,$sq_table,$string);
    if($debug) { print_d($string,__LINE__,__FILE__); }    
    if($chk = my_query("$string")) {
      if(my_id()) {
        $globvars['go'] = my_id() ;
      }
      else {
        $globvars['go'] = $go ;
      }
      // $globvars['msg'] .= 'Record "' . $globvars['go'] . '" added (' . date("H:i:s") . ')<br>';
      if(count($jm_arr)) {
        foreach($jm_arr as $c => $fnamev) {
          join_multi($c,$globvars['go'],$fnamev,$globvars['go']);
          debug_arr();
        }
      }
      $globvars['msg'] .= 'New record added<br>';
      $globvars['action'] = 'edit' ;
    }
    else {
      $globvars['msg'] .= 'ERROR: ' . my_error() . '<br>' ;
    }
  }
  else {
    $globvars['msg'] .= 'Nothing to add<br>';
  }
}

// ------------------------------------------------------------------------------

function update() {
  global $globvars; extract($globvars,EXTR_SKIP);
  $string1 = $string2 = '';
  foreach($c_arr as $c_row) {
    $c = $c_row['col'];
    $fname = $c_row['fname'] ;
    $ftype = $c_row['ftype'] ;
    $fprms = $c_row['fprms'] ;
    if(isset($_POST[$fname . '_encoded']) && ! isset($_POST[$fname])) {
      $_POST[$fname] = $_POST[$fname . '_encoded'];
    }
    globvars($fname) ;
    $fnamev = '' ;
    if(is_array($globvars[$fname])) {
      $globvars[$fname] = safe_implode(',',$globvars[$fname]) ;
    }
    if($gdt = dformat($globvars[$fname],$ftype,'db')) {
      $fnamev = $gdt ;
    }
    elseif($ftype == 'decimal') {
      $fnamev = clean_float($globvars[$fname]);
    }
    elseif($ftype == 'int') {
      $fnamev = clean_int($globvars[$fname]);
    }
    else {
      $fnamev = $globvars[$fname] ;
    }
    if(sq_keys($c,'g')){
      $fnamev = clean_urln($fnamev);
    }
    if( ! ( sq_keys($c,'av') || ( sq_keys($c,'k') && !sq_keys($c,'e') ) ) ) {
      if(defined('DBKEY') && sq_keys($c,'i')) {
        // aes_encrypt
        $string1 .= " `$fname` = AES_ENCRYPT('{$fnamev}','" . DBKEY . "') ,";
      }
      elseif(sq_keys($c,'f') && isset($_FILES['up_'.$fname])) {
        // upload file
        $up_chk = upload_check('up_'.$fname);
        if($debug) { 
          print_d('Field: up_'.$fname,__LINE__,__FILE__);
          print_d($up_chk,__LINE__,__FILE__);
        } 
        if($up_chk['res']) {
          if($_FILES['up_'.$fname]['name']) {
            if(substr_count('/', $_FILES['up_'.$fname]['name'])) {
              $start = strrpos($_FILES['up_'.$fname]['name'], '/' ) + 1 ;
              $up_file = substr($_FILES['up_'.$fname]['name'],$start) ;
            }
            else {
              $up_file = $_FILES['up_'.$fname]['name'] ;
            }
          }
          else {
            $up_file = $_FILES['up_'.$fname]['tmp_name'] ;
          }
          if($debug) { 
            print_d('File: ' . $up_file,__LINE__,__FILE__);
            print_d($_FILES['up_'.$fname],__LINE__,__FILE__);
          } 
          globvadd('up_file_'.$fname,$up_file);  
          $fpath = getfpath($c_row['sq_fpath']) ;
          $up_fname = upload_file($_FILES['up_'.$fname]['tmp_name'] , $fpath , $up_file, '', $debug ? 2 : 0) ;
          $string1 .= " `$fname` = '$up_fname' ,";
        }
        $globvars['msg'] .= isset($_FILES['up_'.$fname]['msg']) ? $_FILES['up_'.$fname]['msg'] . '<br>' : '' ;
      }
      elseif(sq_keys($c,'m')) {
        if(isset($_POST[$fname]) && $_POST[$fname]) {
          // hash only if entered and not disabled
          $string1 .= " `$fname` = '" . hash($hashmethod,$_POST[$fname]) . "' ,";
        }
      }
      elseif(sq_keys($c,'e')) {
        // standard field
        $ss_ord = $fname . '_ssord' ;
        if((sq_keys($c,'s') || sq_keys($c,'f')) && isset($ss_ord)) {
          globvars($ss_ord);
          if(is_array($globvars[$ss_ord]) && count($globvars[$ss_ord])) {
            $arr = array();
            asort($globvars[$ss_ord]);
            $vals = safe_explode( ",", $fnamev );
            foreach($globvars[$ss_ord] as $k => $v) {
              if($v && isset($vals[$k])) {
                $arr[] = $vals[$k];
              }
            }
            if(count($arr)) {
              $fnamev = safe_implode( ",", $arr );
            }
            else {
              $fnamev = '';
            }
          }
        }
        if(! sq_keys($c,'z')) {
          if(sq_keys($c,'w') && ! $fnamev) {
            // null if blank
            $string1 .= " `$fname` = null ,";
          }
          else {
            $string1 .= " `$fname` = '" . $fnamev . "' ,";
          }
        }
        join_multi($c,$go,$fnamev,$go);
        debug_arr();
      }
    }
    elseif(sq_keys($c,'t') && ( sq_keys($c,'vx') ) ) {
      $string1 .= " `$fname` = NOW() ,";
    }
    if(sq_keys($c,'k')){
      if(sq_keys($c,'e') && ! sq_keys($c,'av') && ( $go != $fnamev ) ) {
        $gonew = $fnamev ;
      }
      $string2 = " WHERE `$fname` = '" . $go . "' LIMIT 1";
    }
  }
  $chk = true;
  if($string1 && $string2) {
    $string = "UPDATE `$sq_table` SET " . substr($string1,0,(strlen($string1)-1)) . $string2 ;
    logtable('UPDATE',$cntrl_user,$sq_table,$string);
    if($debug) { print_d($string,__LINE__,__FILE__); }    
    $chk = my_query($string);
  }
  if($chk) {
    if(isset($gonew)) {
      $globvars['go'] = $gonew ;
    }
    // $globvars['msg'] .= 'Record "' . $globvars['go'] . '" updated (' . date("H:i:s") . ')<br>';
    $globvars['msg'] .= 'Record updated<br>';
  }
  else {
    $globvars['msg'] .= 'ERROR: ' . my_error() . '<br>' ;
  }
}

// ------------------------------------------------------------------------------

function form() {
  global $globvars; extract($globvars,EXTR_SKIP);
  $tn = 0 ;
  $f_err = 0 ;
  $inext = $iprev = '';
  // media database
  $media_id = $media_fp = [];
  if(isset($globvars['db_medtable']) && $globvars['db_medtable']) {
    $string = "select * from `{$globvars['db_medtable']}` order by `id`";
    $query = my_query($string);
    if($nrows = my_rows($query)) {
      while($a_row = my_assoc($query)) {
        $a_row['fpath'] = "{$a_row['path']}/{$a_row['file']}";
        $media_id[$a_row['id']] = $a_row;
        $media_fp[$a_row['fpath']] = $a_row['id'];
      }
    }
  }
  foreach($c_arr as $c_row) {
    $c = $c_row['col'];
    $fname = $c_row['fname'] ;
    // arrays for makefile
    if( $done && isset($makefile) && is_array($makefile) && isset($makefile[0]) ) {
      if( ( $makefile[0] == $c ) || ( isset($makefile[2]) && in_csv($makefile[2],$c) ) ) {
        $fnamev = $globvars[$fname] ;
        $fmk_arr[$c]['f'] = $fname ;
        $fmk_arr[$c]['v'] = $globvars[$fname] ;
        if(sq_keys($c,'k')){
          $fmk_arr[$c]['v'] = $go ;
        }
        elseif( sq_keys($c,'o') && isset($lookups[$fname]['sq_arr'][$fnamev]) ) {
          $fmk_arr[$c]['v'] = $lookups[$fname]['sq_arr'][$fnamev][$c_row['sq_lookv']] ;
        }
      }
    }
    // get key and find record
    $istring = '';
    if( sq_keys($c,'k') ) {
      $fkey = $fname ;
      // aes_decrypt
      $astar = '*';
      if(defined('DBKEY')) {
        foreach($l_arr as $c_row) {
          if(sq_keys($c_row['col'],'i')) {
            $astar .= ", AES_DECRYPT(`{$c_row['fname']}`, '" . DBKEY . "') as `{$c_row['fname']}_decrypted`";
          }
        }
      }
      $istring = "SELECT $astar FROM `$sq_table` WHERE `$fkey` = '$go' LIMIT 1" ;
      $item = my_query($istring);
      if($debug) { print_d($istring,__LINE__,__FILE__); }
      if($action == 'edit' && ! my_rows($item)) {
        // record not found
        $globvars['action'] = $globvars['go'] = '';
        ilist();
        return ;
      }
      $i_row = my_array($item);

      if(isvar($prevnext)) {
        $pnstring = "SELECT `$fkey` FROM `$sq_table` WHERE ( 
          `$fkey` = IFNULL((select min(`$fkey`) FROM `$sq_table` WHERE `$fkey` > '$go'),0) 
          OR `$fkey` = IFNULL((select max(`$fkey`) FROM `$sq_table` WHERE `$fkey` < '$go'),0)
        ) ";
        // print_p($pnstring);
        $pquery = my_query($pnstring);
        while($p_row = my_array($pquery)) {
          if($p_row[$fkey] < $go) {
            $iprev = $p_row[$fkey];
            // print $iprev ;
          }
          elseif($p_row[$fkey] > $go) {
            $inext = $p_row[$fkey];
            // print $inext ;
          }
        }
      }

    }
  }

  // make urln
  if( $done && isset($makeurln) && is_array($makeurln) && sq_keys($makeurln[0],'e') && sq_keys($makeurln[1],'e') ) {
    $fnum = array_search($fkey,$fields);
    $umx = $c_arr[$makeurln[0]]['fprms'] - $c_arr[$fnum]['fprms'] - 1;
    $uto = $c_arr[$makeurln[0]]['fname'];
    $ufr = $c_arr[$makeurln[1]]['fname'];
    $uto_o = $uto_n = $globvars[$uto] ;
    if(! $uto_o) {
      $uto_n = $globvars[$ufr] ; // get from field
    }
    $uto_n = clean_urln($uto_n,$umx); // cleanup
    if(! $uto_n) {
      if(sq_keys($makeurln[1],'w')) {
        $uto_n = 'null' ; // blank set to null
      }
      else {
        $uto_n = $go ; // blank set to key
      }
    }
    $uc = "SELECT `$uto` FROM `$sq_table` WHERE `$fkey` != '$go' AND `$uto` = '$uto_n' LIMIT 1";
    $uq = my_query($uc); // check is unique
    if(my_rows($uq)) {
      $uto_n .= '-' . $go ;
    }
    if($uto_n != $uto_o) {
      if($uto_n != 'null') { $uto_n = "'{$uto_n}'"; }
      // change if necessary
      $uu = "UPDATE `$sq_table` SET `$uto` = $uto_n WHERE `$fkey` = '$go' LIMIT 1";
      my_query($uu);
      logtable('UPDATE',$cntrl_user,$sq_table,$uu);
      if($debug) { print_d('Set URLN: ' . $uu,__LINE__,__FILE__); }
      $item = my_query("SELECT * FROM `$sq_table` WHERE `$fkey` = '$go' LIMIT 1");
      $i_row = my_array($item);
    }
  }

  // make files
  $fmg = '';
  if($debug && isset($fmk_arr)) { print_arr($fmk_arr,'fmk_arr'); }    
  if( isset($makefile) && is_array($makefile) && $makefile[0] && sq_keys($makefile[0],'e') && $makefile[1] && file_exists($makefile[1]) && isset($fmk_arr) ) {
    if($mfname = $fmk_arr[$makefile[0]]['v']) {
      $ptemplate = $makefile[1] ;
      $arr_vars = array();
       if(isset($makefile[2])) {
        // replace params
        if(is_array($params = safe_explode( ',', $makefile[2] ))) {
          foreach($params as $param) {
            if($param && isset($fmk_arr[$param]['f']) && $fmk_arr[$param]['f']) {
              $arr_vars[$fmk_arr[$param]['f']] = $fmk_arr[$param]['v'] ;
            }
          }
        }
      }
      $fmg = makefile('../', $mfname, $ptemplate, $arr_vars) ;
    }
  }

  // make images
  $iu = '';
  foreach($c_arr as $c_row) {
    $c = $c_row['col'];
    $fname = $c_row['fname'];
    if( sq_keys($c,'f') && $c_row['sq_fmake'] && isset($i_row[$fname]) ) { 
      $fval = $i_row[$fname] ;
      $sqm_arr = safe_explode( '|', $c_row['sq_fmake'] ) ;
      if($debug) { print_arr($sqm_arr,"$fname ($c)"); }
      $sqt = count($sqm_arr);
      $sqm_ok = 0 ;
      $sqn = 0 ;
      // start loop fmake multiple entries for field
      while($sqn < $sqt && ! $sqm_ok) {
        $sqm = $sqm_arr[$sqn++];
        if($sqm = safe_explode( '-', $sqm )) {
          if(($sqm[3] == 'u') && isset(${'up_file_'.$fname})) {
            // user sizes on upload
            globvars('up_width_'.$fname,'up_height_'.$fname);
            $uw = $globvars['up_width_'.$fname];
            $uh = $globvars['up_height_'.$fname]; 
            if( $uw && is_numeric($uw) && ($uw > 0) && ( $uw < $sqm[1] ) ) { $sqm[1] = $uw ; }
            if( $uh && is_numeric($uh) && ($uh > 0) && ( $uh < $sqm[2] ) ) { $sqm[2] = $uh ; }
            $sqm[3] = 'm';
          }
          if($debug) { print_arr($sqm,"$fname ({$c}-" . ($sqn-1) . ')'); }   
          if( is_numeric($sqm[1]) && is_numeric($sqm[2]) ) { 
            if($sqm[0] == 'v') {
              // if set to v make it this field
              $sqm[0] = $c ;
            }

            // start make image from another field
            if( $sqm[0] != $c ) {
              if( isimg($i_row[$sqm[0]]) ) { 
                if(isset($sqm[6]) && $sqm[6] && $sqm[6] != 'n') {
                  if(isset($i_row[$sqm[0]]) || ! substr_count( $fval, substr($i_row[$sqm[0]],0,strrpos($i_row[$sqm[0]],'.')) ) ) {
                    // force always override existing new file
                    if($debug) { print_d("Forced Override",__LINE__,__FILE__); }
                    $fval = '';
                  }
                }
                if(! $fval) {
                  // make from another field if not set or override allowed
                  if(isset($fnosuff) && $fnosuff) { 
                    $suff = '';
                  }
                  elseif(isset($fixsuff) && $fixsuff) {
                    $suff = $fixsuff;
                  }
                  else {
                    $suff = '_' . $sqm[0] . $sqm[3] . $c ;
                  }
                  if($debug) { print_d("Make ($c) from ({$sqm[0]}) suff ($suff)",__LINE__,__FILE__); }
                  if( file_exists( ( $ofpath = getfpath($sq_fpath[$sqm[0]]) ) . $i_row[$sqm[0]] ) ) { 
                    if($debug) { print_d("File exists ({$ofpath}{$i_row[$sqm[0]]})",__LINE__,__FILE__); }
                    if($nf = make_image( $ofpath, $i_row[$sqm[0]], getfpath($c_row['sq_fpath']), $suff, $sqm[1], $sqm[2], $sqm[3], $sqm[4], $sqm[5])) { 
                      if($debug) { print_d("New File ($nf)",__LINE__,__FILE__); }
                      $sqm_ok = 1 ;
                      $iu .= "`{$c_row['fname']}` = '$nf', " ;
                    }
                  }
                  else {
                    if($debug) { print_d("File NOT found ({$i_row[$sqm[0]]})",__LINE__,__FILE__); }
                  }
                }
                elseif($debug) { print_d("File already set for field",__LINE__,__FILE__); }
              }
              elseif($debug) { print_d('No image file',__LINE__,__FILE__); }   
              if($sqn == $sqt && ! $sqm_ok) {
                // Last loop but image not created try amend this field
                if($debug) { print_d("Image not created try amend this field",__LINE__,__FILE__); }
                $sqm[0] = $c ;
                // $sqm[5] = 1 ;
              }
            }
            // end make image from another field

            // start amend image in this field
            if( (! $sqm_ok) && $sqm[0] == $c ) {
              if( isimg($i_row[$sqm[0]]) ) { 
                if($debug) { print_d('Image Type OK',__LINE__,__FILE__); } 
                if(isset($fnosuff) && $fnosuff) { 
                  // no suffix (force don't delete original)
                  $suff = '';
                  $sqm[5] = 0 ;
                }
                elseif(isset($fixsuff) && $fixsuff) {
                  $suff = $fixsuff;
                }
                else {
                  $suff = '_' . $sqm[3] ;
                }
                if($debug) {print_d("Make ($c) from ({$sqm[0]}) suff ($suff)",__LINE__,__FILE__); }
                if( $fval && ! substr_count( $suff . '.' , $fval ) ) {
                  if($debug) { print_d("Old File ($fval)",__LINE__,__FILE__); }
                  $forceopt = false ;
                  if(isset($_FILES['up_'.$fname])) {
                    // force optimise uploaded file even if resize not needed (unless param 6 is n/0)
                    if(! ( isset($sqm[6]) && (($sqm[6] == 'n') || (! $sqm[6])) ) ) {
                      $forceopt = true ;
                      if($debug) { print_d("Force Optimise ($fval)",__LINE__,__FILE__); }
                    }
                  }
                  if($nf = make_image( getfpath($c_row['sq_fpath']), $i_row[$sqm[0]], getfpath($c_row['sq_fpath']), $suff, $sqm[1], $sqm[2], $sqm[3], $sqm[4], $sqm[5], $forceopt)) {
                    if($debug) { print_d("New File ($nf)",__LINE__,__FILE__); }
                    $sqm_ok = 1 ;
                    if($nf != $i_row[$sqm[0]]) {
                      // change field value
                      $i_row_c[$sqm[0]] = $i_row[$sqm[0]] ;
                      $i_row[$sqm[0]] = $nf ;
                      $iu .= "`{$c_row['fname']}` = '$nf', " ;
                    }
                  }
                  elseif($debug) { print_d("Image not created",__LINE__,__FILE__); }
                }
                elseif($debug) { print_d("File not found or exists ($fval)",__LINE__,__FILE__); }
              }
              elseif($debug) { print_d('No image file',__LINE__,__FILE__); }   
            }
            // end amend image in this field
          }
          elseif($debug) { print_d('Invalid dimensions',__LINE__,__FILE__); }   
        }
      }
      // end loop fmake multiple entries for field
    }
  }
  if($iu) {
    $string = "UPDATE `$sq_table` SET " . substr($iu, 0, -2 ) . " WHERE `$fkey` = '$go' LIMIT 1" ;
    if($debug) { print_d($string,__LINE__,__FILE__); }
    my_query($string);
    logtable('UPDATE',$cntrl_user,$sq_table,$string);
    $item = my_query("SELECT * FROM `$sq_table` WHERE `$fkey` = '$go' LIMIT 1");
    $i_row = my_array($item);
  }

  // display form
  ?>
  <form method="post" action="<?= $php_self ; ?>" onsubmit="return esubmit(<?= file_exists('../scripts/iencode.php') ? 1 : 0 ; ?>)" enctype="multipart/form-data" name="eform" id="eform" autocomplete="off">
      <div class="maintop2">
        <? if($action=='edit') { ?>
        <input type="hidden" name="go" id="go" value="<?= $go ; ?>"> 
        <table border="0" cellpadding="0" cellspacing="0" width="100%" summary=""> 
          <tr> 
            <td width="15%" align="left"> 
              <h2 class="h2">Edit Record</h2></td> 
            <? if(isvar($prevnext) && ($inext || $iprev)) { ?>
            <td width="15%" align="left" class="button"> 
              <? if($iprev) { ?><a href="<?= $php_self . '?action=edit&amp;go=' .$iprev ; ?>">PREVIOUS</a> <? } if($inext) { ?> <a href="<?= $php_self . '?action=edit&amp;go=' .$inext ; ?>">NEXT</a><? } ?></td>
            <? } ?>
            <td width="40%" align="left" class="red" style="padding-left:10px;"><?= $fmg ; ?></td> 
            <td width="30%" align="right"> 
              <table border="0" cellpadding="0" cellspacing="0" summary=""> 
                <tr> 
                  <? if($allowsim) { ?>
                  <td align="right" width="100" class="buttont"><a href="#" onclick="sform.submit();">Add Similar</a></td>
                  <? } elseif($allowadd) { ?>
                  <td align="right" width="100" class="buttont"><a href="<?= linkvars($sort,$start,'add') ; ?>">Add New</a></td>
                  <? } if((! isset($globvars['hidelist'])) || (isset($globvars['hidelist']) && ! $globvars['hidelist'])) { ?>
                  <td align="right" width="100" class="buttont"><a href="<?= linkvars($sort,$start) ; ?>">List Records</a></td> 
                  <? } ?>
                </tr> 
              </table> </td> 
          </tr> 
        </table>
        <? } else { ?>
        <table border="0" cellpadding="0" cellspacing="0" width="100%" summary=""> 
          <tr> 
            <td> 
              <h2 class="h2">Add Record</h2></td> 
            <td align="right" width="100" class="buttont"><a href="<?= linkvars($sort,$start) ; ?>">List Records</a></td> 
          </tr> 
        </table>
        <? } ?>
      </div>
      <br> 
      <table border="0" cellpadding="0" cellspacing="0" width="100%" summary=""> 
        <tr valign="top"> 
          <td valign="top">
            <?
            if(sq_keys(0,'b') && ! sq_keys(0,'x')) {
              echo '<br>';
              if( isset($c_arr[0]['sq_heads']) && $c_arr[0]['sq_heads'] ) {
                echo '<h3>' . clean_upper($c_arr[0]['sq_heads']) . '</h3>';
              }
              else {
                echo '<br>';
              }
            }
            ?>
            <table border="0" cellpadding="4" cellspacing="0"
            width="<?= isvar($formwidth) ? $formwidth : '100%'; ?>" class="tabler" summary=""> 
  <?
  $globvars['save'] = 0 ;
  foreach($c_arr as $c_row) {
    $c = $c_row['col'];
    $ong = 'fldchg++;';
    if($sq_jcall[$c]) {
      $ong .= "{$sq_jcall[$c]}();";
    }
    if(! sq_keys($c,'x')) {
      if(($c>0) && sq_keys($c,'b') && ! sq_keys($c,'x')) {
        // echo '</table></td><td style="padding-left:20px;">' ; // alternative for new column
        echo '</table><br>';
        if(substr_count( $c_row['sq_keys'],'bb')) {
          echo '<div style="text-align:center;"><input type="submit" name="Submit' . $c . '" value="SAVE" class="submit"></div>' ;
        }
        if( isset($c_row['sq_heads']) && $c_row['sq_heads'] ) {
          echo '<h3>' . clean_upper($c_row['sq_heads']) . '</h3>';          
        }
        else {
          echo '<br>';
        }
        echo '<table border="0" cellpadding="4" cellspacing="0" width="' . ( isvar($formwidth) ? $formwidth : '100%' ) . '" class="tabler" summary="">';
      }
      $fname = $c_row['fname'] ;
      $ftype = get_sqlft($c_row['ftype']) ;
      $fprms = $c_row['fprms'] ;
      $flen = $c_row['flen'] ;
      $mlen = $c_row['mlen'] ;
      globvars($fname) ;
      $fnamev = isset( $globvars[$fname]) ? $globvars[$fname] : '' ;
      if( sq_keys($c,'ve') || isvar($c_row['sq_funct']) ) {
        ?>
              <tr> 
                <td width="<?= isvar($formleftc) ? $formleftc : ''; ?>" class="thb button">
                  <b class="chead">
                  <?
                  $chead = $fname ;
                  if(isset($globvars['cleanfname']) && $globvars['cleanfname'] && substr($chead,1,1) == '_') {
                    $chead = substr($chead,2);
                  }
                  $chead = clean_ucwords(str_replace('_',' ',$chead)) ;
                  if(isvar($c_row['sq_names'])) {
                    $chead = $c_row['sq_names'] ;
                  }
                  if($chead == 'Id') {
                    $chead = 'ID';
                  }
                  echo $chead ;
                  ?></b><?
                        
        if( sq_keys($c,'d') ) {
          echo '*';
          $tn = 1 ;
        }                   
        
        $fpath = getfpath($c_row['sq_fpath']);
        if(sq_keys($c,'f') && isset($i_row[$fname])) {
          // image popup
          image_pop($fpath . $i_row[$fname],'id_' . $fname);
        }
        ?></td> 
                <td style="height:26px;" class="<?= isset($sq_class[$c]) ? $sq_class[$c] : '' ?>"><?
      if( $action == 'edit' ) {
        // aes_decrypt
        if( sq_keys($c,'i') && isset($i_row[$fname.'_decrypted'])) {
          $i_row[$fname] = $i_row[$fname.'_decrypted'];
        }
        // sel $dval unless hidden or fake
        $dval = sq_keys($c,'h') || sq_keys($c,'z') ? '' : ( isset($i_row[$fname]) ? $i_row[$fname] : '' );
        // similar add
        if($allowsim && sq_keys($c,'r')) {
          $globvars['sfield'][$fname] = $dval ;
        }
      }
      elseif($allowsim && sq_keys($c,'r') && isset($sform[$fname]) ) {
        // from add similar
        $dval = $sform[$fname];
      }
      elseif( $c_row['sq_deflt'] ) {
        // Default from settings
        $dval = $c_row['sq_deflt'];
      }
      elseif( isset($globvars['cols'][$fname]['Default']) && $globvars['cols'][$fname]['Default'] ) {
        // Default from mySQL
        $dval = $globvars['cols'][$fname]['Default'];
      }
      elseif( sq_keys($c,'t') ) {
        // date/time now
        $dval = dformat('',$ftype,'vw') ;
      }
      else {
        // blank (for add)
        $dval = '';
      }

      $fn = true ;
      if( ($funct = isvar($c_row['sq_funct'])) && function_exists($funct) ) {
        // globvars for sq_funct in form
        if(! is_array($i_row)) {
          $i_row = [];
          foreach($globvars['fields'] as $f) {
            $i_row[$f] = '';
          }
        }
        globvadd(
          'c_row', $c_row,
          'i_row', $i_row,
          'c', $c,
          's', $globvars['save'],
          'thiscol', $fname,
          'fname', $fname,
          'fnamev', $fnamev,
          'ftype', $c_row['ftype'],
          'fpath', $fpath,
          'fprms', $fprms,
          'dval', $dval);
        $fn = $funct();
      }
      if($fn == true) {
        if( sq_keys($c,'a') && ( $action == 'add' ) ) {
          echo 'Auto';
        }
        elseif( ( $action == 'edit' ) && ( sq_keys($c,'a') || ( sq_keys($c,'k') && ! sq_keys($c,'e') ) ) ) {
          if(is_numeric($edlink) && $edlink) {
            echo str_pad ( $dval, $edlink, '0', STR_PAD_LEFT ) ;
          }
          else {
            echo $dval;
          }
        }
        elseif( sq_keys($c,'e') && sq_keys($c,'p') ) {
            $globvars['save']++;
            $flen = ($fprms > 7) ? $fprms : 7 ;
            $mlen = ($flen > $maxbox) ? $maxbox : $flen ;
            color_picker($fname,$dval,$flen,$mlen);
        }
        elseif( sq_keys($c,'e') && sq_keys($c,'o') ) {
          // select from lookup table
          $globvars['save']++;
          $jrebutt = 'REFRESH';
          ?>
          <div style="display:inline-block; vertical-align:middle;">
            <select class="chosen-select" name="<?= $fname ; ?>" id="<?= $fname ; ?>" size="1" onchange="<?= $ong; ?>" style="width:500px;"> 
              <option value="">** Select **</option>
              <?
              if(isset($lookups[$fname]['sq_arr'])) {
                foreach($lookups[$fname]['sq_arr'] as $key => $opt_arr) {
                  $showv = $val = $opt_arr[$c_row['sq_lookv']] ;
                  $dsp = $c_row['sq_lookd'] ;
                  if( substr_count($dsp,'k') || substr_count($dsp,'v') || substr_count($dsp,'[[') ) {
                    $showv = $dsp ;
                    $showv = rep_var($showv,$opt_arr);
                    if($showv == $dsp) {
                      $showv = str_replace('k',$key,$showv);
                      $showv = str_replace('v',$val,$showv);
                    }
                  }
                  if($gdt = dformat($showv,$c_row['ftype'],'vw')) {
                    $showv = $gdt ;
                  }
                  if($showv) {
                    $chk = join_multi($c,$go,$dval);
                    if(in_array($key, $chk)) { 
                      ?>
              <option value="<?= $key ; ?>" selected="selected"><?= cliptext(clean_amp($showv),80) ; ?></option>
                    <? } elseif(! sq_keys($c,'j')) { ?>
              <option value="<?= $key ; ?>"><?= cliptext(clean_amp($showv),80) ; ?></option>
                    <? } else { $jrebutt = 'LOAD All'; }
                  }
                }
              }
              ?>
            </select> 
          </div>
            <? 
            if(! (isset($nojrelook) && $nojrelook)) { 
              idhide($fname . '_sqlookt',$c_row['sq_lookt']);
              idhide($fname . '_sqlookk',$c_row['sq_lookk']);
              idhide($fname . '_sqlookv',$c_row['sq_lookv']);
              idhide($fname . '_sqlookd',iencode($c_row['sq_lookd'],true));
              idhide($fname . '_sqlookl',str_replace('../','..|',$c_row['sq_lookl']));
              idhide($fname . '_sqlookf',$c_row['sq_lookf']);
              $jrelook = "jrelook('{$fname}'); $(this).html('REFRESH'); return false;"; 
              if(! sq_keys($c,'n')) {
                ?>
                <div style="display:inline-block; vertical-align:middle; padding-left:5px;" class="button">
                  <a href="#" onclick="<?= $jrelook ?>"><?= $jrebutt ?></a> 
                </div>            
                <? 
              }
            }
        }
        elseif( sq_keys($c,'e') && sq_keys($c,'s') ) {
          // multiple order options
          $globvars['save']++;
          if( substr_count( $c_row['sq_keys'],'ss') ) {
            if(isset($lookups[$fname]['sq_arr'])) {
              if(! (isset($nojrelook) && $nojrelook)) {
                idhide($fname . '_sqlookt',$c_row['sq_lookt']);
                idhide($fname . '_sqlookk',$c_row['sq_lookk']);
                idhide($fname . '_sqlookv',$c_row['sq_lookv']);
                idhide($fname . '_sqlookd',iencode($c_row['sq_lookd'],true));
                idhide($fname . '_sqlookl',str_replace('../','..|',$c_row['sq_lookl']));
                idhide($fname . '_sqlookf',$c_row['sq_lookf']);
                $jrelook = "jreorder('{$fname}'); return false;"; 
              }
              if(! sq_keys($c,'n')) {
                $ssx = "$('#{$fname}_ssf').val(''); $('.{$fname}_ssr').show();";
                ?> 
                <div style="position:relative;">
                  <div style="position:absolute; top:2px; right:22px; z-index:100; text-align:right;">
                    <span class="button"><a style="width:70px; box-sizing:border-box; margin-bottom:5px;" href="#" onclick="<?= $ssx . $jrelook ?>">REFRESH</a></span><br>
                    <a title="clear" style="color:#000000;" href="#" onclick="<?= $ssx ; ?>return false;">&#10006;&nbsp;</a> 
                    <input id="<?= $fname . '_ssf' ; ?>" onkeyup="ssfilter('<?= $fname ; ?>')" type="text" class="ssfilter" placeholder="FILTER" style="height:22px; width:70px; box-sizing:border-box;">
                   </div>
                </div>
                <?
              }
              ?>
              <div style="min-height:60px; max-height:190px; overflow:auto; position:relative;">
              <?
              $arr = $urls = array();
              foreach($lookups[$fname]['sq_arr'] as $key => $opt_arr) {
                $showv = $val = $opt_arr[$c_row['sq_lookv']] ;
                $dsp = $c_row['sq_lookd'] ;
                if( substr_count($dsp,'k') || substr_count($dsp,'v') || substr_count($dsp,'[[') ) {
                  $showv = $dsp ;
                  $showv = rep_var($showv,$opt_arr);
                  if($showv == $dsp) {
                    $showv = str_replace('k',$key,$showv);
                    $showv = str_replace('v',$val,$showv);
                  }
                }
                if($showv) {
                  $arr[$key] = cliptext(clean_amp($showv),90);
                }
                $urls[$key] = $sq_lookl[$c] ? rep_var($sq_lookl[$c],$opt_arr) : '';
              }
              if(count($arr)) {
                $dvals = join_multi($c,$go,$dval);
                debug_arr();
                $sn = 0 ;
                foreach($dvals as $optsel) {
                  if(isset($arr[$optsel])) {
                    if(! $sn++) {
                      ?>
                    <div style="margin-bottom:10px" id="<?= $fname . '_multsel' ; ?>">
                      <div style="margin-bottom:10px">
                        <u>Selected: Delete order number to unselect option</u> 
                      </div>
                      <?
                    }
                    ?>
                      <div style="margin-bottom:5px" class="<?= $fname . '_ssr' ; ?>">
                        <div style="display:inline-block; vertical-align:middle; margin-right:3px">
                          <input size="2" type="text" id="<?= $fname . '_sel_' . $optsel ; ?>" class="<?= $fname . '_sel' ; ?>" name="<?= $fname . '_ssord[]' ; ?>" value="<?= $sn; ?>" onchange="<?= $ong; ?>" autocomplete="off">
                          <input type="hidden" name="<?= $fname . '[]' ; ?>" value="<?= $optsel; ?>"> 
                        </div>
                        <div style="display:inline-block; vertical-align:middle">
                          <?
                          $url = isset($urls[$optsel]) && $urls[$optsel] ? clean_url($urls[$optsel]) : '';
                          if($url) {
                            ?>
                          <a target="popfile" href="<?= $url ?>">
                            <?
                          }
                          print '<span class="' . $fname . '_sst">' . $arr[$optsel] . '</span>';
                          if($url) {
                            ?>
                          </a>
                            <?
                          }
                          ?>
                        </div>
                      </div>
                    <?
                  }
                }
                if($sn) {
                  ?>
                    </div>
                  <?
                }
                $nn = 0 ;
                foreach($arr as $notkey => $notval) {
                  if(! in_array( $notkey, $dvals )) {
                    if(! $nn++) {
                      ?>
                    <div id="<?= $fname . '_multnot' ; ?>">
                      <div style="margin-bottom:10px;">
                        <u>Not selected: Enter order number to select option</u> 
                      </div>
                      <?
                    }
                    ?>
                      <div style="margin-bottom:5px" class="<?= $fname . '_ssr' ; ?>">
                        <div style="display:inline-block; vertical-align:middle; margin-right:3px">
                          <input size="2" type="text" id="<?= $fname . '_not_' . $notkey ; ?>" class="<?= $fname . '_not' ; ?>" name="<?= $fname . '_ssord[]' ; ?>" value="" onchange="<?= $ong; ?>" autocomplete="off">
                          <input type="hidden" name="<?= $fname . '[]' ; ?>" value="<?= $notkey; ?>"> 
                        </div>
                        <div style="display:inline-block; vertical-align:middle">
                          <?
                          $url = isset($urls[$notkey]) && $urls[$notkey] ? clean_url($urls[$notkey]) : '';
                          if($url) {
                            ?>
                          <a target="popfile" href="<?= $url ?>">
                            <?
                          }
                          print '<span class="' . $fname . '_sst">' . $notval . '</span>';
                          if($url) {
                            ?>
                          </a>
                            <?
                          }
                          ?>
                        </div>
                      </div>
                    <?
                  }
                }
                if($nn) {
                  ?>
                    </div>
                  <?
                }
              }
              ?>
                  </div>
              <?
            }
          }
          else {
            if($chosen_inc) {
              $chs = 1 ;
              $sty = 'width:500px' ;
              $size = 1 ;
            }
            else {
              $chs = 0 ;
              $sty = 'min-width:200px;max-width:500px;' ;
              $size = 3 ;
              if($sqn = sq_num($c)) {
                $size = $sqn ;
              }
              elseif(isset($lookups[$fname]['sq_arr'])) {
                $size = count($lookups[$fname]['sq_arr']) ;
                if($size > 8) { $size = 8 ; }
              }
              if($size < 3) { $size = 3 ; }
              $size++;
            }
            $jrebutt = 'REFRESH';
            ?>
            <div style="display:inline-block; vertical-align:middle;">
              <select class="chosen-select" name="<?= $fname . '[]' ; ?>" id="<?= $fname ; ?>" multiple="multiple" onchange="<?= $ong; ?>" size="<?= $size ; ?>" style="<?= $sty ; ?>">
                <? if(! $chs) { ?>
                <option value="">** Select **</option>
                <?
                }
                if(isset($lookups[$fname]['sq_arr'])) {
                  foreach($lookups[$fname]['sq_arr'] as $key => $opt_arr) {
                    $showv = $val = $opt_arr[$c_row['sq_lookv']] ;
                    $dsp = $c_row['sq_lookd'] ;
                    if( substr_count($dsp,'k') || substr_count($dsp,'v') || substr_count($dsp,'[[') ) {
                      $showv = $dsp ;
                      $showv = rep_var($showv,$opt_arr);
                      if($showv == $dsp) {
                        $showv = str_replace('k',$key,$showv);
                        $showv = str_replace('v',$val,$showv);
                      }      
                    }
                    if($showv) {
                      $chk = join_multi($c,$go,$dval);
                      if(in_array($key, $chk)) { 
                        ?>
                <option value="<?= $key ; ?>" selected="selected"><?= cliptext(clean_amp($showv),80) ; ?></option>
                        <? } elseif(! sq_keys($c,'j')) { ?>
                <option value="<?= $key ; ?>"><?= cliptext(clean_amp($showv),80) ; ?></option>
                        <? } else { $jrebutt = 'LOAD All'; }
                    }
                  }
                }
                ?>
              </select> 
            </div>
            <? 
            if(! (isset($nojrelook) && $nojrelook)) { 
              idhide($fname . '_sqlookt',$c_row['sq_lookt']);
              idhide($fname . '_sqlookk',$c_row['sq_lookk']);
              idhide($fname . '_sqlookv',$c_row['sq_lookv']);
              idhide($fname . '_sqlookd',iencode($c_row['sq_lookd'],true));
              idhide($fname . '_sqlookl',str_replace('../','..|',$c_row['sq_lookl']));
              idhide($fname . '_sqlookf',$c_row['sq_lookf']);
              $jrelook = "jrelook('{$fname}'); $(this).html('REFRESH'); return false;"; 
              if(! sq_keys($c,'n')) {
                ?>
                <div style="display:inline-block; vertical-align:middle; padding-left:5px;" class="button">
                  <a href="#" onclick="<?= $jrelook ?>"><?= $jrebutt ?></a> 
                </div>
                <?
              }
            }
            debug_arr();
          }
        }
        elseif( sq_keys($c,'e') && sq_keys($c,'f') ) {
          if(substr_count($sq_keys[$c],'ff')) {
            $href = 'listfiles.php?fpath=' . str_replace('/','|',$fpath) . '&amp;fid=' . $fid . '&amp;filter=' . $filefilt ;
            $onc  = "listfiles('" . str_replace('/','|',$fpath) . "','" . '' . "','" . $fid . "','" . $filefilt . "');" ;
            ?>
            <div style="position:relative;">
                <div style="position:absolute; top:2px; right:22px; z-index:100; text-align:right;">
                  <span class="button"><a href="<?= $href ?>" onclick="<?= $onc . 'return false;' ; ?>" target="_blank">UPLOAD</a></span>
                </div>
            </div>
            <div style="min-height:60px; max-height:190px; overflow:auto; position:relative;">
            <?
            // multiple files
            if((!isset($files[$fpath])) && ($handle = opendir($fpath))) {
              $files[$fpath] = array();
              while(false !== ($file = readdir($handle))) {
                if( ($file == $dval) || ( (! in_array( $file, array('.','..','Thumbs.db') ) ) && istype($fpath,$file,'file') && ( (! $filefilt) || substr_count( strtolower($file), strtolower($filefilt) ) ) ) ) {
                  $files[$fpath][] = $file;
                }
              }
              closedir($handle);
              natcasesort($files[$fpath]);  
            }
            if(count($files[$fpath])) {
              $dvals = join_multi($c,$go,$dval);
              debug_arr();
              $sn = 0 ;
              // print_arv($files[$fpath]);
              // print_arv($dvals);
              foreach($files[$fpath] as $f) {
                if(in_array($f, $dvals)) {
                  if(! $sn++) {
                    ?>
                  <div style="margin-bottom:10px" id="<?= $fname . '_multsel' ; ?>">
                    <div style="margin-bottom:10px">
                      <u>Selected: Delete order number to unselect option</u> 
                    </div>
                    <?
                  }
                  ?>
                    <div style="margin-bottom:5px" class="<?= $fname . '_ssr' ; ?>">
                      <div style="display:inline-block; vertical-align:middle; margin-right:3px">
                        <input size="2" type="text" class="<?= $fname . '_sel' ; ?>" name="<?= $fname . '_ssord[]' ; ?>" value="<?= $sn; ?>" onchange="<?= $ong; ?>" autocomplete="off">
                        <input type="hidden" name="<?= $fname . '[]' ; ?>" value="<?= $f; ?>"> 
                      </div>
                      <div style="display:inline-block; vertical-align:middle">
                        <?= '<span class="' . $fname . '_sst">' . $f . '</span>'; ?>
                      </div>
                    </div>
                  <?
                }
              }
              if($sn) {
                ?>
                  </div>
                <?
              }
              $nn = 0 ;
              foreach($files[$fpath] as $f) {
                if(!in_array($f, $dvals)) {
                  if(! $nn++) {
                    ?>
                  <div id="<?= $fname . '_multnot' ; ?>">
                    <div style="margin-bottom:10px;">
                      <u>Not selected: Enter order number to select option</u> 
                    </div>
                    <?
                  }
                  ?>
                    <div style="margin-bottom:5px" class="<?= $fname . '_ssr' ; ?>">
                      <div style="display:inline-block; vertical-align:middle; margin-right:3px">
                        <input size="2" type="text" class="<?= $fname . '_not' ; ?>" name="<?= $fname . '_ssord[]' ; ?>" value="" onchange="<?= $ong; ?>" autocomplete="off">
                        <input type="hidden" name="<?= $fname . '[]' ; ?>" value="<?= $f; ?>"> 
                      </div>
                      <div style="display:inline-block; vertical-align:middle">
                        <?= '<span class="' . $fname . '_sst">' . $f . '</span>'; ?>
                      </div>
                    </div>
                  <?
                }
              }
              if($nn) {
                ?>
                  </div>
                <?
              }
            }
            ?>
            </div>
            <?
          }
          else {
            // file upload/selection
            $globvars['save']++; 
            if( isset($fpath) && $fpath && file_exists($fpath) && ( ( $action == 'edit' ) || ! $fprefpadd ) ) {
              if( (!sq_keys($c,'j')) && (!isset($files[$fpath])) && ($handle = opendir($fpath)) ) {
                $files[$fpath] = array();
                while(false !== ($file = readdir($handle))) {
                  if( ($file == $dval) || ( (! in_array( $file, array('.','..','Thumbs.db') ) ) && istype($fpath,$file,'file') && ( (! $filefilt) || substr_count( strtolower($file), strtolower($filefilt) ) ) ) ) {
                    $files[$fpath][] = $file;
                  }
                }
                closedir($handle);
                natcasesort($files[$fpath]);
              }
              $res = '';
              if($c_row['sq_fmake'] && ( $sqm = safe_explode( '-', $c_row['sq_fmake'] ) ) && ($sqm[3] == 'u') ) {
                $res['width'] = $sqm[1];
                $res['height'] = $sqm[2];
              }
              ?>
              <table border="0" cellpadding="1" cellspacing="0" class="tablen" summary=""> 
                <tbody>
                    <? if( (isset($files[$fpath]) && (count($files[$fpath]) > 0)) || sq_keys($c,'j') ) { 
                      $fid = str_replace('[','_',str_replace(']','',$fname)) ;
                      ?>
                  <tr> 
                    <td class="nobr">Server&nbsp;</td> 
                    <td class="nobr"> 
                      <select class="chosen-select" name="<?= $fname ; ?>" id="<?= $fid ; ?>" size="1" onchange="<?= $ong; ?>" style="max-width:560px;"> 
                        <option value="">** Select **</option> 
                          <?
                          $onc = '';
                          if(sq_keys($c,'j')) {
                            if($dval && file_exists(build_path($fpath,$dval)) ) {
                              ?>
                        <option value="<?= $dval ; ?>" selected="selected"><?= cliptext($dval,80) ; ?></option>
                              <?
                            }
                            $onc = "jselmore('" . str_replace('/','|',$fpath) . "','" . $dval . "','" . $fid . "','" . $filefilt . "');" ;
                            ?>
                        <option value="" onclick="<?= $onc . 'return false;' ?>">MORE...</option>
                            <?
                          }
                          else {
                            foreach($files[$fpath] as $file) {
                              if( in_array(pathinfo($file,PATHINFO_EXTENSION),$file_types) ) {
                                $rpath = str_replace('../','',$fpath);
                                $fdisp = $file ;
                                if(isset($globvars['db_medtable']) && $globvars['db_medtable'] && isset($media_fp["{$rpath}{$file}"]) && $media_id[$media_fp["{$rpath}{$file}"]]['note']) {
                                  $fdisp .= ' &nbsp; (' . $media_id[$media_fp["{$rpath}{$file}"]]['note'] . ')' ;
                                }
                                ?>
                        <option value="<?= optsel($file,$dval) ; ?>"><?= cliptext($fdisp,200) ; ?></option>
                                <?
                              }
                            }
                          }
                          $href = 'listfiles.php?fpath=' . str_replace('/','|',$fpath) . '&amp;file=' . $dval . '&amp;fid=' . $fid . '&amp;filter=' . $filefilt ;
                          $onc  = "listfiles('" . str_replace('/','|',$fpath) . "','" . $dval . "','" . $fid . "','" . $filefilt . "');" . $onc ;
                          ?>
                      </select> 
                      <span class="button"><a href="<?= $href ?>" onclick="<?= $onc . 'return false;' ; ?>" target="_blank">POP</a></span>
                    </td> 
                  </tr>
                      <?
                    }
                    ?>
                  <tr> 
                    <td class="nobr">Upload&nbsp;</td> 
                    <td colspan="2"> 
                      <div class="fileUpload">
                      <?
                      $nm = 'up_' . $fname ;
                      $id = 'id_' . $nm ;
                      $hd = 'hd_' . $nm ;
                      $bt = 'bt_' . $nm ;
                      $onc = "getId('{$hd}').value = getId('{$id}').value.split('\\\').pop(); onbrowse('{$fname}')";
                      $omo = "getId('{$bt}').setAttribute('class', 'button1')";
                      $omx = "getId('{$bt}').setAttribute('class', 'button')";
                      ?>
                        <input type="file" onchange="<?= $onc ; ?>" name="<?= $nm ; ?>" id="<?= $id ; ?>" class="browserHidden" onmouseover="<?= $omo ; ?>" onmouseout="<?= $omx ; ?>"> 
                        <div class="browserVisible">
                          <input type="text" class="input" id="<?= $hd ; ?>" style="width:110px;" autocomplete="off"> <span id="<?= $bt ?>" class="button"><a href="#">BROWSE</a></span> 
                        </div>
                      </div>
                    </td>
                      <? if($res) { ?>
                    <td>Image Resize Max&nbsp;</td> 
                    <td><input type="text" name="<?= 'up_width_' . $fname ; ?>" id="<?= 'up_width_' . $fname ; ?>" value="<?= $res['width'] ; ?>" size="3" maxlength="4" style="text-align:right;" autocomplete="off"></td> 
                    <td>W&nbsp;</td> 
                    <td><input type="text" name="<?= 'up_height_' . $fname ; ?>" id="<?= 'up_height_' . $fname ; ?>" value="<?= $res['height'] ; ?>" size="3" maxlength="4" style="text-align:right;" autocomplete="off"></td> 
                    <td>H</td>
                      <? } ?>
                  </tr> 
                </tbody> 
              </table><?
            }
            else {
              echo '#';
              $f_err = 1 ;
            }
          }
        }
        elseif( sq_keys($c,'e') && ( $ftype == 'text' ) ) {
          // textarea
          $globvars['save']++;            
          $textarows1 = $textarows ;
          if( (! sq_keys($c,'yw') ) && ($sqn = sq_num($c)) ) {
            $textarows1 = $sqn ;
          }
          $maxlen = sq_num($c,'_');
          if(sq_keys($c,'yw')) {
            ?>
            <div style="font-family:Arial; font-size:11px; padding:0 1px 5px 1px; color:#C00000">COPY TEXT TO NOTEPAD FIRST TO REMOVE ALL HIDDEN FORMATTING THEN COPY AGAIN FROM THERE BEFORE PASTING HERE</div>
            <?
          }
          ?>
          <div class="tablen">
            <textarea maxlength="<?= $maxlen && ! sq_keys($c,'yw') ? $maxlen : '' ; ?>" class="<?= sq_keys($c,'yw') ? 'ckeditor' : '' ; ?>" name="<?= $fname ; ?>" id="<?= $fname ; ?>" rows="<?= $textarows1 ; ?>" cols="<?= $textacols ; ?>" onchange="<?= $ong; ?>"><?= $dval ; ?></textarea> 
            <?
            if(sq_keys($c,'yw')) {
              ckeditor_js($fname); 
            }
            ?>
          </div>
          <?
        }
        elseif( sq_keys($c,'e') && ( $ftype == 'enum' ) && $fprms ) {
          $globvars['save']++;        
          // enum
          if($fprms == "'','y'") { // checkbox  
            ?>
            <label class="checklabel"><input type="checkbox" name="<?= $fname ; ?>" value="<?= optchk('y',$dval) ; ?>" onchange="<?= $ong; ?>"><span class="checkcust"></span></label>
            <? 
          } 
          else { 
            // radio
            $t = 0 ;
            $eopta = safe_explode(',', str_replace("'",'',$fprms)) ;
            foreach($eopta as $eopt) {
              $eoptv = $eopt ? $eopt : 'N/A';
              if( ( ! ($dval || $t) ) || ($dval == $eopt) ){ ?>
                <label class="radiolabel"><input type="radio" name="<?= $fname ; ?>" value="<?= $eopt ; ?>" checked="CHECKED" onchange="<?= $ong; ?>"><?= $eoptv ?><span class="radiocust"></span></label>
                <? 
              }
              else { ?>
                <label class="radiolabel"><input type="radio" name="<?= $fname ; ?>" value="<?= $eopt ; ?>" onchange="<?= $ong; ?>"><?= $eoptv ?><span class="radiocust"></span></label>
                <? 
              } $t++ ;
            }            
          }
        }
        elseif( sq_keys($c,'e') || ( ( $action == 'add' ) && sq_keys($c,'k') ) ) {
          // input box
          $globvars['save']++;        
          $calf = $calj = '';
          if($ftype == 'datetime') { 
            $cald = str_replace('d','dd',str_replace('m','mm',str_replace('Y','yyyy',$dformat))) . " hh:mm:ss";
            $calf = str_replace('d','%d',str_replace('m','%m',str_replace('Y','%Y',$dformat))) . " %H:%M:00";
            $calj = str_replace('d','dd',str_replace('m','mm',str_replace('Y','yy',$dformat)));
            $calt = true;
            $dval = cdate($dval,"$dformat H:i:s",'');
          }
          elseif($ftype == 'date') { 
            $cald = str_replace('d','dd',str_replace('m','mm',str_replace('Y','yyyy',$dformat)));
            $calf = str_replace('d','%d',str_replace('m','%m',str_replace('Y','%Y',$dformat)));
            $calj = str_replace('d','dd',str_replace('m','mm',str_replace('Y','yy',$dformat)));
            $calt = false;
            $dval = cdate($dval,"$dformat",'');
          }
          elseif($ftype == 'time') { 
            $cald = "hh:mm:ss";
            $dval = ctime($dval,"H:i:s",'');
          }
          if(($mlenp = sq_num($c,'_')) && ($mlenp < $mlen)) {
            $mlen = $mlenp;
          }
          if(sq_keys($c,'m')) {
            if($action == 'add') {
              ?>
              <input type="text" name="<?= $fname ; ?>" id="<?= 'id_'.$fname ; ?>" size="20" maxlength="<?= $mlen ; ?>" onchange="<?= $ong; ?>" value="" autocomplete="off">
              <?
            }
            else {
              ?>
              <input type="text" name="<?= $fname ; ?>" id="<?= 'id_'.$fname ; ?>" size="20" maxlength="<?= $mlen ; ?>" onchange="<?= $ong; ?>" value="" autocomplete="new-passworrd" disabled="disabled"> &nbsp; <span class="button" style="font-size-adjust:0.45"><a href="#" onclick="$('<?= '#id_' . $fname ; ?>').prop('disabled', false); $('<?='#id_' . $fname ; ?>').val(''); return false">CHANGE</a></span>
              <?
            }
          }
          else {
            ?>
             <input type="text" name="<?= $fname ; ?>" id="<?= 'id_'.$fname ; ?>" size="<?= $flen ; ?>" maxlength="<?= $mlen ; ?>" value="<?= $dval ; ?>" onchange="<?= $ong; ?>" autocomplete="off"> 
            <?
          }
          if($calj && file_exists('../scripts/calendar.inc.php')) {
            ?>
            <span class="small"><?= $cald ; ?></span> 
            <script type="text/javascript">
              $(function($) {
                $("#<?= 'id_'.$fname ; ?>").<?= $calt ? 'datetimepicker' : 'datepicker' ?>({
                  firstDay: 1,
                  dateFormat: '<?= $calj ?>', 
                  timeFormat: "HH:mm:ss",
                  showSecond:false
                });
              });
            </script>
            <?
          }
          elseif($calf && file_exists('../scripts/jscalendar.inc.php')) { ?>
            <img src="../scripts/jscalendar/cal.gif" id="<?= 'bt_'.$fname ; ?>" style="cursor:pointer; vertical-align:bottom;" title="Date selector" alt="Date selector" width="16" height="16"> &nbsp; &nbsp; <span class="small"><?= $cald ; ?></span> 
            <script type="text/javascript">
              Calendar.setup({
                  inputField     :    "<?= 'id_'.$fname ; ?>",
                  ifFormat       :    "<?= $calf ; ?>",
                  button         :    "<?= 'bt_'.$fname ; ?>",
                  showsTime      :    "<?= $calt ; ?>"
              });
            </script>
            <? 
          } 
        }
        elseif( sq_keys($c,'v') ) {
          if( sq_keys($c,'o') ) {
            // single options
            $def = isset($sq_deflt[$c]) && $sq_deflt[$c] && isset($lookups[$fname]['sq_arr'][$sq_deflt[$c]][$sq_lookv[$c]]) ? $lookups[$fname]['sq_arr'][$sq_deflt[$c]][$sq_lookv[$c]] : '';
            $dvali = isset($i_row[$fname]) && $i_row[$fname] ? $i_row[$fname] : $def ;
            $dval = join_multi($c,$go,$dvali);
            $dval = (is_array($dval) && count($dval) && $dval[0]) ? $dval[0] : $dvali ;
            $showv = $dval ? clean_amp($dval) : '' ;
            if(isset($lookups[$fname]['sq_arr'][$dval])) {
              $opt_arr = $lookups[$fname]['sq_arr'][$dval] ;
              $key = $opt_arr[$c_row['sq_lookk']] ;
              $showv = $val = $opt_arr[$c_row['sq_lookv']] ;
              $dsp = $c_row['sq_lookd'] ;
              if( substr_count($dsp,'k') || substr_count($dsp,'v') || substr_count($dsp,'[[') ) {
                $showv = $dsp ;
                $showv = rep_var($showv,$opt_arr);
                if($showv == $dsp) {
                  $showv = str_replace('k',$key,$showv);
                  $showv = str_replace('v',$val,$showv);
                }
              }
            }
            if( $gdt = dformat($showv,$c_row['ftype'],'vw') ) {
              echo $gdt ;
            }
            else {
              echo clean_amp($showv) ;
            }
          }
          elseif( sq_keys($c,'s') ) {
            // multiple options
            if(isset($lookups[$fname]['sq_arr'])) {
              $def = isset($sq_deflt[$c]) && $sq_deflt[$c] && isset($lookups[$fname]['sq_arr'][$sq_deflt[$c]][$sq_lookv[$c]]) ? $lookups[$fname]['sq_arr'][$sq_deflt[$c]][$sq_lookv[$c]] : '';
              $vals = isset($i_row[$fname]) && $i_row[$fname] ? $i_row[$fname] : $def ;
              $vals = join_multi($c,$go,$vals);
              debug_arr();
              foreach($vals as $tval) {
                if(isset($lookups[$fname]['sq_arr'][$tval])) {
                  $opt_arr = $lookups[$fname]['sq_arr'][$tval] ;
                  $key = $opt_arr[$c_row['sq_lookk']] ;
                  $showv = $val = $opt_arr[$c_row['sq_lookv']] ;
                  $dsp = $c_row['sq_lookd'] ;
                  if( substr_count($dsp,'k') || substr_count($dsp,'v') || substr_count($dsp,'[[') ) {
                    $showv = $dsp ;
                    $showv = rep_var($showv,$opt_arr);
                    if($showv == $dsp) {
                      $showv = str_replace('k',$key,$showv);
                      $showv = str_replace('v',$val,$showv);
                    }
                  }
                  if( $gdt = dformat($showv,$c_row['ftype'],'vw') ) {
                    echo $gdt . '<br>' ;
                  }
                  else {
                    echo clean_amp($showv) . '<br>' ;
                  }
                }
              }
            }
            else {
              echo clean_amp($showv) ;
            }
          }
          elseif(isset($i_row[$fname]) && $i_row[$fname]) {
            if($gdt = dformat($i_row[$fname],$ftype,'vw') ) {
              echo $gdt ;
            }
            elseif( (substr($i_row[$fname],0,1) == '#') && (strlen($i_row[$fname]) == 7 ) ) {
              echo '<span style="color:' . $i_row[$fname] . '">' . $i_row[$fname] . '</span>' ;
            }
            else {
              echo disp($i_row[$fname]);
            }
            if(! sq_keys($c,'h')) {
              ihide($fname,$i_row[$fname]);
            }
          }
        }
      }
      ?>
              </td> 
              <td width="<?= isvar($formrghtc) ? $formrghtc : ''; ?>" class="th button">
                  <?
                  if(isvar($c_row['sq_notei']) && file_exists($ppath = 'popups/' . $c_row['sq_notei']) && isimg($ppath) && $gis = get_image($ppath)) {
                    $id = 'np_' . $fname ;
                    $mw = 500 ;
                    $dw = $gis['width'] > $mw ? floor($gis['width'] * $mw / $gis['width']) : $gis['width'] ;
                    $dh = $gis['width'] > $mw ? floor($gis['height'] * $mw / $gis['width']) : $gis['height'] ;
                    $dx = 0 - $dw - 50 ;
                    $dy = ceil( $dh / 2 ) ;
                    $omo = "ShowContent('{$id}',{$dx},{$dy}); return false;";
                    $omx = "HideContent('{$id}'); return false;";
                    $onc = "window.open('{$gis['src']}','notepop'); return false;";
                    ?>
                <span><a onmousemove="<?= $omo ; ?>" onmouseover="<?= $omo ; ?>"
                onmouseout="<?= $omx ; ?>" onclick="<?= $onc ; ?>" href="#">?</a></span> 
                <div id="<?= $id ; ?>"
                style="display:none; position:absolute; border: solid 1px black; background-color: white; padding:5px; z-index:999;">
                  <img alt="" border="0" src="<?= $gis['src'] ; ?>" height="<?= $dh ; ?>"
                  width="<?= $dw ; ?>"> 
                </div>
                    <?
                  }
                  if( isvar($c_row['sq_notes']) ) { 
                    if(substr_count(strtolower($c_row['sq_notes']),'</a>') && substr_count($c_row['sq_notes'],'&raquo;')) {
                      $c_row['sq_notes'] = str_replace('&raquo;','',$c_row['sq_notes']);
                    }
                    $cfn = '';
                    if(substr_count($c_row['sq_notes'],'<') == 0 && substr_count($c_row['sq_notes'],'(') == 1 && substr_count($c_row['sq_notes'],')') == 1) {
                      if(($b2 = strpos($c_row['sq_notes'],')')) > ($b1 = strpos($c_row['sq_notes'],'('))) {
                        if(function_exists($cfn = substr($c_row['sq_notes'],0,$b1))) {
                          $ifs = substr($c_row['sq_notes'],$b1+1,$b2-$b1-1);
                          $ify = array();
                          if(is_array($ifa = safe_explode(',',$ifs))) {
                            foreach($ifa as $ifv) {
                              $ifa = rep_var($ifv, $i_row) ;
                              if( ( substr($ifa,0,1) == "'" && substr($ifa,-1) == "'" ) || ( substr($ifa,0,1) == '"' && substr($ifa,-1) == '"' ) ) {
                                $ifa = substr($ifa,1,-1);
                              }
                              $ify[] = $ifa ;
                            }
                          }
                          $dval = $fname && isset($i_row[$fname]) ? $i_row[$fname] : '';
                          if(! is_array($i_row)) {
                            $i_row = [];
                            foreach($globvars['fields'] as $f) {
                              $i_row[$f] = '';
                            }
                          }
                          globvadd( 
                            'c_row', $c_row,
                            'i_row', $i_row,
                            'c', $c,
                            's', $c,
                            'thiscol', $fname,
                            'fname', $fname,
                            'fnamev', isvar($globvars[$fname]),
                            'ftype', $c_row['ftype'],
                            'fpath', getfpath($c_row['sq_fpath']),
                            'fprms', $c_row['fprms'],
                            'dval', $dval);
                          call_user_func_array($cfn,$ify);
                        }
                        else { $cfn = ''; }
                      }
                    }
                    if(! $cfn) {
                      $chk = join_multi($c,$go,$i_row);
                      if(is_array($chk) && count($chk) && (serialize($chk) != serialize($i_row))) {
                        if(isset($chk[$fname]) && $chk[$fname]) {
                          $i_row[$fname] = $chk[$fname];
                          if($sq_joinv[$c]) {
                            $i_row[$sq_joinv[$c]] = $chk[$fname];
                          }
                        }
                        elseif(isset($chk[0]) && $chk[0]) {
                          $i_row[$fname] = $chk[0];
                          if($sq_joinv[$c]) {
                            $i_row[$sq_joinv[$c]] = $chk[0];
                          }
                        }
                      }
                      $notes = rep_var($c_row['sq_notes'], $i_row);
                      $notel = strtolower($notes);
                      if(substr_count($notel,'<a') && substr_count($notel,'href="') && ! (substr_count($notel,'<span') || substr_count($notel,'<div'))) {
                        $notes = '<div class="buttonr">' . $notes . '</div>' ;
                      }
                      echo $notes ;
                    }
                  } 
                  else { 
                    echo '&nbsp;' ; 
                  }
                  ?></td> 
            </tr>
      <?
      }
    }
  }
?>
          </table>
              <?
              if( ($action == 'edit') && $go && isset($stack) && is_array($stack) ) {
                if(function_exists('bespoke_stack')) {
                  bespoke_stack();
                }
                else {
                  form_stack(); 
                }
              }
              if(function_exists('form_foot')) {
                globvadd('i_row',$i_row);
                form_foot(); 
              } 
              ?>
        </td> 
      </tr> 
      <tr valign="top"> 
        <td valign="top" align="center"> <br> 
          <table summary="" border="0" cellpadding="0" cellspacing="0" width="100%" class="tablen"> 
            <tr> 
              <td valign="bottom" width="200"></td> 
              <td valign="bottom" align="center">
                  <? if($globvars['save']) { ?>
                <input type="submit" name="Submit" id="Submit" value="SAVE" class="submit">
                <input type="hidden" name="done" id="done" value="1">
                <input type="hidden" name="action" id="action" value="<?= $action ; ?>">
                <input type="hidden" name="start" id="start" value="<?= $start ; ?>">
                <input type="hidden" name="filter" id="filter" value="<?= filter_str($filter) ; ?>">
                <input type="hidden" name="search" id="search" value="<?= $search ; ?>">
                <input type="hidden" name="rngfr" id="rngfr" value="<?= $rngfr ; ?>">
                <input type="hidden" name="rngto" id="rngto" value="<?= $rngto ; ?>">
                <input type="hidden" name="sort" id="sort" value="<?= $sort ; ?>">
                <input type="hidden" name="vars" id="vars" value="<?= $vars ; ?>">
                  <? } ?>
              </td> 
              <td valign="bottom" width="200" style="font-size-adjust:0.45" class="button" align="right">
                  <? if( $allowdel && ($action=='edit')) { ?>
                <a style="font-size:8px;" onclick="return confirm('ARE YOU SURE YOU WANT TO DELETE THIS ENTIRE RECORD?')" href="<?= linkvars($sort,$start,'delete','',$go) ; ?>" title="Delete Record"><span style="">DELETE RECORD</span></a>
                  <? } ?>
              </td> 
            </tr> 
          </table> </td> 
      </tr> 
    </table> 
  </form>
  <br>
      <?
  if(function_exists('edit_foot')) {
    globvadd('i_row',$i_row);
    edit_foot(); 
  } 
  if($action == 'edit' && isset($globvars['sfield'])) {
      ?>
  <form name="sform" method="post" action="<?= $php_self ; ?>" autocomplete="off">
    <input type="hidden" name="action" value="add"> 
    <input type="hidden" name="addsim" value="<?= $go ?>"> 
    <input type="hidden" name="start" value="<?= $start ; ?>"> 
    <input type="hidden" name="filter" value="<?= filter_str($filter) ; ?>">
    <input type="hidden" name="search" value="<?= $search ; ?>">
    <input type="hidden" name="rngfr" value="<?= $rngfr ; ?>"> 
    <input type="hidden" name="rngto" value="<?= $rngto ; ?>"> 
    <input type="hidden" name="sort" value="<?= $sort ; ?>">
    <input type="hidden" name="vars" value="<?= $vars ; ?>">  
    <? 
    foreach($globvars['sfield'] as $sf1 => $sf2 ) { 
      ihide("sform[{$sf1}]",$sf2) ; 
    } 
    ?>
  </form>
      <?
   }
   if(function_exists('color_widget')) { color_widget(); }
   if($f_err) { print_p('# File folder does not exist.'); }
   if($tn) {
      ?>
  <h2>Text formatting *</h2> 
  <table border="0" cellpadding="4" cellspacing="0" class="tabler" summary="" width="100%"> 
    <tr> 
      <td class="th" width="<?= isvar($formleftc) ? $formleftc : ''; ?>"><b>[b]text[/b]</b></td> 
      <td><?= disp('[b]text[/b]') ; ?> (bold)</td> 
    </tr> 
    <tr> 
      <td class="th"><b>[u]text[/u]</b></td> 
      <td><?= disp('[u]text[/u]') ; ?> (underline)</td> 
    </tr> 
    <tr> 
      <td class="th"><b>[i]text[/i]</b></td> 
      <td><?= disp('[i]text[/i]') ; ?> (italics)</td> 
    </tr> 
    <tr> 
      <td class="th"><b>[o]text</b></td> 
      <td><?= disp('[o]text') ; ?> (bullet)</td> 
    </tr> 
    <tr> 
      <td class="th"><b>[link:url|text]</b></td> 
      <td>eg. [link:<?= $php_self ; ?>|internal] = <?= disp('[link:' . $php_self . '|internal]') ; ?><br>
        OR [link:https://www.wotnot.co.uk|external] = <?= disp('[link:https://www.wotnot.co.uk|external]') ; ?></td>
      
    </tr> 
    <tr> 
      <td class="th"><b>[email:email|text|subj]</b></td> 
      <td>eg. [mail:mail@wotnot.co.uk|send email|test] = <?= disp('[mail:mail@wotnot.co.uk|send email|test]') ; ?><br>
        OR [mail:mail@wotnot.co.uk] = <?= disp('[mail:mail@wotnot.co.uk]') ; ?>
      </td> 
    </tr> 
  </table>
      <?
    } 
}
?></td> 
</tr> 
</table>
<? /* ?>
<form>
<?
*/

function form_stack() {
  // copy function to bespoke_stack() to make bespoke version
  global $globvars; extract($globvars);
  $stack_arr = $stack_ord = $entry = $update = $insert = array();
  $prefix = str_replace('_main','_',$sq_table);
  if(isset($globvars['sfpath'])) {
    $filepath = build_path($filepath, $globvars['sfpath']) ;
  }
  // existing entries 
  if($done && isset($bd) && is_array($bd)) {
    // print_arr($_FILES);
    if(isset($_FILES['bd']['name'])) {
      foreach($_FILES['bd']['name'] as $fkey => $fname) {
        if($fnew = upload_file($_FILES['bd']['tmp_name'][$fkey] , $filepath , $fname)) {
          $fimg = str_replace(array('_upimage','_upfile'),array('_image','_file'),$fkey);
          $bd[$fimg] = $fnew ;
        }
      }
    }
    // print_arr($bd,'bd');
    foreach($bd as $key => $val) {
      $entry = safe_explode( "_", $key );
      $table = $entry[0];
      $id = $entry[1];
      $field = $entry[2];
      if($field == 'delete' && $val == 'y') {
        $string = "DELETE FROM `{$prefix}{$table}` WHERE `id` = '$id' LIMIT 1";
        // print_p($string); 
        my_query($string);                   
      }
      else {
        $update[$table][$id][$field] = $val ;
      }
    }
    // print_arr($update,'update');
    foreach($update as $table => $tarr) {
      foreach($tarr as $id => $iarr) {
        $string = "UPDATE `{$prefix}{$table}` SET ";
        foreach($iarr as $field => $val) {
          $string .= " `$field` = '$val', ";
        }
        $string = substr( $string, 0, -2 );
        $string .= " WHERE `id` = '$id' LIMIT 1";
        // print_p($string);
        my_query($string);
        logtable('UPDATE',$cntrl_user,"{$prefix}{$table}",$string);
        if($debug) { print_d($string,__LINE__,__FILE__); }
      }
    }
  }
  
  // new entries
  if($done && isset($nd) && is_array($nd)) {
    if(isset($_FILES['nd']['name'])) {
      foreach($_FILES['nd']['name'] as $fkey => $fname) {
        if($fnew = upload_file($_FILES['nd']['tmp_name'][$fkey] , $filepath , $fname)) {
          $fimg = str_replace(array('_upimage','_upfile'),array('_image','_file'),$fkey);
          $nd[$fimg] = $fnew ;
        }
      }
    }
    // print_arr($nd,'nd');
    foreach($nd as $key => $val) {
      if($val) {
        $entry = safe_explode( "_", $key );
        $table = $entry[0];
        $field = $entry[1];
        $insert[$table][$field] = $val ;
      }
    }
    // print_arr($insert,'insert');
    foreach($insert as $table => $tarr) {
      $string = "INSERT INTO `{$prefix}{$table}` SET `m_id` = '$go', ";
      foreach($tarr as $field => $val) {
        $string .= " `$field` = '$val', ";
      }
      $string = substr( $string, 0, -2 );
      // print_p($string);
      my_query($string);
      logtable('INSERT',$cntrl_user,"{$prefix}{$table}",$string);
      if($debug) { print_d($string,__LINE__,__FILE__); }
    }
  }
  
  // read all entries
  foreach($stack as $tn => $table) {
    $sfields[$table] = my_fields("{$prefix}{$table}",MYSQL_ASSOC);
    $string = "SELECT * FROM `{$prefix}{$table}` WHERE `m_id` = '$go'";
    $query = my_query($string);
    while($a_row = my_array($query)) {
      $a_row['table'] = $table;
      $stack_arr[] = $a_row;
      $stack_ord[] = $a_row['order'];
      $sqm = [];
      if(isset($a_row['image']) && isset($globvars['smake'])) {
        if(is_array($globvars['smake']) && isset($globvars['smake'][$tn])) {
          $sqm = safe_explode('-',$globvars['smake'][$tn]);
        }
        else {
          $sqm = safe_explode('-',$globvars['smake']);
        }
      }
      if(count($sqm)) {
        $img = build_path($filepath,$a_row['image']) ;
        if(file_exists($img) && ($gis = get_image($img))) {
          // resize images
          if($debug) { print_arr($sqm,'Stack (' . $a_row['order'] . ')'); }
          if($gis['width'] > $sqm[0] || $gis['height'] > $sqm[1]) {
            if($debug) { print_d("Resize {$a_row['image']}",__LINE__,__FILE__); }
            make_image($filepath,$a_row['image'],$filepath,'',$sqm[0],$sqm[1],$sqm[2],$sqm[3],1) ;
          }
        }
      }
    }
  }
  array_multisort($stack_ord,$stack_arr);

  // get files list
  $files = array();
  if(isset($filepath) && $filepath && file_exists($filepath) && $handle = opendir($filepath)) {
    while(false !== ($file = readdir($handle))) {
      if( (! in_array( $file, array('.','..','Thumbs.db') ) ) && istype($filepath,$file,'file') && ( (! $filefilt) || substr_count( strtolower($file), strtolower($filefilt) ) ) ) {
        $files[] = $file;
      }
    }
    if(count($files)>0) {
      natcasesort($files);
    }
    closedir($handle);
  }
  ?>
<div style="width:<?= $formwidth; ?>px;">
 <? if(count($stack_arr)) { ?>
<br> <br> 
<table summary="" border="0" cellpadding="0" cellspacing="0" width="100%"> 
  <tr valign="top"> 
    <td width="400"><br> <br>
      <h2>EXISTING ENTRIES</h2></td> 
    <td align="center"> <input type="submit" name="Submit1" value="SAVE" class="submit"></td> 
    <td width="400"></td> 
  </tr> 
</table> <br> 
<table summary="" border="0" cellpadding="4" cellspacing="0" width="100%" class="tableb"> 
  <tbody>
    <tr class="th"> 
      <td style="padding:10px 4px"><b>ORDER</b></td> 
      <td><b>TYPE</b></td> 
      <td colspan="2"><b>CONTENT</b></td> 
      <td align="center"><b>DELETE</b></td> 
    </tr>
  <? 
  foreach($stack_arr as $row) { 
    $table = $row['table'] ;
    ?>
    <tr> 
      <td colspan="4" style="height:14px;border-right:none;border-left:none;"></td> 
    </tr>
    <?
    $bgs = 'background-color:#EEEEEE';
    if($table == 'head' ) { ?>
    <tr style="<?= $bgs ?>"> 
      <td><input type="text" name="<?= 'bd[' . $table . '_' . $row['id'] . '_order]' ; ?>" size="3" value="<?= $row['order'] ?>"></td> 
      <td><b><?= strtoupper($table) ?></b></td> 
      <td colspan="2">
        <input type="text" name="<?= 'bd[' . $table . '_' . $row['id'] . '_text]' ; ?>" size="<?= $sfields[$table]['text']['Fprms'] > $globvars['maxbox'] ? $globvars['maxbox'] : $sfields[$table]['text']['Fprms'] ; ?>" maxlength="<?= $sfields[$table]['text']['Fprms']; ?>" value="<?= $row['text'] ?>">
      </td>
      <td align="center"><label class="checklabel"><input type="checkbox" name="<?= 'bd[' . $table . '_' . $row['id'] . '_delete]' ; ?>" value="y"><span class="checkcust"></span></label></td> 
    </tr> 
    <? } elseif($table == 'image' || $table == 'file') { ?>
    <tr style="<?= $bgs ?>"> 
      <td><input type="text" name="<?= 'bd[' . $table . '_' . $row['id'] . '_order]' ; ?>" value="<?= $row['order'] ?>" size="3"></td> 
      <td>
        <?
        if($table == 'image') {
          $img = build_path($filepath,$row['image']) ;
        }
        if($table == 'image' && $row['image'] && $gis = get_image($img)) {
          $id = 'id_' . $row['id'] ;
          $mw = 500 ;
          $dw = $gis['width'] > $mw ? floor($gis['width'] * $mw / $gis['width']) : $gis['width'] ;
          $dh = $gis['width'] > $mw ? floor($gis['height'] * $mw / $gis['width']) : $gis['height'] ;
          $dx = 50 ;
          $dy = ceil( $dh / 2 ) ;
          $omo = "ShowContent('{$id}',{$dx},{$dy}); return false;";
          $omx = "HideContent('{$id}'); return false;";
          $onc = "window.open('{$gis['src']}','notepop'); return false;";
          ?>
        <div class="button">
          <a title="<?= $gis['width'] . ' x ' . $gis['height']; ?>" onmousemove="<?= $omo ; ?>" onmouseover="<?= $omo ; ?>" onmouseout="<?= $omx ; ?>" onclick="<?= $onc ; ?>" href="#"><?= $gis['width'] . ' x ' . $gis['height']; ?></a>
        </div>
        <div id="<?= $id ; ?>" style="display:none; position:absolute; border: solid 1px black; background-color: white; padding:5px; z-index:999; text-align:center;">
          <img alt="" border="0" src="<?= $gis['src'] . '?' . $gis['filemtime'] ; ?>" height="<?= $dh ; ?>" width="<?= $dw ; ?>"> 
        </div>
          <?
        }
        else {
          ?>
          <b><?= strtoupper($table) ?></b>
          <?
        }
        ?>
      </td> 
      <td> 
        <table summary="" border="0" cellpadding="2" cellspacing="0" class="tablen"> 
          <tbody>
          <?
          if(count($files)>0) {
            $fname = ($table == 'image') ? 'bd[' . $table . '_' . $row['id'] . '_image]' : 'bd[' . $table . '_' . $row['id'] . '_file]' ;
            $fid = str_replace('[','_',str_replace(']','',$fname)) ;
            $dval = ($table == 'image') ? $row['image'] : $row['file'] ;
            $href = 'listfiles.php?fpath=' . str_replace('/','|',$filepath) . '&amp;file=' . $dval . '&amp;fid=' . $fid . '&amp;filter=' . $filefilt ;
            $onc = "listfiles('" . str_replace('/','|',$filepath) . "','" . $dval . "','" . $fid . "','" . $filefilt . "'); return false;" ;
            ?>
            <tr> 
              <td width="50">Server</td> 
              <td class="nobr" colspan="2"> 
                <span style="display:inline-block;background-color:white;width:220px;"><select class="chosen-select" name="<?= $fname ; ?>" id="<?= $fid ; ?>" size="1"> 
                  <option value="">** Select **</option> 
                  <?
                  foreach($files as $file) {
                    if(istype($filepath,$file,'file')) {
                      if($file == $dval) {
                        ?>
                  <option value="<?= $file ; ?>" selected="selected"><?= cliptext($file,'60','...') ; ?></option>
                        <?
                      }
                      else {
                        ?>
                  <option value="<?= $file ; ?>"><?= cliptext($file,'60','...') ; ?></option>
                        <?
                      }
                    }
                  }
                  ?>
                </select></span> <span class="button"><a href="<?= $href ?>" onclick="<?= $onc . 'return false;' ; ?>" target="_blank">POP</a></span></td> 
            </tr>
            <? } ?>
            <tr> 
              <td width="50">Upload</td> 
              <td colspan="2"> 
                <div class="fileUpload">
                  <?
                  $nm = ($table == 'image') ? 'bd[' . $table . '_' . $row['id'] . '_upimage]' : 'bd[' . $table . '_' . $row['id'] . '_upfile]';
                  $fname = str_replace('[','_',str_replace(']','',$nm)) ;
                  $id = 'id_' . $fname ;
                  $hd = 'hd_' . $fname ;
                  $bt = 'bt_' . $fname ;
                  $onc = "getId('{$hd}').value = getId('{$id}').value.split('\\\').pop(); onbrowse('{$fname}')";
                  $omo = "getId('{$bt}').setAttribute('class', 'button1')";
                  $omx = "getId('{$bt}').setAttribute('class', 'button')";
                  ?>
                  <input type="file" onchange="<?= $onc ; ?>" name="<?= $nm ; ?>" id="<?= $id ; ?>" class="browserHidden" onmouseover="<?= $omo ; ?>" onmouseout="<?= $omx ; ?>"> 
                  <div class="browserVisible">
                    <input type="text" class="input" id="<?= $hd ; ?>" style="width:110px;" autocomplete="off"> <span id="<?= $bt ?>" class="button"><a href="#">BROWSE</a></span> 
                  </div>
                </div>
              </td> 
            </tr> 
          </tbody> 
        </table>
      </td> 
      <td> 
        <table summary="" border="0" cellpadding="1" cellspacing="0" class="tablen"> 
          <? if($table == 'image') { ?>
          <tr> 
            <td width="50">Link</td> 
            <td width="260">
              <input type="text" name="<?= 'bd[' . $table . '_' . $row['id'] . '_link]' ; ?>" size="<?= $sfields[$table]['link']['Fprms']; ?>" maxlength="<?= $sfields[$table]['link']['Fprms']; ?>" value="<?= $row['link'] ?>" style="width:260px;">
            </td> 
          </tr> 
          <tr> 
            <td width="50">Caption</td> 
            <td width="260">
              <input type="text" name="<?= 'bd[' . $table . '_' . $row['id'] . '_caption]' ; ?>" size="<?= $sfields[$table]['caption']['Fprms']; ?>" maxlength="<?= $sfields[$table]['caption']['Fprms']; ?>" value="<?= $row['caption'] ?>" style="width:260px;">
            </td> 
          </tr> 
          <? } else { ?>
          <tr> 
            <td width="50">Text</td> 
            <td width="260">
              <input type="text" name="<?= 'bd[' . $table . '_' . $row['id'] . '_text]' ; ?>" size="<?= $sfields[$table]['text']['Fprms']; ?>" maxlength="<?= $sfields[$table]['text']['Fprms']; ?>" value="<?= $row['text'] ?>" style="width:260px;">
            </td> 
          </tr> 
          <? } ?>
        </table> </td> 
      <td align="center"><label class="checklabel"><input type="checkbox" name="<?= 'bd[' . $table . '_' . $row['id'] . '_delete]' ; ?>" value="y"><span class="checkcust"></span></label></td> 
    </tr> 
    <? } elseif($table == 'twocols') { ?>
    <tr style="<?= $bgs ?>"> 
      <td rowspan="2"><input type="text" name="<?= 'bd[' . $table . '_' . $row['id'] . '_order]' ; ?>" value="<?= $row['order'] ?>" size="3"></td> 
      <td>
        <?
        $img = build_path($filepath,$row['image']) ;
        if($row['image'] && $gis = get_image($img)) {
          $id = 'id_' . $row['id'] ;
          $mw = 500 ;
          $dw = $gis['width'] > $mw ? floor($gis['width'] * $mw / $gis['width']) : $gis['width'] ;
          $dh = $gis['width'] > $mw ? floor($gis['height'] * $mw / $gis['width']) : $gis['height'] ;
          $dx = 50 ;
          $dy = ceil( $dh / 2 ) ;
          $omo = "ShowContent('{$id}',{$dx},{$dy}); return false;";
          $omx = "HideContent('{$id}'); return false;";
          $onc = "window.open('{$gis['src']}','notepop'); return false;";
          ?>
        <div class="button">
          <a title="<?= $gis['width'] . ' x ' . $gis['height']; ?>" onmousemove="<?= $omo ; ?>" onmouseover="<?= $omo ; ?>" onmouseout="<?= $omx ; ?>" onclick="<?= $onc ; ?>" href="#"><?= $gis['width'] . ' x ' . $gis['height']; ?></a>
        </div>
        <div id="<?= $id ; ?>" style="display:none; position:absolute; border: solid 1px black; background-color: white; padding:5px; z-index:999; text-align:center;">
          <img alt="" border="0" src="<?= $gis['src'] . '?' . $gis['filemtime'] ; ?>" height="<?= $dh ; ?>" width="<?= $dw ; ?>"> 
        </div>
          <?
        }
        else {
          ?>
          <b><?= strtoupper($table) ?></b>
          <?
        }
        ?>
      </td> 
      <td> 
        <table summary="" border="0" cellpadding="1" cellspacing="0" class="tablen"> 
          <tbody>
          <?
          if(count($files)>0) {
            $fname = 'bd[' . $table . '_' . $row['id'] . '_image]' ;
            $fid = str_replace('[','_',str_replace(']','',$fname)) ;
            $dval = $row['image'];
            $href = 'listfiles.php?fpath=' . str_replace('/','|',$filepath) . '&amp;file=' . $dval . '&amp;fid=' . $fid . '&amp;filter=' . $filefilt ;
            $onc = "listfiles('" . str_replace('/','|',$filepath) . "','" . $dval . "','" . $fid . "','" . $filefilt . "'); return false;" ;
            ?>
            <tr> 
              <td width="50">Server</td> 
              <td class="nobr" colspan="2"> 
                <span style="display:inline-block;background-color:white;width:220px;"><select class="chosen-select" name="<?= $fname ; ?>" id="<?= $fid ; ?>" size="1"> 
                  <option value="">** Select **</option> 
                  <?
                  foreach($files as $file) {
                    if(istype($filepath,$file,'file')) {
                      if($file == $dval) {
                        ?>
                  <option value="<?= $file ; ?>" selected="selected"><?= cliptext($file,'60','...') ; ?></option>
                        <?
                      }
                      else {
                        ?>
                  <option value="<?= $file ; ?>"><?= cliptext($file,'60','...') ; ?></option>
                        <?
                      }
                    }
                  }
                  ?>
                </select></span> <span class="button"><a href="<?= $href ?>" onclick="<?= $onc . 'return false;' ; ?>" target="_blank">POP</a></span></td> 
            </tr>
            <? } ?>
            <tr> 
              <td width="50">Upload</td> 
              <td colspan="2"> 
                <div class="fileUpload">
                  <?
                  $nm = 'bd[' . $table . '_' . $row['id'] . '_upimage]';
                  $fname = str_replace('[','_',str_replace(']','',$nm)) ;
                  $id = 'id_' . $fname ;
                  $hd = 'hd_' . $fname ;
                  $bt = 'bt_' . $fname ;
                  $onc = "getId('{$hd}').value = getId('{$id}').value.split('\\\').pop(); onbrowse('{$fname}')";
                  $omo = "getId('{$bt}').setAttribute('class', 'button1')";
                  $omx = "getId('{$bt}').setAttribute('class', 'button')";
                  ?>
                  <input type="file" onchange="<?= $onc ; ?>" name="<?= $nm ; ?>" id="<?= $id ; ?>" class="browserHidden" onmouseover="<?= $omo ; ?>" onmouseout="<?= $omx ; ?>"> 
                  <div class="browserVisible">
                    <input type="text" class="input" id="<?= $hd ; ?>" style="width:110px;" autocomplete="off"> <span id="<?= $bt ?>" class="button"><a href="#">BROWSE</a></span> 
                  </div>
                </div>
              </td> 
            </tr> 
          </tbody> 
        </table>
      </td> 
      <td> 
        <table summary="" border="0" cellpadding="2" cellspacing="0" class="tablen"> 
          <tr> 
            <td width="50">Link</td> 
            <td width="260">
              <input type="text" name="<?= 'bd[' . $table . '_' . $row['id'] . '_link]' ; ?>" size="<?= $sfields[$table]['link']['Fprms']; ?>" maxlength="<?= $sfields[$table]['link']['Fprms']; ?>" value="<?= $row['link'] ?>" style="width:260px;">
            </td>
            <td rowspan="2">&nbsp; &nbsp;</td>
            <td rowspan="2">
              <u style="display:block;margin-bottom:5px;">Image Position</u>
              <label class="radiolabel"><input type="radio" name="<?= 'bd[' . $table . '_' . $row['id'] . '_position]' ; ?>" value="<?= optchk('left',$row['position']) ?>" selected="selected"> Left<span class="radiocust"></span></label>
              <br>
              <label class="radiolabel"><input type="radio" name="<?= 'bd[' . $table . '_' . $row['id'] . '_position]' ; ?>" value="<?= optchk('right',$row['position']) ?>"> Right<span class="radiocust"></span></label>
              < 
            </td>
          </tr> 
          <tr> 
            <td width="50">Caption</td> 
            <td width="260">
              <input type="text" name="<?= 'bd[' . $table . '_' . $row['id'] . '_caption]' ; ?>" size="<?= $sfields[$table]['caption']['Fprms']; ?>" maxlength="<?= $sfields[$table]['caption']['Fprms']; ?>" value="<?= $row['caption'] ?>" style="width:260px;">
            </td> 
          </tr> 
        </table> </td> 
      <td align="center" rowspan="2"><label class="checklabel"><input type="checkbox" name="<?= 'bd[' . $table . '_' . $row['id'] . '_delete]' ; ?>" value="y"><span class="checkcust"></span></label></td> 
    </tr> 
    <tr style="<?= $bgs ?>"> 
      <td><b><?= strtoupper($table) ?></b>
      </td>
      <td colspan="2">
        <? 
        $fname = 'bd[' . $table . '_' . $row['id'] . '_html]';
        $fid = str_replace(array('[',']'),'_',$fname);
        ?>
        <textarea class="ckeditor" id="<?= $fid ?>" name="<?= $fname ?>" rows="5" cols="85" style="width:calc(100% - 6px)">
          <?= $row['html'] ?>
        </textarea>
        <? ckeditor_js($fid); ?>
      </td>
    </tr> 
    <? } elseif($table == 'link') { ?>
    <tr style="<?= $bgs ?>"> 
      <td><input type="text" name="<?= 'bd[' . $table . '_' . $row['id'] . '_order]' ; ?>" value="<?= $row['order'] ?>" size="3"></td> 
      <td><b><?= strtoupper($table) ?></b></td> 
      <td> 
        <table summary="" border="0" cellpadding="1" cellspacing="0" class="tablen"> 
          <tr> 
            <td width="50">Link</td> 
            <td width="260">
              <input type="text" name="<?= 'bd[' . $table . '_' . $row['id'] . '_link]' ; ?>" size="<?= $sfields[$table]['link']['Fprms']; ?>" maxlength="<?= $sfields[$table]['link']['Fprms']; ?>" value="<?= $row['link'] ?>" style="width:260px;">
            </td> 
          </tr> 
        </table>
      </td> 
      <td> 
        <table summary="" border="0" cellpadding="1" cellspacing="0" class="tablen"> 
          <tr> 
            <td width="50">Text</td> 
            <td width="270">
              <input type="text" name="<?= 'bd[' . $table . '_' . $row['id'] . '_text]' ; ?>" size="<?= $sfields[$table]['text']['Fprms']; ?>" maxlength="<?= $sfields[$table]['text']['Fprms']; ?>" value="<?= $row['text'] ?>" style="width:260px;">
            </td> 
          </tr> 
        </table>
      </td> 
      <td align="center"><label class="checklabel"><input type="checkbox" name="<?= 'bd[' . $table . '_' . $row['id'] . '_delete]' ; ?>" value="y"><span class="checkcust"></span></label></td> 
    </tr> 
    <? } elseif($table == 'vimeo' || $table == 'youtube') { ?>
    <tr style="<?= $bgs ?>"> 
      <td><input type="text" name="<?= 'bd[' . $table . '_' . $row['id'] . '_order]' ; ?>" value="<?= $row['order'] ?>" size="3"></td> 
      <td><b><?= strtoupper($table) ?></b></td> 
      <td> 
        <table summary="" border="0" cellpadding="1" cellspacing="0" class="tablen"> 
          <tr> 
            <td width="50">Code</td> 
            <td width="270">
              <input type="text" name="<?= 'bd[' . $table . '_' . $row['id'] . '_code]' ; ?>" size="<?= $sfields[$table]['code']['Fprms']; ?>" maxlength="<?= $sfields[$table]['code']['Fprms']; ?>" value="<?= $row['code'] ?>">
            </td> 
          </tr> 
        </table>
      </td> 
      <td> 
        <table summary="" border="0" cellpadding="2" cellspacing="0" class="tablen"> 
          <tr> 
            <td width="50">Caption</td> 
            <td width="260">
              <input type="text" name="<?= 'bd[' . $table . '_' . $row['id'] . '_caption]' ; ?>" size="<?= $sfields[$table]['caption']['Fprms']; ?>" maxlength="<?= $sfields[$table]['caption']['Fprms']; ?>" value="<?= $row['caption'] ?>" style="width:260px;">
            </td> 
          </tr> 
        </table>
      </td> 
      <td align="center"><label class="checklabel"><input type="checkbox" name="<?= 'bd[' . $table . '_' . $row['id'] . '_delete]' ; ?>" value="y"><span class="checkcust"></span></label></td> 
    </tr> 
    <? } else { ?>
    <tr style="<?= $bgs ?>"> 
      <td><input type="text" name="<?= 'bd[' . $table . '_' . $row['id'] . '_order]' ; ?>" value="<?= $row['order'] ?>" size="3"></td> 
      <td><b><?= strtoupper($table) ?></b></td> 
      <td colspan="2">
      <? 
      if(isset($sfields[$table]['text'])) {
        $fname = 'bd[' . $table . '_' . $row['id'] . '_text]';
        if($sfields[$table]['text']['Ftype'] == 'varchar') {
          ?>
          <input type="text" name="<?= $fname ; ?>" size="<?= $sfields[$table]['text']['Fprms'] > $globvars['maxbox'] ? $globvars['maxbox'] : $sfields[$table]['text']['Fprms'] ; ?>" maxlength="<?= $sfields[$table]['text']['Fprms']; ?>" value="<?= $row['text'] ?>">
          <?
        }
        else {
          ?>
          <textarea name="<?= $fname ; ?>" rows="5" cols="85" style="width:calc(100% - 6px)"><?= $row['text'] ?></textarea>
          <?
        }
      }
      elseif(isset($sfields[$table]['html'])) {
        $fname = 'bd[' . $table . '_' . $row['id'] . '_html]';
        $fid = str_replace(array('[',']'),'_',$fname);
        ?>
        <textarea class="ckeditor" id="<?= $fid ?>" name="<?= $fname ; ?>" rows="5" cols="85" style="width:calc(100% - 6px)"><?= $row['html'] ?></textarea>
        <?
        ckeditor_js($fid); 
      }
      ?>
      </td> 
      <td align="center"><label class="checklabel"><input type="checkbox" name="<?= 'bd[' . $table . '_' . $row['id'] . '_delete]' ; ?>" value="y"><span class="checkcust"></span></label></td> 
    </tr> 
    <? }
  } 
  ?>
  </tbody> 
</table>
<? } ?>
<br><br> 
<table summary="" border="0" cellpadding="0" cellspacing="0" width="100%"> 
  <tr valign="top"> 
    <td width="400"><br><br> 
      <h2>ADD ENTRIES</h2></td> 
    <td valign="top" align="center">
      <input type="submit" name="Submit1" value="SAVE" class="submit"></td> 
    <td valign="top" width="400"></td> 
  </tr> 
</table> <br> 
<table summary="" border="0" cellpadding="4" cellspacing="0" width="100%" class="tableb"> 
  <tbody> 
    <tr class="th"> 
      <td style="padding:10px 4px"><b>ORDER</b></td> 
      <td><b>TYPE</b></td> 
      <td colspan="2"><b>CONTENT</b></td> 
    </tr>
  <? 
  $bgn = 0;
  foreach($stack as $table) {
    ?>
    <tr> 
      <td colspan="4" style="height:14px;border-right:none;border-left:none;"></td> 
    </tr>
    <?
    $bgs = 'background-color:#EEEEEE;';
    if($table == 'head') { ?>
    <tr style="<?= $bgs ?>"> 
      <td><input type="text" name="<?= 'nd[' . $table . '_order]' ; ?>" size="3" value=""></td> 
      <td><b><?= strtoupper($table) ?></b></td> 
      <td colspan="2">
        <input type="text" name="<?= 'nd[' . $table . '_text]' ; ?>" size="<?= $sfields[$table]['text']['Fprms'] > $globvars['maxbox'] ? $globvars['maxbox'] : $sfields[$table]['text']['Fprms'] ; ?>" maxlength="<?= $sfields[$table]['text']['Fprms']; ?>">
      </td>
    </tr> 
    <? } elseif($table == 'image' || $table == 'file') { ?>
    <tr style="<?= $bgs ?>"> 
      <td><input type="text" name="<?= 'nd[' . $table . '_order]' ; ?>" size="3" value=""></td> 
      <td><b><?= strtoupper($table) ?></b></td> 
      <td> 
        <table summary="" border="0" cellpadding="2" cellspacing="0" class="tablen"> 
          <tbody>
          <?
          if(count($files)>0) {
            $fname = ($table == 'image') ? 'nd[' . $table . '_image]' : 'nd[' . $table . '_file]' ;
            $fid = str_replace('[','_',str_replace(']','',$fname)) ;
            $href = 'listfiles.php?fpath=' . str_replace('/','|',$filepath) . '&amp;fid=' . $fid . '&amp;filter=' . $filefilt ;
            $onc = "listfiles('" . str_replace('/','|',$filepath) . "','','" . $fid . "','" . $filefilt . "'); return false;" ;
            ?>
            <tr> 
              <td width="50">Server</td> 
              <td class="nobr" colspan="2"> 
                <span style="display:inline-block;background-color:white;width:220px;"><select class="chosen-select" name="<?= $fname ; ?>" id="<?= $fid ; ?>" size="1"> 
                  <option value="">** Select **</option> 
                  <?
                  foreach($files as $file) {
                    if(istype($filepath,$file,'file')) {
                      ?>
                  <option value="<?= $file ; ?>"><?= cliptext($file,'60','...') ; ?></option>
                      <?
                    }
                  }
                  ?>
                </select></span> <span class="button"> <a href="<?= $href ?>" onclick="<?= $onc . 'return false;' ; ?>" target="_blank">POP</a></span></td> 
            </tr>
            <? } ?>
            <tr> 
              <td width="50">Upload</td> 
              <td colspan="2"> 
                <div class="fileUpload">
                  <?
                  $nm = ($table == 'image') ? 'nd[' . $table . '_upimage]' : 'nd[' . $table . '_upfile]';
                  $fname = str_replace('[','_',str_replace(']','',$nm)) ;
                  $id = 'id_' . $fname ;
                  $hd = 'hd_' . $fname ;
                  $bt = 'bt_' . $fname ;
                  $onc = "getId('{$hd}').value = getId('{$id}').value.split('\\\').pop(); onbrowse('{$fname}')";
                  $omo = "getId('{$bt}').setAttribute('class', 'button1')";
                  $omx = "getId('{$bt}').setAttribute('class', 'button')";
                  ?>
                  <input type="file" onchange="<?= $onc ; ?>" name="<?= $nm ; ?>" id="<?= $id ; ?>" class="browserHidden" onmouseover="<?= $omo ; ?>" onmouseout="<?= $omx ; ?>"> 
                  <div class="browserVisible">
                    <input type="text" class="input" id="<?= $hd ; ?>" style="width:110px;" autocomplete="off"> <span id="<?= $bt ?>" class="button"><a href="#">BROWSE</a></span> 
                  </div>
                </div>
              </td> 
            </tr> 
          </tbody> 
        </table>
      </td> 
      <td> 
        <table summary="" border="0" cellpadding="2" cellspacing="0" class="tablen"> 
          <? if($table == 'image') { ?>
          <tr> 
            <td width="50">Link</td> 
            <td width="260"><input type="text" name="<?= 'nd[' . $table . '_link]' ; ?>" size="<?= $sfields[$table]['link']['Fprms']; ?>" maxlength="<?= $sfields[$table]['link']['Fprms']; ?>" style="width:260px;"></td> 
          </tr> 
          <tr> 
            <td width="50">Caption</td> 
            <td width="260"><input type="text" name="<?= 'nd[' . $table . '_caption]' ; ?>" size="<?= $sfields[$table]['caption']['Fprms']; ?>" maxlength="<?= $sfields[$table]['caption']['Fprms']; ?>" style="width:260px;"></td> 
          </tr> 
          <? } else { ?>
          <tr> 
            <td width="50">Text</td> 
            <td width="260"><input type="text" name="<?= 'nd[' . $table . '_text]' ; ?>" size="<?= $sfields[$table]['text']['Fprms']; ?>" maxlength="<?= $sfields[$table]['text']['Fprms']; ?>" style="width:260px;"></td> 
          </tr> 
          <? } ?>
        </table>
      </td> 
    </tr> 
    <? } elseif($table == 'twocols') { ?>
    <tr style="<?= $bgs ?>"> 
      <td rowspan="2"><input type="text" name="<?= 'nd[' . $table . '_order]' ; ?>" size="3" value=""></td> 
      <td rowspan="2"><b><?= strtoupper($table) ?></b></td> 
      <td> 
        <table summary="" border="0" cellpadding="1" cellspacing="0" class="tablen"> 
          <tbody>
          <?
          if(count($files)>0) {
            $fname = 'nd[' . $table . '_image]' ;
            $fid = str_replace('[','_',str_replace(']','',$fname)) ;
            $href = 'listfiles.php?fpath=' . str_replace('/','|',$filepath) . '&amp;fid=' . $fid . '&amp;filter=' . $filefilt ;
            $onc = "listfiles('" . str_replace('/','|',$filepath) . "','','" . $fid . "','" . $filefilt . "'); return false;" ;
            ?>
            <tr> 
              <td width="50">Server</td> 
              <td class="nobr" colspan="2"> 
                <span style="display:inline-block;background-color:white;width:220px;"><select class="chosen-select" name="<?= $fname ; ?>" id="<?= $fid ; ?>" size="1"> 
                  <option value="">** Select **</option> 
                  <?
                  foreach($files as $file) {
                    if(istype($filepath,$file,'file')) {
                      ?>
                  <option value="<?= $file ; ?>"><?= cliptext($file,'60','...') ; ?></option>
                      <?
                    }
                  }
                  ?>
                </select></span> <span class="button"> <a href="<?= $href ?>" onclick="<?= $onc . 'return false;' ; ?>" target="_blank">POP</a></span></td> 
            </tr>
            <? } ?>
            <tr> 
              <td width="50">Upload</td> 
              <td colspan="2"> 
                <div class="fileUpload">
                  <?
                  $nm = 'nd[' . $table . '_upimage]';
                  $fname = str_replace('[','_',str_replace(']','',$nm)) ;
                  $id = 'id_' . $fname ;
                  $hd = 'hd_' . $fname ;
                  $bt = 'bt_' . $fname ;
                  $onc = "getId('{$hd}').value = getId('{$id}').value.split('\\\').pop(); onbrowse('{$fname}')";
                  $omo = "getId('{$bt}').setAttribute('class', 'button1')";
                  $omx = "getId('{$bt}').setAttribute('class', 'button')";
                  ?>
                  <input type="file" onchange="<?= $onc ; ?>" name="<?= $nm ; ?>" id="<?= $id ; ?>" class="browserHidden" onmouseover="<?= $omo ; ?>" onmouseout="<?= $omx ; ?>"> 
                  <div class="browserVisible">
                    <input type="text" class="input" id="<?= $hd ; ?>" style="width:110px;" autocomplete="off"> <span id="<?= $bt ?>" class="button"><a href="#">BROWSE</a></span> 
                  </div>
                </div>
              </td> 
            </tr> 
          </tbody> 
        </table>
      </td> 
      <td>
        <table summary="" border="0" cellpadding="2" cellspacing="0" class="tablen" width="100%"> 
          <tr> 
            <td width="50">Link</td> 
            <td width="260"><input type="text" name="<?= 'nd[' . $table . '_link]' ; ?>" size="<?= $sfields[$table]['link']['Fprms']; ?>" maxlength="<?= $sfields[$table]['link']['Fprms']; ?>" style="width:260px;"></td> 
            <td rowspan="2">&nbsp; &nbsp;</td>
            <td rowspan="2">
              <u style="display:block;margin-bottom:5px;">Image Position</u>
              <label class="radiolabel"><input type="radio" name="<?= 'nd[' . $table . '_position]' ; ?>" value="left"> Left<span class="radiocust"></span></label>
              <br>
              <label class="radiolabel"><input type="radio" name="<?= 'nd[' . $table . '_position]' ; ?>" value="right"> Right<span class="radiocust"></span></label>
            </td>
          </tr> 
          <tr> 
            <td width="50">Caption</td> 
            <td width="260"><input type="text" name="<?= 'nd[' . $table . '_caption]' ; ?>" size="<?= $sfields[$table]['caption']['Fprms']; ?>" maxlength="<?= $sfields[$table]['caption']['Fprms']; ?>" style="width:260px;"></td> 
          </tr> 
        </table>
      </td> 
    </tr> 
    <tr style="<?= $bgs ?>"> 
      <td colspan="2">
        <?
        $fname = 'nd[' . $table . '_html]';
        $fid = str_replace(array('[',']'),'_',$fname);
        ?>
        <textarea class="ckeditor" id="<?= $fid ?>" name="<?= $fname ?>" rows="5" cols="85" style="width:calc(100% - 6px)"></textarea>
        <? ckeditor_js($fid); ?>
      </td> 
    </tr> 
    <? } elseif($table == 'link') { ?>
    <tr style="<?= $bgs ?>"> 
      <td><input type="text" name="<?= 'nd[' . $table . '_order]' ; ?>" size="3" value=""></td> 
      <td><b><?= strtoupper($table) ?></b></td> 
      <td> 
        <table summary="" border="0" cellpadding="1" cellspacing="0" class="tablen"> 
          <tr> 
            <td width="50">Link</td> 
            <td width="260"><input type="text" name="<?= 'nd[' . $table . '_link]' ; ?>" size="<?= $sfields[$table]['link']['Fprms']; ?>" maxlength="<?= $sfields[$table]['link']['Fprms']; ?>" style="width:260px;"></td> 
          </tr> 
        </table>
      </td> 
      <td> 
        <table summary="" border="0" cellpadding="2" cellspacing="0" class="tablen"> 
          <tr> 
            <td width="50">Text</td> 
            <td width="270"><input type="text" name="<?= 'nd[' . $table . '_text]' ; ?>" size="<?= $sfields[$table]['text']['Fprms']; ?>" maxlength="<?= $sfields[$table]['text']['Fprms']; ?>" style="width:260px;"></td> 
          </tr> 
        </table>
      </td> 
    </tr> 
    <? } elseif($table == 'vimeo' || $table == 'youtube') { ?>
    <tr style="<?= $bgs ?>"> 
      <td><input type="text" name="<?= 'nd[' . $table . '_order]' ; ?>" size="3" value=""></td> 
      <td><b><?= strtoupper($table) ?></b></td> 
      <td> 
        <table summary="" border="0" cellpadding="1" cellspacing="0" class="tablen"> 
          <tr> 
            <td width="50">Code</td> 
            <td width="260"><input type="text" name="<?= 'nd[' . $table . '_code]' ; ?>" size="<?= $sfields[$table]['code']['Fprms']; ?>" maxlength="<?= $sfields[$table]['code']['Fprms']; ?>"></td> 
          </tr> 
        </table>
      </td> 
      <td> 
        <table summary="" border="0" cellpadding="2" cellspacing="0" class="tablen"> 
          <tr> 
            <td width="50">Caption</td> 
            <td width="270"><input type="text" name="<?= 'nd[' . $table . '_caption]' ; ?>" size="<?= $sfields[$table]['code']['Fprms']; ?>" maxlength="<?= $sfields[$table]['caption']['Fprms']; ?>" style="width:260px;"></td> 
          </tr> 
        </table>
      </td> 
    </tr> 
    <? } else { ?>
    <tr style="<?= $bgs ?>"> 
      <td><input type="text" name="<?= 'nd[' . $table . '_order]' ; ?>" size="3" value=""></td> 
      <td><b><?= strtoupper($table) ?></b></td> 
      <td colspan="2">
      <? 
      if(isset($sfields[$table]['text'])) {
        $fname = 'nd[' . $table . '_text]';
        if($sfields[$table]['text']['Ftype'] == 'varchar') {
          ?>
          <input type="text" name="<?= $fname ; ?>" size="<?= $sfields[$table]['text']['Fprms'] > $globvars['maxbox'] ? $globvars['maxbox'] : $sfields[$table]['text']['Fprms'] ; ?>" maxlength="<?= $sfields[$table]['text']['Fprms']; ?>" value="">
          <?
        }
        else {
          ?>
          <textarea name="<?= $fname ; ?>" rows="5" cols="85" style="width:calc(100% - 6px)"></textarea>
          <?
        }
      }
      elseif(isset($sfields[$table]['html'])) {
        $fname = 'nd[' . $table . '_html]';
        $fid = str_replace(array('[',']'),'_',$fname);
        ?>
        <textarea class="ckeditor" id="<?= $fid ?>" name="<?= $fname ; ?>" rows="5" cols="85" style="width:calc(100% - 6px)"></textarea>
        <?
        ckeditor_js($fid); 
      }
      ?>
      </td> 
    </tr> 
    <? }
  } 
  ?>
  </tbody> 
</table> 
</div>
  <? 
}

function get_cols() {
  global $globvars;
  $globvars['c_arr'] = $globvars['l_arr'] = $globvars['fields'] = array();
  $globvars['cols'] = my_fields($globvars['sq_table'],MYSQL_ASSOC);
  // add fake fields
  foreach($globvars['sq_keys'] as $k => $v) {
    if(substr_count($v,'z')) {
      $o_arr = array_splice($globvars['cols'], 0, $k);
      if(isset($globvars['sq_lookt'][$k]) && $globvars['sq_lookt'][$k]) {
        $z_name = $globvars['sq_lookt'][$k] . '_' . $k ; // fake field name = table name with field number to ensure unique
      }
      elseif(isset($globvars['sq_names'][$k]) && $globvars['sq_names'][$k]) {
        $z_name = strtolower($globvars['sq_names'][$k]) . '_' . $k ; // fake field name using name where no table
      }
      else {
        $z_name = 'field_' . $k ; // fake field name using just number where no table
      }
      $n_arr = array($z_name=>array('Field'=>$z_name,'Type'=>'fake','Null'=>'','Key'=>'','Default'=>'','Extra'=>'','Ftype'=>'','Fprts'=>'','Fprms'=>''));
      $globvars['cols'] = array_merge($o_arr, $n_arr, $globvars['cols']);
    }
  }
  $len = is_array($globvars['cols']) ? count($globvars['cols']) : 0;
  if($globvars['debug']) {
    print_d("Fields in {$globvars['sq_table']}: $len",__LINE__,__FILE__);
    // print_arv($globvars['cols']);
  }
  if($len) {
    foreach( $globvars['chk_var'] as $key => $val ) {
      if(! isset($globvars[$key]) ) {
        $globvars[$key] = $val ;
      }
    }
    $c = 0 ;
    foreach($globvars['cols'] as $c_row) {
      $globvars['c_arr'][$c]['col'] = $c ;
      $globvars['fields'][$c] = $c_row['Field'];
      foreach( $globvars['chk_arr'] as $arr ) {
        if(isset($globvars[$arr][$c])) {
          $globvars['c_arr'][$c][$arr] = $globvars[$arr][$c] ;
        }
        else {
          $globvars['c_arr'][$c][$arr] = $globvars[$arr][$c] = '' ;
        }
      }
      $globvars['c_arr'][$c]['fname'] = $c_row['Field'] ;

      $ftype = $c_row['Ftype'];
      $fprts = $c_row['Fprts'];
      $mlen = $flen = $fprms = $c_row['Fprms'];
      if(sq_keys($c,'i')) {
        // aes_encrypt
        $mlen -= 50;
      }
      if($flen > $globvars['maxbox']) {
        $flen = $globvars['maxbox'] ;
      }
      if($sqn = sq_num($c)) {
        $flen = $sqn ;
      }

      $align = $globvars['sq_style'][$c];
      if($ftype == 'decimal' && ! sq_keys($c,'koc') ) {
        $align .= ';text-align:right;';
      }

      $globvars['c_arr'][$c]['ftype'] = $ftype ;
      $globvars['c_arr'][$c]['fprts'] = $fprts ;
      $globvars['c_arr'][$c]['fprms'] = $fprms ;
      $globvars['c_arr'][$c]['flen'] = $flen ;
      $globvars['c_arr'][$c]['mlen'] = $mlen ;
      $globvars['c_arr'][$c]['align'] = $align ;
      $c++;
    }
    if(isset($globvars['sq_list'])) {
      foreach($globvars['c_arr'] as $key => $arr) {
        $key1 = isset($globvars['sq_list'][$key]) && $globvars['sq_list'][$key] && is_numeric(intval($globvars['sq_list'][$key])) ? intval($globvars['sq_list'][$key]) : $key + 1000 ;
        $globvars['l_arr'][$key1] = $arr ;
      }
      ksort($globvars['l_arr']);
    }
    else {
      $globvars['l_arr'] = $globvars['c_arr'] ;
    }

    // print_arr($globvars['cols'],'cols');
    // print_arr($globvars['fields'],'fields');
    // print_arr($globvars['c_arr'],'c_arr');
    // print_arr($globvars['l_arr'],'l_arr');
  }
}

function sq_keys($c,$v) {
  global $globvars;
  for($i=0;$i<strlen($v);$i++) {
    $w = substr($v,$i,1);
    if(isset($globvars['c_arr'][$c]['sq_keys']) && substr_count($globvars['c_arr'][$c]['sq_keys'],$w)) { return true ; }
  }
  return false ;
}

function sq_num($c,$max='') {
  global $globvars;
  $num = 0 ;
  if(isset($globvars['c_arr'][$c]['sq_keys']) && $preg = preg_replace("/[^0-9_]/", '', $globvars['c_arr'][$c]['sq_keys'])) {
    if($max) {
      if(substr_count($preg,'_')) {
        $num = substr($preg, strpos($preg,'_')+1);
      }
    }
    else {
      if(substr_count($preg,'_')) {
        $num = substr($preg, 0, strpos($preg,'_'));
      }
      else {
        $num = $preg ;
      }
    }
  }
  $sqn = $num && is_numeric($num) ? $num : false ;
  return $sqn ;
}

function linkvars($sort='',$start='',$action='',$go='',$del='',$filter='') {
  global $globvars;
  $link = $globvars['php_self'] ;

  // from parameters
  if($action) { 
    $link .= '&amp;action=' . $action ;
  }
  if($go) { 
    $link .= '&amp;go=' . $go ; 
  }
  if($del) { 
    $link .= '&amp;del=' . $del ; 
  }
  if($start) { 
    $link .= '&amp;start=' . $start ;
  }
  if($sort) { 
    $link .= '&amp;sort=' . $sort ;
  }

  // filter
  if($globvars['filter'] && ! $filter) {
    $filter = $globvars['filter'] ;
  }
  $filter = is_array($filter) ? filter_str($filter) : $filter ;
  if($filter && ! (substr_count($filter,'csv:') || substr_count($filter,'opt:'))) {
    $link .= '&amp;filter=' . $filter ;
  }

  // from globvars
  if($globvars['search']) {
    $link .= '&amp;search=' . $globvars['search'] ;
  }
  if($globvars['rngfr'] || $globvars['rngto']) {
    if($globvars['rngfr']) {
      $link .= '&amp;rngfr=' . $globvars['rngfr'] ;
    }
    if($globvars['rngto']) {
      $link .= '&amp;rngto=' . $globvars['rngto'] ;
    }
  }
  if($globvars['vars']) {
    $link .= '&amp;vars=' . $globvars['vars'] ;
  }
  if($globvars['xfilter']) {
    $link .= '&amp;xfilter=' . $globvars['xfilter'] ;
  }

  // clean link
  $link = clean_link($link);

  // add after clean
  if($filter && (substr_count($filter,'csv:') || substr_count($filter,'opt:'))) {
    $link .= (substr_count($link, '?') ? '&amp;' : '?') . 'filter=' . $filter ;
  }
  return $link ;
}

function getfpath($pth,$sub='',$pad='') {
  global $globvars; extract($globvars,EXTR_SKIP);
  if(! ($sub && $pad)) {
    $sub = $globvars['go'];
    $pad = $globvars['fprefpadd'];
  }
  $filepath = build_path($filepath, $pth) ;
  if($sub && $pad) {
    $sub = str_pad ( $sub, $pad, '0', STR_PAD_LEFT ) ;
    $newpath = build_path($filepath, $sub) ;
    if(!file_exists($newpath)) {
      make_dir($filepath, $sub);
    }
    $filepath = $newpath ;
  }
  return($filepath);
}

function filter_ars($in) {
  global $globvars ;
  $globvars['filter_arr'] = is_array($in) ? $in : array(); // original array
  if($globvars['xfilter'] == 'off') {
    foreach($globvars['c_arr'] as $sqc => $sqn) {
      if(substr_count($globvars['mfilter'],$sqn['fname']) && ! substr_count($sqn['sq_keys'],'l')) {
        $globvars['c_arr'][$sqc]['sq_keys'] .= 'l';
      }
    }
    $globvars['mfilter'] = '';
  }
  if(is_array($in) && ! array_diff_key($in,array_keys(array_keys($in)))) {
    // from multi selector
    $in = safe_implode( "^", $in );
  }
  if(! is_array($in)) {
    // string to array
    $f_arr = safe_explode('^', $in );
    $in = array();
    foreach($f_arr as $f_key => $f_str) {
      if(substr_count($f_str,'|')) {
        $tt = substr( $f_str, 0 , strpos( $f_str, '|' ) ) ;
        $vv = substr( $f_str, strpos( $f_str, '|' ) + 1 ) ;
        if(isset($globvars['cols'][$tt]) || (1==1)) {
          // do not check for field existing
          $c = array_search($tt,$globvars['fields']);
          if(substr_count($globvars['sq_keys'][$c],'s')) {
            if(! isset($in[$tt])) {
              $in[$tt] = array();
            }
            $in[$tt][] = $vv ;
          }
          else {
            $in[$tt] = $vv ;
          }
        }
      }
    }
  }
  // print_arr($in);
  return($in);
}

function get_fstring() {
  // field|1234 (matches value)
  // csv:field|1234,1235 (match values in array)
  // opt:field|1234 (match value in csv field)
  // field|GT:1234 (greater than)
  // field|GTE:1234 (greater or equal to)
  // field|LT:1234 (less than)
  // field|LTE:1234 (less or equal to)
  // field|NOTNUM (not a number)
  // field|!! (not blank)
  // field|! (is blank)
  global $globvars; extract($globvars,EXTR_SKIP);
  $fgo = 0 ;
  $jstring = $smatch = '';
  if(! isset($fstring)) {
    $fstring = '';
    if(isvar($rangefilt) && ($rngfr || $rngto)) {
      $rfarr = substr( $rangefilt, 0, strpos( $rangefilt, '|' ) );
      $rftyp = substr( $rangefilt, strpos( $rangefilt, '|' ) + 1 );
      if($rftyp == 'date') {
        if($rngfr = cdate($rngfr,'Y-m-d','')) {
          $fstring .= "`{$fields[$rfarr]}` > '$rngfr'";
        }
        if($rngto = cdate($rngto,'Y-m-d','')) {
          if($rngfr) { $fstring .= " AND " ; }
          $fstring .= "`{$fields[$rfarr]}` <= DATE_ADD('{$rngto}', INTERVAL 1 DAY)";
        }
      }
      else {
        if($rngfr) {
          $fstring .= "`{$fields[$rfarr]}` >= '$rngfr'";
        }
        if($rngto) {
          if($rngfr) { $fstring .= " AND " ; }
          $fstring .= "`{$fields[$rfarr]}` <= '$rngto'";
        }
      }
    }
    else {
      if($filter) {
        foreach($c_arr as $c_row) {
          $c = $c_row['col'];
          if(sq_keys($c,'k')) {
            $fgo = $c_row['fname'] ;
          }
        }
        foreach($filter as $tt => $va) {
          $sqtj = $sq_tjoin[0] && ! in_array( $tt, $fields ) ? $sq_tjoin[0] : $sq_table ;
          if(substr($tt,0,4) == 'opt:') {
            $fld = substr($tt,4);
            $fstring .= my_csv("`$sqtj`.`$fld`",$va) . " AND " ;
          }
          elseif(substr($tt,0,4) == 'csv:') {
            $fld = substr($tt,4);
            if(substr_count($va,',')) {
              $fstring .= "`{$sqtj}`.`{$fld}` IN ($va) AND ";
            }
            else {
              $fstring .= "`{$sqtj}`.`{$fld}` = '$va' AND ";
            }
          }
          else {
            if(! is_array($va)) {
              $va = safe_explode( ',', $va ) ;
            }
            foreach($va as $vv) {
              // $vv = clean_amp(urldecode($vv)); // breaks click filter
              if($vv) {
                if(substr($vv,0,2)=='!!') {
                  // !! is blank
                  $vv = substr($vv,2) ;
                  $fstring .= "`{$sqtj}`.`$tt` = '' AND ";
                }
                elseif(substr($vv,0,1)=='!') {
                  // ! is not blank
                  $vv = substr($vv,1) ;
                  $fstring .= "`{$sqtj}`.`$tt` != '{$vv}' AND ";
                }
                elseif(substr($vv,0,6)=='NOTNUM') {
                  // not numeric
                  $vv = substr($vv,6) ;
                  $fstring .= "`{$sqtj}`.`$tt` NOT REGEXP '[0-9]+' AND ";
                }
                elseif(substr($vv,0,4)=='GTE:') {
                  $vv = substr($vv,4) ;
                  $fstring .= "`{$sqtj}`.`$tt` REGEXP '[0-9]+' AND `{$sqtj}`.`$tt` >= '{$vv}' AND ";
                }
                elseif(substr($vv,0,4)=='LTE:') {
                  $vv = substr($vv,4) ;
                  $fstring .= "`{$sqtj}`.`$tt` REGEXP '[0-9]+' AND `{$sqtj}`.`$tt` <= '{$vv}' AND ";
                }
                elseif(substr($vv,0,3)=='GT:') {
                 $vv = substr($vv,3) ;
                 $fstring .= "`{$sqtj}`.`$tt` REGEXP '[0-9]+' AND `{$sqtj}`.`$tt` > '{$vv}' AND ";
                }
                elseif(substr($vv,0,3)=='LT:') {
                  $vv = substr($vv,3) ;
                  $fstring .= "`{$sqtj}`.`$tt` REGEXP '[0-9]+' AND `{$sqtj}`.`$tt` < '{$vv}' AND ";
                }
                else {
                  $kk = array_search($tt,$fields);
                  $c_row = $globvars['c_arr'][$kk];
                  if(isset($c_row['sq_likef']) && $c_row['sq_likef']) {
                    $lk = str_replace('v',$vv,$c_row['sq_likef']);
                    $fstring .= "`$sqtj`.`$tt` LIKE '{$lk}' AND ";
                  }
                  elseif(sq_keys($kk,'o') || sq_keys($kk,'s')) {
                    if($fgo && $c_row['sq_lookt'] && $c_row['sq_lookk'] && $c_row['sq_joint'] && $c_row['sq_joink'] && $c_row['sq_joinv']) {
                      // join multi
                      $jstring .= " LEFT JOIN `{$c_row['sq_joint']}` on `{$c_row['sq_joint']}`.`{$c_row['sq_joink']}` = `{$sqtj}`.`{$fgo}` ";
                      $jstring .= " LEFT JOIN `{$c_row['sq_lookt']}` on `{$c_row['sq_lookt']}`.`{$c_row['sq_lookk']}` = `{$c_row['sq_joint']}`.`{$c_row['sq_joinv']}` ";
                      $fstring .= "`{$c_row['sq_lookt']}`.`{$c_row['sq_lookk']}` = '$vv' AND ";              
                    }
                    else {
                      // match in csv text field
                      $fstring .= my_csv("`$sqtj`.`$tt`",$vv) . " AND " ;
                    }
                  }
                  else {
                    $fstring .= "`{$sqtj}`.`$tt` = '{$vv}' AND ";
                  }
                }
              }
            }
          }
        }
        $fstring = substr($fstring, 0, -5 ) ;
        if($debug) {
          print_d($fstring,__LINE__,__FILE__);
        }        
      }
      if($search){
        $sstring = '' ;
        if(isset($globvars['powersearch']) && $globvars['powersearch']) {
          $search_arr = search_arr($search);
        }
        else {
          $search_arr[0] = $search;
        }
        if($debug) {
          print_arr($search_arr,'search_arr');
        }        
        foreach($c_arr as $c_row) {
          $c = $c_row['col'];
          $ftype = get_sqlft($c_row['ftype']) ;
          if(! sq_keys($c,'x')) {
            if(sq_keys($c,'k')) {
              $fgo = $c_row['fname'] ;
            }
            $smatch = '';
            $sqtj = ! in_array( $c_row['fname'], $fields ) ? $sq_tjoin[0] : $sq_table ;
            foreach($search_arr as $sword) {
              if($sword) {
                if((substr_count($c_row['sq_keys'],'o') || substr_count($c_row['sq_keys'],'s')) && isset($lookups[$c_row['fname']])) {
                  $fieldf = array() ;
                  if($fgo && $c_row['sq_lookt'] && $c_row['sq_lookk'] && $c_row['sq_joint'] && $c_row['sq_joink'] && $c_row['sq_joinv'] ) {
                    if($c_row['sq_lookd'] == 'k') {
                      $fieldf[] = $c_row['sq_lookk'];
                    }
                    elseif($c_row['sq_lookd'] == 'v' || $c_row['sq_lookd'] == 'k : v') {
                      $fieldf[] = $c_row['sq_lookv'];
                    }
                    elseif(substr_count($c_row['sq_lookd'],'[[') == substr_count($c_row['sq_lookd'],']]')) {
                      $str = $c_row['sq_lookd'] ;
                      while($vpos = substr_count($str,'[[')) {
                        $vfrm = strpos($str, '[[') ;
                        $vlen = strpos($str, ']]' , $vfrm) - $vfrm ;
                        $fieldf[] = substr($str, $vfrm + 2, $vlen - 2 ) ;
                        $str = substr_replace($str, '', $vfrm, $vlen + 2 ) ;
                      }
                    }
                  }
                  if(count($fieldf)) {
                    // join multi
                    if(! substr_count( $jstring, "LEFT JOIN `{$c_row['sq_joint']}`" )) {
                      $jstring .= " \r\nLEFT JOIN `{$c_row['sq_joint']}` on `{$c_row['sq_joint']}`.`{$c_row['sq_joink']}` = `{$sqtj}`.`{$fgo}` ";
                      $jstring .= " \r\nLEFT JOIN `{$c_row['sq_lookt']}` on `{$c_row['sq_lookt']}`.`{$c_row['sq_lookk']}` = `{$c_row['sq_joint']}`.`{$c_row['sq_joinv']}` ";
                    }
                    foreach($fieldf as $fieldx) {
                      $skey = "`{$c_row['sq_lookt']}`.`{$fieldx}`" ;
                      $swrd = my_escape_string(str_replace(['\\', '_', '%'], ['\\\\', '\\_', '\\%'], $sword));
                      if(substr_count( $swrd, '&#39;')) {
                        $skey = search_symsql($skey) ;
                      }
                      $smatch .= ($smatch ? " \r\nOR " : '') . "{$skey} LIKE '%$swrd%'";
                    }
                  }
                  else {
                    // find in lookups
                    // print_arr($lookups[$c_row['fname']]['sq_arr']);
                    foreach($lookups[$c_row['fname']]['sq_arr'] as $key => $opt_arr) {
                      $showv = $val = $opt_arr[$lookups[$c_row['fname']]['sq_def']] ;
                      $dsp = $c_row['sq_lookd'] ;
                      if( substr_count($dsp,'k') || substr_count($dsp,'v') || substr_count($dsp,'[[') ) {
                        $showv = $dsp ;
                        $showv = rep_var($showv,$opt_arr);
                        if($showv == $dsp) {
                          $showv = str_replace('k',$key,$showv);
                          $showv = str_replace('v',$val,$showv);
                        }
                      }
                      if(substr_count( strtolower($showv), strtolower($sword) )) {
                        if(substr_count($c_row['sq_keys'],'s')) {
                          $smatch .= ($smatch ? " \r\nOR " : '') . my_csv("$sqtj.{$c_row['fname']}",$key) ;
                        }
                        else {
                          $smatch .= ($smatch ? " \r\nOR " : '') . "`{$sqtj}`.`{$c_row['fname']}` = '{$key}'" ;
                        }
                      }
                    }
                  }
                }
                elseif(substr_count($ftype,'date')) {
                  // date field
                  if($ds = cdate($sword,'Y-m-d',null)) {
                    $smatch .= ($smatch ? " \r\nOR " : '') . "`{$sqtj}`.`{$c_row['fname']}` = '{$ds}'" ;
                  }
                }
                elseif($ftype == 'int' && is_numeric($sword)) {
                  // int field
                  $smatch .= ($smatch ? " \r\nOR " : '') . "`{$sqtj}`.`{$c_row['fname']}` = '{$sword}'" ;
                }
                elseif($ftype == 'decimal' && is_numeric($sword)) {
                  // decimal field
                  $smatch .= ($smatch ? " \r\nOR " : '') . "FLOOR(`{$sqtj}`.`{$c_row['fname']}`) = FLOOR('{$sword}')" ;
                }
                elseif($ftype == 'varchar' || $ftype == 'varbinary' || $ftype == 'text') {
                  // varchar/text field
                  $skey = "`{$sqtj}`.`{$c_row['fname']}`" ;
                  $swrd = my_escape_string(str_replace(['\\', '_', '%'], ['\\\\', '\\_', '\\%'], $sword));
                  if(substr_count( $swrd, '&#39;')) {
                    $skey = search_symsql($skey) ;
                  }
                  if($ftype == 'varbinary') {
                    $smatch .= ($smatch ? " \r\nOR " : '') . "CONVERT(AES_DECRYPT($skey,'" . DBKEY . "') USING 'utf8') LIKE '%{$swrd}%'" ;
                  }
                  else {
                    $smatch .= ($smatch ? " \r\nOR " : '') . "{$skey} LIKE '%{$swrd}%'" ;
                  }
                }

              }
            }
            if($smatch) {
              $sstring .= "{$smatch} \r\nOR ";
            }
          }
        }
        if($sstring) {
          // combine fstring with search
          $sstring = substr($sstring, 0, -4 ) ;
          if($fstring) {
            $fstring = " ( $fstring ) \r\nAND ( $sstring ) ";
          }
          else {
            $fstring = $sstring;
          }
        }
      }
    }
  }
  // sort order
  $globvars['order'] = '';
  if( !($sort) && isvar($sq_dsort) ) {
    $sort = $sq_dsort ;
  }
  if($sort) {
    $globvars['order_arr'] = array();
    $sort_arr = safe_explode( ',', $sort );
    $globvars['order'] = "\r\nORDER BY " ;
    // print_arr($globvars);
    foreach($sort_arr as $key => $sort_fld) {
      $sort_fld = safe_trim($sort_fld);
      if($key > 0) {
        $globvars['order'] .= ", " ;
      }
      $fld_num = array_search(str_replace('_DESC','',$sort_fld),$fields);
      $fld_ord = '';
      $fld_len = 0 ;

      if($fld_num && isset($globvars['sq_names'][$fld_num]) && sq_keys($fld_num,'i')) {
        $fld_ord = 'i';
      }
      elseif($fld_num && isset($globvars['sq_names'][$fld_num]) && sq_keys($fld_num,'n')) {
        $fld_ord = 'n';
      }
      elseif($fld_num && isset($globvars['sq_names'][$fld_num]) && sq_keys($fld_num,'w')) {
        $fld_ord = 'w';
      }
      elseif($fld_num && isset($globvars['sq_names'][$fld_num]) && sq_keys($fld_num,'*') && isset($globvars['c_arr'][$fld_num]) && $globvars['c_arr'][$fld_num]['flen']) {
        $fld_ord = '*';
        $fld_len = $globvars['c_arr'][$fld_num]['flen'];
      }
      if(substr_count($sort_fld,'_DESC')) {
        $sort_fld = str_replace( '_DESC', '', $sort_fld ) ;
        $sqtj = ! in_array( $sort_fld, $fields ) ? $sq_tjoin[$key] : $sq_table ;
        $sqtf = "`{$sqtj}`.`{$sort_fld}`";
        if($fld_ord == 'i') {
          $globvars['order'] .= " AES_DECRYPT($sqtf,'" . DBKEY . "') DESC " ;
        }
        elseif($fld_ord == 'n') {
          $globvars['order'] .= " $sqtf = 0, $sqtf DESC " ;
        }
        elseif($fld_ord == 'w') {
          $globvars['order'] .= " $sqtf IS NULL, $sqtf DESC " ;
        }
        elseif($fld_ord == '*') {
          $globvars['order'] .= " lpad($sqtf, $fld_len, 0) DESC " ;
        }
        else {
          $globvars['order'] .= " $sqtf DESC " ;
        }
        $globvars['order_arr'][$sort_fld] = 'DESC' ;
      }
      elseif(substr_count($sort_fld,'.') || substr_count($sort_fld,'`')) {
        $sqtf = $sort_fld;
        if($fld_ord == 'i') {
          $globvars['order'] .= " AES_DECRYPT($sqtf,'" . DBKEY . "') " ;
        }
        elseif($fld_ord == 'n') {
          $globvars['order'] .= " $sqtf = 0, $sqtf " ;
        }
        elseif($fld_ord == 'w') {
          $globvars['order'] .= " $sqtf IS NULL, $sqtf " ;
        }
        elseif($fld_ord == '*') {
          $globvars['order'] .= " lpad($sqtf, $fld_len, 0) " ;
        }
        else {
          $globvars['order'] .= " $sqtf " ;
        }
        $globvars['order_arr'][$sort_fld] = 'ASC' ;
      }
      else {
        $sqtj = ! in_array( $sort_fld, $fields ) ? $sq_tjoin[$key] : $sq_table ;
        $sqtf = "`{$sqtj}`.`{$sort_fld}`";
        if($fld_ord == 'i') {
          $globvars['order'] .= " AES_DECRYPT($sqtf,'" . DBKEY . "') " ;
        }
        elseif($fld_ord == 'n') {
          $globvars['order'] .= " $sqtf = 0, $sqtf " ;
        }
        elseif($fld_ord == 'w') {
          $globvars['order'] .= " $sqtf IS NULL, $sqtf " ;
        }
        elseif($fld_ord == '*') {
          $globvars['order'] .= " lpad($sqtf,$fld_len,0) " ;
        }
        else {
          $globvars['order'] .= " $sqtf " ;
        }
        $globvars['order_arr'][$sort_fld] = 'ASC' ;
      }
    }
    if(isset($globvars['sq_asort'])) {
      foreach($globvars['sq_asort'] as $s_fr => $s_to) {
        $globvars['order'] = str_replace( $s_fr, $s_to, $globvars['order'] ) ;
      }
    }
  }

  if(isvar($sq_ajoin)) {
    if(isvar($fstring)) {
      if(substr_count( $sq_ajoin, 'WHERE' )) {
        $fstring = "$sq_ajoin \r\nAND ($fstring)" ;
      }
      else {
        $fstring = "$sq_ajoin \r\nWHERE ($fstring)" ;
      }
    }
    else {
      $fstring = "$sq_ajoin" ;
    }
  }
  elseif(isvar($fstring)) {
    $fstring = "\r\nWHERE ($fstring)" ;
  }
  if(isvar($globvars['mfilter'])) {
    if(substr_count( $fstring, 'WHERE' )) {
      $fstring .= " \r\nAND ($mfilter)";
    }
    elseif($mfilter) {
      $fstring .= "\r\nWHERE ($mfilter)";
    }
  }

  // group unique
  if($jstring) {
    $fstring = "{$jstring} {$fstring} AND `{$sq_table}`.`{$fgo}` > 0 \r\nGROUP BY `{$sq_table}`.`{$fgo}` " ;
  }
  return $fstring ;
}

function dformat($val,$ftype,$op='vw') {
  global $globvars; extract($globvars,EXTR_SKIP);
  if($op == 'db'){ $dformat = 'Y-m-d'; }
  $out = false;
  if( $ftype == 'datetime' ) {
    $out = cdate($val,"$dformat H:i:s",' ') ;
    if($op == 'db' && ! safe_trim($out)){ $out = '0000-00-00 00:00:00'; }
  }
  elseif( $ftype == 'date' ) {
    $out = cdate($val,"$dformat",' ') ;
    if($op == 'db' && ! safe_trim($out)){ $out = '0000-00-00'; }
  }
  elseif( $ftype == 'time' ) {
    $out = ctime($val,"H:i:s",' ') ;
    if($op == 'db' && ! safe_trim($out)){ $out = '00:00:00'; }
  }
  return $out ;
}

function join_multi($c,$key,$csv,$update='') {
  // $c = field column, $key = record id, $csv = default text field, $update = update record id
  global $globvars; extract($globvars,EXTR_SKIP);
  if(!is_array($csv)) {
    $csv = safe_explode(',', $csv);
  }
  if(isset($globvars['list_query'])) {
    $stringc1 = '';
    $mk = 'list';
  }
  else {
    $mk = $key ;
    $stringc1 = "WHERE `{$sq_joink[$c]}` = '$key'";
  }
  if($sq_joint[$c] && $sq_joink[$c] && $sq_joinv[$c]) {
    if(! isset($globvars['join_multi'][$c][$mk])) {
      $stringc = "SELECT * FROM `{$sq_joint[$c]}` $stringc1 ORDER BY `{$sq_joink[$c]}`";
      if(substr_count($sq_keys[$c],'ss') && $sq_joino[$c]) {
        $stringc .= ", `{$sq_joino[$c]}`";
      }
      $stringc .= ", `{$sq_joinv[$c]}`";
      $globvars['debug_arr'][] = array('string' => $stringc, 'line' => __LINE__, 'file' => __FILE__) ;
      $query = my_query($stringc);
      $globvars['join_multi'][$c][$mk] = array();
      while($j_row = my_array($query)) {
        $globvars['join_multi'][$c][$mk][] = $j_row;
      }
    }
    $current = array();
    foreach($globvars['join_multi'][$c][$mk] as $j_arr) {
      if($j_arr[$sq_joink[$c]] == $key) {
        $current[] = $j_arr[$sq_joinv[$c]];
      }
    }
    if($update && ($update == $key)) {
      unset($globvars['join_multi'][$c][$mk]);
      // print_arr($current,'Old Join values'); 
      $delete = $add = array();
      foreach($current as $k => $v) {
        if(! in_array( $v, $csv )) {
          $delete[] = $v ;
          unset($current[$k]);
        }
      }
      foreach($csv as $v) {
        if($v && ! in_array( $v, $current )) {
          $add[] = $v ;
          $current[] = $v ;
        }
      }
      // print_arr($csv,'csv');
      // print_arr($current,'current');
      // print_arr($delete,'delete');
      // print_arr($add,'add');
      if(substr_count($sq_keys[$c],'ss') && $sq_joino[$c]) {
        // uses order field
        $current = array();
        $stringd = "DELETE FROM `{$sq_joint[$c]}` WHERE `{$sq_joink[$c]}` = '$key'";
        $globvars['debug_arr'][] = array('string' => $stringd, 'line' => __LINE__, 'file' => __FILE__) ;
        my_query($stringd);
        logtable('DELETE',$cntrl_user,$sq_joint[$c],$stringd);
        $stringi = '';
        foreach($csv as $o => $v) {
          if($v) {
            $oo = $o + 1 ;
            $stringi .= "\r\n('$key','$v','$oo') , ";
            $current[] = $v ;
          }
        }
        if($stringi) {
          $stringi = "INSERT INTO `{$sq_joint[$c]}` (`{$sq_joink[$c]}`,`{$sq_joinv[$c]}`,`{$sq_joino[$c]}`) VALUES " . substr( $stringi, 0, -2 );
          $globvars['debug_arr'][] = array('string' => $stringi, 'line' => __LINE__, 'file' => __FILE__) ;
          my_query($stringi);
          logtable('INSERT',$cntrl_user,$sq_joint[$c],$stringi);
        }
      }
      else {
        // no order field
        asort($current);
        if(count($delete)) {
          $stringd = "DELETE FROM `{$sq_joint[$c]}` WHERE `{$sq_joink[$c]}` = '$key' AND  " . my_arr($sq_joinv[$c],$delete);
          $globvars['debug_arr'][] = array('string' => $stringd, 'line' => __LINE__, 'file' => __FILE__) ;
          my_query($stringd);
          logtable('DELETE',$cntrl_user,$sq_joint[$c],$stringd);
        }
        if(count($add)) {
          $stringi = '';
          foreach($add as $v) {
            if($v) {
              $stringi .= "\r\n('$key','$v') , ";
            }
          }
          if($stringi) {
            $stringi = "INSERT INTO `{$sq_joint[$c]}` (`{$sq_joink[$c]}`,`{$sq_joinv[$c]}`) VALUES " . substr( $stringi, 0, -2 );
            $globvars['debug_arr'][] = array('string' => $stringi, 'line' => __LINE__, 'file' => __FILE__) ;
            my_query($stringi);            
            logtable('INSERT',$cntrl_user,$sq_joint[$c],$stringi);
          }
        }
      }
    }
    $globvars['debug_arr']['array'] = array('array' => $current, 'name' => 'Join values', 'line' => __LINE__, 'file' => __FILE__) ;
    return $current ;
  }
  return $csv;
}

function get_sqlft($in) {
  if(in_array($in, array('decimal','float') ) ) {
    return 'decimal';
  }
  elseif(in_array($in, array('char','varchar') ) ) {
    return 'varchar';
  }
  elseif( in_array($in, array('int','mediumint','tinyint','smallint') ) ) {
    return 'int';
  }
  elseif( in_array($in, array('text','mediumtext','tinytext','longtext') ) ) {
    return 'text';
  }
  else {
    return $in ;
  }
}

function debug_arr() {
  global $globvars ;
  if($globvars['debug'] && isset($globvars['debug_arr']) && is_array($globvars['debug_arr']) && count($globvars['debug_arr'])) {
    foreach($globvars['debug_arr'] as $arr) {
      if(isset($arr['array'])) {
        print_arr($arr['array'],$arr['name']);
      }
      else {
        print_d($arr['string'],$arr['line'],$arr['file']);
      }
    }
  }
  $globvars['debug_arr'] = array();
}

function ckeditor_js($fid) {
  global $globvars ;
  $bhrf = isset($globvars['base_href']) && $globvars['base_href'] ? "baseHref: '{$globvars['base_href']}'," : '' ; 
  ?>
  <script type="text/javascript">
  var ck_<?= $fid ; ?> = CKEDITOR.replace( '<?= $fid ; ?>', {
    <?= $bhrf ; ?> extraAllowedContent: 'div'
  });
  ck_<?= $fid ; ?>.on('instanceReady', function() {
    var dtd = CKEDITOR.dtd;
    for (var e in CKEDITOR.tools.extend({}, dtd.$nonBodyContent, dtd.$block, dtd.$listItem, dtd.$tableContent)) {
      this.dataProcessor.writer.setRules(e, {
        indent: true,
        breakBeforeOpen: true,
        breakAfterOpen: true,
        breakBeforeClose: true,
        breakAfterClose: true
      });
    }
    // this.setMode('source');
  });
  </script>
  <?
}

function image_pop($image,$fid) {
  if(file_exists($image) && isimg($image) && $gis = get_image($image) ) {
    $id = 'id_' . $fid ;
    $mw = 500 ;
    $dw = $gis['width'] > $mw ? floor($gis['width'] * $mw / $gis['width']) : $gis['width'] ;
    $dh = $gis['width'] > $mw ? floor($gis['height'] * $mw / $gis['width']) : $gis['height'] ;
    $dx = 50 ;
    $dy = ceil( $dh / 2 ) ;
    $omo = "ShowContent('{$id}',{$dx},{$dy}); return false;";
    $omx = "HideContent('{$id}'); return false;";
    $onc = "window.open('{$gis['src']}','notepop'); return false;";
    ?>
    <div style="padding:10px 0 5px 0;">
      <a title="<?= $gis['width'] . ' x ' . $gis['height']; ?>"
      onmousemove="<?= $omo ; ?>" onmouseover="<?= $omo ; ?>" onmouseout="<?= $omx ; ?>"
      onclick="<?= $onc ; ?>" href="#"><?= $gis['width'] . ' x ' . $gis['height']; ?></a> 
    </div>
    <div id="<?= $id ; ?>" style="display:none;position:absolute; border: solid 1px #C0C0C0; padding:5px; z-index:999; background-color: #E0E0E0;border-radius: 3px 3px 3px 3px;">
      <img alt="" border="0" src="<?= $gis['src'] . '?' . $gis['filemtime'] ; ?>" height="<?= $dh ; ?>" width="<?= $dw ; ?>" style=" background-color: #000000;display:block;margin:0 auto;text-align:center;"><div style="display:block;margin:5px auto;text-align:center; font-weight:normal"><?= $gis['src'] ?></div> 
    </div>
    <? 
  }
}
?>