<? 
// https://phpspreadsheet.readthedocs.io/en/develop/topics/accessing-cells/
$psh = (float)phpversion() >= 7.2 ? '../scripts/phpspreadsheet7/vendor/autoload.php' : '../scripts/phpspreadsheet/vendor/autoload.php' ;
if(file_exists($psh)) { @include_once($psh); } else { $xstart = 'csv'; }
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$debug = isvar($globvars['debug'],0) ;
$memtot = isvar($globvars['expvars']['memtot'],0) ;
$dstamp = isvar($globvars['expvars']['dstamp'],1) ;
$maxlen = isvar($globvars['expvars']['maxlen'],50) ;
$maxtext = isvar($globvars['expvars']['maxtext'],70) ;
$xformat = isvar($globvars['expvars']['xformat'],'xlsx') ;
$exportv = isvar($globvars['expvars']['lookv'],'v') ; // override lookv : k=key, b=both(two columns k & v), d=specific(two columns k & [[]])

ob_end_clean();
// filter & lookups
$globvars['c_rows'] = $globvars['fnames'] = $globvars['ftypes'] = $globvars['fprmss'] = $globvars['heads'] = $globvars['funcs'] = $globvars['totals'] = array();
foreach($globvars['l_arr'] as $c_row) {
  $c = $c_row['col'];
  $globvars['c_rows'][$c] = $c_row;
  $head = '';
  if( isarr($sq_export) ) {
    // heads array 
    $head = isvar($sq_export[$c]) ? $sq_export[$c] : '' ;
  }
  elseif(($strlen = strlen($sq_export)) > 0) {
    // matching keys
    for( $i = 0; $i <= $strlen; $i++ ) {
      if(sq_keys($c,substr( $sq_export, $i, 1 ))) {
        $head = isvar($sq_names[$c]) ? $sq_names[$c] : $c_row['fname'] ;
        break ;
      }
    }
  }
  else {
    // all fields
    $head = isvar($sq_names[$c]) ? $sq_names[$c] : $c_row['fname'] ;
  }
  if( $head )  {
    if( sq_keys($c,'o') || sq_keys($c,'s') ) {

      // actual field column
      $globvars['cnums'][] = $c;
      $globvars['fnames'][] = $c_row['fname'];
      if($exportv == 'k' || $exportv == 'b' || $exportv == 'd') {
        // overrride this column to k as next column is detail
        $globvars['lookd'][] = 'k' ;
        $globvars['ftypes'][] = $c_row['ftype'];
      }
      else {
        $globvars['lookd'][] = $globvars['sq_lookd'][$c] ;
        $globvars['ftypes'][] = sq_keys($c,'s') ? 'lookmult' : 'lookup'  ;
      }
      $globvars['fprmss'][] = $c_row['fprms'];
      $globvars['heads'][] = $head;
      $globvars['funcs'][] = isset($globvars['sq_funct'][$c]) && $globvars['sq_funct'][$c] ? $globvars['sq_funct'][$c] : '' ;
      $globvars['totals'][] = isset($globvars['sq_exptot'][$c]) && $globvars['sq_exptot'][$c] ? $globvars['sq_exptot'][$c] : '' ;

      if($exportv == 'b' || $exportv == 'd') {
        // extra column if override b or d
        $globvars['cnums'][] = $c;
        $globvars['fnames'][] = $c_row['fname'];
        $globvars['ftypes'][] = sq_keys($c,'s') ? 'lookmult' : 'lookup';
        $globvars['lookd'][] = 'v' ;
        $globvars['fprmss'][] = '';
        $globvars['heads'][] = "[{$head}]" ;
        $globvars['funcs'][] = '';
        $globvars['totals'][] = '';
      }
    }
    else {
      $globvars['cnums'][] = $c;
      $globvars['fnames'][] = $c_row['fname'];
      $globvars['ftypes'][] = $c_row['ftype'];
      $globvars['lookd'][] = $globvars['sq_lookd'][$c] ;
      $globvars['fprmss'][] = $c_row['fprms'];
      $globvars['heads'][] = $head;
      $globvars['funcs'][] = isset($globvars['sq_funct'][$c]) && $globvars['sq_funct'][$c] ? $globvars['sq_funct'][$c] : '' ;
      $globvars['totals'][] = isset($globvars['sq_exptot'][$c]) && $globvars['sq_exptot'][$c] ? $globvars['sq_exptot'][$c] : '' ;
    }
  }
}
if($debug) {
  print_arr($globvars['cnums'],'cnums');
  print_arr($globvars['fnames'],'fnames');
  print_arr($globvars['ftypes'],'ftypes');
  print_arr($globvars['lookd'],'lookd');
  print_arr($globvars['fprmss'],'fprmss');
  print_arr($globvars['heads'],'heads');
  print_arr($globvars['funcs'],'funcs');
  print_arr($globvars['totals'],'totals');
}

