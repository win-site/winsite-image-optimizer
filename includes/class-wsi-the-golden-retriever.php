<?php

class WSI_The_Golden_Retriever {
	static $photon_api_url = 'http://i%d.wp.com/';

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

	static function api_endpoint() {
		return sprintf( self::$photon_api_url, rand( 0, 3 ) );
	}
}