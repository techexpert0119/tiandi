<?
function head0() {
  global $globvars;
}

function head1() {
  global $globvars;
  $s = $globvars['pages_main'][$globvars['main_root']['pages_blog']];
  $globvars['page']['head2'] = $s['head2'];
  $globvars['page']['img_main'] = $s['img_main'];
  $globvars['page']['img_mob'] = $s['img_mob'];
  $globvars['page']['banner1'] = $s['banner1'];
  $globvars['page']['banner2'] = $s['banner2'];
  $globvars['page']['banneri'] = $s['banneri'];
  $globvars['page']['banner1c'] = $s['banner1c'];
  // print_arv($globvars['page']);
  $u = explode("/", $globvars['page']['url']);
  // print_arv($u);
  // print_arv($s);
  $globvars['page']['subs'] = $globvars['blog_all'];
  if(isset($s['subs'])) {
    // cats
    $globvars['page']['cats'] = $s['subs'];
    if(isset($u[1]) && isset($s['subs'][$u[1]])) {
      // cat
      $globvars['page']['cat'] = $s['subs'][$u[1]];
      if(isset($s['subs'][$u[1]]['subs'])) {
        // articles
        $globvars['page']['subs'] = $s['subs'][$u[1]]['subs'];
        if(isset($u[2]) && isset($s['subs'][$u[1]]['subs'][$u[2]])) {
          // article
          $globvars['page']['article'] = $s['subs'][$u[1]]['subs'][$u[2]];
          // stack
          $globvars['page']['stack'] = $ord = array();
          foreach($globvars['blog_stack'] as $table) {
            $string1 = "SELECT * FROM `blog_{$table}` WHERE `m_id` = '{$globvars['page']['article']['id']}'";
            // print_p($string1);
            $query1 = my_query($string1);
            while($a_row = my_array($query1)) {
              $a_row['table'] = $table;
              $globvars['page']['stack'][] = $a_row;
              $ord[] = $a_row['order'];
            }
          }
          array_multisort($ord,$globvars['page']['stack']);
        }
      }
    }
  }
  google_analytics();
}

function head2() {
  global $globvars;
  /*
  if(isset($globvars['page']['article']) && count($globvars['page']['article'])) {
    ?>
    <script type="text/javascript" src="https://platform-api.sharethis.com/js/sharethis.js#property=6475ca24413e9c001905a388&product=inline-share-buttons&source=platform" async="async"></script>
    <?
  }
  */
}

function body() {
  global $globvars;
  body_top();
  ?>
  <div id="main">
    <? body_content(); ?>
  </div>
  <? 
  body_foot();
}

