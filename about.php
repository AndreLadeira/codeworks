<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<?php 
			require_once("./html/head.html"); 
		?>		
		<!-- Arquivo com os estilos -->
		<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet"> 
		
		<link rel="stylesheet" type="text/css" href="./css/main.css">
		<link rel="stylesheet" type="text/css" href="./css/summary.css">
		<link rel="stylesheet" type="text/css" href="./css/article.css">
		<link rel="stylesheet" type="text/css" href="./css/main-mobile.css">
			
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
		<script src="./js/codeworks.js"></script>
			
	</head>
  	<body>
    	<div class="page-wrapper">   		
    		<?php 
    			require_once './php/createHeader.php';
    			cwCreateHeader("about", "about.jpg");
      		?>
      		<div class="content">
      		
	      		<?php 
	      			require_once './php/createHeadline.php';
	      			cwCreateHeadline("codeworks", "Blog", "", HEADLINE_SMALL);
	      			include_once './content/about/about.html';
	      		?>
			</div> <!-- closes content -->
      	
      	</div> <!-- closes page-wrapper -->
      		
<?php 
require_once './php/footer.php';
?>
	
	</body>
</html>
