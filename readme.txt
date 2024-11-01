=== Twitme ===
Contributors: Johnny Mast
Donate link: http://www.phpvrouwen.nl/twitme
Tags: Twitter, Communicate,automaticly,community,Posts,post,admin,plugin,Community,social
Requires at least: 2.5.1
Tested up to: 2.7.2
Stable tag: 1.6.9.7

This plugin allows you to automatically post your new posts on the twitter website.

== Description ==

Twitme 1.6.9.7 fixes the problem that was caused by the 1.6.9.6 version. The problem was that json_encode was not found on php4 systems


**Note**: Twitme does not work in combination with other Twitter plugins like "Twitter Tools". Make sure you disable those plugins before you
install Twitme (This goes for any version of Twitme).  



New in version 1.6.9.7:
  1. Fixed a bug where Twitme reported that it could not find json_encode.



 
**Plugin Summary:**

This plugin allows you to automatically post your new posts on the twitter website. This is good because for example the iPod and iPhone
for example have a large amount of twitter clients to pick from. Your blog posts will arrive to people while they are walking the streets.

== Installation ==
 
**Installation of Twitme is to easy**
 
1. Upload the directory Twitme to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to settings then Twitme Settings and setup your account information and your done.
 
If you are installing this plugin on Wordpress MU you should upload this plugin to the plugin directory. Note
this is just the same as Wordpress wp-content/plugins. Then the siteadmin should allow plugins and all the subblogs
will be abled to turn on Twitme for there own blogs.

And your done :).
 
== Screenshots ==
 
[Can be found here](http://www.phpvrouwen.nl/?page_id=263)
 
 
== Frequently Asked Questions ==

 * Q. Does it work on Wordpress MU ?.
 * A. Yes and with and later with version 1.7 it will even work in Buddypress for Wordpress MU

== Requirements ==
 
1. Php highter then version 4.8
2. Libcurl (Standard on most installations)
3. Apache 2 preferred (1.x supported)

== Credits ==


1. [TwItCh](http://www.designweapon.com/) - For designing the new Twitme logo since version 1.4
2. [Robert](http://www.soma3.de/) - For the suggestion of using the blog preferred perma link style and
    For reporting the bugs in 1.2 that led to version 1.3 and 1.3.1 of Twitme.
3. [nv1962](http://nv1962.net/)  - For suggesting %POSTTITLE% and %POSTURL%  + short urls.
4. [paulprins](http://paulprins.net/)  - For fixing the no-scalar bug that people where troubled with

== Translators ==
1. Johnny Mast (Dutch)

== Contact ==
 
For suggestions, bugs, hugs and love can be donated at the following locations.
 
1. [Authors page](http://www.phpvrouwen.nl/)
2. [Project webpage](http://www.phpvrouwen.nl/?page_id=263)
3. [Project Forum](http://www.phpvrouwen.nl/forums/forum.php?id=2)


== Privacy ==

November 2008

**Twitme 1.6 Privacy statement**

The Twitme privacy statement tells in truth how Twitme handles your privacy. Twitme will not use personal information for any other reason as stated below. Twitme will not ever sell or pass your personal information to other third parties then stated below.


**Communication between Twitme and the Author**

Since version 1.6 of Twitme it will send your information to the author of the plugin.
This information will be 

 * IP address
 * Blog URL
 * Blog email
 * Twitme version
 * Wordpress version

This information will be only used for statistical purposes and maintenance help. The author of Twitme will on no case use this information for
anything else then maintenance support or Statistical information.

**Communication between Twitme and Twitter**

Communication between Twitme and Twitter goes over a non-secured line. Twitter does not allow (yet) sending data over a secured line, This could change in the future


**Communication between Twitme Google Maps**

In your Twitter settings panel you can set your location. This location will reflect the location that you use to send your Tweets from. Twitme as
from version 1.6 uses this location from your followers to send it to Google Maps to indicated there position on the world. Twitme will NOT send this
information to any other third party or store this information on any server. Google Maps will also not save the location of your followers on there servers.

For your own privacy Google Maps support will be disabled by default. If you wish to see your followers on the "Twit Map" then you should add a Google Maps key API key in the Twitme settings panel.
 
