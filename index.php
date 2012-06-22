<?php ob_start();
	try {
		$requireList = 	array(	'php_includes/site_name.inc.php',
								'php_includes/utility_function.inc.php'
						);
		$length = count($requireList);
		for ( $i = 0; $i < $length; $i++ ) {
			if ( file_exists($requireList[$i]) && is_readable($requireList[$i]) ) {
				require_once($requireList[$i]);
			} else {
				throw new Exception('could not initialize');
			}
		}  
?>
<!DOCTYPE html>
<HTML>	
	<HEAD>
		<TITLE><?php echo getPageTitle() ?></TITLE>
		<link rel="stylesheet" type="text/css" href="style/cloudshaped.css" />		
	</HEAD>
	<BODY>
		<DIV class=>
			<A HREF='<?php echo "http://$site_name/page2.php" ?>/'>My Link</A>
		</DIV>
		<DIV#main>
		</DIV>
		<DIV#footer>
			hello!
		</DIV>
	</BODY>
</HTML>
<?php 
	} catch (Exception $e) {
		ob_end_clean();
		header('Location: error.html');
	}
	ob_end_flush();
?>