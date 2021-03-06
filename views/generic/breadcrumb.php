<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Bookmarks
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */

// Show only previous 6 to fit on one line
if (!$last) {
	$current = array_pop($breadcrumb);
}
$breadcrumb = array_slice($breadcrumb, -6, 6, true);
foreach ($breadcrumb as $uri => &$title) {
	$title = strip_tags($title);
	$title = HTML::anchor($uri, Text::limit_chars($title, 20, '&hellip;', true), array('title' => $title));
}
?>

<nav id="breadcrumb">
<?= implode(" &raquo; \n", $breadcrumb) ?>
</nav>
