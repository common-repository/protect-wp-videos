=== Protect Wordpress Videos ===
Contributors: profaceoff
Donate link: https://www.buildwps.com/protect-wordpress-videos-plugin/
Tags: videos, private, content, posts
Requires at least: 4.0.0
Tested up to: 4.8.3
Stable tag: 4.8.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Protect WordPress Videos offers a simple, fast and secure way to embed and protect your WordPress videos.

== Description ==

Protect WordPress Videos built on Video.js HTML5 video player library offers a simple, fast and secure way to embed and protect your WordPress videos.

Your videos will be automatically uploaded and served from Amazon S3 for free. So your videos won’t slow down your website, nor take up too much bandwidth of your hosting.
At the same time, your video links are also protected and cannot be accessed directly by anyone even if their links are shared with others.

In short, Protect WordPress Videos Free Version offers these features:

*   Videos are uploaded and served from Amazon S3
*   Your displayed videos are protected and cannot be accessed directly
*   Friendly UI to embed your videos to any posts, pages and content widget that you want. You don’t even need to use nor understand any shortcode. It’s automatically embedded on your content
*   Built-in HTML5 video player - no Flash required
*   Works on desktop, tablet and all mobile devices

Please note that

*   Our Free version only allows you to protect up to 3 video files with maximum 300MB per video.
*   Your videos are also uploaded directly to your server (WordPress Media), together with Amazon S3. So their original links are not protected and still accessible to the public.

== Installation ==

1. Login to your WordPress admin dashboard
1. Go to Plugins->Add New
1. Enter “Protect WordPress Videos” in the search box
1. Once you find the plugin hit the install button

== Frequently Asked Questions ==

= Why should I use Protect WordPress Videos plugin? =
First, our plugin serves your video from an Amazon S3 server, which otherwise will use up a lot of resources (memory, loading time, and bandwidth) on your server.

Second, our plugin allows you to embed your videos anywhere on your WordPress website using the awesome Video.js HTML5 video player.

Last but not least, our plugin also protects your displayed videos in such a way that no one else could access and steal them.

= How does Protect WordPress Videos plugin protect my videos? =

In short, our plugin automatically upload your videos to a private bucket on Amazon S3, which is only accessible by our custom Video.js player. No one else could access your videos directly through a public link.

= Are my videos embedded by this plugin playable on iPhone? =

Yes, it’s indeed playable on all mobile devices.

== Screenshots ==

1. Icon to upload protected video in post editor
2. Popup to modify

== Changelog ==

= 1.1.3 =

* Fix some host cannot read the key file for CDN by URL, replacing by absolute path.

* Using aws sdk by composer

= 1.1.2 =
* Fix video cannot play after generate url.

= 1.1.1 =
* Fix video cannot play on firefox.

= 1.1 =
* Add chromecast icon button.

= 1.0 =
* Add icon in post editor to create videojs shortcode.
* Protect videos by uploading to private storage and public it to customised videojs player by singed URL.






