<?php
//URL and Directory Setup

/* 
	The last thing that appears before a page name in your blog URI:
	For 'example.com/upblog/MyFirstPost' the BLOG_ROOT is 'upblog/' or '/upblog/' whatever
	For 'example.com/MyFirstPost' it is 'example.com/'
	Default: upblog/
*/
const BLOG_ROOT = 'upblog/';

/*
	The folder relative to index.php where posts are kept.
	So index.php will look in (POSTS . 'MyFirstPost.md') to get a page
	E.g. 'posts/MyFirstPost.md'
	Default: posts/
*/
const POSTS = 'posts/';

/*
	The post file name to render for the blog root
	E.g. using 'index':
	example.com/blog/  -renders->  example.com/blog/posts/index.md
	Default: index
*/
const INDEX_POST = 'index';

/*
	The folder relative to index.php where special pages are kept.
	Used for 'page not found' page and stuff
	E.g. 'special/fail.md'
	Default: special/
*/
const SPECIAL = 'special/';

// Pages within the special folder:
//	Page not found (default: No such post)
const SP_PAGE_NOT_FOUND = 'no-such-post';

/*
	The folder relative to index.php where templates are kept
	Searched for a template with the same file name (before extension)
	as a post, or else master.php is used. It must contain a master.php.
*/
const TEMPLATES = 'templates/'
?>