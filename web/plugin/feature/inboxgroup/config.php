<?php if(!(defined('_SECURE_'))){die('Intruder alert');}; ?>
<?php
// insert to left menu array
if (isadmin()) {
	$menutab_feature = $core_config['menu']['main_tab']['feature'];
	$arr_menu[$menutab_feature][] = array("index.php?app=menu&inc=feature_inboxgroup&op=list", _('Group inbox'));
}
?>