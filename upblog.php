<?php
use \Michelf\Markdown;

//URL and Directory setups
require 'config.php';
require_once 'php-markdown/Michelf/Markdown.php';
require_once 'php-query/phpQuery.php';
require 'upblog/summary.php';
require 'upblog/title.php';
require 'upblog/posts.php';
require 'upblog/links.php';
require 'posts-cache.php';

//Oh hey look it's the deploy function
// Puts the HTML payload of the selected Markdown file into the special $UPBLOG global,
// so that it can then be rendered by the master template by echoing $UPBLOG
function deploy($filename){
	global $UPBLOG, $TITLE, $URL, $DESCRIPTION, $IMAGE_SRC, $TEMPLATE, $FILENAME;
	
	$TEMPLATE = template('master');
	
	//Puts the filename in the correct case if possible, false if it doesn't exist
	$filename = existing($filename);
	
	if ($filename){
		
		$FILENAME = $filename;
		
		//Open the chosen file
		// (either the blog post or a special page) 
		$content = file_get_contents($filename);

		//Populate Markdown
		// It's now the designer's job to echo $UPBLOG somewhere in the template page
		$UPBLOG = Markdown::defaultTransform($content);
		
		//Get template based on filename (or use master)
		$TEMPLATE = get_template($filename);
		$DESCRIPTION = summary_of_current();
		
		// Designer can use the other vars, like $TITLE, if they want to.
		$TITLE = title_of_current();
		$URL = $_SERVER['SCRIPT_URI'];
		
		$IMAGE_SRC = '';
		if (stripos($UPBLOG, '<img') !== false)
		{
			//Select the URL of the first <img> in the document
			$doc = phpQuery::newDocument($UPBLOG);
			$IMAGE_SRC = $doc['img']->attr('src');
			$IMAGE_SRC = site_root() . $IMAGE_SRC;
		}

		//Dispatch as found!
		return true;
	}
	else{
		//Post was not found
		return false;
	}
}

function template($name)
{
	//If argument given, translate POST dir to TEMPLATE dir
	// to get corresponding template
	if ($name)
	{
		$name = str_ireplace(POSTS, '', $name);
		return TEMPLATES . $name . '.php';
	}
	else
	{
		//No arg, return default template
		return TEMPLATES . 'master.php';
	}
}

function get_template($md_file)
{
	$file_without_extension = str_replace('.md', '', $md_file);
	$filename = existing(template($file_without_extension));
		
	if ($filename)
	{
		//Template file found
		return $filename;
	}
	else
	{
		//Not found, return master
		return template();
	}
}

//Get the requested blog post title from the URI:
// The entirety of the URL after BLOG_ROOT is found
// http://blog.com/upblog/NewSchool -> NewSchool
function requested_blog_post(){
	//Remove trailing slash/slashes from request URI
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

function date_difference($postedTime)
{
	$posted = new DateTime("@$postedTime");
	
	$today_ts = strtotime('today');
	$today = new DateTime("@$today_ts");

	$difference = $today->diff($posted);

	if ($difference->days == 0)
	{
		return 'Today';
	}
	elseif ($difference->days == 1)
	{
		return 'Yesterday';
	}
	
	$ds = [];
	if ($difference->m == 1)
	{
		$ds[] = $difference->m . ' month';
	}
	elseif ($difference->m > 1)
	{
		$ds[] = $difference->m . ' months';
	}
	
	if ($difference->d > 0)
	{
		$ds[] = $difference->d . ' days';
	}
	
	$dateString = implode(', ', $ds) . ' ago';
	return $dateString;
}

function nav($limit)
{
	//Get posts and sort by modified time (desc)
	global $posts;
	$keys = array_keys($posts);
	rsort($keys);
	
	$nav_html = '';
	
	$i = 0;
	foreach($keys as $key)
	{
		$post = $posts[$key];
		
		$daysDiff = date_difference($post['modified']);
		$nav_html .= "<li><a href=\"{$post['link']}\">{$post['title']} <small>($daysDiff)</small></a></li>\n";
		
		//Stop processing if we reach the 'limit' argument
		if ($limit)
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

function twitter_card()
{
	global $TITLE, $DESCRIPTION, $URL, $IMAGE_SRC;
	
	$twitterCardMarkup = "
	<meta name=\"twitter:card\" content=\"summary\" />
	<meta name=\"twitter:site\" content=\"@stegriff\" />
	<meta name=\"twitter:creator\" content=\"@stegriff\" />
	<meta name=\"twitter:title\" content=\"$TITLE\" />
	<meta name=\"twitter:description\" content=\"$DESCRIPTION\" />
	<meta name=\"twitter:url\" content=\"$URL\" />
	";

	if ($IMAGE_SRC > '')
	{
		$twitterCardMarkup .= "<meta name=\"twitter:image\" content=\"$IMAGE_SRC\" />";
	}

	return $twitterCardMarkup;
}

//Get URI of the the markdown file for the blog post with the given name
// Looks in the POSTS folder for that file
function blog_md_file($pageName){
	//If sent to a directory root, use index.md
	if(last_char($pageName) == '/'){ $pageName .= 'index'; }
	
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

function last_char($s)
{
	return $s[strlen($s) - 1];
}

//Get the whole scheme+host+url of the directory from which Upblog runs
// Uses the configured BLOG_ROOT
function site_root()
{
	//SCRIPT_URI is like 'http://stegriff.co.uk/upblog/rerouting/forever/and/a/day/'
	$siteRoot = $_SERVER['SCRIPT_URI'];

	$endOfBlogRoot = strpos($siteRoot, BLOG_ROOT) + strlen(BLOG_ROOT);
	$siteRoot = substr($siteRoot, 0, $endOfBlogRoot);
	
	return $siteRoot;
}

?>