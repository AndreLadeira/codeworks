<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<?php 
			require_once("./html/head.html"); 
		?>		
		<link href="https://fonts.googleapis.com/css?family=PT+Mono|Source+Code+Pro" rel="stylesheet"> 
		
		<!-- Arquivo com os estilos -->
		<link rel="stylesheet" type="text/css" href="./css/main.css">
		<link rel="stylesheet" type="text/css" href="./css/article.css">
		<link rel="stylesheet" type="text/css" href="./css/summary.css">
		<link rel="stylesheet" type="text/css" href="./css/main-mobile.css">
		<link rel="stylesheet" type="text/css" href="./js/highlight.js/styles/vs.css">
		<link rel="stylesheet" type="text/css" href="./css/code.css">
		
			
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
		<script src="./js/codeworks.js"></script>
		
    	<script src="./js/highlight.js/highlight.pack.js"></script>
    	<script>
    		$(function()
   			{
    			$("div.code").each(function(i, block) 
    			{
        			hljs.highlightBlock(block);
    			});
    		}); 
   	
    	</script>	
    	
	</head>
  	<body>
  		<div id="fb-root"></div>
		<script>
			(function(d, s, id) {
				var js, fjs = d.getElementsByTagName(s)[0];
				if (d.getElementById(id)) return;
				js = d.createElement(s); js.id = id;
				js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.8";
				fjs.parentNode.insertBefore(js, fjs);
			}(document, 'script', 'facebook-jssdk'));
		</script>
    	<div class="page-wrapper">   		
    		<?php 
    			require_once './php/createHeader.php';
    			
    			@ $id = htmlspecialchars($_GET['id']);
    			//$id = "plugin_factory";
				if ( !cwVariableHasValue($id) ) $id = "";
				
				cwCreateHeader("", $id); 
      		?>
      		
      		<div class="content">
      		
      		<?php 
      			require_once './php/createHeadline.php';
      			cwCreateHeadline("codeworks", "Blog", "", HEADLINE_SMALL);
      			
      			require_once './php/createContent.php';
      			cwCreateContent($id);
      		?>
      			<div class="fb-comments-container">
      			<div class="fb-comments" data-href="http://codework.ddns.net:8080/content.php?id=devcpp_singapore" data-width="500" data-numposts="10"></div>
				</div> <!-- closes fb-comments-container -->
			</div> <!-- closes content -->
      	
      	</div> <!-- closes page-wrapper -->
      		
<?php 
require_once './php/footer.php';
?>
	
	</body>
</html>

