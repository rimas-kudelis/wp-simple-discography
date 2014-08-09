=== Simple Discography ===
Contributors: Sébastien Batteur
Donate link: http://www.batteur.be
Tags: artist, music, discography, music manage, Album, song, track, cover, photos, music photos, genesis
Requires at least: 3.0
Tested up to: 3.9.1
Stable tag: 1.0
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

=Shortcode=

1. sidi-discography :

[sidi-discography posts_per_page=-1 date_format="y" order_by="release" order="DESC" dynamic=1 show_song=1]

* parameter : 

> * posts_per_page : default : -1 (all) value : -1, 1, 2, 3, 4,..
> * date_format : default : "y" value : date format of date function of PHP
> * order_by : default : "release" value : "rand", "title", "date", "modified", "release"
> * order : default : "DESC" value : "DESC", "ASC"
> * dynamic : default : 1 value : 0, 1
> * show_song : default : 1 value : 0, 1

= Main Features =
* Easy to add new album
* Shortcode for view album list
* Easy to organize tacks (Drag&Drop)
* Easy integration into the default WordPress themes and Genesis
* Integrate Genesis SEO
* Translate in French

== Installation ==

1. Upload `simple-discography` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Use the shortcode [sidi-discography] on your discography page for print your Album

== Screenshots ==

1. Display Shortcode sidi-discography
2. Display Shortcode sidi-discography when click on the cover art
3. simple Album
4. Discography Admin
5. Add/Edite new album with the release date and de cover art
6. Add/Edite new album with the song
7. Used ShortCode sidi-discography

== Changelog ==

= 1.0 =

Initial release.

== Todo ==

* Admin Section

> * create a thumbnail of 150x150 px for de Cover
> * Add management business link to Amazon, iTunes, Spotify ...
> * attache file resume track
> * import and export of discography
> * Add placeHolder on input box

* shortcode

> * add parameter on shortcode discography of the sort of the albums
> * add parameter display shortcode discography for a other view
> * add new single album shortcode

* Add a widget with Album list