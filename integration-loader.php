<?php
/**
 * Integration loader. Determines if the integration should execute and if yes, execute it properly.
 *
 * When this file is loaded, we already know Layouts are active, theme integration support is loaded and it has
 * correct API version.
 *
 * See WPDDL_Theme_Integration_Abstract for details.
 *
 * @todo This class name has to be unique. Use pattern "WPDDL_Theme_Name_Integration".
 */
final class WPDDL_Integration_Boilerplate extends WPDDL_Theme_Integration_Abstract {


	/**
	 * Theme-specific initialization.
	 *
	 * @return bool|WP_Error True when the integration was successful or a WP_Error with a sensible message
	 *     (which can be displayed to the user directly).
	 */
	protected function initialize() {

		// Setup the autoloader
		$autoloader = WPDDL_Theme_Integration_Autoloader::getInstance();
		$autoloader->addPath( dirname( __FILE__ ) . '/application' );

		// Run the integration setup
		$integration = WPDDL_Integration_Setup::getInstance();
		$result = $integration->run();

		return $result;
	}


	/**
	 * Determine whether the expected theme is active and the integration can begin.
	 *
	 * @return bool
	 * @todo Replace this by your custom logic.
	 */
	protected function is_theme_active() {
		return function_exists( 'twentyfifteen_setup' );
	}


	/**
	 * Supported theme name (as would wp_get_theme() return).
	 *
	 * @return string
	 * @todo Replace this by your theme name.
	 */
	protected function get_theme_name() {
		return 'Boilerplate';
	}


}


// @todo Update the class name.
WPDDL_Integration_Boilerplate::get_instance();