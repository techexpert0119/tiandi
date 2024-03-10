<?
global $globvars;
$globvars['grform'] = 0 ;
$globvars['grcount'] = 0 ;
if((isset($globvars['local_dev']) && $globvars['local_dev']) || ! (isset($globvars['recaptcha']['site']) && isset($globvars['recaptcha']['secret']))) {
  // live register at: https://www.google.com/recaptcha/admin (reCAPTCHA v2)
  // localhost testing
  $globvars['recaptcha']['site'] = '6LctegoUAAAAADup7iFJEC9_O-iI6u0llD68n2c-';
  $globvars['recaptcha']['secret'] = '6LctegoUAAAAAFrnATxXJUnHoink0ZWaOZcpKGle';
}
if(! (isset($globvars['recaptcha']['lang']) && $globvars['recaptcha']['lang'])) {
  // default to english if not specified
  $globvars['recaptcha']['lang'] = 'en';
}
if(! isset($globvars['recaptcha']['init'])) {
  // default to load header on include if not specified
  $globvars['recaptcha']['init'] = true ;
}
if($globvars['recaptcha']['init']) {
  // load header on include if true
  head_recaptcha();
}

function head_recaptcha() {
  // recaptcha header
  global $globvars ;
  if(isset($globvars['recaptcha']['type']) && $globvars['recaptcha']['type'] == 'invisible') {
    // invisible version
    ?>
    <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit&hl=<?= $globvars['recaptcha']['lang'] ?>" async defer></script>
    <script type="text/javascript">
      var onloadCallback = function() {
        r = 0 ;
        widgetId = new Array();
        $(".g-recaptcha").each(function() {
          var object = $(this);
          widgetId[r] = grecaptcha.render(object.attr("id"), {
             "sitekey" : "<?= $globvars['recaptcha']['site']; ?>",
             "callback" : function() {
                object.parents('form').submit();
              }
           });
           r++;
        });
      };
    </script>
  <? 
  } 
  else {
    // visible version
    ?>
    <script type="text/javascript" src="https://www.google.com/recaptcha/api.js?hl=<?= $globvars['recaptcha']['lang'] ?>"></script>
    <style type="text/css">
    @media screen and (max-width: 400px){ 
      #rc-imageselect, .g-recaptcha {
        transform:scale(0.77);-webkit-transform:scale(0.77);transform-origin:0 0;-webkit-transform-origin:0 0;
      }
    }
    </style> 
    <? 
  }  
}

function disp_recaptcha($id='') {
  // display recaptcha
  global $globvars ;
  ?>
  <div id="<?= $id ?>" class="g-recaptcha" data-sitekey="<?= $globvars['recaptcha']['site']; ?>" data-widget="<?= $globvars['grcount']++ ?>"></div>
  <?
}

function check_recaptcha() {
  // check recaptcha passed
  global $globvars ;
  if(isset($_POST["g-recaptcha-response"])) {
    // print_arv($_POST["g-recaptcha-response"]);
    $secret = $globvars['recaptcha']['secret'];
    $response = $_POST["g-recaptcha-response"];
    // use new function
    return validate_rechapcha($secret,$response);

    /*
    $arrContextOptions=array(
      "ssl"=>array(
        "verify_peer"=>false,
        "verify_peer_name"=>false,
      ),
    );  
    $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret}&response={$response}", false, stream_context_create($arrContextOptions));
    $captcha_success=json_decode($verify);
    if($captcha_success->success==true) {
      return true ;
    }
    */
    $fields = array('secret'=>$secret,'response'=>$response);
    if($verify = post_curl('https://www.google.com/recaptcha/api/siteverify',$fields)) {
      $captcha_success = json_decode($verify);
      // print_p($verify) ;
      // var_dump($captcha_success);
      if(is_object($captcha_success) && $captcha_success->success == true) {
        return true ;
      }
    }
  }
  return false ;
}

function validate_rechapcha($secret,$response) {
  // https://gist.github.com/jonathanstark/dfb30bdfb522318fc819
  $verifyURL = 'https://www.google.com/recaptcha/api/siteverify';

  $query_data = [
    'secret' => $secret,
    'response' => $response,
    'remoteip' => (isset($_SERVER["HTTP_CF_CONNECTING_IP"]) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : $_SERVER['REMOTE_ADDR'])
  ];

  // Collect and build POST data
  $post_data = http_build_query($query_data, '', '&');

  // Send data on the best possible way
  if (function_exists('curl_init') && function_exists('curl_setopt') && function_exists('curl_exec')) {
    // Use cURL to get data 10x faster than using file_get_contents or other methods
    $ch = curl_init($verifyURL);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-type: application/x-www-form-urlencoded'));
    $response = curl_exec($ch);
    curl_close($ch);
  }
  else {
    // If server not have active cURL module, use file_get_contents
    $opts = array('http' =>
      array(
        'method' => 'POST',
        'header' => 'Content-type: application/x-www-form-urlencoded',
        'content' => $post_data
      )
    );
    $context = stream_context_create($opts);
    $response = file_get_contents($verifyURL, false, $context);
  }

  // Verify all reponses and avoid PHP errors
  if ($response) {
    $result = json_decode($response);
    if ($result->success === true) {
      return true;
    }
    else {
      // return $result;
    }
  }

  // Dead end
  return false;
}

function bfid_recaptcha() {
  // call to set form id for invisible
  global $globvars ;
  $globvars['grform']++;
  return 'grForm' . $globvars['grform'] ;
}

function butt_recaptcha($text,$class='submit',$style='') {
  // button for invisible recaptcha
  global $globvars ;
  ?>
  <button id="<?= 'grButton' . $globvars['grform']; ?>" class="g-recaptcha <?= $class; ?>" style="<?= $style; ?>"><?= $text ?></button>
  <?
}
?>