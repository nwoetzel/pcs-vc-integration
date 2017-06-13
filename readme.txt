=== Post Content Shortcodes Visual Composer Integration ===
Contributors: nwoetzel
Tags: pcs, post content shortcodes, vc, visual composer
Requires at least: 4.6
Tested up to: 4.7.2
Stable tag: 1.3.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

This pluging integrates shortcodes defined by the post-content-shortcodes plugin as elements into visual composer.

== Description ==

This plugin requires that you have installed:
* [Visual Composer](https://vc.wpbakery.com/)
* [Post Content Shortcodes](https://wordpress.org/plugins/post-content-shortcodes/)

The [Post Content Shortcodes shortcodes](https://wordpress.org/plugins/post-content-shortcodes/) are mapped as Visual Composer elements.

== Installation ==

Download the latest release from github as zip and install it through wordpress.
Or use [wp-cli](http://wp-cli.org/) with the latest release:
<pre>
wp-cli.phar plugin install https://github.com/nwoetzel/pcs-vc-integration/archive/1.3.0.zip --activate
</pre>

Or add them as a composer package in your wordpress' composer.json file:
<pre>
{
        "repositories": [
                {
                        "type":  "vcs",
                        "url":   "https://github.com/nwoetzel/pcs-vc-integration.git"
                }

        ],
        "require"     : {
                "nwoetzel/pcs-vc-integration":"~1.3"
        }
}
</pre>
Read more about that at http://composer.rarst.net/

== Frequently Asked Questions ==

== Screenshots ==

== Changelog ==

= 1.3.0 =
* added mapping for params: 'shortcodes' and 'link_image'

= 1.2.0 =
* added support for composer http://composer.rarst.net/

= 1.1.0 =
* added translation

= 1.0.1 =
* readme.txt updated

= 1.0 =
* Initial release
