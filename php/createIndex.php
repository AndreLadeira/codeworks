<?php 

require_once 'util.php';
require_once 'files.php';


/*
 * function cwCreateIndex
 * 
 * purpose: 	to create an index page, made up of summaries of contents or articles.
 * 				It may display all the content available (the default behaviour)
 * 				or display content filtered by a'$key contains $value'
 * 				criteria. (see additional notes below)
 * 
 * parameters:
 * 
 * 	key:		key in the json file 
 * 	value:      value of the key that represents a match
 * 
 * 
 * Additional notes:
 * 
 *	Data about the content are stored at ./data/blog-content.json. The format and meaning of its 
 *	contents are as follows:
 *
 *	"id":			unique ID of the content or article.
 * 	"title":		Title of the content
 *	"subtitle":		Subtitle or, most commonly, a short introduction to the subject of the content.
 *	"month":		digit in the range [1-12], corresponding to the month of the publication. 
 *					See "Notes on consistency" below.
 *	"day":			Digit in the range [1-31].
 *  "year":			Year of the publication. No check is done here.
 *  "author":		Content's author.
 *  "categoryIDs": 	A list of white space separated IDs corresponing to the subjects discussed in the content/article. 
 *  				See "Notes on consistency" below.
 *  "article":		Boolean, signaling if it's a regular content or an article.
 *  "rank":			A number thats used to sort the contents order. The LOWER the number, the CLOSER to the
 *  				top the content will be. This parameter is used to give the site the flexibility to 
 *  				have its index ordered in a way other than temporal or alphabetical.  
 *  				Most commonly used to keep a content on the top of the page. 
 *  
 *  
 *  Contents are filtered by a "key contains value" logic.   
 *  for example, for the pair
 *  
 *  	$key = 'category'  $value = 'html'
 *  		
 *  		the contents with the records
 *  
 *  		"category": "html",
 *  	and
 *  		"category": "C++, java, C, html" 
 *  
 *  are both included in the index, because both 'category' values contains 'html'.
 *  
 *  Notes on consistency: 
 *  
 *  	Why use a digit for the month and digits for the categories? Because it's less
 *  	error prone regarding the consistency of the strings used. For example, 
 *  	one day an author may think that the categories discussed within his/her 
 *  	article are
 *  
 *  		Category: "Design Patterns, C++"
 *  	
 *  	The next day he/she is less concentrated and makes a mispelling and use a 
 *  	captal lower case C:
 *  
 *  		Category: "Design Paterns, c++"
 *  	
 *  	The other day uses a singular:
 *  
 *   		Category: "Design Pattern"
 *   
 *   	And so on. This is ugly and may be source of errors, like failing to filter correctly by
 *   	category. To avoid that, basic database principles are used:
 *   	
 *   		- Categories are stored into a table: ./data/categories.json.
 *   		- Categories are referenced by their ID into the blog-contents.json table, instead of by their name.
 *   		- The CategoryIDs is a foreign key....Just kidding. Not possible with JSON files.	
 *   
 *   	The advantage: consistency.
 *   	The drawback: user has to look for the category IDs codes very time he/she publishes something
 *   	(may be solved by an interface...).
 *  
 * 	
 */
function cwCreateIndex($key = "", $value = "")
{
	// get records from the json contents database
	// based on the key => value pair
	
	// if 'category' is the key the string has to be converted
	// to the appropriate ID prior to selecting the contents
	
	$contents = array();
	
	if ( $key == "category" && $value !== "" )
	{
		// gets the ID from the category string
		$value = cwStrToID($value);
		
		// reads the whole content from the table as an array
		$table = cwGetJsonDataAsArray(CONTENT_DATABASE_FILE);
		
		// for each record, breaks the ids into an array
		// then do the comparisson
		foreach ($table as $row)
		{
			$arr = explode(" ", $row["category"]);
			$res = array_search($value,$arr);
			if ($res !== false) $contents[] = $row;
		}
	}
	else
	{	
		$contents = cwSelect(CONTENT_DATABASE_FILE, $key, $value );
	}
	
	//sorts the contents, based on their rank value
	usort($contents,"cwCustomSortByContentRank");
	
	// build the summary
	$ccount = count($contents);
	
	for ($i= 0; $i < $ccount; $i++ )
	{
		cwCreateContentSummary($contents[$i]);
	}

}

