<? 
$scrpath = include_path(pathinfo(__FILE__)) ;
$defer = substr_count($globvars['php_path'], '/control/') ? '' : 'defer';
$defer = '';
js_file("{$scrpath}jquery/jquery-3.6.0.min.js",$defer);
/*
js_file("{$scrpath}jquery/jquery-ui-1.12.1.min.js",$defer);
js_file("{$scrpath}lazy/jquery.lazy.min.js",$defer);
?>
<script type="text/javascript">
$(function() { 
  $('.lazy').Lazy({
    effect: "fadeIn",
    effectTime: 500,
    threshold: 0
  }); 
});
</script>
<?
*/
js_file("{$scrpath}jquery/jquery.js","{$defer} date");
?>