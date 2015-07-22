<div class="link-options">
<label>Link URL: 
<input type="text" name="wp_cloaker_link" value="<?php echo get_post_meta(get_the_ID(),'wp_cloaker_link' ,true) ?>"/></label>
<div class="wp_cloaker_error">this field can not be empty, and it must be a valid URL</div>
<div class="clearfix"></div>
<label class="custom-options">
	<?php $cusOPtions =  get_post_meta(get_the_ID(),'wp_cloaker_link_custom_options' ,true) ?>
	<input type="checkbox" name="wp_cloaker_link_custom_options" id="enable-custom-link-options" value="1"
    <?php if($cusOPtions) echo 'checked="checked"'  ?>"/>
    Activate custom link options
</label><br>
<fieldset id="custom-options" disabled>
<legend>Link Custom Options:</legend>
<!--<label>Link target: 
<select name="wp_cloaker_link_target">
<?php $linkTarget =  get_post_meta(get_the_ID(),'wp_cloaker_link_target' ,true) ?>
    <option value="_self" <?php echo $linkTarget == "_self" ?  'selected="selected"':''; ?>>same window</option>
    <option value="_blank" <?php echo $linkTarget == "_blank" ?  'selected="selected"':''; ?>>new window</option>
</select></label>
<div class="clearfix"></div>-->
<label>Redirection Type:
<select name="wp_cloaker_link_redirection">
<?php $linkRedirection =  get_post_meta(get_the_ID(),'wp_cloaker_link_redirection' ,true) ?>
    <option value="301" <?php echo $linkRedirection == "301" ?  'selected="selected"':''; ?>>301 redirection</option>
    <option value="302" <?php echo $linkRedirection == "302" ?  'selected="selected"':''; ?>>302 redirection</option>
    <option value="303" <?php echo $linkRedirection == "303" ?  'selected="selected"':''; ?>>303 redirection</option>
    <option value="307" <?php echo $linkRedirection == "307" ?  'selected="selected"':''; ?>>307 redirection</option>
    <option value="js" <?php echo $linkRedirection == "js" ?  'selected="selected"':''; ?>>JavaScript redirection</option>
</select></label>
<!--<div class="clearfix"></div>
<?php $linkNoFollow =  get_post_meta(get_the_ID(),'wp_cloaker_nofollow' ,true) ?>
<label><input type="checkbox" name="wp_cloaker_nofollow" <?php if($linkNoFollow) echo 'checked="checked"'; ?>/> Add nofollow to the link</label>
<div class="clearfix"></div>-->
</div>
<?php
	// add hidden field to the form to validate that the contents of the form request came from the current site 
	wp_nonce_field('wp_cloaker_link_options_save','wp_cloaker_link_options_nonce');
?>
</fieldset>