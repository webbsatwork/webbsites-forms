<?php

// CUSTOM COLUMNS
// Submissions
// Set columns
add_filter( 'manage_wsform_sub_posts_columns', 'waw_submission_post_columns' );


// Require the Wordpress screen class if it hasn't been called
require_once( ABSPATH . 'wp-admin/includes/screen.php' );


function waw_submission_post_columns( $columns )
{
	$n_columns['cb'] = '<input type="checkbox" />';
	$n_columns['sub_title'] = 'Title';
	$n_columns['form_name'] = 'Form Name';
	$n_columns['date'] = 'Date';
	
    return $n_columns;
}




// Define sortable columns
add_filter( 'manage_wsform_sub_sortable_columns', 'WSFORM_sortable_columns' );
function wsform_sortable_columns( $columns )
{
    $columns['sub_title'] = 'Title';
 
    return $columns;
}




// Sort columns
add_action( 'pre_get_posts', 'wsform_custom_orderby' );
// add_action( 'pre_get_posts', 'wsform_subs_custom_orderby' );

function wsform_custom_orderby( $query )
{
	// Only on admin pages
	if ( ! is_admin() )
		return;
	
	// Only on wsform_form posts
	$screen = get_current_screen();
	
	if( $screen == null || $screen->id != 'edit-wsform_form' ) return false;

    // Aim only at the main page query
    global $wp_the_query;

    // if( is_main_query() )
    if ( $wp_the_query === $query ) 
    {
        $query->set( 'meta_key', '_wsform_form_id' );
        $query->set( 'orderby', 'meta_value_num' );
        $query->set( 'order', 'ASC' );
    }

    return $query;
}



// function wsform_subs_custom_orderby( $query )
// {
// 	// Only on admin pages
// 	if ( ! is_admin() )
// 		return;
	
// 	// Only on wsform_sub posts
// 	$screen = get_current_screen();
	
// 	if( $screen == null || $screen->id != 'edit-wsform_sub' ) return false;
	
// 	$query->set( 'orderby', 'date' );
// 	$query->set( 'order', 'DESC' );
// }





// // Populate columns
// add_action( 'manage_wsform_sub_posts_custom_column', 'waw_submission_edit_table_content', 10, 2 );

// function waw_submission_edit_table_content( $column_name, $sub_id )
// {
// 	//$fields = wsform_fields_from_sub_id( $post_id );
// 	//$the_title = get_the_title();
// 	//$post_slug = get_the_permalink( $post_id );
	
// 	$wf = new WebbsitesFormSub();
	
// 	$wf->webbsitesform_sub( $sub_id );
	
// 	//$sub_title = $wf->sub_title;
// 	//$post_slug = $wf->output_permalink;
	
//     switch( $column_name )
//     {
// 		case 'sub_title' :
			
// 			echo '<a href="' . $wf->output_permalink . '" target="_blank"><span class="dashicons dashicons-visibility"></span></a>&nbsp;&nbsp;<strong><a class="row-title" href="post.php?post=' . $sub_id . '&amp;action=edit" aria-label="�' . $wf->sub_title . '� (Edit)">' . $wf->sub_title. '</a></strong>';
			
// 			break;
		
// 		case 'trash' :
			
// 			echo '<span class="wf-align-center"><a href="' . get_delete_post_link( $sub_id ) . '"><span class="dashicons dashicons-trash trash-red"></span></a></span>';
			
// 			break;
		
// 		case 'form_name' :
			
// 			echo '<a href="post.php?post=' . $wf->post_id . '&action=edit">' . $wf->post_title . '</a>';
			
// 			break;
        
//     }
// }



// Forms
// Set columns
add_filter( 'manage_wsform_form_posts_columns', 'waw_form_post_columns' );

function waw_form_post_columns( $columns )
{
	$n_columns['cb'] = '<input type="checkbox" />';
	$n_columns['wsform-id'] = 'ID';
	$n_columns['title'] = 'Title';
	$n_columns['wsform-responses'] = 'Responses';
	$n_columns['wsform-shortcode'] = 'Shortcode';
	$n_columns['wsform-php'] = 'PHP';
	$n_columns['date'] = 'Date';
	
    return $n_columns;
}



// Populate columns
add_action( 'manage_wsform_form_posts_custom_column', 'waw_form_edit_table_content', 10, 2 );

function waw_form_edit_table_content( $column_name, $post_id )
{
    global $post;
    // print_array( $post );

	$form_id = get_post_meta( $post_id, '_wsform_form_id', 1 );

    switch( $column_name )
    {
        case 'wsform-id' :
			
            echo '<span class="wsform-columns-id">' . $form_id . '</span>';
            
            break;

        case 'wsform-shortcode' :
			
            // return '&lsqb;webbsitesform id=&quot;' . $form_id . '&quot;&rsqb;';
            // $handle = WebbsitesForm::admin_shortcode_handle( $form_id );

            $ret = '<span class="copy-to-clipboard" data-text-to-copy="[webbsitesform id=\'' 
                 . $form_id . '\']" title="Copy to clipboard">&lsqb;webbsitesform id=&quot;' 
                 . $form_id . '&quot;&rsqb;</span>';
            
            echo $ret;
            
            break;
		
        case 'wsform-php' :
			
            // return '&lt;?php webbsitesform&lpar;' . $form_id . '&rpar;&semi; ?&gt;';
            // $handle = WebbsitesForm::admin_php_handle( $form_id );
            // echo '<span class="copy-to-clipboard" title="Copy to clipboard">' . $handle . '</span>';
            
            $ret = '<span class="copy-to-clipboard" data-text-to-copy="<?php webbsitesform(' 
                 . $form_id . '); ?>" title="Copy to clipboard">&lt;?php webbsitesform&lpar;' 
                 . $form_id . '&rpar;&semi; ?&gt;</span>';
            
            echo $ret;
            
            break;
		
        case 'wsform-responses' :

            WebbsitesForm::admin_column_responses( $post_id );

            break;
        
    }
}






