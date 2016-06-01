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
		// replace URL overrides
		if ( false !== ( $override_siteurl = apply_filters( 'wsi_siteurl_override', false ) ) ) {
			$url_to_image = str_replace( trailingslashit( site_url() ), trailingslashit( $override_siteurl ), $url_to_image );
		}

		// remove schema and prepare for Photon
		$schemaless_url = str_replace( array('http://', 'https://'), '', $url_to_image );
		$photon_ready_url = self::api_endpoint() . $schemaless_url;

		return download_url( $photon_ready_url, 350 );
	}

	/**
	 * Get a random Photon server to request (0-3)
	 *
	 * @static
	 * @return string A random photon server base URI, trailing-slashed
	 */
	public static function api_endpoint() {
		return sprintf( self::$photon_api_url_format, rand( 0, 3 ) );
	}
}