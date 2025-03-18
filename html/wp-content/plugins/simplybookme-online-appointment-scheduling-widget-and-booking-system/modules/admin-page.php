<?php
/**
 * User: vetal
 * Date: 24.12.15
 * Time: 18:14
 */

try {
	if ( isset( $_POST['submit'] ) ) {  //if form submitted
		unset( $_POST['submit'] );

		$simplybookCfg = updateConfig($_POST);

		echo '<div id="message" class="updated">
		        <p><strong>'.sbGetText("Settings saved").'</strong></p>
		    </div>';
	}
} catch (Exception $e) {

	echo '<div id="message" class="error">
		        <p><strong>'.sbGetText($e->getMessage()).'</strong></p>
		    </div>';
}

function updateConfig($data, $isRecursive = false){
	global $simplybookDomain, $simplybookCfg;
	$res = array();

	foreach ( $data as $key => $val ) {
		if ( isset( $data[ $key ] ) ) { //if key exist
			if(is_array($val)){
				$res[ $key ] = updateConfig($val, true);
			} else {
				switch ( $key ) {
					case 'login':
						$val = trim( $val );
						if ( strlen( $val ) < 2 || strlen( $val ) > 128 ) {
							throw new Exception( sbGetText( 'Not a valid company login' ) );
						}
						$res[ $key ] = sanitize_text_field( $val );
						break;
					case 'hide_img_mode':
					case 'is_rtl':
						$res[ $key ] = intval( $val );
						break;
					default:
						$res[ $key ] = sanitize_text_field( $val );
						break;
				}
			}
		}
	}

	if(!$isRecursive) {
		$res = array_merge($simplybookCfg, $res);
		update_option( $simplybookDomain,  $res);
		simplybook_clear_cache();
	}

	return $res;
}

if(isset($_REQUEST['clear_cache'])){
	simplybook_clear_cache();
}


//$theme_list = simplybook_get_theme_list();
$theme_options = '';
/*foreach($theme_list as $theme){
	$theme_options .= "<option value='{$theme['name']}' ".($theme['name']==$simplybookCfg['template']?'selected':'').">{$theme['name']}</option>";
}*/

include_once SIMPLYBOOK_TPL_DIR . '/admin.php';

?>