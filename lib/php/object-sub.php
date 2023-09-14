<?php



class WebbsitesFormSub extends WebbsitesForm
{
	// Submissions
	public $sub_id;
	public $sub_title;
	public $parent_id;
	public $sub_data;
	public $sub_sanitized_data;
	public $sub_serialized_data;
	public $sub_json_data;
	public $sub_output;
	public $sub_wf_input;
	public $sub_wf_sender_name;
	public $sub_wf_sender_email;
	public $sub_wf_id;
	public $sub_wf_page_uri;
	public $sub_wf_user_ip;
	public $sub_wf_time;
	public $sub_wf_date;
    public $sub_wf_is_read;
	public $output_permalink;
    public $sub_wf_status;
    public $sub_post_info;


	// Constructor
	function __construct( $sub_id = null )
	{
        if( $sub_id != null )
            $this->webbsitesform_sub( $sub_id );
	}



	function webbsitesform_sub( $sub_id )
	{

		/*
		 *
		 *
		 *
		$sub_id = (int);
		 *
		 *
		 *
		*/

		// Populate several properties
		// $this->form_input_types = $this::form_input_types();
		$this->form_parameters  = $this::form_parameters();

		// Get the submission info
		$this->sub_id = $sub_id;
		$fields = get_post_meta( $this->sub_id );

		// Get the post title, save as form_title
		$this->sub_title = get_the_title( $sub_id );

        $date_format = get_option( 'date_format' );

        // Get the WP database info
        $this->sub_post_info = get_post( $sub_id );

		// Get the sub_meta info for the sub
		$this->post_id              = $fields['_wsform_sub_parent'][0];
		$this->sub_serialized_data	= trim( $fields['_wsform_sub_serialized_data'][0] );
		$this->sub_data             = unserialize( base64_decode( $this->sub_serialized_data ) );
		$this->sub_wf_input         = $this->sub_data->wf_input;
		$this->sub_wf_sender_name   = trim( $fields['_wsform_sub_wf_sender_name'][0] );
		$this->sub_wf_sender_email 	= trim( $fields['_wsform_sub_wf_sender_email'][0] );
		$this->sub_wf_id            = trim( $fields['_wsform_sub_form_id'][0] );
		$this->sub_wf_page_uri      = key_exists( '_wsform_sub_wf_page_uri', $fields ) ? trim( $fields['_wsform_sub_wf_page_uri'][0] ) : '';
		$this->sub_wf_user_ip       = trim( $fields['_wsform_sub_user_ip'][0] );
		$this->sub_wf_time          = get_post_timestamp( $sub_id );
		$this->sub_wf_date          = get_the_date( $date_format, $sub_id);
		$this->sub_wf_is_read       = key_exists( '_wsform_sub_is_read', $fields ) ? trim( $fields['_wsform_sub_is_read'][0] ) : 0;
        $this->sub_wf_status        = $this->sub_post_info->post_status;

		// Save the permalink
		$this->output_permalink();

		// Get the parent form info
		$this->get_form_data();

	}



    // function get_the_sub()
    // {
    //     $sub->form_sub_display();
    // }





	// META BOXES


    // (deprecated)
	function display_meta_box()
	{
		//print_array( $this );

		$this->output_sub_meta(); ?>

		<p style="text-align: center;">
			<a class="button button-primary button-large" href="<?php echo $this->output_permalink ?>" target="_blank">Print Entry</a>
		</p>

		<?php

	}






	function display_sub()
	{
		// check to see if an output slug has been selected
		if( $this->output_slug != '' )
		{
			$this->the_cust_form_sub();
		}
		else
		{
			$this->the_form_sub();
		}
	}





	function output_sub_meta()
	{
		// check to see if an output slug has been selected
		if( $this->output_slug != '' )
		{
			$this->the_cust_form_sub();
		}
		else
		{
			$this->the_form_sub();
		}
	}



	function the_form_sub()
	{
		echo $this->get_form_sub();
	}



	function the_cust_form_sub()
	{
		echo $this->get_cust_form_sub();
	}



	function get_cust_form_sub()
	{
		if( empty( $this->output_slug ) ) return false;

		// Assemble the path
		$path = WSFORM_PATH_TO_CUSTOM_OUTPUT . '/' . $this->output_slug;

		// If the file's not there, stop
		if( ! file_exists( $path ) ) return false;

		ob_start();

		// If it's there, output the HTML
		require_once( $path );

		return ob_get_clean();
	}




    function form_sub_display()
    {
        ob_start();
        ?>
        <div class="wsform-message-item" id="wsform-messagebody-sub-<?php echo $this->sub_id ?>" rel="<?php echo $this->sub_id ?>">
            
            <?php $this->form_sub_header() ?>
            <?php $this->form_sub_body() ?>

        </div>
        <?php

        $this->output( ob_get_clean() );
    }





    function generate_pdf()
    {
        ?>

        <!DOCTYPE html>
        <html class="wp-toolbar"
            lang="en-US">
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
            <title>Test PDF Viewer</title>
            <style type="text/css">
                body {font-family:sans-serif;font-size:1rem}
                h2 {margin:0 0 .5rem;font-size:1.5rem;padding:0;font-weight:normal}
                p {margin:0 0 1rem;font-size:.9rem;padding:0;line-height:1.5}
            </style>
        
        </head>
        <body>
        
        <?php 
        
        $this->form_sub_header();
        $this->form_sub_body();
        
        ?>

        <p style="font-size:.6rem;margin:2rem 0 0;padding:0">Copyright &copy; 2023, Webbsites &mdash; <a href="https://webbsites.net/">https://webbsites.net/</a></p>
        
        </body>
        </html>
        
        
        <?php 
    }










