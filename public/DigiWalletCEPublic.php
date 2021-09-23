<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      0.0.1
 *
 * @package    DigiWalletCE
 * @subpackage DigiWalletCE/public
 */

namespace DigiWalletCE;

use \DigiWallet as DigiWallet;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    DigiWalletCE
 * @subpackage DigiWalletCE/public
 * @author     Jonas Claes <jonas@jonasclaes.be>
 */
class DigiWalletCEPublic {

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.0.1
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.0.1
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.0.1
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    0.0.1
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in DigiWalletCE_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The DigiWalletCE_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/plugin-name-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    0.0.1
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in DigiWalletCE_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The DigiWalletCE_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/plugin-name-public.js', array( 'jquery' ), $this->version, false );

	}

    public function rewrite_rules() {
        add_rewrite_tag('%digiWalletCE%', '([^&]+)');
        add_rewrite_tag('%action%', '([^&]+)');
        add_rewrite_tag('%data%', '([^&]+)');
        add_rewrite_tag('%trxid%', '([^&]+)');
        add_rewrite_tag('%method%', '([^&]+)');
        add_rewrite_tag('%amount%', '([^&]+)');

        add_rewrite_rule('^digiWalletCE$', 'index.php?digiWalletCE=1', 'top');
    }

    public function flush_rules() {
        $this->rewrite_rules();
        flush_rewrite_rules();
    }

    public function handle_request() {
        if ( get_query_var('digiWalletCE', false) ) {
            global $wpdb;
            $action = intval(get_query_var('action', ActionTypeEnum::PAY));
            $data = get_query_var('data');
            $method = get_query_var('method');
            $trxId = intval(get_query_var('trxid'));
            $amount = intval(get_query_var('amount'));
            $apiKey = get_option('digiwalletce')['api_key'];
            $outletID = get_option('digiwalletce')['outlet_id'];

            $baseUrl = get_bloginfo("wpurl") . "/digiWalletCE?action=";
            $returnUrl = $baseUrl . ActionTypeEnum::RETURN;
            $cancelUrl = $baseUrl . ActionTypeEnum::CANCELED;
            $reportUrl = $baseUrl . ActionTypeEnum::REPORT;
            $tableName = $wpdb->prefix . "digiwalletce_transactions";

            switch ($action) {
                case ActionTypeEnum::PAY:
                    $startPaymentResult = DigiWallet\Transaction::model($method)
                        ->outletId($outletID)
                        ->amount($amount)
                        ->description("Donatie")
                        ->returnUrl($returnUrl)
                        ->cancelUrl($cancelUrl)
                        ->reportUrl($reportUrl)
                        ->start();

                    $transactionId = $startPaymentResult->transactionId;

                    if ($wpdb->insert($tableName, array("transactionId" => $transactionId, "method" => $method, "amount" => $amount), array("%s", "%s", "%d"))) {
                        header("Location: " . $startPaymentResult->url);
                    } else {
                        header("Location: " . $cancelUrl);
                    }
                    break;

                case ActionTypeEnum::RETURN:
                    $result = $wpdb->get_row("SELECT * FROM $tableName WHERE transactionId = $trxId", ARRAY_A);

                    // Check if transaction was successful.
                    $checkPaymentResult = DigiWallet\Transaction::model($result['method'])
                        ->outletId($outletID)
                        ->transactionId($trxId)
                        ->check();

                    if (!$checkPaymentResult->status) {
                        header("Location: " . $cancelUrl . "&trxid=" . $trxId);
                        exit;
                    }

                    $wpdb->update($tableName, array("status" => "paid"), array("transactionId" => $trxId));

                    ?>
                    <?php get_header() ?>
                    <div id="container" style="text-align: center">
                        <div id="content">
                            <h1>Gelukt!</h1>
                            <h3>Bedankt voor uw donatie van &euro; <?php echo number_format($result['amount'] / 100, 2) ?>!</h3>
                        </div>
                    </div>
                    <?php get_footer() ?>
                    <?php
                    break;

                case ActionTypeEnum::CANCELED:
                    $wpdb->update($tableName, array("status" => "canceled"), array("transactionId" => $trxId));
                    ?>
                    <?php get_header() ?>
                    <div id="container" style="text-align: center">
                        <div id="content">
                            <h1>Oops!</h1>
                            <h3>Uw betaling is jammer genoeg niet gelukt.</h3>
                        </div>
                    </div>
                    <?php get_footer() ?>
                    <?php
                    break;

                case ActionTypeEnum::REPORT:
                    break;
            }
            exit;
        }
    }
}
