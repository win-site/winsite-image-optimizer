=== Winsite Image Optimizer ===
Contributors: 		winsite, maor, amitt
Tags: 				image optimization, image compression
Requires at least: 	4.5
Tested up to: 		4.5.2
Stable tag: 		trunk
License:           	GPLv2 or later
License URI:       	https://www.gnu.org/licenses/gpl-2.0.html

Image compression for WordPress done right.

== Description ==

Winsite Image Optimizer will compress your images for optimal file-size while keeping its quality (pretty much) intact, using the ImageOptim API (and other processing engines coming up soon).

You can also process all of your old images retroactively using our dedicated tool, found under the Media section.

**Currently-implemented Image Processing Services:**

* ImageOptim


**Warning**: This won't work on localhost in some cases, since some services work in a way that they pull images in from your site. This is why you'll need a public URL in order to make this work locally.
You can use a service such as ngrok to achieve that, in conjunction with this filter:

`
add_filter( 'wsi_siteurl_override', function( $siteurl ) {
	return 'http://ae190611.ngrok.io/path-to-wp-installation/';
} );
`

== Screenshots ==

1. Compress images retroactively. Will also regenerate any thumbnail sizes. Very useful!
2. Statistics about the compression results
3. Settings section, under Settings > Media

== Changelog ==

= 1.0.0 - June 8, 2016 =
Initial POC built, including some basic Unit Tests. Props [@maor](https://github.com/maor)