    function form_sub_header()
    {
        // $sd = $this->sub_data;
        $ftime = WebbsitesForm::fancy_date( $this->sub_wf_time );
        ?>

                                    <div class="wsform-message-header">
                                        <h2><?php echo $this->post_title ?></h2>
                                        <p><strong>From:</strong> <?php echo $this->sub_wf_sender_name ?> &lt;<a href="mailto:<?php echo $this->sub_wf_sender_email ?>"><?php echo $this->sub_wf_sender_email ?></a>&gt;<br />
                                        <strong>Sent:</strong> <?php echo $ftime ?></p>
                                        <div class="wsform-message-controls">
                                            <?php if( $this->sub_wf_status == 'trash' ) : ?>
                                            <span class="wsform-msg-ctl recover-message ico-lifebuoy" title="Recover"></span>
                                            <span class="wsform-msg-ctl delete-message ico-cancel" title="Delete Permanently"></span>
                                            <?php else : ?>
                                            <a href="/wp/wp-content/plugins/webbsites-forms/show-pdf.php?id=<?php echo $this->sub_id ?>">
                                                <span class="wsform-msg-ctl download-message ico-download" title="Download"></span>
                                            </a>
                                            <span class="wsform-msg-ctl trash-message ico-trash" title="Trash"></span>
                                            <?php endif ?>
                                        </div>
                                        <?php if( $this->sub_wf_status == 'trash' ) : ?>
                                        <span class="wsform-trashed-item">Item In Trash</span>
                                        <?php endif ?>
                                    </div>
    <?php
        
        // print_array( $this );
    }





    function form_sub_body()
    {
        ?>
                                    <div class="wsform-message-body">

                                        <?php echo $this->form_sub_output() ?>

                                    </div>

    <?php
    }








	function form_sub_output()
	{
		global $nl;

		// save the message body
		$sender_name   = trim( strip_tags( stripslashes( $this->sub_wf_sender_name ) ) );
		$sender_email  = trim( strip_tags( stripslashes( $this->sub_wf_sender_email ) ) );
		$form_name = $this->post_title;
		$time = $this->sub_wf_date;
		$user_ip = $this->sub_wf_user_ip;
		$page_uri = $this->sub_wf_page_uri;

		$table_tag = '<table style="border-bottom: 1px solid #aaa; margin: .5em 0 1em; font: normal 10pt/1.2 arial, helvetica, sans-serif; border-collapse: collapse; width: 100%; max-width: 8in">';

		// get info from post data
		$input = (array) $this->sub_wf_input;

		$msg_body = "<h3 style=\"margin: 1rem 0 0\">Message Info</h3>$nl";
		$msg_body .= $table_tag;

		// check to see if it's a custom form
		if( $this->output_slug != '' )
		{
			$path_to_link = $this->output_permalink;

			$msg_body .= "<tr><td style=\"width: 35%; background: #eee; border-bottom: 1px solid #aaa; border-top: 1px solid #aaa; padding: 10px; vertical-align: top\"><strong>Link To Form:</strong></td>$nl" .
						 "<td style=\"border-top: 1px solid #aaa; padding: 10px; vertical-align: top\">" .
						 "<a href=\"$path_to_link\" target=\"_blank\">$path_to_link</a>$nl<p><em>If link above is not clickable, copy and paste into browser.</em></p></td></tr>$nl$nl";
		}
		else
		{
			// get each of the input fields and save as html
			foreach( $input as $key => $row )
			{

				$label = ! empty( $this->form_keys[$key] ) ? $this->form_keys[$key]['wf_label'] : $key;

				// For file attachments
				if( is_array( $row ) && key_exists( 'files', $row ) )
				{
					$files = $row['files'];
					$row = '<ul class="wf-admin-file-list">';
					foreach( $files as $file )
					{
						$path = WSFORM_UPLOADS_DIR . $file;
						$row .= '<li><a href="' . $path . '" target="_blank">' . $file . '</a></li>';
						// $row .= '<li><a href="' . $path . '" download="' . $file . '">' . $file . '</a></li>';
					}
					$row .= '</ul>';
				}
				// for multiple selects
				elseif( is_array( $row ) )
				{
					$row = implode( ', ', $row );
				}
				else
				{
					$row = trim( strip_tags( stripslashes( $row ) ) );
				}

				// assemble all inputs
				$msg_body .= "<tr><td style=\"width: 35%; background: #eee; border-top: 1px solid #aaa; padding: 10px; vertical-align: top\"><strong>$label:</strong></td>$nl" .
							 "<td style=\"border-top: 1px solid #aaa; padding: 10px; vertical-align: top; white-space: pre-line\">$row</td></tr>$nl$nl";
			}

		}

		$msg_body .= "</table>$nl$nl";

		$msg_body .= "<h3 style=\"margin: 1rem 0 0\">Meta Info</h3>$nl";
		$msg_body .= $table_tag;
		$msg_body .= "<tr><td style=\"width: 35%; background: #eee; border-top: 1px solid #aaa; border-bottom: 1px solid #aaa; padding: 10px; vertical-align: top\"><strong>Form Instance URI: </strong></td>" .
					 "<td style=\"border-top: 1px solid #aaa; border-bottom: 1px solid #aaa; padding: 10px; vertical-align: top\"><a href=\"$page_uri\" target=\"_blank\">$page_uri</a></td></tr>$nl";
		$msg_body .= "<tr><td style=\"width: 35%; background: #eee; border-top: 1px solid #aaa; border-bottom: 1px solid #aaa; padding: 10px; vertical-align: top\"><strong>Sender IP address: </strong></td>" .
					 "<td style=\"border-top: 1px solid #aaa; border-bottom: 1px solid #aaa; padding: 10px; vertical-align: top\">$user_ip</td></tr>$nl$nl";
		$msg_body .= '</table>';

		return $msg_body;

	}





