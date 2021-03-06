<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Debug information
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */

$groups = array(
	'Cache'   => Cache::$queries,
	'Session' => Session::instance()->as_array(),
	'Cookies'  => $_COOKIE,
)
?>

<div id="debug" class="kohana">
	<?php foreach ($groups as $group => $content): ?>
	<table class="profiler">
		<tr class="group">
			<th class="name" colspan="2"><?php echo __(ucfirst($group)) ?> (<?= count($content) ?>)</th>
		</tr>
		<tr class="headers">
			<th class="name"><?php echo __('Key') ?></th>
			<th class="average"><?php echo __('Value') ?></th>
		</tr>
		<?php foreach ($content as $key => $value): ?>
		<tr class="mark memory">
			<th class="name"><?php echo $key ?></th>
			<td class="average">
				<div>
					<div class="value"><?php if (strlen($value = print_r($value, true)) > 100): ?>
						<a href="#" onclick="$(this).next('div').toggle(); return false;"><?php echo __('Show') ?> (<?php echo strlen($value) ?>)</a>
						<div style="display:none"><?php echo $value ?></div>
					<?php else: ?>
						<?php echo $value ?>
					<?php endif ?></div>
				</div>
			</td>
		</tr>
		<?php endforeach ?>
	</table>
	<?php endforeach ?>
</div>
