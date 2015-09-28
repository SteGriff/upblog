<?php
use \Michelf\Markdown;

function title_of_current()
{
	global $UPBLOG;
	return title_of($UPBLOG);
}

function title_of_file($filename)
{
	$content = file_get_contents($filename);
	return title_of($content);
}

function title_of($content){
	$content_md = Markdown::defaultTransform($content);
	$content_doc = phpQuery::newDocument($content_md);

	$title = $content_doc['h1']->getString()[0];

	if($title != null && $title != '')
	{
		return $title;
	}
	else{
		//No heading found, return what the user typed
		return requested_blog_post();
	}
}
