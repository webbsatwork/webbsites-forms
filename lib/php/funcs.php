<?php

// Shortcodes
add_shortcode( 'webbsitesform', 'wsform_shortcode' );

function wsform_shortcode( $atts )
{
	if( is_admin() ) return;
	
	// Decipher the shortcode info
	$a = shortcode_atts( array( 'id' => null ), $atts );
	
	// Extract the form ID
	$form_id = $a['id'];
	
	// If it's empty, stop
	if( empty( $form_id ) ) return false;
	
	// Start the output buffer
	ob_start();
	
	// Crunch the form
	wsform( $form_id );
	
	// Return the info
	return ob_get_clean();
}





function wsform( $form_id )
{
	// Hide from backend
	if( is_admin() ) return;
	
	// Create object instance
	$wf = new WebbsitesForm();
	
	// Build the form
	$wf->build_form( array( 'form_id' => $form_id ) );
}



function wsform_download_test( $post )
{
    $f = new WebbsitesFormSub();
    $f->download_forms_test( $post );
}



function wsform_mark_msg_read( $post )
{
    $f = new WebbsitesFormSub();
    $f->mark_msg_read( $post );
}





function wsform_generate_pdf( $content, $atts = array() )
{
    echo '1';
    return true;

	WebbsitesForm::generate_pdf( $content, $atts );
}





function wsform_css()
{
	WebbsitesForm::forms_css();
}





function wsform_state_picker( $name, $css_class, $css_id )
{
	// Wrapper for WebbsitesForm::state_picker
	
	$args = array(
		'name' => $name,
		'css_class' => $css_class,
		'css_id' => $css_id
	);
	
	WebbsitesForm::state_picker( $args );
}






function wsform_files_delete( $post, $files )
{
	$filename = $post['filename'];
	
	$file_path = WSFORM_UPLOADS_DIR_PATH . '/' . $filename;
	
	if( ! file_exists( $file_path ) )
	{
		echo 'no file';
	}
	else
	{
		if( ! unlink( $file_path ) )
		{
			echo 'unable to delete';
		}
		else
		{
			echo 1;
		}
	}
	
	//print_array( $post );
}




function wsform_files_upload( $post, $files )
{
	//print_array( $post );
	
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



function wsform_file_ext( $filename )
{
	return substr( $filename, strrpos( $filename, '.' ) );
}



function wsform_suffix( $string, $suffix )
{
	// get the position of the last period
	$period = strrpos( $string , '.' );
	
	if( $period !== false )
	{
		return substr( $string, 0, $period ) . '-' . $suffix . substr( $string, $period );
	}
	else
	{
		return false;
	}
}



function wsform_form_submit( $post, $files )
{
	$wfs = new WebbsitesFormSub();
	
	$wfs->form_submit( $post, $files );
}




function wsform_display_sub( $sub_id )
{
	// New instance
	// Get the submission info
	$wfs = new WebbsitesFormSub( $sub_id );
	// $wfs->webbsitesform_sub( $sub_id );
	
	// Display it
	$wfs->display_sub();
}










// function wsform_shortcode_handle( $form_id ) 	{ return WebbsitesForm::admin_shortcode_handle( $form_id ); }
// function wsform_php_handle( $form_id ) 			{ return WebbsitesForm::admin_php_handle( $form_id ); }





function wsform_file_upload_max_size()
{
  static $max_size = -1;
  
  //$post_max_size = ini_get( 'post_max_size' );
  //$upload_max = ini_get( 'upload_max_filesize' );
  //return " $post_max_size, $upload_max";

  if ($max_size < 0) {
    // Start with post_max_size.
    $post_max_size = wsform_parse_size(ini_get('post_max_size'));
    if ($post_max_size > 0) {
      $max_size = $post_max_size;
    }

    // If upload_max_size is less, then reduce. Except if upload_max_size is
    // zero, which indicates no limit.
    $upload_max = wsform_parse_size(ini_get('upload_max_filesize'));
    if ($upload_max > 0 && $upload_max < $max_size) {
      $max_size = $upload_max;
    }
  }
  return $max_size;
}






function wsform_parse_size( $size )
{
	$unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
	$size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
	
	if ($unit)
	{
		// Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
		return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
	}
	else
	{
		return round($size);
	}
}






function wsform_simplify_bytes( $bytes )
{
	if( $bytes > 1000000000 ) return round( $bytes / 1000000000 ) . ' GB';
	elseif( $bytes > 1000000 ) return round( $bytes / 1000000 ) . ' MB';
	elseif( $bytes > 1000 ) return round( $bytes / 1000 ) . ' KB';
	else return $bytes . ' bytes';
}




// add the print_array function if it doesn't exist
if( ! function_exists( 'print_array' ) ) :

function print_array( $post, $hide = 0 )
{
	$output = '<pre>' . print_r( $post, 1 ) . '</pre>';
	if( $hide == 1 ) $output = '<!-- ' . $output . ' -->';
	echo $output;
}

endif;







