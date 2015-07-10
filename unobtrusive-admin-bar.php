<?php
/*
Plugin Name: Unobtrusive Admin Bar
Plugin URI: https://github.com/parallelus/unobtrusive-admin-bar
Description: Hide the admin bar for signed in users, but retain access to the features and functionality.
Version: 1.0.0
Author: Andy Wilkerson
Author URI: http://parallelus.github.io/unobtrusive-admin-bar
Text Domain: uab
Domain Path: /languages

Copyright 2015 Andy Wilkerson.

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

*/

// Exit if accessed directly
if ( __FILE__ == $_SERVER['SCRIPT_FILENAME'] ) { exit; }


if (!class_exists('Unobtrusive_Admin_Bar')) :
class Unobtrusive_Admin_Bar {

	/**
	 * @var Unobtrusive_Admin_Bar
	 */
	private static $instance;

	/**
	 * Sets break points.
	 * Accepts any number value.
	 */
	public static $responsive_break;

	/**
	 * Controls body margin (pushes content down)
	 * Accepts: true, false, 'top'
	 */
	public static $add_top_margin;

	/**
	 * Main Instance
	 *
	 * Allows only one instance of Unobtrusive_Admin_Bar in memory.
	 *
	 * @static
	 * @staticvar array $instance
	 * @return Big mama, Unobtrusive_Admin_Bar
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Unobtrusive_Admin_Bar ) ) {

			// Start your engines!
			self::$instance = new Unobtrusive_Admin_Bar;

			// Load the structures to trigger initially
			add_action( 'plugins_loaded', array( self::$instance, 'load_languages' ) );
			add_action( 'init', array( self::$instance, 'init' ), 10 ); // was priority 5
			add_action( 'admin_init', array( self::$instance, 'admin_init' ), 10 ); // was priority 5

		}

		return self::$instance;
	}

	/**
	 * Run all plugin stuff on init.
	 *
	 * @return void
	 */
	public function init() {

		// Set some values
		self::$responsive_break = apply_filters('uab_responsive_break_point', 783); // 783 matches default #wpadminbar beak points
		self::$add_top_margin = apply_filters('uab_add_top_margin', false); // adds top margin before scroll (true, false or 'top')

		// Add the CSS to <head>
		add_action('wp_head', array( self::$instance, 'add_css'), 99);

		// Add the JS to the footer
		if (self::$add_top_margin) {
			add_action( 'wp_footer', array( self::$instance, 'add_js'));
			add_action( 'wp_enqueue_scripts', array( self::$instance, 'enqueue_scripts' ) );
		}

		// Remove body classes
		add_filter('body_class', array( self::$instance, 'unset_body_class'));

	}

	/**
	 * Run all of our plugin stuff on admin init.
	 *
	 * @return void
	 */
	public function admin_init() {

		// No admin functions
	}

	/**
	 * Load our language files
	 *
	 * @access public
	 * @return void
	 */
	public function load_languages() {
		// Set textdomain string
		$textdomain = 'uab';

		// The 'plugin_locale' filter is also used by default in load_plugin_textdomain()
		$locale = apply_filters( 'plugin_locale', get_locale(), $textdomain );

		// Set filter for WordPress languages directory
		$wp_languages_dir = apply_filters( 'uab_wp_languages_dir',	WP_LANG_DIR . '/unobtrusive-admin-bar/' . $textdomain . '-' . $locale . '.mo' );

		// Translations: First, look in WordPress' "languages" folder
		load_textdomain( $textdomain, $wp_languages_dir );

		// Translations: Next, look in plugin's "languages" folder (default)
		$plugin_dir = basename( dirname( __FILE__ ) );
		$languages_dir = apply_filters( 'uab_languages_dir', $plugin_dir . '/languages' );
		load_plugin_textdomain( $textdomain, FALSE, $languages_dir );
	}

