=== Force non-SSL ===
Contributors: iandunn
Donate link: http://www.ijm.org
Tags: http, https, ssl
Requires at least: 2.7
Tested up to: 3.7.1
Stable tag: 0.4

Redirects all HTTPS traffic to HTTP, except for specific exceptions


== Description ==

Redirects all HTTPS traffic to HTTP, except for specific exceptions. You might want to do this to minimize the number of times users would see an error if you don't have a valid SSL certificate setup, or if you want to make sure that the delay SSL adds is only experienced when necessary.


== Installation ==

1. Upload the 'force-non-ssl' directory to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to the General Settings page and add any pages you want to leave HTTPS. See the FAQ for details.


== Frequently Asked Questions ==

= How do I add exceptions? =
* Go to the General Settings page
* Enter each page slug on its own line
	* You can also enter a single word to match any slugs containing that word. E.g., 'cat' would match '/people/cathryn' and '/cats-and-dogs'


== Screenshots ==
1. The exceptions setting on the General Settings page.


== Changelog ==

= 0.4 =
* Escaping output for improved security

= 0.3 =
* Stores settings inside Wordpress admin panel, so you no longer have to edit the raw PHP source file.
* Lots of back-end changes to use modern plugin standards.

= 0.2 =
* Stores an array of multiple pages, instead of just one
* Looser slug comparision, so that it matches trailing slashes and substrings

= 0.1 =
* Initial release


== Upgrade Notice ==
= 0.4 =
Removes a potential security vulnerability by escaping output.

= 0.3 =
Stores settings inside Wordpress admin panel, so you no longer have to edit the raw PHP source file.