//// CUSTOM COLUMNS
//// Submissions
//// Set columns
//add_filter( 'manage_wsform_sub_posts_columns', 'WSFORM_submission_post_columns' );
//
//function wsform_submission_post_columns( $columns )
//{
//	$n_columns['cb'] = '<input type="checkbox" />';
//	$n_columns['sub_title'] = 'Title';
//	$n_columns['form_name'] = 'Form Name';
//	$n_columns['date'] = 'Date';
//	
//    return $n_columns;
//}
//
//
//
//
//// Define sortable columns
//add_filter( 'manage_wsform_sub_sortable_columns', 'WSFORM_sortable_columns' );
//function wsform_sortable_columns( $columns )
//{
//    $columns['sub_title'] = 'Title';
// 
//    return $columns;
//}
//
//
//
//
//// Sort columns
//add_action( 'pre_get_posts', 'WSFORM_custom_orderby' );
//add_action( 'pre_get_posts', 'WSFORM_subs_custom_orderby' );
//
//function wsform_custom_orderby( $query )
//{
//	// Only on admin pages
//	if ( ! is_admin() )
//		return;
//	
//	// Only on wsform_form posts
//	$screen = get_current_screen();
//	if( $screen->id != 'edit-wsform_form' )
//		return;
//	
//	$query->set( 'meta_key', '_wsform_form_id' );
//	$query->set( 'orderby', 'meta_value_num' );
//	$query->set( 'order', 'ASC' );
//}
//
//
//
//function wsform_subs_custom_orderby( $query )
//{
//	// Only on admin pages
//	if ( ! is_admin() )
//		return;
//	
//	// Only on wsform_form posts
//	$screen = get_current_screen();
//	if( $screen->id != 'edit-wsform_sub' )
//		return;
//	
//	$query->set( 'orderby', 'date' );
//	$query->set( 'order', 'DESC' );
//}
//
//
//
//
//
//// Populate columns
//add_action( 'manage_wsform_sub_posts_custom_column', 'WSFORM_submission_edit_table_content', 10, 2 );
//
//function wsform_submission_edit_table_content( $column_name, $sub_id )
//{
//	//$fields = wsform_fields_from_sub_id( $post_id );
//	//$the_title = get_the_title();
//	//$post_slug = get_the_permalink( $post_id );
//	
//	$wf = new WebbsitesFormSub();
//	
//	$wf->webbsitesform_sub( $sub_id );
//	
//	//$sub_title = $wf->sub_title;
//	//$post_slug = $wf->output_permalink;
//	
//    switch( $column_name )
//    {
//		case 'sub_title' :
//			
//			echo '<a href="' . $wf->output_permalink . '" target="_blank"><span class="dashicons dashicons-visibility"></span></a>&nbsp;&nbsp;<strong><a class="row-title" href="post.php?post=' . $sub_id . '&amp;action=edit" aria-label="�' . $wf->sub_title . '� (Edit)">' . $wf->sub_title. '</a></strong>';
//			
//			break;
//		
//		case 'trash' :
//			
//			echo '<span class="wf-align-center"><a href="' . get_delete_post_link( $sub_id ) . '"><span class="dashicons dashicons-trash trash-red"></span></a></span>';
//			
//			break;
//		
//		case 'form_name' :
//			
//			echo '<a href="post.php?post=' . $wf->post_id . '&action=edit">' . $wf->post_title . '</a>';
//			
//			break;
//        
//    }
//}
//
//
//
//// Forms
//// Set columns
//add_filter( 'manage_wsform_form_posts_columns', 'WSFORM_form_post_columns' );
//
//function wsform_form_post_columns( $columns )
//{
//	$n_columns['cb'] = '<input type="checkbox" />';
//	$n_columns['title'] = 'Title';
//	$n_columns['form_id'] = 'Form ID';
//	$n_columns['form_shortcode'] = 'Shortcode';
//	$n_columns['form_php'] = 'PHP';
//	// $n_columns['taxonomy-form_name'] = 'Form Name';
//	$n_columns['date'] = 'Date';
//	// $n_columns['trash'] = '<span class="wf-align-center">Trash</trash>';
//	
//    return $n_columns;
//}
//
//
//
//// Populate columns
//add_action( 'manage_wsform_form_posts_custom_column', 'WSFORM_form_edit_table_content', 10, 2 );
//
//function wsform_form_edit_table_content( $column_name, $post_id )
//{
//	$form_id = get_post_meta( get_the_ID(), '_wsform_form_id', 1 );
//	
//    switch( $column_name )
//    {
//        case 'form_id' :
//			
//            echo '<span class="wsform-columns-id">' . $form_id . '</span>';
//            
//            break;
//		
//        case 'form_shortcode' :
//			
//            echo wsform_shortcode_handle( $form_id );
//            
//            break;
//		
//        case 'form_php' :
//			
//            echo wsform_php_handle( $form_id );
//            
//            break;
//		
//		case 'trash' :
//			
//			echo '<span class="wf-align-center"><a href="' . get_delete_post_link( $post_id ) . '"><span class="dashicons dashicons-trash trash-red"></span></a></span>';
//			
//			break;
//        
//    }
//}
//
//