    function trash_sub()
    {
        // Delete the post altogether
        $res = wp_trash_post( $this->sub_id );

        // If it works, return the output; if not, send an error
        if( $res !== false )
        {
            ob_start();
            $this->form_responses_list( 'publish' );
            $this->output( ob_get_clean() );
        }
        else
        {
            $this->send_error( 'There was a database error' );
        }
    }




    function recover_sub()
    {
        // Move the post from trash to published
        $res = wp_publish_post( $this->sub_id );

        // If it works, return the output; if not, send an error
        if( $res !== false )
        {
            ob_start();
            $this->form_responses_list( 'trash' );
            $this->output( ob_get_clean() );
        }
        else
        {
            $this->send_error( 'There was a database error' );
        }
    }





    public function delete_sub()
    {
        // Delete the post altogether
        $res = wp_delete_post( $this->sub_id );

        // If it works, return the output; if not, send an error
        if( $res !== false )
        {
            $this->output( $res );
        }
        else
        {
            $this->send_error( 'There was a database error' );
        }
    }







    // DEPRECATED
	function get_form_sub()
	{
		global $nl;

		// print_array( $this );

		// save the message body
		$sender_name   = trim( strip_tags( stripslashes( $this->sub_wf_sender_name ) ) );
		$sender_email  = trim( strip_tags( stripslashes( $this->sub_wf_sender_email ) ) );
		$form_name = $this->post_title;
		$time = $this->sub_wf_date;
		$user_ip = $this->sub_wf_user_ip;
		$page_uri = $this->sub_wf_page_uri;

		$table_tag = '<table style="border-bottom: 1px solid #aaa; margin: .5em 0 1em; font: normal 10pt/1.2 arial, helvetica, sans-serif; border-collapse: collapse; width: 100%; max-width: 8in">';

		// get info from post data
		$input = (array) $this->sub_wf_input;

		$msg_body  = "<h3 style=\"margin: 1em 0 0\">Sender Information</h3>$nl";
		$msg_body .= $table_tag;
		$msg_body .= "<tr><td style=\"width: 35%; background: #eee; border-top: 1px solid #aaa; padding: 10px; vertical-align: top\"><strong>Sender Name: </strong></td>" .
					 "<td style=\"border-top: 1px solid #aaa; padding: 10px; vertical-align: top\">$sender_name</td></tr>$nl";
		$msg_body .= "<tr><td style=\"width: 35%; background: #eee; border-top: 1px solid #aaa; padding: 10px; vertical-align: top\"><strong>Sender Email: </strong></td>" .
					 "<td style=\"border-top: 1px solid #aaa; padding: 10px; vertical-align: top\"><a href=\"mailto:$sender_email\">$sender_email</a></td></tr>$nl";
		$msg_body .= "</table>$nl$nl";

		$msg_body .= "<h3 style=\"margin: 1em 0 0\">Form Responses</h3>$nl";
		$msg_body .= $table_tag;

		// check to see if it's a custom form
		if( $this->output_slug != '' )
		{
			$path_to_link = $this->output_permalink;

			$msg_body .= "<tr><td style=\"width: 35%; background: #eee; border-bottom: 1px solid #aaa; border-top: 1px solid #aaa; padding: 10px; vertical-align: top\"><strong>Link To Form:</strong></td>$nl" .
						 "<td style=\"border-top: 1px solid #aaa; padding: 10px; vertical-align: top\">" .
						 "<a href=\"$path_to_link\" target=\"_blank\">$path_to_link</a>$nl<p><em>If link above is not clickable, copy and paste into browser.</em></p></td></tr>$nl$nl";
		}
		else
		{
			// get each of the input fields and save as html
			foreach( $input as $key => $row )
			{

				$label = ! empty( $this->form_keys[$key] ) ? $this->form_keys[$key]['wf_label'] : $key;

				// For file attachments
				if( is_array( $row ) && key_exists( 'files', $row ) )
				{
					$files = $row['files'];
					$row = '<ul class="wf-admin-file-list">';
					foreach( $files as $file )
					{
						$path = WSFORM_UPLOADS_DIR . '/' . $file;
						$row .= '<li><a href="' . $path . '" target="_blank">' . $file . '</a></li>';
					}
					$row .= '</ul>';
				}
				// for multiple selects
				elseif( is_array( $row ) )
				{
					$row = implode( ', ', $row );
				}
				else
				{
					$row = trim( strip_tags( stripslashes( $row ) ) );
				}

				// assemble all inputs
				$msg_body .= "<tr><td style=\"width: 35%; background: #eee; border-top: 1px solid #aaa; padding: 10px; vertical-align: top\"><strong>$label:</strong></td>$nl" .
							 "<td style=\"border-top: 1px solid #aaa; padding: 10px; vertical-align: top; white-space: pre-line\">$row</td></tr>$nl$nl";
			}

		}

		$msg_body .= "</table>$nl$nl";

		$msg_body .= "<h3 style=\"margin: 1em 0 0\">Meta Info</h3>$nl";
		$msg_body .= $table_tag;
		$msg_body .= "<tr><td style=\"width: 35%; background: #eee; border-top: 1px solid #aaa; padding: 10px; vertical-align: top\"><strong>Form Name: </strong></td>" .
					 "<td style=\"border-top: 1px solid #aaa; padding: 10px; vertical-align: top\">$form_name</td></tr>$nl";
		$msg_body .= "<tr><td style=\"width: 35%; background: #eee; border-top: 1px solid #aaa; border-bottom: 1px solid #aaa; padding: 10px; vertical-align: top\"><strong>Form Instance URI: </strong></td>" .
					 "<td style=\"border-top: 1px solid #aaa; border-bottom: 1px solid #aaa; padding: 10px; vertical-align: top\">$page_uri</td></tr>$nl";
		$msg_body .= "<tr><td style=\"width: 35%; background: #eee; border-top: 1px solid #aaa; padding: 10px; vertical-align: top\"><strong>Local Time Received: </strong></td>" .
					 "<td style=\"border-top: 1px solid #aaa; padding: 10px; vertical-align: top\">$time</td></tr>$nl";
		$msg_body .= "<tr><td style=\"width: 35%; background: #eee; border-top: 1px solid #aaa; border-bottom: 1px solid #aaa; padding: 10px; vertical-align: top\"><strong>Sender IP address: </strong></td>" .
					 "<td style=\"border-top: 1px solid #aaa; border-bottom: 1px solid #aaa; padding: 10px; vertical-align: top\">$user_ip</td></tr>$nl$nl";
		$msg_body .= '</table>';

		$msg_body .= '<div class="wf_credits" style="font-size:8pt">Copyright &copy; ' . date('Y') . ', Webbsites. All rights reserved.<p></p></div>';

		return $msg_body;

	}







