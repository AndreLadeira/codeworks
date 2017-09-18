<?php 

require_once 'util.php';
require_once 'files.php';


/*
 * function cwCreateSearch
 * 
 * purpose: 	search within the page contents for a specified word
 * 				then returns the page html for the results
 * 
 * parameters:
 * 
 * 	what:      word or sentence to be searched for
 * 
 * 
 *  
 * 	
 */
function cwCreateSearch($what)
{
	// a little sanity check
	if ( $what == "" || empty($what))
		die("cwCreateSearch: empty search expression.");

	// get website page content data
	// reads data from the table as an array
	$table = cwGetJsonDataAsArray(CONTENT_DATABASE_FILE);

	$found = false;
	$first = true;
	$html = "";

	// for each record...
	foreach ($table as $row)
	{
		$id = $row["id"];
		
		$text = "";
		$count	= 0;
		
		// gets the content title
		$title = $row["title"];
		
		// get the content for this post id
		$content = cwGetContents($id);
		
		// remove my custon code markup from the content
		$content = preg_replace("/##code:(\w+\.\w{1,4}):(\d+):(\d+)#/","",$content);
		
		// remove html tags from the content
		$content = strip_tags($content);
		
		
		// cont the number of occurences of what within the content
		$count = cwCountOccurences($what, $content);
		
		// test for a match in the title
		if ( ($pos = stripos($title, $what, 0) !== false ) )
		{
			$text = cwGetPieceOfContent($content, 0,300);
			$title = cwHighlightMatches($what,$title);
		}
		elseif ($count)
		{
			$pos = stripos($content, $what, 0);
			$text = cwGetPieceOfContent($content, $pos,300);
		}
		else
		{
			continue;
		}
		
		$found = true;
		
		if ($first === true )
		{
			$html .= "<h1>Search results for <span class=\"strong\">$what</span></h1>\n";
			$first = false;
		}
		
		// gets the content summary img
		$imgFile = "./content/$id/$id"."_summary.jpg";
		if ( file_exists($imgFile) ) 
			$imgTag = "<img src=\"$imgFile\" alt=\"plugin_factory\">";
		else
			$imgTag = "<img src=\"./img/cwDefault.jpg\" alt=\"plugin_factory\">";
		
		// assemble its date
		$month = cwGetMonthStr( $row['month'] );
		$day = $row['day'];
		$year = $row['year'];
		
		$countInText = cwCountOccurences($what,$text);
		
		// "(X more matches)
		$diff = $count - $countInText;
		
		if ( $diff )
			$moreMatches = "<p class=\"info\">($diff more matches)</p>";
		else
			$moreMatches = "";
		
	
		$text = cwHighlightMatches($what,$text);
		
		// replaces newlines in $text by <br> tags
		
		$text = str_replace("\n","<br>\n",$text);
		
		// setup the link to the actual content
		$contentURL = "./content.php?id=$id";
		
		$html .= <<< ___html

<hr>
<a href="$contentURL"><div class="container">
	$imgTag
	<div class="contents">
		<h1>$title</h1>
		<span class="date">$month $day, $year</span>
		$text
		$moreMatches
	</div> <!-- contents -->
</div></a><!--  container -->
___html;

			
	}
	
	if (!$found)
	{
		$html .= "<h1>No results found for <span class=\"strong\">$what</span></h1>\n";
		
	}
		
	$html .= <<<___html
<hr>
<p class="poweredby">Search results by <span class="gblue">M</span><span class="gred">y</span><span class="gyellow">O</span><span class="gblue">w</span><span class="ggreen">n</span><span class="gred">C</span><span class="gyellow">o</span><span class="gblue">d</span><span class="ggreen">e</span></p>
___html;
	
	cwAddTabs($html,5);
	echo $html;
	//go home and have some foot massage
}




function cwGetContents($id)
{
	// open the right content file (article or summary)
	$contentFile = "./content/$id/$id".".html";
	
	if ( !file_exists($contentFile) )
	{
		// try again with the summary file
		$contentFile = "./content/$id/summary.html";
		if (!file_exists($contentFile))
			die("cwGetContents: no content found for content id \"$id\".");
	}
	
	// read the actual content from file
	$content = file_get_contents($contentFile) or
		die("cwGetContents: error reading content from file $contentFile, post id is $id");
	
	return $content;
}



function cwCountOccurences($what, $where)
{
	// initial setup
	$offset = 0;
	$count = 0;
	$whatLength = strlen($what);
	
	// gets the first occurrence of what, of false
	$pos = stripos($where, $what, $offset);
	
	while ($pos !== false)
	{	
		// increase the results counter
		$count++;
	
		// update the starting offset
		$offset = $pos + $whatLength;
	
		// gets the next occurrence of what, of false
		$pos = stripos($where,$what,$offset);
	}
	
	return $count;
}

function cwHighlightMatches($what, $where)
{
	// initial setup
	$offset = 0;
	$count = 0;
	$whatLength = strlen($what);
	$replacementLength = strlen("<span class=\"match\">$what</span>");

	// gets the first occurrence of what, of false
	$pos = stripos($where, $what, $offset);

	while ($pos !== false)
	{
		// retrieve the actual word from the text to respect the case
		$original = substr($where, $pos, $whatLength );
		$replacement =  "<span class=\"match\">$original</span>";
		
		$where = substr_replace($where,$replacement,$pos,$whatLength);

		// update the starting offset
		$offset = $pos + $replacementLength;

		// gets the next occurrence of what, of false
		$pos = stripos($where,$what,$offset);
	}
	
	return $where;
}

function cwGetPieceOfContent($content, $start, $length)
{
	$newStart = 0;
	$contentLength = strlen($content);
	$off = -($contentLength -$start);
	
	if ($start != 0 )
	{
		$newStart = strrpos($content,"\n", $off );
		if ($newStart === false ) $newStart = 0;
	}
	
	if ( ($contentLength - $newStart) < $length ) $length = $contentLength - $newStart;
	
	$text = substr($content, $newStart, $length );
	return "<p>" . $text . "...</p>";
	
}

?>