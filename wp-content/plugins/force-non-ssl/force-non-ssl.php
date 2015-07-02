<?php
/*
Plugin Name: Force non-SSL
Description: Redirect all HTTPS traffic to HTTP, except for specific exceptions
Version: 0.4
Author: Ian Dunn
Author URI: http://iandunn.name
License: GPL2
*/

/*  
 * Copyright 2011 Ian Dunn (email : ian@iandunn.name)
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as 
 * published by the Free Software Foundation.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

if ( basename( $_SERVER['SCRIPT_FILENAME'] ) == basename( __FILE__ ) )
	die( "Access denied." );

define( 'FNSSL_NAME',                'Force non-SSL' );
define( 'FNSSL_REQUIRED_PHP_VERSON', '5' );

if ( ! class_exists( 'forceNonSSL' ) ) {
	/**
	 * A Wordpress plugin that redirects all HTTPS traffic to HTTP, except for specific exceptions
	 * Requires PHP5+ because of various OOP features, pass by reference, etc
	 * Requires Wordpress 2.7 because the settings API
	 *
	 * @package ForceNonSSL
	 * @author  Ian Dunn <ian@iandunn.name>
	 * @todo
	 *        Look into using http://codex.wordpress.org/Function_Reference/WP_Error instead of your custom one
	 *        Check if there's a more appropriate hook to use instead of template_redirect
	 *        Add internationalization support
	 */
	class forceNonSSL {
		// Declare variables and constants
		protected $settings, $options, $updatedOptions, $environmentOK, $userMessageCount;
		const REQUIRED_WP_VERSION = '2.7';
		const PREFIX              = 'FNSSL_';
		const DEBUG_MODE          = false;

		/**
		 * Constructor
		 *
		 * @author Ian Dunn <ian@iandunn.name>
		 */
		public function __construct() {
			// Initialize variables
			$defaultOptions               = array( 'updates' => array(), 'errors' => array() );
			$this->options                = array_merge( get_option( self::PREFIX . 'options', array() ), $defaultOptions );
			$this->updatedOptions         = false;
			$this->userMessageCount       = array( 'updates' => 0, 'errors' => 0 );
			$this->settings['exceptions'] = get_option( self::PREFIX . 'exceptions' );
			$this->environmentOK          = $this->checkEnvironment();

			// Register action for error messages and updates
			add_action( 'admin_notices', array( $this, 'printMessages' ) );

			// Register remaining actions and filters
			if ( $this->environmentOK ) {
				add_action( 'admin_init',                                         array( $this, 'addSettings' ) );
				add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'addSettingsLink' ) );
				add_action( 'template_redirect',                                  array( $this, 'redirectHTTPS' ) );
			}
		}

		/**
		 * Checks whether the system requirements are met
		 * file.php is only loaded by WP when necessary, so we include it to make sure we can always check the flag inside it
		 *
		 * @author Ian Dunn <ian@iandunn.name>
		 * @return bool True if system requirements are met, false if not
		 */
		protected function checkEnvironment() {
			require_once( ABSPATH . '/wp-admin/includes/file.php' );
			global $wp_version;
			$environmentOK = true;

			if ( version_compare( $wp_version, self::REQUIRED_WP_VERSION, "<" ) ) {
				$this->enqueueMessage( FNSSL_NAME . ' requires Wordpress ' . self::REQUIRED_WP_VERSION . ' or newer in order to work. Please upgrade if you would like to use this plugin.', 'error' );
				$environmentOK = false;
			}

			return $environmentOK;
		}

		/**
		 * Adds our custom settings to the admin Settings pages
		 *
		 * @author Ian Dunn <ian@iandunn.name>
		 */
		public function addSettings() {
			add_settings_section( self::PREFIX . 'general-settings', 'Force non-SSL', array( $this, 'settingsSectionCallback' ), 'general' );
			add_settings_field( self::PREFIX . 'exceptions', 'Exceptions', array( $this, 'settingsCallback' ), 'general', self::PREFIX . 'general-settings' );
			register_setting( 'general', self::PREFIX . 'exceptions' );
		}

		/**
		 * Adds the section introduction text to the Settings page
		 *
		 * @author Ian Dunn <ian@iandunn.name>
		 */
		public function settingsSectionCallback() {
			// Intentionally blank
		}

		/**
		 * Adds the input field to the Settings page
		 *
		 * @author Ian Dunn <ian@iandunn.name>
		 */
		public function settingsCallback() {
			?>
			
			<p>
				<label for="<?php echo self::PREFIX; ?>exceptions">
					Enter the slug of every post or page you would like to allow to be called with HTTPS. Place each entry on its own line. You can find the slug by editing a page and looking at the Permalink field under the title.
				</label>
			</p>

			<textarea id="<?php echo self::PREFIX; ?>exceptions" name="<?php echo self::PREFIX; ?>exceptions" rows="10" cols="50" class="large-text code"><?php
				echo esc_textarea( $this->settings['exceptions'] );
			?></textarea>
			
			<?php
		}

		/**
		 * Adds a 'Settings' link to the Plugins page
		 *
		 * @author Ian Dunn <ian@iandunn.name>
		 * @param array $links The links currently mapped to the plugin
		 * @return array
		 */
		public function addSettingsLink( $links ) {
			array_unshift( $links, '<a href="options-general.php">Settings</a>' );
			return $links;
		}

		/**
		 * Redirects all HTTPS pages to HTTP if they're not listed as exceptions
		 *
		 * @link   http://us2.php.net/manual/en/reserved.variables.server.php - $_SERVER['HTTPS'] can be unset, an empty value, or "off" (see Josh Fremer's comment)
		 * @author Ian Dunn <ian@iandunn.name>
		 */
		public function redirectHTTPS() {
			// Return right away if already on HTTP
			if ( ! isset( $_SERVER['HTTPS'] ) || empty( $_SERVER['HTTPS'] ) || $_SERVER['HTTPS'] == 'off' )
				return;

			// Check if current page is listed as an exception
			$exceptions = trim( $this->settings['exceptions'] );
			$isExcepted = false;

			if ( ! empty( $exceptions ) ) {
				$exceptions = explode( "\n", $exceptions );

				foreach ( $exceptions as $ex ) {
					if ( strpos( $_SERVER['REQUEST_URI'], trim( $ex ) ) !== false ) {
						$isExcepted = true;
						break;
					}
				}
			}

			// Redirect from HTTPS to HTTP
			if ( ! $isExcepted )
				wp_redirect( esc_url_raw( 'http://' . $_SERVER['SERVER_NAME'] . '/' . $_SERVER['REQUEST_URI'] ), 301 );
		}

		/**
		 * Displays updates and errors
		 *
		 * @author Ian Dunn <ian@iandunn.name>
		 */
		public function printMessages() {
			foreach ( array( 'updates', 'errors' ) as $type ) {
				if ( $this->options[ $type ] && ( self::DEBUG_MODE || $this->userMessageCount[ $type ] ) ) {
					echo '<div id="message" class="' . ( $type == 'updates' ? 'updated' : 'error' ) . '">';
					foreach ( $this->options[ $type ] as $message ) {
						if ( $message['mode'] == 'user' || self::DEBUG_MODE )
							echo '<p>' . esc_html( $message['message'] ) . '</p>';
					}
					echo '</div>';

					$this->options[ $type ]          = array();
					$this->updatedOptions            = true;
					$this->userMessageCount[ $type ] = 0;
				}
			}
		}

		/**
		 * Queues up a message to be displayed to the user
		 *
		 * @author Ian Dunn <ian@iandunn.name>
		 * @param string $message The text to show the user
		 * @param string $type    'update' for a success or notification message, or 'error' for an error message
		 * @param string $mode    'user' if it's intended for the user, or 'debug' if it's intended for the developer
		 */
		protected function enqueueMessage( $message, $type = 'update', $mode = 'user' ) {
			array_push( $this->options[ $type . 's' ], array(
				'message' => $message,
				'type'    => $type,
				'mode'    => $mode
			) );

			if ( $mode == 'user' )
				$this->userMessageCount[ $type . 's' ] ++;

			$this->updatedOptions = true;
		}

		/**
		 * Destructor
		 * Writes options to the database
		 *
		 * @author Ian Dunn <ian@iandunn.name>
		 */
		public function __destruct() {
			if ( $this->updatedOptions )
				update_option( self::PREFIX . 'options', $this->options );
		}
	} // end forceNonSSL
}

/**
 * Prints an error that the required PHP version wasn't met.
 * This has to be defined outside the class because the class can't be called if the required PHP version isn't installed.
 * Writes options to the database
 *
 * @author Ian Dunn <ian@iandunn.name>
 */
function FNSSL_phpOld() {
	echo '<div id="message" class="error">
		<p>' . FNSSL_NAME . ' requires <strong>PHP ' . FNSSL_REQUIRED_PHP_VERSON . '</strong> in order to work. Please ask your web host about upgrading.</p>
	</div>';
}

// Create an instance
if ( version_compare( PHP_VERSION, FNSSL_REQUIRED_PHP_VERSON, '>=' ) ) {
	if ( class_exists( "forceNonSSL" ) )
		$fnssl = new forceNonSSL();
}
else
	add_action( 'admin_notices', 'FNSSL_phpOld' );