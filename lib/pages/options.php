<?php
// create custom plugin settings menu
add_action( 'admin_menu', 'wsform_create_menu' );

// create the wsform_forms object
$wsform_forms = new stdClass();

$wsf = new WebbsitesForm();

// $wsform_forms->prefix 		= 'wsform-form';
// $wsform_forms->groupname 	= $wsform_forms->prefix . '-settings-group';
// $wsform_forms->page_title 	= 'Webbsites Forms Settings';
// $wsform_forms->menu_title 	= 'Settings';


function wsform_create_menu()
{
	// create submenu under Forms
	add_submenu_page( 
        'edit.php?post_type=wsform_form', 
        'Webbsites Forms Settings', 
        'Settings', 
        'administrator', 
        'forms-options', 
        'wsform_settings_page' 
    );

	//call register settings function
	add_action( 'admin_init', 'wsform_register_settings' );
}


function wsform_register_settings()
{
	global $wsf;

    // // register our settings
    // $opts = $wsf->opt_names;

    // foreach( $opts as $opt )
    // {
    //     register_setting( $wsf->settings_groupname, $opt[0] );
    // }

	//register our settings
	register_setting( $wsf->settings_groupname, '_wsform_opt_daemon_id' );
	register_setting( $wsf->settings_groupname, '_wsform_opt_default_sender' );
	register_setting( $wsf->settings_groupname, '_wsform_opt_default_recipient' );
	register_setting( $wsf->settings_groupname, '_wsform_opt_default_success_message' );
	register_setting( $wsf->settings_groupname, '_wsform_opt_default_error_message' );
	register_setting( $wsf->settings_groupname, '_wsform_opt_mx_email' );
	register_setting( $wsf->settings_groupname, '_wsform_opt_honeypot_id' );
	register_setting( $wsf->settings_groupname, '_wsform_opt_dominant_color' );
	register_setting( $wsf->settings_groupname, '_wsform_opt_text_color' );
	register_setting( $wsf->settings_groupname, '_wsform_opt_header_color' );
	register_setting( $wsf->settings_groupname, '_wsform_opt_background_color' );
	register_setting( $wsf->settings_groupname, '_wsform_opt_field_color' );
	register_setting( $wsf->settings_groupname, '_wsform_opt_field_border_color' );
	register_setting( $wsf->settings_groupname, '_wsform_opt_field_border_width' );
	register_setting( $wsf->settings_groupname, '_wsform_opt_field_border_radius' );
}



