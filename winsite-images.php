<?php
/*
Plugin Name: Winsite Images
Plugin URI: http://win-site.co.il
Description: Optimize your images, period.
Version: 1.0
Author: Winsite
Author URI: http://win-site.co.il
License: GPLv2 or later
Text Domain: winsite-images
*/

final class Winsite_images {
	/**
	 * Winsite_images instance.
	 *
	 * @var Winsite_images
	 */
	private static $instance;

	public $hooks;

	/**
	 * Class instance.
	 *
	 * @codeCoverageIgnore
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self;
			self::$instance->requirements();
			self::$instance->setup_actions();
		}
		return self::$instance;
	}

	private function requirements() {
		require_once 'includes/class-wsi-hooks.php';
		require_once 'includes/class-wsi-the-golden-retriever.php';
	}

	/**
	 * Adds actions and filters.
	 */
	private function setup_actions() {
		// setup actions now
		add_action( 'init', array( $this, 'init' ) );
	}

	public function init() {
		$this->hooks = new WSI_Hooks;
	}
}

function winsite_images() {
	return Winsite_images::instance();
}
winsite_images();