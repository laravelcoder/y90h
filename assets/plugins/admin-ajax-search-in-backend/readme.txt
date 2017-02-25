=== Plugin Name ===
Tags: backend, admin, search, ajax
Requires at least: 3.0.1
Tested up to: 3.9.1
Stable tag: 1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Tired of hitting the Search button again and again?

Search your posts, pages, comments and many others in the WordPress Backend with the help of ajax.

== Installation ==

1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress


== Changelog ==

= 1.2.2 =
* Changed Name of function to avoid compatibility issues with other plugins. Namely disqus in the newest version.

= 1.2.1 =
* Enqueuing script with version number to avoid caching problems after upgrade

= 1.2 =
* More intelligent Ajax-loading of new content. Now includes pagination (even though a click wont load the next page over ajax, instead specify your searchterm).

= 1.1 =
* Make selection for Ajax-Container better to target the specific table.
* Updates Counts of Elements for that searchterm.

= 1.0 =
* Initial Version, should work on every admin page that uses the standard WordPress search box.

