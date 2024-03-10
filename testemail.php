<?
@include('control/functions.inc.php');

$globvars['email_to'] = 'info@e-tiandi.com';
// $globvars['email_to'] = 'richard@hallodigital.co.uk';

print sendmail($globvars['email_to'], $globvars['email_fr'], 'T&D Email Test', 'Email Test', '', '', '', '', $globvars['email_fn']);

?>