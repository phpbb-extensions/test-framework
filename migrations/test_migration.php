<?php
/**
*
* @package testing
* @copyright (c) 2014 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbb\example\migrations;

class test_migration extends \phpbb\db\migration\migration
{
	public function update_schema()
	{
		return array(
			'add_tables'		=> array(
				$this->table_prefix . 'test'	=> array(
					'COLUMNS'			=> array(
						'test'	=> array('BOOL', 0),
					),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_tables'		=> array(
				$this->table_prefix . 'test',
			),
		);
	}
}
