<?php

class WSI_Hooks {
	private $current_metadata;
	private $current_attachment_id;


	public function __construct() {
		// filter the generated file
		// add_filter( 'wp_generate_attachment_metadata', array($this, 'filter_wp_generate_attachment_metadata' ), 10, 2 );
		add_filter( 'wp_handle_upload', array($this, 'filter_wp_handle_upload' ), 10, 2 );
	}

	public function filter_wp_handle_upload( $ar, $action ) {
		$mime_check = explode('/', $ar['type']);

		// check MIME type, if it's not an image, bye. Or if it's a sideload. 
		// We Aim only for uploads.
		if ( $action != 'upload' || empty($mime_check[0]) || 'image' != $mime_check[0] ) {
			return $ar;
		}

		// Detach current filter or we'll end up in an infinite loop
		remove_filter( 'wp_handle_upload', array($this, __FUNCTION__) );

		// Sideload media now
		$overrides = array('test_form'=>false);
		$time = current_time( 'mysql' );

		// sanitize file name
		$filename = sanitize_file_name( apply_filters( 'wsi_file_prefix', 'wsi-photonized' ) . '-' . basename( $ar['url'] ) );

		$file_array = array(
			'name' 		=> $filename,
			'tmp_name' 	=> WSI_The_Golden_Retriever::fetch( $ar['url'] ), // <- will download file and pass its contents
		);

		// If error storing temporarily, return the error.
        if ( is_wp_error( $file_array['tmp_name'] ) ) {
        	error_log( 'WSI: Error loading a file. Orig Array: ' . print_r(array( $file_array, $ar ), true) . "\n Error details: " . print_r( $file_array['tmp_name'], true ) );
            return $ar;
        }

		$file = wp_handle_sideload( $file_array, $overrides, $time );

		// Re-attach current filter for further uploads
		add_filter( 'wp_handle_upload', array($this, __FUNCTION__), 10, 2 );

		return $file;
	}


	/**
	 * Get uploads dir URL, trailing slashed
	 * 
	 * @return [type] [description]
	 */
	public function get_uploads_dir_url() {
		$upload_dir = wp_upload_dir();
		$uploads_dir_base_url = $upload_dir['baseurl'];

		return trailingslashit( $uploads_dir_base_url );
	}

	/**
	 * Filters the media upload process and create copies
	 * 
	 * @param  [type] $metadata      [description]
	 * @param  [type] $attachment_id [description]
	 * @return [type]                [description]
	 */
	public function filter_wp_generate_attachment_metadata( $metadata, $attachment_id ) {
		$uploads_url = $this->get_uploads_dir_url();
		$url_to_full_image = $uploads_url . $metadata['file'];

		// set currents
		$this->current_metadata = $metadata;
		$this->current_attachment_id = $attachment_id;


		// to make sure there's not going to be a never-ending loop here, we have to remove this hook
		remove_filter( 'wp_generate_attachment_metadata', array($this, 'filter_wp_generate_attachment_metadata' ), 10 );

		// fetch first full size image
		$res = WSI_The_Golden_Retriever::fetch( $url_to_full_image );

		// re-attach
		add_filter( 'wp_generate_attachment_metadata', array($this, 'filter_wp_generate_attachment_metadata' ), 10, 2 );

		return $metadata;

		// loop size by size, retrieve and insert
		foreach ( $metadata['sizes'] as $size_key => $size ) {
			$schemaless_url = str_replace( array('http://', 'https://'), '', $uploads_url . dirname( $metadata['file'] ) . '/' . $size['file'] );
			$photon_ready_url = $schemaless_url;

			$bone = WSI_The_Golden_Retriever::fetch( $photon_ready_url );
			return $metadata;
		}


		return $metadata;
	}
}