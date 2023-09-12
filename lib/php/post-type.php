<?php



add_action( 'init', 'wsform_register_post_types' );
/**
 * Register WF post types
 *
 * @link http://codex.wordpress.org/Function_Reference/register_post_type
 */

function wsform_register_post_types()
{
	// post type 'wsform_form'
	wsform_post_type_form();
	
	// post type 'wsform_sub'
	wsform_post_type_form_sub();
}
 
 
 
function wsform_post_type_form()
{
	$labels = array(
		'name'               => _x( 'Webbsites Forms', 'post type general name', 'webbsites' ),
		'singular_name'      => _x( 'Form', 'post type singular name', 'webbsites' ),
		'menu_name'          => _x( 'WS Forms', 'admin menu', 'webbsites' ),
		'name_admin_bar'     => _x( 'Form', 'add new on admin bar', 'webbsites' ),
		'add_new'            => _x( 'Add New', 'form', 'webbsites' ),
		'add_new_item'       => __( 'New Form', 'webbsites' ),
		'new_item'           => __( 'New Form', 'webbsites' ),
		'edit_item'          => __( 'Edit Form', 'webbsites' ),
		'view_item'          => __( 'View Form', 'webbsites' ),
		'all_items'          => __( 'Forms', 'webbsites' ),
		'search_items'       => __( 'Search Forms', 'webbsites' ),
		'not_found'          => __( 'No forms found.', 'webbsites' ),
		'not_found_in_trash' => __( 'No forms found in Trash.', 'webbsites' )
	);

	$args = array(
		'labels'             => $labels,
        'description'        => __( 'Description.', 'webbsites' ),
		'public'             => false,
		'publicly_queryable' => false,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'show_in_nav_menus'  => true,
		'rewrite'            => [ 'slug' => 'form' ],
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => null,
        'menu_icon'          => 'dashicons-media-spreadsheet',
		'capability_type'    => 'post',
		'map_meta_cap' 		 => true, // Set to `false`, if users are not allowed to edit/delete existing posts
        // 'taxonomies'         => array( 'wsform_category' ),
		'supports'           => [ 'title' ]
	);

	register_post_type( 'wsform_form', $args );
}





function wsform_post_type_form_sub()
{
	// $slabels = array(
		// 'name'               => _x( 'Form Submissions', 'post type general name', 'webbsites' ),
		// 'singular_name'      => _x( 'Submission', 'post type singular name', 'webbsites' ),
		// 'menu_name'          => _x( 'Submissions', 'admin menu', 'webbsites' ),
		// 'name_admin_bar'     => _x( 'Submission', 'add new on admin bar', 'webbsites' ),
		// 'edit_item'          => __( 'Form Submission', 'webbsites' ),
		// 'view_item'          => __( 'View Submission', 'webbsites' ),
		// 'all_items'          => __( 'Submissions', 'webbsites' ),
		// 'search_items'       => __( 'Search Submissions', 'webbsites' ),
		// 'not_found'          => __( 'No submissions found.', 'webbsites' ),
		// 'not_found_in_trash' => __( 'No submissions found in Trash.', 'webbsites' )
	// );

	$sargs = array(
		// 'labels'             => $slabels,
        'description'        => __( 'Description.', 'webbsites' ),
		'public'             => false,
		'publicly_queryable' => false,
		// 'show_ui'            => true,
		// 'show_in_menu'		 => true,
		// 'show_in_menu'		 => 'edit.php?post_type=wsform_form',
		// 'show_in_nav_menus'  => true,
		// 'rewrite'            => [ 'slug' => 'form' ],
		'has_archive'        => false,
		// 'hierarchical'       => true,
		// 'menu_position'      => null,
		'capability_type'    => 'post',
		'capabilities' 		 => [ 'create_posts' => 'do_not_allow' ],
		'map_meta_cap' 		 => false, // Set to `false`, if users are not allowed to edit/delete existing posts
        // 'taxonomies'         => array( 'wsform_category' ),
		'supports'           => array( 'title' )
	);

	register_post_type( 'wsform_sub', $sargs );
}




// // hook into the init action and call create_book_taxonomies when it fires
// add_action( 'init', 'wsform_create_taxonomies', 0 );


// function wsform_create_taxonomies()
// {
// 	wsform_create_form_category_taxonomy();
// }





// // form categories
// function wsform_create_form_category_taxonomy()
// {
// 	// Add new taxonomy, make it hierarchical (like categories)
// 	$labels = array(
// 		'name'              => _x( 'Form Category', 'taxonomy general name' ),
// 		'singular_name'     => _x( 'Form Category', 'taxonomy singular name' ),
// 		'search_items'      => __( 'Search Form Categories' ),
// 		'all_items'         => __( 'All Form Categories' ),
// 		'edit_item'         => __( 'Edit Form Category' ),
// 		'update_item'       => __( 'Update Form Category' ),
// 		'add_new_item'      => __( 'Add New Form Category' ),
// 		'new_item_name'     => __( 'New Form Category' ),
// 		'menu_name'         => __( 'Categories' ),
// 	);

// 	$args = array(
// 		'hierarchical'      => true,
// 		'labels'            => $labels,
// 		'show_ui'           => true,
// 		'show_admin_column' => true,
// 		'show_in_nav_menus' => false,
// 		'query_var'         => true,
// 		'rewrite'           => array( 'slug' => 'form-category' ),
// 	);

// 	register_taxonomy( 'wsform_category', array( 'wsform_form', 'wsform_sub' ), $args );
// }




/**
 * Add new rewrite for post type "wsform_sub"
 * Add permalink structure
 */
function wsform_post_type_rewrite() {
    global $wp_rewrite;
 
    // Set the query arguments used by WordPress
    $queryarg = 'post_type=wsform_sub&p=';
 
    // Concatenate %cpt_id% to $queryarg (eg.. &p=123)
    $wp_rewrite->add_rewrite_tag( '%cpt_id%', '([^/]+)', $queryarg );
 
    // Add the permalink structure
    $wp_rewrite->add_permastruct( 'wsform_sub', '/submissions/submission/%cpt_id%/', false );
}
add_action( 'init', 'wsform_post_type_rewrite' );



// /**
//  * Hide permalink on admin page
//  *
//  */
// add_filter( 'get_sample_permalink_html', 'wsform_permalinks' );

// function wsform_permalinks( $in )
// {
// 	$screen = get_current_screen();
	
// 	if( $screen->id == 'wsform_sub'  )
// 	{
// 		return '<p>No permalink for this post type</p>';
// 	}
	
// 	elseif( $screen->id == 'wsform_form' )
// 	{
// 		if( $screen->action != 'add' )
// 		{
// 			$form_id = get_post_meta( get_the_ID(), '_wsform_form_id', 1 );
			
// 			$html = '<div id="edit-slug-box">'
// 				  . '<strong>Shortcode</strong>: ' . wsform_shortcode_handle( $form_id ) . '; '
// 				  . '<strong>PHP</strong>: ' . wsform_php_handle( $form_id ) . '; '
// 				  . '</div>';
			
// 			return $html;
// 		}
// 	}
// 	else
// 	{
// 		return $in;
// 	}
// }




