<?
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function parameters() {
  global $globvars;
  globvars('preview','done','action','go','del','q');
  $globvars['parameters'] = [];
  $string = "select * from `{$globvars['param_table']}` order by `order`";
  $query = my_query($string);
  while($row = my_assoc($query)) {
    $globvars['parameters'][$row['id']] = $row ;
    if($row['param']) {
      $globvars[$row['param']] = trim($row['html']);
    }
  }
  $globvars['meta_max'] = array('k'=>160, 'd'=>160, 't'=>50 - strlen($globvars['comp_meta']));
  if(substr_count($globvars['email_to'],"\r\n")) {
    $globvars['email_to'] = explode("\r\n", $globvars['email_to']);
  }
  if(substr_count($globvars['email_fr'],"\r\n")) {
    $globvars['email_fr'] = substr($globvars['email_fr'], 0 , strpos($globvars['email_fr'], "\r\n"));
  }
  if(! $globvars['local_dev']) {
    $globvars['recaptcha']['site'] = $globvars['recaptcha_site'];
    $globvars['recaptcha']['secret'] = $globvars['recaptcha_secret'];
  }
  // print_arv($globvars['parameters'],'parameters');
  // curr_get();
}

function get_systems() {
  global $globvars;
  $globvars['systems'] = [];
  $string = 
    "select * from `models` 
    left join `pages_subp` on `pages_subp`.`q_id` = `models`.`m_id`
    left join `pages_main` on `pages_main`.`p_id` = `pages_subp`.`p_id`
    where `pages_subp`.`q_visible` = 'yes'
    order by `pages_subp`.`q_order`
    limit 5
  ";
  $query = my_query($string);
  while($row = my_assoc($query)) {
    $globvars['systems'][$row['q_id']]['model'] = $row;
  }
  $string = 
    "select * from `products` 
    left join `pages_subs` on `pages_subs`.`q_id` = `products`.`q_id` and `pages_subs`.`r_id` = `products`.`r_id`
    where `pages_subs`.`r_visible` = 'yes'
    order by `pages_subs`.`r_order`
  ";
  $query = my_query($string);
  while($row = my_assoc($query)) {
    if(isset($globvars['systems'][$row['q_id']])) {
      $globvars['systems'][$row['q_id']]['products'][$row['r_id']] = $row ;
    }
  }
  // print_arv($globvars['systems']);
}

function get_components() {
  global $globvars;
  $globvars['components'] = [];
  $string = 
    "select * from `components` 
    where `c_visible` = 'yes'
    order by `c_order`
  ";
  $query = my_query($string);
  while($row = my_assoc($query)) {
    $globvars['components'][$row['c_id']] = $row;
  }
}

function cron_tasks() {
  global $globvars;
  if(! isset($globvars['parameters'])) {
    parameters();
  }
  if((time() - strtotime($globvars['cron_time'])) < 3600) {
    return ;
  }
  $string = "update `{$globvars['param_table']}` set `html` = NOW() where `param` = 'cron_time' limit 1";
  // print_p($string);
  $query = my_query($string);
  /*
  $string = "select * from `order_details` where `s_ref` != '' and `despatched` = '0000-00-00'";
  // print_p($string);
  $query = my_query($string);
  if(my_rows($query)) {
    while($row = my_assoc($query)) {
      $sprint = sprint_get_order($row['order_ref'],$row['s_ref']);
      // print_arr($sprint);
      if($sprint['sprint_ref'] == $row['s_ref'] && $sprint['status'] == 'WITH_OPERATIONS') {
        if(! ( isset($globvars['countries']) && isset($globvars['states']))) {
          $globvars['countries'] = countries('ship_countries','sc_code','sc_name');
          $globvars['states'] = countries('ship_states','ss_code','ss_name');
        }
        $string1 = "update `order_details` set `despatched` = CURDATE(), `s_tracking` = '{$sprint['tracklink']}' WHERE `order_ref` = '{$row['order_ref']}' LIMIT 1";
        // print $row['order_ref'] . '<br>';
        // print_p($string1);
        my_query($string1);
        logtable('UPDATE','CRON','order_details',$string1);
        if($ord = order_find($row['order_ref'])) {
          // print_arr($ord);
          order_email($ord,'despatch');
        }
      }
    }
  }
  */
}

function templates() {
  // get templates
  global $globvars;
  if(substr_count($globvars['php_path'], '/control')) {
    $globvars['preview'] = $globvars['sessmd'];
  }

  $globvars['menus']['head'] = $globvars['menus']['foot'] = $globvars['page']['bread'] = $globvars['lock_temp'] = $globvars['lock_url'] = $globvars['lock_vis'] = $globvars['lock_del'] = $globvars['main_root'] = $globvars['pages_main'] = $globvars['pages_pids'] = $globvars['pages_admin'] = $globvars['search_funcs'] = $globvars['page'] = $globvars['templates'] = [];

  $globvars['main_root']['pages_shop'] = $globvars['main_root']['pages_blog'] = '';

  $string = "select * from `templates` order by `t_select`";
  $query = my_query($string);
  while($row = my_assoc($query)) {
    // css_files
    if($row['t_css'] && ! in_array($row['t_css'],$globvars['minify']['css_files'])) {
      $globvars['minify']['css_files'][] = 'css/' . $row['t_css'];
    }

    // class
    $row['t_class'] = str_replace('.css', '', $row['t_css']);

    // search
    if($row['t_search'] && ! in_array($row['t_search'],$globvars['search_funcs'])) {
      $globvars['search_funcs'][] = $row['t_search'];
    }

    // shop
    if(substr_count($row['t_cms'], 'cms_shop')) {
      $globvars['shop_cms'] = $row['t_id'] ;
    }

    $globvars['templates'][$row['t_id']] = $row ;
  }
  // print_arv($globvars['templates'],'templates');
}

function temp_arr($t_id) {
  global $globvars;
  if($t_id && isset($globvars['templates'][$t_id])) {
    return $globvars['templates'][$t_id];
  }
  return false;
}

/*  PAGES */

function pages_main($ob=true) {
  global $globvars;
  $funcdone = [];

  if($ob) {
    // don't do in admin or it breaks export
    ob_start();
  }

  // pages_main
  $where = ($globvars['preview'] != $globvars['sessmd']) ? "where `pages_main`.`p_visible` = 'yes'" : '';
  $string = "
    select * from `pages_main`
    $where
    order by 
    `pages_main`.`p_order` = 0, 
    `pages_main`.`p_order`
  ";
  $query = my_query($string);
  while($row = my_assoc($query)) {

    // template
    $row['t_pages'] = 'pages_main';
    $row['t_admin0'] = 'pages_main.php';
    $row['t_admin1'] = 'pages_subp.php';
    $row['t_admin2'] = 'pages_subs.php';
    $row['t_admin3'] = '';
    $row['p_inc'] = $row['p_class'] = $row['p_search'] = '';
    if($temp_arr = temp_arr($row['p_template'])) {
      $row['p_inc'] = $temp_arr['t_inc'];
      $row['p_class'] = $temp_arr['t_class'];
      $row['p_search'] = $temp_arr['t_search'];
      if($temp_arr['t_pages']) {
        $row['t_pages'] = $temp_arr['t_pages'];
        $row['t_admin1'] = $temp_arr['t_admin1'];
        $row['t_admin2'] = $temp_arr['t_admin2'];
        $row['t_admin3'] = $temp_arr['t_admin3'];
      }
    }

    // url
    $p_url = $row['p_url'];
    $p_rrl = $row['p_redirect'] ? $row['p_redirect'] : $row['p_url'];
    $globvars['pages_pids'][$row['p_id']]['url'] = $p_url ;

    // meta title
    if(! $row['p_meta_title']) {
      $row['p_meta_title'] = $row['p_head1'] ? $row['p_head1'] : $row['p_menu'];
    }

    // edit & bread
    $row['p_ephp'] = $row['t_admin0'];
    $row['p_edit'] = $row['p_ephp'] . '?action=edit&amp;go=' . $row['p_id'];
    $row['p_bread'][$p_rrl] = $row['p_menu'] ? $row['p_menu'] : $row['p_head1'];

    // images
    $row['p_img_main'] = preg_filter('/.+/', 'images/head/main/$0', explode(",", $row['p_img_main']));
    $row['p_img_mob'] = preg_filter('/.+/', 'images/head/mob/$0', explode(",", $row['p_img_mob']));
    // $row['p_video'] = video_arr($row['p_video']);

    // pages main
    foreach($row as $k => $v) {
      if(substr_count($k,'_id')) {
        $globvars['pages_main'][$p_url][$k] = $v ;
      }
      $k = str_replace('p_','',$k);
      $globvars['pages_main'][$p_url][$k] = $v ;
    }

    // sitemap
    if(!in_array($p_rrl,$globvars['sm_excld'])) {
      $globvars['sm_pages'][] = $p_rrl ;
      $globvars['sm_prior'][] = 0.8;
    }

    // main menu
    if($row['p_menu']) {
      $globvars['menus']['head'][$p_url]['menu'] = $row['p_menu'];
      $globvars['menus']['head'][$p_url]['menud'] = $row['p_menud'];
      $globvars['menus']['head'][$p_url]['menui'] = $row['p_menui'];
      $globvars['menus']['head'][$p_url]['menuc'] = $row['p_menuc'];
      $globvars['menus']['head'][$p_url]['url'] = $p_rrl;
      $globvars['menus']['head'][$p_url]['order'] = $row['p_order'];
    }

    // functions
    if(isset($temp_arr['t_pages']) && $temp_arr['t_pages'] && ($temp_arr['t_pages'] != 'pages_main')) {
      $tf = $temp_arr['t_pages'] ;
      $globvars['main_root'][$tf] = $p_url;
      if((! in_array($tf, $funcdone)) && function_exists($tf)) {
        $tf($p_url);
        $funcdone[] = $tf ;
      }
    }
  }

  // pages_subp
  $where = ($globvars['preview'] != $globvars['sessmd']) ? "
    and `pages_main`.`p_visible` = 'yes' 
    and `pages_subp`.`q_visible` = 'yes'
  " : '';
  $string = "
    select * 
    from `pages_subp` 
    left join `pages_main` on `pages_subp`.`p_id` = `pages_main`.`p_id`
    where 
    `pages_main`.`p_url` != '' 
    and `pages_subp`.`q_url` != ''
    $where
    order by 
    `pages_main`.`p_order` = 0, 
    `pages_main`.`p_order`, 
    `pages_subp`.`q_order` = 0, 
    `pages_subp`.`q_order`
  ";
  $query = my_query($string);
  while($row = my_assoc($query)) {
    // template
    $row['q_inc'] = $row['q_class'] = $row['q_search'] = '';
    if($temp_arr = temp_arr($row['q_template'])) {
      $row['q_inc'] = $temp_arr['t_inc'];
      $row['q_class'] = $temp_arr['t_class'];
      $row['q_search'] = $temp_arr['t_search'];
    }

    // url
    $q_url = $row['q_url'];
    $p_url = $row['p_url'];
    if($q_url) {
      $globvars['pages_pids'][$row['p_id']][$row['q_id']]['url'] = $q_url ;
    }
    $row['q_url'] = "$p_url/$q_url";
    $q_rrl = $row['q_redirect'] ? $row['q_redirect'] : $row['q_url'];

    // meta title
    if(! $row['q_meta_title']) {
      $row['q_meta_title'] = $row['q_head1'] ? $row['q_head1'] : $row['q_menu'];
    }

    // edit & bread
    $row['q_ephp'] = $globvars['pages_main'][$p_url]['t_admin1'];
    $row['q_edit'] = $row['q_ephp'] . '?action=edit&amp;go=' . $row['q_id'];
    $bname = $row['q_menu'] ? $row['q_menu'] : $row['q_head'];
    $row['q_bread'] = array_merge($globvars['pages_main'][$p_url]['bread'],[$q_rrl => $bname]);

    // images
    $row['q_img_main'] = preg_filter('/.+/', 'images/head/main/$0', explode(",", $row['q_img_main']));
    $row['q_img_mob'] = preg_filter('/.+/', 'images/head/mob/$0', explode(",", $row['q_img_mob']));
    // $row['q_video'] = video_arr($row['q_video']);

    if(! isset($globvars['pages_main'][$p_url]['subs'][$q_url])) {

      // pages main
      foreach($row as $k => $v) {
        if(substr_count($k,'_id')) {
          $globvars['pages_main'][$p_url]['subs'][$q_url][$k] = $v ;
        }
        if(substr_count($k,'q_')) {
          $k = str_replace('q_','',$k);
          $globvars['pages_main'][$p_url]['subs'][$q_url][$k] = $v ;
        }
      }

      // sitemap
      if(!in_array($q_rrl,$globvars['sm_excld'])) {
        $globvars['sm_pages'][] = $q_rrl ;
        $globvars['sm_prior'][] = 0.7;
      }
    }

    // main menu
    if(isset($globvars['menus']['head'][$p_url])) {
      $globvars['menus']['head'][$p_url]['subs'][$q_url]['menu'] = $row['q_menu'];
      $globvars['menus']['head'][$p_url]['subs'][$q_url]['menud'] = $row['q_menud'];
      $globvars['menus']['head'][$p_url]['subs'][$q_url]['menui'] = $row['q_menui'];
      $globvars['menus']['head'][$p_url]['subs'][$q_url]['url'] = $q_rrl;
    }
  }

  // pages_subs
  $where = ($globvars['preview'] != $globvars['sessmd']) ? "
    and `pages_main`.`p_visible` = 'yes' 
    and `pages_subp`.`q_visible` = 'yes' 
    and `pages_subs`.`r_visible` = 'yes'
  " : '';
  $string = "
    select * 
    from `pages_subs` 
    left join `pages_subp` on `pages_subs`.`q_id` = `pages_subp`.`q_id`
    left join `pages_main` on `pages_subp`.`p_id` = `pages_main`.`p_id`
    where 
    `pages_main`.`p_url` != '' 
    and `pages_subp`.`q_url` != '' 
    and `pages_subs`.`r_url` != ''
    $where
    order by 
    `pages_main`.`p_order` = 0, 
    `pages_main`.`p_order`, 
    `pages_subp`.`q_order` = 0, 
    `pages_subp`.`q_order`, 
    `pages_subs`.`r_order` = 0,
    `pages_subs`.`r_order`
  ";
  $query = my_query($string);
  while($row = my_assoc($query)) {
    // template
    $row['r_inc'] = $row['r_class'] = $row['r_search'] = '';
    if($temp_arr = temp_arr($row['r_template'])) {
      $row['r_inc'] = $temp_arr['t_inc'];
      $row['r_class'] = $temp_arr['t_class'];
      $row['r_search'] = $temp_arr['t_search'];
    }
    
    // urls
    $r_url = $row['r_url'];
    $q_url = $row['q_url'];
    $p_url = $row['p_url'];
    if($r_url) {
      $globvars['pages_pids'][$row['p_id']][$row['q_id']][$row['r_id']]['url'] = $r_url ;
    }
    $row['r_url'] = "$p_url/$q_url/$r_url";
    $row['q_url'] = "$p_url/$q_url";
    $r_rrl = $row['r_redirect'] ? $row['r_redirect'] : $row['r_url'];

    // meta title
    if(! $row['r_meta_title']) {
      $row['r_meta_title'] = $row['r_head1'] ? $row['r_head1'] : $row['r_menu'];
    }

    // edit & bread
    $row['r_ephp'] = $globvars['pages_main'][$p_url]['t_admin2'];
    $row['r_edit'] = $row['r_ephp'] . '?action=edit&amp;go=' . $row['r_id'];
    $bname = $row['r_menu'] ? $row['r_menu'] : $row['r_head1'];
    $row['r_bread'] = array_merge($globvars['pages_main'][$p_url]['subs'][$q_url]['bread'],[$r_rrl => $bname]);

    // images
    $row['r_img_main'] = preg_filter('/.+/', 'images/head/main/$0', explode(",", $row['r_img_main']));
    $row['r_img_mob'] = preg_filter('/.+/', 'images/head/mob/$0', explode(",", $row['r_img_mob']));
    // $row['r_video'] = video_arr($row['r_video']);

    if(! isset($globvars['pages_main'][$p_url]['subs'][$q_url]['subs'][$r_url])) {

      // pages main
      foreach($row as $k => $v) {
        if(substr_count($k,'_id')) {
          $globvars['pages_main'][$p_url]['subs'][$q_url]['subs'][$r_url][$k] = $v ;
        }
        if(substr_count($k,'r_')) {
          $k = str_replace('r_','',$k);
          $globvars['pages_main'][$p_url]['subs'][$q_url]['subs'][$r_url][$k] = $v ;
        }
      }

      // sitemap
      if(!in_array($r_rrl,$globvars['sm_excld'])) {
        $globvars['sm_pages'][] = $r_rrl ;
        $globvars['sm_prior'][] = 0.6;
      }
    }

    // main menu
    if(isset($globvars['menus']['head'][$p_url]['subs'][$q_url])) {
      $globvars['menus']['head'][$p_url]['subs'][$q_url]['subs'][$r_url]['menu'] = $row['r_menu'];
      $globvars['menus']['head'][$p_url]['subs'][$q_url]['subs'][$r_url]['url'] = $r_rrl;
    }
  }

  // pages_urls;
  $globvars['pages_urls'] = [];
  foreach($globvars['pages_main'] as $u0 => $a0) {
    $globvars['pages_urls'][] = $a0['url'];
    if(isset($a0['subs'])) { foreach($a0['subs'] as $u1 => $a1) {
      $globvars['pages_urls'][] = $a1['url'];
      if(isset($a1['subs'])) { foreach($a1['subs'] as $u2 => $a2) {
        $globvars['pages_urls'][] = $a2['url'];
        if(isset($a2['subs'])) { foreach($a2['subs'] as $u3 => $a3) {
          $globvars['pages_urls'][] = $a3['url'];
        }}
      }}
    }}
  }
  asort($globvars['pages_urls']);

  // footer menu
  $globvars['menus']['foot'] = [];
  $string = "select * from `footer` order by `f_order`";
  $query = my_query($string);
  while ($row = my_assoc($query)) {
    if($row['f_ext']) {
      $globvars['menus']['foot'][$row['f_ext']] = $row['f_menu'];
    }
    elseif(($url = footer_url($row)) !== false) {
      $globvars['menus']['foot'][$url] = $row['f_menu'];
    }
  }

  // page params
  globvars('params');
  $pararr = explode("/", $globvars['params']);

  // page array
  $url = isset($pararr[0]) && $pararr[0] && isset($globvars['pages_main'][$pararr[0]]) ? $pararr[0] : '';
  $tpage = $globvars['pages_main'][$url];
  $globvars['page'] = $tpage;
  $globvars['page']['meta_image'] = isset($tpage['img_main'][0]) && $tpage['img_main'][0] ? $globvars['base_href'] . $tpage['img_main'][0] : '' ;
  for($i=1; $i<=4; $i++) {
    if(isset($pararr[$i]) && $pararr[$i] && isset($tpage['subs'][$pararr[$i]])) {
      // found sub page
      $url = $tpage['subs'][$pararr[$i]]['url'];
      $tpage = $tpage['subs'][$pararr[$i]];
      $globvars['page'] = $tpage;
      $globvars['page']['meta_image'] = isset($tpage['img_main'][0]) && $tpage['img_main'][0] ? $globvars['base_href'] . $tpage['img_main'][0] : '' ;
    }
    else {
      break ;
    }
  }

  // check master redirects
  $loc301 = false;
  if($globvars['params'] && ! $url) {
    $pfrom = url_noslash($globvars['params']);
    $string = "select * from `redirects` order by `order`";
    $query = my_query($string);
    while($row = my_assoc($query)) {
      $rfr = url_noslash($row['from']);
      $rto = url_noslash($row['to']);
      if(($rfr != $rto) && (strpos($pfrom,$rfr,0) === 0)) {
        $url = $rto;
        $loc301 = true;
        break ;
      }
    }
  }

  $location = '';
  if($url != $globvars['params']) {
    // url not matching parameters
    $location = 'location:' . $globvars['base_href'] . $url ;
    // print_p('a: ' . $location); die;
  }
  elseif(isset($globvars['page']['redirect']) && $globvars['page']['redirect']) {
    // redirect field set for page
    // print_arv($globvars['page']['redirect']);
    $location = 'location:' . $globvars['base_href'] . $globvars['page']['redirect'] ;
    // print_p('b: ' . $location); die;
  }
  elseif(! isset($globvars['page']['id'])) {
    // no page so redirect to root
    $location = 'location:' . $globvars['base_href'] ;
    // print_p('c: ' . $location); die;
  }

  if($location) {
    if($loc301) {
      header($location,true,301);
    }
    else {
      header($location);
    }
    die;
  }

  if($ob) {
    ob_end();
  }

  // print_arv($globvars['pages_pids'],'pages_pids');
  // print_arv($globvars['pages_main'],'pages_main');
  // print_arv($globvars['pages_urls'],'pages_urls');
  // print_arv($globvars['main_root'],'main_root');
  // print_arv($globvars['menus'],'menus');
  // print_arv($globvars['page'],'page');

}

function url_noslash($in) {
  if(substr($in,-1) == '/') {
    $in = substr($in,0,-1);
  }
  if(substr($in,0,1) == '/') {
    $in = substr($in,1);
  }
  return $in;
}

function pages_shop($p_url) {
  global $globvars;
  $where = ($globvars['preview'] != $globvars['sessmd']) ? "
    and `shop_cats`.`c_visible` = 'yes' 
    and `shop_subs`.`s_visible` = 'yes' 
    and `shop_items`.`i_visible` != 'no'
  " : '';
  $string = "
    select *, `shop_brands`.`b_name` as `i_brand`
    from `shop_items` 
    left join `shop_subs` on `shop_items`.`s_id` = `shop_subs`.`s_id`
    left join `shop_cats` on `shop_subs`.`c_id` = `shop_cats`.`c_id`
    left join `shop_brands` on `shop_items`.`b_id` = `shop_brands`.`b_id`
    where 
    `shop_cats`.`c_url` != '' 
    and `shop_subs`.`s_url` != '' 
    and `shop_items`.`i_url` != ''
    $where
    order by 
    `shop_cats`.`c_order` = 0, 
    `shop_cats`.`c_order`, 
    `shop_subs`.`s_order` = 0, 
    `shop_subs`.`s_order`, 
    `shop_items`.`i_order` = 0,
    `shop_items`.`i_order`
  ";
  // print_p($string);
  $query = my_query($string);
  while($row = my_assoc($query)) {

    // template from parent
    $row['i_inc'] = $row['s_inc'] = $row['c_inc'] = $globvars['pages_main'][$p_url]['inc'];
    $row['i_class'] = $row['s_class'] = $row['c_class'] = $globvars['pages_main'][$p_url]['class'];
    
    // urls
    $i_url = $row['i_url'];
    $s_url = $row['s_url'];
    $c_url = $row['c_url'];
    $row['i_url'] = $p_url . '/' . $row['c_url'] . '/' . $row['s_url'] . '/' . $row['i_url'];
    $row['s_url'] = $p_url . '/' . $row['c_url'] . '/' . $row['s_url'];
    $row['c_url'] = $p_url . '/' . $row['c_url'];
    $s_rrl = $row['s_redirect'] ? $row['s_redirect'] : $row['s_url'];
    $c_rrl = $row['c_redirect'] ? $row['c_redirect'] : $row['c_url'];

    // meta title
    if(! $row['c_meta_title']) {
      $row['c_meta_title'] = $row['c_head'] ? $row['c_head'] : $row['c_menu'];
    }
    if(! $row['s_meta_title']) {
      $row['s_meta_title'] = $row['s_head'] ? $row['s_head'] : $row['s_menu'];
    }
    if(! $row['i_meta_title']) {
      $row['i_meta_title'] = $row['i_head'] ? $row['i_head'] : $row['i_menu'];
    }

    // shop cats (c)

    if(! isset($globvars['pages_main'][$p_url]['subs'][$c_url])) {

      // edit & bread
      $row['c_ephp'] = $globvars['pages_main'][$p_url]['t_admin1'];
      $row['c_edit'] = $row['c_ephp'] . '?action=edit&amp;go=' . $row['c_id'];
      $bname = $row['c_menu'] ? $row['c_menu'] : $row['c_head'];
      $row['c_bread'] = array_merge($globvars['pages_main'][$p_url]['bread'],[$c_rrl => $bname]);
      
      // images
      $row['c_img_main'] = preg_filter('/.+/', 'images/head/main/$0', explode(",", $row['c_img_main']));
      $row['c_img_mob'] = preg_filter('/.+/', 'images/head/mob/$0', explode(",", $row['c_img_mob']));
      $row['c_video'] = video_arr($row['c_video']);

      // pages main
      foreach($row as $k => $v) {
        if(substr_count($k,'c_')) {
          $k = str_replace('c_','',$k);
          $globvars['pages_main'][$p_url]['subs'][$c_url][$k] = $v ;
        }
      }

      // sitemap
      if(!in_array($c_rrl,$globvars['sm_excld'])) {
        $globvars['sm_pages'][] = $c_rrl ;
        $globvars['sm_prior'][] = 0.7;
      }
    }

    // shop subcats (s)

    if(! isset($globvars['pages_main'][$p_url]['subs'][$c_url]['subs'][$s_url])) {

      // edit & bread
      $row['s_ephp'] = $globvars['pages_main'][$p_url]['t_admin2'];
      $row['s_edit'] = $row['s_ephp'] . '?action=edit&amp;go=' . $row['s_id'];
      $bname = $row['s_menu'] ? $row['s_menu'] : $row['s_head'];
      $row['s_bread'] = array_merge($globvars['pages_main'][$p_url]['subs'][$c_url]['bread'],[$s_rrl => $bname]);
      
      // images
      $row['s_img_main'] = preg_filter('/.+/', 'images/head/main/$0', explode(",", $row['s_img_main']));
      $row['s_img_mob'] = preg_filter('/.+/', 'images/head/mob/$0', explode(",", $row['s_img_mob']));
      $row['s_video'] = video_arr($row['s_video']);

      // pages main
      foreach($row as $k => $v) {
        if(substr_count($k,'s_')) {
          $k = str_replace('s_','',$k);
          $globvars['pages_main'][$p_url]['subs'][$c_url]['subs'][$s_url][$k] = $v ;
        }
      }

      // sitemap
      if(!in_array($s_rrl,$globvars['sm_excld'])) {
        $globvars['sm_pages'][] = $s_rrl ;
        $globvars['sm_prior'][] = 0.6;
      }
    }

    // shop items (i)

    // edit & bread
    $row['i_ephp'] = $globvars['pages_main'][$p_url]['t_admin3'];
    $row['i_edit'] = $row['i_ephp'] . '?action=edit&amp;go=' . $row['i_id'];
    $bname = $row['i_menu'] ? $row['i_menu'] : $row['i_head'];
    $row['i_bread'] = array_merge($globvars['pages_main'][$p_url]['subs'][$c_url]['subs'][$s_url]['bread'],[$row['i_url'] => $bname]);
    
    // images
    $row['i_img_main'] = preg_filter('/.+/', 'images/head/main/$0', explode(",", $row['i_img_main']));
    $row['i_img_mob'] = preg_filter('/.+/', 'images/head/mob/$0', explode(",", $row['i_img_mob']));
    $row['i_video'] = video_arr($row['i_video']);
    $row['i_vidintro'] = video_arr($row['i_vidintro']);

    // pages main
    foreach($row as $k => $v) {
      if(substr_count($k,'i_')) {
        $k = str_replace('i_','',$k);
        $globvars['pages_main'][$p_url]['subs'][$c_url]['subs'][$s_url]['subs'][$i_url][$k] = $v ;
      }
    }
    
    // sitemap
    if(!in_array($row['i_url'],$globvars['sm_excld'])) {
      $globvars['sm_pages'][] = $row['i_url'] ;
      $globvars['sm_prior'][] = 0.5;
    }

    // main menu
    $globvars['menus']['head'][$p_url]['subs'][$c_url]['menu'] = $row['c_menu'];
    $globvars['menus']['head'][$p_url]['subs'][$c_url]['url'] = $c_rrl;

    $globvars['menus']['head'][$p_url]['subs'][$c_url]['subs'][$s_url]['menu'] = $row['s_menu'];
    $globvars['menus']['head'][$p_url]['subs'][$c_url]['subs'][$s_url]['url'] = $s_rrl;

    if($row['i_menu'] && $row['i_url']) {
      $globvars['menus']['head'][$p_url]['subs'][$c_url]['subs'][$s_url]['subs'][$i_url]['menu'] = $row['i_menu'];
      $globvars['menus']['head'][$p_url]['subs'][$c_url]['subs'][$s_url]['subs'][$i_url]['url'] = $row['i_url'];
    }

  }
}

