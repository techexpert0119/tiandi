<?
function head0() {
}

function head1() {
}

function head2() {
  global $globvars;
  get_include($globvars['page']['include'],'<!-- head_start -->','<!-- head_end -->');
}

function body() {
  global $globvars;
  body_top();
  ?>
  <div id="main">
    <?
    get_include($globvars['page']['include'],'<!-- body_start -->','<!-- body_end -->');
    ?>
  </div>
  <?
  body_foot();
  get_include($globvars['page']['include'],'<!-- foot_start -->','<!-- foot_end -->');
}
?>