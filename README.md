# Upblog

Upload Markdown to make a blog, see? **Upblog**.
It's like **Ghost** for PHP with no built-in editor.

## So You Want to Write Markdown and Upload it to Make a Blog

*Great!*
**Upblog** is built to work as soon as you configure it with your domain information:

 *	Fork or check out Upblog
 *	Upload it to your server that has PHP 5-ish
 *	Configure `config.php` with your blog root (read the comments)
 *	Configure `.htaccess` to point all requests to your `index.php`

There are plenty of extra config things you can do in `config.php` if you want to.
If you don't want upblog errors to display, make some CSS (in the master template) that styles `.upblog-error` with `display: none;` ... but be careful! :)

## Your First Post

Write a `test.md` file or use this README (rename it `test.md`). Upload it to your `posts/` directory as configured in `config.php`.
Now, in the browser, go to `your-site.com/blog/test`, except replace `your-site.com` with your domain name and `blog/` with whatever you put in `config.php:BLOG_ROOT` of course!

## Templates

**Upblog** looks in `~/templates` and if it finds a template matching the name of the page (e.g. 'index.php' for 'index.md') then it will use that. Otherwise it uses the `master.php` file in the templates directory.  
  
You can configure the location of the templates directory in `config.php`

A template can make use of the following variables to output post content and metadata:

	<?=$UPBLOG?> //Render the post
	//N.b. this is the shortest PHP echo syntax, but you can do it any other way.
	
Other, optional master variables:

	<?=$TITLE?> //Put it in the page <title> if you feel like it
	<?=nav(6)?> //Output an <ul> of nav items. Optional limit as parameter.
	<?=summaries(10)?> //Output the summaries of recent posts. Optional limit.

## How it works

Your .htaccess file should redirect the request for `/blog/test` to `/blog/index.php`, which picks up the *actual* request. Then it looks in the posts directory for a markdown file with that name. It converts it to HTML and puts it in the `$UPBLOG` variable. It renders the master template, which should echo the contents of that variable.

## File conventions

Write your blog posts in Markdown. Name them with the post title in lowercase with dashes-between-words:

	some-thoughts-on-blogging.md
	index.md


## License

For License information, see LICENSE.txt  

**php-markdown** is courtesy of [Michel Fortin](https://github.com/michelf/) - see php-markdown/License.md for details.

-----
[Stephen Griffiths](http://stegriff.co.uk) 2014 - [@SteGriff](http://twitter.com/stegriff) - github@stegriff.co.uk