<?php

/**
 * Plugin Name: NoodleZero To Door Shipping Method
 * Plugin URI: https://sk8.tech/
 * Description: To Door Shipping Method for NoodleZero
 * Version: 1.0.0
 * Author: SK8Tech
 * Author URI: https://sk8.tech/
 * License: GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Domain Path: /lang
 * Text Domain: noodlezero-to-door-shipping-method
 */

if (!defined('WPINC')) {
	die;
}
/*
 * Check if WooCommerce is active
 */
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {

	function to_door_shipping_method() {
		if (!class_exists('To_Door_Shipping_Method')) {
			class To_Door_Shipping_Method extends WC_Shipping_Method {
				/**
				 * Constructor for your shipping class
				 *
				 * @access public
				 * @return void
				 */
				public function __construct($instance_id = 0) {
					$this->id = 'noodlezero_to_door';
					$this->instance_id = absint($instance_id);
					$this->method_title = __('To Door Shipping', 'noodlezero_to_door');
					$this->method_description = __('To Door Shipping Method for NoodleZero', 'noodlezero_to_door');

					// Availability & Countries
					$this->availability = 'including';
					$this->countries = array(
						'NZ', // New Zealand
					);
					
					// New Zealand
					$this->enabled = isset($this->settings['enabled']) ? $this->settings['enabled'] : 'yes';
					$this->title = isset($this->settings['title']) ? $this->settings['title'] : __('To Door Shipping', 'noodlezero_to_door');
					$this->combo = isset($this->settings['combo']) ? $this->settings['combo'] : __('20', 'noodlezero_to_door');
					$this->nzmiddleearthisland = isset($this->settings['nzmiddleearthisland']) ? $this->settings['nzmiddleearthisland'] : __('4000', 'noodlezero_to_door');
					$this->nznorthisland = isset($this->settings['nznorthisland']) ? $this->settings['nznorthisland'] : __('4000', 'noodlezero_to_door');
					$this->nzsouthisland = isset($this->settings['nzsouthisland']) ? $this->settings['nzsouthisland'] : __('4000', 'noodlezero_to_door');
					$this->nzeastisland = isset($this->settings['nzeastisland']) ? $this->settings['nzeastisland'] : __('4000', 'noodlezero_to_door');
					$this->nzwestisland = isset($this->settings['nzwestisland']) ? $this->settings['nzwestisland'] : __('4000', 'noodlezero_to_door');

				}

				/**
				 * Init your settings
				 *
				 * @access public
				 * @return void
				 */
				function init() {
					// Load the settings API
					$this->init_form_fields();
					$this->init_settings();

					// Save settings in admin if you have any defined
					add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
				}

				/**
				 * Define settings field for this shipping
				 * @return void
				 */
				function init_form_fields() {

					$this->form_fields = array(

						'enabled' => array(
							'title' => __('Enable', 'noodlezero_to_door'),
							'type' => 'checkbox',
							'description' => __('Enable this shipping.', 'noodlezero_to_door'),
							'default' => 'yes',
						),

						'title' => array(
							'title' => __('Title', 'noodlezero_to_door'),
							'type' => 'text',
							'description' => __('Title to be display on site', 'noodlezero_to_door'),
							'default' => __('To Door Shipping', 'noodlezero_to_door'),
						),

						'combo' => array(
							'title' => __('Combo', 'noodlezero_to_door'),
							'type' => 'number',
							'description' => __('No. of products to allow free shipping', 'noodlezero_to_door'),
							'default' => 20,
						),

						'nzmiddleearthisland' => array(
							'title' => __('NZ Middle Earth ZIPs', 'noodlezero_to_door'),
							'type' => 'text',
							'description' => __('Use , to seperate', 'noodlezero_to_door'),
							'default' => "4000",
						),

						'nznorthisland' => array(
							'title' => __('NZ North Island ZIPs', 'noodlezero_to_door'),
							'type' => 'text',
							'description' => __('Use , to seperate', 'noodlezero_to_door'),
							'default' => "4000",
						),

						'nzsouthisland' => array(
							'title' => __('NZ South Island ZIPs', 'noodlezero_to_door'),
							'type' => 'text',
							'description' => __('Use , to seperate', 'noodlezero_to_door'),
							'default' => "4000",
						),

						'nzeastisland' => array(
							'title' => __('NZ East Island ZIPs', 'noodlezero_to_door'),
							'type' => 'text',
							'description' => __('Use , to seperate', 'noodlezero_to_door'),
							'default' => "4000",
						),

						'nzwestisland' => array(
							'title' => __('NZ West Island ZIPs', 'noodlezero_to_door'),
							'type' => 'text',
							'description' => __('Use , to seperate', 'noodlezero_to_door'),
							'default' => "4000",
						),

					);

				}

				/**
				 * This function is used to calculate the shipping cost. Within this function we can check for weights, dimensions and other parameters.
				 *
				 * @access public
				 * @param mixed $package
				 * @return void
				 */
				public function calculate_shipping($package = Array()) {

					$weight = 0;
					$quantity = 0;
					$country = $package["destination"]["country"];

					if ($country != "NZ") {
						return;
					}

					/**
					 * Use Postcode/ZIP to determine if this method applies
					 * @author Jack
					 */
					$postcode = $package["destination"]["postcode"];
					$postcode = rtrim($postcode, " ");

					if (strstr($this->nzmiddleearthisland, $postcode) === FALSE &&
						strstr($this->nznorthisland, $postcode) === FALSE &&
						strstr($this->nzsouthisland, $postcode) === FALSE &&
						strstr($this->nzeastisland, $postcode) === FALSE &&
						strstr($this->nzwestisland, $postcode) === FALSE &&
						$country == "NZ") {
						// The Shipping post code is not found in the pre-configured zip area.
						// To Door shipping not available
						return;
					}

					$quantity = WC()->cart->get_cart_contents_count();

					$weight = wc_get_weight($weight, 'kg');

					if ($quantity >= $this->combo) {

						$rate = array(
							'id' => $this->id,
							'label' => "FREE! " . $this->title,
							'cost' => 0,
						);

						$this->add_rate($rate);
					} else {

						$rate = array(
							'id' => $this->id,
							'label' => $this->title,
							'cost' => 10,
						);

						$this->add_rate($rate);
					}

				}
			}
		}
	}

	add_action('woocommerce_shipping_init', 'to_door_shipping_method');

	function add_to_door_shipping_method($methods) {
		$methods[] = 'To_Door_Shipping_Method';
		return $methods;
	}

	add_filter('woocommerce_shipping_methods', 'add_to_door_shipping_method');

	function to_door_validate_order($posted) {

		$packages = WC()->shipping->get_packages();

		$chosen_methods = WC()->session->get('chosen_shipping_methods');

		if (is_array($chosen_methods) && in_array('noodlezero_to_door', $chosen_methods)) {

			foreach ($packages as $i => $package) {

				if ($chosen_methods[$i] != "noodlezero_to_door") {

					continue;

				}

				$To_Door_Shipping_Method = new To_Door_Shipping_Method();
				$weightLimit = (int) $To_Door_Shipping_Method->settings['weight'];
				$weight = 0;

				foreach ($package['contents'] as $item_id => $values) {
					$_product = $values['data'];
					$weight = $weight + $_product->get_weight() * $values['quantity'];
				}

				$weight = wc_get_weight($weight, 'kg');

				if ($weight > $weightLimit) {

					$message = sprintf(__('Sorry, %d kg exceeds the maximum weight of %d kg for %s', 'noodlezero_to_door'), $weight, $weightLimit, $To_Door_Shipping_Method->title);

					$messageType = "error";

					if (!wc_has_notice($message, $messageType)) {

						wc_add_notice($message, $messageType);

					}
				}
			}
		}
	}

	add_action('woocommerce_review_order_before_cart_contents', 'to_door_validate_order', 10);
	add_action('woocommerce_after_checkout_validation', 'to_door_validate_order', 10);
}
