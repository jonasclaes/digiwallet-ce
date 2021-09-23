<?php

/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      0.0.1
 *
 * @package    DigiWalletCE
 * @subpackage DigiWalletCE/includes
 */

namespace DigiWalletCE;

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      0.0.1
 * @package    DigiWalletCE
 * @subpackage DigiWalletCE/includes
 * @author     Jonas Claes <jonas@jonasclaes.be>
 */
class DigiWalletCEActivator {

    /**
     * The unique identifier of this plugin.
     *
     * @since    0.0.1
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected static $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    0.0.1
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected static $version;

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    0.0.1
	 */
	public static function activate() {
        if ( defined( 'PLUGIN_NAME_VERSION' ) ) {
            self::$version = PLUGIN_NAME_VERSION;
        } else {
            self::$version = '0.0.1';
        }
        self::$plugin_name = 'digiwallet-ce';

        $plugin_public = new DigiWalletCEPublic( self::get_plugin_name(), self::get_version() );

        $plugin_public->flush_rules();

        global $wpdb;

        $charsetCollate = $wpdb->get_charset_collate();
        $tableNameTransactions = $wpdb->prefix . "digiwalletce_transactions";
        $sql = "CREATE TABLE $tableNameTransactions (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            timestamp datetime DEFAULT NOW() NOT NULL,
            transactionId varchar(10) NOT NULL,
            method varchar(20) NOT NULL,
            status varchar(20) DEFAULT 'unpaid' NOT NULL,
            amount mediumint(9) NOT NULL DEFAULT 0,
            PRIMARY KEY (id)
        ) $charsetCollate;";

        require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
	}

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     0.0.1
     * @return    string    The name of the plugin.
     */
    public static function get_plugin_name() {
        return self::$plugin_name;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     0.0.1
     * @return    string    The version number of the plugin.
     */
    public static function get_version() {
        return self::$version;
    }
}
