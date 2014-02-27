<?php
/**
*
* @package testing
* @copyright (c) 2014 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

abstract class extension_functional_test_case extends phpbb_functional_test_case
{
	/**
	* The vendor name of an extension
	* @var string
	*/
	protected $extension_vendor;

	/**
	* The package name of an extension
	* @var string
	*/
	protected $extension_name;

	/**
	* The display name of an extension
	* @var string
	*/
	protected $extension_display_name;

	/**
	* Set an extension vandor/name data up
	*
	* @param string $extension_vendor The vendor name of an extension
	* @param string $extension_name The package name of an extension
	* @param string $extension_display_name The display name of an extension
	* @return null
	* @access public
	*/
	public function set_extension($extension_vendor, $extension_name, $extension_display_name)
	{
		$this->extension_vendor = $extension_vendor;
		$this->extension_name = $extension_name;
		$this->extension_display_name = $extension_display_name;
		
		$this->add_lang('acp/extensions');
	}

	/**
	* Enable an extension in the ACP Extensions area
	*
	* @return null
	* @access public
	*/
	public function enable_extension()
	{
		if ($this->is_enabled())
		{
			return;
		}

		if ($this->is_available() || $this->is_disabled())
		{
			$crawler = self::request('GET', 'adm/index.php?i=acp_extensions&mode=main&action=enable_pre&ext_name=' . $this->extension_vendor . '%2f' . $this->extension_name . '&sid=' . $this->sid);
			$form = $crawler->selectButton($this->lang('EXTENSION_ENABLE'))->form();
			$crawler = self::submit($form);
			$this->assertContainsLang('EXTENSION_ENABLE_SUCCESS', $crawler->text());
		}
		else
		{
			$this->fail($this->extension_display_name . ' not available');
		}
	}

	/**
	* Disable an extension in the ACP Extensions area
	*
	* @return null
	* @access public
	*/
	public function disable_extension()
	{
		if ($this->is_disabled())
		{
			return;
		}

		if ($this->is_enabled())
		{
			$crawler = self::request('GET', 'adm/index.php?i=acp_extensions&mode=main&action=disable_pre&ext_name=' . $this->extension_vendor . '%2f' . $this->extension_name . '&sid=' . $this->sid);
			$form = $crawler->selectButton($this->lang('EXTENSION_DISABLE'))->form();
			$crawler = self::submit($form);
			$this->assertContainsLang('EXTENSION_DISABLE_SUCCESS', $crawler->text());
		}
		else
		{
			$this->fail($this->extension_display_name . ' not enabled');
		}
	}

	/**
	* Purge an extension's data in the ACP Extensions area
	*
	* @return null
	* @access public
	*/
	public function purge_extension()
	{
		if ($this->is_enabled() || $this->is_available)
		{
			return;
		}

		if ($this->is_disabled())
		{
			$crawler = self::request('GET', 'adm/index.php?i=acp_extensions&mode=main&action=delete_data_pre&ext_name=' . $this->extension_vendor . '%2f' . $this->extension_name . '&sid=' . $this->sid);
			$form = $crawler->selectButton($this->lang('EXTENSION_DELETE_DATA'))->form();
			$crawler = self::submit($form);
			$this->assertContainsLang('EXTENSION_DELETE_DATA_SUCCESS', $crawler->text());
		}
		else
		{
			$this->fail($this->extension_display_name . ' not disabled');
		}
	}

	/**
	* Check if the extension is enabled
	*
	* @return bool is extension found in the enabled list
	* @access protected
	*/
	protected function is_enabled()
	{
		$crawler = self::request('GET', 'adm/index.php?i=acp_extensions&mode=main&sid=' . $this->sid);
		$enabled_extensions = $crawler->filter('tr.ext_enabled')->extract(array('_text'));
		foreach ($enabled_extensions as $extension)
		{
			if (strpos($extension, $this->extension_display_name) !== false)
			{
				return true;
			}
		}

		return false;
	}

	/**
	* Check if the extension is disabled
	*
	* @return bool is extension found in the disabled list and available to be enabled or purged
	* @access protected
	*/
	protected function is_disabled()
	{
		$crawler = self::request('GET', 'adm/index.php?i=acp_extensions&mode=main&sid=' . $this->sid);

		$is_disabled = false;

		$crawler->filter('tr.ext_disabled')->each(function ($node, $i) use ($is_disabled) {
			$children = $node->children();
			if (strpos($children[0]->text(), $this->extension_display_name) !== false && strpos($children[2]->text(), $this->lang('EXTENSION_DELETE_DATA')) !== false)
			{
				$is_disabled = true;
			}
		});

		return $is_disabled;
	}

	/**
	* Check if the extension is available
	*
	* @return bool is extension found in the disabled list and available to be enabled
	* @access protected
	*/
	protected function is_available()
	{
		$crawler = self::request('GET', 'adm/index.php?i=acp_extensions&mode=main&sid=' . $this->sid);

		$is_available = false;

		$crawler->filter('tr.ext_disabled')->each(function ($node, $i) use ($is_disabled) {
			$children = $node->children();
			if (strpos($children[0]->text(), $this->extension_display_name) !== false && strpos($children[2]->text(), $this->lang('EXTENSION_DELETE_DATA')) === false)
			{
				$is_available = true;
			}
		});

		return $is_available;
	}
}
