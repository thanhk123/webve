<?php

namespace cBuilder\Helpers;

use cBuilder\Classes\CCBProTemplate;
use cBuilder\Classes\CCBTemplate;

/**
 * Cost Calculator Fields Helper
 */
class CCBFieldsHelper {

	private static $file_field_formats = array(
		'png',
		'jpg/jpeg',
		'gif',
		'webp',
		'svg',
		'pdf',
		'doc/docx',
		'ppt/pptx',
		'pps/ppsx',
		'odt',
		'xls/xlsx',
		'psd',
		'key',
		'mp3',
		'm4a',
		'ogg',
		'wav',
		'mp4',
		'mov',
		'avi',
		'mpg',
		'ogv',
		'3gp',
		'3g2',
		'ai',
		'zip',
		'rar',
		'cdr',
	);

	/** Field templates */
	public static function get_fields_templates() {

		$templates = array(
			'line'         => CCBTemplate::load( 'frontend/fields/cost-line' ),
			'html'         => CCBTemplate::load( 'frontend/fields/cost-html' ),
			'toggle'       => CCBTemplate::load( 'frontend/fields/cost-toggle' ),
			'text-area'    => CCBTemplate::load( 'frontend/fields/cost-text' ),
			'checkbox'     => CCBTemplate::load( 'frontend/fields/cost-checkbox' ),
			'quantity'     => CCBTemplate::load( 'frontend/fields/cost-quantity' ),
			'radio-button' => CCBTemplate::load( 'frontend/fields/cost-radio' ),
			'range-button' => CCBTemplate::load( 'frontend/fields/cost-range' ),
			'drop-down'    => CCBTemplate::load( 'frontend/fields/cost-drop-down' ),
		);

		if ( ccb_pro_active() ) {
			$templates['date-picker']          = CCBProTemplate::load( 'frontend/fields/cost-date-picker' );
			$templates['multi-range']          = CCBProTemplate::load( 'frontend/fields/cost-multi-range' );
			$templates['file-upload']          = CCBProTemplate::load( 'frontend/fields/cost-file-upload' );
			$templates['drop-down-with-image'] = CCBProTemplate::load( 'frontend/fields/cost-drop-down-with-image' );
		}

		return $templates;
	}

	/** Get all posible fields */
	public static function fields() {

		return array(
			array(
				'name'        => __( 'Checkbox', 'cost-calculator-builder' ),
				'alias'       => 'checkbox',
				'type'        => 'checkbox',
				'icon'        => 'fas fa-check-square',
				'description' => 'checkbox fields',
			),
			array(
				'name'        => __( 'Radio', 'cost-calculator-builder' ),
				'alias'       => 'radio',
				'type'        => 'radio-button',
				'icon'        => 'fas fa-dot-circle',
				'description' => 'radio fields',
			),
			array(
				'name'        => __( 'Date Picker', 'cost-calculator-builder' ),
				'alias'       => 'datepicker',
				'type'        => 'date-picker',
				'icon'        => 'fas fa-calendar-alt',
				'description' => 'date picker fields',
			),
			array(
				'name'        => __( 'Range Button', 'cost-calculator-builder' ),
				'alias'       => 'range',
				'type'        => 'range-button',
				'icon'        => 'fas fa-exchange-alt',
				'description' => 'range slider',
			),
			array(
				'name'        => __( 'Drop Down', 'cost-calculator-builder' ),
				'alias'       => 'drop-down',
				'type'        => 'drop-down',
				'icon'        => 'fas fa-chevron-down',
				'description' => 'drop-down fields',
			),
			array(
				'name'        => __( 'Text', 'cost-calculator-builder' ),
				'alias'       => 'text-area',
				'type'        => 'text-area',
				'icon'        => 'fas fa-font',
				'description' => 'text fields',
			),
			array(
				'name'        => __( 'Html', 'cost-calculator-builder' ),
				'alias'       => 'html',
				'type'        => 'html',
				'icon'        => 'fas fa-code',
				'description' => 'html elements',
			),
			array(
				'name'        => __( 'Total', 'cost-calculator-builder' ),
				'alias'       => 'total',
				'type'        => 'total',
				'icon'        => 'fas fa-calculator',
				'description' => 'total fields',
			),
			array(
				'name'        => __( 'Line', 'cost-calculator-builder' ),
				'alias'       => 'line',
				'type'        => 'line',
				'icon'        => 'fas fa-ruler-horizontal',
				'description' => 'horizontal ruler',
			),
			array(
				'name'        => __( 'Quantity', 'cost-calculator-builder' ),
				'alias'       => 'quantity',
				'type'        => 'quantity',
				'icon'        => 'fas fa-hand-peace',
				'description' => 'quantity fields',
			),
			array(
				'name'        => __( 'Multi Range', 'cost-calculator-builder' ),
				'alias'       => 'multi-range',
				'type'        => 'multi-range',
				'icon'        => 'fas fa-exchange-alt',
				'description' => 'multi-range field',
			),
			array(
				'name'        => __( 'Toggle Button', 'cost-calculator-builder' ),
				'alias'       => 'toggle',
				'type'        => 'toggle',
				'icon'        => 'fas fa-toggle-on',
				'description' => 'toggle fields',
			),
			array(
				'name'        => __( 'File Upload', 'cost-calculator-builder' ),
				'alias'       => 'file-upload',
				'type'        => 'file-upload',
				'icon'        => 'fas fa-cloud-upload-alt',
				'description' => 'file upload field',
				'formats'     => self::get_file_field_format_based_on_permission(),
			),
			array(
				'name'        => __( 'Drop Down With Image', 'cost-calculator-builder' ),
				'alias'       => 'drop-down-with-image',
				'type'        => 'drop-down-with-image',
				'icon'        => 'far fa-image',
				'description' => 'drop-down with image field',
			),
		);
	}

	private static function get_file_field_format_based_on_permission() {
		/** check is allowed all */
		if ( defined( 'ALLOW_UNFILTERED_UPLOADS' )
			&& ALLOW_UNFILTERED_UPLOADS !== false
		) {
			return self::$file_field_formats;
		}

		/** check with wp allowed mime types */
		$allowed_file_mime_types = get_allowed_mime_types();
		$allowed_file_types      = array_keys( $allowed_file_mime_types );

		$allowed_types = array();
		foreach ( $allowed_file_types as $type ) {
			$allowed_types = array_merge( $allowed_types, explode( '|', $type ) );
		}

		foreach ( self::$file_field_formats as $field_format ) {
			$allowed = true;
			foreach ( explode( '/', $field_format ) as $sub_type ) {
				if ( ! in_array( $sub_type, $allowed_types, true ) ) {
					$allowed = false;
				}
			}

			if ( ! $allowed && ( $key = array_search( $field_format, self::$file_field_formats, true ) ) !== false ) { //phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.FoundInControlStructure
				unset( self::$file_field_formats[ $key ] );
			}
		}

		return self::$file_field_formats;
	}
}
