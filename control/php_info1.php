<HTML> 
  <HEAD> 
	 <TITLE></TITLE> 
  </HEAD> 
  <BODY> 
<?php 
echo "PHP: ", phpversion (), '<br>';
echo "GD: ", extension_loaded('gd') ? 'OK' : 'MISSING', '<br>';
echo "XML: ", extension_loaded('xml') ? 'OK' : 'MISSING', '<br>';
echo "ZIP: ", extension_loaded('zip') ? 'OK' : 'MISSING', '<br>';
echo "IMAGICK: ", extension_loaded('imagick') ? 'OK' : 'MISSING', '<br>';
?>
   </BODY>
</HTML>
