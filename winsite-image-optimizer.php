<?php
/**
 * Plugin Name: Winsite Image Optimizer
 * Plugin URI: http://win-site.co.il
 * Description: Optimize your images, period.
 * Version: 1.0.6
 * Author: Winsite
 * Author URI: http://win-site.co.il
 * License: GPLv2 or later
 * Text Domain: winsite-images
 */
/*
ATTENTION: ARE YOU A TALENTED...

██╗    ██╗██████╗     ██████╗ ███████╗██╗   ██╗███████╗██╗      ██████╗ ██████╗ ███████╗██████╗ ██████╗
██║    ██║██╔══██╗    ██╔══██╗██╔════╝██║   ██║██╔════╝██║     ██╔═══██╗██╔══██╗██╔════╝██╔══██╗╚════██╗
██║ █╗ ██║██████╔╝    ██║  ██║█████╗  ██║   ██║█████╗  ██║     ██║   ██║██████╔╝█████╗  ██████╔╝  ▄███╔╝
██║███╗██║██╔═══╝     ██║  ██║██╔══╝  ╚██╗ ██╔╝██╔══╝  ██║     ██║   ██║██╔═══╝ ██╔══╝  ██╔══██╗  ▀▀══╝
╚███╔███╔╝██║         ██████╔╝███████╗ ╚████╔╝ ███████╗███████╗╚██████╔╝██║     ███████╗██║  ██║  ██╗
 ╚══╝╚══╝ ╚═╝         ╚═════╝ ╚══════╝  ╚═══╝  ╚══════╝╚══════╝ ╚═════╝ ╚═╝     ╚══════╝╚═╝  ╚═╝  ╚═╝

... IF SO, WE WOULD LIKE TO HEAR FROM YOU AT WINSITE.

EMAIL netta [at] win-site.co.il FOR MORE DETAILS.
*/

/**
 * Here we go!
 *
 * @since  1.0.0
 * @author Maor Chasen <maor@win-site.co.il>
 */
final class Winsite_Image_Optimizer {
	/**
	 * Winsite_images instance.
	 *
	 * @var Winsite_images
	 */
	private static $instance;

	/**
	 * Holds instance of WSI_Hooks
	 * @var object WSI_Hooks
	 */
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
		require_once 'includes/engines/abstract-class-wsi-engine.php';
		require_once 'includes/engines/class-wsi-engine-imageoptim.php';
		require_once 'includes/class-wsi-retro-processor.php';
	}

	/**
	 * Adds actions and filters.
	 */
	private function setup_actions() {
		// setup actions now
		add_action( 'init', array( $this, 'init' ) );

		// Initiate retroactive image optimization handler
		$this->retro = new WSI_Retro_Processor;
	}

	public function init() {
		$this->hooks = new WSI_Hooks;
	}
}

function winsite_image_optimizer() {
	return Winsite_Image_Optimizer::instance();
}
winsite_image_optimizer();