function body_content() {
  global $globvars;
  $filepath = 'images/blog/article';
  body_image();
  ?>
  <div id="content">
    <div class="maxwid divpad">
      <?
      if(isset($globvars['page']['article']) && count($globvars['page']['article'])) {
        // display entry
        // print_arv($globvars['page']);
        ?>
        <div id="blog_articles">
          <div id="blog_social"><? /* ?><div class="sharethis-inline-share-buttons"></div><? */ ?></div>
          <div id="blog_bread">
          <? $b = 0 ; foreach($globvars['page']['bread'] as $u => $p) { if($b++) { print '/'; } ?>
          <a href="<?= $u ?>"><?= $p ?></a>
          <? } ?>
          </div>
          <h1><?= clean_upper($globvars['page']['head']) ; ?></h1>
          <div id="blog_date">
          <?
          // print_arv($globvars['page']);
          if($globvars['page']['article']['date'] != '0000-00-00') {
            print '// &nbsp;' . cdate($globvars['page']['article']['date'],'d.m.y',' ') . '&nbsp; //' ; 
          }
          /*
          if($globvars['page']['article']['author']) {
            print ' by ' . $globvars['page']['article']['author'];
          }
          */
          ?>
          </div>
          <?
          // print_arv($globvars['page']['stack'],'stack');
          foreach($globvars['page']['stack'] as $itm) { 
            $table = $itm['table'];
            if($table == 'head') {
              ?>
              <h2><?= $itm['text'] ?></h2>
              <?
            }
            elseif($table == 'image') {
              if($itm['image'] && file_exists($img = build_path($filepath,$itm['image'])) && $img_arr = get_image($img)) {
                if(substr_count($itm['link'],'www.') && ! substr_count($itm['link'],'http') ) { $itm['link'] = 'http://' . $itm['link'] ; }
                ?>
                <div class="blog_image">
                  <a href="<?= $itm['link'] ? $itm['link'] : '#' ; ?>" title="<?= $itm['caption']; ?>">
                    <IMG SRC="<?= $img_arr['src']; ?>" title="<?= $itm['caption']; ?>" ALT="<?= $itm['caption']; ?>">
                  </A>
                  <? if($itm['caption']) { ?>
                  <div class="blog_caption"><?= $itm['caption']; ?></div>
                  <? } ?>
                </div>
                <?
              }
            }
            elseif($table == 'vimeo') {
              if($itm['code']) {
                $oembed = 'https://vimeo.com/api/oembed.json?url=' . urlencode("https://player.vimeo.com/video/{$itm['code']}");
                $headers = get_headers($oembed);
                if(isset($headers[0]) && substr($headers[0], 9, 3) == "200") {
                  $json = json_decode(file_get_contents($oembed),true);
                  // print_arr($json) ;        
                  if(isset($json['width']) && $json['width'] && isset($json['height']) && $json['height']) {
                    $pad = round($json['height'] / $json['width'] * 100,2);
                    // print $pad ;          
                    ?>
                    <div class="blog_video">
                      <div style="position:relative; text-align:center; padding-bottom:<?= $pad ?>%; height:0; overflow:hidden; max-width:100%; height:auto;">
                        <iframe style="position:absolute; top:0; left:0; width:100%; height:100%; border:0;" src="https://player.vimeo.com/video/<?= $itm['code'] ?>"></iframe> 
                      </div>
                      <? if($itm['caption']) { ?>
                        <div class="blog_caption"><?= $itm['caption']; ?></div>
                      <? } ?>
                    </div>
                    <?
                  }
                }
              }
            }
            elseif($table == 'youtube') {
              if($itm['code']) {
                $oembed = 'https://www.youtube.com/oembed?url=' . urlencode("https://www.youtube.com/watch?v={$itm['code']}");
                $headers = get_headers($oembed);
                if(isset($headers[0]) && substr($headers[0], 9, 3) == "200") {
                  $json = json_decode(file_get_contents($oembed),true);
                  // print_arr($json) ;        
                  if(isset($json['width']) && $json['width'] && isset($json['height']) && $json['height']) {
                    $pad = round($json['height'] / $json['width'] * 100,2);
                    // print $pad ;
                    ?>
                    <div class="blog_video">
                      <div style="position:relative; text-align:center; padding-bottom:<?= $pad ?>%; height:0; overflow:hidden; max-width:100%; height:auto;">
                        <iframe style="position:absolute; top:0; left:0; width:100%; height:100%; border:0;" src="https://www.youtube.com/embed/<?= $itm['code'] ?>"></iframe>
                      </div>
                      <? if($itm['caption']) { ?>
                        <div class="blog_caption"><?= $itm['caption']; ?></div>
                      <? } ?>
                    </div>
                    <?
                  }
                }
              }
            }
            elseif($table == 'link') {
              if(substr_count($itm['link'],'www.') && ! substr_count($itm['link'],'http') ) { 
                $itm['link'] = 'http://' . $itm['link'] ; 
              }
              if($itm['link']) {
                ?>
                <div class="blog_link"><a href="<?= $itm['link']; ?>" title="<?= $itm['text'] ?>"><?= $itm['text'] ? $itm['text'] : $itm['link'] ; ?></A></div>
                <?
              }
            }
            elseif($table == 'html') {
              ?>
              <div class="blog_html"><?= dispc($itm['html']) ?></div>
              <?
            }          
            elseif(isset($itm['text'])) {
              ?>
              <div class="blog_html"><?= $itm['text'] ?></div>
              <?
            }          
          } 
          ?>
          <div id="blog_end"><img src="images/blogline.png"></div>
          <?
          // print_arv($globvars['page']);
          // print_arv($globvars['blog_all']);
          $others['l'] = $others['n'] = $others['t'] = $others['p'] = $others['f'] = '' ;
          foreach($globvars['blog_all'] as $k => $a) {
            $others['l'] = $k;
            if(! $others['f']) {
              $others['f'] = $k;
            }
            if($others['t'] && ! $others['n']) {
              $others['n'] = $k ;
            }
            if($a['url'] == $globvars['page']['url']) {
              $others['t'] = $k ;
            }
            if(! $others['t']) {
              $others['p'] = $k;
            }
            if($others['n'] && $others['p']) {
              break ;
            }
          }
          if(! $others['n']) {
            $others['n'] = $others['f'] ;
          }
          if(! $others['p']) {
            $others['p'] = $others['l'] ;
          }
          // print_arr($others);
          if($others['n'] || $others['p']) {
            ?>
            <div id="blog_others">
              <? if($others['p']) { ?>
              <a href="<?= $globvars['blog_all'][$others['p']]['url'] ?>" title="<?= $globvars['blog_all'][$others['p']]['head'] ?>">
                <span class="blog_other h2">
                  <img src="<?= 'images/blog/intro/' . $globvars['blog_all'][$others['p']]['img_grid'] ?>">
                  <?= $globvars['blog_all'][$others['p']]['head'] ?>
                </span>
              </a>
              <? } if($others['n'] && ($globvars['blog_all'][$others['p']]['url'] != $globvars['blog_all'][$others['n']]['url'])) { ?>
              <a href="<?= $globvars['blog_all'][$others['n']]['url'] ?>" title="<?= $globvars['blog_all'][$others['n']]['head'] ?>">
                <span class="blog_other h2">
                  <img src="<?= 'images/blog/intro/' . $globvars['blog_all'][$others['n']]['img_grid'] ?>">
                  <?= $globvars['blog_all'][$others['n']]['head'] ?>
                </span>
              </a>
              <? } ?>
            </div>
            <?
          }
          ?>
        </div>
        <?
      }
      else {
        ?>
        <h2><?= clean_upper($globvars['page']['head1']) ; ?></h2>
        <? if(isset($globvars['page']['html1']) && $globvars['page']['html1']) { ?>
        <div><?= dispc($globvars['page']['html1']); ?></div>
        <?
        }
        // categories
        if(isset($globvars['page']['cats'])) {
          ?>
          <div id="blog_cats">
          <div id="blog_catsi">
            <span class="blog_cat"><a href="news" style="<?= ! isset($globvars['page']['cat']['id']) ? 'color:#0C364A;' : '' ?>">ALL</a></span>
            <?
            $n = 0 ; 
            foreach($globvars['page']['cats'] as $cat) {
              ?>
              <span class="blog_catsp">//</span><span class="blog_cat"><a style="<?= isset($globvars['page']['cat']['id']) && ( $globvars['page']['cat']['id'] == $cat['b_id']) ? 'color:#0C364A;' : '' ?>" href="<?= $cat['url'] ; ?>"><?= $cat['head1'] ; ?></a></span>
              <?
            }
            ?>
          </div>
          </div>
          <?
        }
        // list articles
        if(isset($globvars['page']['subs'])) {
          ?>
          <div id="blog_table">
            <div id="blog_row">
              <div id="blog_left">
                <? blog_items('left') ; ?>
              </div>
              <div id="blog_right">
                <? blog_items('right') ; ?>
              </div>
            </div>
          </div>
          <?
        }
      }
      ?>
    </div> 
  </div>
  <?
}

