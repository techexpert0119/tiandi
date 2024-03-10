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
  // print_arv($globvars['page']);
  // print_arv($globvars['systems']);
  $model = $globvars['systems'][$globvars['page']['q_id']];
  $product = $globvars['systems'][$globvars['page']['q_id']]['products'][$globvars['page']['r_id']];
  $globvars['page']['banner1c'] = $model['model']['m_colour_light'];
  body_top();
  ?>
  <div id="main">
    <? body_image(); ?>
    <div id="content" style="background-color:<?= $model['model']['m_colour_dark'] ?>">
      <? cms_edit(); ?>
      <div class="maxwid divpad">
        <?
        body_html(1,'h1');
        body_html(2);
        body_html(3);
        // print_arr($globvars['page']);
        // print_arr($product);
        ?>
      </div>
    </div>

    <div id="prodhead">
      <? cms_edit('control/products.php?action=edit&amp;go=' . $product['r_id'],'products.php',''); ?>
      <h1><?= $product['s_detail_head'] ?></h1>
    </div>

    <div id="prodcols" class="maxwid divpad">
      <? for($i=1;$i<=3;$i++) { ?>
      <div class="prodcol"><?= $product['s_detail_col_html'.$i] ?></div>
      <? } ?>
    </div>

    <div id="prodintro">
      <? cms_edit('control/products.php?action=edit&amp;go=' . $product['r_id'],'products.php'); ?>
      <div class="maxwid divpad">
        <div id="proditext"><?= $product['s_detail_html'] ?></div>
        <div id="prodimage">
          <?
          $f = 'images/product/detail/' . $product['s_detail_image'];
          if($img = get_image($f)) {
            $w = $img['width'];
            $h = $img['height'];
            $p = $w / $h ;
            ?>
            <img src="<?= $f ?>" alt="<?= $product['s_detail_head'] ?>">
            <?
            for($i=1;$i<=6;$i++) { 
              $product['s_specs_title'.$i] = clean_upper($product['s_specs_title'.$i]);
              if($product['s_specs_title'.$i] && $product['s_specs_xpos'.$i] && $product['s_specs_ypos'.$i]) { 
                $x = ($product['s_specs_xpos'.$i] - (49/2) - 3) / $w * 100 ;
                $y = ($product['s_specs_ypos'.$i] - 70 - 3) / $h * 100 ;
                ?>
              <div class="prodpoint" style="top:<?= $y ?>%; left:<?= $x ?>%"><a href="#" title="<?= $product['s_specs_title'.$i] ?>" onclick="return prodspec(<?= $i ?>);"><img src="images/expand_icon.png" alt="<?= $product['s_specs_title'.$i] ?>"></a></div>
          <? } } } ?>
        </div>
      </div>
    </div>

    <div id="prodicons">
      <? 
      cms_edit('control/products.php?action=edit&amp;go=' . $product['r_id'],'products.php'); 
      for($i=1;$i<=4;$i++) { ?>
      <div class="prodicon">
        <img src="<?= 'images/product/icon/' . $product['s_icon_img'.$i] ?>" alt="<?= clean_upper($product['s_icon_text'.$i]) ?>">
        <div class="proditext"><?= clean_upper($product['s_icon_text'.$i]) ?></div>
        <div class="prodidata"><?= $product['s_icon_data'.$i] ?></div>
      </div>
      <? } ?>
    </div>

    <div id="prodspecs">
      <? cms_edit('control/products.php?action=edit&amp;go=' . $product['r_id'],'products.php','top:-35px'); ?>
      <div class="maxwid">
        <? 
        for($i=1;$i<=6;$i++) { 
          if($product['s_specs_title'.$i]) {
            $product['s_specs_title'.$i] = clean_upper($product['s_specs_title'.$i]);
            ?>
            <div class="prodspec">
              <div class="prodsmag"><a href="#" onclick="return prodspec(<?= $i ?>);" title="<?= $product['s_specs_title'.$i] ?>"><img src="images/magnify.png"></a></div>
              <h3><?= $product['s_specs_title'.$i] ?></h3>
              <? if($product['s_specs_image'.$i]) { ?>
              <div class="prodsimage"><img src="<?= 'images/product/specs/' . $product['s_specs_image'.$i] ?>" alt="<?= $product['s_specs_title'.$i] ?>"></div>
              <? } ?>
              <div class="prodstable">
              <?
              $data = explode("\r\n",$product['s_specs_data'.$i]);
              foreach($data as $line) {
                if($line) {
                  $parts = explode(":",$line);
                  if(count($parts) == 2) {
                    ?>
                    <div class="prodsrow">
                       <div class="prodscell"><?= $parts[0] ?>:</div>
                       <div class="prodscell"><?= $parts[1] ?></div>
                    </div>
                    <?
                  }
                }
              }
              ?>
              </div>
            </div>
        <? } } ?>
      </div>
    </div>

    <? if(($product['s_pdf_spec_name'] && $product['s_pdf_spec_file']) || ($product['s_pdf_cert_name'] && $product['s_pdf_cert_file'])) { ?>
    <div id="prodpdfs" class="maxwid">
      <? if($product['s_pdf_spec_name'] && $product['s_pdf_spec_file']) { ?>
        <div class="prodpdf">
          <a target="_blank" href="<?= 'images/product/pdf/' . $product['s_pdf_spec_file'] ?>"><img src="images/pdf.png"><?= $product['s_pdf_spec_name'] ?></a>
        </div>
      <? } if($product['s_pdf_cert_name'] && $product['s_pdf_cert_file']) { ?>
        <div class="prodpdf">
          <a target="_blank" href="<?= 'images/product/pdf/' . $product['s_pdf_cert_file'] ?>"><img src="images/pdf.png"><?= $product['s_pdf_cert_name'] ?></a>
        </div>
      <? } ?>
    </div>
    <? } 
    
    for($i=1;$i<=6;$i++) { 
      if($product['s_specs_title'.$i]) {
        $product['s_specs_title'.$i] = clean_upper($product['s_specs_title'.$i]);
        ?>
        <div class="prodpopouter" id="porodpop<?= $i ?>">
          <div class="prodpopinner">
            <div class="prodpophead" style="background-color:<?= $model['model']['m_colour_dark'] ?>">
              <div class="prodpopclose"><a href="#" onclick="return prodspec()"><img src="images/mob_menuc.png" width="30"></a></div>
              <?= $globvars['page']['banner1'] ?>
            </div>
            <div class="prodpopcont">
              <div class="prodpopimg"><? if($product['s_specs_image'.$i]) { ?><img src="<?= 'images/product/specs/' . $product['s_specs_image'.$i] ?>" alt="<?= $product['s_specs_title'.$i] ?>"><? } ?></div>
              <div class="prodspec">
                <div class="h3"><?= $product['s_specs_title'.$i] ?></div>
                <div class="prodstable">
                <?
                $data = explode("\r\n",$product['s_specs_data'.$i]);
                foreach($data as $line) {
                  if($line) {
                    $parts = explode(":",$line);
                    if(count($parts) == 2) {
                      ?>
                      <div class="prodsrow">
                         <div class="prodscell"><?= $parts[0] ?>:</div>
                         <div class="prodscell"><?= $parts[1] ?></div>
                      </div>
                      <?
                    }
                   }
                 }
                 ?>
                </div>
                <div><?= $product['s_specs_html'.$i] ?></div>
              </div>
              <div class="cleaner"></div>
            </div>
          </div>
        </div>
        <?
      }
    }
    ?>
  </div>
  <? 
  body_foot();
}
?>