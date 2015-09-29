<?php

//Private - Return a link to either the newer or older post than current
// $newer (bool) true to get newer
// $fallback (string) displayed if there is no newer or older
function link_to($newer, $fallback)
{
	global $posts, $FILENAME;

	$p = reset($posts);
	while ($p['file'] !== $FILENAME && $p !== false){
		$p = next($posts);
	}
	
	if ($p === false)
	{
		return "Current post is false?! Post not found?!";
	}
	
	$p = $newer ? next($posts) : prev($posts);
	
	if ($p === false)
	{
		return $fallback;
	}
	else 
	{
		return "<a href='{$p['link']}'>{$p['title']}</a>";
	}
}

function link_newer($fallback)
{
	return link_to(true, $fallback);
}

function link_older($fallback)
{
	return link_to(false, $fallback);
}
