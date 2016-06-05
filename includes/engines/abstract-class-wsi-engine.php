<?php

abstract class WSI_Engine {
	public $api_base_url;
	
	abstract public function fetch( $url_to_image );
}