<? include('functions.inc.php'); ?>
<!DOCTYPE html>
<html lang="en">
 <head>
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
   <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
   <link rel="icon" href="favicon.ico" type="image/x-icon">
   <link rel="stylesheet" type="text/css" href="../fonts/GalanoGrotesque/stylesheet.css">
   <meta http-equiv="X-UA-Compatible" content="">
   <script type="text/javascript">window.name='sprint.php';</script>
   <link rel="stylesheet" type="text/css" href="styles.css">
   <title>Sprint Products</title>
 </head>
 <body>
  <h1>Sprint Products</h1>
  <?
  $string1 = "select * from `shop_items`";
  $query1 = my_query($string1);  

  $string2 = "select * from `shop_options`";
  $query2 = my_query($string2);  

  while($row = my_assoc($query2)) {
    $options[$row['i_id']][$row['o_id']] = $row;
  }

  while($row = my_assoc($query1)) {
    $items[$row['i_id']] = $row;
    if(isset($options[$row['i_id']])) {
      $items[$row['i_id']]['options'] = $options[$row['i_id']];
    }
  }

  $sprint = sprint_get_products();
  if(isset($sprint['Result'])) {
    foreach($sprint['Result'] as $sitem) {
      $sku[$sitem['SKU']] = $sitem;
    }
  }

  // print_arv($items);
  // print_arv($sprint);
  // print_arv($sku);

  ?>
  <table cellpadding="6" cellspacing="2" class="tableb" width="1000">
    <tr>
      <th>Item Ref</th>
      <th>Product</th>
      <th>Options</th>
      <th>SKU</th>
      <th>Our Stock</th>
      <th>Sprint Stock</th>
      <th>Errors</th>
    </tr>
    <?
    foreach($items as $item) {
      if(isset($item['options'])) {
        foreach($item['options'] as $option) {
          $err = '';
          $sso = 0 ;
          if(isset($sku[$option['o_sku']])) {
            $sso = $sku[$option['o_sku']]['QtyAvailable'];
            if($sso != $option['o_stock']) {
              $err = 'Stock Mismatch';
            }
          }
          else {
            $err = 'Not found at Sprint';
          }
          ?>
          <tr>
            <td class="button"><a target="shop_items.php" href="shop_items.php?action=edit&go=<?= $item['i_id'] ?>"><?= str_pad ($item['i_id'], 4, '0', STR_PAD_LEFT ) ?></a></td>
            <td><?= $item['i_head'] ?></td>
            <td><?= $option['o_option1'] . ($option['o_option2'] ? ' | ' . $option['o_option2'] : '') ;  ?></td>
            <td><?= $option['o_sku'] ; ?></td>
            <td><?= $option['o_stock'] ; ?></td>
            <td><?= $sso ?></td>
            <td><?= $err ?></td>
          </tr>
          <?
        }
      }
      else {
        $err = '';
        $sso = 0 ;
        if(isset($sku[$item['i_sku']])) {
          $sso = $sku[$item['i_sku']]['QtyAvailable'];
          if($sso != $item['i_stock']) {
            $err = 'Stock Mismatch';
          }
        }
        else {
          $err = 'Not found at Sprint';
        }
        ?>
        <tr>
          <td class="button"><a target="shop_items.php" href="shop_items.php?action=edit&go=<?= $item['i_id'] ?>"><?= str_pad ($item['i_id'], 4, '0', STR_PAD_LEFT ) ?></a></td>
          <td></td>
          <td><?= $item['i_head'] ?></td>
          <td></td>
          <td><?= $item['i_sku'] ; ?></td>
          <td><?= $item['i_stock'] ; ?></td>
          <td><?= $sso ?></td>
          <td><?= $err ?></td>
        </tr>
        <?
      }
    }
    ?>
  </table>
 </body>
</html>
