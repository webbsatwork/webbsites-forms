<?php



add_action( 'add_meta_boxes', 'wsform_register_meta_boxes' );

function wsform_register_meta_boxes( $hook )
{
	if( $hook == 'wsform_form' )
	{
		// meta boxes for forms
		wsform_register_forms_meta_boxes( $hook );
	}
	else
	{
		// meta boxes for submissions
		wsform_register_submission_meta_boxes( $hook );
	}
}





// meta box for FORMS
function wsform_register_forms_meta_boxes( $hook )
{
	if( $hook != 'wsform_form' ) return false;
	
	$id = get_the_ID();
	
	$wf = new WebbsitesForm( $id );
	
	// $wf->wsform( array( 'post_id' => $id ) );
	
	add_meta_box(
		'wf-form-type',
		esc_html__( 'Form Attributes', 'webbsites' ),
		array( $wf, 'meta_form_attributes' ),
		'wsform_form',
		'normal',
		'high'
	);
}





// for saving forms
add_action( 'save_post', 'wsform_save_postdata' );

function wsform_save_postdata( $post_id )
{
	$keys = WebbsitesForm::form_parameters();
	
	foreach( $keys as $key )
	
		if ( array_key_exists( $key, $_POST ) ) $return[$key] = update_post_meta( $post_id, $key, $_POST[$key] );
	
}















// SUBMISSIONS


// for saving submissions
add_action( 'save_post', 'wsform_subs_save_postdata' );
function wsform_subs_save_postdata( $post_id )
{
    if (array_key_exists( '_wsform_sub_entry', $_POST ) ) update_post_meta( $post_id, '_wsform_sub_entry', $_POST['_wsform_sub_entry'] );
}


// meta box for submissions
function wsform_register_submission_meta_boxes( $hook )
{
	if( $hook != 'wsform_sub' ) return false;
	
	$wf = new WebbsitesFormSub();
	
	$wf->webbsitesform_sub(get_the_ID() );
	
    add_meta_box(
        'wf-submission-display-meta-box-id',
        esc_html__( 'Submission Content', 'webbsites' ),
        array( $wf, 'display_meta_box' ),
        'wsform_sub',
        'normal',
        'low'
        );
}




