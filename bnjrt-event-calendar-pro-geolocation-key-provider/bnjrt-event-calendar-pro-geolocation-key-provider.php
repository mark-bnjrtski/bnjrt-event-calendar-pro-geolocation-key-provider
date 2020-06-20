<?php
/**
 * 
 * @link              https://bnjrt.ski
 * @since             1.0.0
 * @package           Event Calendar Pro Geocoding Key Provider
 * 
 * @wordpress-plugin
 * Plugin Name:       Event Calendar Pro Geocoding Key Provider
 * Plugin URI:        https://bnjrt.ski/
 * Description:       Provides a separate Google Geocoding API key for server-side geocoding requests.
 * Version:           1.0.0
 * Author:            Boulder Nordic Junior Racing Team
 * Author URI:        https://bnjrt.ski/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bnjrt-event-calendar-pro-geocoding-key-provider
 * Domain Path:       /languages
 * 
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 * 
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details. 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Provides a separate Google Geocoding API key for server-side geocoding requests. 
 * This API key should be restricted to IP-address requests. 
 */
class Event_Calendar_Pro_Geocoding_Key_Provider {
  /**
   * Google Geocoding API key value.
   */
  private const API_KEY = 'AIzaSyCvnuzakR6qgWL2M4KJnUQ-LFYPJ9R2bcE';

  /**
   * Initializes Google Geocoding API key to be used for server-side geocoding requests.
   *
   * @return void
   */
  private function __construct() {
    add_filter( 'pre_http_request', [ $this, 'pre_http_request' ], 10, 3 );
  }

  /**
   * Create instance if it doesn't exist. Return it.
   *
   * @return Event_Calendar_Pro_Geocoding_Key_Provider
   */
  private static $instance = null;
  public static function get_instance() {
    if (null == self::$instance) {
      self::$instance = new self;
    }
    return self::$instance;
  }

  /**
   * @param mixed  $response
   * @param array  $args
   * @param string $url
   *
   * @return array|WP_Error
   */
  public function pre_http_request( $response, $args, $url ) {
    // If this is not a Google Geocoding API request, or if it is but the server-side
    // API key is already in place, do nothing more.
    if (
      0 !== strpos( $url, 'https://maps.googleapis.com/maps/api/geocode' )
      || false !== strpos( $url, self::API_KEY )
    ) {
      return $response;
    }

    // Replace the API key.
    $url = add_query_arg( 'key', self::API_KEY, $url );

    // Perform a new request with our alternative API key and return the result.
    return wp_remote_get( $url, $args );
  }
}

// Instantiate.
$Event_Calendar_Pro_Geocoding_Key_Provider = Event_Calendar_Pro_Geocoding_Key_Provider::get_instance();
