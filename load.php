<?php

require 'upblog.php';

function cache_posts()
{
	$posts = rebuild_posts();
	$posts_cache_file_content = '<?php $posts = ' . var_export($posts, true) . ';';
	file_put_contents('posts-cache.php', $posts_cache_file_content);
}

cache_posts();

echo 'Posts cache rebuilt and ready!';

?>