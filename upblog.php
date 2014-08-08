<?php
use \Michelf\Markdown;

//URL and Directory setups
require 'config.php';

//Oh hey look it's the deploy function
// Puts the HTML payload of the selected Markdown file into the special $UPBLOG global,
// so that it can then be rendered by the master template by echoing $UPBLOG
function deploy($filename){
	global $UPBLOG, $TITLE;
	
	//Puts the filename in the correct case if possible, false if it doesn't exist
	$filename = existing($filename);
	
	if ($filename){
		//Open the chosen file
		// (either the blog post or a special page) 
		$handle = fopen($filename, 'r');
		$content = fread($handle, filesize($filename));
		fclose($handle);

		//Populate Markdown
		// It's now the designer's job to echo $UPBLOG somewhere in the template page
		// and optionally echo $TITLE if they want to.
		$UPBLOG = Markdown::defaultTransform($content);
		$TITLE = title_of($filename);

		return true;
	}
	else{
		return false;
	}
}

//Get the requested blog post title from the URI:
// The entirety of the URL after BLOG_ROOT is found
// http://blog.com/upblog/NewSchool -> NewSchool
function requested_blog_post(){
	$request = $_SERVER['REQUEST_URI'];
	$pageNamePosition = strpos($request, BLOG_ROOT) + strlen(BLOG_ROOT);
	$pageName = substr($request, $pageNamePosition);
	//If no request then return configured index page post
	return $pageName ?: INDEX_POST;
}

//Kind of extends file_exists
// Returns the cased file name if it exists as given or in lowercase
// Returns false if neither given nor lowercase can be found
function existing($f){
	if (file_exists($f)){
		return $f;
	}
	elseif (file_exists(strtolower($f))){
		return strtolower($f);
	}
	else{
		return false;
	}
}

function title_of($file){
	$h = fopen($file, 'r');
	
	//Get first line
	$line = fgets($h);
	
	//Look through the file for a line starting with '#'
	while(!feof($h) && $line[0] != '#'){
		$line = fgets($h);
	}
	
	if ($line[0] == '#'){
		return trim(substr($line, 1));
	}
	else{
		//No heading found, return what the user typed
		return requested_blog_post();
	}
}

//Generic date formatter, whatev, later, man...
function d($d){
	return date("F d Y H:i", $d);
}

//Returns an associative array of all posts
function posts()
{
	$posts = [];

	//Index all posts by modification date
	foreach(glob(POSTS . '*') as $file) 
	{
		if (dir($file))
		{
			continue;
		}
		
		$modified = filemtime($file);
		
		//Store posts by their modified time
		// but if the slot is taken, we just keep going up until
		// there is an empty slot.
		$storageTime = $modified;
		while (isset($posts[$storageTime])){
			$storageTime++;
		}
		
		//Put together a post information object
		$postInfo = [
			"file" => $file,
			"link" => basename($file, '.md'),
			"title" => title_of($file),
			"modified" => $modified,
			"key" => $storageTime
		];
		
		//Store it under the agreed key
		$posts[$storageTime] = $postInfo;
	}
	
	return $posts;
}

function nav($limit)
{
	//Get posts and sort by modified time (desc)
	$posts = posts();
	$keys = array_keys($posts);
	rsort($keys);
	
	$nav_html = '';
	
	$i = 0;
	foreach($keys as $key)
	{
		$post = $posts[$key];
		//Find difference in timestamps from post modified to now
		//Divide by 86400 seconds in a day, and floor
		$daysDiff = floor((time() - $post['modified']) / 86400);
		$daysDiff = $daysDiff > 0 ? "$daysDiff days ago" : "Today";
		$nav_html .= "<li><a href=\"{$post['link']}\">{$post['title']} <small>({$daysDiff})</small></a></li>\n";
		
		//Stop processing if we reach the 'limit' argument
		if ($limit != null)
		{
			$i++;
			if ($i >= $limit)
			{
				break;
			}
		}
	}
	
	return $nav_html;
}

//Get URI of the the markdown file for the blog post with the given name
// Looks in the POSTS folder for that file
function blog_md_file($pageName){
	return POSTS . $pageName . '.md';
}

//Get URI of the special markdown file with the given name
// Looks in the SPECIAL folder for that file
function special_md_file($pageName){
	return SPECIAL . $pageName . '.md';
}

//Print an error message.
// $level is the <h*> for the tag. 2 is less important than 1.
function error($message, $level = 1){
	global $UPBLOG;
	$UPBLOG .= "<h$level class='upblog-error'><small>Upblog Error:</small> $message</h$level>";
}
?>