	function output_permalink()
	{
		$this->output_permalink = WSFORM_URL_TO_FORM_DISPLAY . '?id=' . $this->sub_id;
	}





















	// PUBLIC FORM SUBMISSIONS
	public function form_submit( $post )
	{
		// Populate several properties
		// $this->set_form_constants();
		// $this->form_input_types = $this::form_input_types();
		$this->form_parameters  = $this::form_parameters();

        // If no data, throw an error
        if( ! array_key_exists( 'd', $post ) OR empty( $post['d'] ) )
            $this->send_error( 'No data sent' );

        // Convert sub data into understandable format
        foreach( $post['d'] as $val )
        {
            $n = $val['name'];
            $v = $val['value'];
            $p = preg_match( '/wf_input\[([A-Za-z0-9_]+)\]/', $n, $m );

            if( $p == 1 )
            {
                $s = $m[1];
                $sub['wf_input'][$s] = $v;
            }
            else
            {
                $sub[$n] = $v;
            }
        }

		// Convert form submission to sub_data object
		$this->sub_data = (object) $sub;

		// Get the form ID info
		$this->form_id = $this->sub_data->wf_id;

		// Get the form ID info
		$this->sub_wf_page_uri = $this->sub_data->wf_page_uri;

		// Get the form data
		$this->get_post_id_from_form_id();
		$this->get_form_data();

		// Prepare the submitted data for storage
		$this->prepare_submitted_data();

		// print_array( $this );
		// return;

		// Check that nothing is entered in honeypot field. Prevents spam.
		// if it's empty, save the file
		if( empty( $this->sub_data->{$this->honeypot_id} ) )
		{
			// insert into WP database
			$args = array(
				'post_author'   => $this->daemon_id,
				'post_title' 	=> $this->sub_title,
				'post_type' 	=> 'wsform_sub',
				'post_status'   => 'publish',
                'post_parent'   => $this->post_id,
				'meta_input' 	=> array(
									'_wsform_sub_wf_sender_email'   => $this->sub_wf_sender_email,
									'_wsform_sub_wf_sender_name'    => $this->sub_wf_sender_name,
									'_wsform_sub_serialized_data'   => $this->sub_serialized_data,
									'_wsform_sub_user_ip'           => $this->sub_wf_user_ip,
									'_wsform_sub_time'              => $this->sub_wf_time,
									'_wsform_sub_date'              => $this->sub_wf_date,
									'_wsform_sub_form_id'           => $this->sub_wf_id,
									'_wsform_sub_wf_page_uri'       => $this->sub_wf_page_uri,
									'_wsform_sub_parent'            => $this->post_id,
									'_wsform_sub_is_read'           => 0,
					)
			);

			// save the form return
			$this->sub_id = wp_insert_post( $args );

			// Save the permalink
			$this->output_permalink();

			// If there are attachments, save them
			$this->process_attachments();

			// Send a notification and a reply if the settings allow
			$this->send_mail_receipts();
		}
		else
		{
			$this->send_bot_alert();
		}

		if( $this->status == 'problem' )
		{
			$this->send_error( 'Sorry, there was an error processing the form' );
		}
		else
		{
			$this->output();
		}
	}




