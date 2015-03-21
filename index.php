<?php
/**
 * This plugin supports a djg_maintenance mode for the Wolf CMS.
 *
 * @package wolf
 * @subpackage plugin.djg_maintenance
 *
 * @author Michał Uchnast <info@kreacjawww.pl>
 * support for Mike Tuupola´s Dashboard plugin
 */

Plugin::setInfos(array(
    'id'          => 'djg_maintenance',
    'title'       => __('[djg] Maintenance'),
    'description' => __('Helps you to take your site offline for a short time sending correct HTTP header.'),
    'version'     => '0.10',
    'author'      => 'Michał Uchnast',
    'website'     => 'http://www.kreacjawww.pl/',
    'update_url'  => 'https://raw.githubusercontent.com/djgprv/djg_maintenance/master/versions.xml'
));

/**
 * Root location where djg_maintenance plugin lives.
 */
define('DJG_MAINTENANCE_ROOT', PATH_PUBLIC.'wolf/plugins/djg_maintenance');

// Load class into the system.
AutoLoader::addFolder(dirname(__FILE__) . '/models');

Behavior::add('djg_maintenance', '');
Observer::observe('page_requested', 'maintenance_page_requested');
Observer::observe('view_page_edit_plugins', 'djg_maintenance_display_dropdown');
Observer::observe('page_add_after_save',  'djg_maintenance_on_page_saved');
Observer::observe('page_edit_after_save', 'djg_maintenance_on_page_saved');

function djg_maintenance_on_page_saved($page) {
    $status = 0;
    $input = $_POST['page'];

    if (isset($input['djg_maintenance']) && is_int((int)$input['djg_maintenance']))
        $status = $input['djg_maintenance'];

    Record::update('Page', array('djg_maintenance' => $status), 'id = ?', array($page->id));
}

function djg_maintenance_display_dropdown(&$page)
{
    echo '<p><label for="page_djg_maintenance_status">'.__('Maintenance').'</label><select id="page_djg_maintenance_status" name="page[djg_maintenance]">';
    echo '<option value="1"'.($page->djg_maintenance == 1 ? ' selected="selected"': '').'>'.__('exception').'</option>';
    echo '<option value="0"'.($page->djg_maintenance == 0 ? ' selected="selected"': '').'>'.__('no exception').'</option>';
    echo '</select></p>';	
}
Plugin::addController('djg_maintenance', __('[djg] Maintenance'),true,false);

function maintenance_page_requested($uri) {
	global $__CMS_CONN__;
	AuthUser::load();
	$users_array = explode(',',Plugin::getSetting('users_array', 'djg_maintenance'));
	$entrance = 0;
	if( (AuthUser::isLoggedIn()) and (in_array(AuthUser::getId(),$users_array)) ) $entrance++;
	/* backodor */
	if(Plugin::getSetting('backdoor_key_session', 'djg_maintenance')== 'off') $_SESSION['djg_maintenance'] = '0';
	if( (isset($_REQUEST['backdoor'])) && ($_REQUEST['backdoor']==Plugin::getSetting('backdoor_key', 'djg_maintenance')) ): 
		$entrance++; 
		$_SESSION['djg_maintenance'] = '1'; 
	endif;
	if ( (isset($_SESSION['djg_maintenance'])) && ($_SESSION['djg_maintenance'] == '1') && (Plugin::getSetting('backdoor_key_session', 'djg_maintenance') == 'on') ) $entrance++;
	if (in_array($_SERVER['REMOTE_ADDR'], explode(',',Plugin::getSetting('global_ip', 'djg_maintenance')))) $entrance++;
	/* exception */
	$pageobject = Page::find('/'.$uri); if (is_object($pageobject) && $pageobject->djg_maintenance == '1') $entrance++;
	/* display */
	if( (Plugin::getSetting('status', 'djg_maintenance') == 'on') and ($entrance == 0) ){
		// Load Page
		$page = Page::find(array('where' => '`behavior_id` = \'djg_maintenance\'', 'limit' => 1));
		if( (is_object($page)) and (Plugin::getSetting('redirect_page', 'djg_maintenance')=='behavior_page') ) {
			$page_id = $page->id;
			while((int) $page->layout_id == 0) {
				$stmt = $__CMS_CONN__->query('SELECT parent_id, layout_id FROM '.TABLE_PREFIX.'page WHERE `id` = '.$page_id);
				$obj = $stmt->fetchObject();
				$page_id = $obj->parent_id;
				$page->layout_id = $obj->layout_id;
			}
			header('HTTP/1.0 503 Service unavailable');
			header('Status: 503 Service unavailable');
			$page->_executeLayout();
			exit();
		}elseif(Plugin::getSetting('redirect_page', 'djg_maintenance')=='url'){
			header('Location: '.Plugin::getSetting('url_page', 'djg_maintenance'));
		}else{
			header('HTTP/1.0 503 Service unavailable');
			header('Status: 503 Service unavailable');
			$page->_executeLayout();
			exit();
		}
	}else{
		return CURRENT_URI;
	}
}
?>