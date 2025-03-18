<?php
/**
 * User: vetal
 * Date: 25.12.15
 * Time: 13:22
 */


switch($do){
	case 'show':
		$themeUrl = SIMPLYBOOK_URL.'/templates/';

		ob_start();
		include_once SIMPLYBOOK_DIR.'/templates/main.php';
		$content = ob_get_contents();
		ob_end_clean();
		break;

	case 'admin_page':
		include_once SIMPLYBOOK_DIR.'/modules/admin-page.php';
		break;
}


?>