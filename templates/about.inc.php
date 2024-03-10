<?
function head0() {
}

function head1() {
  global $globvars;
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
  body_top();
  ?>
  <div id="main">
    <? 
    $footbanner = $globvars['page']['banner2'];
    $globvars['page']['banner2'] = '';
    body_image(); 
    ?>
    <div id="content">
      <? cms_edit(); 
      if($globvars['page']['html1']) { ?>
      <div id="content1">
        <div class="maxwid divpad">
          <? body_html(1); ?>
        </div>
      </div>
      <? } if($globvars['page']['html2']) { ?>
      <div id="content2">
        <div class="maxwid divpad">
          <? body_html(2); ?>
        </div>
      </div>
      <? } if($globvars['page']['html3']) { ?>
      <div id="content3">
        <div class="maxwid divpad">
          <? body_html(3); ?>
        </div>
      </div>
      <? } ?>

      <div id="modelstrip">
        <div id="modelhead" class="maxwid">
          <h2><?= $globvars['pages_main']['']['head2'] ?></h2>
        </div>
        <div id="modelstrip1">
          <? cms_edit('control/models.php','models.php'); ?>
          <div id="modelline" class="maxwid"><div></div></div>
          <div id="modelstrip2" class="maxwid">
            <? 
            foreach($globvars['systems'] as $system) { 
              $model = $system['model'];
              ?><div class="mmodel">
                <? // print_arr($model); ?>
                  <div class="mimage" data-back="<?= $model['m_colour_light'] ?>" data-hover="<?= $model['m_colour_dark'] ?>">
                  <a title="<?= $model['q_menu'] ?>" href="<?= $model['p_url'] . '/' . $model['q_url'] ?>">
                    <span class="mcircle" style="background-color:<?= $model['m_colour_light'] ?>"></span>
                    <span class="micon"><img src="<?= 'images/product/symb/slide/' . $model['m_symbol_slide'] ?>"></span>
                    <span class="mblank"><img src="images/blank.png"></span>
                  </a>
                </div>
                <div class="mname"><?= $model['q_menu'] ?></div>
                <div class="mtag"><?= $model['m_tagline'] ?></div>
                <div class="mtext"><?= $model['m_html_about'] ?></div>
              </div><? } ?>
          </div>
        </div>
      </div>

      <div id="modeldev1">
        <div id="modeldev2">
          <img src="images/logo_small.png"><?= $footbanner ?>
        </div>
      </div>
    </div>


  </div>
  <? 
  body_foot();
}
?>