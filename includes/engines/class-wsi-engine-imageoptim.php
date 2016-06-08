<?php

class WSI_Engine_ImageOptim extends WSI_Engine {
	/**
	 * String format for the ImageOptim base URI
	 * Format: https://im2.io/<Username>/<Options>/<Image URL>
	 * 
	 * @see  https://im2.io/api/post
	 * @static
	 * @var string
	 */
	public static $imageoptim_api_url_format = 'https://im2.io/%s/%s/';

	/**
	 * Fetch file via ImageOptim
	 *
	 * @since 1.0.0
	 * @param  string $url_to_image The URL to the local file sitting on current server
	 * @return string               The contents of the files as returned from ImageOptim API
	 */
	public function fetch( $url_to_image ) {
		// remove schema and prepare for ImageOptim
		$this->final_url = $imageoptim_ready_url = $this->api_endpoint() . $url_to_image;

		// Modify request type from GET to POST
		add_filter( 'http_request_args', array( $this, 'filter_http_request_args' ), 10, 2 );

		// Download the file
		$downloaded = download_url( $imageoptim_ready_url, 350 );

		// Detach
		remove_filter( 'http_request_args', array( $this, 'filter_http_request_args' ) );

		return $downloaded;
	}

	/**
	 * Filter the HTTP request method, since ImageOptim supports POST only
	 * 
	 * @param  [type] $args [description]
	 * @param  [type] $url  [description]
	 * @return [type]       [description]
	 */
	public function filter_http_request_args( $args, $url ) {
		if ( ! empty( $this->final_url ) && $url === $this->final_url ) {
			$args['method'] = 'POST';
		}

		return $args;
	}

	/**
	 * Get composed URL for ImageOptim webservice API
	 *
	 * @return string A random ImageOptim server base URI, trailing-slashed
	 */
	public function api_endpoint() {
		$username = apply_filters( 'wsi_engine_imageoptim_username', winsite_image_optimizer()->retro->get_setting( 'imageoptim-username' ) );

		// Username must be set
		if ( ! $username ) {
			return false;
		}

		// Aggregate options
		$options = apply_filters( 'wsi_engine_imageoptim_options', array(
			'full'
		) );

		// Ready URL
		return sprintf( self::$imageoptim_api_url_format, $username, implode( ',', $options ) );
	}


	/**
	 * Register settings for ImageOptim
	 *
	 * @param  WSI_Retro_Processor $that The object that initiated the settings
	 * @param  [type] $settings_page    [description]
	 * @param  [type] $settings_section [description]
	 * @return void
	 */
	public function register_settings_fields( $that, $settings_page, $settings_section ) {
		add_settings_field(
			$settings_section . '-' . 'imageoptim-username',
			__( 'ImageOptim Username', 'winsite-images' ),
			array( $that, 'field_text' ),
			$settings_page,
			$settings_section,
			array( 'option' => 'imageoptim-username', 'desc' => __( 'Get your username at <a href="https://im2.io/register">ImageOptim API</a>.', 'winsite-images' ) )
		);
	}
}
