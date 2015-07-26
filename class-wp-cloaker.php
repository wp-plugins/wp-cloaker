<?php

require_once(plugin_dir_path(__FILE__).'Add_Taxonomy_To_Post_Permalinks.php');

class WP_Cloaker{
	public function initialize(){
		//register custom post
		add_action( 'init', array($this,'wp_cloaker_link_init'),0 );
		//register custom taxonomy
		add_action( 'init', array($this,'wp_cloaker_link_category_init'), 0 );
		//register link options box
		add_action( 'add_meta_boxes', array($this,'wp_cloaker_link_options_init'), 0 );
		// register link clicks details box
		add_action( 'add_meta_boxes', array($this,'wp_cloaker_link_clicks_details_init'), 0 );
		// save link options data
		add_action( 'save_post',array($this,'wp_cloaker_link_options_save'));
		// delete click data when post deleted
		add_action( 'before_delete_post', array($this,'wp_cloaker_delete_click_data') );
		//enqueue scripts and styles
		add_action('admin_enqueue_scripts',array($this,'enqueue_styles'));
		add_action('admin_enqueue_scripts',array($this,'enqueue_scripts'));
		//hook the single post template
		add_filter('single_template', array($this,'wp_cloaker_link_template'));
		
		add_option( 'wp_cloaker_version', '1.0.0');
		
	}
	//enqueue css file to the admin
	public function enqueue_styles(){
		$page = get_current_screen();
		if('wp_cloaker_link'!=$page->post_type){
			return;	
		}
		wp_enqueue_style('wp-cloaker',wp_cloaker_url.'/assets/css/wp-cloaker.css',array(),wp_cloaker_version);
	}
	//enqueue javascript file to the admin
	public function enqueue_scripts(){
		$page = get_current_screen();
		if('wp_cloaker_link'!=$page->post_type){
			return;	
		}
		$wp_cloaker_vars = array(
			'url'=> plugin_dir_url(__FILE__)
		);	
		
		wp_enqueue_script('zeroclipboard',wp_cloaker_url.'/assets/js/ZeroClipboard.min.js',array('jquery'),wp_cloaker_version);
		wp_enqueue_script('wp-cloaker',wp_cloaker_url.'/assets/js/wp-cloaker.js',array('jquery'),wp_cloaker_version);
		wp_enqueue_script('copyclipboard',wp_cloaker_url.'/assets/js/copy.js',array('jquery'),wp_cloaker_version);
		wp_localize_script( 'copyclipboard', 'wpCloaker', $wp_cloaker_vars );
	}
	// register custom post type
	public function wp_cloaker_link_init(){
		$labels = array(
			'name'                => _x( 'Links', 'Post Type General Name', 'wp_cloaker' ),
			'singular_name'       => _x( 'Link', 'Post Type Singular Name', 'wp_cloaker' ),
			'menu_name'           => __( 'WP Cloaker', 'wp_cloaker' ),
			'name_admin_bar'      => __( 'Post Type', 'wp_cloaker' ),
			'parent_item_colon'   => __( 'Parent Item:', 'wp_cloaker' ),
			'all_items'           => __( 'All Links', 'wp_cloaker' ),
			'add_new_item'        => __( 'Add New Link', 'wp_cloaker' ),
			'add_new'             => __( 'Add New', 'wp_cloaker' ),
			'new_item'            => __( 'New Link', 'wp_cloaker' ),
			'edit_item'           => __( 'Edit Link', 'wp_cloaker' ),
			'update_item'         => __( 'Update Link', 'wp_cloaker' ),
			'view_item'           => __( 'View Link', 'wp_cloaker' ),
			'search_items'        => __( 'Search Item', 'wp_cloaker' ),
			'not_found'           => __( 'Not found', 'wp_cloaker' ),
			'not_found_in_trash'  => __( 'Not found in Trash', 'wp_cloaker' ),
		);
		// rewrite permalink structure
		$custom_slug = get_option('wp_cloaker_link_prefix','visit');
		$rewrite = array(
			'slug'                => "$custom_slug/%link-cat%",
			'with_front'          => true,
			'pages'               => true,
			'feeds'               => false,
		);
		$args = array(
			'label'               => __( 'wp_cloaker_link', 'wp_cloaker' ),
			'description'         => __( 'Post Type Description', 'wp_cloaker' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'thumbnail', ),
			'taxonomies'          => array( 'wp_cloaker_link_category' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 20,
			'menu_icon' 		  => 'dashicons-admin-links',
			'show_in_admin_bar'   => false,
			'show_in_nav_menus'   => false,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'rewrite'             => $rewrite,
			'capability_type'     => 'post',
		);
		register_post_type( 'wp_cloaker_link', $args );
		add_theme_support('post-thumbnails');
	}
	// register taxonomy
	public function wp_cloaker_link_category_init() {
		$labels = array(
			'name'                       => _x( 'Link Categories', 'Taxonomy General Name', 'wp_cloaker' ),
			'singular_name'              => _x( 'Link Category', 'Taxonomy Singular Name', 'wp_cloaker' ),
			'menu_name'                  => __( 'Link Categories', 'wp_cloaker' ),
			'all_items'                  => __( 'All Categories', 'wp_cloaker' ),
			'parent_item'                => __( 'Parent Category', 'wp_cloaker' ),
			'parent_item_colon'          => __( 'Parent Category:', 'wp_cloaker' ),
			'new_item_name'              => __( 'New Category', 'wp_cloaker' ),
			'add_new_item'               => __( 'Add New Category', 'wp_cloaker' ),
			'edit_item'                  => __( 'Edit Category', 'wp_cloaker' ),
			'update_item'                => __( 'Update Category', 'wp_cloaker' ),
			'view_item'                  => __( 'View Link Category', 'wp_cloaker' ),
			'separate_items_with_commas' => __( 'Separate Link Categories with commas', 'wp_cloaker' ),
			'add_or_remove_items'        => __( 'Add or remove Link Categories', 'wp_cloaker' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'wp_cloaker' ),
			'popular_items'              => __( 'Popular Categories', 'wp_cloaker' ),
			'search_items'               => __( 'Search Items', 'wp_cloaker' ),
			'not_found'                  => __( 'Not Found', 'wp_cloaker' ),
		);
		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => true,
			'public'                     => true,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => true,
			'show_tagcloud'              => true,
		);
		register_taxonomy( 'wp_cloaker_link_category', array( 'wp_cloaker_link' ), $args );
		$taxonomy_permalinks = new Add_Taxonomy_To_Post_Permalinks( 'wp_cloaker_link_category',array('tagname'=>'link-cat') );
	}
	//register link options metabox
	public function wp_cloaker_link_options_init(){
		add_meta_box("wp_cloaker_link_options","Link Options",array($this,"wp_cloaker_link_options_display"),
		"wp_cloaker_link","normal","high");
		
	}
	//call link options view
	public function wp_cloaker_link_options_display(){
		require_once(plugin_dir_path(__FILE__).'assets/views/link-options.php');	
	}
	//save link options data
	public function wp_cloaker_link_options_save($post_id){
		if(! $this->user_can_save($post_id)){
			return;	
		}
		//get variables from the POST
		$linkURL = stripslashes(strip_tags($_POST['wp_cloaker_link']));
		if(strpos($linkURL,'http://') === false && strpos($linkURL,'https://') === false){
			$linkURL = 'http://'.$linkURL;
		}
		$cusOption = (isset($_POST['wp_cloaker_link_custom_options']))?$_POST['wp_cloaker_link_custom_options']:'0';
		$linkTarget = (isset($_POST['wp_cloaker_link_target']))?$_POST['wp_cloaker_link_target']:'_self';
		$linkRedirection = (isset($_POST['wp_cloaker_link_redirection']))?$_POST['wp_cloaker_link_redirection']:'301';
		$linkNoFollow = (isset($_POST['wp_cloaker_nofollow']))?$_POST['wp_cloaker_nofollow']:'0';
		
		//update post meta
		update_post_meta($post_id,'wp_cloaker_link',$linkURL);
		update_post_meta($post_id,'wp_cloaker_link_custom_options',$cusOption);
		update_post_meta($post_id,'wp_cloaker_link_target',$linkTarget);
		update_post_meta($post_id,'wp_cloaker_link_redirection',$linkRedirection);
		update_post_meta($post_id,'wp_cloaker_nofollow',$linkNoFollow);
	}

