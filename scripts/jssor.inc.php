<? 
$scrpath = include_path(pathinfo(__FILE__)) ;
@include_once("{$scrpath}jquery.inc.php");
js_file("{$scrpath}jssor/js/jssor.slider.min.js");
js_file("{$scrpath}jssor/jssor.js");
cs_file("{$scrpath}jssor/jssor.css");
?>