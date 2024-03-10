<? 
@include_once('functions.inc.php');
globvars('action');
globvars('fpath','filefilt','fname','dval','selopt','selval','notopt','notval');
globvars('sq_lookt','sq_lookk','sq_lookv','sq_lookd','sq_lookl','sq_lookf');
extract($globvars);

if($action == 'jselmore') {
  // file selector
  $fpath = str_replace('|','/',$fpath);
  $files = array();
  if($handle = opendir($fpath)) {
    while(false !== ($file = readdir($handle))) {
      if( ($file == $dval) || ( (! in_array( $file, array('.','..','Thumbs.db') ) ) && ( $file && is_file(build_path($fpath,$file)) ) && ( (! $filefilt) || substr_count( strtolower($file), strtolower($filefilt) ) ) ) ) {
        $files[] = $file;
      }
    }
    closedir($handle);
    natcasesort($files);
    $files = array_values($files);
  }
  print json_encode($files);
}
elseif($action == 'jrelook' || $action == 'jreorder') {
  // lookup refresh
  $out = $urls = array();
  opendb();
  $sq_lookf = str_replace(array('&#96;','&lsquo;','&#39;','&gt;','&lt;'),array("`","`","'",'>','<'),$sq_lookf);
  $gstring = trim("SELECT * FROM `{$sq_lookt}` {$sq_lookf}");
  if( ! (substr_count(strtolower($gstring),'order by') || substr_count(strtolower($gstring),' join ')) ) {
    $dsp = $sq_lookd ;
    $pm = 0 ;
    if(substr_count($dsp,'[[')) {
      preg_match_all("/\[\[([^\]]*)\]\]/i", $dsp, $ords);
      if(isset($ords[1])) {
        $ords = $ords[1];
        if(count($ords)) {
          $ostring = "";
          foreach($ords as $ord) {
            if($ord) {
              $ostring .= " `$ord`, ";
              $pm++;
            }
          }
          if($ostring) {
            $gstring .= " ORDER BY " . substr($ostring,0,-2);
          }
        }
      }
    }
    if(! $pm) {
      if($sq_lookv && substr_count($dsp,'v') && ( ( ! substr_count($dsp,'k') ) || ( strpos($dsp,'v') < strpos($dsp,'k') ) ) ) {
        $gstring .= " ORDER BY `{$sq_lookv}`" ; // sort by value
      }
      elseif($sq_lookk) {
        $gstring .= " ORDER BY `{$sq_lookk}`" ; // sort by key
      }
    }
  }
  // print_p($gstring);
  $look = my_query($gstring);
  if($look && my_rows($look)) {
    while($opt_arr = my_array($look,MYSQL_ASSOC)) {
      // print_arr($opt_arr);
      $key = $opt_arr[$sq_lookk] ;
      $showv = $val = $opt_arr[$sq_lookv] ;
      $dsp = $sq_lookd ;
      if( substr_count($dsp,'k') || substr_count($dsp,'v') || substr_count($dsp,'[[') ) {
        $showv = $dsp ;
        $showv = str_replace('k',$key,$showv);
        $showv = str_replace('v',$val,$showv);
        $showv = rep_var($showv,$opt_arr);
      }
      $out[$opt_arr[$sq_lookk]] = str_replace(array('&#39;','&amp;','&quot;','£'),array("'",'&','"','&pound;'),$showv) ;
      $urls[$opt_arr[$sq_lookk]] = $sq_lookl ? rep_var(str_replace('..|','../',$sq_lookl),$opt_arr) : '';
    }
  }
  // print_arr($out,'out');
  // print_arr($urls,'urls');
  if($action == 'jrelook') {
    $jso['k'] = array_keys($out);
    $jso['v'] = array_values($out);
    print json_encode($jso) ;
  }
  elseif($action == 'jreorder') {
    $jshtml['sel'] = $jshtml['not'] = '';
    $seldone = array();

    // already selected keeping order
    $n = 0 ;
    if(is_array($selopt) && count($selopt)) {
      foreach($selopt as $k => $opt) {
        if(isset($out[$opt]) && $out[$opt]) {
          $seldone[] = $opt ;
          $text = '';
          $url = isset($urls[$opt]) && $urls[$opt] ? clean_url($urls[$opt]) : '';
          if($url) {
            $text .= "<a target=\"popfile\" href=\"{$url}\">";
          }
          $text .= $out[$opt];
          if($url) {
            $text .= "</a>";
          }
          $sort = $selval[$k];
          // $jshtml['sel'] .= "{$k} - {$opt} - {$sort} - {$text}<br>";
          if(! $n++) {
            $jshtml['sel'] .= '<div style="margin-bottom:10px"><u>Selected: Delete order number to unselect option (refreshed)</u></div>';
          }
          $jshtml['sel'] .= "
            <div style=\"margin-bottom:5px\" class=\"{$fname}_ssr\">
              <div style=\"display:inline-block; vertical-align:middle; margin-right:3px\">
                <input size=\"2\" type=\"text\" id=\"{$fname}_sel_{$opt}\" class=\"{$fname}_sel\" name=\"{$fname}_ssord[]\" value=\"{$sort}\" onchange=\"fldchg++;\" autocomplete=\"off\">
                <input type=\"hidden\" name=\"{$fname}[]\" value=\"{$opt}\"> 
              </div>
              <div style=\"display:inline-block; vertical-align:middle\"><span class=\"{$fname}_sst\">" . cliptext(clean_amp($text),90) . "</span></div>
            </div>
          ";
        }
      }
    }

    // not selected but numbers entered
    $n = 0 ;
    if(is_array($notopt) && count($notopt)) {
      foreach($notopt as $k => $opt) {
        if(isset($out[$opt]) && $out[$opt] && ($sort = $notval[$k])) {
          $seldone[] = $opt ;
          $text = '';
          $url = isset($urls[$opt]) && $urls[$opt] ? $urls[$opt] : '';
          if($url) {
            $text .= "<a target=\"popfile\" href=\"{$url}\">";
          }
          $text .= $out[$opt];
          if($url) {
            $text .= "</a>";
          }
          // $jshtml['not'] .= "{$k} - {$opt} - {$sort} - {$text}<br>";
          if(! $n++) {
            $jshtml['not'] .= '<div style="margin-bottom:10px"><u>Not selected: Enter order number to select option (refreshed)</u></div>';
          }
          $jshtml['not'] .= "
            <div style=\"margin-bottom:5px\" class=\"{$fname}_ssr\">
              <div style=\"display:inline-block; vertical-align:middle; margin-right:3px\">
                <input size=\"2\" type=\"text\" id=\"{$fname}_sel_{$opt}\" class=\"{$fname}_sel\" name=\"{$fname}_ssord[]\" value=\"{$sort}\" onchange=\"fldchg++;\" autocomplete=\"off\">
                <input type=\"hidden\" name=\"{$fname}[]\" value=\"{$opt}\"> 
              </div>
              <div style=\"display:inline-block; vertical-align:middle\"><span class=\"{$fname}_sst\">" . cliptext(clean_amp($text),90) . "</span></div>
            </div>
          ";
        }
      }
    }

    foreach($out as $opt => $v) {
      if(isset($out[$opt]) && $out[$opt] && ! in_array($opt, $seldone)) {
        // not aleady listed
        $text = $sort = '';
        $k = 0 ;
        $url = isset($urls[$opt]) && $urls[$opt] ? $urls[$opt] : '';
        if($url) {
          $text .= "<a target=\"popfile\" href=\"{$url}\">";
        }
        $text .= $out[$opt];
        if($url) {
          $text .= "</a>";
        }
        if(in_array($opt, $notopt)) {
          $k = array_search($opt, $notopt);
          $sort = $notval[$k];
        }
        // $jshtml['not'] .= "{$k} - {$opt} - {$sort} - {$text}<br>";
        if(! $n++) {
          $jshtml['not'] .= '<div style="margin-bottom:10px"><u>Not selected: Enter order number to select option (refreshed)</u></div>';
        }
        $jshtml['not'] .= "
          <div style=\"margin-bottom:5px\" class=\"{$fname}_ssr\">
            <div style=\"display:inline-block; vertical-align:middle; margin-right:3px\">
              <input size=\"2\" type=\"text\" id=\"{$fname}_sel_{$opt}\" class=\"{$fname}_sel\" name=\"{$fname}_ssord[]\" value=\"{$sort}\" onchange=\"fldchg++;\" autocomplete=\"off\">
              <input type=\"hidden\" name=\"{$fname}[]\" value=\"{$opt}\"> 
            </div>
            <div style=\"display:inline-block; vertical-align:middle\"><span class=\"{$fname}_sst\">" . cliptext(clean_amp($text),90) . "</span></div>
          </div>
        ";
      }
    }

    print json_encode($jshtml) ;
  }
}