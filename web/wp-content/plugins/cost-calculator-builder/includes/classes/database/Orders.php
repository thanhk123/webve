<?php

namespace cBuilder\Classes\Database;

use cBuilder\Classes\Vendor\DataBaseModel;


class Orders extends DataBaseModel {
	public static $pending  = 'pending';
	public static $paid     = 'paid';

	public static $statusList = ['pending', 'paid'];

	/**
	 * Create Table
	 */
	public static function create_table() {
		global $wpdb;

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$table_name  = self::_table();
		$primary_key = self::$primary_key;

		$sql = "CREATE TABLE {$table_name} (
			id INT UNSIGNED NOT NULL AUTO_INCREMENT,
			calc_id  INT UNSIGNED NOT NULL,
			calc_title VARCHAR(255) DEFAULT NULL,
			total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
			status VARCHAR(20) NOT NULL DEFAULT 'pending',
			currency CHAR(20) NOT NULL,
			payment_method VARCHAR(30) NOT NULL DEFAULT 'no_payments',
			order_details longtext DEFAULT NULL,
			form_details longtext DEFAULT NULL,
			created_at TIMESTAMP NOT NULL,
			updated_at TIMESTAMP NOT NULL,
			PRIMARY KEY ({$primary_key}),
		    INDEX `idx_calc_id` (`calc_id`),
		    INDEX `idx_created_at` (`created_at`),
		    INDEX `idx_status` (`status`),
		    INDEX `idx_total` (`total`)
		) {$wpdb->get_charset_collate()};";

		maybe_create_table($table_name, $sql);
	}

    /**
     * Create Order with payment
     */
    public static function create_order($orderData, $paymentData) {

        self::insert($orderData);
        $orderId = self::insert_id();

        $paymentData['order_id'] = $orderId;
        Payments::insert($paymentData);

        return $orderId;
    }

    /**
     * Update Order by id
     * todo return result later
     */
    public static function update_order( $data, $id ) {
        global $wpdb;

        $sql   = sprintf('SELECT %1$s.* FROM %1$s WHERE %1$s.id = %%d', self::_table() );
        $order = $wpdb->get_row( $wpdb->prepare( $sql, intval($id) ), ARRAY_A );

        $newOrder = array_replace($order, array_intersect_key($data, $order));
        $newOrder['updated_at'] = wp_date('Y-m-d H:i:s');

        self::update( $newOrder, [ 'id' => $id ] );
    }


	/**
	 * Update orders
	 */
	public static function update_orders($d, $args) {
		global $wpdb;
		$table_name = self::_table();
		$sql = $wpdb->prepare("UPDATE $table_name SET status = %s WHERE id IN ($d)", $args);
		$wpdb->get_results($sql);
	}

    /**
     * Get Orders by ids
     */
    public static function get_by_ids( $ids = [] ) {
        if ( empty( $ids )) { return  []; }

        global $wpdb;
        $sql = sprintf(
            'SELECT %1$s.*
					FROM %1$s
					WHERE %1$s.id IN ( %3$s )
					ORDER BY %1$s.%2$s DESC',
            self::_table(),
            static::$primary_key,
            implode(',', $ids )
        );

        return $wpdb->get_results($sql,ARRAY_A);
    }

	/**
	 * Delete Order
	 */
	public static function delete_orders($d, $ids) {
		global $wpdb;
		$table_name = self::_table();
		$sql = $wpdb->prepare("DELETE FROM $table_name WHERE id IN ($d)", $ids);
		$wpdb->get_results($sql);
	}

	/**
	 * Complete Order by id
	 */
	public static function complete_order_by_id($id) {
		global $wpdb;
		$table_name = self::_table();
		return $wpdb->get_results("UPDATE $table_name SET status = 'complete' WHERE id = $id");
	}

	public static function existing_calcs() {
		global $wpdb;
		$table_name = self::_table();
		return $wpdb->get_results("SELECT DISTINCT calc_id, calc_title FROM $table_name", ARRAY_A);
	}

    /**
     * Get total orders
     */
    public static function get_total_orders( $calcIds = [], $paymentMethod = '', $paymentStatus = '') {
        global $wpdb;

        $sql = sprintf(
            'SELECT COUNT(%1$s.id)
                    FROM %1$s
                    LEFT JOIN %2$s ON %1$s.id = %2$s.order_id
                    WHERE %1$s.calc_id in (%3$s) 
                    %4$s %5$s 
                    ',
            self::_table(),
            Payments::_table(),
            implode(',', $calcIds ),
            ( !empty( $paymentMethod ) ) ? " AND ". Payments::_table().".payment_method in ('{$paymentMethod}')" : '',
            ( !empty( $paymentStatus ) ) ? " AND ". Payments::_table().".status in ('{$paymentStatus}')" : ''
        );
        return $wpdb->get_var( $sql );
    }

	/**
	 *  Get all orders
	 */
	public static function get_all_orders($params) {
		global $wpdb;

        $paymentMethod = $params['payment_method'];
        $paymentStatus = $params['payment_status'];
        $calcIds       = $params['calc_ids'];
		$sorting       = $params['sorting'];
		$orderBy       = $params['orderBy'];
        $limit         = $params['limit'];
        $offset        = $params['offset'];

        $sql = sprintf(
            'SELECT %1$s.*, 
                    %2$s.type as paymentMethod,
                    %2$s.currency as paymentCurrency,
                    %2$s.status as paymentStatus,
                    %2$s.transaction,
                    %2$s.total
                    FROM %1$s
                    LEFT JOIN %2$s ON %1$s.id = %2$s.order_id
                    WHERE %1$s.calc_id in (%3$s) 
                    %4$s %5$s 
                    ORDER BY %6$s %7$s LIMIT %8$s OFFSET %9$s
                    ',
            self::_table(),
            Payments::_table(),
            implode(',', $calcIds ),
            ( !empty( $paymentMethod ) ) ? " AND ". Payments::_table().".payment_method in ('{$paymentMethod}')" : '',
            ( !empty( $paymentStatus ) ) ? " AND ". Payments::_table().".status in ('{$paymentStatus}')" : '',
            $orderBy,
            $sorting,
            $limit,
            $offset
        );

        return $wpdb->get_results( $wpdb->prepare($sql), ARRAY_A);
	}
}

