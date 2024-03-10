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
      <? cms_edit(); 
      if($globvars['page']['html1']) { ?>
      <div id="content1">
        <div class="divpad">
          <? body_html(1); ?>
        </div>
      </div>
      <? } if($globvars['page']['html2']) { ?>
      <div id="content2">
        <div class="divpad">
          <? body_html(2); ?>
        </div>
      </div>
      <? } if($globvars['page']['html3']) { ?>
      <div id="content3">
        <div class="divpad">
          <? body_html(3); ?>
        </div>
      </div>
      <? } ?>
    </div>
  </div>
  <? 
  body_foot();
}
?>