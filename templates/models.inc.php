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
  $model = $globvars['systems'][$globvars['page']['q_id']];
  $globvars['page']['banner1c'] = $model['model']['m_colour_light'];
  body_top();
  ?>
  <div id="main">
    <? body_image(); ?>
    <div id="content" style="background-color:<?= $model['model']['m_colour_dark'] ?>">
      <? cms_edit(); ?>
      <div class="maxwid divpad">
        <?
        body_html(1);
        body_html(2);
        // print_arr($globvars['page']);
        // print_arr($model);
        ?>
      </div>
    </div>

    <? if(isset($model['products']) && count($model['products'])) { ?>
    <div id="modproducts">
      <? 
      // print_arv($globvars['systems']);
      foreach($model['products'] as $product) { ?>
        <div class="modproduct">
          <? cms_edit('control/products.php?action=edit&amp;go=' . $product['r_id'],'products.php'); ?>
          <div class="maxwid divpad">
            <div class="modprodtable">
              <div class="modprodleft">
                <h2><?= $product['s_intro_head1']; ?></h2>
                <div class="modprodhtml"><?= $product['s_intro_html']; ?></div>
                <? if($product['s_intro_image']) { ?>
                <div class="modprodimg"><img src="<?= 'images/product/intro/' . $product['s_intro_image']; ?>" alt="<?= $product['s_intro_head1']; ?>"></div>
                <? } if($product['s_intro_button']) { ?>
                <div class="button"><a href="<?= $model['model']['p_url'] . '/' . $model['model']['q_url'] . '/' . $product['r_url'] ; ?>"><?= $product['s_intro_button']; ?></a></div>
                <? } ?>
              </div>
              <div class="modprodright">
                <? if($product['s_intro_image']) { ?>
                <img src="<?= 'images/product/intro/' . $product['s_intro_image']; ?>" alt="<?= $product['s_intro_head1']; ?>">
                <? } ?>
              </div>
            </div>
          </div>
        </div>
      <? } ?>
    </div>
    <? } ?>

    <? 
    $globvars['components'] = [];
    for($i=1;$i<=5;$i++) {
      if($model['model']['m_comp_title'.$i]) {
        $globvars['components'][$i]['c_title'] = $model['model']['m_comp_title'.$i];
        $globvars['components'][$i]['c_html'] = $model['model']['m_comp_html'.$i];
        $globvars['components'][$i]['c_image'] = $model['model']['m_comp_image'.$i];
      }
    }
    if(count($globvars['components'])) { ?>

    <div id="components">
      <? cms_edit('control/pages_subp.php?action=edit&amp;go=' . $model['model']['m_id'],'pages_subp.php'); ?>
      <div id="comphead" class="maxwid">
        <? body_html(3); ?>
      </div>
      <div id="components1">
        <? cms_edit('control/models.php?action=edit&amp;go=' . $model['model']['m_id'],'models.php','top:-5px'); ?>
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

  </div>
  <? 
  body_foot();
}
?>