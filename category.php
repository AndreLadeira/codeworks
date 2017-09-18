<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<?php 
			require_once("./html/head.html"); 
		?>		

		<!-- Arquivo com os estilos -->
		<link rel="stylesheet" type="text/css" href="./css/main.css">
		<link rel="stylesheet" type="text/css" href="./css/summary.css">
		<link rel="stylesheet" type="text/css" href="./css/main-mobile.css">
					
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
		<script src="./js/codeworks.js"></script>
    	
	</head>
  	<body>
    	<div class="page-wrapper">   		
    		<?php 
    			require_once './php/createHeader.php';
    			
    			@ $id = htmlspecialchars($_GET['id']);
    			
    			//$id = "C++";
				if ( !cwVariableHasValue($id) ) $id = "";
				
				cwCreateHeader("", "category", $id); 
      		?>
      		
      		<div class="content">
      		
      		<?php 
      			require_once './php/createHeadline.php';
      			$cat = $id;
      			if ($id === "") $cat = "Uncategorized";
      			
      			cwCreateHeadline("codeworks", "Blog", "");
      			echo <<<___html
      			<h1 class="summary title">Articles and posts by category: <strong>$cat</strong></h1>
___html;
      			
      			require_once './php/createIndex.php';
      			cwCreateIndex('category',$id);
      		?>
      		
			</div> <!-- closes content -->
      	
      	</div> <!-- closes page-wrapper -->
      		
<?php 
require_once './php/footer.php';
?>
	
	</body>
</html>

