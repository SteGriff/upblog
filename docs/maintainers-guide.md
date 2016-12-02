# Maintainer's Guide

## posts.php

Responsible for:

 * Rebuilding the post cache (called upon by `load.php` to do so)
 * Locating the manifest (.json) for a post (.md)
 * Constructing the metadata for a post
 
## upblog.php

Responsible for:

 * Rerouting requests to the correct post
 * Converting a post from markdown to HTML
 * Rendering a navigation bar
 * Locating post files and special files
 * Handling URLs
 
Other things in here that shouldn't be:

 * Utilities and extensions for date formatting, string manipulation, etc. (shouldn't be here)
 * Rendering twitter cards