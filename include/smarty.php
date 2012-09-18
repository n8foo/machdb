<?php
# This file is part of MachDB.  For license info, read LICENSE

require('Smarty/Smarty.class.php');
$smarty = new Smarty();

$smarty->template_dir = "${webroot}/smarty/templates/${templatechoice}/";
$smarty->compile_dir = "${webroot}/smarty/templates_c/";
$smarty->cache_dir = "${webroot}/smarty/cache";
$smarty->config_dir = "${webroot}/smarty/configs";
$smarty->assign('proprietary', $proprietary);

?>
