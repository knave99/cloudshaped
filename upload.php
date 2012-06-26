<?php 
	ob_start();
	try {
		$requireList = 	array(	'php_includes/site_name.inc.php',
								'php_includes/utility_function.inc.php',
								'php_includes/upload.inc.php',
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
		<link rel="stylesheet" type="text/css" href="<?php echo $cssPath ?>/cloudshaped.css" />
		<?php include('php_includes/head.inc.php'); ?>		
	</HEAD>
	<BODY>
		<DIV id="wrap">
			<DIV id="main">
				<DIV id="header">
					header
				</DIV>	
				<DIV id="content">
					<DIV id="leftcolumn">
						column
					</DIV>
					<DIV id="rightcolumn">
						<?php 
							if ( isset($result) ) {
								echo '<ul>';
								foreach ($result as $message) {
									echo "<li>$message</li>";	
								}
								echo '</ul>';
							}
						?>
						<form action="" method="post" enctype="multipart/form-data" id="uploadImage">
							<P>
								<label for="image">Upload Image:</label>
								<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $maxFileSize; ?>"/>
								<input type="file" name="image[]" id="image" multiple/>
							</P>
							<P>
								<input type="submit" name="upload" id="upload" value="upload"/>
							</P>
						</form>
						<PRE>
							<?php
								if (isset($_POST['upload'])) {
									print_r($_FILES);
								}
							?>
						</PRE>
					</DIV>
				</DIV>			
			</DIV>
		</DIV>			
		<DIV id="footer">
			footer
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