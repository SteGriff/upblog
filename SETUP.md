# Upblog setup guide

## Download

Clone the code from github and include submodules!

	$ git clone --recursive https://github.com/stegriff/upblog

## Config

Open `config.php` and change the `BLOG_ROOT` constant to the relative directory where upblog is installed on your domain. This is just used in a "find and replace" kind of way. Here are two examples:

 1. If you are using Upblog to power your whole website, called `bloggins.com`: **set the `BLOG_ROOT` to `bloggins.com/`
 2. If you are using it to power the `/blog` section of your site, then **set `BLOG_ROOT` to `/blog/`
 
That's all you need to configure in that file unless you have a particular reason to make upblog run differently.

## htaccess

Open `.htaccess` and change the address on the `RewriteRule` line. Two examples again:

 1.	If you are using Upblog as the whole site, the line should read:
		RewriteRule . /index.php [L]
		
 2.	If you are using it to power the `/blog` section of your site:
		RewriteRule . /blog/index.php [L]
		
## Upload

Upload the contents of the `upblog` directory that you checked out to your website.