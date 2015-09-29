<!DOCTYPE HTML>
<html>
<head>
<title>My Blog - <?=$TITLE?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<?=twitter_card()?>

<link rel="shortcut icon" href="/favicon.ico" />

</head>
<body>

	<header>
		<h1><a href="/">My Blog</a></h1>
		
		<nav>
			<ul>
				<?=nav(6)?>
			</ul>
		</nav>
	</header>
	
	<article>
		<?=$UPBLOG?>
	</article>
	
	<footer>
		<p>
			Newer: <?=link_newer('This is the newest post')?>
		</p>
		<p>
			Older: <?=link_older('This is the oldest post')?>
		</p>
		<p>By me</p>
	</footer>

</body>
</html>