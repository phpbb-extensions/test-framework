<?php
/**
*
* @package testing
* @copyright (c) 2014 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbb\example\migrations;

class test_module extends \phpbb\db\migration\migration
{
	public function update_data()
	{
		return array(
			array('module.add', array(
				'acp',
				'ACP_CAT_USERS',
				array(
					'module_basename'	=> '\phpbb\example\acp\example_module',
					'module_langname'	=> 'ACP_EXAMPLE',
					'module_mode'		=> 'settings',
					'module_auth'		=> 'ext_phpbb/example',
				)
			)),
		);
	}

	public function revert_schema()
	{
		return array(
			array('module.remove', array('acp', false, 'ACP_EXAMPLE')),
		);
	}
}
