<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * View fix for exceptions
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2010-2011 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Anqh_View extends Kohana_View {

	/**
	 * Magic method, returns the output of [View::render].
	 *
	 * @return  string
	 * @uses    View::render
	 */
	public function __toString() {
		try {
			return $this->render();
		} catch (Exception $e) {

			// Display the exception message only if not in production
			ob_start();
			Anqh_Exception::handler($e);

			if (Kohana::$environment === Kohana::PRODUCTION) {
				ob_end_clean();
				return __('An error occured and has been logged.');
			} else {
				return ob_get_clean();
			}
		}
	}


}
