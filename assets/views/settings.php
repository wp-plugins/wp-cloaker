<?php
if (!current_user_can('manage_options')) {
    wp_die('You do not have sufficient permissions to access this page.');
}
$path = str_replace('assets'.DIRECTORY_SEPARATOR.'views','',plugin_dir_path( __FILE__ ));
?>
<div class="wrap">
	<img class="wp-cloaker-logo" src="<?php echo  plugins_url( 'images/wp-cloaker-logo.png', __FILE__ ) ?>" alt="WP Cloaker Logo" />
	<h2><?php echo __('WP Cloaker Default Settings');?></h2>
    <form method="POST" action="options.php" enctype="multipart/form-data">
    	<?php settings_fields( 'wp_cloaker_settings_group' ); ?>
        <?php do_settings_sections( 'wp_cloaker_link_settings' ); ?>
        <?php submit_button(); ?>
    </form>
</div>