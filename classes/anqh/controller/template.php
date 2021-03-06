<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Abstract Anqh Template controller
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2010-2011 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
abstract class Anqh_Controller_Template extends Controller {

	/**
	 * @var  string  Page width setting, 'fixed' or 'liquid'
	 */
	protected $page_width = 'fixed';

	/**
	 * @var  string  Skin for the site
	 */
	protected $skin;

	/**
	 * @var  array  Skin files imported in skin, check against file modification time for LESS
	 */
	protected $skin_imports;

	/**
	 * @var  string  page template
	 */
	public $template = 'template';


	/**
	 * Construct controller
	 */
	public function before() {
		parent::before();

		$this->auto_render = !$this->internal;
		$this->breadcrumb = Session::instance()->get('breadcrumb', array());
		$this->history = $this->history && !$this->ajax;

		// Load the template
		if ($this->auto_render === true) {
			$this->template = View::factory($this->template);
		}

	}


	/**
	 * Destroy controller
	 */
	public function after() {
		if ($this->ajax || $this->internal) {

			// AJAX and HMVC requests
			$this->response->body($this->response->body() . '');

		} else if ($this->auto_render) {

			// Normal requests

			$session = Session::instance();

			// Save current URI
			/* Moved to Controller
			if ($this->history && $this->response->status() < 400) {
				$uri = $this->request->current_uri();
				unset($this->breadcrumb[$uri]);
				$this->breadcrumb = array_slice($this->breadcrumb, -9, 9, true);
				$this->breadcrumb[$uri] = $this->page_title;
				$session
					->set('history', $uri . ($_GET ? URL::query($_GET) : ''))
					->set('breadcrumb', $this->breadcrumb);
			}
			 */

			// Controller name as the default page id if none set
			empty($this->page_id) and $this->page_id = $this->request->controller();


			// Stylesheets
			$styles = array(
				'ui/boot.css'      => null, // Reset
				'ui/typo.css'      => null, // Typography
				'ui/base.css'      => null, // Deprecated
				'ui/jquery-ui.css' => null, // Deprecated
				'http://fonts.googleapis.com/css?family=Nobile:regular,bold' => null,
			);


			// Generic views
			Widget::add('breadcrumb', View::factory('generic/breadcrumb', array('breadcrumb' => $this->breadcrumb, 'last' => !$this->history)));
			Widget::add('actions',    View::factory('generic/actions',    array('actions' => $this->page_actions)));
			Widget::add('navigation', View::factory('generic/navigation', array(
				'items'    => Kohana::$config->load('site.menu'),
				'selected' => $this->page_id,
			)));
			if (!empty($this->tabs)) {
				Widget::add('subnavigation', View::factory('generic/navigation', array(
					'items'    => $this->tabs,
					'selected' => $this->tab_id,
				)));
			}
			/*
			Widget::add('tabs', View::factory('generic/tabs_top', array(
				'tabs'     => $this->tabs,
				'selected' => $this->tab_id
			)));
			 */

			// Footer
			Widget::add('footer', View_Module::factory('events/event_list', array(
				'mod_id'    => 'footer-events-new',
				'mod_class' => 'article grid4 first cut events',
				'mod_title' => __('New events'),
				'events'    => Model_Event::factory()->find_new(10)
			)));
			Widget::add('footer', View_Module::factory('forum/topiclist', array(
				'mod_id'    => 'footer-topics-active',
				'mod_class' => 'article grid4 cut topics',
				'mod_title' => __('New posts'),
				'topics'    => Model_Forum_Topic::factory()->find_by_latest_post(10)
			)));
			Widget::add('footer', View_Module::factory('blog/entry_list', array(
				'mod_id'    => 'footer-blog-entries',
				'mod_class' => 'article grid4 cut blogentries',
				'mod_title' => __('New blogs'),
				'entries'   => Model_Blog_Entry::factory()->find_new(10),
			)));


			// Skin
			$skins = Kohana::$config->load('site.skins');
			$skin = 'dark';//$session->get('skin', 'dark');
			$skin_imports = array(
				'ui/mixin.less',
				'ui/grid.less',
				'ui/layout.less',
				'ui/widget.less',
				'ui/custom.less'
			);

			// Dock
			$classes = array(
//				HTML::anchor(Route::get('setting')->uri(array('action' => 'width', 'value' => 'narrow')), __('Narrow'), array('onclick' => '$("body").toggleClass("fixed", true).toggleClass("liquid", false); $.get(this.href); return false;')),
//				HTML::anchor(Route::get('setting')->uri(array('action' => 'width', 'value' => 'wide')),   __('Wide'),   array('onclick' => '$("body").toggleClass("liquid", true).toggleClass("narrow", false); $.get(this.href); return false;')),
//				HTML::anchor(Route::get('setting')->uri(array('action' => 'main',  'value' => 'left')),   __('Left'),   array('onclick' => '$("body").toggleClass("left", true).toggleClass("right", false); $.get(this.href); return false;')),
//				HTML::anchor(Route::get('setting')->uri(array('action' => 'main',  'value' => 'right')),  __('Right'),  array('onclick' => '$("body").toggleClass("right", true).toggleClass("left", false); $.get(this.href); return false;')),
			);
			foreach ($skins as $skin_name => &$skin_config) {
				$skin_config['path'] = 'ui/' . $skin_name . '/skin.less';
				$classes[] = HTML::anchor(
					Route::get('setting')->uri(array('action' => 'skin', 'value' => $skin_name)),
					$skin_config['name'],
					array(
						'class' => 'theme',
						'rel'   => $skin_name,
					));
			}
			//Widget::add('dock', __('Theme') . ': ' . implode(', ', $classes));

			// Language selection
			$available_languages  = Kohana::$config->load('locale.languages');
			if (count($available_languages)) {
				$languages = array();
				foreach ($available_languages as $lang => $locale) {
					$languages[] = HTML::anchor('set/lang/' . $lang, HTML::chars($locale[2]));
				}
//				Widget::add('dock', ' | ' . __('Language: ') . implode(', ', $languages));
			}

			// Search
			/*
			Widget::add('search', View_Module::factory('generic/search', array(
				'mod_id' => 'search'
			)));
			 */

			// Visitor card
			Widget::add('visitor', View::factory('generic/visitor', array(
				'user' => self::$user,
			)));

			// Time & weather
			Widget::add('dock', ' | ' . View::factory('generic/clock', array(
				'user' => self::$user,
			)));

			// Pin
			Widget::add('dock', ' | ' . HTML::anchor('#pin', '&#9650;', array('title' => __('Lock menu'), 'class' => 'icon unlock', 'onclick' => '$("#header").toggleClass("pinned"); return false;')));

			// End
			Widget::add('end', View::factory('generic/end'));

			// Analytics
			if ($google_analytics = Kohana::$config->load('site.google_analytics')) {
				Widget::add('head', HTML::script_source("
var tracker;
head.js(
	{ 'google-analytics': 'http://www.google-analytics.com/ga.js' },
	function() {
		tracker = _gat._getTracker('" . $google_analytics . "');
		tracker._trackPageview();
	}
);
"));
			}

			// Open Graph
			$og = array();
			foreach ((array)Anqh::open_graph() as $key => $value) {
				$og[] = '<meta property="' . $key . '" content="' . HTML::chars($value) . '" />';
			}
			if (!empty($og)) {
				Widget::add('head', implode("\n", $og));
			}

			// Share
			if (Anqh::share()) {
				if ($share = Kohana::$config->load('site.share')) {

					// 3rd party share
					Widget::add('share', View_Module::factory('share/share', array('mod_class' => 'like', 'id' => $share)));
					Widget::add('foot', View::factory('share/foot', array('id' => $share)));

				} else if ($facebook = Kohana::$config->load('site.facebook')) {

					// Facebook Like
					Widget::add('share', View_Module::factory('facebook/like'));
					Widget::add('ad_top', View::factory('facebook/connect', array('id' => $facebook)));

				}
			}


			// Ads
			$ads = Kohana::$config->load('site.ads');
			if ($ads && $ads['enabled']) {
				foreach ($ads['slots'] as $ad => $slot) {
					Widget::add($slot, View::factory('ads/' . $ad), Widget::MIDDLE);
				}
			}

			// And finally the profiler stats
			if (self::$user && self::$user->has_role('admin')) { //in_array(Kohana::$environment, array(Kohana::DEVELOPMENT, Kohana::TESTING))) {
				Widget::add('foot', View::factory('generic/debug'));
				Widget::add('foot', View::factory('profiler/stats'));
			}

			// Do some CSS magic to page class
			$page_class = explode(' ',
				$this->language . ' ' .                      // Language
				$session->get('page_width', 'fixed') . ' ' . // Fixed/liquid layout
				$session->get('page_main', 'left') . ' ' .   // Left/right aligned layout
				$this->request->action() . ' ' .             // Controller method
				$this->page_class);                          // Controller set classes
			$page_class = implode(' ', array_unique(array_map('trim', $page_class)));

			// Bind the generic page variables
			$this->template
				->set('styles',        $styles)
				->set('skin',          $skin)
				->set('skins',         $skins)
				->set('skin_imports',  $skin_imports)
				->set('language',      $this->language)
				->set('page_id',       $this->page_id)
				->set('page_class',    $page_class)
				->set('page_title',    $this->page_title)
				->set('page_subtitle', $this->page_subtitle);

			// Add statistics
			$queries = 0;
			if (Kohana::$profiling) {
				foreach (Profiler::groups() as $group => $benchmarks) {
					if (strpos($group, 'database') === 0) {
						$queries += count($benchmarks);
					}
				}
			}
			$total = array(
				'{memory_usage}'     => number_format((memory_get_peak_usage() - KOHANA_START_MEMORY) / 1024, 2) . 'KB',
				'{execution_time}'   => number_format(microtime(true) - KOHANA_START_TIME, 5),
				'{database_queries}' => $queries,
				'{included_files}'   => count(get_included_files()),
			);
			$this->template = strtr($this->template, $total);

			// Render page
			if ($this->auto_render === true) {
				$this->response->body($this->template);
			}

		}

		return parent::after();
	}


	/**
	 * Print an error message
	 *
	 * @param  string  $message
	 */
	public function error($message = null) {
		!$message && $message = __('Error occured.');

		Widget::add('error', View_Module::factory('generic/error', array('message' => $message)));
	}

}