/*
 * function cwCreateContentSummary
 *
 * purpose: 	Creates aactual HTML code, containing the summary some content.
 * 				Content may be a content, article or a section of the site.
 *
 * parameters:
 *
 *	contentdata:   Content data, that is one particular record from ./data/blog-content.json table.
 *
 * Additional notes:
 * 
 * 	There is a nice amount of coupling and hidden structure of the website hard-coded into this function:
 *  
 * 		1 - It shows that the actual contents are created by a php file, content.php, that receives the content id as a parameter
 * 		2 - There's may be an image just for the summary. If used, the summary image:
 * 
 * 				- must be a square (not coded below, but I say so).
 * 				- must be located into ./contents/"content id" folder
 * 				- its name must be "content id"_summary.jpg
 * 		
 * 		3 - The content summary must be in a file called... summary.html!
 * 
 * Why all this coupling and hidden structure? Because it was fastest to do this way, and I wanted to see some results. 
 * Will regret that later.
 */

function cwCreateContentSummary($contentdata)
{
	$id = $contentdata["id"];
	
	$contentURL = "./content.php?id=$id";
	
	// checks if there is a image for the summary
	$img = "./content/$id/$id" . "_summary.jpg";
	
	if ( file_exists( $img ) )
		$img = "<a href=\"$contentURL\"><img src=\"$img\" alt=\"$id\"></a>";
	else
		$img = "";
	
	$title = htmlentities( $contentdata['title'] );
	
	$month = cwGetMonthStr( $contentdata['month'] );
	$day = $contentdata['day'];
	$year = $contentdata['year'];
	
	// HTML entities: convert from text UTF-8 to HTML
	// ex: < becomes &lt;
	// urlencode: convert from UTF-8 text to url format
	// ex: André Ladeira becomes Andr%C3%A9+Ladeira
	
	$authorHTML =  htmlentities( explode( " ", $contentdata['author'] )[0] );
	//$authorHTML =  htmlentities( $contentdata['author']);
	$authorURL  =  urlencode($contentdata['author']);
	
	$catHTML =  cwCreateCategoriesHTML( $contentdata['category'] , 3);
	$summaryHTML = cwCreateContentSummaryHTML( $id , 6);
	
	echo <<<__html
	
				<hr>
				<div class="summary">
					$img
			
					<div class="contents">
			
						<h1><a href="$contentURL">$title</a></h1>
						<span class="date">$month $day, $year. By <a href="./author.php?id=$authorURL"><span class="author">$authorHTML.</span></a></span>
	
						$summaryHTML
	
						<span class="category">$catHTML</span>
	
					</div> <!-- contents -->
	
				</div> <!-- summary -->
	
__html;
}

/*
 * function cwCustomSortByContentRank
 *
 * purpose: 	comparisson function, to s	ort the contents by their rank.
 *
 * parameters:
 * 
 * 	a, b: 	content data to be compared.
 *	
 */
function cwCustomSortByContentRank($a,$b)
{
	return $a['rank'] <= $b['rank'];
}

/*
 * function cwCreateCategoriesHTML
 *
 * purpose: 	Receives category IDs from content data record,	translates it
 * 				IDs to the corresponding strings, and returns 
 * 				the appropriate HTML markup.
 *
 * parameters:
 *
 * 	cats:		String containing a list of category ID's (see additional notes) sepatrated
 * 			 	by whitespaces. If cats is empty "Uncategorized" is returned.
 * 
 * additional notes:
 * 
 * 	- Category IDs and the corresponding strings are stored into ./data/categories.json
 * 	- Categories are supposed to be string containing a white-space separated list of IDs
 *  - Category strings can contain special characters, like +, <, > etc. They're converted
 *    to the appropriate html code. 
 *  
 *  Examples:
 *  
 *  empty $cats returns
 *  
 *  "<p><a href='./category.php?cat='>Uncategorized</a></p>"
 *  
 *  $cats = "c++, design patterns, <html>" (already translated from IDs to strings) returns
 *  "<p>
 *  <a href='./category.php?cat=c%2B%2B'>c&plus;&plus;</a>
 *  <a href='./category.php?cat=design+patterns'>design patterns</a>
 *  <a href='./category.php?cat=%3Chtml%3E'>&lt;html&gt;</a>
 *  </p>"
 *
 */

