<?
@include_once('control/functions.inc.php');
pages_main();
get_systems();
if(! isset($globvars['page'])) {
  die('Unable to get page data');
}
if($globvars['page']['url'] == 404) {
  header("HTTP/1.0 404 Not Found");
}
if(isset($globvars['page']['inc']) && $globvars['page']['inc'] && file_exists('templates/' . $globvars['page']['inc'])) {
  @include_once('templates/' . $globvars['page']['inc']);
}
else {
  header("location:404");
  die;
}
if(function_exists('head0')) { head0(); }
// basket_items();
?>
<!DOCTYPE html>
<html lang="<?= $globvars['htmlang'] ?>">
  <head>
    <? 
    // special pages handled in their head1
    if(function_exists('head1')) { head1(); } 
    if($globvars['page']['meta_title'] && $globvars['page']['meta_title'] != trim(str_replace(['-','|'],['',''],$globvars['comp_meta']))) {
      $globvars['page']['meta_title'] .= ' - ' . $globvars['comp_meta'] ;
    }
    else {
      $globvars['page']['meta_title'] = $globvars['comp_meta'] ;
    }
    ?>
    <base href="<?= $globvars['base_href'] ?>">
    <title><?= $globvars['page']['meta_title'] ; ?></title>
    <meta name="language" content="<?= $globvars['htmlang'] ; ?>">	
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="content-language" content="<?= $globvars['htmlang'] ; ?>"> 
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<?
    // print_arv($globvars['page']);
    if($globvars['altsites']) {
      $d = 0 ;
      foreach($globvars['altlangs'] as $c => $a) {
        $u = ($d && $globvars['page']['alturl']) ? $globvars['page']['alturl'] : $globvars['page']['url'];
?>
    <link rel="alternate" hreflang="<?= $c ?>" href="<?= $a['url'] . $u ?>">
<?  
        $d++;
      }
    }
    if(isset($_SESSION['admin_login']) && $_SESSION['admin_login']) { ?>
    <script>window.name='public';</script>
    <? } 
    cs_files('date','data-cfasync="false"');
    ?>
    <style type="text/css">
<?
    foreach($globvars['systems'] as $system) {
      $colour = $system['model']['m_colour_light'];
      $url = $system['model']['q_url'];
      print '    ';
      print '.colour_' . $url . ', .hover_' . $url . ':hover { color: ' . $colour . "; } ";
      print '.meganav .megadropn .hover_' . $url . ':hover .menuarrow, .meganav .megadropn .colour_' . $url . ' .menuarrow { border-color:' . $colour . "; }\r\n";
    }
?>
    </style>
    <?
    meta_tags($globvars['page']['meta_title'],$globvars['page']['meta_desc'],$globvars['page']['meta_keyw'],$globvars['page']['meta_image'],'',$globvars['live_href'] . $globvars['page']['url']);
    // print_arv($globvars['page']);
    if( isset($globvars['mega_nav']) && $globvars['mega_nav']) {
      $globvars['page']['class'] .= ' meganav';
    }
    if($globvars['page']['img_main'] || isset($globvars['page']['video']['file']) || substr_count($globvars['page']['class'],'contact')) {
      $globvars['page']['class'] .= ' menutrans';
    }
    if(function_exists('head2')) { head2(); }
    if(function_exists('head_yoast')) { head_yoast(); }
    if(function_exists('track_head')) { track_head(); }
    ?>
    <link rel="apple-touch-icon" sizes="57x57" href="favicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="favicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="favicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="favicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="favicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="favicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="favicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="favicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="favicon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="favicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon/favicon-16x16.png">
    <link rel="manifest" href="favicon/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="favicon/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <link rel="shortcut icon" href="favicon.ico">
  </head>
  <body class="<?= $globvars['page']['class'] ?> <?= 'lang_' . $globvars['htmlang'] ?>">
    <? if(function_exists('track_body')) { track_body(); } ?>
    <div id="wrapper">
      <div id="cover"></div>
      <?
      // print_arv($globvars['page']);
      // print_arv($globvars['pages_main']);

      $globvars['menus']['head']['systems']['subs']['powertrain'] = [
        'menu' => 'Configure Powertrain', 
        'menud' => 'Configure Powertrain', 
        'menui' => '',
        'url' => 'https://evo.tiandi-e.com/index.html',
        'target' => '_blank',
        'subs' => [
          'powertrain' => [
            'menu' => 'Use our calculator',
            'url' => 'https://evo.tiandi-e.com/index.html',
            'target' => '_blank'
          ]
        ]
      ];
      // print_arv($globvars['menus']);

      body();
      ?>
    </div>
    <div id="alert_pop"></div>
    <span id="sess" data-sess="<?= $globvars['sessmd']; ?>"></span>
    <? 
    partner_formtab();
    js_file("scripts/jquery/jquery-3.6.0.min.js");
    js_file("scripts/jquery/jquery-ui-1.12.1.min.js");
    js_files('date','data-cfasync="false"');
    if(function_exists('body_end')) { body_end(); }
    if(function_exists('track_last')) { track_last(); }
    cron_tasks();
    ?>
 </body>
</html>