	/**
	 * Enqueue scripts.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {

		// Load some dependancies (probably a temporary requirement)
		wp_enqueue_script( 'jquery' );
	}

	/**
	 * Writes the custom CSS <style> tag to the head
	 *
	 * This is only written if the user is logged in and on the public website.
	 */
	public function add_css() {
		if ( is_user_logged_in() ) {

			?>
			<style type='text/css'>

				@media screen and ( min-width: <?php echo esc_attr(self::$responsive_break) ?>px ) {

					html { margin-top: 0 !important; }
					* html body { margin-top: 0 !important; }
					/*
					@media screen and ( max-width: 782px ) {
						html { margin-top: 46px !important; }
						* html body { margin-top: 46px !important; }
					}
					*/

					div#wpadminbar {
						opacity: 0;
						top: -32px;
					}

					div#wpadminbar:hover {
						opacity: 1;
						top: 0;
					}

					div#wpadminbar:after {
						content: " ";
						position: absolute;
						top: 100%;
						display: block;
						width: 100%;
						height: 10px;
					}

					div#wpadminbar:hover::after {
						height: 0;
						background-color: rgba(0,0,0,.15); /*#23282D;*/
					}

					div#wpadminbar, div#wpadminbar:after {
						-webkit-transition: opacity .35s ease .65s, background-color .35s ease 0s, top .35s ease .65s, height .35s ease .65s;
						-moz-transition: opacity .35s ease .65s, background-color .35s ease 0s, top .35s ease .65s, height .35s ease .65s;
						-0-transition: opacity .35s ease .65s, background-color .35s ease 0s, top .35s ease .65s, height .35s ease .65s;
						transition: opacity .35s ease .65s, background-color .35s ease 0s, top .35s ease .65s, height .35s ease .65s;
					}

					div#wpadminbar:hover, div#wpadminbar:hover::after {
						-webkit-transition: opacity .2s ease 0s, background-color .2s ease 0s, top .35s ease .65s, height .35s ease .65s;
						-moz-transition: opacity .2s ease 0s, background-color .2s ease 0s, top .35s ease .65s, height .35s ease .65s;
						-0-transition: opacity .2s ease 0s, background-color .2s ease 0s, top .35s ease .65s, height .35s ease .65s;
						transition: opacity .2s ease 0s, background-color .2s ease 0s, top .35s ease .65s, height .35s ease .65s;
					}

					body, html {
						-webkit-transition: margin .35s ease .65s;
						-moz-transition: margin .35s ease .65s;
						-0-transition: margin .35s ease .65s;
						transition: margin .35s ease .65s;
					}
				}

			</style>
			<?php
		}
	}

	/**
	 * Writes the custom JS <script> tag to the head
	 *
	 * This is only written if the user is logged in and on the public website.
	 */
	public function add_js() {
		if ( is_user_logged_in() ) {

			## TODO: Add condition so this only applies to screens larger than responsive break point setting.
			?>
			<script type='text/javascript'>
				jQuery(document).ready(function($) {
					$('#wpadminbar').hover(
						function() {
							<?php if (self::$add_top_margin === 'top') { ?> if ( $(document).scrollTop() < 32 ) { <?php } ?>
								$('body', 'html').css('margin-top', '32px');
							<?php if (self::$add_top_margin === 'top') { ?> } <?php } ?>
						}, function() {
							<?php if (self::$add_top_margin === 'top') { ?> if ( $(document).scrollTop() < 32 ) { <?php } ?>
								$('body', 'html').css('margin-top', '0');
							<?php if (self::$add_top_margin === 'top') { ?> } <?php } ?>
						}
					);
				});
			</script>
			<?php
		}
	}

	/**
	 * Remove body classes for .admin-bar
	 *
	 * The .admin-bar body class is used by plugins and themes to adjust display styles and
	 * to the extra body margin. With that removed by the plugin we can remove the body class
	 * also so that themes and plugins do not modify their appearance and leave a gap at the
	 * top of the page.
	 */
	public function unset_body_class( $classes = array() ) {

		$key = array_search('admin-bar', $classes);
		if ( is_user_logged_in() && $key !== false ) {
			unset($classes[$key]);
		}

		return $classes;
	}


} // End Class
endif;

/**
* The main function. Use like a global variable, except no need to declare the global.
*
* @return object The one true Unobtrusive_Admin_Bar Instance
*/
function Unobtrusive_Admin_Bar() {
	return Unobtrusive_Admin_Bar::instance();
}

// GO!
Unobtrusive_Admin_Bar();