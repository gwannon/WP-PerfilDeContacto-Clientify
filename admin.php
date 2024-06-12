<?php

//Administrador 
add_action( 'admin_menu', 'wp_pcc_plugin_menu' );
function wp_pcc_plugin_menu() {
	add_options_page( __('Socias', 'wp-perfil-contacto'), __('Panel de socias', 'wp-perfil-contacto'), 'manage_options', 'wp-perfil-contacto', 'wp_pcc_page_settings');
}

function wp_pcc_page_settings() { 
	?><h1><?php _e("Configuración", 'wp-perfil-contacto'); ?></h1><?php 
	if(isset($_REQUEST['send']) && $_REQUEST['send'] != '') { 
		?><p style="border: 1px solid green; color: green; text-align: center;"><?php _e("Datos guardados correctamente", 'wp-perfil-contacto'); ?></p><?php
		update_option('_wp_pcc_clientify_api_key', $_POST['_wp_pcc_clientify_api_key']);
	} ?>
	<form method="post">
    <h2><?php _e("Configuración Principal", 'wp-perfil-contacto'); ?></h2>
		<b><?php _e("Clientify API key", 'wp-perfil-contacto'); ?>:</b><br/>
		<input type="text" name="_wp_pcc_clientify_api_key" value="<?php echo get_option("_wp_pcc_clientify_api_key"); ?>" style="width: calc(100% - 20px);" /><br/>
		<br/><input type="submit" name="send" class="button button-primary" value="<?php _e("Guardar", 'wp-perfil-contacto'); ?>" />
	</form>
	<?php
}