	//check if the user can save
	public function user_can_save($post_id){
		$is_valid_nonce =
		(isset($_POST['wp_cloaker_link_options_nonce'])) &&
		wp_verify_nonce($_POST['wp_cloaker_link_options_nonce'],
			'wp_cloaker_link_options_save');
		
		$is_autosave = wp_is_post_autosave($post_id);
		$is_revision = wp_is_post_revision($post_id);
		
		return !($is_revision || $is_autosave) && $is_valid_nonce;
	}
	
	function wp_cloaker_link_template(){
		 global $wp_query, $post;
		/* Checks for single template by post type */
		if ($post->post_type == "wp_cloaker_link"){
			if(file_exists(plugin_dir_path(__FILE__). 'assets/views/single-wp_cloaker_link.php'))
				return plugin_dir_path(__FILE__). 'assets/views/single-wp_cloaker_link.php';
		}
	}
	//register link clicks details metabox
	public function wp_cloaker_link_clicks_details_init(){
		add_meta_box("wp_cloaker_link_clicks_details","Clicks details",array($this,"wp_cloaker_link_clicks_details"),
		"wp_cloaker_link","normal","low");
		
	}
	
	public function wp_cloaker_link_clicks_details(){
		require_once(plugin_dir_path(__FILE__).'assets/views/clicks_details.php');
	}
	
	public function wp_cloaker_delete_click_data($postID){
		global $wpdb;
		$sql = "DELETE FROM {$wpdb->wp_cloaker_clicks_table}  WHERE link_id={$postID}";	
		$rows = $wpdb->query($sql);
		$sql = "DELETE FROM {$wpdb->wp_cloaker_clicks_count_table}  WHERE link_id={$postID}";	
		$rows = $wpdb->query($sql);
	}
	
}