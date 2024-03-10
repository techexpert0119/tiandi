<? @include_once('control/functions.inc.php'); ?>
<!DOCTYPE html>
<html lang="en">
 <head>
  <title></title>
  <meta name="language" content="en-gb">	
  <meta charset="utf-8">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex, nofollow">
  <link rel="stylesheet" type="text/css" href="css/combined.min.css">

<!-- head_start -->
  <link rel="stylesheet" type="text/css" href="inc/css/include.css">
<!-- head_end -->

 </head>
 <body class="include">

<!-- body_start -->
    <? body_image(); ?>
    <div id="content">
      <?
      body_bread();
      body_h1();
      ?>
      <div class="maxwid">
        <h2>SAMPLE INC FILE</h2>
        <p>&bull; Include files should be file type .inc.php and be saved in the root so that relative links to images etc. will work.</p>
        <p>&bull; Content must be between the <b>body_start</b> and <b>body_end</b> markers. This example includes the maxwid div which is for the standard central area.</p>
        <p>&bull; Images and css files etc. should be put in the <b>inc folder</b> so they are separated from the core files.</p>

        <h2>STYLES AND SCRIPTS</h2>
        <p>&bull; Additional stylesheets etc. to be put in the <b>inc folder</b> and linked between the <b>head_start</b> and <b>head_end</b> markers.</p>
        <p>&bull; Note that header tags are loaded after the core stylesheets so they can be used to override styles.</p>
        <p>&bull; Any jquery must be added between the <b>foot_start</b> and <b>foot_end</b> markers so that it works after the jquery library has loaded.</p>

        <h2>DEVELOPMENT</h2>
        <p>&bull; This page can be loaded directly for development and testing which will not show the header and footer etc - <a href="incnotes.inc.php">see here</a>.</p>
        <p>&bull; Content outside the <b>_start</b> and <b>_end</b> markers is to aid testing of the page directly but is NOT LOADED when the page runs from the site.</p>
        <p>&bull; Core functions can be removed eg. body_image, body_bread, body_h1, body_html if required.</p>

        <h2>TEST FUNCTIONS</h2>
        <p><?= '&bull; This line is to test that php works.' ?></p>
        <p id="testjs"></p>
      </div>
      <?
      body_html(1);
      body_html(2);
      body_html(3);
      body_html(4);
      ?>
    </div>
<!-- body_end -->

<script src="scripts/jquery/jquery-3.6.0.min.js"></script>
<script src="scripts/jquery/jquery.min.js"></script>

<!-- foot_start -->
<script>
  $(document).ready(function(){
    $('#testjs').html('&bull; This line is to test that jquery works.');
  });
</script>
<!-- foot_end -->
 </body>
</html>