    function files_upload( $post, $files )
    {
        // print_array( $post );
        // print_array( $files );
        // return;
        
        $bad_file_types = array( 'text/php', 'text/javascript', 'text/x-perl-script' );
        
        $field = $post['wf_field_name'];
        $uploads = $files[$field]['name'];
        
        $result = array();
        
        for( $c = 0 ; $c < count( $uploads ) ; $c++ )
        {
            // Get the file info
            $name =     $files[$field]['name'][$c];
            $type =     $files[$field]['type'][$c];
            $tmp_name = $files[$field]['tmp_name'][$c];
            $error =    $files[$field]['error'][$c];
            $size =     $files[$field]['size'][$c];
            $suffix =   uniqid();
            
            if( $type == null ) $type = wsform_file_ext( $name );
            
            $clean_name = strtolower( preg_replace( '/[^\w\d\.]+/', '-', $name ) );
            
            $result[$c]['orig_filename'] = $name;
            $result[$c]['clean_name'] = $clean_name;
            
            // Test to see if it's a disallowed file type
            if( in_array( $type, $bad_file_types ) )
            {
                $result[$c]['error'] = 1;
                $result[$c]['err_msg'] = 'Sorry, ' . $type . ' is not an allowed filetype';
            }
            elseif( strrpos( $name , '.' ) == 0 )
            {
                $result[$c]['error'] = 1;
                $result[$c]['err_msg'] = 'Sorry, files without extensions are not allowed';
            }
            elseif( $error != 0 )
            {
                $result[$c]['error'] = 1;
                $result[$c]['err_msg'] = 'Sorry, there was a system error';
            }
            else
            {
                $filename  = wsform_suffix( $clean_name, $suffix );
                $file_path = WSFORM_UPLOADS_DIR_PATH . '/' . $filename;
                $file_url  = WSFORM_UPLOADS_DIR . '/' . $filename;
                
                // Attempt to save the file
                if( move_uploaded_file( $tmp_name, $file_path ) )
                {
                    $result[$c]['error'] = 0;
                    $result[$c]['file_url'] = $file_url;
                    $result[$c]['file_type'] = $file_type;
                    $result[$c]['filename'] = $filename;
                }
                else
                {
                    $result[$c]['error'] = 1;
                    $result[$c]['err_msg'] = 'Sorry, there was a problem saving the file';
                }
            }
        }
        
        // Output the result array
        echo json_encode( $result );
    }
    




	private function send_bot_alert()
	{
		// Send error email if honeypot triggered

		global $nl;

		// Get the admin_email from site options
		$admin_email = get_option( 'admin_email' );

		// Get website name
		$site_name = get_bloginfo( 'name' );

		// Get the name of the site from site options
		$subject = '[MX] -- Honeypot triggered in form at ' . $site_name;

		// Get the raw submission data and save as string
		$output_value  = "<p>A bot apparently tried to use your form at <strong>$site_name</strong> and triggered the honeypot trap.</p>$nl";
		$output_value .= "<p>Here are the details:</p>$nl$nl";
		$output_value .= "<p>- Date/time sent: " . $this->sub_wf_date . "<br />$nl";
		$output_value .= "- Sender's IP address: " . $this->sub_wf_user_ip . "<br />$nl";
		$output_value .= "- Page of form instance: " . $this->sub_wf_page_uri . "<br />$nl";
		$output_value .= "- Honeypot ID: " . $this->honeypot_id . "<br />$nl&nbsp;<br />$nl";
		$output_value .= "Form data:<br>$nl";
		$output_value .= '<pre>' . print_r( $this->sub_data, 1 ) . '</pre>' . "</p>$nl$nl";

		//$mail_args = array( $admin_email, $subject, $output_value, 'From: ' . $admin_email );

		// Send the email
		//mail( $mail_args );


		$mail_args = array(
			'from_email' => $admin_email,
			'to_email' => $admin_email,
			'subject' => $subject,
			'message' => $output_value
		);

		$this->send_nice_email( $mail_args );
	}





	function prepare_submitted_data()
	{
		// Save the parent form_id
		$this->sub_wf_id = $this->form_id;

		// Save the parent post_id
		$this->parent_id = $this->post_id;

		// Save lots of properties for the sub
		$format = 'g:i a, l, F j, Y';

		$this->sub_wf_user_ip = $_SERVER['REMOTE_ADDR'];	// from where the message was sent
		$this->sub_wf_time    = time();	// timestamp

		$dt = new DateTime();
		$dt->setTimestamp( $this->sub_wf_time );
		$dt->setTimezone( wp_timezone() );

		$this->sub_wf_date		= $dt->format( $format );

		// Attempt to get the name and email from the form
		$this->sub_wf_sender_name  = ! empty( $this->sub_data->wf_sender_name )  ? trim( $this->sub_data->wf_sender_name )  : 'No name';
		$this->sub_wf_sender_email = ! empty( $this->sub_data->wf_sender_email ) ? trim( $this->sub_data->wf_sender_email ) : 'No email';

		if( $this->sub_wf_sender_name != 'No name' && $this->sub_wf_sender_email != 'No email' )
			$this->sub_title = $this->sub_wf_sender_name . ' (' . $this->sub_wf_sender_email . ')';

		else
			$this->sub_title = 'Form Submission: ' . date( 'Y-m-d-H-i-s' );


		// Save all of the form output into an object
		$post = new stdClass();

		// Loop through each input and sanitize the data
		foreach( $this->sub_data as $key => $val )
		{
			    if( $key == 'wf_sender_name' ) 	$post->wf_sender_name 	= sanitize_text_field( $val );
			elseif( $key == 'wf_sender_email' ) $post->wf_sender_email 	= sanitize_email( $val );
			elseif( $key == 'wf_id' ) 			$post->wf_id 			= intval( $val );
			elseif( $key == 'action' ) 			$post->action 			= sanitize_text_field( $val );
			elseif( $key == 'func' ) 			$post->func 			= sanitize_text_field( $val );

			elseif( $key == 'wf_input' )
			{
                $post->wf_input = new stdClass();

				foreach( $val as $k => $v )
				{
					if( is_array( $v ) )
					{
						foreach( $v as $ke => $va )
						{
							if( $ke == 'files' )
							{
								foreach( $va as $fi )
								{
									$post->wf_input->{$k}[$ke][] = sanitize_text_field( $fi );
								}
							}
							else
							{
								$post->wf_input->{$k}[$ke] = sanitize_text_field( $va );
							}
						}
					}
					else
					{
						$form_type = $this->form_keys[$k]['wf_type'];

							if( $form_type == 'email' ) 	$post->wf_input->{$k} = sanitize_email( $v );
						elseif( $form_type == 'textarea' ) 	$post->wf_input->{$k} = sanitize_textarea_field( $v );
						else 								$post->wf_input->{$k} = sanitize_text_field( $v );
					}
				}
			}

		}

		// Save sub_sanitized_data
		$this->sub_sanitized_data = $post;

		// Save wf_input for email output
		$this->sub_wf_input = $this->sub_sanitized_data->wf_input;

		// Encode output as sub_json_data
		$this->sub_json_data = json_encode( $this->sub_sanitized_data );

		// Next, let's serialize the form input and save as sub_serialized_data.
		// This is what will be saved in the database and will be used
		$this->sub_serialized_data = base64_encode( serialize( $post ) );
	}







