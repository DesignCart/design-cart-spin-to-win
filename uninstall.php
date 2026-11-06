<?php
/**
 * Uninstall — Design Cart Spin To Win
 *
 * @package DesignCartSpinToWin
 * @author  Paweł Nosko
 * @company Design Cart
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

global $wpdb;

delete_option( 'dc_stw_installed' );
delete_option( 'dc_stw_db_version' );
delete_option( 'dc_stw_general' );
delete_option( 'dc_stw_coupons' );
delete_option( 'dc_stw_content' );
delete_option( 'dc_stw_typography' );

// phpcs:ignore WordPress.DB.DirectDatabaseQuery.SchemaChange,WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
$wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'dc_stw_leads' );
