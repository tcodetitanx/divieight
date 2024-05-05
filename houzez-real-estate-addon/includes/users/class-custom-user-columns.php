<?php
/**
 * Custom user columns.
 *
 * @package HRE_Addon\Includes\Users
 */

namespace HRE_Addon\Includes\Users;

use HRE_Addon\Includes\Rest_Api\User_Service;
use HRE_Addon\Initializer;
use HRE_Addon\Libs\Settings;
use WP_User;
use function HRE_Addon\mp_get_script;
use function HRE_Addon\mp_get_style;

if ( ! defined( 'ABSPATH' ) ) {
	exit(); // Exit if accessed directly.
}

/**
 * Class Custom_User_Columns
 */
class Custom_User_Columns {

	/**
	 * Initialize.
	 *
	 * @return $this
	 */
	public function init(): self {
		add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ) );
		// Hook into WordPress to add custom columns and display values.
		add_filter( 'manage_users_columns', array( $this, 'add_custom_columns' ) );
		add_filter( 'manage_users_custom_column', array( $this, 'display_custom_column_values' ), 999, 3 );

		// Make custom columns sortable.
		// add_filter( 'manage_users_sortable_columns', array( $this, 'make_columns_sortable' ) );
		add_action( 'restrict_manage_users', array( $this, 'custom_agent_city_filter' ) );
		add_action( 'restrict_manage_users', array( $this, 'custom_agent_state_filter' ) );
		add_action( 'restrict_manage_users', array( $this, 'custom_agent_zip_code_filter' ) );
		add_action( 'restrict_manage_users', array( $this, 'filter_button' ) );

		add_action( 'pre_get_users', array( $this, 'filter_users_by_role' ) );

		return $this;
	}

	/**
	 * Filter users by filters.
	 *
	 * @param \WP_User_Query $query Query object.
	 *
	 * @return void
	 */
	public function filter_users_by_role( $query ): void {
		global $pagenow;

		if ( is_admin() && 'users.php' === $pagenow ) {
			$hre_agent_city     = filter_input( INPUT_GET, 'hre_agent_city' );
			$hre_agent_zip_code = filter_input( INPUT_GET, 'hre_agent_zip_code' );
			$hre_agent_state    = filter_input( INPUT_GET, 'hre_agent_state' );

			$meta = array();
			if ( ! empty( $query->query_vars['meta_query'] ) ) {
				$meta = $query->query_vars['meta_query'];
			}

			if ( ! empty( $hre_agent_state ) ) {
				$meta[] = array(
					'key'     => Settings::UM_AGENT_STATE,
					'value'   => $hre_agent_state,
					'compare' => '=',
				);
			}
			if ( ! empty( $hre_agent_city ) ) {
				$meta[] = array(
					'key'     => Settings::UM_AGENT_CITY,
					'value'   => $hre_agent_city,
					'compare' => '=',
				);
			}
			if ( ! empty( $hre_agent_zip_code ) ) {
				$meta[] =
					array(
						'key'     => Settings::UM_AGENT_ZIP_CODE,
						'value'   => $hre_agent_zip_code,
						'compare' => '=',
					);
			}

			if ( ! empty( $meta ) ) {
				$query->query_vars['meta_query'] = $meta;
			}
		}
	}

	/**
	 * Custom agent city filter.
	 *
	 * @return void
	 */
	public function custom_agent_city_filter(): void {
		$all_rows = User_Service::get_all_cities_for_all_agents();

		$options_html = '';
		$current_city = filter_input( INPUT_GET, 'hre_agent_city' );

		$options_html .= sprintf(
			'<option value="%s" %s>%s</option>',
			'',
			$current_city === '' ? 'selected' : '',
			__( "By Agent's City", 'hre-addon' )
		);
		foreach ( $all_rows as $row ) {
			$label        = esc_html( $row );
			$value        = esc_attr( $row );
			$selected     = $current_city === $value ? 'selected' : '';
			$options_html .= sprintf( '<option value="%s" %s>%s</option>', $value, $selected, $label );
		}

		echo sprintf(
			'<select name="hre_agent_city" title="%2$s" >%1$s</select>',
			$options_html,
			__( "Filter By Agent's City", 'hre-addon' )
		);
	}

	/**
	 * Custom agent state filter.
	 *
	 * @return void
	 */
	public function custom_agent_state_filter(): void {
		$all_rows = User_Service::get_all_states_for_all_agents();

		$options_html = '';
		$current_city = filter_input( INPUT_GET, 'hre_agent_state' );

		$options_html .= sprintf(
			'<option value="%s" %s>%s</option>',
			'',
			$current_city === '' ? 'selected' : '',
			__( "By Agent's State", 'hre-addon' )
		);
		foreach ( $all_rows as $row ) {
			$label        = esc_html( $row );
			$value        = esc_attr( $row );
			$selected     = $current_city === $value ? 'selected' : '';
			$options_html .= sprintf( '<option value="%s" %s>%s</option>', $value, $selected, $label );
		}

		echo sprintf(
			'<select name="hre_agent_state" id="hre-filter-by-agent-state" title="%2$s">%1$s</select>',
			$options_html,
			__( "Filter By Agent's State", 'hre-addon' )
		);
	}

	/**
	 * Custom agent zip code filter.
	 *
	 * @return void
	 */
	public function custom_agent_zip_code_filter(): void {
		$all_rows = User_Service::get_all_zip_codes_for_all_agents();

		$options_html     = '';
		$current_zip_code = filter_input( INPUT_GET, 'hre_agent_zip_code' );

		$options_html .= sprintf(
			'<option value="%s" %s>%s</option>',
			'',
			$current_zip_code === '' ? 'selected' : '',
			__( "By Agent's Zip Code", 'hre-addon' )
		);
		foreach ( $all_rows as $row ) {
			$label        = esc_html( $row );
			$value        = esc_attr( $row );
			$selected     = $current_zip_code === $value ? 'selected' : '';
			$options_html .= sprintf( '<option value="%s" %s>%s</option>', $value, $selected, $label );
		}

		echo sprintf(
			'<select name="hre_agent_zip_code" id="hre-filter-by-agent-zip-codes" title="%2$s">%1$s</select>',
			$options_html,
			__( "Filter By Agent's Zip Code", 'hre-addon' )
		);
	}

	/**
	 * Filter button.
	 *
	 * @return void
	 */
	public function filter_button(): void {
		echo sprintf(
			'<button type="button" class="button hre-filter-button">%s</button>',
			__( 'Filter', 'hre-addon' )
		);
	}


	/**
	 * Add custom columns
	 *
	 * @param array $columns Add custom columns.
	 *
	 * @return array
	 */
	public function add_custom_columns( array $columns ): array {
		$columns['hre_buyer_preference'] = __( 'Buyer Preference', 'hre-addon' );
		$columns['hre_seller_agent']     = __( "Seller's Agent", 'hre-addon' );
		$columns['hre_buyer_agent']      = __( "Buyer's Agent", 'hre-addon' );
		$columns['hre_agent_details']    = __( 'Agent Details', 'hre-addon' );

		do_action( 'hre_enqueue_cpt_users' );

		return $columns;
	}

	/**
	 * Display custom column values.
	 *
	 * @param string $value Value.
	 * @param string $column_name Column name.
	 * @param int $user_id User id.
	 *
	 * @return string
	 */
	public function display_custom_column_values( $value, $column_name, $user_id ): string {
		if ( 'hre_buyer_preference' === $column_name ) {
			$value = $this->display_buyer_preference(
				$user_id
			);
		} elseif ( 'hre_seller_agent' === $column_name ) {
			$value = $this->display_seller_agent(
				$user_id
			);
		} elseif ( 'hre_buyer_agent' === $column_name ) {
			$value = $this->display_buyer_agent(
				$user_id
			);
		} elseif ( 'hre_agent_details' === $column_name ) {
			$value = $this->display_agent_details(
				$user_id
			);
		} else {
			// If column not found, return the default value.
			$value = '-';
		}

		return $value;
	}


	/**
	 * Display buyer preference button.
	 *
	 * @param int $user_id Buyer id.
	 *
	 * @return string
	 */
	private function display_buyer_preference( int $user_id ): string {
		// Make sure the user have the role of buyer.

		return sprintf(
			'
			<div class="hre-buyer-preference-button-wrapper " data-user-id="%1$s">
				<button %2$s type="button" >%3$s</button>
			</div>
		',
			$user_id,
			// Add button classes.
			'class="hre-view-buyer-preference !bg-hre-10 text-white hover:!bg-rpt-hover cursor-pointer !border-0 disabled:opacity-30 disabled:cursor-not-allowed"',
			__( 'View', 'hre-addon' ),
		);
	}

	/**
	 * Display buyer preference button.
	 *
	 * @param int $user_id Buyer id.
	 *
	 * @return string
	 */
	private function display_seller_agent( int $user_id ): string {
		$agent_details = array(
			'first_name' => '',
			'last_name'  => '',
			'phone'      => '',
			'state'      => '',
		);
		$seller        = get_user_by( 'ID', $user_id );
		if ( ! ( $seller instanceof WP_User ) ) {
			return sprintf( '<div data-why="user-not-found"></div>' );
		}

		// User must be a seller role.
		if ( ! is_array( $seller->roles ) || empty( $seller->roles[0] ) || Settings::USER_ROLE_SELLER !== $seller->roles[0] ) {
			return sprintf( '<div data-why="user-not-seller"></div>' );
		}

		$agent_doesnt_exist = get_user_meta( $user_id, Settings::UM_SELLER_AGENT_DOESNT_EXIST, true );
		if ( 'no' === $agent_doesnt_exist ) {
			$agent_id = get_user_meta( $user_id, Settings::UM_SELLER_AGENT_ID, true );
			$agent    = get_user_by( 'ID', $agent_id );
			if ( ! ( $agent instanceof WP_User ) ) {
				return sprintf( '<div data-why="agent-not-found"></div>' );
			}

			if ( Settings::USER_ROLE_AGENT !== $agent->roles[0] ) {
				return sprintf( '<div data-why="agent-not-agent" data-agent-id="%1$s"></div>', $agent_id );
			}

			$agent_edit_link             = get_edit_user_link( $agent_id );
			$agent_details['first_name'] = sprintf( "<a href='%s'>%s</a>", $agent_edit_link, $agent->first_name );
			$agent_details['last_name']  = sprintf( "<a href='%s'>%s</a>", $agent_edit_link, $agent->last_name );
			$agent_details['phone']      = get_user_meta( $agent_id, Settings::UM_AGENT_PHONE, true );
			$agent_details['state']      = get_user_meta( $agent_id, Settings::UM_AGENT_STATE, true );
		} elseif ( 'yes' === $agent_doesnt_exist ) {
			$agent_details['first_name'] = get_user_meta( $user_id, Settings::UM_SELLER_AGENT_FIRST_NAME, true );
			$agent_details['last_name']  = get_user_meta( $user_id, Settings::UM_SELLER_AGENT_LAST_NAME, true );
			$agent_details['phone']      = get_user_meta( $user_id, Settings::UM_SELLER_AGENT_PHONE, true );
			$agent_details['state']      = get_user_meta( $user_id, Settings::UM_SELLER_AGENT_STATE_OF_RESIDENCE, true );
		}

		// Make sure the user have the role of buyer.
		return sprintf(
			'
			<div class="hre-seller-agent-detailswrapper " data-user-id="%1$s">
				<div class="hre-agent-first-name flex flex-row gap-2"><span class="whitespace-nowrap">%2$s :</span>  %3$s</div>
				<div class="hre-agent-last-name flex flex-row gap-2"><span  class="whitespace-nowrap">%4$s :</span>  %5$s</div>
				<div class="hre-agent-phone flex flex-row gap-2"><span  class="whitespace-nowrap">%6$s :</span> %7$s</div>
				<div class="hre-agent-phone flex flex-row gap-2"><span  class="whitespace-nowrap">%8$s :</span> %9$s</div>
			</div>
		',
			$user_id,
			__( 'First Name', 'hre-addon' ),
			$agent_details['first_name'],
			__( 'Last Name', 'hre-addon' ),
			$agent_details['last_name'],
			__( 'Phone', 'hre-addon' ),
			$agent_details['phone'],
			__( 'State', 'hre-addon' ),
			$agent_details['state']
		);
	}

	/**
	 * Display buyer agent.
	 *
	 * @param int $user_id Buyer id.
	 *
	 * @return string
	 */
	private function display_buyer_agent( int $user_id ): string {
//		Settings::UM_BUYER_REFERRER_FULL_NAME                       => $referrer_full_name,
//		Settings::UM_BUYER_WAS_REFERRED                             => $was_referred,
//		Settings::UM_BUYER_REFERRER_EMAIL                           => $referrer_email,
//		Settings::UM_BUYER_REFERRER_PHONE                           => $referrer_phone,
//		Settings::UM_BUYER_REFERRER_IS_AGENT_OR_BROKER              => $referrer_is_agent_or_broker,
//		Settings::UM_BUYER_REFERRER_IS_AGENT_OR_BROKER_CONFIRMATION => $referrer_is_agent_or_broker_confirmation,
		$agent_details = array(
			'full_name'                                => '',
			'email'                                    => '',
			'phone'                                    => '',
			'was_referred'                             => '',
			'referrer_is_agent_or_broker'              => '',
			'referrer_is_agent_or_broker_confirmation' => ''
		);
		$buyer         = get_user_by( 'ID', $user_id );
		if ( ! ( $buyer instanceof WP_User ) ) {
			return sprintf( '<div data-why="user-not-found"></div>' );
		}

		// User must be a seller role.
		if ( empty( $buyer->roles[0] ) || Settings::USER_ROLE_BUYER !== $buyer->roles[0] ) {
			return sprintf( '<div data-why="user-not-buyer"></div>' );
		}

		$agent_details['full_name']                                = get_user_meta( $user_id, Settings::UM_BUYER_REFERRER_FULL_NAME, true );
		$agent_details['email']                                    = get_user_meta( $user_id, Settings::UM_BUYER_REFERRER_EMAIL, true );
		$agent_details['phone']                                    = get_user_meta( $user_id, Settings::UM_BUYER_REFERRER_PHONE, true );
		$agent_details['was_referred']                             = get_user_meta( $user_id, Settings::UM_BUYER_WAS_REFERRED, true );
		$agent_details['referrer_is_agent_or_broker']              = get_user_meta( $user_id, Settings::UM_BUYER_REFERRER_IS_AGENT_OR_BROKER, true );
		$agent_details['referrer_is_agent_or_broker_confirmation'] = get_user_meta( $user_id, Settings::UM_BUYER_REFERRER_IS_AGENT_OR_BROKER_CONFIRMATION, true );

		// Make sure the user have the role of buyer.
		return sprintf(
			'
			<div class="hre-buyer-agent-details-wrapper " data-user-id="%1$s">
				<div class="hre-agent-full-name flex flex-row gap-2"><span class="whitespace-nowrap">%2$s :</span>  %3$s</div>
				<div class="hre-agent-email flex flex-row gap-2"><span  class="whitespace-nowrap">%4$s :</span>  %5$s</div>
				<div class="hre-agent-phone flex flex-row gap-2"><span  class="whitespace-nowrap">%6$s :</span> %7$s</div>
				<div class="hre-agent-was-referred flex flex-row gap-2"><span  class="whitespace-nowrap">%8$s :</span> %9$s</div>
				<div class="hre-agent-was-is-agent-or-broker flex flex-row gap-2"><span  class="whitespace-nowrap">%10$s :</span> %11$s</div>
				<div class="hre-agent-was-is-agent-or-broker-confirmation flex flex-row gap-2"><span  class="whitespace-nowrap">%12$s :</span> %13$s</div>
			</div>
		',
			$user_id,
			__( 'Full Name', 'hre-addon' ),
			$agent_details['full_name'],
			__( 'Email', 'hre-addon' ),
			$agent_details['email'],
			__( 'Phone', 'hre-addon' ),
			$agent_details['phone'],
			__( 'Was Referred', 'hre-addon' ),
			$agent_details['was_referred'],
			__( 'Referrer is Agent or Broker', 'hre-addon' ),
			$agent_details['referrer_is_agent_or_broker'],
			__( 'Referrer is Agent or Broker Confirmation', 'hre-addon' ),
			$agent_details['referrer_is_agent_or_broker_confirmation']
		);
	}


	/**
	 * Display agent details.
	 *
	 * @param int $user_id Buyer id.
	 *
	 * @return string
	 */
	private function display_agent_details( int $user_id ): string {
		$agent_details = array(
			'city'    => '',
			'state'   => '',
			'zp_code' => '',
		);
		$agent         = get_user_by( 'ID', $user_id );
		if ( ! ( $agent instanceof WP_User ) ) {
			return sprintf( '<div data-why="user-not-found"></div>' );
		}

		// User must be a seller role.
		if ( empty( $agent->roles[0] ) || Settings::USER_ROLE_AGENT !== $agent->roles[0] ) {
			return sprintf( '<div data-why="user-not-agent"></div>' );
		}

		$agent_details['city']    = get_user_meta( $user_id, Settings::UM_AGENT_CITY, true );
		$agent_details['state']   = get_user_meta( $user_id, Settings::UM_AGENT_STATE, true );
		$agent_details['zp_code'] = get_user_meta( $user_id, Settings::UM_AGENT_ZIP_CODE, true );

		return sprintf(
			'
			<div class="hre-seller-agent-details-wrapper flex flex-col gap-1" data-user-id="%1$s">
				<div class="hre-agent-city flex flex-row gap-2"><span class="whitespace-nowrap">%2$s :</span>  %3$s</div>
				<div class="hre-agent-state flex flex-row gap-2"><span  class="whitespace-nowrap">%4$s :</span>  %5$s</div>
				<div class="hre-agent-zip-code flex flex-row gap-2"><span  class="whitespace-nowrap">%6$s :</span> %7$s</div>
				<button type="button" class="button hre-view-agent-details" data-agent-id="%1$s" >%8$s</button>
			</div>
		',
			$user_id,
			__( 'City', 'hre-addon' ),
			$agent_details['city'],
			__( 'State', 'hre-addon' ),
			$agent_details['state'],
			__( 'Zip Code', 'hre-addon' ),
			$agent_details['zp_code'],
			__( 'View Details', 'hre-addon' )
		);
	}


	/**
	 * Register scripts.
	 *
	 * @return void
	 */
	public function register_scripts(): void {
		$css = mp_get_style( '/cpt/users' );
		$js  = mp_get_script( '/cpt/users' );

		wp_register_style( 'hre-cpt-users', $css, array(), Initializer::$script_version );
		// wp-components is important so React will be loaded.
		wp_register_script(
			'hre-cpt-users',
			$js,
			array(
				'jquery',
				'wp-components',
			),
			Initializer::$script_version,
			true
		);

		/**
		 * Enqueue cpt users scripts and styles.
		 *
		 * @since 0.0.1
		 */
		add_action(
			'hre_enqueue_cpt_users',
			static function () {
				wp_enqueue_style( 'hre-cpt-users' );
				wp_enqueue_script( 'hre-cpt-users' );

				add_action(
					'admin_footer',
					static function () {
						echo '<div class="hre-toast-wrapper"></div>';
					}
				);
			}
		);
	}
}
