<?php
/**
 * This plugin supports a maintenance mode for the Wolf CMS.
 *
 * @package wolf
 * @subpackage plugin.djg_maintenance
 *
 * @author Michał Uchnast <info@kreacjawww.pl>
 */

?>
<h1><?php echo __('Settings'); ?></h1>
<?php $users = User::findAll();?>
<?php $users_array = explode(',',Plugin::getSetting('users_array', 'djg_maintenance')); ?>
	<form id="djg_maintenance" action="<?php echo get_url('plugin/djg_maintenance/_save'); ?>" method="post">
	<fieldset style="padding: 0.5em;">
	  <legend style="padding: 0em 0.5em 0em 0.5em; font-weight: bold;"><?php echo __('Redirect page'); ?></legend>
		<ul>
			<li>
			<input type="radio" id="redirect_page" name="redirect_page" value="url" <?php echo ($redirect_page == "url")? 'checked="checked"' :'';?> /> <label for="redirect_page" id="lbl_redirect_page"><?php echo __('URL adress'); ?></label> : <input type="text" id="url_page" name="url_page" value="<?php echo $url_page; ?>" />
			<?php if($has_page == true): ?></li>
			<li>
			<input type="radio" id="behavior_page" name="redirect_page" value="behavior_page" <?php echo ($redirect_page == "behavior_page")? 'checked="checked"' : '';?> /> <label for="behavior_page"><?php echo __('Behavior page (Djg_maintenance)'); ?></label>
			<?php else: ?>
			<?php echo '<p class="red">'.__('Set Djg_maintenance behavior').'</p>'; ?>
      
			<?php endif; ?>
			</li>
		</ul>
	</fieldset>
	<!-- backdoor key -->
	<fieldset style="padding: 0.5em;">
		<legend style="padding: 0em 0.5em 0em 0.5em; font-weight: bold;"><?php echo __('Backdoor key'); ?></legend>
		<input type="text" value="<?php echo $backdoor_key; ?>" class="backdoor_key" name="backdoor_key" />
		<input type="checkbox" id="backdoor_key_session" name="backdoor_key_session" <?php echo ($backdoor_key_session=='on' ? 'checked="checked"' : ''); ?> /> 
		<label for="backdoor_key_session"><?php echo __('Use session to remember key'); ?></label> 
		<p style="color: green;"><a id="keygen"><?php echo __('click to generate random key'); ?></a></p>
		<p class="green"><?php echo __('example: :$url',array(':$url'=>'http://website.com/about-us.html?backdoor=WTW039ar')); ?></p>
	</fieldset>
	<!-- ip -->
	<fieldset style="padding: 0.5em;">
		<legend style="padding: 0em 0.5em 0em 0.5em; font-weight: bold;"><?php echo __('Global ip'); ?></legend>
		<input type="text" value="<?php echo $global_ip; ?>" class="global_ip" name="global_ip" />
		<p style="color: green;"><a id="appendIp"><?php echo __('click to append Your IP'); ?></a> | <a id="resetIp"><?php echo __('reset to default'); ?></a></p>
		
	</fieldset>
	<fieldset style="padding: 0.5em;">
		<legend style="padding: 0em 0.5em 0em 0.5em; font-weight: bold;"><?php echo __('Users list (:$1 / :$2)',array(':$1'=>count($users_array),':$2'=>count($users))); ?></legend>
		<ul>
		<li><input type="hidden" name="users_array[]" value="1" /></li>
		<?php foreach($users as $user): ?>
		<?php if($user->id==1): ?>
		<li><input type="checkbox" disabled="disabled" checked="checked" /> <?php echo $user->username; ?> </li>
		<?php else: ?>
		<li><input type="checkbox" id="users_array" name="users_array[]" <?php if(in_array($user->id,$users_array)): echo 'checked="checked"'; endif; ?> value='<?php echo $user->id; ?>' />
		<label for="users_array"><?php echo $user->username; ?></label> 
		</li>
		<?php endif; ?>
		<?php endforeach; ?>
		</ul>
	</fieldset>

    <p class="buttons">
        <input class="button" name="commit" type="submit" accesskey="s" value="<?php echo __('Save'); ?>" />
    </p>
	</form>
<script type="text/javascript">
    // <![CDATA[
    function setConfirmUnload(on, msg) {
        window.onbeforeunload = (on) ? unloadMessage : null;
        return true;
    }

    function unloadMessage() {
        return '<?php echo __('You have modified this page.If you navigate away from this page without first saving your data, the changes will be lost.'); ?>';
    }

    function password(length, special) {
        var iteration = 0;
        var password = "";
        var randomNumber;
        if (special == undefined) {
            var special = false;
        }
        while (iteration < length) {
            randomNumber = (Math.floor((Math.random() * 100)) % 94) + 33;
            if (!special) {
                if ((randomNumber >= 33) && (randomNumber <= 47)) {
                    continue;
                }
                if ((randomNumber >= 58) && (randomNumber <= 64)) {
                    continue;
                }
                if ((randomNumber >= 91) && (randomNumber <= 96)) {
                    continue;
                }
                if ((randomNumber >= 123) && (randomNumber <= 126)) {
                    continue;
                }
            }
            iteration++;
            password += String.fromCharCode(randomNumber);
        }
        return password;
    }

    $(document).ready(function () {
		// Prevent accidentally navigating away
        $(':input').bind('change', function () {
            setConfirmUnload(true);
        });
		
        $('form').submit(function () {
            setConfirmUnload(false);
            return true;
        });

        $("#appendIp").live("click", function (event) {
			$("input[name=global_ip]").val($("input[name=global_ip]").val()+','+'<?php echo $_SERVER['REMOTE_ADDR']; ?>');
            return false;
        });
        
		$("#resetIp").live("click", function (event) {
			$("input[name=global_ip]").val('0.0.0.0');
			return false;
		});

        $("#keygen").live("click", function (event) {
            $("input[name=backdoor_key]").val('').val($("input[name=backdoor_key]").val() + password(8));
            return false;
        });
		
        $("#lbl_redirect_page").live("click", function (event) {
			$("input[name=url_page]").focus();
            return false;
        });
		
        $("#url_page").focus(function () {
            $("input[name='redirect_page'][value='url']").attr("checked", true);
        });
		
    });
    // ]]>
</script>