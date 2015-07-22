<?php
$path = str_replace('assets'.DIRECTORY_SEPARATOR.'views','',plugin_dir_path( __FILE__ ));
require_once($path.'class-wp-cloaker-clicks.php');
$clickObj = new WP_Cloaker_Clicks();
$count = $clickObj->getClicksCountBYID(get_the_ID());
if($count){
//
$current_page = (isset($_GET['click_details_page']))? $_GET['click_details_page'] : 1;
if(isset($_GET['click_details_page'])){
	
	$current_page = $_GET['click_details_page'];
	$page_start = ($_GET['click_details_page'] == 1)? 0 : $_GET['click_details_page'] * 10 - 9;
}else{
	$current_page = 1;
	$page_start = 0;
}

//get all clicks details
$clicks = $clickObj->getClicksDetailsByID(get_the_ID(),$page_start,10);

$total_pages =ceil( $clickObj->getClicksDetailsTotalBYID(get_the_ID()) / 10);
?>
<div class="clicks-details-container">
<span class="clicks-count"><strong>Clicks Total: </strong><?php echo $count;?> clicks</span>
<table class="clicks-details">
	<tr>
    	<th>No.</th>
        <th>Date/Time</th>
        <th>IP</th>
        <th>Country</th>
        <th>Region Code</th>
        <th>Latitude / Longitude</th>
        <th>timezone</th>
        <th>ISP</th>
    </tr>
<?php
$ii = (isset($_GET['click_details_page']))? $_GET['click_details_page'] : 0;
$i = ( $ii == 1 || $ii == 0 )? 0 : $page_start - 1;
foreach($clicks as $click){
	$i++;
	?>
    <tr>
    	<td><?php echo $i; ?></td>
        <td><?php echo $click->click_date; ?></td>
        <td><?php echo $click->click_ip; ?></td>
        <td><?php echo $click->click_country.'['.$click->click_country_code.']'; ?></td>
        <td><?php echo $click->click_region_code;?></td>
        <td><?php echo $click->click_latitude.','.$click->click_longitude; ?></td>
        <td><?php echo $click->click_timezone; ?></td>
        <td><?php echo $click->click_isp; ?></td>
    </tr>
    <?php
}
?>
<tr class="footer">
    <td colspan="2">
     <strong>Page:</strong> <?php echo $current_page;?> of <?php echo $total_pages;?>
    </td>
    <td colspan="9">
    	<div class="pages">
        	<?php
				for($i=1;$total_pages >= $i;$i++){
					if($current_page == $i){?>
                    <span><?php echo $i; ?></span>
					<?php }else{?>
					<a href="<?php echo $_SERVER["REQUEST_URI"] ."&click_details_page=$i"?>"><?php echo $i; ?></a>
				<?php }}
			?>
        </div>
    </td>
</tr>
</table></div>
<?php }else{
	echo __('There is no clicks yet.');
}?>