<?php
/**
 * Repozytorium leadów e-mail + kodów rabatowych.
 *
 * @package DesignCartSpinToWin
 * @author  Paweł Nosko
 * @company Design Cart
 */

defined( 'ABSPATH' ) || exit;

/**
 * Tabela wp_dc_stw_leads.
 */
final class DC_STW_Email_Lead_Repository {

	public const TABLE_SUFFIX = 'dc_stw_leads';

	/**
	 * Pełna nazwa tabeli z prefixem.
	 */
	public static function table(): string {
		global $wpdb;
		return $wpdb->prefix . self::TABLE_SUFFIX;
	}

	/**
	 * Escapowana nazwa tabeli do zapytań SQL.
	 */
	private static function escaped_table(): string {
		global $wpdb;
		return '`' . esc_sql( self::table() ) . '`';
	}

	/**
	 * Tworzy tabelę (dbDelta).
	 */
	public static function install(): void {
		global $wpdb;

		$table   = self::table();
		$charset = $wpdb->get_charset_collate();

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$sql = "CREATE TABLE {$table} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			coupon_id bigint(20) unsigned NOT NULL DEFAULT 0,
			email varchar(255) NOT NULL,
			coupon_code varchar(64) NOT NULL DEFAULT '',
			prize_label varchar(255) NOT NULL DEFAULT '',
			prize_amount varchar(64) NOT NULL DEFAULT '',
			user_id bigint(20) unsigned NOT NULL DEFAULT 0,
			ip_address varchar(45) NOT NULL DEFAULT '',
			created_at datetime NOT NULL,
			PRIMARY KEY  (id),
			KEY email (email),
			KEY created_at (created_at)
		) {$charset};";

		dbDelta( $sql );
	}

	/**
	 * @param array{
	 *   coupon_id:int,
	 *   email:string,
	 *   coupon_code:string,
	 *   prize_label?:string,
	 *   prize_amount?:string,
	 *   user_id?:int,
	 *   ip_address?:string
	 * } $data
	 */
	public static function insert( array $data ): int {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Custom plugin table, no WP API.
		$ok = $wpdb->insert(
			self::table(),
			array(
				'coupon_id'    => (int) $data['coupon_id'],
				'email'        => (string) $data['email'],
				'coupon_code'  => (string) $data['coupon_code'],
				'prize_label'  => (string) ( $data['prize_label'] ?? '' ),
				'prize_amount' => (string) ( $data['prize_amount'] ?? '' ),
				'user_id'      => (int) ( $data['user_id'] ?? 0 ),
				'ip_address'   => (string) ( $data['ip_address'] ?? '' ),
				'created_at'   => current_time( 'mysql' ),
			),
			array( '%d', '%s', '%s', '%s', '%s', '%d', '%s', '%s' )
		);

		if ( ! $ok ) {
			return 0;
		}

		return (int) $wpdb->insert_id;
	}

	/**
	 * @return list<array<string, mixed>>
	 */
	public static function find_all( int $limit = 500, int $offset = 0 ): array {
		global $wpdb;

		$table = self::escaped_table();

		// Custom table name cannot be a prepare placeholder; escaped via esc_sql().
		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, PluginCheck.Security.DirectDB.UnescapedDBParameter
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$table} ORDER BY created_at DESC LIMIT %d OFFSET %d",
				$limit,
				$offset
			),
			ARRAY_A
		);
		// phpcs:enable

		return is_array( $rows ) ? $rows : array();
	}

	public static function count_all(): int {
		global $wpdb;

		$table = self::escaped_table();

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- Table name escaped via esc_sql().
		return (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" );
	}
}
