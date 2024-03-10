<?
// https://select2.org/
$scrpath = include_path(pathinfo(__FILE__)) ;
@include_once("{$scrpath}jquery.inc.php");
?>
<script src="<?= $scrpath ?>select2/select2.min.js"></script>
<link href="<?= $scrpath ?>select2/select2.min.css" rel="stylesheet">
<script>
$(document).ready(function() {
  $('.select2').select2({
    minimumResultsForSearch: 10
  });
  $('.select2n').select2({
    minimumResultsForSearch: Infinity
  });
  $('.select2').one('select2:open', function() {
    $('input.select2-search__field').prop('placeholder', 'SEARCH');
  });
});
</script>
