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
        <div id="contactform" class="maxwid maxtext">
          <form id="contactformf" action="#" method="post" onsubmit="return contact_submit()">
            <div class="contactformn"><?= $globvars['forms_name'] ?></div>
            <div class="contactformi"><input class="contactformb" type="text" id="contact_name" value="" maxlength="200"></div>
            <div class="contactformn"><?= $globvars['forms_email'] ?></div>
            <div class="contactformi"><input class="contactformb" type="text" id="contact_email" value="" maxlength="200"></div>
            <div class="contactformn"><?= $globvars['forms_company'] ?></div>
            <div class="contactformi"><input class="contactformb" type="text" id="contact_company" value="" maxlength="200"></div>
            <div class="contactformn"><?= $globvars['forms_phone'] ?></div>
            <div class="contactformi"><input class="contactformb" type="text" id="contact_phone" value="" maxlength="200"></div>
            <div class="contactformn"><?= $globvars['forms_location'] ?></div>
            <div class="contactformi"><input class="contactformb" type="text" id="contact_location" value="" maxlength="200"></div>
            <div class="contactformn"><?= $globvars['forms_details'] ?></div>
            <div class="contactformi"><textarea id="contact_details"></textarea></div>
            <? /* ?><div class="contactformn"><?= $globvars['forms_gdpr'] ?><input type="checkbox" name="contact_mailing" id="contact_mailing" value="yes"></div><? */ ?>
            <div class="contactforms"><input type="submit" class="submit" name="submit" value="<?= $globvars['forms_submit'] ?>"></div>
          </form>
          <div id="contactformt">
            <div id="contactformth"><?= $globvars['pages_main']['contact']['head3'] ?></div>
            <?= $globvars['pages_main']['contact']['html3'] ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  <? 
  body_foot();
}
?>