	private function send_mail_receipts()
	{
        // For offline development
        // return true;
        // bw

		global $nl;

		$sub_id = $this->sub_id;
		$post = $this->sub_sanitized_data;

		// get the site name
		$site_name = get_bloginfo( 'name' );

		// INITIAL VALUES
		$host_domain = $this->server_name; 		// name of host server (i.e. domain.com)

		// Get the form values
		$fields = $this->sub_wf_input;




		// BEGIN SUBMISSION RECEIPTS
		// First, check to see if the form is set to send receipts to the owner
		if( $this->form_send_receipt == 'checked' )
		{
			$send_sub_receipts = true;

			// If it's true, check to see if one or more emails have been specified
			if( ! empty( $this->form_recipient ) )
			{
				$notification_emails = explode( ',', $this->form_recipient );
			}
			elseif( ! empty( $this->default_recipient ) )
			{
				$notification_emails[] = $this->default_recipient;
			}
			else
			{
				$send_sub_receipts = false;
			}
		}
		else
		{
			$send_sub_receipts = false;
		}


		// If all is well, send the submission receipt
		if( $send_sub_receipts == true )
		{
			// SEND SUBMISSION RECEIPTS
			// Begin the receipt email array
			$dest_email = array();
			foreach( $notification_emails as $val ) $dest_email[] = trim( $val );

			// get some form info
			$sender_name	= trim( strip_tags( stripslashes( $this->sub_wf_sender_name ) ) );
			$sender_email	= trim( strip_tags( stripslashes( $this->sub_wf_sender_email ) ) );

			// assemble from entries
			//$from_name = $sender_name . ' via form at ' . $this->server_name;
			//$from_email = ! empty( $this->form_sender ) ? $this->form_sender : $this->default_sender;

			// get the message body
			$this->sub_output = $this->get_form_sub();

			// Begin the message
			$receipt_msg  = '<p>You have received a message from your website form <strong>' . $this->post_title . '</strong> on your website <strong>' . $site_name . ' (' . $host_domain . ')</strong>. Here are the details:</p>';
			$receipt_msg .= "$nl$nl";
			$receipt_msg .= $this->sub_output;




			// configure the submission email, save in $email_args array
			foreach( $dest_email as $to_email )
			{
				$email_args[] = array(
					'from_email' 		=> $this->default_sender,
					'from_name' 		=> $sender_name . ' via form at ' . $this->server_name,
					'to_email' 			=> $to_email,
					'to_name' 			=> $sender_name,
					'subject' 			=> '[' . $this->server_name . '] Submission to ' . $this->post_title,
					// 'subject' 			=> 'Submission to ' . $this->post_title . ' at ' . $this->server_name,
					'reply_to_name'		=> $sender_name,
					'reply_to_email' 	=> $sender_email,
					'message' 			=> $receipt_msg
				);
			}
		}
		// END SUBMISSION RECEIPTS





		// BEGIN SUBMISSION AUTOMATED REPLIES
		//
		// First, check to see if the form is set to send automated replies to the user
		if( $this->form_send_reply == 'checked' )
		{
			$send_sub_replies = true;

			// If it's true, get the sender's email address
			$sender_name = $this->sub_wf_sender_name;
			$sender_email = $this->sub_wf_sender_email;
			$return_address = "$sender_name <$sender_email>";

			// If a custom mail sender has been set, use it; otherwise, use the default sender
			if( ! empty( $this->form_sender ) ) $reply_sender = $this->form_sender;
			else $reply_sender = $this->default_sender;

			if( empty( $reply_sender ) ) $send_sub_replies = false;
		}
		else
		{
			$send_sub_replies = false;
		}


		// If all is well, send the receipt
		if( $send_sub_replies == true )
		{
			// CONFIGURE RETURN EMAIL

			// assemble message subject
			$reply_subject = 'Your submission to ' . $this->post_title . " on $site_name"; // subject line of message to recipient

			$reply_from_name = ! empty( $this->form_sender_name ) ? $this->form_sender_name : 'Webbsites at ' . $site_name;

			// Begin the message
			$reply_msg  = "<p>Thank you for your submission to <strong>" . $this->post_title . "</strong> on the <strong>$site_name</strong> website. Here is what you sent us:</p>$nl$nl";
			$reply_msg .= $this->sub_output;

			// return email arguments, save in $email_args array
			$email_args[] = array(
				'from_email' 		=> $reply_sender,
				'to_email'   		=> $sender_email,
				'subject'    		=> $reply_subject,
				'message'    		=> $reply_msg,
				'reply_to_name'		=> $sender_name,
				'reply_to_email' 	=> $sender_email,
				'from_name'  		=> $reply_from_name,
				'to_name'			=> $reply_to_name,
				'extra_headers'		=> ''
			);
		}

		// Send out each email
		foreach( $email_args as $args ) $this->send_nice_email( $args );




		// BEGIN MX EMAIL
		//
		// Finally, if an address has been provided for sending a maintenance email, send it there with default sender email
		if( ! empty( $this->mx_email ) )
		{
			$send_mx_email = true;
			$mx_email = $this->mx_email;
			$mx_email_sender = $this->default_sender;
		}
		else
		{
			$send_mx_email = false;
		}


		// CONFIGURE MX EMAIL
		if( $send_mx_email == true )
		{
			// Get all the variables and add them on to the end of the message
			$vars = get_defined_vars();
			unset( $vars['email_args'] );
			$vars['forms_obj'] = $this;
			//$cons = get_defined_constants();

			$mx_email_subject = '[MX][' . $this->server_name . '] Submission to ' . $this->post_title;
			$mx_sender_name = 'Webbsites at ' . $this->server_name;
			$mx_email_msg  = $receipt_msg;
			$mx_email_msg .= '<pre>';
			$mx_email_msg .= print_r( $vars, 1 );
			$mx_email_msg .= print_r( $cons, 1 );
			$mx_email_msg .= '</pre>';

			// MX email arguments
			$mx_args = array(
				'from_email' 	=> $this->default_sender,
				'to_email' 		=> $mx_email,
				'subject' 		=> $mx_email_subject,
				'message' 		=> $mx_email_msg,
				'from_name' 	=> $mx_sender_name,
				'extra_headers' => ''
			);

			// Send the MX email
			$this->send_nice_email( $mx_args );
		}

		return true;
	}





