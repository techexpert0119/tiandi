<?
$scrpath = include_path(pathinfo(__FILE__)) ;
@include_once("{$scrpath}jquery.inc.php");
js_file("{$scrpath}jscrollpane/jquery.mousewheel.js");
js_file("{$scrpath}jscrollpane/mwheelIntent.js");
js_file("{$scrpath}jscrollpane/jquery.jscrollpane.min.js");
cs_file("{$scrpath}jscrollpane/jquery.jscrollpane.css");
cs_file("{$scrpath}jscrollpane/jquery.jscrollpane.lozenge.css");
js_file("{$scrpath}jscrollpane/jscrollpane.js");
?>