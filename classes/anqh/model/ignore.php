<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Ignore model
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2011 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Anqh_Model_Ignore extends Jelly_Model {

	/**
	 * Create new model
	 *
	 * @param  Jelly_Meta  $meta
	 */
	public static function initialize(Jelly_Meta $meta) {
		$meta->fields(array(
			'id'     => new Field_Primary,
			'user'   => new Field_BelongsTo,
			'ignore' => new Field_BelongsTo(array(
				'column'  => 'ignore_id',
				'foreign' => 'user'
			)),
			'created' => new Field_Timestamp(array(
				'auto_now_create' => true,
			)),
		));
	}

}