<? 
$scrpath = include_path(pathinfo(__FILE__)) ;
@include_once("{$scrpath}jquery.inc.php");
js_file("{$scrpath}iosslider/jquery.iosslider.js");
js_file("{$scrpath}iosslider/jquery.easing-1.3.js");
js_file("{$scrpath}iosslider/iosslider.js");
cs_file("{$scrpath}iosslider/iosslider.css");
?>