function pages_blog($p_url) {
  global $globvars;
  $globvars['pages_blog'] = $globvars['blog_all'] = [];
  $where = ($globvars['preview'] != $globvars['sessmd']) ? "
    and `blog_cats`.`b_visible` = 'yes' 
    and `blog_main`.`m_visible` = 'yes'
  " : '';
  $string = "
    select * 
    from `blog_main` 
    left join `blog_cats` on `blog_main`.`b_id` = `blog_cats`.`b_id`
    where `blog_main`.`m_url` != '' 
    and `blog_cats`.`b_url` != ''
    $where
    order by 
    `blog_main`.`m_date` DESC 
  ";
  // print_p($string);
  $query = my_query($string);
  while($row = my_assoc($query)) {
    // template from parent
    $row['b_inc'] = $row['m_inc'] = $globvars['pages_main'][$p_url]['inc'];
    $row['b_class'] = $row['m_class'] = $globvars['pages_main'][$p_url]['class'];
    $row['b_search'] = $row['m_search'] = $globvars['pages_main'][$p_url]['search'];

    // urls
    $b_url = $row['b_url'];
    $m_url = $row['m_url'];
    $row['m_url'] = $p_url . '/' . $row['b_url'] . '/' . $row['m_url'];
    $row['b_url'] = $p_url . '/' . $row['b_url'];

    // meta title
    if(! $row['b_meta_title']) {
      $row['b_meta_title'] = $row['b_head'] ? $row['b_head'] : $row['b_menu'];
    }

    $globvars['pages_blog'][$b_url][$m_url] = $row ;

    // blog cats (b)

    if(! isset($globvars['pages_main'][$p_url]['subs'][$b_url])) {

      // edit & bread
      $row['b_ephp'] = $globvars['pages_main'][$p_url]['t_admin1'];
      $row['b_edit'] = $row['b_ephp'] . '?action=edit&amp;go=' . $row['b_id'];
      $bname = $row['b_menu'] ? $row['b_menu'] : $row['b_head'];
      $row['b_bread'] = array_merge($globvars['pages_main'][$p_url]['bread'],[$row['b_url'] => $bname]);

      // images
      $row['b_img_main'] = preg_filter('/.+/', 'images/blog/main/$0', explode(",", $row['b_img_main']));
      $row['b_img_mob'] = preg_filter('/.+/', 'images/blog/mob/$0', explode(",", $row['b_img_mob']));
      $row['b_video'] = video_arr($row['b_video']);

      // pages main
      foreach($row as $k => $v) {
        if(substr_count($k,'b_')) {
          $k = str_replace('b_','',$k);
          $globvars['pages_main'][$p_url]['subs'][$b_url][$k] = $v ;
        }
      }
      $globvars['pages_main'][$p_url]['subs'][$b_url]['p_url'] = $p_url ;
      $globvars['pages_main'][$p_url]['subs'][$b_url]['b_id'] = $row['b_id'] ;
      $globvars['pages_main'][$p_url]['subs'][$b_url]['b_url'] = $b_url ;

      if(!in_array($row['b_url'],$globvars['sm_excld'])) {
        $globvars['sm_pages'][] = $row['b_url'] ;
        $globvars['sm_prior'][] = 0.7;
      }
    }

    // blog articles (m)

    // edit & bread
    $row['m_ephp'] = $globvars['pages_main'][$p_url]['t_admin2'];
    $row['m_edit'] = $row['m_ephp'] . '?action=edit&amp;go=' . $row['m_id'];
    $bname = $row['m_menu'] ? $row['m_menu'] : $row['m_head'];
    // $row['m_bread'] = array_merge($globvars['pages_main'][$p_url]['subs'][$b_url]['bread'],[$row['m_url'] => $bname]);
    $row['m_bread'] = array_merge($globvars['pages_main'][$p_url]['subs'][$b_url]['bread']);

    // images
    $row['m_img_main'] = preg_filter('/.+/', 'images/head/main/$0', explode(",", $row['m_img_main']));
    $row['m_img_mob'] = preg_filter('/.+/', 'images/head/mob/$0', explode(",", $row['m_img_mob']));
    $row['m_video'] = video_arr($row['m_video']);

    // pages main
    foreach($row as $k => $v) {
      if(substr_count($k,'m_')) {
        $k = str_replace('m_','',$k);
        $globvars['pages_main'][$p_url]['subs'][$b_url]['subs'][$m_url][$k] = $v ;
      }
    }
    $globvars['pages_main'][$p_url]['subs'][$b_url]['subs'][$m_url]['p_url'] = $p_url ;
    $globvars['pages_main'][$p_url]['subs'][$b_url]['subs'][$m_url]['b_id'] = $row['b_id'] ;
    $globvars['pages_main'][$p_url]['subs'][$b_url]['subs'][$m_url]['b_url'] = $b_url ;
    $globvars['pages_main'][$p_url]['subs'][$b_url]['subs'][$m_url]['m_id'] = $row['m_id'] ;
    $globvars['pages_main'][$p_url]['subs'][$b_url]['subs'][$m_url]['m_url'] = $m_url ;

    $globvars['pages_main'][$p_url]['subs'][$b_url]['subs'][$m_url]['head1'] = $row['b_menu'];

    $globvars['blog_all'][$m_url] = $globvars['pages_main'][$p_url]['subs'][$b_url]['subs'][$m_url] ;
   
    if(!in_array($row['m_url'],$globvars['sm_excld'])) {
      $globvars['sm_pages'][] = $row['m_url'] ;
      $globvars['sm_prior'][] = 0.6;
    }

    // main menu
    /*
    $globvars['menus']['head'][$p_url]['subs'][$b_url]['menu'] = $row['b_menu'];
    $globvars['menus']['head'][$p_url]['subs'][$b_url]['menud'] = 'ALL ' . $row['b_menu'];
    $globvars['menus']['head'][$p_url]['subs'][$b_url]['url'] = $row['b_url'];
     
    if($row['m_menu'] && $row['m_url']) {
      $globvars['menus']['head'][$p_url]['subs'][$b_url]['subs'][$m_url]['menu'] = $row['m_menu'];
      $globvars['menus']['head'][$p_url]['subs'][$b_url]['subs'][$m_url]['url'] = $row['m_url'];
    }
    */

  }
  $globvars['pages_main'][$p_url]['subs'] = array_sort($globvars['pages_main'][$p_url]['subs'],'order');
  // print_arv($globvars['pages_blog'],'pages_blog');
  // print_arv($globvars['blog_all'],'blog_all');
}

function page_check($url) {
  global $globvars;

}

function footer_url($row) {
  global $globvars; 
  $url = false;
  if($row['p_id'] && isset($globvars['pages_pids'][$row['p_id']])) {
    if($row['q_id'] && isset($globvars['pages_pids'][$row['p_id']][$row['q_id']])) {
      if($row['r_id'] && isset($globvars['pages_pids'][$row['p_id']][$row['q_id']][$row['r_id']])) {
        $url = $globvars['pages_pids'][$row['p_id']]['url'] . '/' . $globvars['pages_pids'][$row['p_id']][$row['q_id']]['url'] . '/' . $globvars['pages_pids'][$row['p_id']][$row['q_id']][$row['r_id']]['url'];
      }
      else {
        $url = $globvars['pages_pids'][$row['p_id']]['url'] . '/' . $globvars['pages_pids'][$row['p_id']][$row['q_id']]['url'] ;
      }
    }
    else {
      $url = $globvars['pages_pids'][$row['p_id']]['url'] ;
    }
  }
  return $url ;
}

/* BODY */

function body_top() {
  global $globvars;
  // body_ticker(); 
  body_meganav();
}

function body_meganav() {
  global $globvars;
  // print_arv($globvars['menus']['head']);
  ?>
  <div id="meganav1">
    <div id="meganav2">
      <div id="meganav3">
        <div id="megalogo1">
          <div id="megalogo2">
            <a href="<?= $globvars['base_href'] ?>" title="<?= $globvars['comp_name']; ?>">
            <? 
            if(substr_count($globvars['head_logo_w'],'.svg')) {
              include($globvars['head_logo_w']);
            }
            else {
              ?>
              <img src="<?= $globvars['head_logo_w']; ?>" alt="<?= $globvars['comp_name']; ?>">
              <?
            }
            ?>
            </a>
          </div>
          <div id="megamopen">
            <a href="#" title="menu open"><img src="images/mob_menu.png" alt="menu open" width="30"></a>
          </div>
          <? if($globvars['altsites']) { ?>
            <div class="langsel">
              <a href="#" class="langsel1" title="<?= LANGUAGE ?>"><?= LANGUAGE . ' - ' . $globvars['altlangs'][array_key_first($globvars['altlangs'])]['lang'] ; ?></a>
              <div class="langsel2">
                <? $d = 0 ; foreach($globvars['altlangs'] as $c => $a) { if($d++ > 0) { ?>
                <div class="langsel3">
                  <a href="<?= $a['url'] ?>"><?= $a['lang'] ?></a>
                </div>
                <? } } ?>
              </div>
            </div>
          <? } ?>
          <div id="megamclose">
            <a href="#" title="menu close"><img src="images/mob_menuc.png" alt="menu close" width="30"></a>
          </div>
        </div>

        <div id="megamenu">
          <? $m = 0 ; foreach($globvars['menus']['head'] as $a0) { if(isset($a0['menu']) && $a0['menu']) { ?>
            <div class="megamain">
              <a id="megamain<?= $m ?>" class="<?= isset($a0['subs']) ? 'megamainc' : '' ?>" href="<?= isset($a0['subs']) ? '#' : $a0['url'] ?>" title="<?= clean_upper($a0['menu']); ?>"><span><?= clean_upper($a0['menu']); ?></span></a>
            </div>
          <? $m++; } } ?>
        </div>

        <? $m = 0 ; foreach($globvars['menus']['head'] as $a0) { if(isset($a0['menu']) && $a0['menu'] && isset($a0['subs'])) { ?>
        <div class="megadrop" id="megadrop<?= $m ?>">

          <? if($a0['menui']) { ?>
          <div class="megapic">
            <img src="<?= 'images/menu/' . $a0['menui'] ?>" alt="<?= $a0['menuc'] ?>">
            <?= $a0['menuc'] ?>
          </div>
          <? } ?>

          <div class="megadropc">

            <div class="megadropl">
              <?
              $n = 0 ;
              if($a0['menud']) { 
                ?>
                <a href="<?= $a0['url']; ?>" title="<?= clean_upper($a0['menud']); ?>" target="<?= isset($a0['target']) ? $a0['target'] : '' ?>"><?= clean_upper($a0['menud']); ?></a>
                <? 
              }
              foreach($a0['subs'] as $sn => $a1) { 
                if($a1['menu']) {
                  $cls = $cli = $arw = '';
                  if(isset($a1['subs'])) {
                    $cli = 'megadropn' . $m  . '_' . $sn ;
                    $cla = 'megadropa' . $m  . '_' . $sn ;
                    // $arw = '<img src="images/arrow.png">';
                    $arw = '<span class="menuarrow"></span>';
                    $cls = 'megadropn';
                  }
                  $di = isset($a1['menui']) && $a1['menui'] ? 'images/menu/' . $a1['menui'] : '';
                  ?>
                  <a id="<?= $cli ?>" class="megadropi <?= $cls ?>" href="<?= $a1['url']; ?>" title="<?= clean_upper($a1['menu']); ?>" target="<?= isset($a1['target']) ? $a1['target'] : '' ?>"><span id="<?= $cla ?>" class="hover_all hover_<?= $sn ?>" data-click="colour_<?= $sn ?>" data-image="<?= $di ?>"><?= clean_upper($a1['menu']) . $arw; ?></span></a>
                  <? 
                } 
                elseif(isset($a1['subs'])) { 
                  foreach($a1['subs'] as $a2) { 
                    if($a2['menu']) { 
                      $cls = $cli = $arw = '';
                      if(isset($a2['subs'])) {
                        $cls = 'megadropn';
                        $cli = 'megadropn' . $m  . '_' . $n++ ;
                        // $arw = '<img src="images/arrow.png">';
                        $arw = '<span class="menuarrow"></span>';
                      }
                      ?>
                      <a id="<?= $cli ?>" class="mindent megadropi <?= $cls ?>" href="<?= $a2['url']; ?>" title="<?= clean_upper($a2['menu']); ?>" target="<?= isset($a2['target']) ? $a2['target'] : '' ?>"><?= clean_upper($a2['menu']) . $arw; ?></a>
                      <?
                    } 
                  } 
                } 
              }
              ?>
            </div>
            
            <?
           $n = 0 ;
           foreach($a0['subs'] as $sn => $a1) { 
              if($a1['menu']) { 
                if(isset($a1['subs'])) {
                  $cli = 'megadropr' . $m  . '_' . $sn ;
                  ?>
                  <div class="megadropr" id="<?= $cli ?>">
                    <?
                    $j = 0 ;
                    foreach($a1['subs'] as $a2) {
                      if($a1['url'] != $a2['url'] && ! $j) {
                        ?>
                        <a href="<?= $a1['url']; ?>" title="<?= clean_upper(isset($a1['menud']) && $a1['menud'] ? $a1['menud'] : $a1['menu']); ?>" target="<?= isset($a1['target']) ? $a1['target'] : '' ?>"><span class="<?= 'colour_' . $sn ?>"><?= clean_upper(isset($a1['menud']) && $a1['menud'] ? $a1['menud'] : $a1['menu']); ?></span></a>
                        <?
                      }
                      $j++;
                      ?>
                      <a href="<?= $a2['url']; ?>" title="<?= clean_upper($a2['menu']); ?>" target="<?= isset($a2['target']) ? $a2['target'] : '' ?>"><span class="<?= 'hover_' . $sn ?>"><?= clean_upper($a2['menu']); ?></span></a>
                      <?
                    }
                    ?>
                  </div>
                  <?
                }
              } 
              elseif(isset($a1['subs'])) { 
                foreach($a1['subs'] as $a2) { 
                  if($a2['menu']) { 
                    if(isset($a2['subs'])) {
                      $cli = 'megadropr' . $m  . '_' . $n++ ;
                      ?>
                      <div class="megadropr" id="<?= $cli ?>">
                        <?
                        $j = 0 ;
                        foreach($a2['subs'] as $a3) {
                          if($a2['url'] != $a3['url'] && ! $j) {
                            ?>
                            <a href="<?= $a2['url']; ?>" title="<?= clean_upper($a2['menu']); ?>" target="<?= isset($a2['target']) ? $a2['target'] : '' ?>"><?= clean_upper($a2['menu']); ?></a>
                            <?
                          }
                          $j++;
                          ?>
                          <a class="mindent" href="<?= $a3['url']; ?>" title="<?= clean_upper($a3['menu']); ?>" target="<?= isset($a3['target']) ? $a3['target'] : '' ?>"><?= clean_upper($a3['menu']); ?></a>
                          <?
                        }
                        ?>
                      </div>
                      <?
                    }
                  } 
                } 
              } 
            } 
            ?>

          </div>

          <div class="cleaner"></div>
        </div>
        <? } $m++; } ?>

      </div>
    </div>
  </div>

  <div id="megamob">

    <div id="megamobi">
      <a href="#" title="menu"><img src="images/mob_menu.png" alt="menu" width="30"></a>
    </div>
    <? if($globvars['altsites']) { ?>
      <div class="langsel">
        <a href="#" class="langsel1" title="<?= LANGUAGE ?>"><?= LANGUAGE . ' - ' . $globvars['altlangs'][array_key_first($globvars['altlangs'])]['lang'] ; ?></a>
        <div class="langsel2">
          <? $d = 0 ; foreach($globvars['altlangs'] as $c => $a) { if($d++ > 0) { ?>
          <div class="langsel3">
            <a href="<?= $a['url'] ?>"><?= $a['lang'] ?></a>
          </div>
          <? } } ?>
        </div>
      </div>
    <? } ?>

    <div id="megamobl">
      <a href="<?= $globvars['base_href'] ?>" title="<?= $globvars['comp_name']; ?>">
      <? 
      if(substr_count($globvars['head_logo_w'],'.svg')) {
        include($globvars['head_logo_w']);
      }
      else {
        ?>
        <img src="<?= $globvars['head_logo_w']; ?>" alt="<?= $globvars['comp_name']; ?>">
        <?
      }
      ?>
      </a>
    </div>

    <div id="megamobm">
      <? if(isset($globvars['menus']['head'])) { foreach($globvars['menus']['head'] as $a0) { if(isset($a0['menu']) && $a0['menu']) { ?>
      <div class="megamobm">
        <a class="<?= isset($a0['subs']) ? 'megamobmc' : '' ?>" href="<?= isset($a0['subs']) ? '#' : $a0['url'] ?>" title="<?= clean_upper($a0['menu']); ?>" target="<?= isset($a0['target']) ? $a0['target'] : '' ?>"><?= clean_upper($a0['menu']); ?></a>
        <? if(isset($a0['subs'])) { ?>
        <div class="megamobm0">
          <? if($a0['menud']) { ?>
          <a href="<?= $a0['url']; ?>" title="<?= clean_upper($a0['menud']); ?>" target="<?= isset($a0['target']) ? $a0['target'] : '' ?>"><?= clean_upper($a0['menud']); ?></a>
          <? } 
          foreach($a0['subs'] as $a1) { ?>
          <div class="megamobm1<?= $a0['menud'] && $a1['menu'] && isset($a1['subs']) ? ' mindent' : '' ?>">
            <? if($a1['menu']) { ?>
            <a href="<?= $a1['url']; ?>" title="<?= clean_upper($a1['menu']); ?>" target="<?= isset($a1['target']) ? $a1['target'] : '' ?>"><?= clean_upper($a1['menu']); ?></a>
            <? } 
            if(isset($a1['subs'])) { ?>
            <div class="megamobm2">
              <? foreach($a1['subs'] as $a2) { ?>
              <div class="menu3 <?= $a2['menu'] && ($a0['menud'] || $a1['menu']) ? ' mindent' : '' ?>">
                <? if($a2['menu']) { ?>
                <a href="<?= $a2['url']; ?>" title="<?= clean_upper($a2['menu']); ?>" target="<?= isset($a2['target']) ? $a2['target'] : '' ?>"><?= clean_upper($a2['menu']); ?></a>
                <? } 
                if(isset($a2['subs'])) { foreach($a2['subs'] as $a3) { ?>
                <div class="megamobm4 mindent">
                  <a href="<?= $a3['url']; ?>" title="<?= clean_upper($a3['menu']); ?>" target="<?= isset($a3['target']) ? $a3['target'] : '' ?>"><?= clean_upper($a3['menu']); ?></a>
                </div>
                <? } } ?>
              </div>
              <? } ?>
            </div>
            <? } ?>
          </div>
          <? } ?>
        </div>
        <? } ?>
      </div>
      <? } } } ?>
    </div>
  </div>
  <? /* ?>
  <div id="megaicons">
  <? 
  if(! (isset($globvars['page']['hidebutts']) && $globvars['page']['hidebutts'])) {
    menu_icons();
  }
  ?>
  </div>
  <?
  */
}

function body_ticker() {
  $string = "select * from `ticker` order by `order` = 0, `order`";
  $query = my_query($string);
  ?>
  <div id="ticker">
    <?
    while($row = my_assoc($query)) {
      ?>
      <div class="ticker">
        <?
        if($row['url']) {
          ?>
          <a href="<?= $row['url'] ?>">
          <?
        }
        print $row['text'];
        if($row['url']) {
          include('svg/chevron-right.svg');
          ?>
          </a>
          <?
        }
        ?>
      </div>
      <?
    }
    ?>
  </div>
  <?
}

function cms_edit($url="",$target="",$style="") {
  global $globvars;
  if(isset($_SESSION['admin_login']) && $_SESSION['admin_login']) {
    ?>
    <div class="cms_edit" style="<?= $style ?>">
      <?
      if($url && $target) {
        ?>
        <a target="<?= $target ?>" href="<?= $url ?>"><img src="images/cms.png"></a>
        <? 
      }
      elseif(isset($globvars['page']['edit']) && $globvars['page']['edit']) { 
        ?>
        <a target="<?= $globvars['page']['ephp'] ?>" href="<?= 'control/' . $globvars['page']['edit'] ?>"><img src="images/cms.png"></a>
        <? 
      }
      ?>
    </div>
    <?
  }
}

function body_image() {
  global $globvars;
  // print_arv($globvars['page']['video']);
  if(isset($globvars['page']['video']['type'])) {
    // css for video to fit
    $hght = 680;
    $prop = $globvars['page']['video']['prop'] > 0 ? $globvars['page']['video']['prop'] : 56.25 ;
    $maxw = floor($hght / $prop * 100) ;
    ?>
<style>
.video_container::after {
  padding-top:<?= $prop ?>%;
}

/* Fixed height */
@media only screen and (max-width: <?= $maxw ?>px) {
  .menutrans #img_main {
    height:<?= $hght ?>px;
    max-height:auto;
  }

  .video_container {
    height:100%;
  }

  .video_container::after {
    padding-top:0;
  }

  .video_container iframe,
  .video_container video {
    position:relative;
    top:auto;
    left:auto;
    height:100%;
    width:calc(<?= $hght ?>px / <?= $prop ?> * 100);
    margin-left:calc( 50vw - (<?= $hght ?>px / <?= $prop ?> * 50) );
  }
}
@media only screen and (max-width: 800px) {
  .menutrans #img_main {
    height:80vw;
  }
  .video_container iframe,
  .video_container video {
    width:calc(80vw / <?= $prop ?> * 100);
    margin-left:calc( 50vw - (80vw / <?= $prop ?> * 50) );
  }
}
</style>
    <?
    if($globvars['page']['video']['file'] && $globvars['page']['video']['type'] == 'mp4') {
      $dsty = "position:relative; height:0; overflow:hidden; padding-top:calc(56.25%);";
      ?>
      <div id="img_main">
        <div class="video_container">
          <video playsinline autoplay loop muted>
             <source src="<?= $globvars['page']['video']['file'] ?>" type="video/mp4">
          </video>
        </div>
        <div id="img_grad"></div>
      </div>
      <?
    }
    elseif($globvars['page']['video']['frame'] && $globvars['page']['video']['type'] == 'vimeo') {
      $src = $globvars['page']['video']['frame'] . '?background=1' ;
      ?>
      <div id="img_main">
        <div class="video_container" style="background-image:url('<?= $globvars['page']['video']['thumb'] ?>')"><iframe src="<?= $src ?>" frameborder="0" allow="autoplay"></iframe></div>
        <div id="img_grad"></div>
      </div>
      <?
    }
    elseif($globvars['page']['video']['frame'] && $globvars['page']['video']['id'] && $globvars['page']['video']['type'] == 'youtube') {
      $src = $globvars['page']['video']['frame'] . '?rel=0&controls=0&showinfo=0&autoplay=1&mute=1&loop=1&autopause=0&playlist=' . $globvars['page']['video']['id'] ;
      ?>
      <div id="img_main">
        <div class="video_container"><iframe src="<?= $src ?>" frameborder="0" allow="autoplay; encrypted-media"></iframe></div>
        <div id="img_grad"></div>
      </div>
      <?
    }
  }
  elseif($globvars['page']['img_main'] && is_array($globvars['page']['img_main']) && count($globvars['page']['img_main'])) {
    ?>
    <div id="img_main">
      <div id="img_full" class="slide_set">
        <?
        $n = 0 ;
        foreach($globvars['page']['img_main'] as $k => $img_main) {
          if(file_exists($img_main)) {
            $img_mob = isset($globvars['page']['img_mob'][$k]) && file_exists($globvars['page']['img_mob'][$k]) ? $globvars['page']['img_mob'][$k] : $img_main;
            ?>
            <style type="text/css">
              #slide_img_<?= $n ?> { background-image:url('<?= $img_main ?>'); } 
              @media only screen and (max-width: 540px) { 
                #slide_img_<?= $n ?> { background-image:url('<?= $img_mob ?>'); } 
              }
            </style>
            <div id="slide_img_<?= $n ?>" class="slide_img"></div>
            <?
            $n++;
          }
        }
        ?>
        <div id="banners1">
          <div id="banners2" class="maxwid">
            <?
            if($globvars['page']['banner1']) { ?>
            <div id="banner1">
              <? if($globvars['page']['banneri']) { ?>
              <div id="banner1i">
                <img src="<?= 'images/product/symb/page/' . $globvars['page']['banneri'] ?>">
              </div>
              <? } if($globvars['page']['inc'] == 'blog.inc.php') { ?>
              <div class="h1" style="<?= $globvars['page']['banner1c'] ? 'color:' . $globvars['page']['banner1c'] : '' ?>"><?= $globvars['page']['banner1'] ?></div>
              <? } else { ?>
              <h1 style="<?= $globvars['page']['banner1c'] ? 'color:' . $globvars['page']['banner1c'] : '' ?>"><?= $globvars['page']['banner1'] ?></h1>
              <? } ?>
            </div>
            <? } if($globvars['page']['banner2']) { ?>
            <div id="banner2">
              <?= $globvars['page']['banner2'] ?>
            </div>
            <? } ?>
          </div>
        </div>
      </div>
      <? /* ?><div id="img_grad"></div><? */ ?>
    </div>
    <?
  }
}

