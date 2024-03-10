<? @include_once('functions.inc.php'); ?>
<!DOCTYPE html>
<html lang="en"> 
  <head> 
    <title><?= $globvars['comp_name'] ; ?> - Admin Menu</title>
    <? if(isset($globvars['charset']) && substr_count($globvars['charset'], 'utf8')) { ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <? } if(file_exists('favicon.ico')) { ?>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <? } if(file_exists('head1.inc.php')) { @include_once('head1.inc.php'); } ?>
    <script type="text/javascript">window.name='<?= $globvars['php_self']; ?>';</script>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="stylesheet" type="text/css" href="leftmenu.css">
    <link rel="stylesheet" type="text/css" href="styles.css.php">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes, minimal-ui">
  </head>
  <body> 
    <?
    // print_arr(browser_info());
    ?>
    <table border="0" cellpadding="0" cellspacing="0" summary="" id="maintable" width="1255"> 
      <tr valign="top"> 
        <td valign="top" id="leftcol"> 
          <div id="leftdiv" align="center">
            <div class="maintop">
              <? 
              $plink = isset($globvars['plink']) && $globvars['plink'] ? $globvars['plink'] : '../' ;
              if(file_exists($globvars['admin_logo'])) { ?>
                <a href="<?= $plink ; ?>" target="public"><img src="<?= $globvars['admin_logo'] ?>" border="0" alt="<?= $globvars['comp_name'] ; ?>" width="170"></a>
              <? } else { ?>
                <h1><?= $globvars['comp_name']; ?></h1>
              <? } ?>
            </div>
            <br>
            <? @include_once('leftmenu.inc.php'); ?>
          </div>
        </td> 
        <td valign="top" id="midcol"> 
        </td> 
        <td valign="top" id="rightcol"> 
          <h1>WEBSITE SITEMAP</h1>
          <p style="margin-bottom:42px;">CLICK <img style="display:inline-block;padding:0 5px;width:15px" src="../images/edit.png"> TO EDIT OR <img style="display:inline-block;padding:0 5px;width:15px" src="../images/view.png"> TO VIEW PAGE</p>
          <div style="font-size:14px;">
          <?
          pages_main();
          disp_sitemap();
          ?>
          </div>
        </td> 
      </tr>
    </table>
    <?
    geoclean(48);
    logclean(6);
    // curr_call();
    ?>
  </body>
</html>