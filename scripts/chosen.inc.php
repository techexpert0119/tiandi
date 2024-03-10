<?
// http://harvesthq.github.io/chosen/
$globvars['chosen_inc'] = 'y';
$scrpath = include_path(pathinfo(__FILE__)) ;
@include_once("{$scrpath}jquery.inc.php");
js_file("{$scrpath}chosen/chosen.jquery.min.js");
cs_file("{$scrpath}chosen/chosen.css");
js_file("{$scrpath}chosen/chosen.js");
?>
<style type="text/css">
.chosen-select {
width:100%;}
.chosen-container{ 
font-size:11px; color: #000000;}
.chosen-container .search-choice .group-name, 
.chosen-container .chosen-single .group-name, 
.chosen-container-single .chosen-single,
.chosen-container-single .chosen-default, 
.chosen-container .chosen-results, 
.chosen-container-multi .chosen-choices li.search-field input[type="text"]
{color: #000000;}
.chosen-container-single .chosen-single,
.chosen-container-active .chosen-choices,
.chosen-container-active.chosen-with-drop .chosen-single
{background:none;}
.chosen-container-single .chosen-single
{height:28px;border-radius: 3px;}
.chosen-container-multi .chosen-choices 
{border-radius: 3px;}
.chosen-container-active .chosen-single,
.chosen-container-active .chosen-choices
{border-color: #4D6391;}
</style>