function body_bread($cls='') {
  global $globvars;
  // print_arv($globvars['page']['bread'],'bread');
  ?>
  <div id="breadcrumbs" class="maxwid lgrey <?= $cls ?>">
    <? if(isset($globvars['page']['bread']) && count($globvars['page']['bread'])) { ?>
    <a href="<?= $globvars['base_href'] ?>" title="Home">Home</a>
    <? $i = 0 ;
    foreach($globvars['page']['bread'] as $u => $p) { if($u && $p) {
      ?> &nbsp;&gt;&nbsp; <a href="<?= $u ?>" title="<?= $p ?>" style="<?= ++$i == count($globvars['page']['bread']) ? 'font-weight:bold' : '' ?>"><?= $p ?></a> <?
    } } }
    ?>
  </div>
  <?
}

function body_h1() {
  global $globvars;
  if($globvars['page']['head1']) { ?>
    <h1><?= $globvars['page']['head1'] ; ?></h1>
  <?
  }
}

function body_html($n,$cls='') {
  global $globvars;
  if((isset($globvars['page']['html'.$n]) && $globvars['page']['html'.$n]) || (isset($globvars['page']['head'.$n]) && $globvars['page']['head'.$n])) {
    if(isset($globvars['page']['head'.$n]) && $globvars['page']['head'.$n]) { 
      if($cls) { ?>
        <div class="<?= $cls ?>"><?= $globvars['page']['head'.$n] ; ?></div>
      <? } elseif ($n > 1) { ?>
        <h2><?= $globvars['page']['head'.$n] ; ?></h2>
      <? } else { ?>
        <h1><?= $globvars['page']['head'.$n] ; ?></h1>
      <? 
      } 
    } 
    if(isset($globvars['page']['html'.$n]) && $globvars['page']['html'.$n]) { 
      ?>
      <div class="maxwid maxtext"><?= dispc($globvars['page']['html'.$n]); ?></div>
      <? 
    }
    if($globvars['page']['butturl'.$n] && $globvars['page']['button'.$n]) { 
      ?>
      <div class="htmlbutt button"><a href="<?= $globvars['page']['butturl'.$n]; ?>" target="<?= strpos($globvars['page']['butturl'.$n], "http") === 0 ? '_blank' : '' ?>"><?= $globvars['page']['button'.$n]; ?></a></div>
      <? 
    } 
  }
}

function body_foot() {
  global $globvars;
  if($globvars['page']['url'] != 'newsletter') {
  ?>
  <div id="newsbar" class="button">
    <span>SIGN UP TO OUR NEWSLETTER</span> <a href="newsletter">CLICK HERE</a>
  </div>
  <? } ?>
  <div id="footer1">
    <? news_form(); ?>
    <div id="footer2" class="maxwid">
      <div id="footright">
       <?
       if(isset($globvars['menus']['foot'])) { 
         // print_arv($globvars['menus']['foot']); 
        ?>
        <div id="footmenu">
          <div id="footmenu1">
            <div class="footmenu1">
              <? $n = 0 ; foreach($globvars['menus']['foot'] as $u => $m) { ?>
                <div class="footmenu"><a href="<?= $u; ?>" title="<?= $m; ?>" target="<?= strpos($u, "http") === 0 ? '_blank' : '' ?>"><?= $m; ?></a></div>
              <? if(++$n == ceil(count($globvars['menus']['foot']) / 2)) { ?>
                </div><div class="footmenu1">
            <? } } ?>
            </div>
          </div>
        </div>
        <? } ?>
        <div id="footmodels">
          <div id="footmodels1">
              <div class="footmenu"><?= $globvars['foot_systems'] ?></div>
            <? 
            // print_arv($globvars['systems']);
            foreach($globvars['systems'] as $s) { ?>
              <div class="footmenu"><a href="<?= $s['model']['p_url'] . '/' . $s['model']['q_url']; ?>" title="<?= $s['model']['q_menu']; ?>"><span class="hover_<?= $s['model']['q_url'] ?>"><?= $s['model']['q_menu']; ?></span></a></div>
            <? } ?>
          </div>
        </div>
      </div>

      <div id="footleft">
        <div id="footlogo">
          <a href="<?= $globvars['base_href'] ?>" title="<?= $globvars['comp_name']; ?>">
          <? 
          if(substr_count($globvars['head_logo_w'],'.svg')) {
            include($globvars['head_logo_w']);
          }
          else {
            ?>
            <img src="<?= $globvars['head_logo_w']; ?>" alt="<?= $globvars['comp_name']; ?>">
            <?
          }
          ?>
          </a>
        </div>
        <? body_social() ?>
        <div id="footreg" class="nobr">
          <?= str_replace('[year]',date('Y'),$globvars['foot_copyright']) ?><span id="cssSize"></span>
        </div>
      </div>

      <div class="cleaner"></div>

    </div>
  </div>
  <div id="cookiepolicy" style="display:none;">
    <div id="cookiepolicy1">
      <div id="cookieptext">At <?= $globvars['comp_name'] ?>, we use cookies to enhance your online experience with us. By using this website you agree to our <a href="privacy">Cookie Policy</a>. <span id="cookiepclose"><a href="#">ACCEPT &#x2715;</a></span>
      </div>
    </div>
  </div>
  <?
  /*
  if(! ( (isset($globvars['page']['hidebutts']) && $globvars['page']['hidebutts']) || in_array($globvars['page']['inc'], ['newsletter.inc.php','account.inc.php','basket.inc.php','checkout.inc.php','payment.inc.php']) ) ) {
    ?>
    <div id="exitmodal" style="display:none;">
      <div id="exitimage" style="background-image:url('images/popup.jpg');"></div>
      <div id="exitright">
        <div id="exithead">KEEP IN TOUCH</div>
        <div id="exittext"></div>
        <form id="exitform" onsubmit="exit_submit(); return false;" method="post" action="#">
          <input type="text" name="exit_email" id="exit_email" placeholder="EMAIL">
          <input type="text" name="exit_name" id="exit_name" placeholder="NAME">
          <input type="submit" class="submit" value="SUBSCRIBE">
        </form>
      </div>
      <a href="#" id="exitclose">&times;</a>
    </div>
    <?
  }
  */
}

function body_social() {
  $string = "select * from `social` order by `order`";
  $query = my_query($string);
  if(my_rows($query)) {
    ?>
    <div id="footsocial">
      <?
      while($row = my_assoc($query)) {
        if($row['icon'] && $row['link']) {
          ?>
          <div class="footsocial"><a href="<?= $row['link'] ?>" title="<?= $row['title'] ?>" target="_blank"><img src="<?= 'images/social/' . $row['icon'] ?>" alt="<?= $row['title'] ?>"></a></div>
          <?
        }
      }
      ?>
    </div>
    <?
  }
}

/*  CMS */

function cms_pages() {
  // light call for admin only
  global $globvars;
  $string = "
    select * from `pages_main`
    order by 
    `pages_main`.`p_order` = 0, 
    `pages_main`.`p_order`
  ";
  $query = my_query($string);
  while($row = my_assoc($query)) {
    // locks
    if($row['p_edit_temp'] == 'no') {
      $globvars['lock_temp'][$row['p_id']] = $row['p_url'];
    }
    if($row['p_edit_url'] == 'no') {
      $globvars['lock_url'][$row['p_id']] = $row['p_url'];
    }
    if($row['p_edit_vis'] == 'no') {
      $globvars['lock_vis'][$row['p_id']] = $row['p_url'];
    }
    if($row['p_edit_del'] == 'no') {
      $globvars['lock_del'][$row['p_id']] = $row['p_url'];
    }
    if(($temp_arr = temp_arr($row['p_template'])) && $temp_arr['t_pages'] && ($temp_arr['t_pages'] != 'pages_main')) {
      $tf = $temp_arr['t_pages'] ;
      $globvars['main_root'][$tf] = $row['p_url'];
    }
    // pages_admin
    $globvars['pages_admin'][$row['p_id']] = $row ;
  }
  // print_arv($globvars['pages_admin'],'pages_admin');
  // print_arv($globvars['main_root'],'main_root');
}

function cms_include() {
  global $globvars, $globvarr; extract($globvars) ;
  // print_arv($globvars['templates']);
  $f = str_replace('_include', '_template', $fname);
  $t = 0 ;
  foreach($globvars['templates'] as $k => $a) {
    if($a['t_inc'] == 'include.inc.php') {
      $t = $k;
    }
  }
  if($t && $i_row[$f] == $t) {
    if($globvars['action'] == 'edit' || $globvars['action'] == 'add') {
      $files = array();
      if($handle = opendir("../")) {
        while(false !== ($file = readdir($handle))) {
          if(substr_count( $file,'inc.php')) { $files[] = $file; }
        }
        closedir($handle);
        sort($files);
        ?>
        <select class="chosen-select" name="<?= $fname ?>" id="<?= $fname ?>" size="1" onchange="fldchg++;" style="width:500px;">
          <option value="">*** Select ***</a>
          <? foreach($files as $file) { ?>
          <option value="<?= optsel($file,$dval) ?>"><?= $file ?></a>
          <? } ?>
        </select>
        <?
      }
    }
    else {
      return true;
    }
  }
  else {
    print 'N/A';
  }
}

function cms_options($i) {
  global $globvars, $globvarr; extract($globvars) ;
  if(isset($globvars['templates'][$i_row['t_id']]) && in_array($globvars['templates'][$i_row['t_id']]['t_inc'],['gallery.inc.php','staff.inc.php'])) {
    print 'Select Gallery';
  }
  else {
    print 'N/A';
  }
}

function cms_columns() {
  global $globvars, $globvarr; extract($globvars) ;
  $video_arr = [];
  $string = "select * from `video_map` order by `id`";
  $query = my_query($string);
  while($row = my_assoc($query)) {
    $video_arr[$row['id']] = $row ;
  }
  ?>
  <br><div style="text-align:center;"><input type="submit" name="Submit" value="SAVE" class="submit"></div><br><br>
  <?
  $mw = 1000 ;
  $mh = 1000 ;
  $mq = 85 ;
  // print_arv($page);
  $p_id = isset($page['p_id']) ? $page['p_id'] : 0;
  $q_id = isset($page['q_id']) ? $page['q_id'] : 0;
  $r_id = isset($page['r_id']) ? $page['r_id'] : 0;
  $c_id = isset($page['c_id']) ? $page['c_id'] : 0;
  $s_id = isset($page['s_id']) ? $page['s_id'] : 0;
  $i_id = isset($page['i_id']) ? $page['i_id'] : 0;
  if(! ($p_id || $q_id || $r_id || $c_id || $s_id || $i_id)) { return ; }
  $fpath = "../images/columns";
  if($globvars['done']) {
    globvars('(array) z_id','(array) z_del','(array) z_upd','(array) z_order','(array) z_image','(array) z_backc','(array) z_ipos','(array) z_head','(array) z_html','(array) z_button','(array) z_url','(array) z_video');
    foreach($globvars['z_id'] as $z_id) {
      $string = '';
      if($z_id) {
        // existing
        if(isset($globvars['z_del'][$z_id]) && $globvars['z_del'][$z_id] == 'y') {
          // delete
          $lt = 'DELETE';
          $string = "delete from `cont_columns` where `id` = '$z_id'";
        }
        elseif(isset($globvars['z_upd'][$z_id]) && $globvars['z_upd'][$z_id] == 'y') {
          // update
          $fname = 'up_z_image_' . $z_id;
          $upload = upload_move($fname,'image',$fpath,'','',$mw,$mh,$mq);
          $image = ($upload['res'] == 'ok') ? $upload['fname'] : $globvars['z_image'][$z_id];
          $lt = 'UPDATE';
          $html = $globvars['z_html'][$z_id] ? $_POST['z_html'][$z_id] : '' ;
          $where = cms_colwhere($p_id,$q_id,$r_id,$c_id,$s_id,$i_id);
          if($where) {
            $string = "update `cont_columns` set $where,
              `order`  = '{$globvars['z_order'][$z_id]}',
              `image`  = '{$image}',
              `backc`  = '{$globvars['z_backc'][$z_id]}',
              `ipos`   = '{$globvars['z_ipos'][$z_id]}',
              `head`   = '{$globvars['z_head'][$z_id]}',
              `html`   = '{$html}',
              `button` = '{$globvars['z_button'][$z_id]}',
              `url`    = '{$globvars['z_url'][$z_id]}',
              `video`  = '{$globvars['z_video'][$z_id]}'
              where `id` = '$z_id'
            ";
          }
        }
      }
      elseif($globvars['z_head'][0] || $globvars['z_html'][0]) {
        // new
        $fname = 'up_z_image_' . $z_id;
        $upload = upload_move($fname,'image',$fpath,'','',$mw,$mh,$mq);
        $image = ($upload['res'] == 'ok') ? $upload['fname'] : $globvars['z_image'][$z_id];
        $lt = 'INSERT';
        $html = $globvars['z_html'][0] ? $_POST['z_html'][0] : '' ;
        if($where = cms_colwhere($p_id,$q_id,$r_id,$c_id,$s_id,$i_id)) {
          $string = "insert into `cont_columns` set $where,
            `order`  = '{$globvars['z_order'][0]}',
            `image`  = '{$image}',
            `backc`  = '{$globvars['z_backc'][0]}',
            `ipos`   = '{$globvars['z_ipos'][0]}',
            `head`   = '{$globvars['z_head'][0]}',
            `html`   = '{$html}',
            `button` = '{$globvars['z_button'][$z_id]}',
            `url`    = '{$globvars['z_url'][$z_id]}',
            `video`  = '{$globvars['z_video'][$z_id]}'
          ";
        }
      }
      if($string) {
        // print_p($string);
        my_query($string);
        logtable($lt,$cntrl_user,'cont_columns',$string);
        if($debug) { print_d($string,__LINE__,__FILE__); }
      }
    }
  }
  $files = [];
  if($handle = opendir($fpath)) {
    while(false !== ($file = readdir($handle))) {
      if($file != "." && $file != "..") {
        $files[] = $file;
      }
    }
  }
  closedir($handle);
  sort($files);
  if($where = cms_colwhere($p_id,$q_id,$r_id,$c_id,$s_id,$i_id)) {
    $string = "select * from `cont_columns` where $where order by `order`";
    // print_p($string);
    $query = my_query($string);
    if(my_rows($query)) {
      ?>
      <h3>COLUMN ENTRIES</h3>
      <?
      while($row = my_assoc($query)) {
        ?>
        <table class="tabler" width="<?= isvar($formwidth) ? $formwidth : '100%'; ?>" cellspacing="0" cellpadding="4" border="0">
          <tr>
            <td width="<?= isvar($formleftc) ? $formleftc : ''; ?>" class="thb button">Order</td>
            <td style="height:26px;">
              <input name="z_order[<?= $row['id'] ?>]" type="text" size="3" maxlength="3" value="<?= $row['order'] ?>" onchange="$('#z_upd_<?= $row['id'] ?>').val('y')">
              <input name="z_id[<?= $row['id'] ?>]" type="hidden" value="<?= $row['id'] ?>">
              <input name="z_upd[<?= $row['id'] ?>]" id="z_upd_<?= $row['id'] ?>" type="hidden" value="">
            </td>
            <td width="<?= isvar($formrghtc) ? $formrghtc : ''; ?>" class="th button">
              99 to hide - Delete <input name="z_del[<?= $row['id'] ?>]" type="checkbox" value="y">
            </td>
          </tr>
          <tr>
            <td class="thb button">
            Image
            <? 
            $fid = 'z_image_' . $row['id'];
            $fname = 'z_image[' . $row['id'] . ']';
            $dval = $row['image'];
            $och = "$('#z_upd_" . $row['id'] . "').val('y')";
            image_pop($fpath . '/' . $dval,$fid) ; 
            ?>
            </td>
            <td style="height:26px;">
              <? select_image($files,$fpath,$fname,$fid,$dval,$och); ?>
            </td>
            <td class="th button">1000x1000</td>
          </tr>
          <tr>
            <td class="thb button">Image Position</td>
            <td style="height:26px;">
              <select name="z_ipos[<?= $row['id'] ?>]" onchange="$('#z_upd_<?= $row['id'] ?>').val('y')">
                <option value="">none</option>
                <option value="<?= optsel('left',$row['ipos']) ?>">left</option>
                <option value="<?= optsel('right',$row['ipos']) ?>">right</option>
              </select>
            </td>
            <td class="th button"></td>
          </tr>
          <tr>
            <td class="thb button">Background</td>
            <td style="height:26px;">
              White <input name="z_backc[<?= $row['id'] ?>]" type="radio" value="<?= optchk('white',$row['backc']) ?>" onchange="$('#z_upd_<?= $row['id'] ?>').val('y')">
              Light <input name="z_backc[<?= $row['id'] ?>]" type="radio" value="<?= optchk('light',$row['backc']) ?>" onchange="$('#z_upd_<?= $row['id'] ?>').val('y')">
              Dark  <input name="z_backc[<?= $row['id'] ?>]" type="radio" value="<?= optchk('dark',$row['backc']) ?>" onchange="$('#z_upd_<?= $row['id'] ?>').val('y')">
            </td>
            <td class="th button"></td>
          </tr>
          <tr>
            <td class="thb button">Heading</td>
            <td style="height:26px;">
              <input name="z_head[<?= $row['id'] ?>]" type="text" size="50" maxlength="100" value="<?= $row['head'] ?>" onchange="$('#z_upd_<?= $row['id'] ?>').val('y')">
            </td>
            <td class="th button"></td>
          </tr>
          <tr>
            <td class="thb button">Text</td>
            <td>
              <div style="font-family:Arial; font-size:11px; padding:0 1px 5px 1px; color:#C00000">COPY TEXT TO NOTEPAD FIRST TO REMOVE ALL HIDDEN FORMATTING THEN COPY AGAIN FROM THERE BEFORE PASTING HERE</div>
              <textarea class="ckeditor" name="z_html[<?= $row['id'] ?>]" id="z_html_<?= $row['id'] ?>"><?= $row['html'] ?></textarea> 
              <? ckeditor_js('z_html_' . $row['id']); ?>
              <script>
              ck_z_html_<?= $row['id'] ?>.on('change', function() { 
                $('#z_upd_<?= $row['id'] ?>').val('y');
              });
              </script>
            </td>
            <td class="th button"></td>
          </tr>
          <tr>
            <td class="thb button">Button</td>
            <td style="height:26px;">
              <input name="z_button[<?= $row['id'] ?>]" type="text" size="50" maxlength="30" value="<?= $row['button'] ?>" onchange="$('#z_upd_<?= $row['id'] ?>').val('y')">
            </td>
            <td class="th button"></td>
          </tr>
          <tr>
            <td class="thb button">URL</td>
            <td style="height:26px;">
              <input name="z_url[<?= $row['id'] ?>]" type="text" size="50" maxlength="200" value="<?= $row['url'] ?>" onchange="$('#z_upd_<?= $row['id'] ?>').val('y')">
            </td>
            <td class="th button"></td>
          </tr>
          <tr>
            <td class="thb button">Video</td>
            <td style="height:26px;">
              <select class="chosen-select" name="z_video[<?= $row['id'] ?>]" size="1" onchange="$('#z_upd_<?= $row['id'] ?>').val('y')" style="width: 500px; display: none;"> 
                <option value="">** Select **</option>
                <? foreach($video_arr as $k => $v) { ?>
                <option value="<?= optsel($k,$row['video']) ?>"><?= $v['id'] . ': (' . $v['note'] . ')' ?></option>
                <? } ?>
              </select>
            </td>
            <td class="th button"><a href="video_map.php" target="video_map.php">VIDEOS</a></td>
          </tr>
        </table><br><br>
        <?
      }
    }
  }
  ?>
  <h3>ADD COLUMN ENTRY</h3>
  <table class="tabler" width="<?= isvar($formwidth) ? $formwidth : '100%'; ?>" cellspacing="0" cellpadding="4" border="0">
    <tr>
      <td width="<?= isvar($formleftc) ? $formleftc : ''; ?>" class="thb button">Order</td>
      <td style="height:26px;">
        <input name="z_order[0]" type="text" size="3" maxlength="3">
        <input name="z_id[0]" type="hidden" value="0">
      </td>
      <td width="<?= isvar($formrghtc) ? $formrghtc : ''; ?>" class="th button"></td>
    </tr>
    <tr>
      <td class="thb button">Image</td>
      <td style="height:26px;">
        <?
        $fid = 'z_image_0';
        $fname = 'z_image[0]';
        select_image($files,$fpath,$fname,$fid);
        ?>
      </td>
      <td class="th button">1000x1000</td>
    </tr>
    <tr>
      <td class="thb button">Image Position</td>
      <td style="height:26px;">
        <select name="z_ipos[0]">
          <option value="">none</option>
          <option value="left">left</option>
          <option value="right">right</option>
        </select>
      </td>
      <td class="th button"></td>
    </tr>
    <tr>
      <td class="thb button">Background</td>
      <td style="height:26px;">
        White <input name="z_backc[0]" type="radio" value="white" checked="checked">
        Light <input name="z_backc[0]" type="radio" value="light">
        Dark  <input name="z_backc[0]" type="radio" value="dark">
      </td>
      <td class="th button"></td>
    </tr>
    <tr>
      <td class="thb button">Heading</td>
      <td style="height:26px;">
        <input name="z_head[0]" type="text" size="50" maxlength="100">
      </td>
      <td class="th button"></td>
    </tr>
    <tr>
      <td class="thb button">Text</td>
      <td>
        <div style="font-family:Arial; font-size:11px; padding:0 1px 5px 1px; color:#C00000">COPY TEXT TO NOTEPAD FIRST TO REMOVE ALL HIDDEN FORMATTING THEN COPY AGAIN FROM THERE BEFORE PASTING HERE</div>
        <textarea class="ckeditor" name="z_html[0]" id="z_html_0"></textarea> 
        <?
        ckeditor_js('z_html_0'); 
        ?>
      </td>
      <td class="th button"></td>
    </tr>
    <tr>
      <td class="thb button">Button</td>
      <td style="height:26px;">
        <input name="z_button[0]" type="text" size="50" maxlength="30">
      </td>
      <td class="th button"></td>
    </tr>
    <tr>
      <td class="thb button">URL</td>
      <td style="height:26px;">
        <input name="z_url[0]" type="text" size="50" maxlength="200">
      </td>
      <td class="th button"></td>
    </tr>
    <tr>
      <td class="thb button">Video</td>
      <td style="height:26px;">
        <select class="chosen-select" name="z_video[0]" size="1" style="width: 500px; display: none;"> 
          <option value="">** Select **</option>
          <? foreach($video_arr as $k => $v) { ?>
          <option value="<?= $k ?>"><?= $v['id'] . ': (' . $v['note'] . ')' ?></option>
          <? } ?>
        </select>
      </td>
      <td class="th button"><a href="video_map.php" target="video_map.php">VIDEOS</a></td>
    </tr>
  </table><br><br>
  <?
}

function cms_colwhere($p_id='',$q_id='',$r_id='',$c_id='',$s_id='',$i_id='') {
  $out = '';
  if($i_id) {
    $out = "`i_id`  = '$i_id'";
  }
  elseif($s_id) {
    $out = "`s_id`  = '$s_id'";
  }
  elseif($c_id) {
    $out = "`c_id`  = '$c_id'";
  }
  elseif($r_id) {
    $out = "`r_id`  = '$r_id'";
  }
  elseif($q_id) {
    $out = "`q_id`  = '$q_id'";
  }
  elseif($p_id) {
    $out = "`p_id`  = '$p_id'";
  }
  return $out ;
}

