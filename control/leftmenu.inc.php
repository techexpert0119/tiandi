<table id="leftdivt" border="0" cellpadding="0" cellspacing="0" width="100%" summary=""> 
  <tr>
    <td valign="top">
      <h2 class="center">SERVER - <?= $globvars['hosting'] ?></h2>
      <table border="0" cellpadding="3" cellspacing="0" class="tabler" width="100%" summary=""> 
        <tr class="th"> 
          <td><h3 class="h3 center">PAGES</h3></td> 
        </tr> 
        <tr> 
          <td class="<?= $globvars['php_self'] == 'pages_main.php' ? 'button1' : 'button' ?>"><a href="pages_main.php" class="mbutt">Main Pages</a></td> 
        </tr> 
        <tr> 
          <td class="<?= $globvars['php_self'] == 'pages_subp.php' ? 'button1' : 'button' ?>"><a href="pages_subp.php" class="mbutt">Sub Pages 1</a></td> 
        </tr> 
        <tr> 
          <td class="<?= $globvars['php_self'] == 'pages_subs.php' ? 'button1' : 'button' ?>"><a href="pages_subs.php" class="mbutt">Sub Pages 2</a></td> 
        </tr> 
      </table>
      <br> 
      <table border="0" cellpadding="3" cellspacing="0" class="tabler" width="100%" summary=""> 
        <tr class="th"> 
          <td><h3 class="h3 center">SYSTEMS</h3></td> 
        </tr> 
        <tr> 
          <td class="<?= $globvars['php_self'] == 'models.php' ? 'button1' : 'button' ?>"><a href="models.php" class="mbutt">Models</a></td> 
        </tr> 
        <tr> 
          <td class="<?= $globvars['php_self'] == 'products.php' ? 'button1' : 'button' ?>"><a href="products.php" class="mbutt">Products</a></td> 
        </tr> 
        <tr> 
          <td class="<?= $globvars['php_self'] == 'components.php' ? 'button1' : 'button' ?>"><a href="components.php" class="mbutt">Components</a></td> 
        </tr> 
      </table>
      <br>
      <table border="0" cellpadding="3" cellspacing="0" class="tabler" width="100%" summary=""> 
        <tr class="th"> 
          <td><h3 class="h3 center">BLOG</h3></td> 
        </tr> 
        <tr> 
          <td class="<?= $globvars['php_self'] == 'blog_main.php' ? 'button1' : 'button' ?>"><a href="blog_main.php" class="mbutt">Articles</a></td> 
        </tr> 
        <tr> 
          <td class="<?= $globvars['php_self'] == 'blog_cats.php' ? 'button1' : 'button' ?>"><a href="blog_cats.php" class="mbutt">Categories</a></td> 
        </tr> 
        <tr> 
          <td class="<?= $globvars['php_self'] == 'blog_tags.php' ? 'button1' : 'button' ?>"><a href="blog_tags.php" class="mbutt">Tags</a></td> 
        </tr> 
      </table> 
      <br> 
      <table border="0" cellpadding="3" cellspacing="0" class="tabler" width="100%" summary=""> 
        <tr class="th"> 
          <td><h3 class="h3 center">OTHER</h3></td> 
        </tr> 
        <? /* ?>
        <tr> 
          <td class="<?= $globvars['php_self'] == 'home.php' ? 'button1' : 'button' ?>"><a href="home.php" class="mbutt">Home</a></td> 
        </tr> 
        <tr> 
          <td class="<?= $globvars['php_self'] == 'slider.php' ? 'button1' : 'button' ?>"><a href="slider.php" class="mbutt">Slider</a></td> 
        </tr> 
        <tr> 
          <td class="<?= $globvars['php_self'] == 'ticker.php' ? 'button1' : 'button' ?>"><a href="ticker.php" class="mbutt">Ticker</a></td> 
        </tr> 
        <tr> 
          <td class="<?= $globvars['php_self'] == 'press.php' ? 'button1' : 'button' ?>"><a href="press.php" class="mbutt">Press</a></td> 
        </tr> 
        <tr> 
          <td class="<?= $globvars['php_self'] == 'faqs.php' ? 'button1' : 'button' ?>"><a href="faqs.php" class="mbutt">FAQs</a></td> 
        </tr>
        <? */ ?>
        <tr> 
          <td class="<?= $globvars['php_self'] == 'footer.php' ? 'button1' : 'button' ?>"><a href="footer.php" class="mbutt">Footer</a></td> 
        </tr> 
        <tr> 
          <td class="<?= $globvars['php_self'] == 'social.php' ? 'button1' : 'button' ?>"><a href="social.php" class="mbutt">Social media</a></td> 
        </tr> 
        <tr> 
          <td class="<?= $globvars['php_self'] == 'parameters.php' ? 'button1' : 'button' ?>"><a href="parameters.php" class="mbutt">Parameters</a></td> 
        </tr> 
      </table>
      <? /* ?>
      <br> 
      <table border="0" cellpadding="3" cellspacing="0" class="tabler" width="100%" summary=""> 
        <tr class="th"> 
          <td><h3 class="h3 center">FORMS</h3></td> 
        </tr> 
        <tr> 
          <td class="<?= $globvars['php_self'] == 'contact.php' ? 'button1' : 'button' ?>"><a href="contact.php" class="mbutt">Contact Us</a></td> 
        </tr> 
        <tr> 
          <td class="<?= $globvars['php_self'] == 'partner.php' ? 'button1' : 'button' ?>"><a href="partner.php" class="mbutt">Partner With Us</a></td> 
        </tr> 
        <tr> 
          <td class="<?= $globvars['php_self'] == 'newsletter.php' ? 'button1' : 'button' ?>"><a href="newsletter.php" class="mbutt">Newsletter</a></td> 
        </tr> 
      </table>
      <br> 
      <table border="0" cellpadding="3" cellspacing="0" class="tabler" width="100%" summary=""> 
        <tr class="th"> 
          <td><h3 class="h3 center">SHOP</h3></td> 
        </tr> 
        <tr> 
          <td class="<?= $globvars['php_self'] == 'shop_cats.php' ? 'button1' : 'button' ?>"><a href="shop_cats.php" class="mbutt">Categories</a></td> 
        </tr> 
        <tr> 
          <td class="<?= $globvars['php_self'] == 'shop_subs.php' ? 'button1' : 'button' ?>"><a href="shop_subs.php" class="mbutt">Sub Categories</a></td> 
        </tr> 
        <tr> 
          <td class="<?= $globvars['php_self'] == 'shop_items.php' ? 'button1' : 'button' ?>"><a href="shop_items.php" class="mbutt">Products</a></td> 
        </tr> 
        <tr> 
          <td class="<?= $globvars['php_self'] == 'shop_brands.php' ? 'button1' : 'button' ?>"><a href="shop_brands.php" class="mbutt">Brands</a></td> 
        </tr> 
        <tr> 
          <td class="<?= $globvars['php_self'] == 'vouchers.php' ? 'button1' : 'button' ?>"><a href="vouchers.php" class="mbutt">Vouchers</a></td> 
        </tr> 
        <tr> 
          <td class="<?= $globvars['php_self'] == 'googlecats.php' ? 'button1' : 'button' ?>"><a href="googlecats.php?filter=g_visible|yes" class="mbutt">Google Cats</a></td> 
        </tr> 
        <tr> 
          <td class="button"><a target="sprint" href="sprint.php" class="mbutt">Check Sprint</a></td> 
        </tr> 
      </table>
      <br> 
      <table border="0" cellpadding="3" cellspacing="0" class="tabler" width="100%" summary=""> 
        <tr class="th"> 
          <td><h3 class="h3 center">Media</h3></td> 
        </tr> 
        <tr> 
          <td class="<?= $globvars['php_self'] == 'media.php' ? 'button1' : 'button' ?>"><a href="media.php" class="mbutt">Images</a></td> 
        </tr> 
        <tr> 
          <td class="<?= $globvars['php_self'] == 'gall_cats.php' || $globvars['php_self'] == 'gall_images.php' ? 'button1' : 'button' ?>"><a href="gall_cats.php" class="mbutt">Gallery</a></td> 
        </tr> 
        <tr> 
          <td class="<?= $globvars['php_self'] == 'video_map.php' ? 'button1' : 'button' ?>"><a href="video_map.php" class="mbutt">Videos</a></td> 
        </tr> 
      </table> 
      <? /* ?>
      <br> 
      <table border="0" cellpadding="3" cellspacing="0" class="tabler" width="100%" summary=""> 
        <tr class="th"> 
          <td><h3 class="h3 center">CUSTOMERS</h3></td> 
        </tr> 
        <tr> 
          <td class="<?= $globvars['php_self'] == 'order_details.php' ? 'button1' : 'button' ?>"><a href="order_details.php" class="mbutt">Orders</a></td> 
        </tr> 
        <tr> 
          <td class="<?= $globvars['php_self'] == 'user_details.php' ? 'button1' : 'button' ?>"><a href="user_details.php" class="mbutt">Accounts</a></td> 
        </tr> 
        <tr> 
          <td class="<?= $globvars['php_self'] == 'wishlist.php' ? 'button1' : 'button' ?>"><a href="wishlist.php" class="mbutt">Wishlist</a></td> 
        </tr> 
      </table> 
      <br> 
      <table border="0" cellpadding="3" cellspacing="0" class="tabler" width="100%" summary=""> 
        <tr class="th"> 
          <td><h3 class="h3 center">SHIPPING</h3></td> 
        </tr> 
        <tr> 
          <td class="<?= $globvars['php_self'] == 'ship_regions.php' ? 'button1' : 'button' ?>"><a href="ship_regions.php" class="mbutt">Regions</a></td> 
        </tr> 
        <tr> 
          <td class="<?= $globvars['php_self'] == 'ship_countries.php' ? 'button1' : 'button' ?>"><a href="ship_countries.php" class="mbutt">Countries</a></td> 
        </tr> 
        <tr> 
          <td class="<?= $globvars['php_self'] == 'ship_types.php' ? 'button1' : 'button' ?>"><a href="ship_types.php" class="mbutt">Types</a></td> 
        </tr> 
        <tr> 
          <td class="<?= $globvars['php_self'] == 'ship_options.php' ? 'button1' : 'button' ?>"><a href="ship_options.php" class="mbutt">Options</a></td> 
        </tr> 
      </table> 
      <? */ ?>
      <? if($globvars['cntrl_admin']) { ?>
      <br> 
      <table border="0" cellpadding="3" cellspacing="0" class="tabler" width="100%" summary=""> 
        <tr class="th"> 
          <td><h3 class="h3 center">ADMIN</h3></td> 
        </tr> 
        <? /* ?>
        <tr> 
          <td class="<?= $globvars['php_self'] == 'currencies.php' ? 'button1' : 'button' ?>"><a href="currencies.php" class="mbutt">Currencies</a></td> 
        </tr> 
        <? */ ?>
        <tr> 
          <td class="<?= $globvars['php_self'] == 'admin_users.php' ? 'button1' : 'button' ?>"><a href="admin_users.php" class="mbutt">Admin Users</a></td> 
        </tr> 
        <tr> 
          <td class="<?= $globvars['php_self'] == 'redirects.php' ? 'button1' : 'button' ?>"><a href="redirects.php" class="mbutt">Redirects</a></td> 
        </tr> 
        <tr> 
          <td class="<?= $globvars['php_self'] == 'db_log.php' ? 'button1' : 'button' ?>"><a href="db_log.php" class="mbutt">Database Log</a></td> 
        </tr> 
        <tr> 
          <td class="<?= $globvars['php_self'] == 'db_make.php' ? 'button1' : 'button' ?>"><a href="db_make.php" class="mbutt">Image Log</a></td> 
        </tr> 
        <tr> 
          <td class="<?= $globvars['php_self'] == 'minify.php' ? 'button1' : 'button' ?>"><a href="minify.php" class="mbutt">Minify CSS/JS</a></td> 
        </tr> 
        <tr> 
          <td class="button"><a href="index.php?logout" class="mbutt">CMS LOGOUT</a></td> 
        </tr> 
      </table>
      <? } ?>
    </td>
  </tr>
  <tr>
    <td valign="bottom" style="padding-top:10px;"><a href="../" target="public"><img src="<?= $globvars['admin_foot'] ?>" border="0" alt="<?= $globvars['design_name'] ; ?>" width="170"></a></td>
  </tr>
</table>