function wsform_settings_page()
{
	global $wsf;

    // $opt = $wsf->opt_vals;

    // $wsf = new WebbsitesForm();


	// // Get Initial Defaults
	// $opt_daemon_id = 			get_option( '_wsform_opt_daemon_id',                'wf' );
	// $opt_default_sender = 		get_option( '_wsform_opt_default_sender',           'no-reply@' . $_SERVER['SERVER_NAME'] );
	// $opt_default_recipient =    get_option( '_wsform_opt_default_recipient',        get_option( 'admin_email' ) );
	// $opt_default_success_msg =  get_option( '_wsform_opt_default_success_message',  'Your message was successfully sent!' );
	// $opt_default_error_msg = 	get_option( '_wsform_opt_default_error_message',    'Sorry, there was a problem.' );
	// $opt_mx_email = 			get_option( '_wsform_opt_mx_email',                 $opt_default_recipient );
	// $opt_honeypot_id = 			get_option( '_wsform_opt_honeypot_id',              'wf_url' );
	// $opt_dominant_color = 		get_option( '_wsform_opt_dominant_color',           'rgb(68, 68, 68)' );
	// $opt_text_color = 			get_option( '_wsform_opt_text_color',               'rgb(34, 34, 34)' );
	// $opt_header_color = 		get_option( '_wsform_opt_header_color',             'rgb(119, 119, 119)' );
	// $opt_background_color = 	get_option( '_wsform_opt_background_color',         'rgb(238, 238, 238)' );
	// $opt_field_color = 			get_option( '_wsform_opt_field_color',              'rgb(255, 255, 255)' );
	// $opt_field_border = 		get_option( '_wsform_opt_field_border',             'rgba(0, 0, 0, 0)' );
	// $opt_field_border_width =	get_option( '_wsform_opt_field_border_width',       '1px' );
    // $opt_field_border_radius =  get_option( '_wsform_opt_field_border_radius',      5 );

?>

<style type="text/css">

	.wf-input {width: 100%; max-width: 500px;}

</style>

<div class="wrap">
<h1>Webbsites Forms Settings</h1>

<form method="post" action="options.php">
    <?php settings_fields( $wsf->settings_groupname ) ?>
    <table class="form-table">

        <tr valign="top">
        <th scope="row">Forms User</th>
        <td><?php wsform_user_select( '_wsform_opt_daemon_id', $wsf->daemon_id ) ?><br />
				<em>User who will be author of all forms submissions.</em></td>
		<!-- Daemon ID: <?php echo $wsf->daemon_id ?> -->
        </tr>

        <tr valign="top">
        <th scope="row">Default Email Sender</th>
        <td><input class="wf-input" type="email" name="_wsform_opt_default_sender" value="<?php echo $wsf->default_sender ?>" /><br />
				<em>Email "from" address for email form messages.</em></td>
        </tr>

        <tr valign="top">
        <th scope="row">Default Email Recipient</th>
        <td><input class="wf-input" type="email" name="_wsform_opt_default_recipient" value="<?php echo $wsf->default_recipient ?>" /><br />
				<em>Default recipient for form submissions. Can be overridden in individual forms.</em></td>
        </tr>

        <tr valign="top">
        <th scope="row">Default Success Message</th>
        <td><textarea class="wf-input" name="_wsform_opt_default_success_message"><?php echo $wsf->default_success_msg ?></textarea><br />
				<em>Default message for successful submissions. Can be overridden in individual forms.</em></td>
        </tr>

        <tr valign="top">
        <th scope="row">Default Error Message</th>
        <td><textarea class="wf-input" name="_wsform_opt_default_error_message"><?php echo $wsf->default_error_msg ?></textarea><br />
				<em>Default message for unsuccessful submissions. Can be overridden in individual forms.</em></td>
        </tr>

        <tr valign="top">
        <th scope="row">Maintenance</th>
        <td><input class="wf-input" type="email" name="_wsform_opt_mx_email" value="<?php echo $wsf->mx_email ?>" /><br />
				<em>Maintenance email address. Enter address to get notifications for testing. Empty to stop notifications.</em></td>
        </tr>

        <tr valign="top">
        <th scope="row">Honeypot ID</th>
        <td><input class="wf-input" type="text" name="_wsform_opt_honeypot_id" value="<?php echo $wsf->honeypot_id ?>" /><br />
				<em>CSS ID for "honeypot" spam prevention feature.</em></td>
        </tr>

        <tr valign="top">
        <th scope="row">Controls Color</th>
        <td><input type="text" class="wsform-color-entry" name="_wsform_opt_dominant_color" value="<?php echo $wsf->dominant_color ?>">
				<em>Sets the color used by the form&rsquo;s controls.</td>
        </tr>

				<tr valign="top">
        <th scope="row">Text Color</th>
        <td><input type="text" class="wsform-color-entry" name="_wsform_opt_text_color" value="<?php echo $wsf->text_color ?>">
				<em>Sets the form&rsquo;s text color.</td>
        </tr>

				<tr valign="top">
        <th scope="row">Header Color</th>
        <td><input type="text" class="wsform-color-entry" name="_wsform_opt_header_color" value="<?php echo $wsf->header_color ?>">
				<em>Sets the color used by the form&rsquo;s headers.</td>
        </tr>

				<tr valign="top">
        <th scope="row">Background Color</th>
        <td><input type="text" class="wsform-color-entry" name="_wsform_opt_background_color" value="<?php echo $wsf->background_color ?>">
				<em>Sets the form&rsquo;s background color.</td>
        </tr>

				<tr valign="top">
        <th scope="row">Field Color</th>
        <td><input type="text" class="wsform-color-entry" name="_wsform_opt_field_color" value="<?php echo $wsf->field_color ?>">
				<em>Sets the background color for the form&rsquo;s fields.</td>
        </tr>

				<tr valign="top">
        <th scope="row">Field Borders</th>
        <td><input type="text" class="wsform-color-entry" name="_wsform_opt_field_border_color" value="<?php echo $wsf->field_border_color ?>">
				<em>Sets the border color for the form&rsquo;s fields.</td>
        </tr>

				<tr valign="top">
        <th scope="row">Field Border Width</th>
				<td><select class="wsform-select" name="_wsform_opt_field_border_width">
					<option value="none"<?php if( $wsf->field_border_width == 'none' ) echo ' selected' ?>>None</option>
					<option value="1px"<?php if( $wsf->field_border_width == '1px' ) echo ' selected' ?>>1px</option>
					<option value="2px"<?php if( $wsf->field_border_width == '2px' ) echo ' selected' ?>>2px</option>
					<option value="3px"<?php if( $wsf->field_border_width == '3px' ) echo ' selected' ?>>3px</option>
				</select>
				<p><em>Sets the border width for the form&rsquo;s fields.</em></p></td>
        </tr>

				<tr valign="top">
        <th scope="row">Field Border Radius</th>
        <td><input type="number" min="0" max="10" class="wsform-text-entry" name="_wsform_opt_field_border_radius" value="<?php echo $wsf->field_border_radius ?>"><br />
				<em>Sets the border radius for the form&rsquo;s fields (in pixels).</td>
        </tr>

    </table>

    <?php submit_button() ?>

</form>
</div>
<?php 

}



function wsform_user_select( $opt_id, $user_id )
{
	$users_data = get_users( array( 'orderby' => 'display_name' ) );

	foreach( $users_data as $usr )
	{
		$userid = $usr->data->ID;
		$username = $usr->data->display_name;
		$users[$userid] = $username;

		$user_emails[] = $usr->data->user_email;
	}



	?>
	<select name="<?php echo $opt_id ?>">
		<?php foreach( $users as $uid => $uname ) : ?>
		<option value="<?php echo $uid ?>"<?php if( $uid == $user_id ) echo ' selected' ?>><?php echo $uname ?></option>
		<?php endforeach;
		if( ! in_array( WSFORM_DEFAULT_FORMS_EMAIL, $user_emails ) ) : ?>
		<option value="wf">Create Forms Daemon</option>
		<?php endif; ?>
	</select>
	<?php
}