	private function send_nice_email( $args )
	{
		global $nl;

		//$args = array(
		//	'from_email' => $from_email (REQUIRED),
		//	'to_email' => $to_email (REQUIRED),
		//	'subject' => $subject (REQUIRED),
		//	'message' => $message (REQUIRED),
		//	'reply_to_name' => $sender_name,
		//	'reply_to_email' => $sender_email,
		//	'from_name' => $from_name,
		//	'to_name' => $to_name,
		//	'extra_headers' => $extra_headers
		//);

		// Extract the variables
		//
		// Required Vars (return false if empty)
		if( ! empty( $args['from_email'] ) ) $from_email = $args['from_email'];
		else return false;

		if( ! empty( $args['to_email'] ) ) $to_email = $args['to_email'];
		else return false;

		if( ! empty( $args['subject'] ) ) $subject = $args['subject'];
		else return false;

		if( ! empty( $args['message'] ) ) $message = $args['message'];
		else return false;


		// Optional Vars
		$from_name 		= key_exists( 'from_name', $args ) 		? $args['from_name'] 		: null;
		$to_name 		= key_exists( 'to_name', $args ) 		? $args['to_name'] 			: null;
		$reply_to_name 	= key_exists( 'reply_to_name', $args ) 	? $args['reply_to_name'] 	: null;
		$reply_to_email	= key_exists( 'reply_to_email', $args ) ? $args['reply_to_email'] 	: null;
		$extra_headers	= key_exists( 'extra_headers', $args )	? $args['extra_headers']	: null;


		// set variables
		$boundary 	 = uniqid( 'waw' ); 		// boundary for multipart email
		//$nl 		 = "\r\n";					// set the CRLF in a variable


		// HEADERS
		//
		// Assemble message headers
		//$headers = array();

		// add the MIME version
		$headers[] = "MIME-Version: 1.0";


		// from address
		if( ! empty( $from_name ) ) $from = "$from_name <$from_email>";
			else 					$from = $from_email;

		// to address
		if( ! empty( $to_name ) )   $to = "$to_name <$to_email>";
			else 					$to = $to_email;

		// to address
		if( ! empty( $reply_to_email ) )
		{
			if( ! empty( $reply_to_name ) )
			{
				$reply_to = "$reply_to_name <$reply_to_email>";
			}
			else
			{
				$reply_to = "$reply_to_email";
			}
		}
		else
		{
			$reply_to = $to;
		}



		// assemble headers
		$headers[] = "From: $from";
		$headers[] = "To: $to";
		$headers[] = "Reply-To: $reply_to";
		//$headers[] = "Subject: $subject";


		// grab extra headers if they're there
		if( ! empty( $extra_headers ) )
		{
			foreach( $extra_headers as $header ) $headers[] = $header;
		}

		// add the content-type
		$headers[] = "Content-Type: multipart/alternative;boundary=\"$boundary\"";

		// implode array
		$headers = implode( "$nl", $headers );


		// MESSAGE
		//
		// Assemble the message
		//
		// First, assemble the html version
		$msg_html  = "<div style=\"font: normal 10pt/1.5 arial, helvetica, sans-serif\">$message</div>";
		//
		//// Remove all line breaks
		//$msg_html = str_replace( array( "\r", "\n" ), '', $msg_html );

		// Next, assemble the plain version
		$msg_plain = strip_tags( $message );


		// Put the full submission message together
		$msg = array();
		$msg[] = "--$boundary";
		$msg[] = "Content-Type: text/plain; charset=\"utf-8\"";
		$msg[] = "Content-Transfer-Encoding: quoted-printable$nl";
		$msg[] = $msg_plain;
		$msg[] = "$nl--$boundary";
		$msg[] = "Content-Type: text/html; charset=\"utf-8\"";
		$msg[] = "Content-Transfer-Encoding: base64$nl";
		$msg[] = rtrim( chunk_split( base64_encode( $msg_html), 60, $nl ) );
		$msg[] = "$nl--$boundary--";

		$msg = implode( $nl, $msg );

		//print_array( $headers );
		//print_array( $subject );
		//print_array( $msg );

		return mail( '', $subject, $msg, $headers );

	}




