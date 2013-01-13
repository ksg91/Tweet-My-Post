=== Plugin Name ===
Contributors: ksg91
Donate link:
Tags: twitter, tweet my post, author, twitter handle, publish, ksg91, post image, featured image,
Requires at least: 3.0
Tested up to: 3.5
Stable tag: 1.7.32

A WordPress Plugin which Tweets the new posts with its title, link, Author's twitter handle and a featured image from the post. 

== Description ==

This plugin allows WordPress Authors to set their twitter handle and whenever a new post is published, tweet will be sent with post's title, link, it's author's twitter handle and a featured image. 

Defualt Format would be "POST_TITLE - POST_LINK by @AUTHOR FEATURED_IMAGE" .

You can also set your own custom format that suits you best. Several options are available to customize your tweet from settings or at sidebar on post/page editor.

Much more to come! :-)



== Installation ==

1. Upload all files to the `/wp-content/plugins/tweet-my-post/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= How to set my twitter handle? =

You will find a menu called *Tweet My Post* under *Users*. Open it and set your Twitter Handle.

= How do I set my Twitter account's details from which the tweets are suppose to go? =

You will find the instructions under *Tweet My Post* menu.

= Why does this plugin doesn't send Tweet? =

You can check the reason at *TMP - Log* page. Most probably because you have not set your API and Access Tokens/Secret. Another well-known cause is, your Twitter app has only read permission. Update setting to read+write permission and re-create access tokens. 



== Screenshots ==

1. Admin Page to set API keys and tokens.
2. Option to select whether to tweet the post or not
3. User Page to set their Twitter handle.
4. Log Page

== Changelog ==

= 1.7.32 =
* Added option to set defaults for Editor's Sidebar.

= 1.7.29 =
* Warnings while posting a post fixed, breaking posting flow.

= 1.7.28 =
* Fixed bug of error resulting in breaking WordPress post flow.
* Exceptions are handled and logged 

= 1.7.24 =
* Library update

= 1.7.23 =
* Fixed issue of Tweet My Post not working due to Twitter's API v1's deprecation

= 1.7.22 =
* Tweet My Post messing other functionalities is fixed, hopefully.
* Tweet My Post uses the WordPress' jQuery now.
* Performance may improve a bit

= 1.7.21 =
* Updated support details

= 1.7.20 =
* Fixed internal bug

= 1.7.19 =
* Added Support for Future/Scheduled Posts

= 1.7.17 =
* Few bugs fixed
* Featured Image for Post and Pages
* jQuery based Side Pan while composing a post.

= 1.6.32 =
* Preview bug fixed

= 1.6.31 =
* Previews the Tweet 

= 1.6.24 =
* Added support for shortlinks
* Decent logs
* Fixed permalink issue
* Added support for page

= 1.4.12 =
* Quick fix to styling issue in other pages 

= 1.4.11 = 
* Allows you choose whether to tweet or not while publishing post or page

= 1.3.17 = 
* Added Feature to set custom Format

= 1.2.1 = 
* Fixed a security flaw

= 1.2 = 
* Added Debug Log

= 1.0 =
* Under the hood changes

= 0.9 =
* A separate page for Twitter API keys and access tokens
* A user page to set thier Twitter handle.

== Upgrade Notice ==

= 1.7.32 =
* Update to get couple of new options

= 1.7.29 =
* Updae asap to get broken wordpress flow on error fixed 

= 1.7.28 =
* Updae asap to get broken wordpress flow on error fixed 

= 1.7.24 =
* Hotfix for TMP's issue of not working for last few days

= 1.7.22 =
* Quick fix to resolve other functionalities from breaking.

= 1.7.17 =
* Upgrade asap. Fixes the bugs and bring a new feature.

= 1.6.32 =
* Fix  for preview tweet bug. Update asap.

= 1.4.12 =
* Update asap to fix style issue in other pages.

= 1.4.11 = 
* Choose whether to tweet new post or page, or not by simple checkbox on right

= 1.3.17 = 
* Added Feature to set custom Format

= 1.2.1 = 
* Fixed a security flaw

= 1.2 =
* Debug Log Mode for logging the response of twitter. After upgrading, please deactivate and reactive the plugin.

= 1.0 =
Internal changes to the plugin