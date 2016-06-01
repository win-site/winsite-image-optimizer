<?php

/**
 * Base for Winsite-Hooks.
 *
 * @package Winsite
 * @subpackage Winsite_Images
 */
class WSI_Hooks {
	private $current_metadata;
	private $current_attachment_id;
	private $last_filename = false;

	/**
	 * Kick things off
	 */
	public function __construct() {
		// Filter uploaded file. Filetype validation done within callback.
		add_filter( 'wp_handle_upload', array($this, 'filter_wp_handle_upload' ), 10, 2 );
		add_filter( 'wp_update_attachment_metadata', array($this, 'filter_wp_update_attachment_metadata' ), 10, 2 );
	}

	public function filter_wp_update_attachment_metadata( $data, $post_id ) {
		// is this file the one that was last processed here?
		if ( false !== strpos( $data['file'], $this->last_filename ) ) {
			// if so update its meta
			update_post_meta( $post_id, '_wsi_photonized', '1' );

			// as well as attachment metadata right here
			$data['wsi_photonized'] = true;
		}

		return $data;
	}

	/**
	 * Does the filtering of an uploaded file, creates an optimized copy
	 *
	 * @since  1.0.0
	 * @param  array  $upload {
     *     Array of upload data.
     *
     *     @type string $file Filename of the newly-uploaded file.
     *     @type string $url  URL of the uploaded file.
     *     @type string $type File type.
     * }
	 * @param  string $action Expected value for $_POST['action'].
	 * @return array On success, returns an associative array of file attributes. On failure, returns
	 *               $overrides['upload_error_handler'](&$file, $message ) or array( 'error'=>$message ).
	 */
	public function filter_wp_handle_upload( $file, $action ) {
		// check MIME type, if it's not an image, bye. Or if it's a sideload. 
		// We Aim only for uploads.
		if ( $action != 'upload' || false === strpos( $file['type'], 'image/' ) ) {
			return $file;
		}

		// Detach current filter or we'll end up in an infinite loop
		remove_filter( 'wp_handle_upload', array($this, __FUNCTION__) );

		// Sideload media now
		$overrides = array('test_form'=>false);
		$time = current_time( 'mysql' );

		// sanitize file name
		$filename = $this->last_filename = sanitize_file_name( apply_filters( 'wsi_file_prefix', 'wsi-photonized' ) . '-' . basename( $file['url'] ) );

		$file_array = array(
			'name' 		=> $filename,
			'tmp_name' 	=> WSI_The_Golden_Retriever::fetch( $file['url'] ), // <- will download file and pass its contents
		);

		// If error storing temporarily, return the error.
        if ( is_wp_error( $file_array['tmp_name'] ) ) {
        	error_log( 'WSI: Error loading a file. Orig Array: ' . print_r(array( $file_array, $file ), true) . "\n Error details: " . print_r( $file_array['tmp_name'], true ) );
        	$this->last_filename = false; // make sure we're not going to mark this one as processed
            
            return $file;
        }

		$file = wp_handle_sideload( $file_array, $overrides, $time );

		// Re-attach current filter for further uploads
		add_filter( 'wp_handle_upload', array($this, __FUNCTION__), 10, 2 );

		return $file;
	}

	/**
	 * Get uploads dir URL, trailing slashed
	 * 
	 * @return string URL to uploads dir
	 */
	public function get_uploads_dir_url() {
		$upload_dir = wp_upload_dir();
		$uploads_dir_base_url = $upload_dir['baseurl'];

		return trailingslashit( $uploads_dir_base_url );
	}
}