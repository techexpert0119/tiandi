<?
@include_once('functions.inc.php');
globvars('action');
if(! $globvars['cntrl_user']) { 
  print json_encode(array('res'=>0,'arr'=>[],'msg'=>'Access Denied')); 
  die; 
}

elseif($globvars['action'] == 'upimage') {
  print json_encode(cms_upimage());
}

elseif($globvars['action'] == 'upshopi') {
  print json_encode(cms_upshopi());
}
?>