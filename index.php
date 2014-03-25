<?php
//Use Michel's Markdown parser
require "php-markdown/Michelf/Markdown.php";

//URL and Directory setups
require 'config.php';
//Upbloggy functions
require 'upblog.php';

//Global for making post render in the template
$UPBLOG = '';

//Global for page titles
$TITLE = '';

// ----- A wild REQUEST appears! -----

//Get the requested blog post filename
$TITLE = requested_blog_post();
$filename = blog_md_file($TITLE);

//If the file for that post exists, deploy it!
if (!deploy($filename)){
	//If it doesn't, use the special PAGE_NOT_FOUND page instead
	$errorPage = special_md_file(SP_PAGE_NOT_FOUND);
	
	//But what if THAT doesn't exist??!!
	if (!deploy($errorPage)){
		//Just print an error message:
		error("No such post, and also I couldn't find the 'Page not found' special page at $errorPage! Double-bad times.");
	}
} 

//Replace dashes with spaces in the page title
$TITLE = str_replace('-', ' ', $TITLE);

//Render the template (the template should render the blog post by echoing $UPBLOG variable)
require 'master.php';
?>