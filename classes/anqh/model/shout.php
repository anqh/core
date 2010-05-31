<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Shout model
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Anqh_Model_Shout extends Jelly_Model implements Interface_Permission {

	/**
	 * Create new model
	 *
	 * @param  Jelly_Meta  $meta
	 */
	public static function initialize(Jelly_Meta $meta) {
		$meta
			->sorting(array('id' => 'DESC'))
			->fields(array(
				'id' => new Field_Primary,
				'created' => new Field_Timestamp(array(
					'auto_now_create' => true,
				)),
				'author' => new Field_BelongsTo(array(
					'empty'   => false,
					'column'  => 'author_id',
					'foreign' => 'users.id',
				)),
				'shout'  => new Field_String(array(
					'empty'      => false,
					'min_length' => 1,
					'max_length' => 250,
				)),
			));
	}


	/**
	 * Check permission
	 *
	 * @param   string      $permission
	 * @param   Model_User  $user
	 * @return  boolean
	 */
	public function has_permission($permission, $user) {

		// Logged in user has access to everything for now
		$status = !is_null($user);

		return $status;
	}


	/**
	 * Get object id for Permission
	 *
	 * @return  integer
	 */
	public function get_permission_id() {
		return 0;
	}

}