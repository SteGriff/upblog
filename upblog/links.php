<?php

//Private - Return a link to either the newer or older post than current
// $newer (bool) true to get newer
// $fallback (string) displayed if there is no newer or older
function link_to($newer, $fallback)
{
	global $posts, $FILENAME;

	$keys = array_keys($posts);
	rsort($keys);
	
	$key = null;
	foreach($keys as $key)
	{
		$p = $posts[$key];
		if ($p['file'] === $FILENAME || $p == null)
		{
			prev($keys);
			break;
		}
	}
	
	$key = $newer ? prev($keys) : next($keys);
	if (isset($posts[$key]))
	{
		$p = $posts[$key];
		return "<a href='{$p['link']}'>{$p['title']}</a>";
	}
	else
	{
		return $fallback;
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
