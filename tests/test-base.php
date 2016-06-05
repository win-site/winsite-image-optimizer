<?php
/**
 * Class WSI_Test_Base
 *
 * @package 
 */

/**
 * WSI_Test_Base
 */
class WSI_Test_Base extends WP_UnitTestCase {

	/**
	 * Test if instance is the same as it should be
	 * 
	 * @return [type] [description]
	 */
	function test_get_instance() {
		$instance = winsite_image_optimizer();
		$this->assertInstanceOf( 'Winsite_Image_Optimizer', $instance );
	}
}