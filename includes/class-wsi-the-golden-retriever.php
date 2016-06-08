<?php

/**
 * The Golden Rertiever for WSI
 *
 * @since 1.0.0
 * @author Maor <maor@win-site.co.il>
 */
class WSI_The_Golden_Retriever {
	/**
	 * String format for the Photon base URI
	 *
	 * @static
	 * @var string
	 */
	public static $photon_api_url_format = 'http://i%d.wp.com/';

	/**
	 * Fetch file via Photon
	 *
	 * @static
	 * @since 1.0.0
	 * @param  string $url_to_image The URL to the local file sitting on current server
	 * @return string               The contents of the files as returned from Photon API
	 */
	public static function fetch( $url_to_image ) {
		$engine_instance = self::get_engine();

		// Default processing value
		$photon_ready_url = false;

		// Check if this engine's class exist in memory 
		if ( $engine_instance ) {
			if ( method_exists( $engine_instance, 'fetch' ) ) {
				// Replace URL overrides first
				if ( false !== ( $override_siteurl = apply_filters( 'wsi_siteurl_override', false ) ) ) {
					$url_to_image = str_replace( trailingslashit( site_url() ), trailingslashit( $override_siteurl ), $url_to_image );
				}

				// send request via engine
				$photon_ready_url = $engine_instance->fetch( $url_to_image );
			}
		}

		return $photon_ready_url;
	}

	/**
	 * Get engines, after filter runs
	 *
	 * @return array Array of engines mapped as engine_id => Engine_PHP_Class
	 */
	public static function get_engines() {
		return apply_filters( 'wsi_available_image_processing_engines', array(
			'imageoptim' => 'WSI_Engine_ImageOptim',
		) );
	}

	/**
	 * Get a random Photon server to request (0-3)
	 *
	 * @static
	 * @return string A random photon server base URI, trailing-slashed
	 */
	public static function get_engine( $return_just_the_name = false ) {
		// Allow devs to hook into and alter available engines
		$available_engines = self::get_engines();
		// Default engine, allow devs to prefer specific option
		$default_engine = apply_filters( 'wsi_default_image_processing_engine', 'imageoptim' );

		if ( array_key_exists( $default_engine, $available_engines ) && class_exists( $available_engines[ $default_engine ] ) ) {
			return $return_just_the_name ? $default_engine : new $available_engines[ $default_engine ];
		}

		return false;
	}
}