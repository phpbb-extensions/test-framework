<?php
/**
*
* @package testing
* @copyright (c) 2014 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbb\example\migrations;

class test_permissions extends \phpbb\db\migration\migration
{
	public function update_data()
	{
		return array(
			array('permission.add', array('a_new')), // New global admin permission a_new
			array('permission.add', array('m_new')), // New global moderator permission m_new
			array('permission.add', array('m_new', false)), // New local moderator permission m_new
			array('permission.add', array('u_new')), // New global user permission u_new
			array('permission.add', array('u_new', false)), // New local user permission u_new

			array('permission.add', array('a_copy', true, 'a_existing')), // New global admin permission a_copy, copies permission settings from a_existing

			array('permission.role_add', array('new admin role', 'a_', 'a new role')), // New admin role "new admin role"
			array('permission.role_add', array('new moderator role', 'm_', 'a new role')), // New admin role "new moderator role"
			array('permission.role_add', array('new user role', 'u_', 'a new role')), // New admin role "new user role"

			array('permission.permission_set', array('ROLE_ADMIN_FULL', 'a_new')), // Give ROLE_ADMIN_FULL a_new permission
			array('permission.permission_set', array('REGISTERED', 'u_new', 'group')), // Give REGISTERED users u_new permission
		);
	}

	public function revert_schema()
	{
		return array(
			array('permission.remove', array('a_new')), // Remove global admin permission a_new
			array('permission.remove', array('m_new')), // Remove global moderator permission m_new
			array('permission.remove', array('m_new', false)), // Remove local moderator permission m_new
			array('permission.remove', array('u_new')), // Remove global user permission u_new
			array('permission.remove', array('u_new', false)), // Remove local user permission u_new

			array('permission.role_remove', array('new admin role')), // Remove role "new admin role"
			array('permission.role_remove', array('new moderator role')), // Remove role "new moderator role"
			array('permission.role_remove', array('new user role')), // Remove role "new user role"

			array('permission.permission_unset', array('ROLE_ADMIN_FULL', 'a_new')), // Remove a_new permission from role ROLE_ADMIN_FULL
			array('permission.permission_unset', array('REGISTERED', 'u_new', 'group')), // Remove u_new permission from group REGISTERED
		);
	}
}
