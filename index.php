<?php
//Use Michel's Markdown parser
require 'php-markdown/Michelf/Markdown.php';

//Upbloggy functions
require 'upblog.php';

//Globals for rendering - populated by deploy();
//Global for making post render in the template
$UPBLOG = '';
$TITLE = '';
$DESCRIPTION = '';
$IMAGE_SRC = '';
$URL = '';
$SITE_ROOT = site_root();
$TEMPLATE = '';
$FILENAME = '';

// ----- A wild REQUEST appears! -----

//Get the requested blog post filename
$requested_post = requested_blog_post();
$filename = blog_md_file($requested_post);

//If the file for that post exists, deploy it!
if (!deploy($filename)){

	//If it doesn't, use the special PAGE_NOT_FOUND page instead
	$errorPage = special_md_file(SP_PAGE_NOT_FOUND);
	
	//But what if THAT doesn't exist??!!
	if (!deploy($errorPage)){
		$TITLE = "So broken";
	
		//Just print an error message:
		error("No such post, and also I couldn't find the 'Page not found' special page at $errorPage! Double-bad times.");
	}
} 

//Render the template (the template should render the blog post by echoing $UPBLOG variable)
require $TEMPLATE;
?>