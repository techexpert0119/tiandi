<?
@include_once('control/functions.inc.php');
globvars('action','sess');
if($globvars['sess'] != $globvars['sessmd']) { 
  print json_encode(array('res'=>0,'arr'=>[],'msg'=>'Access Denied')); 
  die; 
}

elseif($globvars['action'] == 'login_form') {
  print json_encode(user_login());
}

elseif($globvars['action'] == 'register_form') {
  print json_encode(user_register());
}

elseif($globvars['action'] == 'lostpass_form') {
  print json_encode(user_lostpass());
}

elseif($globvars['action'] == 'reset_form') {
  print json_encode(user_resetpass());
}

elseif($globvars['action'] == 'edit_form') {
  print json_encode(user_edit());
}

elseif($globvars['action'] == 'pass_form') {
  print json_encode(user_pass());
}

elseif($globvars['action'] == 'subscribe_form') {
  globvars('email','name');
  print json_encode(news_subscribe($globvars['email'],$globvars['name']));
}

elseif($globvars['action'] == 'unsubscribe_form') {
  globvars('email');
  print json_encode(news_unsubscribe($globvars['email']));
}

elseif($globvars['action'] == 'contact_form') {
  print json_encode(contact_form());
}

elseif($globvars['action'] == 'basket_action') {
  // basket_items();
  print $globvars['basket']['pop'];
}

elseif($globvars['action'] == 'wish_add') {
  print wish_add();
}

elseif($globvars['action'] == 'st_update') {
  print st_update();
}

elseif($globvars['action'] == 'partner') {
  print json_encode(partner_submit());
}

elseif($globvars['action'] == 'contact') {
  print json_encode(contact_submit());
}

elseif($globvars['action'] == 'news') {
  globvars('email');
  print json_encode(news_subscribe($globvars['email']));
}
?>