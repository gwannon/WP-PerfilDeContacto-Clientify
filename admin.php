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
		update_option('_wp_pcc_clientify_tag', $_POST['_wp_pcc_clientify_tag']);
		update_option('_wp_pcc_clientify_hash', $_POST['_wp_pcc_clientify_hash']);
		update_option('_wp_pcc_asociada_page_id', $_POST['_wp_pcc_asociada_page_id']);
		update_option('_wp_pcc_asociada_edit_profile_id', $_POST['_wp_pcc_asociada_edit_profile_id']);
		update_option('_wp_pcc_no_photo', $_POST['_wp_pcc_no_photo']);
	} ?>
	<form method="post">
    <h2><?php _e("Configuración Principal", 'wp-perfil-contacto'); ?></h2>
		<b><?php _e("Clientify API key", 'wp-perfil-contacto'); ?>:</b><br/>
		<input type="text" name="_wp_pcc_clientify_api_key" value="<?php echo get_option("_wp_pcc_clientify_api_key"); ?>" style="width: calc(100% - 20px);" /><br/>
		<b><?php _e("Etiqueta para filtrar contactos", 'wp-perfil-contacto'); ?>:</b><br/>
		<input type="text" name="_wp_pcc_clientify_tag" value="<?php echo get_option("_wp_pcc_clientify_tag"); ?>" style="width: calc(100% - 20px);" /><br/>
		
		<b><?php _e("Texto de encriptación", 'wp-perfil-contacto'); ?>:</b><br/>
		<input type="text" name="_wp_pcc_clientify_hash" value="<?php echo get_option("_wp_pcc_clientify_hash"); ?>" style="width: calc(100% - 20px);" /><br/>
		
		
		<b><?php _e("ID de la página del perfil de asociada", 'wp-perfil-contacto'); ?>:</b><br/>
		<input type="text" name="_wp_pcc_asociada_page_id" value="<?php echo get_option("_wp_pcc_asociada_page_id"); ?>" style="width: calc(100% - 20px);" /><br/>
		<b><?php _e("ID de la página donde está el editor de asociada", 'wp-perfil-contacto'); ?>:</b><br/>
		<input type="text" name="_wp_pcc_asociada_edit_profile_id" value="<?php echo get_option("_wp_pcc_asociada_edit_profile_id"); ?>" style="width: calc(100% - 20px);" /><br/>
		<b><?php _e("URL de la imagen cuando no hay foto", 'wp-perfil-contacto'); ?>:</b><br/>
		<input type="text" name="_wp_pcc_no_photo" value="<?php echo get_option("_wp_pcc_no_photo"); ?>" style="width: calc(100% - 20px);" /><br/>
		<br/><input type="submit" name="send" class="button button-primary" value="<?php _e("Guardar", 'wp-perfil-contacto'); ?>" />
	</form>
	<?php
}