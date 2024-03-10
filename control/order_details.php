<? @include_once('head.inc.php'); ?>
<!DOCTYPE html>
<html lang="en"> 
  <head>
  <?
  globvars('action','send','go');
  if(($globvars['action'] == 'edit') && $globvars['send'] && $globvars['go'] && $ord = order_find($globvars['go'])) {
    $globvars['countries'] = countries('ship_countries','sc_code','sc_name');
    $globvars['states'] = countries('ship_states','ss_code','ss_name');
    // print_arv($ord);
    order_email($ord,$globvars['send']);
    $globvars['msg'] = ucwords($globvars['send']) . " email sent";
  }

  $globvars['order_pay'] = $globvars['order_items'] = [];
  $string = "select * from `order_items` order by `order_ref`";
  $query = my_query($string);
  while($row = my_assoc($query)) {
    $globvars['order_items'][$row['order_ref']][$row['item_ref']] = $row;
  }
  $payfn = '';
  if(isset($globvars['sagepay'])) {
    $payfn = 'sage_pay';
    $string = "select * from `order_sage` order by `order_ref`";
    $query = my_query($string);
    while($row = my_assoc($query)) {
      $globvars['order_pay'][$row['order_ref']][$row['id']] = $row;
    }
  }
  elseif(isset($globvars['trustpay'])) {
    $payfn = 'trust_pay';
    $string = "select * from `order_trust` order by `tr_ordref`";
    $query = my_query($string);
    while($row = my_assoc($query)) {
      $globvars['order_pay'][$row['tr_ordref']][$row['tr_id']] = $row;
    }
  }

  $globvars['debug'] = 0 ;
  $globvars['sq_table'] = 'order_details'; // table name
  // a=auto inc, b=break before (bb save), c=form edit, d=text disp, e=edit, f=file (+j=more), g=clean_urln, h=hide value, 
  // k=key, l=list, m=md5 entry, n=zero last, o=opts from table, p=color picker, q=filter, r=add similar, s=select multiple (ss sort), 
  // t=now(), u=link, v=view, w=null if blank, x=noshow, y=ckeditor, z=fake, 100=length/rows, _100=maxtext, (i not used)
  $globvars['sq_keys'] = array(
    'lkuav','lv','vo',
    'bzv','zv','zv','zv',
    'ble','le','e','e','e','e','eo','e','eo','ble','e','e',
    'be','e','e','e','e','e','eo','e','eo','e',
    'bbvox','vx','vx',
    'bvx','vx','vx','vx','vx','vx','vx','vx','vx',
    'bvx','vx','vx','vx','vx','vx','vx','vx','vx','lvx',
    'bvx','vx','vx','vx','vx','vx','vx','vx','vx','vx','vx',
    'bv','lv','lv','v',
    'ble','le','le','e','x','e','zv',
    'be'
  ); // field keys
  $globvars['sq_names'] = array(
    'Ref','Date','Account',
    'Address','Totals','Items','Paylog',
    'Forename','Surname','Company','Address 1','Address 2','City','State','Postcode','Country','Email','Phone','Mobile',
    'Forename','Surname','Company','Address 1','Address 2','City','State','Postcode','Country','Delivery Notes',
    'Voucher Code','Voucher Text','Shipping',
    'VAT Rate','Items','Voucher','Subtotal','Net','VAT','Gross','Shipping','Total',
    'Currency','Rate','Items','Voucher','Subtotal','Net','VAT','Gross','Shipping','Total',
    '','','','','','','','','','','',
    'Trans ID','Trans Ref','Total Paid','Discount',
    'Processing','Cancelled','Despatched','Reference','Response','Tracking','Sprint',
    'Notes'
  ); // field names
  $globvars['sq_notes'] = array(
    '','','',
    '','','','',
    '','','','','','','','','','','','',
    '','','','','','','','','','',
    '','','',
    '','','','','','','','','',
    '','','','','','','','','','',
    '','','','','','','','','','','',
    '','','','',
    '<a style="width:100px" href="order_details.php?action=edit&amp;send=order&amp;go=[[order_ref]]">ORDER EMAIL</a>','','<a style="width:100px" href="order_details.php?action=edit&amp;send=despatch&amp;go=[[order_ref]]">DESPATCH EMAIL</a>','','','','',
    'Private for information only'
  ); // field notes
  $globvars['sq_notei'] = array(); // popup image
  $globvars['sq_lookt'] = array(
    '','','user_details',
    '','','','',
    '','','','','','','ship_states','','ship_countries','','','',
    '','','','','','','ship_states','','ship_countries','',
    'vouchers','','',
    '','','','','','','','','',
    '','','','','','','','','','',
    '','','','','','','','','','','',
    '','','','',
    '','','','','','','',
    ''
  ); // opt tables
  $globvars['sq_lookk'] = array(
    '','','u_id',
    '','','','',
    '','','','','','','ss_code','','sc_code','','','',
    '','','','','','','ss_code','','sc_code','',
    'v_id','','',
    '','','','','','','','','',
    '','','','','','','','','','',
    '','','','','','','','','','','',
    '','','','',
    '','','','','','','',
    ''
  ); // opt keys
  $globvars['sq_lookv'] = array(
    '','','u_email',
    '','','','',
    '','','','','','','ss_name','','sc_name','','','',
    '','','','','','','ss_name','','sc_name','',
    'vcode','','',
    '','','','','','','','','',
    '','','','','','','','','','',
    '','','','','','','','','','','',
    '','','','',
    '','','','','','','',
    ''
  ); // opt values
  $globvars['sq_lookd'] = array(
    '','','v',
    '','','','',
    '','','','','','','v','','v','','','',
    '','','','','','','v','','v','',
    'v','','',
    '','','','','','','','','',
    '','','','','','','','','','',
    '','','','','','','','','','','',
    '','','','',
    '','','','','','','',
    ''
  ); // eg. 'k : v' or [[field]]
  $globvars['sq_lookl'] = array(); // ss multi link
  $globvars['sq_lookf'] = array(); // opt query eg. "WHERE `key` = 'x'" (or "WHERE `key` = '[[value]]'" only where $go)
  $globvars['sq_joint'] = array(); // multi join tables
  $globvars['sq_joink'] = array(); // multi join keys
  $globvars['sq_joinv'] = array(); // multi join values
  $globvars['sq_joino'] = array(); // multi join order (if ss)
  $globvars['sq_fpath'] = array(); // extra file paths
  $globvars['sq_fmake'] = array(); // arrnum[v this]-width-height-[mecpf]-[qual,85]-[del,y1/n0]-[force overwrite,y1/n0]
  $globvars['sq_deflt'] = array(); // default values
  $globvars['sq_funct'] = array(
    '','','',
    'order_address','order_totals','order_items',$payfn,
    '','','','','','','','','','','','',
    '','','','','','','','','','',
    '','','',
    '','','','','','','','','',
    '','','','','','','','','','',
    '','','','','','','','','','','',
    '','','','',
    '','','','','','','check_sprint',
    ''
  ); // call functions
  $globvars['sq_jcall'] = array(); // call jquery
  $globvars['sq_heads'] = array(
    '','','',
    '','','','',
    'Billing','','','','','','','','','','','',
    'Delivery','','','','','','','','','',
    'Order','','',
    'GBP','','','','','','','','',
    'Currency','','','','','','','','','',
    'Finance','','','','','','','','','','',
    'Payment','','','',
    'Logistics','','','','','','',
    ''
  ); // break headings (where 'b')
  $globvars['sq_style'] = array(); // style override

  $globvars['sq_export'] = ''; // export heads array or match keys eg. 'le' or '' for all
  $globvars['sq_exptot'] = ''; // export totals array or '' for none
  $globvars['sq_list'] = ''; // column order array or '' for default

  $globvars['sq_dsort'] = 'order_ref_DESC'; // default sort (reverse _DESC)
  $globvars['sq_ajoin'] = "" ; // join filter string for list
 
  $globvars['plogo'] = $globvars['admin_logo'] ; // logo
  $globvars['ptitle'] =  'Orders' ; // page title
  $globvars['adminm'] = 'index.php' ; // admin menu
  $globvars['public'] = '../' ; // public page
  $globvars['pubtext'] = '' ; // alternate button
  $globvars['publicid'] = '' ; // public page id
  $globvars['publicfld'] = '' ; // public page field or array
  $globvars['publicfjn'] = '' ; // join for publicfld
  $globvars['maxdisp'] = 50 ; // max display in list
  $globvars['maxbox'] = 50 ; // max edit box size
  $globvars['maxtext'] = 100 ; // max text length in list
  $globvars['maxbutts'] = 15 ; // max next links in list
  $globvars['mainwidth'] = 1300 ; // main width
  $globvars['listwidth'] = 1300 ; // list width
  $globvars['formwidth'] = 1300 ; // form width
  $globvars['formleftc'] = 150 ; // form left column
  $globvars['formrghtc'] = 180 ; // form right column
  $globvars['textarows'] = 3 ; // textarea rows
  $globvars['textacols'] = 55 ; // textarea cols

  $globvars['filepath'] = '../images/' ; // file path
  $globvars['fprefpadd'] = 0 ;// add record ref to filepath (number pad zeroes OR 0 = n/a)
  $globvars['filefilt'] = '' ; // filter filenames in selector
  $globvars['allowdel'] = 0 ; // allow delete
  $globvars['allowadd'] = 0 ; // allow add
  $globvars['allowsim'] = 0 ; // add similar (fields = r)
  $globvars['edlink'] = 4 ; // edit link (number pad zeroes OR text, '' default Edit)
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

  function check_sprint() {
    global $globvars;
    if($globvars['go'] && $globvars['i_row']['s_ref']) {
      $sprint = sprint_get_order($globvars['go'],$globvars['i_row']['s_ref']);
      print_arr($sprint);
    }
  }
  
  function order_address() {
    global $globvars;
     // print_arr($globvars['lookups']['bill_country']['sq_arr']);
    ?>
    <table cellpadding="4" cellspacing="0" class="tablen" style="width:calc(100% - 10px);margin:5px;">
      <tr valign="top">
        <td width="50%">
          <b><u>Billing Address</u></b><br><br>
          <?
          print $globvars['i_row']['bill_forename'] . ' ' . $globvars['i_row']['bill_surname'] ;
          print '<br>' . $globvars['i_row']['bill_address1'] ;
          if($globvars['i_row']['bill_address2']) { print '<br>' . $globvars['i_row']['bill_address2'] ; }
          if($globvars['i_row']['bill_city']) { print '<br>' . $globvars['i_row']['bill_city'] ; }
          if(isset($globvars['lookups']['bill_state']['sq_arr'][$globvars['i_row']['bill_state']])) {
            print '<br>' . $globvars['lookups']['bill_state']['sq_arr'][$globvars['i_row']['bill_state']]['ss_name'];
          }
          print '<br>' . $globvars['i_row']['bill_postcode'] ;
          if(isset($globvars['lookups']['bill_country']['sq_arr'][$globvars['i_row']['bill_country']])) {
            print '<br>' . $globvars['lookups']['bill_country']['sq_arr'][$globvars['i_row']['bill_country']]['sc_name'];
          }
          ?>
          <br><br>
          <table class="tablen" cellpadding="0" cellspacing="0" >
            <tr>
              <td style="padding-right:20px;"><b>Email:</b></td>
              <td><?= $globvars['i_row']['bill_email'] ?></td>
            </tr>
            <? if($globvars['i_row']['bill_phone']) { ?>
            <tr>
              <td style="padding-right:20px;"><b>Phone:</b></td>
              <td><?= $globvars['i_row']['bill_phone'] ?></td>
            </tr>
            <? } if($globvars['i_row']['bill_mobile']) { ?>
            <tr>
              <td style="padding-right:20px;"><b>Mobile:</b></td>
              <td><?= $globvars['i_row']['bill_mobile'] ?></td>
            </tr>
            <? } ?>
          </table>
        </td>
        <td width="50%">
          <b><u>Delivery Address</u></b><br><br>
          <?
          print $globvars['i_row']['deliv_forename'] . ' ' . $globvars['i_row']['deliv_surname'] ;
          print '<br>' . $globvars['i_row']['deliv_address1'] ;
          if($globvars['i_row']['deliv_address2']) { print '<br>' . $globvars['i_row']['deliv_address2'] ; }
          if($globvars['i_row']['deliv_city']) { print '<br>' . $globvars['i_row']['deliv_city'] ; }
          if(isset($globvars['lookups']['deliv_state']['sq_arr'][$globvars['i_row']['deliv_state']])) {
            print '<br>' . $globvars['lookups']['deliv_state']['sq_arr'][$globvars['i_row']['deliv_state']]['ss_name'];
          }
          print '<br>' . $globvars['i_row']['deliv_postcode'] ;
          if(isset($globvars['lookups']['deliv_country']['sq_arr'][$globvars['i_row']['deliv_country']])) {
            print '<br>' . $globvars['lookups']['deliv_country']['sq_arr'][$globvars['i_row']['deliv_country']]['sc_name'];
          }
          ?>
          <br><br>
          <table class="tablen" cellpadding="0" cellspacing="0" >
            <tr>
              <td style="padding-right:20px;"><b>Shipping:</b></td>
              <td><?= $globvars['i_row']['ship_text'] ?></td>
            </tr>
            <? if($globvars['i_row']['vdisc_code']) { ?>
            <tr>
              <td style="padding-right:20px;"><b>Voucher:</b></td>
              <td><?= $globvars['i_row']['vdisc_code'] . ' - ' . $globvars['i_row']['vdisc_text'] ?></td>
            </tr>
            <? } if($globvars['i_row']['deliv_notes']) { ?>
            <tr>
              <td style="padding-right:20px;"><b>Notes:</b></td>
              <td><?= disp($globvars['i_row']['deliv_notes']) ?></td>
            </tr>
            <? } ?>
          </table>
        </td>
      </tr>
    </table>
    <br>
    <?
  }

  function order_totals() {
    global $globvars;
    ?>
    <table cellpadding="4" cellspacing="0" class="tablen" style="width:calc(100% - 10px);margin:5px;">
      <tr valign="top">
        <td width="50%">
          <b><u>GBP</u></b><br><br>
          <table class="tablen" cellpadding="0" cellspacing="0" >
            <tr>
              <td style="padding-right:20px;"><b>Items:</b></td>
              <td style="text-align:right"><?= number_format($globvars['i_row']['gbp_items'],2) ?></td>
            </tr>
            <tr>
              <td style="padding-right:20px;"><b>Voucher:</b></td>
              <td style="text-align:right"><?= number_format($globvars['i_row']['gbp_voucher'],2) ?></td>
            </tr>
            <tr>
              <td style="padding-right:20px;"><b>Subtotal:</b></td>
              <td style="text-align:right"><hr size="1"><?= number_format($globvars['i_row']['gbp_subt'],2) ?><hr size="1"></td>
            </tr>
            <tr>
              <td style="padding-right:20px;"><b>Net:</b></td>
              <td style="text-align:right"><?= number_format($globvars['i_row']['gbp_net'],2) ?></td>
            </tr>
            <tr>
              <td style="padding-right:20px;"><b>VAT:</b></td>
              <td style="text-align:right"><?= number_format($globvars['i_row']['gbp_vat'],2) ?></td>
            </tr>
            <tr valign="bottom">
              <td style="padding-right:20px;"><b>Gross:</b></td>
              <td style="text-align:right"><hr size="1"><?= number_format($globvars['i_row']['gbp_gross'],2) ?></td>
            </tr>
            <tr>
              <td style="padding-right:20px;"><b>Shipping:</b></td>
              <td style="text-align:right"><?= number_format($globvars['i_row']['gbp_ship'],2) ?></td>
            </tr>
            <tr>
              <td style="padding-right:20px;"><b>Total:</b></td>
              <td style="text-align:right"><hr size="1"><?= number_format($globvars['i_row']['gbp_total'],2) ?><hr size="1"></td>
            </tr>
          </table>
        </td>
        <td width="50%">
          <? if($globvars['i_row']['cur_code'] != 'GBP') { ?>
          <b><u><?= $globvars['i_row']['cur_code'] ?></u></b><br><br>
          <table class="tablen" cellpadding="0" cellspacing="0" >
            <tr>
              <td style="padding-right:20px;"><b>Items:</b></td>
              <td style="text-align:right"><?= number_format($globvars['i_row']['cur_items'],2) ?></td>
            </tr>
            <tr>
              <td style="padding-right:20px;"><b>Voucher:</b></td>
              <td style="text-align:right"><?= number_format($globvars['i_row']['cur_voucher'],2) ?></td>
            </tr>
            <tr>
              <td style="padding-right:20px;"><b>Subtotal:</b></td>
              <td style="text-align:right"><hr size="1"><?= number_format($globvars['i_row']['cur_subt'],2) ?><hr size="1"></td>
            </tr>
            <tr>
              <td style="padding-right:20px;"><b>Net:</b></td>
              <td style="text-align:right"><?= number_format($globvars['i_row']['cur_net'],2) ?></td>
            </tr>
            <tr>
              <td style="padding-right:20px;"><b>VAT:</b></td>
              <td style="text-align:right"><?= number_format($globvars['i_row']['cur_vat'],2) ?></td>
            </tr>
            <tr valign="bottom">
              <td style="padding-right:20px;"><b>Gross:</b></td>
              <td style="text-align:right"><hr size="1"><?= number_format($globvars['i_row']['cur_gross'],2) ?></td>
            </tr>
            <tr>
              <td style="padding-right:20px;"><b>Shipping:</b></td>
              <td style="text-align:right"><?= number_format($globvars['i_row']['cur_ship'],2) ?></td>
            </tr>
            <tr>
              <td style="padding-right:20px;"><b>Total:</b></td>
              <td style="text-align:right"><hr size="1"><?= number_format($globvars['i_row']['cur_total'],2) ?><hr size="1"></td>
            </tr>
          </table>
        </td>
        <? } ?>
      </tr>
    </table>
    <?
  }

  function order_items() {
    global $globvars;
    if(isset($globvars['order_items'][$globvars['go']])) {
      $arr = $globvars['order_items'][$globvars['go']];
      // print_arr($arr);
      ?>
      <table cellpadding="4" cellspacing="0" class="tableb" style="width:calc(100% - 10px);margin:5px;">
        <tr class="thb">
          <td></td>            
          <td>Product</td>            
          <td>SKU</td>            
          <td>Category</td>            
          <td>Options</td>            
          <td>Available</td>            
          <td align="right">GBP</td>
          <? if($globvars['i_row']['cur_code'] != 'GBP') { ?>
          <td align="right"><?= $globvars['i_row']['cur_code'] ?></td>
          <? } ?>
          <td align="right">Quantity</td>            
          <td align="right">Shipping</td>            
        </tr>
        <?
        foreach($arr as $itm) {
          ?>
          <tr>
            <td class="button"><a href="shop_items.php?action=edit&amp;go=<?= $itm['i_id'] ?>" target="shop_items.php">ITEM</a></td>            
            <td><?= $itm['prodname'] ?></td>            
            <td><?= $itm['sku'] ?></td>            
            <td><?= $itm['catname'] ?></td>            
            <td><?= disp($itm['options']) ?></td>            
            <td><?= $itm['available'] ?></td>            
            <td align="right"><?= number_format($itm['price'],2) ?></td>   
            <? if($globvars['i_row']['cur_code'] != 'GBP') { ?>
            <td align="right"><?= number_format($itm['price'] * $globvars['i_row']['cur_rate'],2) ?></td> 
            <? } ?>
            <td align="right"><?= $itm['quantity'] ?></td>            
            <td align="right"><?= number_format($itm['shipping'],2) ?></td>            
          </tr>
          <?
        }
        ?>
      </table>
      <?
    }
  }
  
  function sage_pay() {
    global $globvars;
    if(isset($globvars['order_pay'][$globvars['go']])) {
      $arr = $globvars['order_pay'][$globvars['go']];
      // print_arr($arr);
      ?>
      <table cellpadding="4" cellspacing="0" class="tableb" style="width:calc(100% - 10px);margin:5px;">
        <tr class="thb">
          <td>Recorded</td>            
          <td>Type</td>            
          <td>Date/Time</td>            
          <td>Status</td>            
          <td>VPSTxID</td>            
          <td>TxAuthNo</td>            
          <td>AVSCV2</td>            
          <td>Currency</td>            
          <td align="right">Amount</td>            
        </tr>
        <?
        foreach($arr as $itm) {
          ?>
          <tr>
            <td><?= $itm['recorded'] != '0000-00-00 00:00:00' ? 'Yes' : 'Ignored' ?></td>            
            <td><?= $itm['type'] ?></td>            
            <td><?= $itm['datetime'] ?></td>            
            <td><?= $itm['Status'] ?></td>            
            <td><?= $itm['VPSTxID'] ?></td>            
            <td><?= $itm['TxAuthNo'] ?></td>            
            <td><?= $itm['AVSCV2'] ?></td>            
            <td><?= $itm['currency'] ?></td>            
            <td align="right"><?= number_format($itm['Amount'],2) ?></td>            
          </tr>
          <?
        }
        ?>
      </table>
      <?
    }
  }
  
  function trust_pay() {
    global $globvars;
    if(isset($globvars['order_pay'][$globvars['go']])) {
      $arr = $globvars['order_pay'][$globvars['go']];
      // print_arr($arr);
      ?>
      <table cellpadding="4" cellspacing="0" class="tableb" style="width:calc(100% - 10px);margin:5px;">
        <tr class="thb">
          <td>Recorded</td>            
          <td>Type</td>            
          <td>Date/Time</td>            
          <td>Status</td>            
          <td>Code</td>            
          <td>Message</td>            
          <td>Ref</td>            
          <td>Settled</td>            
          <td>Currency</td>            
          <td>Seccode</td>            
          <td>Secaddr</td>            
          <td>Secpcode</td>            
          <td align="right">Amount</td>            
        </tr>
        <?
        foreach($arr as $itm) {
          ?>
          <tr>
            <td><?= $itm['tr_recorded'] != '0000-00-00 00:00:00' ? 'Yes' : 'Ignored' ?></td>            
            <td><?= $itm['tr_type'] ?></td>            
            <td><?= $itm['tr_datetime'] ?></td>            
            <td><?= $itm['tr_status'] ?></td>            
            <td><?= $itm['tr_errorcode'] ?></td>            
            <td><?= $itm['tr_errormsg'] ?></td>            
            <td><?= $itm['tr_transref'] ?></td>            
            <td><?= $itm['tr_settled'] ?></td>            
            <td><?= $itm['tr_currency'] ?></td>            
            <td><?= $itm['tr_seccode'] ?></td>            
            <td><?= $itm['tr_secaddr'] ?></td>            
            <td><?= $itm['tr_secpcode'] ?></td>            
            <td align="right"><?= number_format($itm['tr_amount'],2) ?></td>            
          </tr>
          <?
        }
        ?>
      </table>
      <?
    }
  }
  
  function _simage() {
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
        <img src="<?= clean_url($imgv1); ?>" style="<?= 'max-height:' . $imgh . 'px; max-width:' . ( $imgh * 2 ) . 'px' ; ?>" alt="" border=""> &nbsp; <?= $imgv ; ?>
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

  function _list_row_style($i_row) {
    $bgc = '';
    if($i_row['done'] == 'N') {
      $bgc = '#FFCC99';
    }
    elseif($i_row['done'] == 'Y') {
      $bgc = '#EFDEDE';
    }
    if($bgc) {
      print "background-color:$bgc;";
    }
  }
  
  function _filter_all() {
    global $globvars; extract($globvars);
    $string = "SELECT * FROM `cat_sub` LEFT JOIN `cat_main` ON `cat_sub`.`c_id`=`cat_main`.`c_id` ORDER BY `cat_main`.`c_id`, `cat_sub`.`s_name` ";
    $query = my_query($string);
    $cat_sel = array();
    while($a_row = my_array($query)) { 
      $cat_sel[$a_row['s_id']] = $a_row;
    }
    globvadd('cat_sel',$cat_sel);        
    $prev = ''; $optg = 0 ;
    ?>
      <select class="chosen-select" id="filter" name="filter" size="1" style="font-size:11px; width:150px;"
       onclick="$('#search').val('');" onchange="$('#lform').submit();"> 
        <option value="">*** ALL ***</option> 
        <? 
        foreach($cat_sel as $a_row) { 
          if($prev != $a_row['c_id']) { ?>
        <optgroup label="<?= clean_upper($a_row['c_name']); ?>">
          <? $optg++; } ?>
        <option value="<?= optsel('s_id|'.$a_row['s_id'],filter_str($filter)); ?>"><?= $a_row['s_name']; ?></option>
        <? $prev = $a_row['c_id']; } 
          if($optg) { ?>
        </optgroup>
        <? } ?>
      </select> 
    <? 
  }

  function _cat_sel() {
    global $globvars; extract($globvars);
    if(! (isset($cat_sel) && is_array($cat_sel)) ) {
      $string = "SELECT * FROM `cat_sub` LEFT JOIN `cat_main` ON `cat_sub`.`c_id`=`cat_main`.`c_id` ORDER BY `cat_main`.`c_id`, `cat_sub`.`s_name` ";
      $query = my_query($string);
      $cat_sel = array();
      while($a_row = my_array($query)) { 
        $cat_sel[$a_row['s_id']] = $a_row;
      }
      globvadd('cat_sel',$cat_sel);        
    }
    if( (isset($i_row[0]) && $i_row[0] == $go) || ($action == 'add')) {
      // edit form
      $prev = '';
      ?>
      <select class="chosen-select" id="<?= 'id_' . $fname ?>" name="<?= $fname ?>"
       size="1" onchange="fldchg++"> 
        <option value="">*** Select ***</option> 
        <? foreach($cat_sel as $a_row) { 
          if($prev != $a_row['c_id']) { if($prev) { ?>
        <option value="">&nbsp;</option> 
          <? } ?>
        <option style="text-decoration:underline;"
         value="<?= $a_row['s_id']; ?>"><?= clean_upper($a_row['c_name']); ?></option>
          <? } ?>
        <option class="link" value="<?= optsel($a_row['s_id'],$dval); ?>">
          &raquo;  <?= $a_row['s_name']; ?></option>
        <? $prev = $a_row['c_id']; } ?>
      </select>      
      <?
    }
    else {
      // item list
      // print_arr($cat_sel);
      if(isset($cat_sel[$i_row[$thiscol]]['c_name'])) {
        print $cat_sel[$i_row[$thiscol]]['c_name'] . ' &raquo; ' ;
      }
      if(isset($cat_sel[$i_row[$thiscol]]['s_name'])) {
        print $cat_sel[$i_row[$thiscol]]['s_name'] ;
      }
    }
  }

  function _filt_opts() {
    global $globvars; extract($globvars);
  }

  function _form_foot() {
    global $globvars; extract($globvars);
  }

  function _list_foot() {
    global $globvars; extract($globvars);
  }

  function _sq_funct() {
    global $globvars; extract($globvars);
    // fields:  dval, i_row, fname (or thiscol), c, s, fnamev (posted), c_row, ftype, fprms 
    if($go || ($action == 'add')) { // edit form
      if(isset($globvars['save'])) { $globvars['save']++; }
      print $dval;
    }
    elseif($action == 'export') { // export
      print $i_row[$fname]; // same as dval
    }
    else { // item list
      return true ; // for normal display
    }
  }

  /* ?>
    </form><? */
  ?>
  </body>
</html>