// returns $fstring and creates $globvars['order'] and $globvars['order_arr']
$fstring = get_fstring();

$astar = isset($globvars['sq_astar']) && $globvars['sq_astar'] ? $globvars['sq_astar'] : '*';
// aes_decrypt
if(defined('DBKEY')) {
  foreach($globvars['l_arr'] as $c_row) {
    if(sq_keys($c_row['col'],'i')) {
      $astar .= ", AES_DECRYPT(`{$c_row['fname']}`, '" . DBKEY . "') as `{$c_row['fname']}_decrypted`";
    }
  }
}
$qstring = "SELECT {$astar} FROM `$sq_table` $fstring {$globvars['order']}" ;
if($xstart && $xmax && ($xstart !== 'csv')) { 
  $xstart--;
  $qstring .= " LIMIT {$xstart}, {$xmax}"; 
}
elseif(isset($sq_limit) && $sq_limit) { 
  $qstring .= " LIMIT {$sq_limit}"; 
}
$items = my_query($qstring);
$num = my_rows($items);

$data = array();
$n = $fgo = 0 ;
while($a_row = my_array($items,'assoc')) {
  $c = 0 ;
  // get entire i_row and strip table. from assoc array
  foreach($globvars['fnames'] as $fname) {
    $cn = $globvars['cnums'][$c];

    $i_row[$fname] = ! substr_count($globvars['c_rows'][$cn]['sq_keys'],'z') ? $a_row[$sq_table.'.'.$fname] : '' ;
    $c_row = $globvars['c_rows'][$cn] ;
    // aes_decrypt
    if(sq_keys($c_row['col'],'i') && isset($a_row['.'.$fname . '_decrypted'])) {
      $i_row[$fname] = $a_row['.'.$fname . '_decrypted'];
    }
    if(sq_keys($cn,'k')) {
      $fgo = $i_row[$c_row['fname']] ;
    }
    $c++;
  }
  $c = 0 ;
  foreach($globvars['fnames'] as $fname) {
    $cn = $globvars['cnums'][$c];
    $len = strlen($i_row[$fname]) ;
    $dpf = true;
    if( isset($globvars['funcs'][$c]) && ($funct = $globvars['funcs'][$c]) && function_exists($funct) ) {
      // use function
      $c_row = $globvars['c_rows'][$cn] ;
      globvadd( 
        'c_row', $c_row,
        'i_row', $i_row,
        'a_row', $a_row,
        'c', $cn,
        's', $n,
        'thiscol', $fname,
        'fname', $fname,
        'fnamev', isvar($globvars[$fname]),
        'ftype', $c_row['ftype'],
        'fpath', getfpath($c_row['sq_fpath']),
        'fprms', $c_row['fprms'],
        'dval', $i_row[$fname]
      );
      $dpf = $funct();
      if($dpf !== true) {
        $data[$n][$fname] = $fl = trim(xclean_xls(ob_get_clean())) ;
        $len = strlen($fl) ;
        ob_start();
      }
    }
    if($dpf === true) {
      $data[$n][$fname] = $i_row[$fname] ;
      if( $globvars['ftypes'][$c] == 'lookup' ) { 
        // option lookup
        $dval = $i_row[$fname] ;
        $dval = join_multi($cn,$fgo,$dval);
        $dval = (is_array($dval) && count($dval) && $dval[0] ) ? $dval[0] : $i_row[$fname] ;          
        $data[$n][$fname] = $dval ;
        $data[$n]["{$fname}_lookup"] = '' ;
        if( isset($globvars['lookups'][$fname]['sq_arr'][$dval]) ) {
          $opt_arr = $globvars['lookups'][$fname]['sq_arr'][$dval] ;
          $key = $opt_arr[$sq_lookk[$cn]] ;
          $val = $opt_arr[$sq_lookv[$cn]] ;
          $dsp = $globvars['lookd'][$c] ;
          if( substr_count($dsp,'k') || substr_count($dsp,'v') || substr_count($dsp,'[[') ) {
            $showv = $dsp ;
            $showv = rep_var($showv,$opt_arr);
            if($showv == $dsp) {
              $showv = str_replace('k',$key,$showv);
              $showv = str_replace('v',$val,$showv);
            }
            $showv = cliptext($showv);
          }
          else {
            $showv = $val ;
          }
          $data[$n]["{$fname}_lookup"] = $showv ;
          $len = strlen($data[$n]["{$fname}_lookup"]) ;
        }
      }
      elseif( $globvars['ftypes'][$c] == 'lookmult' ) { 
        // multi lookup
        $data[$n][$fname] = '' ;
        $data[$n]["{$fname}_lookup"] = '' ;
        if( isset($globvars['lookups'][$fname]['sq_arr']) ) {
          $vals = join_multi($cn,$fgo,$i_row[$fname]);
          foreach($vals as $tval) {
            if(isset($globvars['lookups'][$fname]['sq_arr'][$tval])) {
              $opt_arr = $globvars['lookups'][$fname]['sq_arr'][$tval] ;
              $key = $opt_arr[$sq_lookk[$cn]] ;
              $val = $opt_arr[$sq_lookv[$cn]] ;
              $dsp = $globvars['lookd'][$c] ;
              if( substr_count($dsp,'k') || substr_count($dsp,'v') || substr_count($dsp,'[[') ) {
                $showv = $dsp ;
                $showv = rep_var($showv,$opt_arr);
                if($showv == $dsp) {
                  $showv = str_replace('k',$key,$showv);
                  $showv = str_replace('v',$val,$showv);
                }
                $showv = cliptext($showv);
              }
              else {
                $showv = $val ;
              }
              if($exportv == 'd' || $exportv == 'b') {
                $data[$n][$fname] .= $key . "\r\n" ;
                $data[$n]["{$fname}_lookup"] .= $showv . "\r\n" ;
                if(strlen($key) > $len) {
                  $len = strlen($key) ;
                }
              }
              else {
                $data[$n][$fname] .= $showv . "\r\n" ;
                $data[$n]["{$fname}_lookup"] .= $showv . "\r\n" ;
                if(strlen($showv) > $len) {
                  $len = strlen($showv) ;
                }
              }
            }
          }
          // print_p("[$n] $fname  = {$data[$n][$fname]}");
          // print_p("[$n] {$fname}_lookup = {$data[$n]["{$fname}_lookup"]}");
        }
      }
      elseif( substr_count( $globvars['ftypes'][$c],'datetime' ) ) { 
        $data[$n][$fname] = cdate($data[$n][$fname],'d/m/Y H:i:s','') ;
        $len = strlen($data[$n][$fname]);
      }
      elseif( substr_count( $globvars['ftypes'][$c],'date' ) ) { 
        $data[$n][$fname] = cdate($data[$n][$fname],'d/m/Y','') ;
        $len = strlen($data[$n][$fname]);
      }
    }
    $c++ ;
  }
  $n++;
}
if($debug) { 
  print_p($qstring);
  print_p( 'Rows: ' . $num );
  print_p( 'Data: ' . count($data) );
  print_arr($globvars['lookups'],'lookups');
  print_arr($globvars['fnames'],'fnames');
  print_arr($globvars['ftypes'],'ftypes');
  print_arr($globvars['fprmss'],'fprmss');
  print_arr($globvars['heads'],'heads');
  print_arr($globvars['funcs'],'funcs');
  print_arr($globvars['totals'],'totals');
  print_arr($data,'data');
  die ;
}
if(! count($data)) {
  die('No results found');
}

