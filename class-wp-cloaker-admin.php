<?php
require_once(plugin_dir_path(__FILE__).'class-wp-cloaker-clicks.php');

class WP_Cloaker_Admin{
	public function initialize(){
		add_filter( 'manage_edit-wp_cloaker_link_columns', array($this,'wp_cloaker_edit_link_columns') ) ;
		add_filter( 'manage_edit-wp_cloaker_link_category_columns', array($this,'wp_cloaker_edit_link_category_columns') ) ;
		add_filter( 'manage_wp_cloaker_link_posts_custom_column', array($this,'wp_cloaker_columns_data') ) ;
		add_filter( 'manage_wp_cloaker_link_category_custom_column', array($this,'wp_cloaker_category_columns_data'),10,3 ) ;
		//add setting page menu
		add_action("admin_menu",array($this,'wp_cloaker_setting_page_init'));
		//register settings page
		add_action('admin_init', array($this,'register_wp_cloaker_settings'));
		// pre update 
		add_filter( 'pre_update_option_wp_cloaker_link_prefix', array($this,'wp_cloaker_link_slug_change'), 10, 2 );
	}
	//add clicks column to wp cloaker link edit page
	public function wp_cloaker_edit_link_columns($columns){
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'title' => __('Link'),
			'category' => __('Link Categories'),
			'clicks' => __('Clicks'),
			'link' => __('Link'),
			'date' => __('Date')
		);
		return $columns;
	}
	// add click to link category edit page
	public function wp_cloaker_edit_link_category_columns($columns){
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'name' => __('Name'),
			'description' => __('Description'),
			'slug' => __('Slug'),
			'clicks' => __('Clicks'),
			'posts' => __('Count'),
		);
		return $columns;
	}
	//add content to the custom columns
	public function wp_cloaker_columns_data($column){
		global $post;
		$post_id = $post->ID;
		switch( $column ){
			case 'category':
				$terms = get_the_terms( $post_id, 'wp_cloaker_link_category' );
				if ( !empty( $terms ) ) {
					/* Loop through each term, linking to the 'edit posts' page for the specific term. */
					foreach ( $terms as $term ) {
						$out[] = sprintf( '<a href="%s">%s</a>',
							esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'wp_cloaker_link_category' => $term->slug ), 'edit.php' ) ),
							esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'wp_cloaker_link_category', 'display' ) )
						);
					}
	
					/* Join the terms, separating them with a comma. */
					echo join( ', ', $out );	
				}else{
					echo __('Uncategorized');	
				}
			break;	
			case 'clicks':
				$clickObj = new WP_Cloaker_Clicks();
				$clicks = $clickObj->getClickCountByPostID($post_id);
				if($clicks){
					echo $clicks;
				}else{
					echo '0';	
				}
			break;
			case 'link':
				echo '<div class="copylink-container"><input class="copythis" type="text" disabled value="'.get_permalink($post_id).
				'"/> <span class="copy add-new-h2" data-clipboard-target="copythis" datalink="'.
				get_permalink($post_id)
				.'">Copy</span></div>';
			break;
			default:
			break;
		}
	}
	//add count content to the link category admin table
	public function wp_cloaker_category_columns_data($out,$column,$tax_id){
		switch( $column ){
			case 'clicks':
				//add clicks count by category id
				$term = get_term( $tax_id, 'wp_cloaker_link_category' );
				$clickObj = new WP_Cloaker_Clicks();
				$clicks = $clickObj->getClickCountByCatID($term->term_id);
				$out = (isset($clicks))?$clicks:'0';
			break;
			default:
			break;
		}
		return $out;
	}
	
	//setting page function 
	public function wp_cloaker_setting_page_init(){
		add_submenu_page('edit.php?post_type=wp_cloaker_link', 'WP Cloaker Settings', 'Settings', 'manage_options', 'wp_cloaker_link_settings', array($this,'wp_cloaker_setting_page_contnet'));
	}
	//setting page content
	public function wp_cloaker_setting_page_contnet(){
		require_once(plugin_dir_path(__FILE__).'assets/views/settings.php');
	}
	//register setting fields
	public function register_wp_cloaker_settings(){
		add_settings_section( 'default_settings', '', array($this,'default_settings_callback'), 'wp_cloaker_link_settings' );
		register_setting( 'wp_cloaker_settings_group', 'wp_cloaker_link_prefix' );
		add_settings_field( 'wp_cloaker_link_prefix', 'Link Prefix', array($this,'wp_cloaker_link_prefix_callback'), 'wp_cloaker_link_settings', 'default_settings' );
		
		register_setting( 'wp_cloaker_settings_group', 'wp_cloaker_link_redirection' );
		add_settings_field( 'wp_cloaker_link_redirection', 'Link Redirection', array($this,'wp_cloaker_link_redirection_callback'), 'wp_cloaker_link_settings', 'default_settings' );
		
		/*register_setting( 'wp_cloaker_settings_group', 'wp_cloaker_link_target' );
		add_settings_field( 'wp_cloaker_link_target', 'Link Target', array($this,'wp_cloaker_link_target_callback'), 'wp_cloaker_link_settings', 'default_settings',array('disabled'=>'disabled') );
		
		register_setting( 'wp_cloaker_settings_group', 'wp_cloaker_link_nofollow' );
		add_settings_field( 'wp_cloaker_link_nofollow', '', array($this,'wp_cloaker_link_nofollow_callback'), 'wp_cloaker_link_settings', 'default_settings' );*/
		
		register_setting( 'wp_cloaker_settings_group', 'wp_cloaker_link_tracking_code' );
		add_settings_field( 'wp_cloaker_link_tracking_code', '3rd Party tracking code', array($this,'wp_cloaker_link_tracking_code_callback'), 'wp_cloaker_link_settings', 'default_settings' );
	}
	// call back function for add settings section
	public function default_settings_callback(){
		//echo 'some text';	
	}
	public function wp_cloaker_link_prefix_callback() {
		$setting = esc_attr( get_option( 'wp_cloaker_link_prefix','visit' ) );
		echo "<input type='text' name='wp_cloaker_link_prefix' value='$setting' />";
	}	
	public function wp_cloaker_link_redirection_callback(){
		$linkRedirection = esc_attr( get_option( 'wp_cloaker_link_redirection','301' ) );?>
		<select name="wp_cloaker_link_redirection">
			<option value="301" <?php echo $linkRedirection == "301" ?  'selected="selected"':''; ?>>301 redirection</option>
			<option value="302" <?php echo $linkRedirection == "302" ?  'selected="selected"':''; ?>>302 redirection</option>
			<option value="303" <?php echo $linkRedirection == "303" ?  'selected="selected"':''; ?>>303 redirection</option>
			<option value="307" <?php echo $linkRedirection == "307" ?  'selected="selected"':''; ?>>307 redirection</option>
			<option value="js" <?php echo $linkRedirection == "js" ?  'selected="selected"':''; ?>>JavaScript redirection</option>
		</select><?php
	}
	public function wp_cloaker_link_target_callback(){
		$linkTarget = esc_attr( get_option( 'wp_cloaker_link_target','_self' ) );?>
        <select name="wp_cloaker_link_target">
            <option value="_self" <?php echo $linkTarget == "_self" ?  'selected="selected"':''; ?>>same window</option>
            <option value="_blank" <?php echo $linkTarget == "_blank" ?  'selected="selected"':''; ?>>new window</option>
        </select>
        <?php
	}
	public function wp_cloaker_link_nofollow_callback(){
		$linkNofollow = esc_attr( get_option( 'wp_cloaker_link_nofollow','0' ) );?>
        <label for="wp_cloaker_link_nofollow">
            <input type="checkbox" name="wp_cloaker_link_nofollow" <?php if($linkNofollow) echo 'checked="checked"'; ?>/>
        Add no follow to the link</label><?php
	}
	public function wp_cloaker_link_tracking_code_callback(){
		$tracking = esc_attr( get_option( 'wp_cloaker_link_tracking_code' ) );?>
        <textarea rows="10" cols="60" name="wp_cloaker_link_tracking_code"><?php echo $tracking;?></textarea>
        <?php
	}
	// change wp cloaker link post slug if the option changed
	public function wp_cloaker_link_slug_change($new_value,$old_value ){
		if($new_value !== $old_value ){
			//check if the old value is empty
			if(empty($old_value)) $old_value = 'visit';
			// First, try to load up the rewrite rules. We do this just in case
    		// the default permalink structure is being used.
			if( ($current_rules = get_option('rewrite_rules')) ) {
				// Next, iterate through each custom rule adding a new rule
				// that replaces 'visit' with 'option value' and give it a higher
				// priority than the existing rule.
				foreach($current_rules as $key => $val) {
					if(strpos($key, $old_value ) !== false) {
						add_rewrite_rule(str_ireplace($old_value, $new_value, $key), $val, 'top');
					}
				}
			}
			flush_rewrite_rules();
		}
		return $new_value;
	}
}