function cms_shopopts() {
  global $globvars, $globvarr; extract($globvars) ;
  $globvars['shopimgs'] = $globvars['shopopts'] = [];

  // get shop item 
  $i_id = 0 ;
  $string1 = '';
  $globvars['notebutt1'] = '';
  $globvars['pricecalcs'] = [];

  if(($globvars['action'] == 'edit') && $globvars['go']) {
    $string = "select * from `shop_items` where `i_id` = '{$globvars['go']}' limit 1";
    $query = my_query($string);
    if(my_rows($query)) {
      $i_row = my_assoc($query);
      $i_id = $i_row['i_id'];
      // subcats link
      $globvars['notebutt1'] = '<a style="width:110px" href="shop_subs.php?action=edit&amp;go=[[s_id]]" target="shop_subs.php">EDIT SUBCAT</a>';
      // images folders
      if(! file_exists('../images/shop/' . $i_id)) {
        make_dir('../images/shop', $i_id);
        make_dir('../images/shop/' . $i_id, 'full');
        make_dir('../images/shop/' . $i_id, 'large');
        make_dir('../images/shop/' . $i_id, 'small');
      }
      // calc/min/max
      $string1 = "where `i_id` = '$i_id'";
      if($globvars['done']) {
        globvars('i_price','i_discprice','i_discpcnt');
        $i_row['i_price'] = $globvars['i_price'];
        $i_row['i_discprice'] = $globvars['i_discprice'];
        $i_row['i_discpcnt'] = $globvars['i_discpcnt'];
      }
      $p_arr = price_calc($i_row['i_price'],$i_row['i_discprice'],$i_row['i_discpcnt']);
      $_POST['i_pricecalc'] = $globvars['pricecalcs'][0] = $p_arr['i_pricecalc'];
    }
  }

  // get shop_options
  $ostring = "select * from `shop_options` $string1 order by `o_order` = 0, `o_order`, `o_option1`, `o_option2`";
  $oquery = my_query($ostring);
  $c = 0 ;
  while($row = my_assoc($oquery)) {
    $globvars['shopopts'][$row['i_id']][$row['o_id']] = $row ;
    $globvars['pricecalcs'][$row['o_id']] = $row['o_pricecalc'] ;
  }

  // get shop_images
  $istring = "select * from `shop_images` $string1 order by `g_order` = 0, `g_order`, `g_file`";
  $iquery = my_query($istring);
  while($row = my_assoc($iquery)) { 
    $globvars['shopimgs'][$row['i_id']][$row['g_id']] = $row ;
  } 

  // updates
  if($i_id && $globvars['done']) {
    globvars('(array) o_id','(array) o_del','(array) o_upd','(array) o_images','(array) g_id','(array) g_del','(array) g_order');
    $flds = ['o_order','o_visible','o_sku','o_mpn','o_gtin','o_option1','o_option2','o_price','o_discprice','o_discpcnt','o_stock','o_expected','o_images'];
    $decs = ['o_price','o_discprice','o_discpcnt'];
    foreach($flds as $fld) {
      globvars('(array) ' . $fld);
    }

    // update shop_options
    foreach($globvars['o_id'] as $o_id) {
      $string = '';
      $c = 0 ;
      if($o_id) {
        // existing
        if(isset($globvars['o_del'][$o_id]) && $globvars['o_del'][$o_id] == 'y') {
          // delete
          $lt = 'DELETE';
          $string = "delete from `shop_options` where `o_id` = '$o_id' and `i_id`  = '$i_id'";
          unset($globvars['shopopts'][$i_id][$o_id]);
          unset($globvars['pricecalcs'][$o_id]);
        }
        elseif(isset($globvars['o_upd'][$o_id]) && $globvars['o_upd'][$o_id] == 'y') {
          // update
          $lt = 'UPDATE';
          $string = '';
          foreach($flds as $fld) {
            if(in_array($fld, $decs)) {
              if(! $globvars[$fld][$o_id]){ $globvars[$fld][$o_id] = 0; }
              $globvars[$fld][$o_id] = number_format($globvars[$fld][$o_id],2,'.','');
            }
            $globvars['shopopts'][$i_id][$o_id][$fld] = $globvars[$fld][$o_id];
            $optv = ($fld == 'o_expected') ? cdate($globvars['o_expected'][$o_id],'Y-m-d','0000-00-00') : $globvars[$fld][$o_id];
            $string .= "`{$fld}` = '{$optv}', ";
          }
          $p_arr = price_calc($i_row['i_price'],$i_row['i_discprice'],$i_row['i_discpcnt'],$i_row['i_discsubs'],
                              $globvars['o_price'][$o_id],$globvars['o_discprice'][$o_id],$globvars['o_discpcnt'][$o_id]);
          $globvars['pricecalcs'][$o_id] = $globvars['shopopts'][$i_id][$o_id]['o_pricecalc'] = $p_arr['o_pricecalc'] ;
          $string = "update `shop_options` set {$string} `o_pricecalc` = '{$p_arr['o_pricecalc']}' where `o_id` = '$o_id' and `i_id`  = '$i_id' limit 1";
        }
      }
      elseif($globvars['o_option1'][0] || $globvars['o_option2'][0]) {
        // new
        $lt = 'INSERT';
        $string = '';
        foreach($flds as $fld) {
          if(in_array($fld, $decs)) {
            if(! $globvars[$fld][0]){ $globvars[$fld][0] = 0; }
            $globvars[$fld][0] = number_format($globvars[$fld][0],2,'.','');
          }
          $globvars['shopopts'][$i_id][0][$fld] = $globvars[$fld][0];
          $optv = ($fld == 'expected') ? cdate($globvars['o_expected'][0],'Y-m-d','0000-00-00') : $globvars[$fld][0];
          $string .= "`{$fld}` = '{$optv}', ";
        }
        $p_arr = price_calc($i_row['i_price'],$i_row['i_discprice'],$i_row['i_discpcnt'],$i_row['i_discsubs'],
                            $globvars['o_price'][0],$globvars['o_discprice'][0],$globvars['o_discpcnt'][0]);
        $string = "insert into `shop_options` set `i_id` = '$i_id', {$string} `o_pricecalc` = '{$p_arr['o_pricecalc']}'";
        $globvars['shopopts'][$i_id][0]['o_pricecalc'] = $p_arr['o_pricecalc'];
      }
      if($string) {
        // print_p($string);
        my_query($string);
        if($lt == 'INSERT' && $o_id = my_id()) {
          $globvars['shopopts'][$i_id][$o_id] = $globvars['shopopts'][$i_id][0];
          unset($globvars['shopopts'][$i_id][0]);
          $globvars['pricecalcs'][$o_id] = $p_arr['o_pricecalc'] ;
        }
        logtable($lt,$cntrl_user,'shop_options',$string);
        if($debug) { print_d($string,__LINE__,__FILE__); }
      }
    }

    // update shop_images
    foreach($globvars['g_id'] as $g_id) {
      if(isset($globvars['shopimgs'][$i_id][$g_id])) {
        if(isset($globvars['g_del'][$g_id]) && $globvars['g_del'][$g_id] == 'y') {
          $string = "delete from `shop_images` where `g_id` = '$g_id' and `i_id` = '{$globvars['go']}' limit 1";
          my_query($string);
          logtable('DELETE',$cntrl_user,'shop_images',$string);
          if($debug) { print_d($string,__LINE__,__FILE__); }
          del_file("../images/shop/{$globvars['go']}/full",$globvars['shopimgs'][$i_id][$g_id]['g_file']);
          del_file("../images/shop/{$globvars['go']}/large",$globvars['shopimgs'][$i_id][$g_id]['g_file']);
          del_file("../images/shop/{$globvars['go']}/small",$globvars['shopimgs'][$i_id][$g_id]['g_file']);
          unset($globvars['shopimgs'][$i_id][$g_id]);
        }
        elseif(isset($globvars['g_order'][$g_id]) && ($globvars['shopimgs'][$i_id][$g_id]['g_order'] != $globvars['g_order'][$g_id])) {
          $string = "update `shop_images` set `g_order` = '{$globvars['g_order'][$g_id]}' where `g_id` = '$g_id' and `i_id` = '{$globvars['go']}' limit 1";
          my_query($string);
          logtable('UPDATE',$cntrl_user,'shop_images',$string);
          if($debug) { print_d($string,__LINE__,__FILE__); }
          $globvars['shopimgs'][$i_id][$g_id]['g_order'] = $globvars['g_order'][$g_id];
        }      
      }
    }

    // update item min/max

    $pr = price_range();
    $_POST['i_pricemin'] = $pr['pricemin'];
    $_POST['i_pricemax'] = $pr['pricemax'];

    // sort arrays
    if(isset($globvars['shopimgs'][$i_id])) {
      $globvars['shopimgs'][$i_id] = array_sort($globvars['shopimgs'][$i_id],['g_order','g_file'],'',true);
    }
    if(isset($globvars['shopopts'][$i_id])) {
      $globvars['shopopts'][$i_id] = array_sort($globvars['shopopts'][$i_id],'o_order','',true);
    }
  }
  // print_arv($globvars['shopimgs']);
  // print_arv($globvars['shopopts']);
  // print_arv($globvars['pricecalcs']);
}

function cms_shop() {
  global $globvars, $globvarr; extract($globvars) ;
  if($php_self != 'shop_items.php') {
    return;
  }
  ?>
  <br><div style="text-align:center;"><input type="submit" name="Submit" value="SAVE" class="submit"></div><br><br>
  <?
  // print_arv($page);
  $option1 = $option2 = 'N/A';
  if(isset($page['i_option1']) && $page['i_option1']) {
    $option1 = $page['i_option1'];
  }
  if(isset($page['i_option2']) && $page['i_option2']) {
    $option2 = $page['i_option2'];
  }
  ?>

  <h3>PRODUCT IMAGES (ORD NUMBERS FOR CATEGORY PAGE)</h3>
  <table class="tabler" width="<?= isvar($formwidth) ? $formwidth : '100%'; ?>" cellspacing="0" cellpadding="4" border="0">
    <tr>
      <td style="background-color:#eeeeee;height:20px;" id="upshopv">
      <? 
      if(isset($globvars['shopimgs'][$page['i_id']])) { foreach($globvars['shopimgs'][$page['i_id']] as $row) { 
        print cms_shopi($row['g_id'],$globvars['go'],$row['g_file'],$row['g_order']);
      } }
      ?>
      </td>
    </tr>
    <tr>
      <td style="padding:10px 5px;" class="button">
        Upload (1000x1000) &nbsp;<input type="file" id="id_upshopi" onchange="upshopi('<?= $globvars['go'] ?>');"> &nbsp; <span class="red" id="upshopa"></span>
      </td>
    </tr>
  </table><br><br><br>

  <style>
  .selimages {
    position:absolute;
    bottom:40px;
    left:-20px;
    width:120px;
    border:1px solid #C0C0C0;
    background-color:#ffffff;
    -moz-border-radius: 4px; 
    -webkit-border-radius: 4px; 
    border-radius: 4px 4px 4px 4px;
    z-index:10;
    height:220px;
    overflow:auto;
  }
  </style>
  <h3>OPTIONS</h3>
  <table class="tabler" width="<?= isvar($formwidth) ? $formwidth : '100%'; ?>" cellspacing="0" cellpadding="4" border="0">
    <tr class="thb">
      <td style="width:2%; height:20px;">Ord</td>
      <td style="width:9%;"><?= $option1 ?></td>
      <td style="width:9%;"><?= $option2 ?></td>
      <td style="width:7%;">SKU</td>
      <td style="width:7%;">MPN</td>
      <td style="width:7%;">GTIN</td>
      <td style="width:5%;">Price<br><span style="font-size:10px;font-weight:normal">(main if 0)</span></td>
      <td style="width:5%;">Disc £</td>
      <td style="width:5%;">Disc %</td>
      <td style="width:5%;">Calc £</td>
      <td style="width:5%;">Stock</td>
      <td style="width:7%;">Expected<br><span style="font-size:10px;font-weight:normal">(dd/mm/yyyy)</span></td>
      <td colspan="2">Images</td>
      <td style="width:7%;">Visible</td>
      <td style="width:2%;">Del</td>
    </tr>
    <? 
    if(isset($globvars['shopopts'][$page['i_id']])) { foreach($globvars['shopopts'][$page['i_id']] as $o_id => $row) { 
      ?>
      <tr class="o_options" data-id="<?= $o_id ?>">
        <td>
          <input name="o_order[<?= $o_id ?>]" type="text" size="3" maxlength="3" value="<?= $row['o_order'] ? $row['o_order'] : 0 ?>" onkeyup="$('#o_upd_<?= $o_id ?>').val('y')" style="width:calc(100% - 6px)">
        </td>
        <td>
          <input name="o_option1[<?= $o_id ?>]" type="text" size="50" maxlength="50" value="<?= $row['o_option1'] ?>" onkeyup="$('#o_upd_<?= $o_id ?>').val('y')" style="width:calc(100% - 6px);<? if($page['i_option1'] && ! $row['o_option1']) { print 'background-color:#FFC0C0'; } ?>">
        </td>
        <td>
          <input name="o_option2[<?= $o_id ?>]" type="text" size="50" maxlength="50" value="<?= $row['o_option2'] ?>" onkeyup="$('#o_upd_<?= $o_id ?>').val('y')" style="width:calc(100% - 6px);<? if($page['i_option2'] && ! $row['o_option2']) { print 'background-color:#FFC0C0'; } ?>">
        </td>
        <td>
          <input name="o_sku[<?= $o_id ?>]" type="text" size="40" maxlength="40" value="<?= $row['o_sku'] ?>" onkeyup="$('#o_upd_<?= $o_id ?>').val('y')" style="width:calc(100% - 6px)">
        </td>
        <td>
          <input name="o_mpn[<?= $o_id ?>]" type="text" size="40" maxlength="40" value="<?= $row['o_mpn'] ?>" onkeyup="$('#o_upd_<?= $o_id ?>').val('y')" style="width:calc(100% - 6px)">
        </td>
        <td>
          <input name="o_gtin[<?= $o_id ?>]" type="text" size="20" maxlength="20" value="<?= $row['o_gtin'] ?>" onkeyup="$('#o_upd_<?= $o_id ?>').val('y')" style="width:calc(100% - 6px)">
        </td>
        <td>
          <input class="o_price" name="o_price[<?= $o_id ?>]" type="text" size="12" maxlength="12" value="<?= $row['o_price'] ?>" onkeyup="$('#o_upd_<?= $o_id ?>').val('y')" style="width:calc(100% - 6px);">
        </td>
        <td>
          <input class="o_discprice" name="o_discprice[<?= $o_id ?>]" type="text" size="12" maxlength="12" value="<?= $row['o_discprice'] ?>" onkeyup="$('#o_upd_<?= $o_id ?>').val('y')" style="width:calc(100% - 6px);">
        </td>
        <td>
          <input class="o_discpcnt" name="o_discpcnt[<?= $o_id ?>]" type="text" size="12" maxlength="12" value="<?= $row['o_discpcnt'] ?>" onkeyup="$('#o_upd_<?= $o_id ?>').val('y')" style="width:calc(100% - 6px);">
        </td>
        <td class="o_pricecalc" style="font-weight:bold;<? if($row['o_pricecalc'] <= 0) { print 'background-color:#FFC0C0'; } ?>">
          <? print $row['o_pricecalc']; ?>
        </td>
        <td>
          <input name="o_stock[<?= $o_id ?>]" type="text" size="5" maxlength="5" value="<?= $row['o_stock'] ?>" onkeyup="$('#o_upd_<?= $o_id ?>').val('y')" style="width:calc(100% - 6px);<? if($row['o_stock'] <= 0) { print 'background-color:#FFC0C0'; } ?>">
        </td>
        <td>
          <input name="o_expected[<?= $o_id ?>]" type="text" size="10" maxlength="10" value="<?= cdate($row['o_expected'],'d/m/Y','') ?>" onkeyup="$('#o_upd_<?= $o_id ?>').val('y')" style="width:calc(100% - 6px);">
        </td>
        <td id="imgschosen_<?= $o_id ?>">
          <?
          if($row['o_images']) {
            $imgs = explode( ",", $row['o_images']);
            foreach($imgs as $img) {
              if(isset($globvars['shopimgs'][$page['i_id']][$img]) && $globvars['shopimgs'][$page['i_id']][$img]['g_file']) {
                $small = "../images/shop/{$globvars['go']}/small/{$globvars['shopimgs'][$page['i_id']][$img]['g_file']}";
                ?>
                <img data-id="<?= $globvars['shopimgs'][$page['i_id']][$img]['g_id'] ?>" src="<?= $small ?>" width="30">
                <?
              }
            }
          }
          ?>
        </td>
        <td class="button" style="position:relative;width:20px;">
          <input name="o_images[<?= $o_id ?>]" id="o_images_<?= $o_id ?>" type="hidden" value="<?= $row['o_images'] ?>">
          <a style="font-size:9px;" href="#" onclick="selimgopts('<?= $o_id ?>');return false">SELECT</a>
          <div style="display:none" class="selimages" id="selimages_<?= $o_id ?>"></div>
        </td>
        <td style="<? if($row['o_visible'] == 'no') { print 'background-color:#FFC0C0'; } ?>">
          <select name="o_visible[<?= $o_id ?>]" onchange="$('#o_upd_<?= $o_id ?>').val('y')">
            <option value="<? optchk('yes',$row['o_visible']) ?>">yes</option>
            <option value="<? optchk('enquire',$row['o_visible']) ?>">enquire</option>
            <option value="<? optchk('no',$row['o_visible']) ?>">no</option>
          </select>
        </td>
        <td>
          <label class="checklabel"><input type="checkbox" name="o_del[<?= $o_id ?>]" value="y"><span class="checkcust"></span></label>
          <input name="o_id[<?= $o_id ?>]" type="hidden" value="<?= $o_id ?>">
          <input name="o_upd[<?= $o_id ?>]" id="o_upd_<?= $o_id ?>" class="o_upd" type="hidden" value="">
        </td>
      </tr>
    <? } } ?>
    <tr class="o_options" data-id="0">
      <td>
        <input name="o_order[0]" type="text" size="3" maxlength="3" style="width:calc(100% - 6px)">
      </td>
      <td>
        <input name="o_option1[0]" type="text" size="50" maxlength="50" style="width:calc(100% - 6px)">
      </td>
      <td>
        <input name="o_option2[0]" type="text" size="50" maxlength="50" style="width:calc(100% - 6px)">
      </td>
      <td>
        <input name="o_sku[0]" type="text" size="40" maxlength="40" style="width:calc(100% - 6px)">
      </td>
      <td>
        <input name="o_mpn[0]" type="text" size="40" maxlength="40" style="width:calc(100% - 6px)">
      </td>
      <td>
        <input name="o_gtin[0]" type="text" size="20" maxlength="20" style="width:calc(100% - 6px)">
      </td>
      <td>
        <input class="o_price" name="o_price[0]" type="text" size="12" maxlength="12" style="width:calc(100% - 6px)">
      </td>
      <td>
        <input class="o_discprice" name="o_discprice[0]" type="text" size="12" maxlength="12" style="width:calc(100% - 6px)">
      </td>
      <td>
        <input class="o_discpcnt" name="o_discpcnt[0]" type="text" size="12" maxlength="12" style="width:calc(100% - 6px)">
      </td>
      <td class="o_pricecalc">
        <?= $i_row['i_pricecalc'] ?>
      </td>
      <td>
        <input name="o_stock[0]" type="text" size="5" maxlength="5" style="width:calc(100% - 6px)">
      </td>
      <td>
        <input name="o_expected[0]" type="text" size="10" maxlength="10" style="width:calc(100% - 6px)">
      </td>
      <td id="imgschosen_0">
      </td>
      <td class="button" style="position:relative;width:20px;">
        <input name="o_images[0]" id="o_images_0" type="hidden" value="">
        <a style="font-size:9px;" href="#" onclick="selimgopts(0);return false">SELECT</a>
        <div style="display:none" class="selimages" id="selimages_0"></div>
      </td>
      <td>
        <select name="o_visible[0]">
          <option value="yes">yes</option>
          <option value="enquire">enquire</option>
          <option value="no">no</option>
        </select>
      </td>
      <td>
        New<input name="o_id[0]" type="hidden" value="0">
      </td>
    </tr>
  </table><br><br>
  <?
}

function cms_shopi($g_id,$i_id,$file,$order) {
  if(! $order) { $order = ''; }
  $full  = "../images/shop/{$i_id}/full/{$file}";
  $large = "../images/shop/{$i_id}/large/{$file}";
  $small = "../images/shop/{$i_id}/small/{$file}";
  $out = <<<END
  <input type="hidden" name="g_id[{$g_id}]" value="{$g_id}">
  <div id="sd_{$g_id}" style="display:inline-block;padding:4px;vertical-align:top">
    <a style="color:#000000;" onmousemove="ShowContent('si_{$g_id}',-200,450); return false;" onmouseover="ShowContent('si_{$g_id}',-200,450); return false;" onmouseout="HideContent('si_{$g_id}'); return false;" href="#" onclick="return false">
      <img id="imgthumb_{$g_id}" class="imgthumbs" data-id="{$g_id}" src="{$small}" width="136" style="border:1px solid #C0C0C0;">
    </a>
    <div style="margin:5px 0;">
      <span style="display:inline-block;padding-bottom:4px;padding-right:2px;vertical-align:bottom;">ORD</span><input type="text" size="2" name="g_order[{$g_id}]" value="{$order}" style="text-align:center;padding:1px 0"> &nbsp; 
      <span style="display:inline-block;padding-bottom:4px;padding-right:2px;vertical-align:bottom;">DEL</span><label class="checklabel"><input type="checkbox" name="g_del[{$g_id}]" value="y"><span id="imgthumb_{$g_id}" class="checkcust" style="top:8px"></span></label>
    </div>
    <div id="si_{$g_id}" style="display: none; position: absolute; border: 1px solid #C0C0C0; background-color: white; padding: 5px; z-index: 999;">
      <img src="{$large}" width="400" border="0"><div style="margin:5px auto;text-align:center">{$file}</div>
    </div>
  </div>
  END;
  return $out ; 
}

function cms_image($field,$path,$value='',$width='',$height='') {
  global $globvars; extract($globvars) ;
  $files = [];
  if($path && ($handle = opendir($path))) {
    while(false !== ($file = readdir($handle))) {
      if(substr_count($file,'.jpg')||substr_count($file,'.jpeg')||substr_count($file,'.png')||substr_count($file,'.webp')) { $files[] = $file; }
    }
    closedir($handle);
    sort($files);
  }
  // print_arv($files);
  // print_arv($images);
  $images = explode(",", $value);
  ?>
  <input type="hidden" name="upfields[]" value="<?= $field ?>">
  <div style="max-height:120px;overflow-y:auto;overflow-x:hidden">
    <table class="tablen" width="100%" cellspacing="0" cellpadding="4" border="0">
      <tr>
        <td>Already selected (remove order to unselect)</td>
        <td>Not selected (add order to select)</td>
        <td>Upload File</td>
      </tr>
      <tr>
        <td valign="top" width="270">
          <? 
          $num = 0 ;
          $order = 0 ;
          foreach($images as $file) {
            if(in_array($file, $files)) {
              print cms_imagei($field,$path,$file,++$num,++$order);
            }
          }
          ?>
        </td>
        <td valign="top" width="270">
          <div id="<?= 'new_' . $field ?>"></div>
          <?
          foreach($files as $file) {
            if(! in_array($file, $images)) {
              print cms_imagei($field,$path,$file,++$num);
            }
          }
          ?>
        </td>
        <td valign="top">
          <? $ong = "upimage('{$field}','{$path}','{$width}','{$height}');"; ?>
          <input type="file" id="<?= 'up_id_' . $field ?>" onchange="<?= $ong ?>">
          <div style="margin-top:5px;" class="red" id="<?= 'res_' . $field ?>"></div>
        </td>
      </tr>
    </table>
  </div>
  <?
}

function cms_imagei($field,$path,$file,$num,$order='') {
  // Delete <label class="checklabel"><input type="checkbox" name="del_{$field}[{$num}]" value="y"><span class="checkcust" style="top:8px"></span></label>
  $out = <<<END
  <input type="hidden" name="img_{$field}[{$num}]" value="{$file}">
  <input type="hidden" name="pth_{$field}[{$num}]" value="{$path}">
  <div style="display:block;position:relative;padding:5px 0;">
    <a style="display:inline-block;color:#000000;vertical-align:top;margin-right:5px;" onmousemove="ShowContent('hi_{$field}_{$num}',120,70); return false;" onmouseover="ShowContent('hi_{$field}_{$num}',120,70); return false;" onmouseout="HideContent('hi_{$field}_{$num}'); return false;" href="#" onclick="return false">
      <img style="border:ipx solid #C0C0C0;width:100px;" src="{$path}{$file}">
    </a>
    <div style="display:inline-block;color:#000000;vertical-align:top;">
      Order <input type="text" size="2" name="ord_{$field}[{$num}]" value="{$order}" style="text-align:center;padding:1px 0;margin-right:5px;">
    </div>
  </div>
  <div id="hi_{$field}_{$num}" style="display: none; position: absolute; border: 1px solid #C0C0C0; background-color: white; padding: 5px; z-index: 999;">
    <img src="{$path}{$file}" width="400" border="0"><div style="margin:5px auto;text-align:center; font-weight:normal">{$file}</div>
  </div>
  END;
  return $out ; 
}

function cms_imgmain() {
  global $globvars; extract($globvars);
  if(in_array( $action, ['edit','add'] )) {
    $field = $fname;
    $fpath = build_path($filepath,$sq_fpath[$c]);
    $value = $i_row[$field];
    $width = 2000;
    $height = 680;
    // cms_image($field,$fpath,$value,$width,$height);
    cms_image($field,$fpath,$value); // no resize
  }
  else {
    return true;
  }
}

function cms_imgmob() {
  global $globvars; extract($globvars);
  if(in_array( $action, ['edit','add'] )) {
    $field = $fname;
    $fpath = build_path($filepath,$sq_fpath[$c]);
    $value = $i_row[$field];
    $width = 540;
    $height = 200;
    // cms_image($field,$fpath,$value,$width,$height);
    cms_image($field,$fpath,$value); // no resize
  }
  else {
    return true;
  }
}

function cms_upimage() {
  global $globvars;
  $globvars['debug'] = 0 ;
  $res = false;
  $arr = [];
  $msg = '';
  $data = '';
  globvars('field','path','width','height','file');
  if($globvars['file']) {
    if($globvars['file']['size'] == 0) {
      $arr[] = 'File failed to upload';
    }
    elseif($globvars['file']['error'] || $globvars['file']['size'] > 5000000) {
      $arr[] = 'File exceeds 5Mb';
    }
    elseif($globvars['file']['tmp_name']) { 
      $uc = upload_check('file');
      if($uc['res']) {
        $path = '../' . $globvars['path'] . '/';
        if(! file_exists($path . $uc['name'])) {
          if($uc['ext'] == 'png' || $uc['ext'] == 'jpg' || $uc['ext'] == 'jpeg' || $uc['ext'] == 'webp') {
            $res = true ;
            $um = upload_move('file','image',$path,$uc['name']);
            $mi = make_image($path,$um['fname'],$path,'',$globvars['width'],$globvars['height'],'m',85,0,true,true);
            foreach($globvars['make_errors'] as $a) {
              $arr[] = $a ;
            }
            $data = cms_imagei($globvars['field'],$path,$um['fname'],time());
          }
          else {
            $arr[] = 'File type invalid';
          }
        }
        else {
          $arr[] = 'File already exists';
        }
      }
      else {
        $arr[] = $uc['ufail'];
      }
    }
    else {
      $arr[] = 'File upload error';
    }
  }
  $msg = implode(", ", $arr);
  return array('res'=>$res,'arr'=>$arr,'msg'=>$msg,'data'=>$data);
}