if($xstart === 'csv') {
  // CSV
  $xfile = $dstamp ? $sq_table . date('_Y-m-d_H-i') : $sq_table ;
  headerCSV($xfile);
  $fp = fopen('php://output', 'w');
  $row = [];
  $c = 0 ;
  foreach($globvars['fnames'] as $fname) {
    $row[] = clean_upper(str_replace(array('-','_'),' ',xclean_csv($globvars['heads'][$c])));
    $c++;
  }
  fputcsv($fp, $row); 
  foreach($data as $i_row) {
    $row = [];
    $c = 0 ;
    foreach($globvars['fnames'] as $fname) {
      $row[] = trim( xclean_csv( in_array( $globvars['ftypes'][$c], array('lookup','lookmult') ) && isset($i_row["{$fname}_lookup"]) ? $i_row["{$fname}_lookup"] : $i_row[$fname] ) ) ;
      $c++;
    }
    fputcsv($fp, $row);
  }
  fclose($fp);
}
else {
  // spreadsheet
  $globvars['addlast'] = 1 ;
  $format_date = array('numberformat' => array('code' => 'dd/mm/yyyy',),);
  $format_datetime = array('numberformat' => array('code' => 'dd/mm/yyyy hh:mm',),);

  $spreadsheet = new Spreadsheet();
  $sheet = $spreadsheet->getActiveSheet();

  // header row
  $c = $r = 0 ;
  $lengths = $formats = array();
  foreach($globvars['fnames'] as $fname) {
    $header = clean_upper(str_replace(array('-','_'),' ',xclean_xls($globvars['heads'][$c])));
    $lengths[$c] = strlen($header)+5;
    $formats[$c] = '';
    $spreadsheet->getActiveSheet()->setCellValue(getCell($c,$r), $header);
    $c++;
  }
  $r++;

  // data
  foreach($data as $i_row) {
    $c = 0 ;
    foreach($globvars['fnames'] as $fname) {
      $cell = getCell($c,$r);
      $value = trim( xclean_xls( in_array( $globvars['ftypes'][$c], array('lookup','lookmult') ) && isset($i_row["{$fname}_lookup"]) ? $i_row["{$fname}_lookup"] : $i_row[$fname] ) ) ;
      
      // format
      if(is_numeric($value)) {
        $ladd = 0 ;
        $spreadsheet->getActiveSheet()->setCellValue($cell, $value);
        if($c && ($formats[$c] != 'char')) { $formats[$c] = 'numeric'; }
      }
      else {
        $ladd = 5 ;
        $spreadsheet->getActiveSheet()->setCellValueExplicit($cell, $value, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $formats[$c] = 'char';
      }

      // length
      if(substr_count($value,"\r\n")) {
        $larr = safe_explode("\r\n",$value);
        $leng = max(array_map('strlen', $larr)) + $ladd;
        $formats[$c] = 'char';
      }
      else {
        $leng = strlen($value) + $ladd;
      }
      if($leng > $lengths[$c]) {
        $lengths[$c] = $leng;
      }

      $c++;
    }
    if( ($r/1000 == floor($r/1000)) && mem_limit(0.75) ) { break ; }
    $r++;
  }
  $globvars['addlast'] = 0 ;

  // widths and formatting
  $c = $totline = 0 ;
  foreach($globvars['fnames'] as $fname) {

    $col =  getCell($c,1).':'.getCell($c,$globvars['lastrow']+2) ;
    $col1 = getCell($c,0).':'.getCell($c,$globvars['lastrow']+2) ;

    // format and alignment
    $fmt = 'na';
    if(substr_count( $globvars['ftypes'][$c],'decimal')) {
      if(substr_count($globvars['fprmss'][$c],',')) {
        $p = strpos($globvars['fprmss'][$c],',')+1;
        $dn = substr($globvars['fprmss'][$c],$p);
        $fmt = '#,##0.' . str_pad ( '0', $dn, '0', STR_PAD_LEFT ) ;
      }
      else {
        $fmt = '#,##0.00';
      }
      $spreadsheet->getActiveSheet()->getstyle($col)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\style\Alignment::HORIZONTAL_RIGHT);
    }
    elseif(substr_count( $globvars['ftypes'][$c],'datetime')) {
      $fmt = 'format_datetime' ;
    }
    elseif(substr_count( $globvars['ftypes'][$c],'date')) {
      $fmt = 'format_date' ;
    }
    elseif($formats[$c] == 'numeric') {
      $spreadsheet->getActiveSheet()->getstyle($col)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\style\Alignment::HORIZONTAL_RIGHT);
    }
    else {
      if(! $c) {
        $spreadsheet->getActiveSheet()->getstyle($col)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\style\Alignment::HORIZONTAL_LEFT);
      }
      $spreadsheet->getActiveSheet()->getstyle($col)->getAlignment()->setWrapText(true);
    }

    // set format
    if(isset($$fmt)) {
      $spreadsheet->getActiveSheet()->getstyle($col)->applyFromArray($$fmt);
    }
    elseif($fmt != 'na') {
      $spreadsheet->getActiveSheet()->getstyle($col)->getNumberFormat()->setFormatCode($fmt);
    }

    // vertical align
    $spreadsheet->getActiveSheet()->getstyle($col1)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\style\Alignment::VERTICAL_TOP);

    // cell widths
    $flen = $lengths[$c] > 100 ? 100 : $lengths[$c] ;
    $spreadsheet->getActiveSheet()->getColumnDimension(getCell($c))->setWidth($flen);

    // totals
    if($globvars['totals'][$c]) {
      $totline++;
      $sum =  getCell($c,1).':'.getCell($c,$globvars['lastrow']) ;
      $spreadsheet->getActiveSheet()->setCellValue(getCell($c,$globvars['lastrow']+2),"=SUM({$sum})");
    }
    
    $c++;
  }

  // totals
  if($totline) {
    $totrow = getCell(0,$globvars['lastrow']+2).':'.getCell($globvars['lastcol'],$globvars['lastrow']+2) ;
    $ttext = $memtot ? 'TOTALS (' . memory_get_peak_usage() . ' )' : 'TOTALS' ;
    $spreadsheet->getActiveSheet()->setCellValue(getCell(0,$globvars['lastrow']+2), $ttext);
    $spreadsheet->getActiveSheet()->getstyle($totrow)->getFont()->setBold(true);
    $spreadsheet->getActiveSheet()->getstyle($totrow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\style\Alignment::HORIZONTAL_RIGHT);
  }

  // header
  $spreadsheet->getActiveSheet()->freezePane('A2');
  $spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(25);
  $toprow = getCell(0,0).':'.getCell($globvars['lastcol'],0) ;
  $spreadsheet->getActiveSheet()->getstyle($toprow)->getFont()->setBold(true);

  $xfile = $dstamp ? $sq_table . date('_Y-m-d_H-i') : $sq_table ;
  if($xformat == 'xlsx') {
    $xfile .= '.xlsx';
    headerExcel($xfile);
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
  }
  else {
    $xfile .= '.xls';
    headerExcel($xfile);
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xls($spreadsheet);
  }
  $writer->save('php://output');    
}
die;

