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
	public function setUp()
	{
		parent::setUp();

		$this->vendor = 'phpbb';
		$this->extension = 'example';
		$this->display_name = 'phpBB Example Extension';
		
		$this->login();
		$this->admin_login();
		$this->set_extension($this->vendor, $this->extension, $this->display_name);
	}

	/**
	* Test enabling / disabling / purging of an extension
	*
	* @access public
	*/
	public function test_enable__diabel_purge_extension()
	{
		// Enable extension
		$this->enable_extension();

		// Test enabled extension exists
		$crawler = self::request('GET', 'adm/index.php?i=acp_extensions&mode=main&sid=' . $this->sid);
		$enabled_extensions = $crawler->filter('tr.ext_enabled')->extract(array('_text'));
		foreach ($enabled_extensions as $extension)
		{
			if (strpos($extension, $this->display_name) !== 0)
			{
				continue;
			}
			
			$this->assertEquals($this->display_name, $extension);
		}

		// Disable extension
		$this->disable_extension();

		// Test disabled extension exists
		$crawler = self::request('GET', 'adm/index.php?i=acp_extensions&mode=main&sid=' . $this->sid);
		$disabled_extensions = $crawler->filter('tr.ext_disabled')->extract(array('_text'));
		foreach ($disabled_extensions as $extension)
		{
			if (strpos($extension, $this->display_name) !== 0)
			{
				continue;
			}
			
			$this->assertEquals($this->display_name, $extension);
		}

		// Purge extension
		$this->purge_extension();

		// Test purged extension exists
		$crawler = self::request('GET', 'adm/index.php?i=acp_extensions&mode=main&sid=' . $this->sid);
		$disabled_extensions = $crawler->filter('tr.ext_disabled')->extract(array('_text'));
		foreach ($disabled_extensions as $extension)
		{
			if (strpos($extension, $this->display_name) !== 0)
			{
				continue;
			}
			
			$this->assertEquals($this->display_name, $extension);
		}
	}
}
