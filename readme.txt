=== Simple Discography ===
Contributors: Sébastien Batteur
Donate link: http://www.batteur.be
Tags: artist, music, discography, music manage, Album, song, track, cover, photos, music photos, genesis
Requires at least: 3.0
Tested up to: 4.2
Stable tag: 1.3.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Simple Discography is a easy to use plugin that will allow you to manage the music tracks for an album or albums.

== Description ==

Simple Discography is a very easy to use plugin that will allow you to manage the music tracks for an album or albums.

For each album, you will be able to provide the following data:

* Titus Album
* Album Description
* Date of Publication
* Cover art
* Song titles
* Song duration

= Shortcode =

1. sidi-discography :

  [sidi-discography posts_per_page=-1 date_format="y" order_by="release" order="DESC" dynamic=1 show_song=1 show_title=1 display="list" cover_width=150 cover_height=150 id="" filter="" show_all=0 ]

  * parameter :
    * posts_per_page : default : -1 (all) value : -1, 1, 2, 3, 4,...
    * date_format : default : "y" value : date format of date function of PHP
    * order_by : default : "release" value : "rand", "title", "date", "modified", "release"
    * order : default : "DESC" value : "DESC", "ASC"
    * dynamic : default : 1 value : 0, 1 : show directly the discs and Songs. the user can not discs!
    * show_song : default : 1 value : 0, 1 : don't show the Discs and the songs
    * Display : default : list value : list, thumbnail
    * show_title : default : 1 value : 0, 1
    * cover_width : default : 150 value : 1, 2, 3, 4,...
    * cover_height : default : 150 value : 1, 2, 3, 4,...
    * id : default : "" value : string : if you use more than one sidi-discography on a page
    * filter : default : "" value : "2" ou "3,6" : Displays only the albums that are in the mentioned categories
    * show_all : default : 0 value : 0, 1 : Displays a link to the discography page if all the albums of the selection are not displayed


= Main Features =
* Easy to add new album
* Shortcode for view album list
* Widget with Album list
* Scalable cover on widget and shortcode
* Easy to organize tacks (Drag&Drop)
* Integrate responsive design for web and mobile
* Easy integration into the default WordPress themes and Genesis
* Integrate Genesis SEO
* Translate in French

== Installation ==

1. Upload `simple-discography` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Use the shortcode [sidi-discography] on your discography page for print your Album

== Screenshots ==

1. Diplay Widget
2. Add/Edite a new widget
3. Display Shortcode sidi-discography witch thumbnail parameter
4. Display Shortcode sidi-discography witch list parameter
5. Display Shortcode sidi-discography when click on the cover art
6. simple Album
7. Discography Admin
8. Add/Edite new album with the release date and de cover art
9. Add/Edite new album with the song
10. Used ShortCode sidi-discography

== Changelog ==

= 1.3.1 =

* Fix display "show_all" option on shortcode and widget

= 1.3 =

* Add category and tag
* Add filter category on widget and shortcode
* Add option show_all on widget and shortcode
* set standart format of album descrition
* fix small bug

= 1.2.4 =

* Fix the order_by for the value "release" with the help of John Leblanc

= 1.2.3 =

* Fix save release date during create a new album

= 1.2.2 =

* Fix compatibility with auther plugin
* Used wp_get_attachment_image for the cover

= 1.2.1 =

* Correction Widget_Form

= 1.2 =

* Add Widget with Album list
* Add setting "show_title", "cover_width", "cover_height" and "id" in sidi-discography shorcode
* Add sizing cover on widget and shortcode

= 1.1.2 =

* Correction for loading of the javascript and style of admin section
* Correction when release date for a post is empty

= 1.1.1 =

* Correction create Array for PHP < 5.4

= 1.1 =

* Add new display for the sidi-discography shortcode

= 1.0 =

Initial release.

== Todo ==

* Admin Section
  * create a thumbnail of 150x150 px for de Cover
  * Add management business link to Amazon, iTunes, Spotify ...
  * attache file resume track
  * import and export of discography
  * Add placeHolder on input box
* shortcode
  * add new single album shortcode
  * add "Simple list" for display type

