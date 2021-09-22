<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin
 */

namespace DigiWalletCE;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin
 * @author     Your Name <email@example.com>
 */
class DigiWalletCEAdmin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

    private $options;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/plugin-name-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/plugin-name-admin.js', array( 'jquery' ), $this->version, false );

	}

    public function add_plugin_page() {
        add_options_page(
            'DigiWalletCE',
            'DigiWallet Community Edition',
            'manage_options',
            'digiwalletce',
            array($this, 'create_admin_page')
        );
    }

    public function create_admin_page() {
        $this->options = get_option( 'digiwalletce' );

        ?>

        <div class="wrap">
            <h2>DigiWallet Community Edition</h2>
            <p></p>

            <form method="post" action="options.php">
                <?php
                    settings_fields( 'digiwalletce_option_group' );
                    do_settings_sections( 'digiwalletce-admin' );
                    submit_button();
                ?>
            </form>
        </div>

        <?php
    }

    public function page_init() {
        register_setting(
                'digiwalletce_option_group',
                'digiwalletce',
                array($this, 'sanitize')
        );

        add_settings_section(
                'digiwalletce_setting_section',
                'Settings',
                array($this, 'section_info'),
                'digiwalletce-admin'
        );

        // Settings fields
        add_settings_field(
                'api_key',
                'API key',
                array($this, 'api_key_callback'),
                'digiwalletce-admin',
                'digiwalletce_setting_section'
        );

        add_settings_field(
            'outlet_id',
            'Outlet ID',
            array($this, 'outlet_id_callback'),
            'digiwalletce-admin',
            'digiwalletce_setting_section'
        );
    }

    public function sanitize($input) {
        $sanitary_values = array();

        if (isset($input['api_key'])) {
            $sanitary_values['api_key'] = sanitize_text_field($input['api_key']);
        }

        if (isset($input['outlet_id'])) {
            $sanitary_values['outlet_id'] = sanitize_text_field($input['outlet_id']);
        }

        return $sanitary_values;
    }

    public function section_info() {

    }

    public function api_key_callback() {
        printf('<input class="regular-text" type="text" name="digiwalletce[api_key]" id="api_key" value="%s">', isset($this->options['api_key']) ? esc_attr($this->options['api_key']) : '');
    }

    public function outlet_id_callback() {
        printf('<input class="regular-text" type="text" name="digiwalletce[outlet_id]" id="outlet_id" value="%s">', isset($this->options['outlet_id']) ? esc_attr($this->options['outlet_id']) : '');
    }
}
