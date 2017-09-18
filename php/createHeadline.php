<?php 

/*
 *  function cwCreateHeadline
 *
 *  purpose: creates a headline in the format
 *  		 
 *  				sitename | section
 *  		  ************ headline ************  
 *  		
 *  		 on the top of the contents section of each page
 *
 * parameters:
 *
 * 	sitename: 	name of the site or brand identifying it.
 *  section:	current section within the site
 *  headline:   some cool short text to catch your reader and describe your site.		
 *  small: 		display only a small "sitename | section" of the headline, without the "headline" text
 *
 *
 */
define("HEADLINE_SMALL",0);
define("HEADLINE_NORMAL",1);

function cwCreateHeadline($sitename, $section, $headline, $size=HEADLINE_NORMAL)
{
	$smallTag = "";
	if ( $size == HEADLINE_SMALL ) $smallTag = "small";
	
	$headlineHtml = "";
	
	if ( $headline !== "")
		$headlineHtml = "<h2>$headline</h2>";
	
	echo<<<__html
	
				<div class="headline $smallTag">
					<!--
					Important: if the second <h1> goes on the next line
					an annoying white space is added between them
					-->
					<h1>$sitename</h1><h1>$section</h1>
				    $headlineHtml
				</div>

__html;

}

?>