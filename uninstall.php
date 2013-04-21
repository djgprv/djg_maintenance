<?php
/* Security measure */
if (!defined('IN_CMS')) { exit(); }

if (Plugin::deleteAllSettings('djg_maintenance') === false) {
    Flash::set('error', __('Unable to delete plugin settings.'));
    redirect(get_url('setting'));
}