<?
@include('functions.inc.php');

/*
$order_ref = 16 ;
if($ord = order_find($order_ref)) {
  $res = sprint_make_order($ord);
  print_arr($res,'sprint_make_order_result');
  if($res['status'] == 'ok') {
    $string = "update `order_details` set `s_ref` = '{$res['ref']}', `s_response` = '{$res['response']}', `processing` = CURDATE() where `order_ref` = '$order_ref' limit 1";
    my_query($string);
  }
}

test_sprint_make_order();

test_sprint_get_products();

AUTOPICK
RECEIVED
AWAITING_CHECK
WITH_PICKERS
WITH_OPERATIONS = shipped

test_sprint_get_orders('WITH_OPERATIONS');
test_sprint_get_orders('RECEIVED');


test_sprint_get_order('7073241');
test_sprint_get_order('7073241');


*/

test_sprint_get_tracking('7073241');

function test_sprint_make_order() {
  $res = ['status'=>'','response'=>[]];
  global $globvars;
  $ch = curl_init();

  $request = str_replace("'","\'",'{ 
    "AttnOf" : "Munir", 
    "Telephone" : "07938344744", 
    "EmailAddr" : "nmunir@sprintlogistics.com", 
    "CompanyName" : "SprintLogistics", 
    "Address1" : "A2 Cranford Lane", 
    "Address2" : null, 
    "Address3" : null, 
    "City" : "London", 
    "State" : "", 
    "PostCode" : "TW5 9QA", 
    "CountryID" : "222", 
    "CountryCode" : "GB", 
    "SpecialInstructions" : "Standard Delivery", 
    "PackingNote" : "test packing", 
    "CustomerRef1" : "test1234", 
    "CustomerRef2" : "", 
    "StockOrderItems" : [ 
      { 
        "SKU" : "00001", 
        "Quantity" : "1"
      }, 
      { 
        "SKU" : "0000022-A", 
        "Quantity" : "1"
      }
    ] 
  }');

  print_pre($request);

  $apiurl = 'https://api.sprintlogistics.com/api/orders';
  print_p('POST: ' . $apiurl);
  curl_setopt_array($ch, array(
    CURLOPT_URL => $apiurl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $request,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_HTTPHEADER => array(
      'Accept: application/json',
      'Content-Type: application/json',
      'Content-Length: ' . strlen($request),
      'Authorization: Basic ' . $globvars['sprint_auth']
    ),
  ));

  $result = curl_exec($ch);
  if(! $result) {
    $res['status'] = 'no response';
  }
  if(curl_error($ch)) {
    $res['status'] = curl_error($ch);
  }
  else {
    $arr = objectToArray(json_decode($result));
    if(isset($arr['Status'])) {
      if($arr['Status']) {
        $res['response'] = $arr;
        $res['status'] = 'ok';
        $globvars['order'] = $arr['Result']['AWB'];
      }
      else {
        $res['status'] = $arr['Message'];
      }
    }
    else {
      $res['status'] = 'no status';
    }
  }
  curl_close($ch);
  print_arr($res,'sprint_make_order_result');
  return $res ;
}

function test_sprint_get_orders($status='') {
  global $globvars;
  $ch = curl_init();

  $apiurl = 'https://api.sprintlogistics.com/api/orders?pageSize=10000&status=' . $status;
  print_p('GET: ' . $apiurl);
  curl_setopt_array($ch, array(
    CURLOPT_URL => $apiurl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => array(
      'Accept: application/json',
      'Content-Type: application/json',
      'Authorization: Basic ' . $globvars['sprint_auth']
    ),
  ));

  $result = curl_exec($ch);
  if(curl_error($ch)) {
    print_arr(curl_error($ch),'sprint_get_orders_error');
  }
  curl_close($ch);
  $arr = objectToArray(json_decode($result));
  print_arr($arr,'sprint_get_orders_result');
}

function test_sprint_get_order($in='') {
  global $globvars;
  $ch = curl_init();

  $apiurl = 'https://api.sprintlogistics.com/api/orders/' . $in;
  print_p('GET: ' . $apiurl);
  curl_setopt_array($ch, array(
    CURLOPT_URL => $apiurl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => array(
      'Accept: application/json',
      'Content-Type: application/json',
      'Authorization: Basic ' . $globvars['sprint_auth']
    ),
  ));

  $result = curl_exec($ch);
  if(curl_error($ch)) {
    print_arr(curl_error($ch),'sprint_get_orders_error');
  }
  curl_close($ch);
  $arr = objectToArray(json_decode($result));
  print_arr($arr,'sprint_get_order_' . $in . '_result');
}

function test_sprint_get_tracking($in='') {
  global $globvars;
  $ch = curl_init();

  $apiurl = 'https://api.sprintlogistics.com/api/shipment/tracking?HAWB=' . $in;
  print_p('GET: ' . $apiurl);
  curl_setopt_array($ch, array(
    CURLOPT_URL => $apiurl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => array(
      'Accept: application/json',
      'Content-Type: application/json',
      'Authorization: Basic ' . $globvars['sprint_auth']
    ),
  ));

  $result = curl_exec($ch);
  if(curl_error($ch)) {
    print_arr(curl_error($ch),'sprint_get_orders_error');
  }
  curl_close($ch);
  $arr = objectToArray(json_decode($result));
  print_arr($arr,'sprint_get_order_' . $in . '_result');
}

function test_sprint_get_products() {
  global $globvars;
  $ch = curl_init();

  $apiurl = 'https://api.sprintlogistics.com/api/product/';
  print_p('GET: ' . $apiurl);
  curl_setopt_array($ch, array(
    CURLOPT_URL => $apiurl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => array(
      'Accept: application/json',
      'Content-Type: application/json',
      'Authorization: Basic ' . $globvars['sprint_auth']
    ),
  ));

  $result = curl_exec($ch);
  if(curl_error($ch)) {
    print_arr(curl_error($ch),'sprint_get_products_error');
  }
  curl_close($ch);
  $arr = objectToArray(json_decode($result));
  print_arr($arr,'sprint_get_products_result');
}

?>