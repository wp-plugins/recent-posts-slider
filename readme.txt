=== Recent Posts Slider ===
Contributors: nehagoel	
Tags: posts, recent, recent posts, recent post, scroll, slider, most recent, posts slider, most recent posts
Requires at least: 2.9.1
Tested up to: 3.1.1
Stable tag: 0.6.1

Recent posts slider displays your blog's recent posts using slider.

== Description ==

Recent Posts slider displays your blog's recent posts either with excerpt or thumbnail image of first image of the post using slider.
You can customize the slider in many ways (width, height, post per slide, no of posts to display & more).

It creates the thumbnail of either the first image from the post or of featured image. If you want to create a thumbnail of a specific image add a custom field as rps_custom_thumb and specify image path as value.

Check out the working demo at http://rps.eworksphere.com

If you have a feature that you would like to get added in future versions, feel free to contact me at http://rps.eworksphere.com/contact.

If you find it useful please don't forget to rate this plugin.

== Installation ==

= Installation =

1. You can use the built-in installer.
     OR         
     Download the zip file and extract the contents.
     Upload the 'recent-posts-slider' folder to your plugins directory (wp-content/plugins/).
1. Activate the plugin through the 'Plugins' menu in WordPress.

Now go to **Settings** and then **Recent Posts Slider** to configure any options as desired.

= How to use =

In order to display the recent posts slider, you have three options

1. Simply place `<?php if (function_exists('rps_show')) echo rps_show(); ?>` in your theme. 
1. Add the shortcode '[rps]'.
1. Using widget.

== Frequently Asked Questions ==

= Why only few characters display in post title? =
Yes, it will display only few characters due to some restrictions for flexible width & height of slider. 
To view complete post title move the mouse pointer over it.

= Having problems, questions, bugs & suggestions =
Contact me at http://rps.eworksphere.com/contact

== Screenshots ==

1. Configuration page

== Changelog ==

= v0.6.1 =
* Small image issue is fixed.

= v0.6 =
* Featured image thumbnail support is added.
* IE issue is fixed.

= v0.5 =
* Resolved some issues.
* New feature is added to show both excerpt & post thumb.
* New features are added to set post title color, pagination style, slider speed & excerpt words size.

= v0.4 =
* Fixed the issue related to image thumbnail

= v0.3 =
* Widget support is added.
* Custom field is added to pull post image.
* Jquery updated to latest version.

= v0.2 =
* Added more customization options for specific categories & posts.

= v0.1 =
* Initial release version.