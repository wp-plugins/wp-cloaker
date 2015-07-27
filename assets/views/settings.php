<?php
if (!current_user_can('manage_options')) {
    wp_die('You do not have sufficient permissions to access this page.');
}
$path = str_replace('assets'.DIRECTORY_SEPARATOR.'views','',plugin_dir_path( __FILE__ ));
?>
<style>
	.rightcol{
		position:absolute;
		right:0;
		top:115px;
		width:250px;	
	}
	.rightcol h3{
		line-height:30px;	
	}
	.rightcol img{
		max-width:100%;
	}
</style>
<div class="wrap">
	<img class="wp-cloaker-logo" src="<?php echo  plugins_url( 'images/wp-cloaker-logo.png', __FILE__ ) ?>" alt="WP Cloaker Logo" />
	<h2><?php echo __('WP Cloaker Default Settings');?></h2>
    <form method="POST" action="options.php" enctype="multipart/form-data">
    	<?php settings_fields( 'wp_cloaker_settings_group' ); ?>
        <?php do_settings_sections( 'wp_cloaker_link_settings' ); ?>
        <?php submit_button(); ?>
    </form>
    <div class="rightcol">
        <h3>do you have any WordPress project?</h3><br>
        <a href="https://www.upwork.com/users/~01cdee61686dcfce3c" target="_blank" class="hireus">
        	<img src="<?php echo plugin_dir_url(__FILE__).'images/hire-us.png' ?>" />
        </a>
        <p style="text-align:center"><strong>Hourly rate: $22.22</strong></p><br>
        <a href="http://www.wwgate.net" target="_blank">Developed by WWGate</a>
    </div>
</div>