function cwCreateCategoriesHTML($cats)
{
	// categories are whitespace separated
	// category IDs like: "1 5 7" 

	// First, deal with the simplest case: empty string
	// meaning 'Uncategorized'

	if ( $cats == "" )
		return "<a href='./category.php?id='>Uncategorized</a>";
	
	// Translate from list of IDs to array of category names
		
	$category = cwIDsToStr($cats);
		
	// then build the markup
	$count = count($category);

	$str = "";


	for($i = 0; $i < $count; $i++)
	{
		$catHtml = htmlentities( $category[$i] );
		$catEncoded = urlencode( $category[$i] );
		
		if ($i < $count -1 )
			$str .= "<a href='./category.php?id=$catEncoded'>$catHtml</a>, ";
		else
			$str .= "<a href='./category.php?id=$catEncoded'>$catHtml</a>";
	}
	return $str;
}

/*
 * function cwIDsToStr
 *
 * purpose: 	Translates categories data from list of IDs to array of category names 
 *
 * parameters:
 *
 * 	cats:		String containing a list of category ID's sepatrated
 * 			 	by whitespaces.
 *
 */

function cwIDsToStr($cats)
{
	$catIDs = explode(" ",$cats);
	
	$res = array();
	
	foreach ($catIDs as $ID) 
	{
		$cat = cwGetRecord(CATEGORIES_DATABASE_FILE, "id", $ID);
		
		if ( $cat !== false) array_push($res, $cat["name"]);
	}
	
	return $res;
}
/*
 * function cwStrToID
 *
 * purpose: 	translate a string back to a category ID
 *
 * parameters:
 *
 * 	cats:		String containing a category
 *
 */

function cwStrToID($cat)
{	
	$data = cwSelect(CATEGORIES_DATABASE_FILE, "name", $cat);
	if ($data !== false) return $data[0]["id"];
	else return "";
}
/*
 * function cwCreateContentSummaryHTML
 *
 * purpose: 	Creates HTML markup containing the summary of the content
 *
 * parameters:
 *
 * 	id:			Content ID. Used to locate its data (see additional notes).
 * 
 * 
 * Additional notes:
 * 
 * 	Here we have another load of coupling and hidden structure:
 * 
 * 		- Each content is supposed to be into a ./content/$id folder.	
 * 		
 * 		- Content in the site me be split into two categories:
 * 
 * 			Some are smaller, just comments on something, impressions, thoughts. Let's call them posts.
 * 			Some are detailed discussions on some subject. Let's call them articles.
 * 		
 * 		- Articles have to have a summary file (containing a comprehensive summary and/or a captivating
 * 		  introduction to the subject being discussed) and the article file itself.
 * 		
 * 		- Posts have only the summary file.
 * 
 */

function cwCreateContentSummaryHTML( $id, $tabs = 0 )
{
	// file containig the content text
	$summary = file_get_contents("./content/$id/summary.html") or die("createContentSummaryHTML: summary not found: $id");

	// if there's a html for the content itself then put a link to it
	// in a "continue reading" message

	if ( file_exists("./content/$id/$id.html") )
		$summary = $summary . "\n<p><a href=\"./content.php?id=$id\">(continue reading)</a></p>";

	// puts the desired number of tabs before each line
	// every new line \n is replaced by a new line and n tabs

	cwAddTabs($summary, $tabs);

	return $summary;

}
?>


