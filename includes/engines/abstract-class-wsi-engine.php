<?php

abstract class WSI_Engine {
	public $api_base_url;

	public function __construct() {
		if ( method_exists( $this, 'register_settings_fields' ) ) {
			add_action( 'wsi_register_settings_fields', array( $this, 'register_settings_fields' ), 10, 3 );
		}
	}

	abstract public function fetch( $url_to_image );
}