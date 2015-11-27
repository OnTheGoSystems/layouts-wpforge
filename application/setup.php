<?php

/**
 * Singleton for setting up the integration.
 *
 * Note that it doesn't have to have unique name. Because of autoloading, it will be loaded only once (when this
 * integration plugin is operational).
 *
 * @todo Take look at the parent class, explore it's code and figure out if anything needs overriding.
 */
/** @noinspection PhpUndefinedClassInspection */
class WPDDL_Integration_Setup extends WPDDL_Theme_Integration_Setup_Abstract {


	protected function __construct(){
         add_action('init', array('WPDDL_Integration_Framework_Foundation', 'get_instance') );
    }

    /**
	 * Run Integration.
	 *
	 * @return bool|WP_Error True when the integration was successful or a WP_Error with a sensible message
	 *     (which can be displayed to the user directly).
	 */
	public function run() {
        $this->addLayoutRowTypes();
		return parent::run();
	}

	public function add_bootstrap_support(){
        return null;
    }

    public function frontend_enqueue(){
            parent::frontend_enqueue();
    }

	/**
	 * @todo Set supported theme version here.
	 * @return string
	 */
	protected function get_supported_theme_version() {
		return '3.5.3';
	}


	/**
	 * Build URL of an resource from path relative to plugin's root directory.
	 *
	 * @param string $relative_path Some path relative to the plugin's root directory.
	 * @return string URL of the given path.
	 */
	protected function get_plugins_url( $relative_path ) {
		return plugins_url( '/../' . $relative_path , __FILE__ );
	}


	/**
	 * Get list of templates supported by Layouts with this theme.
	 *
	 * @return array Associative array with template file names as keys and theme names as values.
	 * @todo Update the array of templates according to what the integration plugin offers
	 */
	protected function get_supported_templates() {
		return array(
			'template-page.php' => __( 'Template page', 'ddl-layouts' )
		);
	}


	/**
	 * Layouts Support
	 *
	 * @todo Implement theme-specific logic here. For example, you may want to:
	 *     - if theme has it's own loop, replace it by the_ddlayout()
	 *     - remove headers, footer, sidebars, menus and such, if achievable by filters
	 *     - otherwise you will have to resort to something like redirecting templates (see the template router below)
	 *     - add $this->clear_content() to some filters to remove unwanted site structure elements
	 */
	protected function add_layouts_support() {

		parent::add_layouts_support();
        $this->layouts_menu_cells_overrides();
		/** @noinspection PhpUndefinedClassInspection */
		WPDDL_Integration_Theme_Template_Router::get_instance();

	}

    protected function layouts_menu_cells_overrides(){
        add_filter('ddl-menu_has_container', array(&$this, 'return_false'), 99, 2 );
        add_filter('ddl-wrap_menu_start', array(&$this, 'wrap_menu_start'), 10, 3 );
        add_filter('ddl-wrap_menu_end', array(&$this, 'wrap_menu_end'), 10, 3 );
        add_filter('ddl-menu_toggle_controls', array(&$this, 'clear_content'), 10, 3 );
        add_filter( 'ddl-get_menu_walker', array(&$this, 'get_menu_walker'), 10, 2 );
        add_action( 'ddl-menu_additional_fields', array(&$this, 'menu_additional_fields') );
        add_filter( 'ddl-get_menu_class', array(&$this, 'add_menu_class_if'), 10, 2 );
        add_filter( 'ddl-menu-walker-args', array(&$this, 'add_menu_args'), 10 );
    }

    public function wrap_menu_start( ){
        if( get_ddl_field('menu_dir') === 'nav-horizontal' && get_ddl_field('topbar') ){
            return '<section class="top-bar-section">';
        }
        return '';
    }

    public function wrap_menu_end(){
        if( get_ddl_field('menu_dir') === 'nav-horizontal' && get_ddl_field('topbar') ){
            return '</section>';
        }
        return '';
    }

    public function add_menu_class_if( $class, $menu ){
        $align = get_ddl_field('menu_align');

        if( is_null( $align  ) === false ){
            $class .= ' '.$align;
        }

        if( get_ddl_field('menu_dir') === 'nav-horizontal' && !get_ddl_field('topbar') ){
            $class = ' inline-list';
        }

        return $class;
    }

    public function add_menu_args( $args ){
        if( get_ddl_field('menu_dir') === 'nav-horizontal' && !get_ddl_field('topbar') ){
            $args['flying_class'] = 'no';
        }
        return $args;
    }

    public function return_false( $bool, $menu ){
        if( get_ddl_field('menu_dir') === 'nav-horizontal' ){
            return false;
        } else {
            return true;
        }
    }

	/**
	 * Add custom theme elements to Layouts.
	 *
	 * @todo Setup your custom layouts cell here.
	 */
	protected function add_layouts_cells() {
		// Custom boilerplate cell
		// @todo Remove this one completely after you are done with it.
		$orbit_slider = new WPDDL_Integration_Layouts_Cell_Orbit_Slider();
        $orbit_slider->setup();

		$sidebar_cell = new WPDDL_Integration_Layouts_Cell_Site_title();
		$sidebar_cell->setup();
	}

    private function addLayoutRowTypes() {
        // Site Header
        $cornerstone_header = new WPDDL_Integration_Layouts_Row_Cornerstone_header();
        $cornerstone_header->setup();
    }


	/**
	 * This method can be used to remove all theme settings which are obsolete with the use of Layouts
	 * i.e. "Default Layout" in "Theme Settings"
	 *
	 * @todo You can either use this class for very simple tasks or create dedicated classes in application/theme/settings.
	 */
	protected function modify_theme_settings() {
		// ...
	}

    public function get_menu_walker( $walker, $style ){
        $is_top = get_ddl_field('menu_dir') === 'nav-horizontal' && get_ddl_field('topbar');
        if ( class_exists( 'WPDDL_Theme_Cornerstone_Menu_Walker' ) ){
            $walker = new WPDDL_Theme_Cornerstone_Menu_Walker(
                array(
                    'in_top_bar' => $is_top,
                    'item_type' => 'li'
                )
            );
            return $walker;
        }
        return null;
    }

    public function menu_additional_fields(){
        ob_start();?>
        <p>
            <label for="<?php the_ddl_name_attr('topbar'); ?>" class="ddl-manual-width-190"><?php _e('Foundation top menu', 'ddl-layouts'); ?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="<?php the_ddl_name_attr('topbar'); ?>" value="1" checked />
        </p>
        <p>
            <label for="<?php the_ddl_name_attr('menu_align'); ?>" class="ddl-manual-width-190"><?php _e('Alignment', 'ddl-layouts'); ?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="<?php the_ddl_name_attr('menu_align'); ?>" value="left" checked /><?php _e('Align left', 'ddl-layouts');?> &nbsp;
            <input type="radio" name="<?php the_ddl_name_attr('menu_align'); ?>" value="right" /><?php _e('Align right', 'ddl-layouts');?>
        </p>
        <?php
        echo ob_get_clean();
    }
}