function cms_upshopi() {
  global $globvars;
  $globvars['debug'] = 0 ;
  globvars('i_id');
  $res = false;
  $arr = [];
  $msg = '';
  $data = '';
  $mwl = 600;
  $mhl = 600;
  $mws = 150;
  $mhs = 150;
  if($globvars['i_id']) {
    $field = 'id_upshopi';
    globvars($field);
    if($globvars[$field]['size'] == 0) {
      $arr[] = 'File failed to upload';
    }
    elseif($globvars[$field]['error'] || $globvars[$field]['size'] > 5000000) {
      $arr[] = 'File exceeds 5Mb';
    }
    elseif($globvars[$field]['tmp_name']) { 
      $uc = upload_check($field);
      if($uc['res']) {
        $fpath = '../images/shop/' . $globvars['i_id'];
        if(! file_exists($fpath . '/full/' . $uc['name'])) {
          if($uc['ext'] == 'png' || $uc['ext'] == 'jpg' || $uc['ext'] == 'jpeg' || $uc['ext'] == 'webp') {
            $res = true ;
            $full  = $fpath . '/full';
            $large = $fpath . '/large';
            $small = $fpath . '/small';
            $um = upload_move($field,'image',$full,$uc['name']);
            $ml = make_image($full,$um['fname'],$large,'',$mwl,$mhl,'m',85,0,true,true);
            $ms = make_image($full,$um['fname'],$small,'',$mws,$mhs,'m',85,0,true,true);
            foreach($globvars['make_errors'] as $a) {
              $arr[] = $a ;
            }

            $string = "insert into `shop_images` set `i_id` = '{$globvars['i_id']}', `g_file` = '{$um['fname']}'";
            $query = my_query($string);
            $id = my_id();
            $data = cms_shopi($id,$globvars['i_id'],$um['fname'],0);

            logtable('INSERT',$globvars['cntrl_user'],'shop_images',$string);
          }
          else {
            $arr[] = 'File type invalid';
          }
        }
        else {
          $arr[] = 'File already exists';
        }
      }
      else {
        $arr[] = $uc['ufail'];
      }
    }
    else {
      $arr[] = 'File upload error';
    }
  }
  else {
    $arr[] = 'No Product ID';
  }
  $msg = implode(", ", $arr);
  return array('res'=>$res,'arr'=>$arr,'msg'=>$msg,'data'=>$data);
}

/*  TIANDI - Nice idea but do it in separate CMS instead */

function cms_subp() {
  global $globvars, $globvarr; extract($globvars) ;
  if($i_row['p_id'] == 3) {
    $m_id = $i_row['q_id'];

    if($globvars['done']) {
      globvars(
        'm_colour_back',
        'm_colour_over',
        'm_colour_text',
        'm_slide_main',
        'm_slide_mobile',
        'm_slide_title1',
        'm_slide_title2',
        'm_slide_symbol'
      );

      $fname = 'up_m_slide_main';
      $upload = upload_move($fname,'image',$fpath . 'model/main','','',2000,2000,85);
      // print_arr($upload);
      $slide_main = ($upload['res'] == 'ok') ? $upload['fname'] : $globvars['m_slide_main'];

      $fname = 'up_m_slide_mobile';
      $upload = upload_move($fname,'image',$fpath . 'model/mob','','',2000,2000,85);
      // print_arr($upload);
      $slide_mobile = ($upload['res'] == 'ok') ? $upload['fname'] : $globvars['m_slide_mobile'];

      $fname = 'up_m_slide_symbol';
      $upload = upload_move($fname,'image',$fpath . 'model/symb','','',2000,2000,85);
      // print_arr($upload);
      $slide_symbol = ($upload['res'] == 'ok') ? $upload['fname'] : $globvars['m_slide_symbol'];

      $string = "update `models` set 
        `m_colour_back`  = '{$globvars['m_colour_back']}',
        `m_colour_over`  = '{$globvars['m_colour_over']}',
        `m_colour_text`  = '{$globvars['m_colour_text']}',
        `m_slide_main`   = '$slide_main',
        `m_slide_mobile` = '$slide_mobile',
        `m_slide_title1` = '{$globvars['m_slide_title1']}',
        `m_slide_title2` = '{$globvars['m_slide_title2']}',
        `m_slide_symbol` = '$slide_symbol'
        where `m_id` = '{$m_id}' limit 1
      ";
      // print_p($string);
      my_query($string);
      logtable('UPDATE',$cntrl_user,'models',$string);
    }

    $string = "select * from `models` where `m_id` = '$m_id' limit 1";
    $query = my_query($string);
    if(! my_rows($query)) {
      $string = "insert into `models` set `m_id` = '$m_id'";
      $query = my_query($string);
      logtable('INSERT',$cntrl_user,'models',$string);
      $string = "select * from `models` where `m_id` = '$m_id' limit 1";
      $query = my_query($string);
    }
    $row = my_assoc($query);
    ?>
    <br><h3>MODEL CONTENT</h3>

    <br><h3>COLOURS</h3>
    <table class="tabler" width="<?= isvar($formwidth) ? $formwidth : '100%'; ?>" cellspacing="0" cellpadding="4" border="0">
      <tr>
        <td width="<?= isvar($formleftc) ? $formleftc : ''; ?>" class="thb button">Background</td>
        <td style="height:26px;">
          <input id="m_colour_back" name="m_colour_back" type="text" size="7" maxlength="7" value="<?= $row['m_colour_back'] ?>" onkeyup="changepicker('id_q_menuc');" onchange="fldchg++">
          <a title="Colour Picker" class="color_click" href="#" onclick="return showpicker('m_colour_back');" style="background-color:<?= $row['m_colour_back'] ?>"></a>
        </td>
        <td width="<?= isvar($formrghtc) ? $formrghtc : ''; ?>" class="th button">
        </td>
      </tr>
      <tr>
        <td class="thb button">Rollover</td>
        <td style="height:26px;">
          <input id="m_colour_over" name="m_colour_over" type="text" size="7" maxlength="7" value="<?= $row['m_colour_over'] ?>" onkeyup="changepicker('id_q_menuc');" onchange="fldchg++">
          <a title="Colour Picker" class="color_click" href="#" onclick="return showpicker('m_colour_over');" style="background-color:<?= $row['m_colour_over'] ?>"></a>
        </td>
        <td class="th button">
        </td>
      </tr>
      <tr>
        <td class="thb button">Text</td>
        <td style="height:26px;">
          <input id="m_colour_text" name="m_colour_text" type="text" size="7" maxlength="7" value="<?= $row['m_colour_text'] ?>" onkeyup="changepicker('id_q_menuc');" onchange="fldchg++">
          <a title="Colour Picker" class="color_click" href="#" onclick="return showpicker('m_colour_text');" style="background-color:<?= $row['m_colour_text'] ?>"></a>
        </td>
        <td class="th button">
        </td>
      </tr>
    </table>

    <br><h3>HOME SLIDES</h3>
    <table class="tabler" width="<?= isvar($formwidth) ? $formwidth : '100%'; ?>" cellspacing="0" cellpadding="4" border="0">
      <tr>
        <td width="<?= isvar($formleftc) ? $formleftc : ''; ?>" class="thb button">
        Main Image
        <? 
        $fid = 'm_slide_main';
        $fname = 'm_slide_main';
        $dval = $row['m_slide_main'];
        $och = 'fldchg++;';
        $tpath = $fpath . 'model/main/';
        $files = [];
        if(is_dir($tpath) && $handle = opendir($tpath)) {
          while(false !== ($file = readdir($handle))) {
            if($file != "." && $file != "..") {
              $files[] = $file;
            }
          }
          closedir($handle);
          sort($files);
        }
        image_pop($tpath . $dval,$fid) ; 
        ?>
        </td>
        <td style="height:26px;">
          <? select_image($files,$tpath,$fname,$fid,$dval,$och); ?>
        </td>
        <td width="<?= isvar($formrghtc) ? $formrghtc : ''; ?>" class="th button">?x?</td>
      </tr>
      <tr>
        <td class="thb button">
        Mobile Image
        <? 
        $fid = 'm_slide_mobile';
        $fname = 'm_slide_mobile';
        $dval = $row['m_slide_mobile'];
        $och = 'fldchg++;';
        $tpath = $fpath . 'model/mob/';
        $files = [];
        if(is_dir($tpath) && $handle = opendir($tpath)) {
          while(false !== ($file = readdir($handle))) {
            if($file != "." && $file != "..") {
              $files[] = $file;
            }
          }
          closedir($handle);
          sort($files);
        }
        image_pop($tpath . $dval,$fid) ; 
        ?>
        </td>
        <td style="height:26px;">
          <? select_image($files,$tpath,$fname,$fid,$dval,$och); ?>
        </td>
        <td class="th button">?x?</td>
      </tr>
      <tr>
        <td class="thb button">Title 1</td>
        <td>
          <textarea name="m_slide_title1" rows="3" cols="55" onchange="fldchg++;"><?= $row['m_slide_title1'] ?></textarea>
        </td>
        <td class="th button">
          Part of [text] in square brackets<br>changed to text colour as above
        </td>
      </tr>
      <tr>
        <td class="thb button">Title 2</td>
        <td>
          <input name="m_slide_title2" type="text" size="70" maxlength="200" value="<?= $row['m_slide_title2'] ?>" onchange="fldchg++">
        </td>
        <td class="th button">
        </td>
      </tr>
      <tr>
        <td width="<?= isvar($formleftc) ? $formleftc : ''; ?>" class="thb button">
        Symbol
        <? 
        $fid = 'm_slide_symbol';
        $fname = 'm_slide_symbol';
        $dval = $row['m_slide_symbol'];
        $och = 'fldchg++;';
        $tpath = $fpath . 'model/symb';
        $files = [];
        if(is_dir($tpath) && $handle = opendir($tpath)) {
          while(false !== ($file = readdir($handle))) {
            if($file != "." && $file != "..") {
              $files[] = $file;
            }
          }
          closedir($handle);
          sort($files);
        }
        image_pop($tpath . $dval,$fid) ; 
        ?>
        </td>
        <td style="height:26px;">
          <? select_image($files,$tpath,$fname,$fid,$dval,$och); ?>
        </td>
        <td width="<?= isvar($formrghtc) ? $formrghtc : ''; ?>" class="th button">?x?</td>
      </tr>
    </table>
    <?
  }
}

function cms_subs() {
  global $globvars, $globvarr; extract($globvars) ;
  if($globvars['page']['p_id'] == 3) {
    if($globvars['done']) {
    }
    ?>
    <br><h3>SPECIAL CONTENT</h3>
    <br><h3>PRODUCT DETAILS</h3>
    <?
  }
}

/*  SEARCH */

function search_blog($data) {
  global $globvars;
  if(! isset($globvars['blog_data'])) {
    $globvars['blog_data'] = array();
    foreach($globvars['blog_stack'] as $table) {
      $string = "SELECT * FROM `blog_{$table}`";
      // print_p($string);
      $query = my_query($string);
      while($row = my_assoc($query)) {
        $globvars['blog_data'][$row['m_id']][$row['id']][$table] = $row;
      }
    }
    // print_arv($globvars['blog_data']);
  }
  $m_id = isset($data['m_id']) && $data['m_id'] ? $data['m_id'] : '' ;
  $out = $arr = [];
  $fields = ['html'=>1,'head'=>10];
  $return = ['head'=>'bread', 'clip'=>'html'];
  if($m_id && isset($globvars['blog_data'][$m_id])) {
    foreach($globvars['blog_data'][$m_id] as $arr) {
      if(isset($arr['head'])) {
        $out[] = ['field'=>'blog_head','blog_head'=>$arr['head']['text'],'rate'=>5];
      }
      if(isset($arr['html'])) {
        $out[] = ['field'=>'blog_text','blog_text'=>$arr['html']['html'],'rate'=>1];
      }
    }
  }
  return $out;
}

function search_columns($data) {
  global $globvars;
  $out = [];
  $sdb = 'cont_columns';
  $arr = search_extra($data,$sdb);
  $head = '';
  $html = '';
  foreach($arr as $ent) {
    if(isset($ent['head'])) {
      $head .= $ent['head'] . ' ' ; 
      $html .= $ent['html'] . ' ' ; 
    }
  }
  $out[] = ['field'=>$sdb.'_head',$sdb.'_head'=>$head,'rate'=>10];
  $out[] = ['field'=>$sdb.'_text',$sdb.'_text'=>$html,'rate'=>1];
  // print_arr($out);
  return $out ;
}

function search_extra($data,$sdb) {
  global $globvars;
  $arr = [];
  if(! isset($globvars[$sdb])) {
    $string = "select * from `$sdb`";
    $query = my_query($string);
    while($row = my_assoc($query)) {
      if($row['p_id'] && $row['q_id'] && $row['r_id']) {
        $globvars[$sdb][$row['p_id']]['subs'][$row['q_id']]['subs'][$row['r_id']][$row['id']] = $row ;
      }
      elseif($row['p_id'] && $row['q_id']) {
        $globvars[$sdb][$row['p_id']]['subs'][$row['q_id']][$row['id']] = $row ;
      }
      elseif($row['p_id']) {
        $globvars[$sdb][$row['p_id']][$row['id']] = $row ;
      }
    }
  }
  $p_id = isset($data['p_id']) && $data['p_id'] ? $data['p_id'] : 0 ;
  $q_id = isset($data['q_id']) && $data['q_id'] ? $data['q_id'] : 0 ;
  $r_id = isset($data['r_id']) && $data['r_id'] ? $data['r_id'] : 0 ;
  if($p_id && $q_id && $r_id && isset($globvars[$sdb][$p_id]['subs'][$q_id]['subs'][$r_id])) {
    $arr = $globvars[$sdb][$p_id]['subs'][$q_id]['subs'][$r_id];
  }
  elseif($p_id && $q_id && isset($globvars[$sdb][$p_id]['subs'][$q_id])) {
    $arr = $globvars[$sdb][$p_id]['subs'][$q_id];
  }
  elseif($p_id && isset($globvars[$sdb][$p_id])) {
    $arr = $globvars[$sdb][$p_id];
  }
  return $arr;
}

/*  SCHEMA */

function head_yoast() {
  global $globvars;
  $bread = [];
  if(isset($globvars['page']['bread']) && $n = count($globvars['page']['bread'])) {
    $bread = $globvars['page']['bread'] ;
    $u = array_key_last($bread);
    if($u && $u != $globvars['page']['url'] && isset($globvars['page']['head'])) {
      $bread[$globvars['page']['url']] = clean_meta($globvars['page']['head']);
    }
    // print_arv($bread);
  }
  ?>
  <script type="application/ld+json">
  {
    "@context":"https://schema.org",
    "@graph":[{
      "@type":"Organization",
      "@id":"<?= $globvars['sm_url'] ?>#organization",
      "name":"<?= $globvars['comp_name'] ?>",
      "url":"<?= $globvars['sm_url'] ?>",
 <? /* ?>
      "sameAs":[
      ],
 <? */ ?>
      "logo":{
        "@type":"ImageObject",
        "@id":"<?= $globvars['sm_url'] ?>#logo",
        "inLanguage":"<?= $globvars['htmlang'] ?>",
        "url":"<?= $globvars['comp_logo'] ?>",
        "width":<?= $globvars['comp_logo_w'] ?>,
        "height":<?= $globvars['comp_logo_h'] ?>,
        "caption":"<?= $globvars['comp_name'] ?>"
      },
      "image":{"@id":"<?= $globvars['sm_url'] ?>#logo"}
    },
    {
      "@type":"WebSite",
      "@id":"<?= $globvars['sm_url'] ?>#website",
      "url":"<?= $globvars['sm_url'] ?>",
      "name":"<?= $globvars['comp_name'] ?>",
      "description":"",
      "publisher":{"@id":"<?= $globvars['sm_url'] ?>#organization"},
 <? /* ?>
      "potentialAction":[
        {
          "@type":"SearchAction",
          "target":"<?= $globvars['sm_url'] ?>search?q={search_term_string}",
          "query-input":"required name=search_term_string"
        }
      ],
 <? */ ?>
      "inLanguage":"<?= $globvars['htmlang'] ?>"
    },
    {
      "@type":"WebPage",
      "@id":"<?= $globvars['sm_url'] . $globvars['page']['url']; ?>#webpage",
      "url":"<?= $globvars['sm_url'] . $globvars['page']['url']; ?>",
      "name":"<?= clean_meta($globvars['page']['meta_title']) ?>",
      "isPartOf":{"@id":"<?= $globvars['sm_url'] ?>#website"},
      "description":"<?= clean_meta($globvars['page']['meta_desc']) ?>",
      "breadcrumb":{"@id":"<?= $globvars['sm_url'] . $globvars['page']['url']; ?>#breadcrumb"},
      "inLanguage":"<?= $globvars['htmlang'] ?>",
      "potentialAction":[{"@type":"ReadAction","target":["<?= $globvars['sm_url'] . $globvars['page']['url']; ?>"]}]
    },
    {	
      "@type":"BreadcrumbList",
      "@id":"<?= $globvars['sm_url'] . $globvars['page']['url']; ?>#breadcrumb",
      "itemListElement":[
        {
          "@type":"ListItem",
          "position":1,
          "item":{
            "@type":"WebPage",
            "@id":"<?= $globvars['sm_url'] ?>",
            "url":"<?= $globvars['sm_url'] ?>",
            "name":"Home"
          }
        }<? if(count($bread)) { $c = 2 ; foreach($bread as $u => $t) { ?>,
        {
          "@type":"ListItem",
          "position":<?= $c ?>,
          "item":{
            "@type":"WebPage",
            "@id":"<?= $globvars['sm_url'] . $u ?>",
            "url":"<?= $globvars['sm_url'] . $u ?>",
            "name":"<?= $t ?>"
          }
        }<? $c++; } } print "\r\n"; ?>
      ]
    }
  ]}
  </script>
 <?
}

/*  DISPLAY */

function dispc($in) {
  global $globvars ;
  foreach($globvars['parameters'] as $a) {
    if($a['pattern'] && $in && substr_count($in,$a['match'])) {
      $patterns = explode("\r\n", $a['pattern']);
      foreach($patterns as $pattern) {
        if($a['html']) {
          if(substr_count($a['match'],':')) {
            $in = preg_replace_callback($pattern, function($matches) use ($a) { 
              if(isset($matches[1])) {
                return str_replace('[[param]]',$matches[1],$a['html']) ;
              } }
            , $in );
          }
          else {
            $in = preg_replace($pattern, $a['html'], $in );
          }
        }
        elseif($a['function'] && function_exists($a['function'])) {
          $in = preg_replace_callback($pattern, function($matches) { return $a['function']($matches[1]); }, $in );
        }
      }
    }
  }
  return $in ;
}

/* MEDIA */

function gall_get($gc) {
  $out = [];
  global $globvars ;
  $string = "select * from `gall_images` 
    left join `gall_cats` on `gall_cats`.`gc_id` = `gall_images`.`gc_id`
    where `gall_images`.`gc_id` = '$gc' and `gall_images`.`gi_img_large` != '' 
    order by `gall_images`.`gi_order`
  ";
  $query = my_query($string);
  while($row = my_assoc($query)) {
    $arr['small'] = clean_url($row['gi_img_small'] ? "images/gallery/{$row['gc_id']}/small/" . $row['gi_img_small'] : $arr['large']);
    $arr['large'] = clean_url("images/gallery/{$row['gc_id']}/large/" . $row['gi_img_large']);
    $arr['caption'] = $row['gi_caption'];
    $arr['url'] = $row['gi_url'];
    $out['images'][$row['gi_id']] = $arr;
    $out['sizes'] = ['large_w'=>$row['gc_large_w'],'large_h'=>$row['gc_large_h'],'small_w'=>$row['gc_small_w'],'small_h'=>$row['gc_small_h']];
  }
  // print_arv($out);
  return $out;
}

