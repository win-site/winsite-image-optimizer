<?php
/**
 * Class WSI_Test_Base
 *
 * @package
 */

require_once 'mocks/class-wsi-engine-dummy-mock.php';

/**
 * WSI_Test_Base
 */
class WSI_Test_Image_Optimizer extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
	}

	/**
	 * Test if instance is the same as it should be
	 *
	 * @return [type] [description]
	 */
	function test_get_instance() {
		$instance = winsite_image_optimizer();
		$this->assertInstanceOf( 'Winsite_Image_Optimizer', $instance );
	}


	function test_hooks_instance_exists() {
		$hooks_instance = winsite_image_optimizer()->hooks;
		$this->assertInstanceOf( 'WSI_Hooks', $hooks_instance );
	}

	function test_hooks_instance_filters_in_place() {
		$hooks_instance = winsite_image_optimizer()->hooks;

		// has_filter() will return the priority this filter will run in. test against that.
		$this->assertGreaterThan( 0, has_filter( 'wp_handle_upload', array( $hooks_instance, 'filter_wp_handle_upload' ) ) );
		$this->assertGreaterThan( 0, has_filter( 'wp_update_attachment_metadata', array( $hooks_instance, 'filter_wp_update_attachment_metadata' ) ) );
		$this->assertGreaterThan( 0, has_filter( 'wp_prepare_attachment_for_js', array( $hooks_instance, 'filter_wp_prepare_attachment_for_js' ) ) );
	}

	function test_golden_retriever_get_engine() {
		$this->assertInstanceOf( 'WSI_Engine_ImageOptim', WSI_The_Golden_Retriever::get_engine() );

		add_filter('wsi_available_image_processing_engines', function( $engines ) {
			$engines['mock'] = 'WSI_Engine_Dummy_Mock';
			return $engines;
		});

		add_filter('wsi_default_image_processing_engine', function( $engines ) {
			return 'mock';
		});

		// assure that the engine overriding works
		$this->assertInstanceOf( 'WSI_Engine_Dummy_Mock', WSI_The_Golden_Retriever::get_engine() );
	}
}
