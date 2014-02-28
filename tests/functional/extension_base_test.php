<?php
/**
*
* @package testing
* @copyright (c) 2014 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

/**
* @group functional
*/
class extension_functional_extension_base_test extends extension_functional_test_case
{
	/**
	* Test enabling / disabling / purging of an extension
	*
	* @access public
	*/
	public function test_enable_disable_purge_extension()
	{
		$this->login();
		$this->admin_login();
		$this->set_extension('phpbb', 'example', 'phpBB Example Extension');

		// Enable extension
		$this->enable_extension();
		$this->assertTrue($this->is_enabled());

		// Disable extension
		$this->disable_extension();
		$this->assertTrue($this->is_disabled());

		// Purge extension
		$this->purge_extension();
		$this->assertTrue($this->is_available());
	}
}
