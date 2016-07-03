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

* [ImageOptim](https://imageoptim.com/api) (You'll need to obtain an [API key](https://im2.io/register))
* Send your suggestions on our [GitHub issue tracker](https://github.com/win-site/winsite-image-optimizer/issues).


**Warning**: This won't work on localhost in some cases, since some services work in a way that they pull images in from your site. This is why you'll need a public URL in order to make this work locally.
You can use a service such as ngrok to achieve that, in conjunction with this filter:

`
add_filter( 'wsi_siteurl_override', function( $siteurl ) {
	return 'http://ae190611.ngrok.io/path-to-wp-installation/';
} );
`

We'd love to recieve contributions / suggestions from you on our [GitHub project](https://github.com/win-site/winsite-image-optimizer/).

== Installation ==

1.	Install the Winsite Image Optimizer Plugin either via the search option inside the WordPress plugins page (located in the toolbar of your admin page), or by uploading the files to your server (in the /wp-content/plugins/ directory). 
2.	Activate the Plugin (Install Now > Activate Plugin) 
3.	Then go to the Winsite Image Optimizer setting section (Settings > Media, under Winsite Image Optimizer section)  and then fill in the missing settings (API details).
4. Upload images (or retroactively processs images via Media > WS. Image Optimizer) and watch the file size decrease drastically!

== Screenshots ==

1. Compress images retroactively. Will also regenerate any thumbnail sizes. Very useful!
2. Statistics about the compression results
3. Settings section, under Settings > Media

== Changelog ==

= 1.0.6 - July 3, 2016 =
* Bug fix: Fixed issue where attachment metadata would be overriden and cleared when using the Retroactive Generator
* Bug fix: Fixed a UI/UX nuance. Now showing a notice for when a user hasn't set API details.
* Added a bit of fresh salsa sauce


= 1.0.2 - June 13, 2016 =
* Bug fix: Fixed issue where JS wasn't loading in back-end, disabling use of the Retroactive Generator. Props [@maor](https://github.com/maor)
* Improved UX on Retroactive Image Generator page by adding an spinner/indicator when process runs. Props [@maor](https://github.com/maor)
* Bug fix: Fixed issue where images that got processed would be able to get procesed again with the Retroactive Gen. Props [@maor](https://github.com/maor)

= 1.0.1 - June 8, 2016 =
Allow inputting ImageOptim username via Media settings page. Props [@maor](https://github.com/maor)

= 1.0.0 - June 8, 2016 =
Initial POC built, including some basic Unit Tests. Props [@maor](https://github.com/maor)