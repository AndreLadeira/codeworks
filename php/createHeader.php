<?php

include_once 'util.php';
include_once 'files.php';

/*
 *  function cwCreateHeader
 *  
 *  purpose: creates the header section of each page of the codeworks site
 * 
 *  The header section contains:
 *  
 *  - A label containing the site name (codeworks)
 *  
 *  - A label describing the site (Programming articles and blog by andre ladeira)
 *  
 *  - A navbar with the sections of the site: home, about and search. 
 * 	  The current section, indicated by the "section" parameter, is labeled "active" ("class="active" in HTML).
 *  
 *  - A Search form, to search for content within the blog 
 *  
 *  - A background picture
 *  
 *  - A label describing the background pictute. That label is also a link to a website related to the picture.
 * 
 * parameters:
 * 	
 * 	section: 	current section of the site. The current section, indicated by the "section" parameter, 
 * 				is labeled "active" ("class="active" in HTML), so the CSS code can highlight it correclty.
 * 				If "section" is empty, the only consequence is that no section is labeled active in the navbar, 
 * 				what may be the expected behaviour. For example, if a category or a article is selected.  
 * 				
 * 
 *  fname:		file name of the picture to be displayed (see Additional notes below). 
 *  			If "fname" is empty, a random picture is selected.
 *  
 *  
 *  Additional notes:
 *  
 *  - Data about the pictures is stored in a JSON file: ./data/header-images.json
 *  
 *  - Pictures are stored in the ./img folder.
 *  
 *  - Some pictures are specific to a post, to a category, or to a section. For example, each post is supposed to have
 *    a picture related to the content of the post, each category has a picture etc. For these pictures a file name
 *    should be given in order to display the right picture in the header. If a file name is not given a randon
 *    image is choosen.  
 *  
 *  - In principle, all images in the ./img folder may be choosen when no file name is given. But then a problem arises:
 *    some images may be to specifc to a subject or post and may appear odd or out of context if displayed disconnected
 *    of its original context. To avoid that a field in JSON file ("random", yes or no) indicates whether the image can be
 *    selected at randomly or not.   
 *  
 *  - The json database files contains the following data for each picture available:
 *  
 *  
 *  "file": file name.
 *  "desc": short text describing the image, to be used as its caption.
 *	"author": author of the image.
 *	"year": year the image has been produced.
 *  "copyright": copyright info of the image.
 * 	"url": website pointed by the image caption.
 *  "random": flag indicating whether the image can be selected randomly or not.
 * 
 */

function cwCreateHeader($section = "", $fname = "" )
{
    // set the active section
	$active = array(
    		'home' => '',
    		'about' => '',
    		'search' => '');
    
    if ( $section !== "" )
    {
    	if ( array_key_exists($section, $active)  )
    		$active[$section] = "active";
    	else
    		die("cwGetHeader: section \"$section\" is invalid.");
    }
	

	
	$src = "";
	$alt = "";
	$captionURL = "";
	$captionText = "";
	
	if ( $fname !== "" ) 
	{
		$data = cwGetRecord(HEADER_IMAGES_DATABASE_FILE,"file", "$fname");
	
		if ( $data !== false )
		{
			$src = "./img/{$data["file"]}";
			$alt = $fname;
			$captionURL = $data["url"];
			$captionText = cwGetCaptionText($data);
		}
		else 
		{
			cwGetRandonImageData($src,$alt,$captionURL, $captionText);
		}
	}
	else 
	{
		cwGetRandonImageData($src,$alt,$captionURL, $captionText);
	}
	
	echo<<<__html

			<header>
				<img id="header-picture" src="$src" alt="$alt">
				<div class="navbar">
					<div class="content">
						<div class="brand center-vertical">
							<h1>&lt;codeworks/&gt;</h1>
							<h2>Programming Articles and Blog by André Ladeira</h2>
						</div>
						<div class="nav center-vertical">   
							<ul>
								<li><a class="{$active['home']}" href="./index.php">Home</a></li>
								<li><a class="{$active['about']}" href="./about.php">About</a></li>
								<li><a class="{$active['search']}" id="search">Search</a></li>
							</ul>
							<button class="collapse" value="off"></button>
							<div class="search">
                      			<form name="searchForm" action="./search.php" onsubmit="return validateSearchForm()" method="get">
                          			<input type="text" name="what" placeholder="search...">
                          			<button class="cancelBtn" type="button"></button>
                          			<button class="searchBtn" type="submit"></button>
                      			</form>
                    		</div><!-- search -->
						</div> <!-- nav -->
					</div> <!-- content -->  
				</div> <!-- navbar -->
				<div class="caption">
					<a id="header-picture-caption" href="$captionURL">$captionText</a>
				</div>
			</header>

__html;

}
	
/*
 *  function cwGetCaptionText
 *  
 *  Purpose: 	Gets a $data associative array containing data about a header image and return a label describing that image.
 *  			This piece of code is a function to avoid inconsistencies, because its used more than once.
 * 
 */
function cwGetCaptionText($data)
{
	return $data["desc"] . ". " . $data["author"] . ", " . $data["year"] ;
}

/*
 * function cwGetRandonImageData
 * 
 * Purpose: 	This function opens the header images database file (./data/header-images.json), selects a random entry from it,
 *  			and return the data of to the randomly selected image.
 * Parameters:  (all): data of the randomly selected image.
 * 
 * 
 * Additional notes:
 * 
 * 	Some images in the database file are not supposed to used everywhere and therefore should not be randomly selected
 *  (see the comments for the function cwCreateHeader above). These entries have the datafield "random" different from "yes" 
 *  and are checked and not returned by the function.
 * 	
 */

function cwGetRandonImageData(&$src, &$alt, &$captionURL, &$captionText)
{
	$string = file_get_contents(HEADER_IMAGES_DATABASE_FILE) or die("cwGetRandonImageData: JSON database file not found.");

	$arr = json_decode($string, true) or die("cwGetRandonImageData: Error decoding JSON database file");

	do 
	{
		$i = mt_rand(0, count($arr) - 1);
	}
	while( $arr[$i]["random"] !== "yes");

	$src = "./img/". $arr[$i]["file"];
	$alt = $arr[$i]["file"];
	$captionURL = $arr[$i]["url"];
	$captionText = cwGetCaptionText($arr[$i]);
}

?>