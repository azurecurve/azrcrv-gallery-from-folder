=== Gallery From Folder ===

Description:	Loads a gallery of thumbnails from a folder; full-size images are linked from thumbnail.
Version:		2.0.0
Tags:			gallery,images
Author:			azurecurve
Author URI:		https://development.azurecurve.co.uk/
Plugin URI:		https://development.azurecurve.co.uk/classicpress-plugins/gallery-from-folder/
Download link:	https://github.com/azurecurve/azrcrv-gallery-from-folder/releases/download/v2.0.0/azrcrv-gallery-from-folder.zip
Donate link:	https://development.azurecurve.co.uk/support-development/
Requires PHP:	5.6
Requires:		1.0.0
Tested:			4.9.99
Text Domain:	gallery-from-folder
Domain Path:	/languages
License: 		GPLv2 or later
License URI: 	http://www.gnu.org/licenses/gpl-2.0.html

Loads a gallery of thumbnails from a folder; full-size images are linked from thumbnail.

== Description ==

# Description

Gallery From Folder is a simple plugin which will read a folder and display a gallery of all thumbnails with links to the original full-size image.

The [gallery-from-folder] shortcode can be provided with either an alt_id parameter referning a specific name, or a post_id for a ClassicPress post.

An example of the shortcode is [gallery-from-folder alt_id="sample-gallery"]

== Installation ==

# Installation Instructions

 * Download the plugin from [GitHub](https://github.com/azurecurve/azrcrv-gallery-from-folder/releases/latest/).
 * Upload the entire zip file using the Plugins upload function in your ClassicPress admin panel.
 * Activate the plugin.
 * Configure relevant settings via the settings page in the admin control panel (azurecurve menu).

== Frequently Asked Questions ==

# Frequently Asked Questions

### Can I translate this plugin?
Yes, the .pot fie is in the plugins languages folder and can also be downloaded from the plugin page on https://development.azurecurve.co.uk; if you do translate this plugin, please sent the .po and .mo files to translations@azurecurve.co.uk for inclusion in the next version (full credit will be given).

### Is this plugin compatible with both WordPress and ClassicPress?
This plugin is developed for ClassicPress, but will likely work on WordPress.

== Changelog ==

# Changelog

### [Version 2.0.0](https://github.com/azurecurve/azrcrv-gallery-from-folder/releases/tag/v2.0.0)
 * Fix plugin action link to use admin_url() function.
 * Rewrite option handling so defaults not stored in database on plugin initialisation.
 * Update CSS to use _width: 100%_ instead of _width: 1000px_.
 * Rewrite options; this is a breaking change which will require options to be reset.
 * Update azurecurve plugin menu.

### [Version 1.0.0](https://github.com/azurecurve/azrcrv-gallery-from-folder/releases/tag/v1.0.0)
 * Initial release.

== Other Notes ==

# About azurecurve

**azurecurve** was one of the first plugin developers to start developing for Classicpress; all plugins are available from [azurecurve Development](https://development.azurecurve.co.uk/) and are integrated with the [Update Manager plugin](https://codepotent.com/classicpress/plugins/update-manager/) by [CodePotent](https://codepotent.com/) for fully integrated, no hassle, updates.

Some of the top plugins available from **azurecurve** are:
* [Add Twitter Cards](https://development.azurecurve.co.uk/classicpress-plugins/add-twitter-cards/)
* [Breadcrumbs](https://development.azurecurve.co.uk/classicpress-plugins/breadcrumbs/)
* [Series Index](https://development.azurecurve.co.uk/classicpress-plugins/series-index/)
* [To Twitter](https://development.azurecurve.co.uk/classicpress-plugins/to-twitter/)
* [Theme Switcher](https://development.azurecurve.co.uk/classicpress-plugins/theme-switcher/)
* [Toggle Show/Hide](https://development.azurecurve.co.uk/classicpress-plugins/toggle-showhide/)