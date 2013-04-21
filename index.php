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
    'version'     => '0.0.9a',
    'author'      => 'Michał Uchnast',
    'website'     => 'http://www.kreacjawww.pl/',
    'update_url'  => 'http://kreacjawww.pl/public/wolf_plugins/plugin-versions.xml'
));

Behavior::add('djg_maintenance', '');
Observer::observe('page_requested', 'maintenance_page_requested');
Observer::observe('view_page_edit_plugins', 'djg_maintenance_checkbox');
function djg_maintenance_checkbox(&$page)
{
    echo '<p><label for="djg_gallery_checkbox">'.__('Maintenance').'</label> <select id="djg_maintenance_checkbox" name="page[djg_maintenance]">';
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