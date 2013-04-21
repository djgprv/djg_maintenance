<?php
/**
 * This plugin supports a maintenance mode for the Wolf CMS.
 *
 * @package wolf
 * @subpackage plugin.djg_maintenance
 *
 * @author Michał Uchnast <info@kreacjawww.pl>
 */

class DjgMaintenanceController extends PluginController {
	public function __construct() {
		AuthUser::load();
		if ( ! AuthUser::isLoggedIn()) redirect(get_url('login'));
        $this->setLayout('backend');
		$this->assignToLayout('sidebar', new View('../../plugins/djg_maintenance/views/sidebar'));
	}
    public function index() {
        $this->settings();
    }
    public function documentation() {
        $this->display('djg_maintenance/views/documentation');
    }
	public function _changeStatus() {
		$settings['status'] = $_GET['status'];
    Plugin::setAllSettings($settings, 'djg_maintenance');
		if($_POST['status'] == 'on') {
			Observer::notify('log_event', __('Maintenance mode has been enabled by :username.'), __('Maintenance Mode'), DASHBOARD_LOG_NOTICE);
			Flash::set('success', __('Maintenance mode has been enabled.'));
		} elseif ($_POST['status'] == 'off'){
			Observer::notify('log_event', __('Maintenance mode has been disabled by :username.'), __('Maintenance Mode'), DASHBOARD_LOG_NOTICE);
			Flash::set('success', __('Maintenance mode has been disabled.'));
		};
    
		if($_SERVER['HTTP_REFERER'] != ''):
			header('Location: '.$_SERVER['HTTP_REFERER']); // Forward back
		else:
			$url = get_url('plugin/djg_maintenance/');
			header('Location: '.$url); // Forward home
		endif;
	}
	
	public function _save() {
    if ( ( $_POST['redirect_page'] == 'url') && (!preg_match('/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i',$_POST['url_page'])) ):
           Flash::set('error', __('Url is not valid'));
		else:
      $settings = array('users_array' => implode(',',$_POST['users_array']),
        'redirect_page' => $_POST['redirect_page'],
        'url_page' => $_POST['url_page'],
        'backdoor_key'=> $_POST['backdoor_key']
      );
			if (!array_key_exists('backdoor_key_session', $_POST))
				$settings['backdoor_key_session'] = 'off';
			else
				$settings['backdoor_key_session'] = 'on';
			$ip = explode(',',$_POST['global_ip']);
			// removo duplicates
			$ip = array_unique($ip);
			// remove empty entries
			$ip = array_diff($ip, array(''));
			if (count($ip)==0)
				$settings['global_ip'] = '0.0.0.0';
			else
        $settings['global_ip'] = implode(',',$ip);
      if (Plugin::setAllSettings($settings, 'djg_maintenance'))
		        Flash::set('success', __('Plugin settings saved.'));
		    else
		        Flash::set('error', __('Plugin settings not saved!'));
		endif;
		redirect(get_url('plugin/djg_maintenance/settings'));
	}
    function settings() {
		$page = Page::find(array('where' => '`behavior_id` = \'djg_maintenance\'', 'limit' => 1));
      $this->display('djg_maintenance/views/settings', array(
        'status' => Plugin::getSetting('status', 'djg_maintenance'),
        'has_page' => is_object($page),
        'users_array' => Plugin::getSetting('users_array', 'djg_maintenance'),
        'redirect_page' => Plugin::getSetting('redirect_page', 'djg_maintenance'),
        'url_page' => Plugin::getSetting('url_page', 'djg_maintenance'),
        'backdoor_key' => Plugin::getSetting('backdoor_key', 'djg_maintenance'),
        'backdoor_key_session' => Plugin::getSetting('backdoor_key_session', 'djg_maintenance'),
        'global_ip' => Plugin::getSetting('global_ip', 'djg_maintenance')
      ));
    }
}