function gall_slides($in) {
  global $globvars ;
  $globvars['slide_set'] = isset($globvars['slide_set']) ? $globvars['slide_set']++ : 0 ;
  if(is_array($in) && isset($in['images']) && isset($in['sizes'])) {
    // print_arv($in,'SLIDES');
    $c = 0 ;
    $large_p = $in['sizes']['large_h'] / $in['sizes']['large_w'];
    $small_p = $in['sizes']['small_h'] / $in['sizes']['small_w'];
    $max_h = floor(1160 * $large_p) . 'px';
    $large_h = "calc(" . (100 * $large_p) . "vw - 20px)";
    $small_h = "calc(" . (100 * $small_p) . "vw - 20px)";
    ?>
    <style>
      #slide_set<?= $globvars['slide_set'] ?> { width:100%; height:<?= $max_h ?>; }
      @media only screen and (max-width: 1200px) { #slide_set<?= $globvars['slide_set'] ?> { height:<?= $large_h ?>; } }
      @media only screen and (max-width: <?= $in['sizes']['small_w'] ?>px) { #slide_set<?= $globvars['slide_set'] ?> { height:<?= $small_h ?>; } }
    </style>
    <div class="slide_set" id="slide_set<?= $globvars['slide_set'] ?>">
      <? 
      foreach($in['images'] as $slide) {
        $srs = $src = '';
        if($slide['small']) {
          if( $srs ) { $srs .= ', ' ; }
          $srs = $slide['small'] . ' 360w, ' . $slide['large'] . ' 1200w' ;
        }
        else {
          $src = $slide['large'];
        }
        ?>
        <style type="text/css">
          #slide_img<?= $c ?> { background-image:url(<?= $slide['large'] ?>); }
          @media only screen and (max-width: 500px) { #slide_img<?= $c ?> { background-image:url(<?= $slide['small'] ?>); } }
        </style>
        <div id="slide_img<?= $c ?>" class="slide_img" style="display:none" arial-label="<?= $slide['caption'] ?>">
        <? if($slide['url']) { ?>
        <a title="<?= $slide['caption'] ?>" style="position:absolute;width:100%;height:100%;top:0;left:0;" href="<?= $slide['url']; ?>"></a>
        <? } ?>
        </div>
        <?
        $c++;
      }
      ?>
    </div>
    <?
  }
}

function gall_fancybox($in) {
  global $globvars ;
  $globvars['fancy_grid'] = isset($globvars['fancy_grid']) ? $globvars['fancy_grid']++ : 0 ;
   if(is_array($in) && isset($in['images']) && isset($in['sizes'])) {
    // print_arv($in);
    ?>
    <div class="fancy_outer" id="fancy_grid<?= $globvars['fancy_grid'] ?>">
    <div class="fancy_inner"><?
      foreach($in['images'] as $image) {
        ?><div class="fancy_img"><a data-fancybox="fancy_grid<?= $globvars['fancy_grid'] ?>" data-caption="<?= $image['caption'] ?>" href="<?= $image['large'] ?>"><img src="<?= $image['small'] ?>" alt="<?= $image['caption'] ?>"></a></div><?
      }
      ?></div></div>
    <?
  }
}

/*  SITEMAP */

function disp_sitemap() {
  global $globvars; 
  ?>
  <div id="sitemap">
    <?
    if(substr_count($globvars['php_path'], '/control')) {
      // print_arv($globvars['pages_main']);
    }
    foreach($globvars['pages_main'] as $u0 => $a0) {
      $globvars['sitevisline'] = true;
      disp_siteline($a0,0);
      if(isset($a0['subs'])) { foreach($a0['subs'] as $u1 => $a1) {
        disp_siteline($a1,1);
        if(isset($a1['subs'])) { foreach($a1['subs'] as $u2 => $a2) {
          disp_siteline($a2,2);
          if(isset($a2['subs'])) { foreach($a2['subs'] as $u3 => $a3) {
            disp_siteline($a3,3);
          }}
        }}
      }}
    }
    ?>
  </div>
  <?
}

function disp_siteline($s,$l) {
  global $globvars;
  $name = $s['menu'] ? $s['menu'] : $s['head1'];
  if(! in_array($s['url'],$globvars['sm_excld'])) {
    $s_url = isset($s['redirect']) && $s['redirect'] ? $s['redirect'] : $s['url'];
    if(substr_count($globvars['php_path'], '/control')) {
      ?>
      <div class="siteline<?= $l ?>">
        <a style="color:#0000C0" target="<?= $s['ephp'] ?>" href="<?= $s['edit'] ?>" title="Edit"><img alt="Edit" src="../images/edit.png"></a><a target="public" href="<?= $globvars['base_href'] . $s_url ?>" title="View"><img alt="View" src="../images/view.png"></a>
        <?
        print $name ;
        if(! ($globvars['sitevisline'] && ($s['visible'] == 'yes'))) {
          print ' (hidden)';
          $globvars['sitevisline'] = false;
        }
        ?>
      </div>
      <?
    }
    elseif($s_url != '404') {
      ?>
      <div class="siteline<?= $l ?>"> 
        <a href="<?= $globvars['base_href'] . $s_url ?>"><?= $name ?></a>
      </div>
      <?
    }
  }
}

function sm_funct() {
  global $globvars; 
}

/* CURRENCY */

function curr_get() {
  globvars('curr_select');
  global $globvars; 

  $string = "select * from `currencies` where `currencies`.`visible` = 'yes' order by `currencies`.`order`";
  $query = my_query($string);
  $c = 0 ;
  while($c_row = my_assoc($query)) {
    $globvars['currencies'][$c_row['code']] = $c_row ;
    if(! $c++) {
      $curr_def = $c_row['code'] ;
    }
  }
  
  // start force to GBP for now ?test_ip=159.172.255.255
  if($globvars['local_path'] == 'gladstn') {
    globvars('test_ip');
    $geo = geolocate($globvars['test_ip']);
    // print_arv($geo);
    $_SESSION['test_ip'] = $globvars['test_ip'];
    $_SESSION['curr_set'] = $globvars['curr_set'] = 'GBP';
    $globvars['disp_net'] = $geo['res_ctry'] == 'GB' ? false : true;
    scookie('curr_set',$globvars['curr_set'],0);
    $_SESSION['curr_region'] = $globvars['currencies'][$globvars['curr_set']]['sr_id'];
    $globvars['curr_arr'] = $globvars['currencies'][$globvars['curr_set']];
    return;
  }
  // end force to GBP for now

  $c = '';
  if(isset($_COOKIE['curr_set']) && $_COOKIE['curr_set']) {
    // set from cookie
    $c .= $_COOKIE['curr_set'] ;
  }

  if($globvars['curr_select']) {
    // set from form change
    $globvars['curr_set'] = $globvars['curr_select'] ;
    $c .= '_form' ;
  }
  elseif(isset($_SESSION['curr_set']) && $_SESSION['curr_set']) {
    // set from session
    $globvars['curr_set'] = $_SESSION['curr_set'] ;
    $c .= '_session' ;
  }
  elseif(isset($_COOKIE['curr_set']) && $_COOKIE['curr_set']) {
    // set from cookie
    $globvars['curr_set'] = $_COOKIE['curr_set'] ;
    $c .= '_cookie' ;
  }
  else {
    // set from geolocation
    $c .= '_geo' ;
    $geo = geolocate();
    if($geo['res_cont'] == 'EU' && $geo['res_ctry'] != 'GB') {
      $globvars['curr_set'] = 'EUR';
    }
    elseif($geo['res_cont'] == 'NA') {
      $globvars['curr_set'] = 'USD';
    }
    elseif($geo['res_cont'] == 'OC') {
      $globvars['curr_set'] = 'AUD';
    }
    else {
      $globvars['curr_set'] = 'GBP';
    }
  }

  if(! (isset($globvars['curr_set']) && $globvars['curr_set'] && isset($globvars['currencies'][$globvars['curr_set']]))) {
    // default if not set/exist
    $globvars['curr_set'] = $curr_def ;
  }

  $globvars['disp_net'] = $globvars['curr_set'] == 'GBP' ? false : true;

  // print_arv($c . '=' . $globvars['curr_set']) ;
  // set session & cookie

  $_SESSION['curr_set'] = $globvars['curr_set'];
  scookie('curr_set',$globvars['curr_set'],365);
  $_SESSION['curr_region'] = $globvars['currencies'][$globvars['curr_set']]['sr_id'];
  $globvars['curr_arr'] = $globvars['currencies'][$globvars['curr_set']];
}

function curr_call() {
  global $globvars;
  $string = "SELECT * FROM `currencies` WHERE `visible` = 'Yes' AND `code` != 'GBP' AND `date` != CURDATE() ORDER BY `order`";
  // print_p($string);
  $query = my_query($string);
  if(! my_rows($query)) { return; }
  $curs = [] ;
  while($c_row = my_assoc($query)) {
    if($c_row['code'] != 'GBP') {
      $curs[] = $c_row['code'];
    }
  }
  $url = 'https://apilayer.net/api/live?access_key=' . $globvars['apilayer_key'] . '&format=1&source=GBP&currencies=' . implode(",",$curs);
  // print_p($url);
  $str = file_get_contents($url);
  $arr = json_decode($str, true);
  // print_arr($arr); 
  if(isset($arr['success']) && ($arr['success'] == 1)) {
    foreach($arr['quotes'] as $k => $v) {
      $k = str_replace('GBP','',$k);
      $string1 = "UPDATE `currencies` SET `rate` = '$v', `date` = CURDATE() WHERE `code` = '{$k}' LIMIT 1";
      // print_p($string1);
      my_query($string1);      
    }
  }
}

function curr_conv($price=0,$dec=2) {
  global $globvars;
  // print_arr($globvars['curr_arr']);
  $price = floatval($price);
  return round($price * $globvars['curr_arr']['rate'],$dec);
}

function curr_disp($price=0,$dec=2) {
  global $globvars;
  $price = floatval($price);
  if($price >= 0) {
    return $globvars['curr_arr']['symbol'] . number_format(curr_conv($price),$dec);
  }
  else {
    return '-' . $globvars['curr_arr']['symbol'] . number_format(curr_conv(0 - $price),$dec);
  }
}

function curr_show($price=0,$dec=2,$code='') {
  global $globvars;
  $price = floatval($price);
  if($code && isset($globvars['currencies'][$code])) {
    $symbol = $globvars['currencies'][$code]['symbol'];
  }
  else {
    $symbol = $globvars['curr_arr']['symbol'];
  }
  if($price >= 0) {
    return $symbol . number_format($price,$dec);
  }
  else {
    return '-' . $symbol . number_format(0 - $price,$dec);
  }
}

/* LOGIN */

function user_login() {
  globvars('user_login','verify','action','email','pass');
  global $globvars;
  $res = false;
  $arr = [];
  $msg = '';
  if($globvars['action'] == 'logout') {
    $_COOKIE['user_login'] = $_SESSION['user_login'] = $globvars['user_login'] = '';
    scookie('user_login','');
    $msg = 'Logged out';
    $res = 'location:account';
  }
  elseif($globvars['verify']) {
    if(substr_count($globvars['verify'], '|')) {
      $u_id = substr($globvars['verify'], 0 , strpos($globvars['verify'], '|'));
      $string = "select * from `user_details` where `u_id` = '$u_id' limit 1";
      $query = my_query($string);
      if(my_rows($query)) {
        $row = my_assoc($query);
        $vtime = time();
        if($vtime - $row['u_vtime'] < 3600) {
          $vcode = $row['u_id'] . '|' . md5($row['u_vtime'] . $row['u_email'] . $row['u_pass']); 
          if($vcode == $globvars['verify']) {
            $res = true ;
            $msg = 'ok' ;
          }
        }
      }
    }
    if(! $res) {
      $href = 'account?action=lostpass' ;
      $msg = 'The verification code is invalid or has expired<br><br>Please <a href="' . $href . '">click here</a> to request a new code';
    }
  }
  elseif(checkemail($globvars['email']) && $globvars['pass']) {
    $msg = 'Login failed';
    $globvars['email'] = strtolower($globvars['email']);
    $globvars['pass'] = md5($globvars['pass']);
    $string = "select * from `user_details` where `u_email` = '{$globvars['email']}' and `u_pass` = '{$globvars['pass']}' limit 1";
    $query = my_query($string);
    if(my_rows($query)) {
      $row = my_assoc($query);
      if($globvars['email'] == $row['u_email'] && $globvars['pass'] == $row['u_pass']) {
        $res = 'location:account';
        $msg = 'Login successful<br><br>Please <a href="account">click here</a> to go to your account';
        $arr = $row;
        $vcode = $row['u_id'] . '|' . md5($row['u_id'] . $row['u_email'] . $row['u_pass']);
        scookie('user_login',$vcode);
      }
    }
  }
  elseif($globvars['user_login'] && substr_count($globvars['user_login'], '|')) {
    $u_id = substr($globvars['user_login'], 0 , strpos($globvars['user_login'], '|'));
    $msg = $u_id ;
    $string = "select * from `user_details` where `u_id` = '$u_id' limit 1";
    $query = my_query($string);
    if(my_rows($query)) {
      $res = true;
      $msg = 'You are already logged in';
      $arr = my_assoc($query);
      if(! $arr['u_country']) {
        $arr['u_country'] = 'GB';
      }
    }
    else {
      $res = false;
    }
  }
  else {
    $msg = 'Email and password required';
  }
  $arr['u_news'] = 'No';
  if(isset($arr['u_email']) && $arr['u_email']) {
    $string = "select * from `newsletter` where `email` = '{$arr['u_email']}' limit 1";
    $query = my_query($string);
    if(my_rows($query)) {
      $row = my_assoc($query);
      if($row['mailing'] == 'yes') {
        $arr['u_news'] = 'Yes';
      }
    }
  }
  return array('res'=>$res,'arr'=>$arr,'msg'=>$msg);
}

function user_wishlist($u_id) {
  globvars('d');
  global $globvars;
  $arr = [];
  if($u_id) {
    if($globvars['d']) {
      $string = "delete from `wishlist` where `w_id` = '{$globvars['d']}' and `u_id` = '$u_id' limit 1";
      $query = my_query($string);
    }
    $string = "select * from `wishlist` where `u_id` = '$u_id' order by `wishlist`.`w_date` DESC";
    $query = my_query($string);
    if(my_rows($query)) {
      while($row = my_assoc($query)) {
        $itm = basket_item($row['i_id'],$row['o_id']);
        $itm['w_date'] = $row['w_date'];
        $arr[$row['w_id']] = $itm;
      }
    }  
  }
  return $arr;
}

function user_register() {
  global $globvars;
  $fields = ['email','pass','passc','forename','surname','address1','address2','city','state','postcode','country','phone','mobile','news'];
  $required = ['email','pass','passc','forename','surname','address1','city','postcode','country']; // ensure same as required in form
  $res = true ;
  $arr = [];
  $msg = [];
  foreach($fields as $field) {
    globvars($field);
    if(in_array( $field, $required )) {
      if( (! $globvars[$field]) || ( ($field == 'email') && ! checkemail($globvars[$field]) ) ) { 
        $arr[] = ['field'=>$field,'flag'=>'cross'];
        $res = false;
      }
      else {
        $arr[] = ['field'=>$field,'flag'=>'tick'];
      }
    }
  }
  extract($globvars);
  if(! ($phone || $mobile)) {
    $arr[] = ['field'=>'phone','flag'=>'cross'];
    $arr[] = ['field'=>'mobile','flag'=>'cross'];
    $msg[] = 'At least one phone number is required';
    $res = false;
  }
  if(strlen($pass) < 5) {
    $arr[] = ['field'=>'pass','flag'=>'cross'];
    $msg[] = 'Password must be at least 5 characters';
    $res = false;
  }
  if($pass != $passc) {
    $msg[] = 'Password and confirmation do not match';
    $res = false;
  }
  $string = "select * from `user_details` where `u_email` = '$email' limit 1";
  $query = my_query($string);
  if(my_rows($query)) {
    $arr[] = ['field'=>'email','flag'=>'cross'];
    $msg[] = 'An account with that email address already exists';
    $res = false;
  }
  $msg = implode( "<br><br>", $msg );
  if($res == true) {
    $email = strtolower($email);
    $pass = md5($pass);
    $string = "insert into `user_details` set 
      `u_email` = '{$email}',
      `u_pass` = '{$pass}',
      `u_forename` = '{$forename}',
      `u_surname` = '{$surname}',
      `u_address1` = '{$address1}',
      `u_address2` = '{$address2}',
      `u_city` = '{$city}',
      `u_state` = '{$state}',
      `u_postcode` = '{$postcode}',
      `u_country` = '{$country}',
      `u_phone` = '{$phone}',
      `u_mobile` = '{$mobile}'
    ";
    $query = my_query($string);
    $u_id = my_id();
    $msg = 'Thank you for registering<br><br>Please <a href="account">click here</a> to go to your account';
    $vcode = $u_id  . '|' . md5($u_id  . $email . $pass);
    scookie('user_login',$vcode);
    logtable('INSERT','','user_details',$string);
    if($news) {
      news_subscribe($email,$forename . ' ' . $surname);
    }
  }
  return array('res'=>$res,'arr'=>$arr,'msg'=>$msg);
}

function user_lostpass() {
  globvars('email');
  global $globvars; extract($globvars);
  $res = false;
  $arr = [];
  $msg = '';
  if($email && checkemail($email)) {
    $arr[] = ['field'=>'email','flag'=>'tick'];
    $email = strtolower($email);
    $string = "select * from `user_details` where `u_email` = '$email' limit 1";
    $query = my_query($string);
    if(my_rows($query)) {
      $row = my_assoc($query);
      $vtime = time();
      if($vtime - $row['u_vtime'] > 60) {
        $vcode = $row['u_id'] . '|' . md5($vtime . $row['u_email'] . $row['u_pass']);
        $string = "update `user_details` set `u_vtime` = '$vtime' where `u_email` = '$email' limit 1";
        $query = my_query($string);
        $res = true;
        $msg = 'Please click the link in the email to reset your password<br><br>Please note it is only valid for one hour.';

        // send email
        $subject = 'Lost Password';
        $href = $globvars['live_href'] . 'account?verify=' . $vcode ;

        $html = '<p>Please <a href="' . $href . '">click here</a> link below to reset your password</p>';
        $html .= '<p>Please note this link is only valid for one hour</p>';

        $text = "Please click the link below to reset your password\r\n\r\n{$href}";
        $text .= "\r\n\r\nPlease note this link is only valid for one hour";

        $text .= str_replace('[[year]]',date('Y'),"\r\n\r\n{$globvars['email_foot_text']}");
        $html .= text_para(str_replace('[[year]]',date('Y'),$globvars['email_foot_html']));

        htmlmail($email, $globvars['email_fr'], $globvars['email_fn'], $subject, $text, $html);
        logtable('UPDATE','','user_details',$string);
      }
      else {
        $res = false;
        $msg = 'An email has already been sent. Please check your spam folder if not received.<br><br>Please wait at least one minute before trying again.';
      }
    }
    else {  
      // even if account not found to stop phishing
      $res = true;
      $msg = 'If an account exists with your details an email has been sent to you.<br><br>Please click the link to reset your password';
    }
  }
  else {
    $arr[] = ['field'=>'email','flag'=>'cross'];
    $res = false;
  }
  return array('res'=>$res,'arr'=>$arr,'msg'=>$msg);
}

function user_resetpass() {
  globvars('email','pass','passc','verify');
  global $globvars; extract($globvars);
  $res = false;
  $arr = [];
  $msg = '';
  if($email && checkemail($email) && $verify) {
    $arr[] = ['field'=>'email','flag'=>'tick'];
    $arr[] = ['field'=>'pass','flag'=>'tick'];
    if($pass && ($pass == $passc) && (strlen($pass) >= 5)) {
      $email = strtolower($email);
      $string = "select * from `user_details` where `u_email` = '$email' limit 1";
      $query = my_query($string);
      if(my_rows($query)) {
        $row = my_assoc($query);
        $vtime = time();
        $vcode = $row['u_id'] . '|' . md5($row['u_vtime'] . $row['u_email'] . $row['u_pass']);
        $pass = md5($pass);
        if(($verify == $vcode) && ($vtime - $row['u_vtime'] < 3600)) {
          $string = "update `user_details` set `u_vtime` = '0', `u_pass` = '$pass' where `u_id` = '{$row['u_id']}' limit 1";
          $query = my_query($string);
          $res = true;
          $msg = 'Your password has been changed<br><br>Please <a href="account">click here</a> to login';
          logtable('UPDATE','','user_details',$string);
        }
        else {
          $msg = 'The verification code has expired';
        }
      }
      else {
        $msg = 'Invalid email address entered';
        $arr[] = ['field'=>'email','flag'=>'cross'];
      }
    }
    else {
      $msg = 'Password must be a minimum of 5 characters and match the confirmation';
      $arr[] = ['field'=>'pass','flag'=>'cross'];
    }
  }
  else {
    $msg = 'Invalid email address entered';
    $arr[] = ['field'=>'email','flag'=>'cross'];
  }
  return array('res'=>$res,'arr'=>$arr,'msg'=>$msg);
}

function user_edit() {
  globvars('user_login','ref','email','forename','surname','address1','address2','city','state','postcode','country','phone','mobile','news');
  global $globvars; extract($globvars);
  $res = false;
  $arr = [];
  $msg = [];
  $u_id = substr($user_login, 0 , strpos($user_login, '|'));
  if(md5($u_id) == $ref) {
    $string = "select * from `user_details` where `u_id` = '{$u_id}' limit 1";
    $query = my_query($string);
    if(my_rows($query)) {
      $row = my_assoc($query);
      if($email && checkemail($email)) {
        if($email != $row['u_email']) {
          $string = "select * from `user_details` where `u_email` = '{$email}' and `u_id` != '{$u_id}' limit 1";
          $query = my_query($string);
          if(my_rows($query)) {
            $msg[] = 'The email address entered has been used for another account';
            $arr[] = ['field'=>'email','flag'=>'cross'];
          }
        }
        if(! count($arr)) {
          if(! $forename) { $arr[] = ['field'=>'forename','flag'=>'cross']; }
          if(! $surname) { $arr[] = ['field'=>'surname','flag'=>'cross']; }
          if(! $address1) { $arr[] = ['field'=>'address1','flag'=>'cross']; }
          if(! $city) { $arr[] = ['field'=>'city','flag'=>'cross']; }
          if($country == 'US' && ! $state) { $arr[] = ['field'=>'state','flag'=>'cross']; }
          if(! $postcode) { $arr[] = ['field'=>'postcode','flag'=>'cross']; }
          if(! count($arr)) {
            $string = "update `user_details` set 
              `u_email` = '{$email}', 
              `u_forename` = '{$forename}', 
              `u_surname` = '{$surname}', 
              `u_address1` = '{$address1}', 
              `u_address2` = '{$address2}', 
              `u_city` = '{$city}', 
              `u_state` = '{$state}', 
              `u_postcode` = '{$postcode}', 
              `u_country` = '{$country}', 
              `u_phone` = '{$phone}', 
              `u_mobile` = '{$mobile}' 
              where `u_id` = '{$u_id}' limit 1";
            $query = my_query($string);
            $res = true;
            logtable('UPDATE','','user_details',$string);
            $arr[] = ['field'=>'email','flag'=>'quest'];
            $arr[] = ['field'=>'forename','flag'=>'quest'];
            $arr[] = ['field'=>'surname','flag'=>'quest'];
            $arr[] = ['field'=>'address1','flag'=>'quest'];
            $arr[] = ['field'=>'address2','flag'=>'quest'];
            $arr[] = ['field'=>'city','flag'=>'quest'];
            $arr[] = ['field'=>'state','flag'=>'quest'];
            $arr[] = ['field'=>'postcode','flag'=>'quest'];
            $arr[] = ['field'=>'country','flag'=>'quest'];
            $arr[] = ['field'=>'phone','flag'=>'quest'];
            $arr[] = ['field'=>'mobile','flag'=>'quest'];
            if($news == 'Yes') {
              $a = news_subscribe($email,$forename . ' ' . $surname);
              $msg[] = $a['msg'];
             }
            else {
              $a = news_unsubscribe($email);
              $msg[] = $a['msg'];
            }
          }
          else {
            $msg[] = 'Please complete the fields highlighted above with <b>&#x2715;</b>';
          }
        }
      }
      else {
        $msg[] = 'Invalid email address entered';
        $arr[] = ['field'=>'email','flag'=>'cross'];
      }
    }
  }
  if(! ($res || count($msg)) ) {
    $msg[] = 'An error occurred. Please contact us';
  }
  $msg = implode( "<br><br>", $msg );
  return array('res'=>$res,'arr'=>$arr,'msg'=>$msg);
}

function user_pass() {
  globvars('user_login','ref','user','pass','passn1','passn2');
  global $globvars; extract($globvars);
  $res = false;
  $arr = [];
  $msg = [];
  $u_id = substr($user_login, 0 , strpos($user_login, '|'));
  if(md5($u_id) == $ref) {
    if(strlen($passn1) < 5) {
      $msg[] = 'Password must be at least 5 characters';
      $arr[] = ['field'=>'passn','flag'=>'cross'];
    }
    if($passn1 != $passn2) {
      $msg[] = 'New password and confirmation don\'t match';
      $arr[] = ['field'=>'passn1','flag'=>'cross'];
      $arr[] = ['field'=>'passn2','flag'=>'cross'];
    }
    if($pass == $passn1) {
      $msg[] = 'Current and new password are the same';
      $arr[] = ['field'=>'passn1','flag'=>'cross'];
      $arr[] = ['field'=>'passn2','flag'=>'cross'];
    }
    if(! count($msg)) {
      $string = "select * from `user_details` where `u_id` = '{$u_id}' limit 1";
      $query = my_query($string);
      if(my_rows($query)) {
        $row = my_assoc($query);
        if($user == $row['u_email'] && md5($pass) == $row['u_pass']) {
          $passn = md5($passn1); 
          $string = "update `user_details` set `u_pass` = '{$passn}' where `u_id` = '{$u_id}' limit 1";
          $query = my_query($string);
          logtable('UPDATE','','user_details',$string);
          $arr[] = ['field'=>'user','flag'=>'quest'];
          $arr[] = ['field'=>'pass','flag'=>'quest'];
          $arr[] = ['field'=>'passn1','flag'=>'quest'];
          $arr[] = ['field'=>'passn2','flag'=>'quest'];
          $res = true;
        }
        else {
          $msg[] = 'Username or current password are incorrect';
          $arr[] = ['field'=>'pass','flag'=>'cross'];
          $arr[] = ['field'=>'user','flag'=>'cross'];
        }
      }
    }
  }
  if(! ($res || count($msg)) ) {
    $msg[] = 'An error occurred. Please contact us';
  }
  $msg = implode( "<br><br>", $msg );
  return array('res'=>$res,'arr'=>$arr,'msg'=>$msg);
}

/* CONTACT */

function contact_form() {
  global $globvars;
  $fields = ['name','phone','email','message','file'];
  $required = ['name','email']; // ensure same as required in form
  $res = true;
  $arr = [];
  $fup = '';
  $msg = '';
  $html = $text = '';
  foreach($fields as $field) {
    globvars($field);
    if(in_array( $field, $required )) {
      if( (! $globvars[$field]) || ( ($field == 'email') && ! checkemail($globvars[$field]) ) ) { 
        $res = false;
        $arr[] = ['field'=>$field,'flag'=>'cross'];
      }
      else {
        $arr[] = ['field'=>$field,'flag'=>'tick'];
      }
    }
    if($field == 'file') {
      if(is_array($globvars[$field])) {
        if($globvars[$field]['size'] == 0) {
          $msg = 'File failed to upload';
        }
        elseif($globvars[$field]['error'] || $globvars[$field]['size'] > 5000000) {
          $msg = 'File exceeds 5Mb';
        }
        elseif($globvars[$field]['tmp_name']) { 
          $uc = upload_check($field);
          if($uc['res']) {
            if($uc['ext'] == 'png' || $uc['ext'] == 'jpg' || $uc['ext'] == 'jpeg' || $uc['ext'] == 'pdf') {
              $fup = $globvars[$field] ;
            }
            else {
              $msg = 'File type invalid';
            }
          }
          else {
            $msg = $uc['ufail'];
          }
        }
        else {
          $msg = 'File upload error';
        }
        if($fup) {
          $arr[] = ['field'=>$field,'flag'=>'tick'];
        }
        else {
          $arr[] = ['field'=>$field,'flag'=>'cross'];
          $res = false;
        }
      }
    }
    else {
      $text .= "{$field} : {$globvars[$field]}\r\n\r\n";
      $html .= "<p>{$field} : {$globvars[$field]}</p>\r\n";
    }
  }
  if($res) {
    // send email
    $subject = 'Contact Form';
    // $text .= "\r\n\r\n{$globvars['email_foot_text']}";
    // $html .= text_para($globvars['email_foot_html']);
    htmlmail($globvars['email_to'], $globvars['email_fr'], $globvars['email_fn'], $subject, $text, $html, '', $globvars['email'], '', '', $fup);
  }
  return array('res'=>$res,'arr'=>$arr,'msg'=>$msg);
}

function text_para($in) {
  return '<p>' . str_replace(array("\r\n", "\r", "\n"), '<br>',$in) . '</p>';
}

/* NEWSLETTER */

function news_subscribe($email='',$name='',$company='',$phone='',$location='',$details='') {
  $res = false ;
  $arr = [];
  $msg = '';
  if($email && checkemail($email)) {
    $string = "select * from `newsletter` where `email` = " . my_aes_encrypt($email,DBKEY) . " limit 1";
    $query = my_query($string);
    if(my_rows($query)) {
      $row = my_assoc($query);
      if($row['mailing'] == 'n') {
        $note = date('d/m/Y H:i:s') . " - subscribed\r\n";
        $string = "update `newsletter` set 
          `mailing` = 'yes',
          `date` = NOW(),
          `notes` = concat(`notes`,'$note')
          where `email` = " . my_aes_encrypt($email,DBKEY) . " limit 1
        ";
        $query = my_query($string);
        $msg = "Thank you. Your email address {$email} has been subscribed to our newsletter.";
        $res = true ;
        logtable('UPDATE','','newsletter',$string);
      }
      else {
        $msg = "Your email address {$email} is aleady subscribed to our newsletter.";
        $res = true ;
      }
    }
    else {
      $note = date('d/m/Y H:i:s') . " - subscribed\r\n";
      $string = "insert into `newsletter` set 
        `email` = " . my_aes_encrypt($email,DBKEY) . ",
        `name` = " . my_aes_encrypt($name,DBKEY) . ",
        `company` = " . my_aes_encrypt($company,DBKEY) . ",
        `phone` = " . my_aes_encrypt($phone,DBKEY) . ",
        `location` = '$location',
        `details` = '$details',
        `mailing` = 'yes',
        `date` = NOW(),
        `notes` = '$note'
      ";
      $query = my_query($string);
      $msg = "Thank you. Your email address {$email} has been subscribed to our newsletter.";
      $res = true ;
      logtable('INSERT','','newsletter',$string);
      if($globvars['email_to'] && $globvars['email_fr']) {
        $content = "Email: {$email}\r\nName: {$name}\r\nCompany: {$company}\r\nPhone: {$phone}\r\nLocation: {$location}\r\n\r\n{$details}";
        sendmail($globvars['email_to'], $globvars['email_fr'], 'T&D Newsletter', $content, '', '', '', '', $globvars['email_fn']);
      }
    }
  }
  else {
    $msg = "Your email address {$email} is invalid.";
  }
  return array('res'=>$res,'arr'=>$arr,'msg'=>$msg);
}

function news_unsubscribe($email='') {
  $res = false ;
  $arr = [];
  $msg = '';
  if($email && checkemail($email)) {
    $string = "select * from `newsletter` where `email` = '$email' limit 1";
    $query = my_query($string);
    if(my_rows($query)) {
      $row = my_assoc($query);
      if($row['mailing'] == 'yes') {
        $note = date('d/m/Y H:i:s') . " - unsubscribed\r\n";
        $string = "update `newsletter` set 
          `mailing` = 'no', 
          `date` = NOW(), 
          `notes` = concat(`notes`,'$note')
          where `email` = '$email' limit 1
        ";
        $query = my_query($string);
        $msg = "Your email address {$email} has been unsubscribed from our newsletter.";
        $res = true ;
        logtable('UPDATE','','newsletter',$string);
      }
      else {
        $msg = "Your email address {$email} is aleady unsubscribed from our newsletter.";
        $res = true ;
      }
    }
    else {
      $msg = "Your email address {$email} is aleady unsubscribed from our newsletter.";
      $res = true ;
    }
  }
  else {
    $msg = "Your email address {$email} is invalid.";
  }
  return array('res'=>$res,'arr'=>$arr,'msg'=>$msg);
}

function news_form() {
  global $globvars;
  /*
  if($globvars['params'] == 'newsletter') { return; } 
  ?>
  <form action="#" method="post" onsubmit="return news_submit();">
    <div id="newsform1">
      <div id="newsform2">
          <div id="newsforms" class="newsformc"><?= $globvars['news_sign'] ?></div>
          <div id="newsformi" class="newsformc"><input placeholder="<?= $globvars['forms_email'] ?>" type="text" name="news_email" id="news_email" maxlength="200"><a href="#" onclick="return news_submit();"><img src="images/arrow_right.png"></a></div>
      </div> 
    </div>    
    <div id="newsformd"><?= $globvars['news_thanks'] ?></div>
  </form>
  <?
  ?>
  <div id="newsform1">
    <div id="newsform2">
        <div id="newsforms" class="newsformc"><a href="newsletter"><?= $globvars['news_sign'] ?></a></div>
    </div> 
  </div>    
  <?
  */
}

function partner_formtab() {
  global $globvars;
  ?>
  <div id="partnerpop">
    <div id="partnertab">
      <div id="partnertab1" class="nobr">
        <a id="partnertab1a" href="#" title="<?= $globvars['pages_main']['partner']['head2'] ?>"><?= $globvars['pages_main']['partner']['head2'] ?></a>
      </div>
    </div>
    <div id="partnerpopform">
      <div id="partnerpopformd"><?= $globvars['pages_main']['partner']['html2'] ?></div>
      <form id="partnerpopformf" action="#" method="post" onsubmit="return partnerpop_submit()">
        <div class="partnerpopformn"><?= $globvars['forms_name'] ?></div>
        <div class="partnerpopformi"><input class="partnerpopformb" type="text" id="partnerpop_name" value="" maxlength="200"></div>
        <div class="partnerpopformn"><?= $globvars['forms_email'] ?></div>
        <div class="partnerpopformi"><input class="partnerpopformb" type="text" id="partnerpop_email" value="" maxlength="200"></div>
        <div class="partnerpopformn"><?= $globvars['forms_company'] ?></div>
        <div class="partnerpopformi"><input class="partnerpopformb" type="text" id="partnerpop_company" value="" maxlength="200"></div>
        <? /* ?><div class="partnerpopformn"><?= $globvars['forms_gdpr'] ?><input type="checkbox" id="partnerpop_mailing" value="yes"></div><? */ ?>
        <div class="partnerpopforms"><input type="submit" class="submit" name="submit" value="<?= $globvars['forms_submit'] ?>"></div>
      </form>
      <div id="partnerpopformt">
        <div id="partnerpopformth"><?= $globvars['pages_main']['partner']['head3'] ?></div>
        <?= $globvars['pages_main']['partner']['html3'] ?>
      </div>
    </div>
    <a id="partnerpopclose" href="#">&#x2715;</a>
  </div>
  <?
}

function partner_submit() {
  global $globvars;
  globvars('email','name','company','phone','location','details','mailing');
  $res = false ;
  $arr = [];
  $msg = '';
  if($globvars['email']) {
    $res = true ;
    /*
    $string = "insert into `partner` set 
      `email` = " . my_aes_encrypt($globvars['email'],DBKEY) . ", 
      `name` = " . my_aes_encrypt($globvars['name'],DBKEY) . ", 
      `company` = " . my_aes_encrypt($globvars['company'],DBKEY) . ", 
      `phone` = " . my_aes_encrypt($globvars['phone'],DBKEY) . ",
      `location` = '{$globvars['location']}',
      `details` = '{$globvars['details']}',
      `date` = NOW() 
    ";
    my_query($string);
    if($globvars['mailing'] == 'yes') {
      news_subscribe($globvars['email'],$globvars['name'],$globvars['company'],$globvars['phone'],$globvars['location'],$globvars['details']);
    }
    */
    if($globvars['mailing'] != 'yes') {
      $globvars['mailing'] = 'no';
    }
    if($globvars['email_to'] && $globvars['email_fr']) {
      // $content = "Email: {$globvars['email']}\r\nName: {$globvars['name']}\r\nCompany: {$globvars['company']}\r\nPhone: {$globvars['phone']}\r\nLocation: {$globvars['location']}\r\nMarketing: {$globvars['mailing']}\r\n\r\n{$globvars['details']}";
      $content = "Email: {$globvars['email']}\r\nName: {$globvars['name']}\r\nCompany: {$globvars['company']}\r\nPhone: {$globvars['phone']}\r\nLocation: {$globvars['location']}\r\n\r\n{$globvars['details']}";
      sendmail($globvars['email_to'], $globvars['email_fr'], 'T&D Partner With Us', $content, '', '', '', '', $globvars['email_fn']);
    }
  }
  else {
    $msg = 'email invalid';
  }
  return array('res'=>$res,'arr'=>$arr,'msg'=>$msg);
}

function contact_submit() {
  global $globvars;
  globvars('email','name','company','phone','location','details','mailing');
  $res = false ;
  $arr = [];
  $msg = '';
  if($globvars['email']) {
    $res = true ;
    /*
    $string = "insert into `contact` set 
      `email` = " . my_aes_encrypt($globvars['email'],DBKEY) . ", 
      `name` = " . my_aes_encrypt($globvars['name'],DBKEY) . ", 
      `company` = " . my_aes_encrypt($globvars['company'],DBKEY) . ", 
      `phone` = " . my_aes_encrypt($globvars['phone'],DBKEY) . ",
      `location` = '{$globvars['location']}',
      `details` = '{$globvars['details']}',
      `date` = NOW() 
    ";
    my_query($string);
    if($globvars['mailing'] == 'yes') {
      news_subscribe($globvars['email'],$globvars['name'],$globvars['company'],$globvars['phone'],$globvars['location'],$globvars['details']);
    }
    */
    if($globvars['mailing'] != 'yes') {
      $globvars['mailing'] = 'no';
    }
    if($globvars['email_to'] && $globvars['email_fr']) {
      // $content = "Email: {$globvars['email']}\r\nName: {$globvars['name']}\r\nCompany: {$globvars['company']}\r\nPhone: {$globvars['phone']}\r\nLocation: {$globvars['location']}\r\nMarketing: {$globvars['mailing']}\r\n\r\n{$globvars['details']}";
      $content = "Email: {$globvars['email']}\r\nName: {$globvars['name']}\r\nCompany: {$globvars['company']}\r\nPhone: {$globvars['phone']}\r\nLocation: {$globvars['location']}\r\n\r\n{$globvars['details']}";
      sendmail($globvars['email_to'], $globvars['email_fr'], 'T&D Contact Us', $content, '', '', '', '', $globvars['email_fn']);
    }
  }
  else {
    $msg = 'email invalid';
  }
  return array('res'=>$res,'arr'=>$arr,'msg'=>$msg);
}


/* SHOP */

function price_calc($i_price,$i_discprice,$i_discpcnt,$i_discsubs='no',$o_price=0,$o_discprice=0,$o_discpcnt=0) {
  global $globvars;

  // item price
  $i_pricecalc = $o_pricecalc = $i_price;
  if($i_discprice > 0 && $i_discprice < $i_price) {
    // fixed item discount
    $i_pricecalc = $i_price - $i_discprice ;
  }
  elseif($i_discpcnt > 0) {
    // percent item discount
    $i_pricecalc = $i_price * (100 - $i_discpcnt) / 100 ;
  }
  $i_pricecalc = round($i_pricecalc,2) ;

  // option price
  if($o_price <= 0) {
    $o_price = $i_price;
  }
  $o_pricecalc = $o_price;
  if($o_discprice > 0 && $o_discprice < $o_price) {
    // fixed option discount 
    $o_pricecalc = $o_price - $o_discprice ;
  }
  elseif($o_discpcnt > 0) {
    // percent option discount
    $o_pricecalc = $o_price * (100 - $o_discpcnt) / 100 ;
  }
  elseif($i_discsubs == 'yes') {
    if($i_discprice > 0 && $i_discprice < $o_price) {
      // fixed option discount from item
      $o_pricecalc = $o_price - $i_discprice ;
    }
    elseif($i_discpcnt > 0) {
      // percent option discount from item
      $o_pricecalc = $o_price * (100 - $i_discpcnt) / 100 ;
    }
  }
  $o_pricecalc = round($o_pricecalc,2) ;

  return ['i_pricecalc'=>number_format($i_pricecalc,2,'.',''),'o_pricecalc'=>number_format($o_pricecalc,2,'.','')];
}

function price_range() {
  global $globvars;
  $pricemin = $pricemax = 0 ;
  if(isset($globvars['pricecalcs'])) {
    if(isset($globvars['pricecalcs'][0]) && (count($globvars['pricecalcs']) > 1)) {
      // options so ignore main
      unset($globvars['pricecalcs'][0]);
    }
    // print_arv($globvars['pricecalcs']);
    foreach($globvars['pricecalcs'] as $price) {
      if($price > $pricemax) {
        $pricemax = $price;
      }
      if(($pricemin == 0) || ($price > 0 && $price < $pricemin)) {
        $pricemin = $price;
      }
    }
  }
  return ['pricemin'=>number_format($pricemin,2,'.',''),'pricemax'=>number_format($pricemax,2,'.','')];
}

function calc_net($in) {
  global $globvars;
  return round($in / (1 + $globvars['vat_rate']),2);
}

function price_rdisp($min,$max) {
  if($min > 0) {
    return $min == $max ? curr_disp($min) : curr_disp($min) . ' - ' . curr_disp($max) ;
  }
}

function basket_voucher($voucher,$itot=0,$prds=[],$clr='') {
  // prds is array [i_id => price]
  global $globvars;
  $globvars['vouch_nofinance'] = false ;
  $text = '';
  $amount = 0 ;
  if($voucher && $itot) {
    $voucher = strtoupper($voucher);
    if(! $amount) {
      // check vouchers
      $string = "SELECT * FROM `vouchers` WHERE 
        ( `vcode` = '$voucher' ) AND 
        ( `active` = 'yes' ) AND 
        ( `usage` = 'multiple' OR `last` = '0000-00-00 00:00:00' ) AND 
        ( `minimum` <= '$itot' ) AND 
        ( `starts` = '0000-00-00 00:00:00' OR NOW() >= `starts` ) AND 
        ( `expires` = '0000-00-00 00:00:00' OR NOW() <= `expires` ) 
        LIMIT 1";
      // print_arv($string) ;
      $query = my_query($string); 
      if(my_rows($query)) {
        $v_row = my_array($query);
        $p_arr = [];
        if($v_row['products']) {
          $p_arr = explode(",",$v_row['products']);
        }
        if(! count($p_arr)) {
          // apply to total
          if($v_row['fixed'] > 0) {
            $amount = $v_row['fixed'] ;
            $text = 'Fixed ' . $v_row['fixed'] ;
          }
          elseif($v_row['percent'] > 0) {
            $amount = round($itot * $v_row['percent'] / 100 , 2 ) ;
            $text = $v_row['percent'] . '%';
          }
        }
        else {
          // apply to each product
          foreach($prds as $i_id => $i_price) {
            if(in_array($i_id, $p_arr)) {
              // matching item
              if($v_row['fixed'] > 0) {
                $amount = $v_row['fixed'] ;
                // only deduct fixed once
                break;
              }
              elseif($v_row['percent'] > 0) {
                $amount += round($i_price * $v_row['percent'] / 100 , 2 ) ;
              }
              $text = '(item discounts)';
            }
          }
        }
        if($v_row['finance'] == 'n') {
          $globvars['vouch_nofinance'] = true ;
        }
        if($clr) {
          // voucher used
          $string1 = "UPDATE `vouchers` SET `last` = NOW(), `count` = `count` + 1 WHERE `vcode` = '$voucher' LIMIT 1";
          // print_p($string1);
          my_query($string1);
          logtable('UPDATE','payment','vouchers',$string1);
          $voucher = '' ;
        }
      }
    }
  }

  if( $itot && ( $amount <= 0 ) ) {
    // delete if no discount
    $voucher = '';
    $amount = 0 ;
  }
  $_SESSION['voucher'] = $voucher;
  return array('amount' => $amount, 'voucher' => $voucher, 'text' => $text);
}

function basket_action($i_id,$o_id=0,$num=0) {
  // chngaes to basket
  global $globvars;
  $res = false ;
  $globvars['basket']['msg'] = '';
  $msg1 = '';
  $itm = basket_item($i_id,$o_id);
  if(count($itm)) {
    $b_id = $itm['i_id'] . '_' . $itm['o_id'];
    if(isset($_SESSION[BASKET][$b_id])) {
      $bnum = $_SESSION[BASKET][$b_id]['num'];
      if($num > 0) {
        if($itm['stock'] > 0) {
          $bnum += $num;
          if($bnum > $itm['stock']) {
            $bnum = $itm['stock'];
            $msg1 = ' (up to max stock)';
          }
          $_SESSION[BASKET][$b_id]['num'] = $bnum ;
          $globvars['basket']['msg'] = "Item added to basket{$msg1}";
          $res = true ;
        }
        else {
          $globvars['basket']['msg'] = $itm['available'];
        }
      }
      elseif($num < 0) {
        $bnum += $num;
        if($bnum > 0) {
          $_SESSION[BASKET][$b_id]['num'] = $bnum ;
        }
        else {
          unset($_SESSION[BASKET][$b_id]);
        }
        $globvars['basket']['msg'] = 'Item removed from basket';
      }
    }
    elseif($num > 0) {
      if($num > $itm['stock']) {
        $num = $itm['stock'];
        $msg1 = ' (up to max stock)';
      }
      $itm['num'] = $num > 0 ? $num : 1;
      $_SESSION[BASKET][$b_id] = $itm;
      $globvars['basket']['msg'] = "Item added to basket{$msg1}";
      $res = true ;
    }
    else {
      $globvars['basket']['msg'] = "No amount selected";
    }
  }
  else {
    $itm = [];
    $globvars['basket']['msg'] = "Item not found";
  }
}

function basket_item($i_id,$o_id) {
  global $globvars;
  // get item details
  $arr = [];
  if($i_id) {
    $string = "select * from `shop_items`
      left join `shop_subs` on `shop_subs`.`s_id`=`shop_items`.`s_id` 
      left join `shop_cats` on `shop_cats`.`c_id`=`shop_subs`.`c_id`
      where `shop_items`.`i_id` = '{$i_id}' limit 1;
    ";
    $query = my_query($string);
    if(my_rows($query)) {
      $i_arr = my_assoc($query);
      // print_arv($i_arr);
      $arr['i_id'] = $i_id;
      $arr['o_id'] = 0;
      $arr['sku'] = $i_arr['i_sku'];
      $arr['mpn'] = $i_arr['i_mpn'];
      $arr['caturl'] = 'shop/' . $i_arr['c_url'] . '/' . $i_arr['s_url'];
      $arr['catname'] = $i_arr['c_head'];
      if($i_arr['s_head']) {
        $arr['catname'] .= ' - ' . $i_arr['s_head'];
      }
      $arr['prodname'] = $i_arr['i_head'];
      $arr['produrl'] = $arr['caturl'] . '/' . $i_arr['i_url'];
      $arr['options'] = '';
      $arr['original'] = $i_arr['i_price'];
      $arr['price'] = $i_arr['i_pricecalc'];
      $arr['pricec'] = curr_conv($i_arr['i_pricecalc']);
      $arr['stock'] = $i_arr['i_stock'];
      $arr['available'] = '';
      if($i_arr['i_stock'] > 0) {
        if(($i_arr['i_expected'] != '0000-00-00') && (ddates($i_arr['i_expected']) > 0)) {
          $arr['available'] = 'Stock Expected ' . cdate($i_arr['i_expected'],'d/m/Y');
        }
      }
      else {
        $arr['available'] = 'Out of Stock';
      }
      $arr['shipopt'] = $i_arr['so_id'];

      // options
      $gopts = '';
      if($o_id) {
        $string1 = "select * from `shop_options` where `o_id` = '{$o_id}' and `i_id` = '{$i_id}' limit 1";
        $query1 = my_query($string1);
        if(my_rows($query1)) {
          $o_arr = my_assoc($query1);
          $gopts = $o_arr['o_images'];
          $arr['o_id'] = $o_id;
          if($o_arr['o_sku']) {
            $arr['sku'] = $o_arr['o_sku'];
          }
          if($o_arr['o_mpn']) {
            $arr['mpn'] = $o_arr['o_mpn'];
          }
          if($o_arr['o_pricecalc'] > 0) {
            $arr['price'] = $o_arr['o_pricecalc'];
          }
          $arr['stock'] = $o_arr['o_stock'];
          $arr['available'] = '';
          if($o_arr['o_stock'] > 0) {
            if(($o_arr['o_expected'] != '0000-00-00') && (ddates($o_arr['o_expected']) > 0)) {
              $arr['available'] = 'Stock Expected ' . cdate($o_arr['o_expected'],'d/m/Y');
            }
          }
          else {
            $arr['available'] = 'Out of Stock';
          }
          if($i_arr['i_option1'] && $o_arr['o_option1']) {
            $arr['options'] .= $i_arr['i_option1'] . ': ' . $o_arr['o_option1'];
          }
          elseif($o_arr['o_option1']) {
            $arr['options'] = $o_arr['o_option1'];
          }
          if($i_arr['i_option2'] && $o_arr['o_option2']) {
            if($arr['options']) { $arr['options'] .= "<br>"; }
            $arr['options'] .= $i_arr['i_option2'] . ': ' . $o_arr['o_option2'];
          }
          elseif($o_arr['o_option2']) {
            if($arr['options']) { $arr['options'] .= "<br>"; }
            $arr['options'] .= $o_arr['o_option2'];
          }
        }
      }
      // image
      $arr['imgsrc'] = 'images/shopsmall.jpg'; 
      if($gopts) {
        $string2 = "select * from `shop_images` where `g_id` IN ({$gopts}) and `g_file` != '' order by field(`g_id`,{$gopts}), `g_order` = 0, `g_order`, `g_file` limit 1";
      }
      else {
        $string2 = "select * from `shop_images` where `i_id` = '{$i_id}' and `g_file` != '' order by `g_order` = 0, `g_order`, `g_file` limit 1";
      }
      // print($string2 . "\r\n");
      $query2 = my_query($string2);
      if(my_rows($query2)) {
        $g_arr = my_assoc($query2);
        if($g_arr['g_file']) {
          $sml = 'images/shop/' . $i_arr['i_id'] . '/small/' . $g_arr['g_file'];
          $lrg = 'images/shop/' . $i_arr['i_id'] . '/small/' . $g_arr['g_file'];
          if(file_exists($sml)) {
            $arr['imgsrc'] = $sml; 
          }
          elseif(file_exists($lrg)) {
            $arr['imgsrc'] = $lrg; 
          }
        }
      }
    }
  }
  return $arr ;
}

function menu_icons() {
  // pop basket & currency
  global $globvars;
  $bnum = 0 ;
  if(isset($globvars['basket']['items'])) {
    foreach($globvars['basket']['items'] as $itm) {
      $bnum += $itm['num'];
    }
  }
  if(isset($globvars['hide_pop'])) { return ; }
  ?>
  <div id="bsk_icon" class="svg_icon">
    <a href="basket" title="Basket"><? include('svg/basket.svg') ?><span id="bsk_num" style="<?= $bnum ? '' : 'display:none' ; ?>"><?= $bnum ; ?></span></a>
  </div>
  <? /* ?>
  <div id="curr_now" class="svg_icon curr_opt">
    <a href="#" title="Currency"><span class="tpart1"><?= $globvars['curr_arr']['symbol'] ?></span><span class="tpart2"><?= $globvars['curr_arr']['code'] ?></span></a>
  </div>
  <? */ ?>
  <div id="acc_icon" class="svg_icon">
    <a href="account" title="Account"><? include('svg/user.svg') ?></a>
  </div>
  <div id="wsh_icon" class="svg_icon">
    <a href="account" title="Wishlist"><? include('svg/heart.svg') ?></a>
  </div>

  <div id="bsk_popa">
    <div id="bsk_pop">
      <div id="bsk_poph">
        <a href="#" onclick="return bsk_close()">&#x2715;</a>
        ITEMS IN BASKET
      </div>
      <div id="bsk_popi">
        <?
        if($globvars['basket']['pop']) {
          print $globvars['basket']['pop'];
        }
        else {
          ?>
          <div id="bmsg">No items in basket</div>
          <?
        }
        ?>
      </div>
      <div id="bsk_goto" class="buttons">
        <a href="basket">GO TO BASKET</a>
      </div>
    </div>

    <div id="curr_pop">
      <div id="curr_poph">
        <a href="#" onclick="return curr_close();">&#x2715;</a>
        CURRENCY
      </div>
      <?
      foreach($globvars['currencies'] as $code => $carr) {
        if($carr['visible'] == 'yes') {
          ?>
          <div class="curr_opt">
            <a title="<?= $carr['code'] ?>" href="#" onclick="return curr_set('<?= $carr['code'] ?>');"><span class="tpart1"><?= $carr['symbol'] ?></span><span class="tpart2"><?= $carr['code'] ?></span><span class="tpart3"><img class="csel_img" src="<?= 'images/currency/' . $carr['flag'] ?>" alt=""></span></a>
          </div>
          <?
        }
      }
      ?>
    </div>
  </div>
  <form id="curr_form" action="<?= $globvars['base_href'] . $globvars['page']['url'] ?>" method="get">
    <input type="hidden" id="curr_select" name="curr_select" value="">
  </form>
  <?
}

function basket_items() {
  // change then get basket
  global $globvars;
  globvars('b','n','i_id','o_id','num','ship_opt','(array) bill','(array) deliv');
  $calcs = isset($globvars['page']['url']) && ( $globvars['page']['url'] == 'basket' || $globvars['page']['url'] == 'checkout') ? true : false;

  $vitems = [];
  $globvars['basket']['items'] = [];
  $globvars['basket']['pop'] = '';
  $globvars['basket']['msg'] = '';
  $globvars['basket']['region'] = 1;

  $globvars['basket']['shipopt'] = 0;
  $globvars['basket']['shiptxt'] = 'Free worldwide shipping';
  $globvars['basket']['shiptyp'] = '';

  $globvars['basket']['voucher'] = [];
  $globvars['basket']['vat_rate'] = $globvars['vat_rate'];

  $globvars['basket']['gbp']['voucher'] = 0;
  $globvars['basket']['gbp']['items'] = 0;
  $globvars['basket']['gbp']['subt'] = 0;
  $globvars['basket']['gbp']['net'] = 0;
  $globvars['basket']['gbp']['vat'] = 0;
  $globvars['basket']['gbp']['gross'] = 0;
  $globvars['basket']['gbp']['ship'] = 0;
  $globvars['basket']['gbp']['total'] = 0;

  $globvars['basket']['cur']['code'] = $globvars['curr_arr']['code'];
  $globvars['basket']['cur']['rate'] = $globvars['curr_arr']['rate'];
  $globvars['basket']['cur']['voucher'] = 0;
  $globvars['basket']['cur']['items'] = 0;
  $globvars['basket']['cur']['subt'] = 0;
  $globvars['basket']['cur']['net'] = 0;
  $globvars['basket']['cur']['vat'] = 0;
  $globvars['basket']['cur']['gross'] = 0;
  $globvars['basket']['cur']['ship'] = 0;
  $globvars['basket']['cur']['total'] = 0;

  $globvars['basket']['address']['bill'] = [];
  $globvars['basket']['address']['deliv'] = [];
  $globvars['basket']['addrmsg'] = '';
  $globvars['basket']['addrfail'] = [];

  if($calcs) {

    // addresses
    $bflds = ['forename','surname','company','address1','address2','city','state','postcode','country','email','phone','mobile'];
    $bfldr = ['forename','surname','address1','city','state','postcode','country','email'];
    $dflds = ['forename','surname','company','address1','address2','city','state','postcode','country'];

    // billing

    foreach($bflds as $fld) {
      if($globvars['action'] == 'checkout' && isset($globvars['bill'][$fld])) {
        $fv = $_SESSION['bill'][$fld] = $globvars['bill'][$fld];
      }
      elseif(isset($_SESSION['bill'][$fld])) {
        $fv = $_SESSION['bill'][$fld];
      }
      elseif(isset($globvars['user']['arr']['u_'.$fld]) && $globvars['user']['arr']['u_'.$fld]) {
        $fv = $_SESSION['bill'][$fld] = $globvars['user']['arr']['u_'.$fld];
      }
      elseif($fld == 'country') {
        $fv = 'GB';
      }
      else {
        $fv = '';
      }
      $globvars['basket']['address']['bill'][$fld] = $fv;
      if(in_array( $fld, $bfldr ) && ! $fv) {
        $globvars['basket']['addrfail'][]  = 'bill_' . $fld ;
      }
    }

    // delivery same as billing
    if($globvars['action'] == 'checkout') {
      $globvars['deliv_same'] = $_SESSION['deliv_same'] = isset($_POST['deliv_same']) ? $_POST['deliv_same'] : '';
    }
    elseif(isset($_SESSION['deliv_same'])) {
      $globvars['deliv_same'] = $_SESSION['deliv_same'];
    }
    else {
      $globvars['deliv_same'] = 'Yes';
    }
    if($globvars['deliv_same']) {
      $_SESSION['deliv'] = [];
      $globvars['deliv'] = $globvars['bill'];
    }
    
    // delivery
    foreach($dflds as $fld) {
      if($globvars['action'] == 'checkout' && isset($globvars['deliv'][$fld])) {
        $fv = $_SESSION['deliv'][$fld] = $globvars['deliv'][$fld];
      }
      elseif(isset($_SESSION['deliv'][$fld])) {
        $fv = $_SESSION['deliv'][$fld];
      }
      elseif($fld == 'country') {
        $fv = 'GB';
      }
      else {
        $fv = '';
      }
      $globvars['basket']['address']['deliv'][$fld] = $fv;
      if(! $fv) {
        $globvars['basket']['addrfail'][]  = 'deliv_' . $fld ;
      }
    }

    /*
    if(! ($globvars['basket']['address']['bill']['phone'] || $globvars['basket']['address']['bill']['mobile'])) {
      $globvars['basket']['addrmsg'] .= '<p>At least one phone number is required</p>' ;
      $globvars['basket']['addrfail'][]  = 'bill_phone' ;
    }
    */

    // shipping region
    $sr_id = 1;
    if($globvars['basket']['address']['deliv']['country']) {
      // from checkout
      $string = "select `sr_id` from `ship_countries` where `sc_code` = '{$globvars['basket']['address']['deliv']['country']}' limit 1";
      $query = my_query($string);
      if(my_rows($query)) {
        $row = my_assoc($query);
        $sr_id = $row['sr_id'];
      }
    }
    elseif(isset($_SESSION['curr_region']) && $_SESSION['curr_region']) {
      // from currency
      $sr_id = $_SESSION['curr_region'];
    }
    $string = "select `sr_id` from `ship_regions` where `sr_id` = '{$sr_id}' limit 1";
    $query = my_query($string);
    if(my_rows($query)) {
      $globvars['basket']['region'] = $sr_id;
    }

    // shipping
    $globvars['basket']['shipopts']['id'] = 1;
    $globvars['basket']['shipopts']['region'] = '';
    $globvars['basket']['shipopts']['deftype'] = '';
    $globvars['basket']['shipopts']['rates'] = [];
    $globvars['basket']['shipopts']['totals'] = [];
    $string = "select 
      `ship_regions`.`sr_id`, 
      `ship_regions`.`sr_name`, 
      `ship_options`.`so_id`, 
      `ship_options`.`so_name`, 
      `ship_types`.`st_id`, 
      `ship_types`.`st_name`, 
      `ship_rates`.`sr_price`  
      from `ship_rates` 
      left join `ship_options` on `ship_options`.`so_id` = `ship_rates`.`so_id` 
      left join `ship_types` on `ship_types`.`st_id` = `ship_rates`.`st_id` 
      left join `ship_regions` on `ship_regions`.`sr_id` = `ship_types`.`sr_id` 
      where `ship_regions`.`sr_id` = '{$globvars['basket']['region']}'
      order by `ship_options`.`so_order`, `ship_types`.`st_order`
    ";
    // print_p($string);
    $query = my_query($string);
    $k = 0 ;
    while($row = my_assoc($query)) {
      $globvars['basket']['shipopts']['id'] = $row['sr_id'];
      $globvars['basket']['shipopts']['region'] = $row['sr_name'];
      if(! $k++) {
        $globvars['basket']['shipopts']['deftype'] = $row['st_name'];
      }
      $globvars['basket']['shipopts']['rates'][$row['so_id']]['option'] = $row['so_name'] ;
      $globvars['basket']['shipopts']['rates'][$row['so_id']]['options'][$row['st_id']] = ['name' => $row['st_name'], 'price' => $row['sr_price']] ;
      $globvars['basket']['shipopts']['totals'][$row['st_id']] = ['name'=>$row['st_name'], 'gbp'=>0, 'cur'=>0] ;
    }
  }

  if($globvars['b'] && isset($_SESSION[BASKET][$globvars['b']])) {
    // from basket
    basket_action($_SESSION[BASKET][$globvars['b']]['i_id'],$_SESSION[BASKET][$globvars['b']]['o_id'],$globvars['n']);
  }
  elseif($globvars['i_id']) {
    // from jquery
    basket_action($globvars['i_id'],$globvars['o_id'],$globvars['num']);
  }
  if(isset($_SESSION[BASKET]) && count($_SESSION[BASKET])) {
    $globvars['basket']['pop'] .= "<div id=\"blines\">";
    foreach($_SESSION[BASKET] as $b_id => $b_arr) {
      // get item
      $itm = basket_item($b_arr['i_id'],$b_arr['o_id']);
      // print_arv($itm);

      // stock
      if($itm['stock'] >= $b_arr['num']) {
        $itm['num'] = $b_arr['num'];
      }
      else {
        $itm['num'] = $itm['stock'];
      }

      // shipping
      $itm['ship'] = 0 ;
      if(($itm['num'] > 0) && isset($globvars['basket']['shipopts']['rates'][$itm['shipopt']])) {
        if(isset($globvars['basket']['shipopts']['totals'][$globvars['ship_opt']])) {
          $k = $globvars['ship_opt'];
        }
        else {
          $k = array_key_first($globvars['basket']['shipopts']['totals']);
        }
        $globvars['basket']['shipopt'] = $_SESSION['ship_opt'] = $k;
        $globvars['basket']['shiptxt'] = 'Shipping to ' . $globvars['basket']['shipopts']['region'];
        foreach($globvars['basket']['shipopts']['totals'] as $f => $s) {
          if($f == $k) {
            $itm['ship'] = $globvars['basket']['shipopts']['rates'][$itm['shipopt']]['options'][$f]['price'] * $itm['num'] ; // per item
            $globvars['basket']['gbp']['ship'] += $itm['ship'];
            $globvars['basket']['cur']['ship'] += curr_conv($itm['ship']);
            $globvars['basket']['shiptyp'] = ' (' . $globvars['basket']['shipopts']['totals'][$f]['name'] . ')';
          }
          $stot = $globvars['basket']['shipopts']['rates'][$itm['shipopt']]['options'][$f]['price'] * $itm['num'];
          $globvars['basket']['shipopts']['totals'][$f]['gbp'] += $stot;
          $globvars['basket']['shipopts']['totals'][$f]['cur'] += curr_conv($stot);
        }
      }

      $globvars['basket']['items'][$b_id] = $itm ;
      $price = $original = 0 ;
      if($itm['num'] > 0) {
        $price = $itm['price'] * $itm['num'];
        $original = $itm['original'] * $itm['num'];
      }
      $globvars['basket']['pop'] .= "<div class=\"bline\"><div class=\"bpic\"><img src=\"{$itm['imgsrc']}\"></div><div class=\"bname\"><a title=\"{$itm['prodname']}\" href=\"{$itm['produrl']}\">{$itm['prodname']}</a> ({$itm['num']})<div class=\"bopts\">{$itm['options']}</div></div><div class=\"bprice\">" ;
      if($price > 0) {
        $globvars['basket']['pop'] .= $globvars['disp_net'] ? curr_disp(calc_net($price)) : curr_disp($price);
        if($original > $price) {
          $globvars['basket']['pop'] .= "<br><span class=\"bprev1\"><span class=\"bprev2\">" . ($globvars['disp_net'] ? curr_disp(calc_net($original)) : curr_disp($original)) . "</span></span>";
        }
      }
      else {
        $globvars['basket']['pop'] .= $itm['available'];
      }
      $globvars['basket']['pop'] .= "</div></div>"; 
      $globvars['basket']['gbp']['items'] += $price ;
      $globvars['basket']['cur']['items'] += curr_conv($price) ;
      $vitems[$itm['i_id']] = $price;
    }
    $globvars['basket']['pop'] .= "</div>"; 
  }
  if($globvars['basket']['msg']) {
    $globvars['basket']['pop'] .= "<div id=\"bmsg\">{$globvars['basket']['msg']}</div>"; 
  }

  if($calcs) {
    // items and shipping
    $globvars['basket']['gbp']['subt'] = $globvars['basket']['gbp']['items'];
    $globvars['basket']['cur']['subt'] = $globvars['basket']['cur']['items'];

    // voucher
    globvars('voucher');
    $globvars['basket']['voucher'] = basket_voucher($globvars['voucher'],$globvars['basket']['gbp']['subt'],$vitems);
    $globvars['voucher'] = $globvars['basket']['voucher']['voucher'];

    $globvars['basket']['gbp']['voucher'] = $globvars['basket']['voucher']['amount'];
    $globvars['basket']['cur']['voucher'] = curr_conv($globvars['basket']['voucher']['amount']);

    $globvars['basket']['gbp']['subt'] -= $globvars['basket']['gbp']['voucher'];
    $globvars['basket']['cur']['subt'] -= $globvars['basket']['cur']['voucher'];

    // net
    $globvars['basket']['gbp']['net'] = round( ($globvars['basket']['gbp']['subt']) / (1 + $globvars['vat_rate']),2);
    $globvars['basket']['cur']['net'] = round( ($globvars['basket']['cur']['subt']) / (1 + $globvars['vat_rate']),2);

    if($globvars['basket']['region'] == 1) { // Delivery to UK
      // vat
      $globvars['basket']['gbp']['vat'] = $globvars['basket']['gbp']['subt'] - $globvars['basket']['gbp']['net'];
      $globvars['basket']['cur']['vat'] = $globvars['basket']['cur']['subt'] - $globvars['basket']['cur']['net'];

      // gross
      $globvars['basket']['gbp']['gross'] = $globvars['basket']['gbp']['subt'];
      $globvars['basket']['cur']['gross'] = $globvars['basket']['cur']['subt'];
    }
    else { // Delivery overseas
      // vat
      $globvars['basket']['gbp']['vat'] = 0;
      $globvars['basket']['cur']['vat'] = 0;

      // gross
      $globvars['basket']['cur']['gross'] = $globvars['basket']['gbp']['net'];
      $globvars['basket']['gbp']['gross'] = $globvars['basket']['cur']['net'];
    }

    // total
    $globvars['basket']['gbp']['total'] = $globvars['basket']['gbp']['gross'] + $globvars['basket']['gbp']['ship'];
    $globvars['basket']['cur']['total'] = $globvars['basket']['cur']['gross'] + $globvars['basket']['cur']['ship'];  
  }

}

function basket_display() {
  // display basket
  global $globvars;
  if(count($globvars['basket']['items'])) {
    // print_arv($globvars['basket']);
    ?>
    <div id="basket">
      <div id="basket_head">
        <div class="basket_image">ITEM</div>
        <div class="basket_name">PRODUCT DETAILS</div>
        <div class="basket_each">PRICE EACH</div>
        <div class="basket_quant">QUANTITY</div>
        <div class="basket_price">TOTAL PRICE</div>
      </div>
      <? 
      foreach($globvars['basket']['items'] as $b_id => $itm) { 
        if($globvars['page']['url'] == 'basket' || $itm['num'] > 0) {
          ?>
          <div class="basket_item">
            <div class="basket_image"><a title="<?= $itm['prodname'] ?>" href="<?= $itm['produrl'] ?>"><img alt="<?= $itm['prodname'] ?>" src="<?= $itm['imgsrc'] ?>"></a></div>
            <div class="basket_name">
              <?
              print "<div class=\"basket_prod\"><a title=\"{$itm['prodname']}\" href=\"{$itm['produrl']}\">{$itm['prodname']}</a></div>";
              print "<div class=\"basket_opts\">Category: {$itm['catname']}</div>";
              print "<div class=\"basket_opts\">{$itm['options']}</div>";
              if($itm['available']) {
                print "<div class=\"basket_opts\">{$itm['available']}</div>";
              }
              if($itm['ship']) {
                $ect = $itm['num'] > 1 ? ' (each)' : '';
                print "<div class=\"basket_opts\">Shipping: " . curr_disp($itm['ship'] / $itm['num']) . $ect . " <sup>1</sup></div>";
              }
              ?>
            </div>
            <div class="basket_each">
              <?
              print curr_disp($itm['price']) ; 
              if($itm['price'] < $itm['original']) {
                ?>
                <br><span class="basket_prev1"><span class="basket_prev2"><?= curr_disp($itm['original']) ?></span></span>
                <?
              }
              ?>
            </div>
            <div class="basket_quant">
              <? if($globvars['page']['url'] == 'basket') { ?>
              <span class="buttminus"><a href="<?= 'basket?b=' . $b_id . '&amp;n=-1' ?>">&ndash;</a></span><span class="bsk_quant"><?= $itm['num'] ?></span><? if($itm['stock'] > $itm['num']) { ?><span class="buttplus"><a href="<?= 'basket?b=' . $b_id . '&amp;n=1' ?>">+</a></span><? } else { ?><span class="buttblnk"></span>
              <? } } else { print $itm['num']; } ?>
            </div>
            <div class="basket_price">
              <?
              if($itm['num'] > 0) {
                print curr_disp($itm['price'] * $itm['num']);
              }
              else {
                print $itm['available'];
              }
              ?>
            </div>
          </div>
          <? 
        }
      } 
      ?>
    </div>
    <?
  } 
  else {
    ?>
    <p class="center">Your basket is empty</p>
    <?
  }
  // print_arv($globvars['basket']);
}

function postcode_check($postcode) {
  require_once 'scripts/postcodes/class.Postcode.php';
  $res = Postcode::isValidFormat($postcode);
  return $res;
}

function st_update() {
  global $globvars;
  globvars('ordref','total','currency','billingfirstname','billinglastname','billingpremise','billingstreet','billingpostcode','billingcountryiso2a');
  return jwt_encode($globvars['ordref'], $globvars['total'], $globvars['currency'], $globvars['billingfirstname'], $globvars['billinglastname'], $globvars['billingpremise'], $globvars['billingstreet'], $globvars['billingpostcode'], $globvars['billingcountryiso2a']);
}

function jwt_encode($orderreference,$baseamount,$currencyiso3a,$billingfirstname,$billinglastname,$billingpremise,$billingstreet,$billingpostcode,$billingcountryiso2a) {
  global $globvars;
  // https://github.com/firebase/php-jwt
  $key = $globvars['trustpay']['jwt_secret'];
  $jwa = [
    'iat' => time(),
    'iss' => $globvars['trustpay']['jwt_user'],
    'payload' => [
      'accounttypedescription' => 'ECOM',
      'requesttypedescriptions' => ['THREEDQUERY','AUTH'],
      'orderreference' => $orderreference,
      'baseamount' => floor($baseamount * 100),
      'currencyiso3a' => $currencyiso3a,
      'sitereference' => $globvars['trustpay']['reference'],
      'billingfirstname' => $billingfirstname,
      'billinglastname' => $billinglastname,
      'billingpremise' => $billingpremise,
      'billingstreet' => $billingstreet,
      'billingpostcode' => $billingpostcode,
      'billingcountryiso2a' => $billingcountryiso2a
    ]
  ];
  $jwa['jwt'] = JWT::encode($jwa, $key, 'HS256');
  logtable('OTHER','payment','jwt_encode',print_r($jwa,true));
  // print_arr($jwa);
  return $jwa['jwt'];
}

function jwt_decode($jwt) {
  global $globvars;
  $key = $globvars['trustpay']['jwt_secret'];
  $jwd = JWT::decode($jwt, new Key($key, 'HS256'));
  logtable('OTHER','payment','jwt_decode',print_r($jwd,true));
  // print_arv($jwd);
  return $jwd;
}

function wish_add() {
  globvars('i_id','o_id');
  global $globvars;
  $globvars['user'] = user_login();
  if($globvars['user']['res'] === true) {
    $string = "select * from `wishlist` where `u_id` = '{$globvars['user']['arr']['u_id']}' and `i_id` = '{$globvars['i_id']}' and `o_id` = '{$globvars['o_id']}'";
    $query = my_query($string);
    if(my_rows($query)) {
      $html = '<div id="poptext">Item already in your wishlist</div>';
      $html .= '<div class="buttons" id="popleftb"><a href="account">VIEW</a></div>';
      $html .= '<div class="buttons" id="poprightb"><a href="#" onclick="alert_close();return false">CLOSE</a></div>';
    }
    else {
      $string = "insert into `wishlist` set `u_id` = '{$globvars['user']['arr']['u_id']}', `i_id` = '{$globvars['i_id']}', `o_id` = '{$globvars['o_id']}', `w_date` = CURDATE()";
      $query = my_query($string);
      $html = '<div id="poptext">Item added to your wishlist</div>';
      // $html .= '<div id="poptext">' . $string . '</div>';
      $html .= '<div class="buttons" id="popleftb"><a href="account">VIEW</a></div>';
      $html .= '<div class="buttons" id="poprightb"><a href="#" onclick="alert_close();return false">CLOSE</a></div>';
    }
  }
  else {
    $html = '<div id="poptext">Please login to your account or register to add items to wishlist</div>';
    $html .= '<div class="buttons" id="popleftb"><a href="account">LOGIN</a></div>';
    $html .= '<div class="buttons" id="poprightb"><a href="#" onclick="alert_close();return false">CLOSE</a></div>';
  }
  return $html;
}

function order_find($ordref='',$u_id='') {
  $orders = [];
  $ordrefs = [];
  $string = '';
  if($ordref) {
    $string = "select * from `order_details` where `order_ref` = '{$ordref}'  limit 1";
  }
  elseif($u_id) {
    $string = "select * from `order_details` where `u_id` = '{$u_id}' order by `order_ref` DESC";
  }
  // print_p($string);
  if($string) {
    $query = my_query($string);
    if(my_rows($query)) {
      while($ord = my_assoc($query)) {
        $ordrefs[] = $ord['order_ref'];
        $status = order_status($ord);
        $ord['status'] = $status['status'];
        $ord['balance'] = $status['balance'];
        $orders[$ord['order_ref']] = $ord ;
      }
      $string1 = "select * from `order_items` where `order_ref` IN (" . implode( ",", $ordrefs ) . ")";
      $query1 = my_query($string1);
      while($itm = my_assoc($query1)) {
        $orders[$itm['order_ref']]['items'][$itm['item_ref']] = $itm ;
      }
    }
  }
  if($ordref && isset($orders[$ordref])) {
    return $orders[$ordref];
  }
  elseif($u_id && count($orders)) {
    return $orders;
  }
  else {
    return false ;
  }
}

function order_status($ord) {
  $status = '';
  $balance = $ord['cur_total'] - $ord['total_paid'] - $ord['discount'] ;
  if($balance < 0) { $balance = 0 ; }
  if($ord['cancelled'] != '0000-00-00') {
    $status = 'Order cancelled';
    $balance = 0 ;
  }
  elseif($ord['despatched'] != '0000-00-00') {
    $status = 'Order despatched on ' . cdate($ord['despatched'],'d/m/Y');
    $balance = 0 ;
  }
  elseif($ord['processing'] != '0000-00-00') {
    $status = 'Order being processed';
    $balance = 0 ;
  }
  elseif($balance > 0) {
    if(time() - strtotime($ord['order_date']) > 24 * 60 * 60) {
      $status = 'Order expired';
    }
    else {
      $status = 'Awaiting payment';
    }
  }
  else {
    $status = 'Payment made';
  }
  return ['status' => $status, 'balance' => $balance];
}

function order_email($ord,$type) {
  global $globvars;
  // print_arv($ord);

  $em_to = $ord['bill_email'];
  $em_fr = $globvars['email_fr'];
  $em_fn = $globvars['email_fn'];
  $em_bcc = $globvars['local_dev'] ? $globvars['error_to'] : $em_fr ;

  $globvars['em_vars']['order_ref'] = $ord['order_ref'] ;

  // billing address
  $globvars['em_vars']['bill_forename'] = $ord['bill_forename'] ;
  $globvars['em_vars']['bill_addr'] = $ord['bill_forename'] . ' ' . $ord['bill_surname'];
  if($ord['bill_company']) {
    $globvars['em_vars']['bill_addr'] .= '<br>' . $ord['bill_company'];
  }
  $globvars['em_vars']['bill_addr'] .= '<br>' . $ord['bill_address1'];
  if($ord['bill_address2']) {
    $globvars['em_vars']['bill_addr'] .= '<br>' . $ord['bill_address2'];
  }
  $globvars['em_vars']['bill_addr'] .= '<br>' . $ord['bill_city'];
  if($ord['bill_state']) {
    $globvars['em_vars']['bill_addr'] .= '<br>' . $ord['bill_state'];
  }
  $globvars['em_vars']['bill_addr'] .= '<br>' . $ord['bill_postcode'];
  $globvars['em_vars']['bill_addr'] .= '<br>' . $globvars['countries'][$ord['bill_country']]['sc_name'] . '<br>';
  if($ord['bill_email']) {
    $globvars['em_vars']['bill_addr'] .= '<br>E: ' . $ord['bill_email'];
  }
  if($ord['bill_phone']) {
    $globvars['em_vars']['bill_addr'] .= '<br>T: ' . $ord['bill_phone'];
  }
  if($ord['bill_mobile']) {
    $globvars['em_vars']['bill_addr'] .= '<br>M: ' . $ord['bill_mobile'];
  }

  $globvars['em_vars']['bill_email'] = $ord['bill_email'];
  $globvars['em_vars']['bill_phone'] = $ord['bill_phone'];
  $globvars['em_vars']['bill_email'] = $ord['bill_email'];

  // delivery address
  $globvars['em_vars']['deliv_addr'] = $ord['deliv_forename'] . ' ' . $ord['deliv_surname'];
  if($ord['bill_company']) {
    $globvars['em_vars']['deliv_addr'] .= '<br>' . $ord['deliv_company'];
  }
  $globvars['em_vars']['deliv_addr'] .= '<br>' . $ord['deliv_address1'];
  if($ord['bill_address2']) {
    $globvars['em_vars']['deliv_addr'] .= '<br>' . $ord['deliv_address2'];
  }
  $globvars['em_vars']['deliv_addr'] .= '<br>' . $ord['deliv_city'];
  if($ord['bill_state']) {
    $globvars['em_vars']['deliv_addr'] .= '<br>' . $ord['deliv_state'];
  }
  $globvars['em_vars']['deliv_addr'] .= '<br>' . $ord['deliv_postcode'];
  $globvars['em_vars']['deliv_addr'] .= '<br>' . $globvars['countries'][$ord['deliv_country']]['sc_name'] . '<br>';

  $globvars['em_vars']['cur_code'] = $ord['cur_code'] ;

  $globvars['em_vars']['vdisc_code'] = $ord['vdisc_code'] ;
  $globvars['em_vars']['cur_voucher'] = $ord['cur_voucher'] ;
  
  $globvars['em_vars']['ship_text'] = trim($ord['ship_text']) ;
  $globvars['em_vars']['cur_ship'] = $ord['cur_ship'] ;

  $globvars['em_vars']['cur_net'] = $ord['cur_net'] ;
  $globvars['em_vars']['cur_vat'] = $ord['cur_vat'] ;
  $globvars['em_vars']['cur_gross'] = $ord['cur_gross'] ;

  $globvars['em_vars']['tracking'] = $ord['s_tracking'] ;

  foreach($ord['items'] as $itn => $itm) {
    $globvars['em_vars']['items'][$itn]['prodname'] = $itm['prodname'] ;
    $globvars['em_vars']['items'][$itn]['options'] = $itm['options'] ;
    $globvars['em_vars']['items'][$itn]['price'] = $itm['price'] * $ord['cur_rate'] ;
    $globvars['em_vars']['items'][$itn]['quantity'] = $itm['quantity'] ;
    $globvars['em_vars']['items'][$itn]['available'] = $itm['available'] ;
  }
  // print_arv($globvars['em_vars']);

  if($type == 'order') {
    $subject = "{$globvars['comp_name']} order received {$ord['order_ref']}";
    make_email('control/email_order.html', $em_to, $em_fr, $em_fn, $subject, '', '', $em_bcc);
  }
  elseif($type == 'despatch') {
    $subject = "{$globvars['comp_name']} order despatched {$ord['order_ref']}";
    make_email('control/email_despatch.html', $em_to, $em_fr, $em_fn, $subject, '', '', $em_bcc);
  }

}

function video_arr($id) {
  $out = [] ;
  if($id) {
    $string = "select * from `video_map` where `id` = '$id' limit 1";
    $query = my_query($string);
    if(my_rows($query)) {
      $row = my_assoc($query);
      if($row['vidfile']) {
        $out['file'] = 'videos/file/' . $row['vidfile'];
        $out['thumb'] = $row['vidthm'] ? 'videos/thm/' . $row['vidthm'] : '';
        $out['type'] = 'mp4';
        $out['prop'] = $row['proportion'];
      }
      elseif($row['exturl']) {
        $out['file'] = $row['exturl'];
        $out['thumb'] = $row['exthm'];
        $params = video_params($row['exturl']);
        $out = array_merge($out,$params);
        $out['prop'] = $row['proportion'];
      }
    }
  }
  return $out ;
}

function video_url($vidref,$vidtype) {
  $out = false;
  if($vidref) {
    if($vidtype == 'vimeo') {
      $out['play'] = 'https://player.vimeo.com/video/' . $vidref;
      $out['page'] = 'https://vimeo.com/' . $vidref;
    }
    elseif($vidtype == 'youtube') {
      $out['play'] = 'https://www.youtube.com/embed/' . $vidref; 
      $out['page'] = 'https://www.youtube.com/watch?v=' . $vidref; 
    }
  }
  return $out ;
}

function sprint_make_order($ord) {
  global $globvars ;
  $out = ['ref'=>'','status'=>'Syntax error','response'=>[]];
  if(! $globvars['sprint_auth']) { return $out ; }
  $items = '';
  // print_arv($ord);

  foreach($ord['items'] as $item) {
    $items .= '      {"SKU" : "' . $item['sku'] . '", "Quantity" : "' . $item['quantity'] . '"}, ' . "\r\n";
  }
  $items = substr(trim($items), 0, -1 );

  $request = str_replace("'","\'",'{ 
    "AttnOf" : "' . $ord['deliv_forename'] . ' ' . $ord['deliv_surname'] . '", 
    "Telephone" : "' . ($ord['bill_phone'] ? $ord['bill_phone'] : $ord['bill_mobile']) . '", 
    "EmailAddr" : "' . $ord['bill_email'] . '", 
    "CompanyName" : "' . $ord['deliv_company'] . '", 
    "Address1" : "' . $ord['deliv_address1'] . '", 
    "Address2" : "' . $ord['deliv_address2'] . '", 
    "City" : "' . $ord['deliv_city'] . '", 
    "State" : "' . $ord['deliv_state'] . '", 
    "PostCode" : "' . $ord['deliv_postcode'] . '", 
    "CountryCode" : "' . $ord['deliv_country'] . '", 
    "CustomerRef1" : "' . $ord['order_ref'] . '", 
    "StockOrderItems" : [
      ' . $items . '
    ] 
  }');
  // print_pre($request);

  $ch = curl_init();
  $apiurl = 'https://api.sprintlogistics.com/api/orders';
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
    $out['status'] = 'No response';
  }
  elseif(curl_error($ch)) {
    $out['status'] = curl_error($ch);
  }
  else {
    $arr = objectToArray(json_decode($result));
    if(isset($arr['Status'])) {
      if($arr['Status']) {
        $out['response'] = addslashes(print_r($result,true));
        $out['status'] = 'ok';
        $out['ref'] = $arr['Result']['AWB'];
      }
      elseif(isset($arr['Message']) && $arr['Message']) {
        $out['status'] = $arr['Message'];
      }
    }
  }
  if($out['status'] != 'ok') {
    htmlmail($globvars['email_to'], $globvars['email_fr'], $globvars['email_fn'], "Sprint Error - Order {$ord['order_ref']}", $out['status']);
  }
  curl_close($ch);
  return $out ;
}

function sprint_get_products() {
  global $globvars;
  $out = [];
  if(! $globvars['sprint_auth']) { return $out ; }

  $ch = curl_init();
  $apiurl = 'https://api.sprintlogistics.com/api/product/?pageSize=10000';
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
      'Authorization: Basic ' . $globvars['sprint_live']
    ),
  ));

  $result = curl_exec($ch);
  if(curl_error($ch)) {
    print_arr(curl_error($ch),'sprint_get_products_error');
  }
  curl_close($ch);
  $out = objectToArray(json_decode($result));
  // print_arv($out,'sprint_get_products_result');
  return $out ;
}

