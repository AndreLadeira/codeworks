<?php 
require_once 'files.php';
require_once 'util.php';
require_once 'createIndex.php';
require_once 'processSourceCode.php';

/*
 * function cwCreateContent
 *
 * purpose: 	creates html markup corresponding to the "content" section of a page.
 * 
 *
 * parameters:
 *
 * 	id:			content id
 *
 * Additional notes:
 *
 *	This function has also a lot of structure and code coupling hidden within it. Most of
 *	it is inherited from createIndex.
 *
 */

function cwCreateContent($id)
{
	if (($data = cwGetRecord(CONTENT_DATABASE_FILE, "id", $id)) === false)
		die("cwCreateContent: error retrieving data from content database. Content ID is: $id");
	
	// tries to read the file containig the content text
	@ $html = file_get_contents("./content/$id/$id.html");
	
	// if not available, try to read and output it as a summary 
	if ( $html === false)
	{
		@ $html = file_get_contents("./content/$id/summary.html");
		
		if ($html !== false )
		{
			cwCreateContentSummary($data);
			return;
		}
		else
		{
			die("cwCreateContent: content file not found; content ID is: $id");
		}
	}
	
	// find and process source code markups in the html file
	
	cwProcessSourceCode($html,$id, 2);
	
	// add tabs only to look good (actually to make debug easier)
	cwAddTabs($html, 6);
	
	
	$title = htmlentities($data['title']);
	$subtitle = htmlentities($data['subtitle']);
	$month = cwGetMonthStr($data['month'] );
	$day = $data['day'];
	$year = $data['year'];
	
	$authorHTML =  htmlentities( explode( " ", $data['author'] )[0] );
	//$authorHTML =  htmlentities( $arr[$i]['author']);
	$authorURL  =  urlencode($data['author']);
	
	echo <<<__html
	
				<div class="article">
	
					<h1>$title</h1>
					<h2>$subtitle</h2>
					<span class="date">$month $day, $year. By <a href="./author.php?id=$authorURL"><span class="author">$authorHTML.</span></a></span>
$html
				</div> <!-- article -->
__html;
	
}

?>