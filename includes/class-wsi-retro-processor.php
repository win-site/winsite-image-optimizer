<?php

class WSI_Retro_Processor {
	private $settings_page = 'media';
	private $settings_section = 'wsi_media_settings';

	private static $settings = array(); // use $this->get_settings()

	public function __construct() {
		// Load up the localization file if we're using WordPress in a different language
		load_plugin_textdomain( 'wsi-image-optimizer' );

		add_action( 'admin_menu',              array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init',              array( $this, 'action_admin_init' ) );
		add_action( 'wp_ajax_wsi-regeneretro', array( $this, 'do_regeneretro' ) );

		// Allow people to change what capability is required to use this plugin
		$this->capability = apply_filters( 'wsi_processor_page_cap', 'manage_options' );
	}

	public function action_admin_init() {
		// Plugin setting section
		register_setting( $this->settings_page, $this->settings_section, array( $this, 'sanitize_options' ) );

		add_settings_section(
			$this->settings_section,
			__( 'Winsite Image Optimizer', 'winsite-images' ),
			array( $this, 'settings_section_intro' ),
			$this->settings_page
		);

		add_settings_field(
			$this->settings_section . '-' . 'should-keep-original',
			__( 'Keep original?', 'winsite-images' ),
			array( $this, 'field_yesno' ),
			$this->settings_page,
			$this->settings_section,
			array( 'option' => 'should-keep-original' )
		);

		// Load all available engines so they can register their own settings
		foreach ( WSI_The_Golden_Retriever::get_engines() as $engine_id => $engine_class ) {
			/**
			 * @todo  consider not instantiating the class but instead calling an internal
			 *        static method that will register the settings itself.
			 */
			new $engine_class;
		}

		do_action( 'wsi_register_settings_fields', $this, $this->settings_page, $this->settings_section );
	}

	public function add_admin_menu() {
		// add_management_page
		$this->menu_id = add_media_page( __( 'Winsite Image Optimizer', 'wsi-image-optimizer' ), __( 'WS. Image Optimizer', 'wsi-image-optimizer' ), $this->capability, 'wsi-image-optimizer', array( $this, 'regenerate_interface' ) );
		add_action( "load-$this->menu_id", array( $this, 'page_load' ) ); // register hook
	}

	public function page_load() {
		// Enqueue assets
		wp_enqueue_script( 'wsi-main', plugins_url( 'static/js/winsite-image-optimizer.js', dirname( __FILE__ ) ), array( 'jquery' ) );

		$args = array(
			'post_type'	 	 => 'attachment',
			'post_status'	 => 'inherit',
			'post_mime_type' => array( 'image/jpeg', 'image/gif', 'image/png' ),
			'posts_per_page' => -1,
			'cache_results'  => false,
			'fields' 		 => 'ids',
			'meta_query' => array(
				'relation' => 'OR',
				array(
					'key'     => '_wsi_photonized',
					'compare' => 'NOT EXISTS',
				),
				array(
					'key'     => '_wsi_photonized',
					'value'   => '1',
					'compare' => '!=',
				),
			),
		);
		$filtered_attachments = get_posts( $args );

		wp_localize_script( 'wsi-main', 'WSI_Generetro_Data', array(
			'ids' => $filtered_attachments,
			'l10n' => array(
				'alert_no_images' => __( 'There are no images to process at the moment. Yay!', 'winsite-images' ),
			),
		) );
	}

	/**
	 * Display assistive text in settings section
	 *
	 * @uses _e
	 * @uses this::plugin_priority
	 * @return string
	 */
	public function settings_section_intro() {
		?>
		<p id="winsite-image-optimizer"><?php _e( 'Control the way Winsite Image Optimizer works via the settings below.', 'wp_revisions_control' ); ?></p>
		<?php
	}

	/**
	 * Render text field
	 *
	 * @param array $args
	 * @uses esc_attr
	 * @uses this::get_setting
	 * @return string
	 */
	public function field_text( $args ) {
		$value = $this->get_setting( $args['option'] );
		?>
		<input type="text" name="<?php echo esc_attr( $this->settings_section . '[' . $args['option'] . ']' ); ?>" value="<?php echo esc_attr( $value ); ?>" />
		<?php if ( ! empty( $args['desc'] ) ) : ?>
		<p class="description"><?php echo wp_kses_data( $args['desc'] ); ?></p>
		<?php endif;
	}

	/**
	 * Render field for each post type
	 *
	 * @param array $args
	 * @uses this::get_setting
	 * @uses esc_attr
	 * @return string
	 */
	public function field_yesno( $args ) {
		$value = $this->get_setting( $args['option'] ) === '1';
		?>
		<label class="tix-yes-no description"><input type="radio" name="<?php echo esc_attr( $this->settings_section . '[' . $args['option'] . ']' ); ?>" value="1" <?php checked( $value, true ); ?>> <?php _e( 'Yes', 'winsite-images' ); ?></label>
		<label class="tix-yes-no description"><input type="radio" name="<?php echo esc_attr( $this->settings_section . '[' . $args['option'] . ']' ); ?>" value="0" <?php checked( $value, false ); ?>> <?php _e( 'No', 'winsite-images' ); ?></label>
		<?php
	}

	/**
	 * Sanitize plugin settings
	 *
	 * @param array $options
	 * @return array
	 */
	public function sanitize_options( $options ) {
		$options_sanitized = array();
		// return $options_sanitized;
		return $options;
	}


	public function regenerate_interface() {
		$username_exists = (bool) $this->get_setting('imageoptim-username');
		?>
		<div class="wrap">
			<h2><?php esc_html_e( 'Winsite Image Optimizer', 'mimi' ); ?></h2>

			<?php if ( ! $username_exists ) : ?>
			<div class="error">
				<p><?php printf( __( 'Hey! Looks like you haven\'t set up your ImageOptim API yet. Head over to the <a href="%s">settings section</a> and follow the instructions there. It\'s easy!', 'wsi-image-optimizer' ), admin_url( 'options-media.php#winsite-image-optimizer' ) ); ?></p>
			</div>
			<?php endif; ?>

			<form id="wsi-regeneretro" method="post">
				<h3><?php esc_html_e( 'Optimize Your Images', 'wsi-image-optimizer' ); ?></h3>

				<ul class="wsi-file-list"></ul>

				<?php wp_nonce_field( 'wsi-regeneretro' ) ?>
				<progress id='progress-bar' max='100'><span></span>%</progress>
				<button class="button button-primary" <?php echo $username_exists ?: 'disabled'; ?> type="submit"><?php esc_html_e( 'Regenerate Retroactive', 'wsi-image-optimizer' ); ?></button>
				<span class="status status-finished"><span class="dashicons dashicons-yes"></span> Finished</span>
				<span class="spinner"></span>
			</form>
		</div>

		<style>
		#wsi-regeneretro .status { display: none; }
		#wsi-regeneretro .status-active { display: inline-block; }
		#wsi-regeneretro .spinner {
			float: none;
			display: inline-block;
		}
		#wsi-regeneretro .status.status-display {
			display: inline-block;
			vertical-align: sub; /*fallback*/
			vertical-align: -webkit-baseline-middle;
			color: green;
			cursor: default;
		}
		#wsi-regeneretro .wsi-file-list {
			overflow-y: auto;
			max-height: 35vh;
		}
		progress{
			display: block;
			margin-bottom: 20px;
			height: 20px;
			min-width: 160px;
		}
		progress:not([value]) {
			display: none;
		}

		</style>
		<?php
	}

	public function do_regeneretro() {
		@error_reporting( 0 ); // Don't break the JSON result

		header( 'Content-type: application/json' );

		$id = (int) $_REQUEST['id'];
		$image = get_post( $id );

		if ( ! $image || 'attachment' != $image->post_type || 'image/' != substr( $image->post_mime_type, 0, 6 ) ) {
			die( json_encode( array( 'error' => sprintf( __( 'Failed resize: %s is an invalid image ID.', 'regenerate-thumbnails' ), esc_html( $_REQUEST['id'] ) ) ) ) ); }

		if ( ! current_user_can( $this->capability ) ) {
			$this->die_json_error_msg( $image->ID, __( "Your user account doesn't have permission to resize images", 'regenerate-thumbnails' ) ); }

		if ( get_post_meta( $image->ID, '_wsi_photonized', true ) === '1' ) {
			$this->die_json_error_msg( $image->ID, __( 'This image has already been processed.', 'regenerate-thumbnails' ) );
		}

		$fullsizepath = get_attached_file( $image->ID );
		$fullsizeurl = wp_get_attachment_url( $image->ID );

		// make sure we're not going to run the other actions
		remove_filter( 'wp_handle_upload', array( winsite_image_optimizer()->hooks, 'filter_wp_handle_upload' ) );

		// Sideload media now
		$overrides = array( 'test_form' => false );
		$time = current_time( 'mysql' );

		// sanitize file name
		$filename = sanitize_file_name( apply_filters( 'wsi_file_prefix', 'wsi-' . WSI_The_Golden_Retriever::get_engine( true ) ) . '-' . basename( $fullsizepath ) );

		$file_array = array(
			'name' 		=> $filename,
			'tmp_name' 	=> WSI_The_Golden_Retriever::fetch( $fullsizeurl ), // <- will download file and pass its contents
		);

		// If error storing temporarily, return the error.
		if ( is_wp_error( $file_array['tmp_name'] ) ) {
			error_log( 'WSI: Error loading a file. Orig Array: ' . print_r( array( $file_array ), true ) . "\n Error details: " . print_r( $file_array['tmp_name'], true ) );
			$this->die_json_error_msg( $image->ID, __( 'Failed on line ' . __LINE__, 'regenerate-thumbnails' ) );
		}

		$file = wp_handle_sideload( $file_array, $overrides, $time );

		// get image post
		$image->guid = $file['url'];
		$image->post_mime_type = $file['type'];

		// update original attachment
		$ia = wp_insert_attachment( $image, $file['file'] );

		// update in meta that it got processed
		update_post_meta( $image->ID, '_wsi_photonized', '1' );

		if ( file_exists( $fullsizepath ) ) {
			update_post_meta( $image->ID, '_wsi_original_filesize', filesize( $fullsizepath ) );

			// Should we unlink this file?
			if ( winsite_image_optimizer()->retro->get_setting( 'should-keep-original' ) != '1' ) {
				// Delete old image while we're at it
				unlink( $fullsizepath );
			}
		}

		update_post_meta( $image->ID, '_wsi_engine', (string) WSI_The_Golden_Retriever::get_engine( true ) );

		// refresh our file variables
		$fullsizepath = $file['file'];
		$fullsizeurl = $file['url'];

		if ( false === $fullsizepath || ! file_exists( $fullsizepath ) ) {
			$this->die_json_error_msg( $image->ID, sprintf( __( 'The originally uploaded image file cannot be found at %s', 'regenerate-thumbnails' ), '<code>' . esc_html( $fullsizepath ) . '</code>' ) ); }

		@set_time_limit( 900 ); // 5 minutes per image should be PLENTY

		$metadata = wp_generate_attachment_metadata( $image->ID, $fullsizepath );

		if ( is_wp_error( $metadata ) ) {
			$this->die_json_error_msg( $image->ID, $metadata->get_error_message() ); }

		if ( empty( $metadata ) ) {
			$this->die_json_error_msg( $image->ID, __( 'Unknown failure reason.', 'regenerate-thumbnails' ) ); }

		// If this fails, then it just means that nothing was changed (old value == new value)
		wp_update_attachment_metadata( $image->ID, $metadata );

		die( json_encode( array( 'success' => sprintf( __( '&quot;%1$s&quot; (ID %2$s) was successfully resized in %3$s seconds.', 'regenerate-thumbnails' ), esc_html( get_the_title( $image->ID ) ), $image->ID, timer_stop() ) ) ) );
	}

	/**
	 * @todo update with WP new JSON functions
	 */
	public function die_json_error_msg( $id, $message ) {
		die( json_encode( array( 'error' => sprintf( __( '&quot;%1$s&quot; (ID %2$s) failed to resize. The error message was: %3$s', 'regenerate-thumbnails' ), esc_html( get_the_title( $id ) ), $id, $message ) ) ) );
	}


	/**
	 * * PLUGIN UTILITIES
	 **/

	/**
	 * Retrieve plugin settings
	 *
	 * @uses this::get_post_types
	 * @uses get_option
	 * @return array
	 */
	private function get_settings() {
		if ( empty( self::$settings ) ) {
			$settings = get_option( $this->settings_section, array() );

			if ( empty( $settings ) ) {
				// Defaults
				$settings = apply_filters( 'wsi_default_settings', array(
					'should-keep-original' => '1',
				) );

				update_option( $this->settings_section, $settings );
			}

			self::$settings = $settings;
		}
		return self::$settings;
	}

	/**
	 * Get a single setting
	 *
	 * @uses this::get_settings
	 * @param  string         $key     Key of option to return
	 * @param  boolean|string $default Default option to return if value not found
	 * @return mixed           The option as stored in DB
	 */
	public function get_setting( $key, $default = false ) {
		$settings = $this->get_settings();
		return isset( $settings[ $key ] ) ? $settings[ $key ] : $default;
	}
}
