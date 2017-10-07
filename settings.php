<?php
/**
 * New User Administration Screen.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
//require_once( dirname( __FILE__ ) . '/admin.php' );
$title = __('Settings');
//$parent_file = 'banner_upload.php';

/**
 * Filter whether to enable user auto-complete for non-super admins in Multisite.
 *
 * @since 3.4.0
 *
 * @param bool $enable Whether to enable auto-complete for non-super admins. Default false.
 */
if ( is_multisite() && current_user_can( 'promote_users' ) && ! wp_is_large_network( 'users' )
	&& ( is_super_admin() || apply_filters( 'autocomplete_users_for_site_admins', false ) )
) {
	wp_enqueue_script( 'user-suggest' );
}

//require_once( ABSPATH . 'wp-admin/admin-header.php' );
?>

<div class="wrap">
<h2 id="add-new-user"> Form Settings
</h2>
    <style type="text/css">
        
       
.success{
        color: #3c763d;
    background-color: #dff0d8;
    border-color: #d6e9c6;
    padding: 8px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 4px;
    display: block;
    margin: 20px 0px;
}
.error{
         color: #a94442; 
    background-color: #f2dede;
    border-color: #ebccd1;
    padding: 8px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 4px;
    display: block;
    margin: 20px 0px;
}
.alert-info {
    color: #31708f;
    background-color: #d9edf7;
    border-color: #bcdff1;
        padding: 15px;
    margin-bottom: 1rem;
    border: 1px solid transparent;
    border-radius: .25rem;
}
.shortcode_text{
    line-height: 25px !important;
}
    </style>
   
  
<?php 
if(!empty($_POST)){
    extract($_POST);
    
	update_option( 'admin_form_email', $admin_form_email);
	update_option('form_user_email', $useremailcontent);
    update_option('form_admin_email', $adminemailcontent);
	
	
    echo "<div class='notice notice-success is-dismissible'> 
	<p><strong>Settings saved.</strong></p>
</div>";
	
}


?>

<?php

if ( current_user_can( 'create_users') ) {
	echo '<h3 id="">' . __( 'Form Settings' ) . '</h3>';
?>
  <div class="alert-info">Shortcode : [alljobs]</div>

<form method="post" action="" novalidate="novalidate">

<table class="form-table">


<tr>
<th scope="row"><label for="admin_form_email"><?php _e('Email') ?></label></th>
<td><input name="admin_form_email" class="regular-text" value="<?php echo get_option('admin_form_email'); ?>"/></td>
</tr>
<tr>
<th scope="row"><label for="blogname"><?php _e('Select Template') ?></label></th>
<td><select class="regular-text change_template">
<option value="user">User Email Template</option>
<option value="admin">Admin Email Template</option>
</select></td>
</tr>
<tr class="user_template">
<th scope="row"><label for="blogname"><?php _e('Template') ?></label></th>
<td><?php 
$content = get_option('form_user_email');
$editor_id = 'useremailcontent';

wp_editor( $content, $editor_id ,array('media_buttons'=>true));
?>
</td>
</tr>
<tr style="display:none;" class="admin_template">
<th scope="row"><label for="blogname"><?php _e('Template') ?></label></th>
<td><?php 
$content = get_option('form_admin_email');
$editor_id = 'adminemailcontent';

wp_editor( $content, $editor_id ,array('media_buttons'=>true));
?>
</td>
</tr>
<tr>
    <th></th>
    <td class="shortcode_text">
        <strong>Note:</strong> Use shortcodes for Form Fields:<br>
        [given_name], [surname], [telephone], [email], [send_copy_to_myself]   
    </td>
</tr>
<tr>
<td>
<?php submit_button(); ?>
</td>
</tr>
</table>
</form>
<?php } // current_user_can('create_users') ?>
</div>
<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery(".change_template").click(function(){
			if(jQuery(this).val()== 'admin'){
				jQuery('.admin_template').show();
				jQuery('.user_template').hide();
			}else{
				jQuery('.user_template').show();
				jQuery('.admin_template').hide();
			}
		})
	})
</script>