	private function process_attachments()
	{
		$inputs = (array) $this->sub_sanitized_data;

		//$inputs = (array) $post->wf_input;
		$atts = array();

		foreach( $inputs as $input )
		{
			$input = (array) $input;
			if( is_array( $input ) && array_key_exists( 'files', $input ) )
			{
				if( empty( $input['files'] ) ) continue;

				$arr = (array) $input['files'];

				foreach( $arr as $val )
				{
					//$path = wsform_UPLOADS_DIR_PATH . '/' . $val;

					if( ! empty( $val ) && file_exists( WSFORM_UPLOADS_DIR_PATH . '/' . $val ) )
						add_post_meta( $sub_id, '_wsform_attached_file', $val, false );
						//$atts[] = $val;
				}
			}
		}

		//// Save eatch attachment as a post_meta
		//foreach( $atts as $att )
		//	add_post_meta( $this->sub_id, '_wsform_attached_file', $att );
	}







    // function mark_message_read( $atts )
    // {
    //     $msg_id = intval( $atts['msg_id'] );
    //     $this->webbsitesform_sub( $msg_id );
    //     $this->update_sub_meta( '_wsform_sub_is_read', 1 );
    // }




    // function trash_this_sub( $atts )
    // {
    //     $msg_id = intval( $atts['msg_id'] );
    //     $this->webbsitesform_sub( $msg_id );
    //     $this->update_sub( 'post_status', 'trash' );
    // }



    static function validate( $val, $method )
    {
        if( $method == 'bool' ) $ret = boolval( $val );
        elseif( $method == 'int' ) $ret = intval( $val );
        elseif( $method == 'email' ) $ret = sanitize_email( $val );
        elseif( $method == 'url' ) $ret = esc_url( $val );
        elseif( $method == 'html' ) $ret = esc_html( $val );
        elseif( $method == 'attr' ) $ret = esc_attr( $val );
        else $ret = sanitize_text_field( $val );

        return $ret;
    }




    public function update_sub( $a = [] )
    {
        // var a = { c : 'wsfs', m : 'update_sub', o : '_wsform_sub_is_read', v : 1, vm : int, msg_id : msg_id };

        // First, validate that $attr is valid WP post attribute
        $post_atts = [ 
            'ID', 'post_author', 'post_date', 'post_date_gmt', 'post_content', 'post_title', 'post_excerpt',
            'post_status', 'comment_status', 'ping_status', 'post_password', 'post_name', 'to_ping', 'pinged',
            'post_modified', 'post_modified_gmt', 'post_content_filtered', 'post_parent', 'guid', 'menu_order',
            'post_type', 'post_mime_type', 'comment_count'
        ];

        if( ! in_array( $a['o'], $post_atts ) )
        {
            $this->send_error( 'Invalid attribute' );
        }
        else
        {
            $attr = $a['o'];
        }

        // Validate value
        $value = $this::validate( $a['v'], $a['vm'] );

        // Save the array for the wp function
        $post_atts = array(
            'ID' => $this->sub_id,
            $attr => $value
        );

        $val = wp_update_post( [ 'ID' => $this->sub_id, $attr => $value ], 1, 0 );

        if( $val === false )
        {
            $this->send_error( 'Unable to update post' );
        }
        else
        {
            $this->output( $val );
        }
    }





    public function update_sub_meta( $a = [] )
    {
        // // Get the message ID
        // $msg_id = intval( $a['msg_id'] );

        // Valid metas
        $post_meta = [
            '_wsform_sub_parent', '_wsform_sub_serialized_data', '_wsform_sub_wf_sender_name', 
            '_wsform_sub_wf_sender_email', '_wsform_sub_form_id', '_wsform_sub_wf_page_uri',
            '_wsform_sub_user_ip', '_wsform_sub_time', '_wsform_sub_date', '_wsform_sub_is_read'
        ];

        // If not valid sub meta, throw an error
        if( ! in_array( $a['o'], $post_meta ) )
        {
            $this->send_error( 'Invalid attribute' );
        }
        else
        {
            $meta = $a['o'];
        }

        // Validate value
        $value = WebbsitesFormSub::validate( $a['v'], $a['vm'] );

        // Attempt to update the post meta
        $val = update_post_meta( $this->sub_id, $meta, $value );

        if( $val === false )
        {
            $this->send_error( 'Unable to update meta' );
        }
        else
        {
            $this->output( $val );
        }
    }





	function __destruct() {}

}




    // function action()
    // {
    //     echo 'hello';
    //     return;
    
    //     // global $wpdb; // this is how you get access to the database
    
    //     // Get the stuff from $_POST that we need
    //     $atts = $_POST['atts'];
    
    //     // get the class name
    //     $class = $atts['c'] == 'wsfs' ? 'WebbsitesFormSub' : 'WebbsitesForm';
    
    //     // get the function name
    //     $method = $atts['m'];
    
    //     // if the class doesn't exist, throw error 'no class'
    //     if( ! class_exists( $class ) )
    //     {
    //         ws_forms_throw_error( 'no class' );
    //     }
    
    //     // if the method doesn't exist, throw error 'no method'
    //     elseif( ! method_exists( $class, $method ) )
    //     {
    //         ws_forms_throw_error( 'no method' );
    //     }
    //     else
    //     {
    //         // execute the method
    //         $obj = new $class();
    //         $obj->$method( $atts );
    //     }
    
    //     wp_die(); // this is required to terminate immediately and return a proper response
    // }






