<?php

namespace cBuilder\Classes;

use cBuilder\Classes\Database\Orders;
use cBuilder\Classes\Database\Payments;
use cBuilder\Helpers\CCBConditionsHelper;
use cBuilder\Helpers\CCBFieldsHelper;

class CCBUpdatesCallbacks {

	/**
	 * 2.3.2
	 * Update Payments table total column.
	 */
	public static function ccb_update_payments_table_total_column() {
		global $wpdb;

		$payment_table = Payments::_table();
		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW COLUMNS FROM `%1s` LIKE %s;', $payment_table, 'total' ) ) ) {

			$wpdb->query(
				$wpdb->prepare(
					"ALTER TABLE `%1s` CHANGE `total` `total` double NOT NULL DEFAULT '0.00000000';",
					$payment_table
				)
			);
		}
	}

	/**
	 * 2.3.0
	 * Add Payments table.
	 */
	public static function ccb_add_payments_table() {
		Payments::create_table();
	}

	/**
	 * 2.3.0
	 * Move Payments data from order to payment table.
	 */
	public static function move_from_order_to_payment_table() {
		global $wpdb;

		$orders = Orders::get_all();

		foreach ( $orders as $order ) {
			$exist = Payments::get( 'order_id', $order['id'] );
			if ( null !== $exist ) {
				continue;
			}

			$paymentType = Payments::$defaultType;
			if ( ! empty( $order['payment_method'] ) && in_array( $order['payment_method'], Payments::$typeList, true ) ) {
				$paymentType = $order['payment_method'];

			}

			$payment = array(
				'order_id'   => $order['id'],
				'type'       => $paymentType,
				'status'     => $order['status'] ? $order['status'] : Payments::$defaultStatus,
				'total'      => $order['total'],
				'currency'   => $order['currency'],
				'created_at' => wp_date( 'Y-m-d H:i:s' ),
				'updated_at' => wp_date( 'Y-m-d H:i:s' ),
			);

			if ( Payments::$completeStatus === $payment['status'] ) {
				$payment['paid_at'] = wp_date( 'Y-m-d H:i:s' );
			}
			Payments::insert( $payment );
		}
		self::drop_payment_fields_from_order_table();
	}

	/**
	 * 2.3.0
	 * Update Orders table, remove payment_method, currency, total
	 */
	public static function drop_payment_fields_from_order_table() {
		global $wpdb;

		try {
			if ( $wpdb->get_var( $wpdb->prepare( 'SHOW COLUMNS FROM `%1s` LIKE %s;', Orders::_table(), 'payment_method' ) ) ) {
				$wpdb->query( $wpdb->prepare( 'ALTER TABLE `%1s` DROP  COLUMN `payment_method`;', Orders::_table() ) );
			}

			if ( $wpdb->get_var( $wpdb->prepare( 'SHOW COLUMNS FROM `%1s` LIKE %s;', Orders::_table(), 'currency' ) ) ) {
				$wpdb->query( $wpdb->prepare( 'ALTER TABLE `%1s` DROP  COLUMN `currency`;', Orders::_table() ) );
			}

			if ( $wpdb->get_var( $wpdb->prepare( 'SHOW COLUMNS FROM `%1s` LIKE %s;', Orders::_table(), 'total' ) ) ) {
				$wpdb->query( $wpdb->prepare( 'ALTER TABLE `%1s` DROP  COLUMN `total`;', Orders::_table() ) );
			}
		} catch ( \Exception $e ) {
			ccb_write_log( $e );
		}
	}
	/**
	 * Version 2.3.0
	 * add file-upload styles
	 */
	public static function cc_add_file_upload_style() {
		$fileUploadKey       = 'file-upload';
		$calculatorList      = CCBCalculators::get_calculator_list();
		$defaultCustomFields = CCBCustomFields::custom_fields();
		$defaultCustomStyle  = CCBCustomFields::custom_default_styles();

		foreach ( $calculatorList as $calculator ) {
			$calculatorCustomFields = get_post_meta( $calculator['id'], 'ccb-custom-fields', true );
			$calculatorCustomStyles = get_post_meta( $calculator['id'], 'ccb-custom-styles', true );

			if ( ! array_key_exists( $fileUploadKey, $calculatorCustomFields ) ) {
				$calculatorCustomFields[ $fileUploadKey ] = $defaultCustomFields[ $fileUploadKey ];
				$calculatorCustomStyles[ $fileUploadKey ] = $defaultCustomStyle[ $fileUploadKey ];

				update_post_meta( $calculator['id'], 'ccb-custom-fields', apply_filters( 'stm_ccb_sanitize_array', $calculatorCustomFields ) );
				update_post_meta( $calculator['id'], 'ccb-custom-styles', apply_filters( 'stm_ccb_sanitize_array', $calculatorCustomStyles ) );
			}
		}
	}

	/**
	 * Version 2.3.0
	 * Change custom styles structure
	 * buttons instead of submit-button
	 * input-fields instead of quantity
	 */
	public static function cc_style_input_fields_and_buttons() {
		$changeStyleElements = array(
			'quantity'      => 'input-fields',
			'submit-button' => 'buttons',
		);

		$calculatorList = CCBCalculators::get_calculator_list();

		foreach ( $calculatorList as $calculator ) {
			$calculatorCustomFields = get_post_meta( $calculator['id'], 'ccb-custom-fields', true );
			$calculatorCustomStyles = get_post_meta( $calculator['id'], 'ccb-custom-styles', true );

			foreach ( $changeStyleElements as $keyToChange => $newKey ) {
				if ( array_key_exists( $newKey, $calculatorCustomStyles ) && array_key_exists( $newKey, $calculatorCustomFields ) ) {
					continue;
				}
				if ( 'buttons' === $newKey ) {
					/** change name */
					$calculatorCustomFields[ $keyToChange ]['name'] = 'buttons';

					/** add new style */
					$calculatorCustomFields[ $keyToChange ]['fields'][]      = CCBCustomFields::generate_border_radius( 0, 100, 1, 4, 4, 4, 4, 4, null, null, 'px' );
					$calculatorCustomStyles[ $keyToChange ]['border-radius'] = '4px 4px 4px 4px';
				} else {

					/** change name */
					$calculatorCustomFields[ $keyToChange ]['name'] = 'input-fields';

					/** change field name */
					$quantityActiveEffectName = 'quantity-active-effects';
					$key                      = array_search( $quantityActiveEffectName, array_column( $calculatorCustomFields[ $keyToChange ]['fields'], 'name' ), true );
					if ( false !== $key ) {
						$calculatorCustomFields[ $keyToChange ]['fields'][ $key ]['name'] = 'input-active-effects';
					}
				}

				$calculatorCustomFields[ $newKey ] = $calculatorCustomFields[ $keyToChange ];
				$calculatorCustomStyles[ $newKey ] = $calculatorCustomStyles[ $keyToChange ];

				unset( $calculatorCustomFields[ $keyToChange ] );
				unset( $calculatorCustomStyles[ $keyToChange ] );

				update_post_meta( $calculator['id'], 'ccb-custom-fields', apply_filters( 'stm_ccb_sanitize_array', $calculatorCustomFields ) );
				update_post_meta( $calculator['id'], 'ccb-custom-styles', apply_filters( 'stm_ccb_sanitize_array', $calculatorCustomStyles ) );
			}
		}
	}

	/**
	 * Version 2.2.7
	 * Save setting calculators
	 */
	public static function cc_or_and_conditions() {
		$calculatorList = CCBCalculators::get_calculator_list();
		CCBConditionsHelper::updateConditionStructureToMakeMultiple( $calculatorList );
	}

	/**
	 * Version 2.2.6
	 * Save setting calculators
	 */
	public static function cc_update_calc_settings() {
		$calculators = self::get_calculators();

		foreach ( $calculators as $calculator ) {
			$settings = get_option( 'stm_ccb_form_settings_' . $calculator->ID );

			update_option( 'stm_ccb_form_settings_' . sanitize_text_field( $calculator->ID ), apply_filters( 'stm_ccb_sanitize_array', $settings ) );
		}
	}

	/**
	 * Version 2.2.6
	 * Create wp_cc_order table
	 */
	public static function cc_create_orders_table() {
		Orders::create_table();
	}

	/**
	 * Version 2.2.5
	 * Update custom fields postmeta
	 * add datepicker default style data
	 */
	public static function cc_add_custom_styles_to_datepicker() {
		$datePickerKey       = 'date-picker';
		$calculatorList      = CCBCalculators::get_calculator_list();
		$defaultCustomFields = CCBCustomFields::custom_fields();
		$defaultCustomStyle  = CCBCustomFields::custom_default_styles();

		foreach ( $calculatorList as $calculator ) {
			$calculatorCustomFields = get_post_meta( $calculator['id'], 'ccb-custom-fields', true );
			$calculatorCustomStyles = get_post_meta( $calculator['id'], 'ccb-custom-styles', true );

			$calculatorCustomFields[ $datePickerKey ] = $defaultCustomFields[ $datePickerKey ];
			$calculatorCustomStyles[ $datePickerKey ] = $defaultCustomStyle[ $datePickerKey ];

			update_post_meta( $calculator['id'], 'ccb-custom-fields', apply_filters( 'stm_ccb_sanitize_array', $calculatorCustomFields ) );
			update_post_meta( $calculator['id'], 'ccb-custom-styles', apply_filters( 'stm_ccb_sanitize_array', $calculatorCustomStyles ) );
		}
	}

	/**
	 * Version 2.2.5
	 * update condition actions ( set values without spaces )
	 */
	public static function cc_update_all_calculators_condition_actions() {
		$calculatorList = CCBCalculators::get_calculator_list();
		CCBConditionsHelper::updateConditionActions( $calculatorList );
	}

	/**
	 *  Version 2.2.4
	 *  update condition node coordinates
	 *  and add links target data
	 *  based on old logic
	 */
	public static function cc_update_all_calculators_conditions_coordinates() {
		$calculatorList = CCBCalculators::get_calculator_list();
		CCBConditionsHelper::recalculateCoordinates( $calculatorList );
	}

	public static function get_calculators() {
		$calculators = new \WP_Query(
			array(
				'posts_per_page' => - 1,
				'post_type'      => 'cost-calc',
				'post_status'    => array( 'publish' ),
			)
		);

		return $calculators->posts;
	}

	/**
	 * Change old icons
	 */
	public static function update_icons() {
		$calculators = self::get_calculators();
		if ( ! empty( $calculators ) ) {
			foreach ( $calculators as $calculator ) {
				$clone  = array();
				$fields = get_post_meta( $calculator->ID, 'stm-fields', true );
				if ( ! empty( $fields ) ) {
					foreach ( $fields as $field ) {
						foreach ( CCBFieldsHelper::fields() as $data ) {
							if ( $field['type'] === $data['name'] ) {
								$field['icon'] = $data['icon'];
							}
						}
						$clone[] = $field;
					}
				}

				update_post_meta( (int) $calculator->ID, 'stm-fields', apply_filters( 'stm_ccb_sanitize_array', $clone ) );
			}
		}
	}

	public static function add_header_title_options() {
		$calculators = self::get_calculators();
		foreach ( $calculators as $calculator ) {
			$customs = get_post_meta( $calculator->ID, 'ccb-custom-fields', true );
			if ( ! empty( $customs ) && is_array( $customs ) && empty( $customs['headers'] ) ) {
				$customs['headers'] = array(
					'fields' => array(
						CCBCustomFields::generate_text_settings(
							array(
								'label' => 'Text-color',
								'value' => '#000000',
							),
							array(
								'label'     => 'Font-size',
								'min'       => 0,
								'max'       => 100,
								'step'      => 1,
								'value'     => 22,
								'dimension' => 'px',
							),
							array(
								'label'     => 'Letter-spacing',
								'min'       => 0,
								'max'       => 100,
								'step'      => 1,
								'value'     => 0,
								'dimension' => 'px',
							),
							array(
								'blur'        => array(
									'min'       => 0,
									'max'       => 20,
									'step'      => 1,
									'value'     => 0,
									'dimension' => 'px',
								),
								'opacity'     => array(
									'min'       => 0,
									'max'       => 1,
									'step'      => 0.01,
									'value'     => 0,
									'dimension' => 'px',
								),
								'shift_right' => array(
									'min'       => - 40,
									'max'       => 40,
									'step'      => 1,
									'value'     => 0,
									'dimension' => 'px',
								),
								'shift_down'  => array(
									'min'       => - 40,
									'max'       => 40,
									'step'      => 1,
									'value'     => 0,
									'dimension' => 'px',
								),
								'color'       => '#ffffff',
							),
							array(
								'value' => '700',
							),
							array(
								'value' => 'normal',
							)
						),
					),
					'name'   => 'headers',
				);
				update_post_meta( (int) $calculator->ID, 'ccb-custom-fields', apply_filters( 'stm_ccb_sanitize_array', $customs ) );
			}
		}
	}

	public static function update_recaptcha_options() {
		$calculators = self::get_calculators();

		foreach ( $calculators as $calculator ) {
			$settings = get_option( 'stm_ccb_form_settings_' . $calculator->ID );

			if ( ! empty( $settings ) && isset( $settings['recaptcha'] ) ) {

				$captcha = $settings['recaptcha'];

				$enable     = ! empty( $captcha['enable'] ) ? $captcha['enable'] : '';
				$site_key   = ! empty( $captcha['siteKey'] ) ? $captcha['siteKey'] : '';
				$secret_key = ! empty( $captcha['secretKey'] ) ? $captcha['secretKey'] : '';

				if ( empty( $settings['recaptcha']['v3'] ) ) {
					$settings['recaptcha'] = array(
						'enable'  => $enable,
						'type'    => 'v2',
						'options' => array(
							'v2' => 'Google reCAPTCHA v2',
							'v3' => 'Google reCAPTCHA v3',
						),
						'v2'      => array(
							'siteKey'   => $site_key,
							'secretKey' => $secret_key,
						),
						'v3'      => array(
							'siteKey'   => '',
							'secretKey' => '',
						),
					);
				}

				update_option( 'stm_ccb_form_settings_' . sanitize_text_field( $calculator->ID ), apply_filters( 'stm_ccb_sanitize_array', $settings ) );
			}
		}
	}

	/**
	 * Version 2.1.0
	 */
	public static function update_condition_data() {
		$calculators = self::get_calculators();

		foreach ( $calculators as $calculator ) {
			$conditions = get_post_meta( $calculator->ID, 'stm-conditions', true );

			if ( ! empty( $conditions['links'] ) ) {
				foreach ( $conditions['links'] as $index => $link ) {

					$options_from = $link['options_from'];
					$condition    = isset( $link['condition'] ) ? $link['condition'] : array();
					$changed      = true;
					$options      = ! empty( $options_from['options'] ) ? $options_from['options'] : array();

					if ( isset( $condition ) ) {
						foreach ( $condition as $condition_key => $condition_item ) {
							foreach ( $options as $option_index => $option ) {
								if ( $condition_item['value'] === $option['optionValue'] && $changed ) {
									$condition[ $condition_key ]['key'] = $option_index;
									$changed                            = false;
								}
							}
						}
					}
					$conditions['links'][ $index ]['condition'] = $condition;
				}
			}

			update_post_meta( (int) $calculator->ID, 'stm-conditions', apply_filters( 'stm_ccb_sanitize_array', $conditions ) );
		}
	}

	/**
	 *  Version 2.1.1
	 */
	public static function condition_restructure() {
		$calculators = self::get_calculators();

		foreach ( $calculators as $calculator ) {
			$conditions = get_post_meta( $calculator->ID, 'stm-conditions', true );

			if ( ! empty( $conditions['nodes'] ) ) {
				$conditions['nodes'] = array_map(
					function ( $node ) {
						$node['options'] = isset( $node['options']['alias'] ) ? $node['options']['alias'] : $node['options'];

						return $node;
					},
					$conditions['nodes']
				);
			}

			if ( ! empty( $conditions['links'] ) ) {
				$conditions['links'] = array_map(
					function ( $link ) {
						$link = self::replace_options( $link );
						if ( isset( $link['condition'] ) ) {
							$link['condition'] = array_map(
								function ( $condition ) {
									$condition = self::replace_options( $condition, true );

									return $condition;
								},
								$link['condition']
							);
						}

						return $link;
					},
					$conditions['links']
				);
			}

			update_post_meta( (int) $calculator->ID, 'stm-conditions', apply_filters( 'stm_ccb_sanitize_array', $conditions ) );
		}
	}

	public static function generate_hover_effects() {
		$calculators = self::get_calculators();

		foreach ( $calculators as $calculator ) {
			$customs = get_post_meta( $calculator->ID, 'ccb-custom-fields', true );
			$styles  = get_post_meta( $calculator->ID, 'ccb-custom-styles', true );

			if ( ! empty( $customs ) && is_array( $customs ) && ! empty( $customs['submit-button'] ) ) {
				$submit_btn = $customs['submit-button'];

				if ( empty( $submit_btn['fields'][5] ) ) {
					$customs['submit-button']['fields'][] = CCBCustomFields::generate_effects(
						array(
							'name'        => 'submit-hover-effects',
							'label'       => 'Hover-effects',
							'data'        => array(
								array(
									'label'   => 'Background-color',
									'name'    => 'background-color',
									'type'    => 'single-color',
									'default' => '#047b47',
									'value'   => '#047b47',

								),
								array(
									'label'   => 'Border-color',
									'name'    => 'border-color',
									'type'    => 'single-color',
									'default' => '#bdc9ca',
									'value'   => '#bdc9ca',

								),
								array(
									'label'   => 'Font-color',
									'name'    => 'font-color',
									'type'    => 'single-color',
									'default' => '#fff',
									'value'   => '#fff',
								),
							),
							'effect_type' => 'hover',
						)
					);
				}

				update_post_meta( (int) $calculator->ID, 'ccb-custom-fields', apply_filters( 'stm_ccb_sanitize_array', $customs ) );
			}

			if ( ! empty( $styles ) && ! empty( $styles['checkbox'] ) ) {
				$checkbox                 = $styles['checkbox'];
				$checkbox['bg_color']     = isset( $checkbox['bg_color'] ) && '#00b163' === $checkbox['bg_color'] ? '#fff' : $checkbox['bg_color'];
				$checkbox['checkedColor'] = isset( $checkbox['checkedColor'] ) && '#fff' === $checkbox['bg_color'] ? '#00b163' : $checkbox['checkedColor'];
				$styles['checkbox']       = $checkbox;

				update_post_meta( (int) $calculator->ID, 'ccb-custom-styles', apply_filters( 'stm_ccb_sanitize_array', $styles ) );
			}
		}
	}

	private static function replace_options( $param, $camel_case = false ) {
		$option_to_key   = $camel_case ? 'optionTo' : 'options_to';
		$option_from_key = $camel_case ? 'optionFrom' : 'options_from';

		$param[ $option_to_key ]   = ! empty( $param[ $option_to_key ] ) && is_array( $param[ $option_to_key ] ) && ! empty( $param[ $option_to_key ]['alias'] ) ? $param[ $option_to_key ]['alias'] : $param[ $option_to_key ];
		$param[ $option_from_key ] = ! empty( $param[ $option_from_key ] ) && is_array( $param[ $option_from_key ] ) && ! empty( $param[ $option_from_key ]['alias'] ) ? $param[ $option_from_key ]['alias'] : $param[ $option_from_key ];

		return $param;
	}

	public static function rename_woocommerce_settings() {
		$calculators = self::get_calculators();

		foreach ( $calculators as $calculator ) {
			$settings = get_option( 'stm_ccb_form_settings_' . $calculator->ID );

			if ( ! empty( $settings ) && isset( $settings['wooCommerce'] ) ) {
				$settings['woo_checkout'] = $settings['wooCommerce'];
				unset( $settings['wooCommerce'] );

				update_option( 'stm_ccb_form_settings_' . sanitize_text_field( $calculator->ID ), apply_filters( 'stm_ccb_sanitize_array', $settings ) );
			}
		}
	}

	public static function generate_active_effects() {
		$calculators = self::get_calculators();

		foreach ( $calculators as $calculator ) {
			$customs = get_post_meta( $calculator->ID, 'ccb-custom-fields', true );

			if ( ! empty( $customs ) && is_array( $customs ) ) {

				if ( ! empty( $customs['quantity']['fields'] ) ) {

					$active_effect_name = 'quantity-active-effects';
					$key                = array_search( $active_effect_name, array_column( $customs['quantity']['fields'], 'name' ), true );

					if ( empty( $key ) || empty( $customs['quantity']['fields'][ $key ] ) ) {
						$customs['quantity']['fields'][] = self::set_generate_effects( $active_effect_name );
					}
				}

				if ( ! empty( $customs['text-area']['fields'] ) ) {
					$active_effect_name = 'text-area-active-effects';
					$key                = array_search( $active_effect_name, array_column( $customs['text-area']['fields'], 'name' ), true );

					if ( empty( $key ) || empty( $customs['text-area']['fields'][ $key ] ) ) {
						$customs['text-area']['fields'][] = self::set_generate_effects( $active_effect_name );
					}
				}

				update_post_meta( (int) $calculator->ID, 'ccb-custom-fields', apply_filters( 'stm_ccb_sanitize_array', $customs ) );

			}
		}
	}

	protected static function set_generate_effects( $active_effect_name ) {
		return CCBCustomFields::generate_effects(
			array(
				'name'        => $active_effect_name,
				'label'       => 'Active-effects',
				'data'        => array(
					array(
						'label'   => 'Background-color',
						'name'    => 'background-color',
						'type'    => 'effects',
						'default' => '#fff',
						'value'   => '#fff',

					),
					array(
						'label'   => 'Border-color',
						'name'    => 'border-color',
						'type'    => 'effects',
						'default' => '#00b163',
						'value'   => '#00b163',

					),
					array(
						'label'   => 'Font-color',
						'name'    => 'font-color',
						'type'    => 'effects',
						'default' => '#000',
						'value'   => '#000',
					),
				),
				'effect_type' => 'active',
			)
		);
	}

	public static function ccb_admin_notification_transient() {
		$data = array(
			'show_time'   => DAY_IN_SECONDS * 3 + time(),
			'step'        => 0,
			'prev_action' => '',
		);
		set_transient( 'stm_cost-calculator-builder_notice_setting', $data );
	}
}
