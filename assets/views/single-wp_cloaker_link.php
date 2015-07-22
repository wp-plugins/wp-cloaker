<?php
$path = str_replace('assets'.DIRECTORY_SEPARATOR.'views','',plugin_dir_path( __FILE__ ));
require_once($path.'class-wp-cloaker-clicks.php');
$linkOptions = get_post_meta($post->ID);
$link = $linkOptions['wp_cloaker_link'][0];

/*//print out 3rd parthy traking code
if(get_option( 'wp_cloaker_link_tracking_code')){
	echo "<script>". esc_attr( get_option( 'wp_cloaker_link_tracking_code' ) )."</script>";
}*/
$cusOptions = $linkOptions['wp_cloaker_link_custom_options'][0];
if($cusOptions){
	$redirect = $linkOptions['wp_cloaker_link_redirection'][0];
}else{
	$redirect = esc_attr( get_option( 'wp_cloaker_link_redirection','301' ) );
}
//get visitor information from IP-API
//get link category id
$terms = wp_get_post_terms( $post->ID, 'wp_cloaker_link_category');
//add visitor information to db
$clicks = new WP_Cloaker_Clicks();
//register this click to db
$clicks->wp_cloaker_click_count( $post->ID,$terms[0]->term_id);
$clicks->getIPInfo($post->ID);
switch($redirect){
	case '301':
		header("Location: $link",true,301); 
		break;
	case '302':
		header("Location: $link",true,302); 
		break;
	case '303':
		header("Location: $link",true,303); 
		break;
	case '307':
		header("Location: $link",true,307); 
		break;
	case 'js':
		?><script>window.location.replace("<?php echo $link ?>");</script>'<?php ;
		break;
}
exit;
?>

