<?php
use \Michelf\Markdown;

//URL and Directory setups
require_once 'config.php';
require_once 'php-markdown/Michelf/Markdown.php';
require_once 'php-query/phpQuery.php';
require_once 'upblog/summary.php';
require_once 'upblog/title.php';
require_once 'upblog/posts.php';
require_once 'upblog/links.php';
require_once 'posts-cache.php';

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
		$URL = current_url();
		
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

function template($name = null)
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
	$request = current_path();
	
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

function date_difference($posted)
{	
	$posted = DateTime::createFromFormat('U', $posted);
	$today = new DateTime('today');

	$difference = $today->diff($posted);

	if ($difference->days === 0)
	{
		return $difference->invert
			? 'Yesterday'
			: 'Today';
	}

	$lessThanAYearAgo = $difference->y === 0;
	
	$ds = [];
	if ($difference->y === 1)
	{
		$ds[] = $difference->y . ' year';
	}
	elseif ($difference->y > 1)
	{
		$ds[] = $difference->y . ' years';
	}
	
	if ($difference->m === 1)
	{
		$ds[] = $difference->m . ' month';
	}
	elseif ($difference->m > 1)
	{
		$ds[] = $difference->m . ' months';
	}
	
	if ($lessThanAYearAgo){
		$days = $difference->d + 1;
		if ($days > 1)
		{
			$ds[] = $days . ' days';
		}
	}
	
	$dateString = implode(', ', $ds) . ' ago';
	return $dateString;
}

function nav($limit = 6)
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
	//First get the entire requested URL
	$siteRoot = current_url();

	//Get the BLOG_ROOT from config 
	$endOfBlogRoot = strpos($siteRoot, BLOG_ROOT) + strlen(BLOG_ROOT);
	
	//Pare the current URL down to only the part up-to-and-including the blog root
	$siteRoot = substr($siteRoot, 0, $endOfBlogRoot);
	
	return $siteRoot;
}

//Get the bit like '/upblog/post-i-want'
function current_path()
{
	if (isset($_SERVER['REQUEST_URI']))
	{
		//Apache httpd
		return $_SERVER['REQUEST_URI'];
	}
	else
	{
		//IIS hopefully
		return $_SERVER['HTTP_X_ORIGINAL_URL'];
	}
	
}

//Get the whole address like 'http://stegriff.co.uk/upblog/post-i-want'
function current_url()
{
	if (isset($_SERVER['SCRIPT_URI']))
	{
		//Apache httpd
		//SCRIPT_URI is like 'http://stegriff.co.uk/upblog/rerouting/forever/and/a/day/'
		return $_SERVER['SCRIPT_URI'];
	}
	else
	{
		//You better hope it's IIS
		$scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'
			? 'https://'
			: 'http://';
		
		return $scheme . $_SERVER['HTTP_HOST'] . $_SERVER['HTTP_X_ORIGINAL_URL'];
	}
}

?>