function headerCSV($xfile) {
  header("Content-type: text/csv");
  header("Content-Disposition: attachment; filename={$xfile}.csv");
  header("Expires: 0");
  header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
  header("Pragma: public");
}

function headerExcel($xfile) {
  header("Content-Disposition: attachment; filename=$xfile" );
  if(substr_count($xfile,'.xlsx')) {
    header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
  }
  else {
    header("Content-type: application/vnd.ms-excel");
  }
  header('Content-Transfer-Encoding: binary');
  header("Expires: 0");
  header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
  header("Pragma: public");
}

function getCell($col,$row=-1) {
  global $globvars ;
  // column
  if($globvars['addlast'] && ((! isset($globvars['lastcol'])) || ($col > $globvars['lastcol']))) {
    $globvars['lastcol'] = $col;
  }
  if($col<26) {
    $out = chr($col+65) ;
  }
  else {
    $col -= 26 ;
    $col1 = floor($col/26) ;
    $col2 = $col - ($col1 * 26) ;
    $out = chr($col1+65) . chr($col2+65) ;
  }
  // row
  if($row >= 0) { 
    $row++;
    if($globvars['addlast'] && ((! isset($globvars['lastrow'])) || ($row > $globvars['lastrow']))) {
      $globvars['lastrow'] = $row;
    }
    $out .= $row ;
  }
  return $out ; 
}

function xclean_csv($in) {
  $fr = array('&quot;', '&rsquo;', '&ldquo;', '&lsquo;', '&rdquo;', '&bull;', '&hellip;', '&amp;', '&pound;', '%20', '<br>', '&#39;');
  $to = array('"', "'", '"', "'", "'", '-', '...', '&', '£', ' ', "\r\n", "'");
  return mb_convert_encoding( str_ireplace(  array('&lt;', '&gt;'), array('<', '>'), strip_tags( str_ireplace( $fr, $to, $in ) ) ), 'ISO-8859-1', 'UTF-8' );
}

function xclean_xls($in) {
  $fr = array('&quot;', '&rsquo;', '&ldquo;', '&lsquo;', '&rdquo;', '&bull;', '&hellip;', '&amp;', '&pound;', '%20', '<br>', '&#39;');
  $to = array('"', "'", '"', "'", "'", '-', '...', '&', '£', ' ', "\r\n", "'");
  return mb_convert_encoding( str_ireplace(  array('&lt;', '&gt;'), array('<', '>'), strip_tags( str_ireplace( $fr, $to, $in ) ) ), 'UTF-8', 'ISO-8859-1' );
}
?>