function blog_items($side) {
  global $globvars;
  $n = 0 ;
  foreach($globvars['page']['subs'] as $article) {
    $n++;
    $cls = ($n/2 == floor($n/2)) ? 'blog_even' : 'blog_odd';
    ?>
    <a href="<?= $article['url']; ?>" title="<?= $article['head']; ?>">
      <span class="blog_item <?= $cls ?>">
        <span class="blog_itemh">
          <?
          if($side == 'right') {
            ?>
            <span class="line"></span><span class="blob"></span><span class="h2"><?= $article['head']; ?></span>
            <?
          }
          ?>
          <?
          if($side == 'left') {
            ?>
            <span class="linetable">
              <span class="linecell h2"><?= $article['head']; ?></span>
              <span class="linecell hb"><span class="blob"></span></span>
              <span class="linecell hl"><span class="line"></span></span>
            </span>
            <?
          }
          ?>
        </span>
        <span class="blog_itemd">
          <b><?= $article['head1'] ?></b> <span>&nbsp;//&nbsp;</span> <?= cdate($article['date'],'d.m.y',' '); ?>
        </span>
        <span class="blog_itemi">
          <img src="<?= 'images/blog/intro/' . $article['img_grid'] ; ?>">
        </span>
        <span class="blog_itemt">
          <?= $article['intro']; ?>
        </span>
      </span>
    </a>
    <?
  }
}
?>