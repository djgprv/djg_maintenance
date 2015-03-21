<?php

/**
 * Wolf CMS - Content Management Simplified. <http://www.wolfcms.org>
 * Copyright (C) 2008 Martijn van der Kleijn <martijn.niji@gmail.com>
 *
 * This file is part of Wolf CMS.
 *
 * Wolf CMS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Wolf CMS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Wolf CMS.  If not, see <http://www.gnu.org/licenses/>.
 *
 * Wolf CMS has made an exception to the GNU General Public License for plugins.
 * See exception.txt for details and the full text.
 */


/**
 * The djg_maintenance plugin

 * @author Michał Uchnast <djgprv@gmail.com>,
 * @copyright kreacjawww.pl
 * @license http://www.gnu.org/licenses/gpl.html GPLv3 license
 */


/* Security measure */
if (!defined('IN_CMS')) { exit(); }

$url = get_url('plugin/djg_maintenance/_changeStatus');
$status = Plugin::getSetting('status', 'djg_maintenance');
switch ($status) {
    case 'on':
        ?><p class="button"><a style="color:red; text-shadow: 0 0 4px #FFBBCC;" href="<?php echo $url; ?>?status=off"><img src="<?php echo PLUGINS_URI;?>/djg_maintenance/images/48_off.png" align="middle" alt="settings icon" /> <?php echo __('Turn off maintenance mode'); ?></a></p><?php
        break;
    case 'off':
        ?><p class="button"><a style="color:#339900; text-shadow: 0 0 4px #99FFCC;" href="<?php echo $url; ?>?status=on"><img src="<?php echo PLUGINS_URI;?>/djg_maintenance/images/48_on.png" align="middle" alt="settings icon" /> <?php echo __('Turn on maintenance mode'); ?></a></p><?php
        break;
	default:
      echo "<p class='red'>".__('Something wrong, reinstall plugin please!')."</p>";
}
?>
<p class="button"><a href="<?php echo get_url('plugin/djg_maintenance/settings'); ?>"><img src="<?php echo PLUGINS_URI;?>/djg_maintenance/images/32_settings.png" align="middle" alt="settings icon" /> <?php echo __('Settings'); ?></a></p>
<p class="button"><a href="<?php echo get_url('plugin/djg_maintenance/documentation/'); ?>"><img src="<?php echo PLUGINS_URI;?>/djg_maintenance/images/32_documentation.png" align="middle" alt="documentation icon" /> <?php echo __('Documentation'); ?></a></p>
<p class="kreacjawww"><span><a href="http://kreacjawww.pl/">Michał Uchnast</a></span> - djg maintenance plugin</p>