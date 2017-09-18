<?php 

require_once 'util.php';


/*
 *  function cwProcessSourceCode: 
 *  
 *  purpose: 			Find and replace a special markup string with the appropriate
 *  					html markup and with source code from a file in the local directory
 * 
 *  parameters:
 *  
 *  	html: 			String containing the html source.
 *  	id:				content id.
 *      tabs:			Number of tab caharters to be inserted in the begining of each line.
 *      				The purpose of these tabs are to make the final html code more readable. 
 *  
 *  Additional notes:
 * 
 *  	The markup string is in the form: ##code:<filename>:<start>:<end># where
 *  
 *  <filename> 		- string 	: file, located into the ./content/$id folder,  containing the source code to be injected
 *  							  into the html markup.
 *  
 *  <start>/<end> 	- integers 	: line numbers in the source code file, corresponding to the start/end of the section
 *  							  of code to be inserted. If start is less or equal to zero, all content is shown.
 *  							  when all content within the source code file is shown, a download button is 
 *  							  added to the html markup.
 *  
 * 
 */
function cwProcessSourceCode(&$html, $id, $tabs = 0)
{
	
	if ( preg_match_all("/##code:(\w+\.\w{1,4}):(\d+):(\d+)#/",$html, $matches))
	{
		// for each source code markup found into the html....
		$count = count($matches[0]);
		
		for ($i = 0; $i < $count; $i++)
		{
			//echo "File: {$matches[1][$i]}, starts at line {$matches[2][$i]}, ends at line {$matches[3][$i]}. String is {$matches[0][$i]}\n";
			
			$file = "./content/$id/{$matches[1][$i]}";
			
			// get the code and turn it into readable html (replacing < for &lt; and so on)
			$codeHtml = htmlentities(file_get_contents($file)) 
			or die("cwProcessSourceCode: error opening source code file: $file");
			
			$start = $matches[2][$i];
			$end = $matches[3][$i];
			
			// string containing the html code for the download button.
			$downloadBtn = "";
			
			// this variable will hold the html markup with the line numbers
			$lineNumbers = "";
			
			// get only some lines in the file, if that's the case
			if ( $start > 0 )
			{
				$arr = explode("\n",$codeHtml);
				$codeHtml = "";
				for ($line = $start -1; $line < $end; $line++ )
				{
					$codeHtml .= $arr[$line] . "\n";
				}
				$lineNumbers = cwGetLineNumbersHTML($end-$start+1);
			}
			else // whole file option
			{
				// add a download btn to download the whole file
				$downloadBtn = "<span class=\"btn\">download</span>";
				
				// get the html with the line numbers
				$lines = cwGetLinesInFile($file);
				$lineNumbers = cwGetLineNumbersHTML($lines);
			}
			
			// replace the tab characters by 3 spaces and the newlines by <br>;
			$codeHtml = str_replace(array("\t","\n"),array("&nbsp;&nbsp;&nbsp;","<br>\n"),$codeHtml);
			cwAddTabs($codeHtml, $tabs);
			
			// All set. Now, lets buld the complete html code representing a window
			// showing source code on the second column, and line numbers on the first column. 
			$codeblockHtml = <<<___html
<div class="codeblock header">
	<a href="$file" download>$downloadBtn</a>
</div>
<div class="codeblock container">
	<div class="linenumbers">
		$lineNumbers
	</div>
	<div class="code">
		$codeHtml
	</div> <!-- code -->
</div> <!-- codeblock container -->
			
___html;
			// 
			// Finally, replace the regex markup in the html with the 
			// complete html code for the source code window.
			$html = str_replace($matches[0][$i],$codeblockHtml,$html);
		} // for $i ... $count
			
	}// if preg_match_all....
	
}

// the next function returns a string containg
// html that renders to line numbers. In this application its a string in the 
// format: 1.<br>2.<br>3.<br>4.<br>5.<br>6.<br>7.<br>8.<br>9.  ...  $count.


function cwGetLineNumbersHTML($count)
{
	$res = "";

	for ($i=1;$i< $count;$i++)
	{
		$res .= "$i.<br>";
	}

	$res .= "$count.";

	return $res;
}

?>