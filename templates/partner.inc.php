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
    <? body_image(); ?>
    <div id="content">
      <? cms_edit(); ?>
      <div class="divpad">
        <? body_html(1); ?>
        <div id="partnerform" class="maxwid maxtext">
          <form id="partnerformf" action="#" method="post" onsubmit="return partner_submit()">
            <div class="partnerformn"><?= $globvars['forms_name'] ?></div>
            <div class="partnerformi"><input class="partnerformb" type="text" id="partner_name" value="" maxlength="200"></div>
            <div class="partnerformn"><?= $globvars['forms_email'] ?></div>
            <div class="partnerformi"><input class="partnerformb" type="text" id="partner_email" value="" maxlength="200"></div>
            <div class="partnerformn"><?= $globvars['forms_company'] ?></div>
            <div class="partnerformi"><input class="partnerformb" type="text" id="partner_company" value="" maxlength="200"></div>
            <div class="partnerformn"><?= $globvars['forms_phone'] ?></div>
            <div class="partnerformi"><input class="partnerformb" type="text" id="partner_phone" value="" maxlength="200"></div>
            <div class="partnerformn"><?= $globvars['forms_location'] ?></div>
            <div class="partnerformi"><input class="partnerformb" type="text" id="partner_location" value="" maxlength="200"></div>
            <div class="partnerformn"><?= $globvars['forms_details'] ?></div>
            <div class="partnerformi"><textarea id="partner_details"></textarea></div>
            <? /* ?><div class="partnerformn"><?= $globvars['forms_gdpr'] ?><input type="checkbox" name="partner_mailing" id="partner_mailing" value="yes"></div><? */ ?>
            <div class="partnerforms"><input type="submit" class="submit" name="submit" value="<?= $globvars['forms_submit'] ?>"></div>
          </form>
          <div id="partnerformt">
            <div id="partnerformth"><?= $globvars['pages_main']['partner']['head3'] ?></div>
            <?= $globvars['pages_main']['partner']['html3'] ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  <? 
  body_foot();
}
?>