<?php

class WSI_Engine_Photon extends WSI_Engine {
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
	 * @since 1.0.0
	 * @param  string $url_to_image The URL to the local file sitting on current server
	 * @return string               The contents of the files as returned from Photon API
	 */
	public function fetch( $url_to_image ) {
		// remove schema and prepare for Photon
		$schemaless_url = str_replace( array('http://', 'https://'), '', $url_to_image );
		$photon_ready_url = $this->api_endpoint() . $schemaless_url;

		return download_url( $photon_ready_url, 350 );
	}

	/**
	 * Get a random Photon server to request (0-3)
	 *
	 * @return string A random photon server base URI, trailing-slashed
	 */
	public function api_endpoint() {
		return sprintf( self::$photon_api_url_format, rand( 0, 3 ) );
	}
}