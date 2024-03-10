<?
function head0() {
}

function head1() {
  global $globvars;
  ?>
  <script type="application/ld+json">
  {
    "@context": "http://schema.org",
    "@type": "Organization",
    "url": "<?= $globvars['sm_url'] ?>",
    "logo": "<?= $globvars['comp_logo'] ?>",
    "contactPoint" : [{
      "@type" : "ContactPoint",
      "telephone" : "<?= $globvars['comp_phonei'] ?>",
      "contactType" : "customer service",
      "availableLanguage" : "English"
    }]
  }
  </script>
  <?
  get_components();
  google_analytics();
}

function head2() {
  global $globvars;
  if(! substr_count($globvars['page']['class'],'menutrans')) {
    $globvars['page']['class'] .= ' menutrans';
  }
}

function body() {
  global $globvars;
  ?>
  <div id="main">
    <? body_top(); ?>
    <div id="home_slider">
      <? cms_edit('control/models.php','models.php','z-index:9999') ?>
      <div class="slide_set" data-method="slideleft" data-interval="8000">
        <?
        $n = 0 ;
        foreach($globvars['systems'] as $system) {
          $model = $system['model'];
          $m_slide_title1 = str_replace("\r\n",'<br>',str_replace(']','</span>',str_replace('[','<span style="color:' . $model['m_colour_light'] . '">',$model['m_slide_title1'])));
          $img_main = 'images/product/slide/main/' . $model['m_slide_main'];
          if(file_exists($img_main)) {
            $img_mob = $model['m_slide_mobile'] ? 'images/product/slide/mob/' . $model['m_slide_mobile'] : $img_main;
            ?>
            <style type="text/css">
              #slide_img_<?= $n ?> { background-image:url('<?= $img_main ?>'); } 
              @media only screen and (max-width: 540px) { 
                #slide_img_<?= $n ?> { background-image:url('<?= $img_mob ?>'); } 
              }
            </style>
              <div id="slide_img_<?= $n ?>" class="slide_img" style="<?= $n ? 'display:none' : '' ?>">
                <div class="slide_table maxwid">
                  <div class="slide_cell">
                    <div class="slide_title1">
                      <div class="slide_symb">
                        <img src="<?= 'images/product/symb/slide/' . $model['m_symbol_slide'] ?>">
                      </div>
                      <?= $m_slide_title1 ?>
                    </div>
                    <div class="slide_title2">
                      <?= $model['m_slide_title2'] ?>
                    </div>
                  </div>
                </div>
              </div>
            <?
          $n++; 
          }
        }
        ?>
      </div>
    </div>

    <div id="content">
      <div id="content1">
        <? cms_edit(); ?>
        <div class="maxwid divpad">
          <?
          makesitemap();
          body_html(1);
          // print_arr($globvars['systems']);
          ?>
        </div>
      </div>

      <div id="modelstrip">
        <div id="modelhead" class="maxwid">
          <? body_html(2); ?>
        </div>
        <div id="modelstrip1">
          <? cms_edit('control/models.php','models.php','top:40px') ?>
          <div id="modelline" class="maxwid"><div></div></div>
          <div id="modelstrip2" class="maxwid">
            <? 
            foreach($globvars['systems'] as $system) { 
              $model = $system['model'];
              ?><div class="mmodel">
                  <div class="mimage" data-back="<?= $model['m_colour_light'] ?>" data-hover="<?= $model['m_colour_dark'] ?>">
                  <a title="<?= $model['q_menu'] ?>" href="<?= $model['p_url'] . '/' . $model['q_url'] ?>">
                    <span class="mcircle" style="background-color:<?= $model['m_colour_light'] ?>"></span>
                    <span class="micon"><img src="<?= 'images/product/symb/slide/' . $model['m_symbol_slide'] ?>"></span>
                    <span class="mblank"><img src="images/blank.png"></span>
                  </a>
                </div>
                <div class="mname"><?= $model['q_menu'] ?></div>
                <div class="mtag"><?= $model['m_tagline'] ?></div>
              </div><? } ?>        
          </div>
        </div>
      </div>

      <? if(count($globvars['components'])) { ?>
      <div id="components">
        <? cms_edit('','','') ?>
        <div id="comphead" class="maxwid">
          <h2><?= $globvars['page']['head3'] ; ?></h2>
          <div class="maxtext"><?= dispc($globvars['page']['html3']); ?></div>
        </div>
        <div id="components1">
          <? cms_edit('control/components.php','components.php','top:-5px') ?>
          <div class="maxwid" id="comptable">
            <? 
            // print_arr($globvars['components']);
            foreach($globvars['components'] as $component) { 
              ?>
              <div class="component">
                <div class="comptext">
                  <h3><?= $component['c_title'] ?></h3>
                  <? if($component['c_image']) { ?>
                  <div class="compimage"><img src="<?= 'images/product/comp/' . $component['c_image'] ?>"></div>
                  <? } ?>
                  <?= $component['c_html'] ?>
                </div>
                <div class="compright">
                  <? if($component['c_image']) { ?>
                    <img src="<?= 'images/product/comp/' . $component['c_image'] ?>">
                  <? } ?>
                </div>
              </div>
            <? } ?>
          </div>
        </div>
      </div>
      <? } 
      if($globvars['page']['butturl3'] && $globvars['page']['button3']) { 
        ?>
        <div class="htmlbutt button"><a href="<?= $globvars['page']['butturl3']; ?>" target="<?= strpos($globvars['page']['butturl3'], "http") === 0 ? '_blank' : '' ?>"><?= $globvars['page']['button3']; ?></a></div><br>
        <? 
      }
      ?>
    </div>
  </div>
  <? 
  body_foot();
}
?>