function sprint_get_order($order_ref='',$sprint_ref='') {
  global $globvars;
  $out = ['order_ref'=>$order_ref,'sprint_ref'=>$sprint_ref,'status'=>'','tracklink'=>'','order'=>[],'tracking'=>[]];
  if(! ($order_ref && $sprint_ref && $globvars['sprint_auth'])) { return $out ; }

  $ch = curl_init();
  $apiurl = 'https://api.sprintlogistics.com/api/orders/' . $sprint_ref;
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
  if($result && ! curl_error($ch)) {
    $arr = objectToArray(json_decode($result));
    // print_arv($arr);
    if(isset($arr['Status']) && $arr['Status'] && isset($arr['Result']['Status'])) {
      $out['status'] = $arr['Result']['Status'];
      $out['order'] = $arr;
      $out['tracking'] = sprint_get_tracking($sprint_ref);
      $out['tracklink'] = isset($out['tracking']['Result']['TrackingLink']) ? $out['tracking']['Result']['TrackingLink'] : '';
    }
  }
  curl_close($ch);
  return $out ;
}

function sprint_get_tracking($sprint_ref='') {
  global $globvars;
  $out = '';
  if(! ($sprint_ref && $globvars['sprint_auth'])) { return $out ; }

  $ch = curl_init();
  $apiurl = 'https://api.sprintlogistics.com/api/shipment/tracking?HAWB=' . $sprint_ref;
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
  if($result && ! curl_error($ch)) {
    $out = objectToArray(json_decode($result));
  }
  curl_close($ch);
  return $out ;
}
?>