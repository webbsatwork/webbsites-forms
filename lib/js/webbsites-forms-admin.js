
jQuery( document ).ready(function( $ ) {

	// Initialize jQuery UI Sortable
	$( '.wsform-sortable-list' ).sortable({ handle: ".wsform-sg-move-item", axis: "y" });

	// Add event to update hidden input on sortable updated position
	$( '.wsform-sortable-list' ).on( 'sortupdate', function() { wsform_update_sg_input(); });

	// Disable dragging of WP metaboxes
    $('.meta-box-sortables').sortable({ disabled: true });
    $('.postbox .hndle').css('cursor', 'auto');

	// select tab
	$( '.wsform-tab' ).click( function( e ) {

		e.preventDefault();

		var tab = $( this );
		var ident = tab.attr( 'rel' );
		var target = $( '.wsform-section.' + ident );

		// alert( 'Ident: ' + ident );

		$('.wg-active').removeClass( 'wg-active' );
		target.addClass( 'wg-active' );

		$('.nav-tab-active').removeClass( 'nav-tab-active' );
		tab.addClass( 'nav-tab-active' );

		// change the address label
		var title = document.title;
		var doc_url = document.URL;
		var end = doc_url.indexOf('&tab=');
		if( end >= 0 ) doc_url = doc_url.substring( 0, end );
		var new_url = doc_url + '&tab=' + ident;
		window.history.pushState('', title, new_url);

        var path = document.location.pathname + document.location.search;
        // alert(path)
        $( 'input[name="_wp_http_referer"]' ).val( path );

	});



    $( '#wsform-check-all-days' ).click( function() {
        var vist = $( '#wsform-display-visibility-table' );
        let itms = vist.find( '.wsform-vis-table-input.active-day' );
        let chkd = vist.find( '.wsform-vis-table-input.active-day:checked' );

        var stat = itms.length == chkd.length ? false : true;
        
        itms.each( function() {
            $( this ).prop( 'checked', stat ).trigger( 'change' );
        });
    });



    $( '.wsform-vis-table-input' ).change( function() {

        var ip = $( this );
        var p = ip.attr( 'rel' );

        // First, check to see if any items need to be revealed or hidden
        if( ip.hasClass( 'active-day' ) )
        {
            if( ip.is( ':checked' ) )
            {
                $( '.all-day-' + p ).removeClass( 'wsform-hide' );
            }
            else
            {
                $( '.all-day-' + p ).addClass( 'wsform-hide' );
                $( '#all-day-' + p ).prop( 'checked', true );
                $( '.from-time-' + p + ',.to-time-' + p ).addClass( 'wsform-hide' );
            }
        }
        else if( ip.hasClass( 'all-day' ) )
        {
            if( ip.is( ':not(:checked)' ) )
            {
                $( '.from-time-' + p + ',.to-time-' + p ).removeClass( 'wsform-hide' );
            }
            else
            {
                $( '.from-time-' + p + ',.to-time-' + p ).addClass( 'wsform-hide' );
            }
        }

        // Next, get all the data and save it
        var vals = {};

        var inputs = $( '#wsform-display-visibility-table' ).find( '.wsform-vis-table-input' );

        inputs.each( function() {

            let inp = $( this );

            var key = inp.attr( 'data-field-name' );

            if( inp.is( ':checkbox' ) )
            {
                if( inp.is( ':checked' ) ) var val = true;
                else var val = false;
            }
            else
            {
                var val = $( this ).val();
            }

            vals[key] = val;

        });

        // Update the input
        let vals_c = btoa( JSON.stringify( vals ) );

        $( '#wsform_display_vis_table' ).val( vals_c );
    });




    $( '#wsform-display-visibility' ).change( function() {
        let sel = $( this ).val();
        let table = $( '#wsform-display-visibility-table' );
        
        if( sel == 'on-these-days' || sel == 'not-on-these-days' )
            table.removeClass( 'wsform-hide' );
        else
            table.addClass( 'wsform-hide' );
    });

    // Messages viewer
    // Read an item
    $( document ).on( 'click', '.wsform-message-list-item', function() {

        var litem = $( this );
        var sub_id = litem.attr( 'rel' );

        // Delete current item
        $( '#ws-form-active-message-con' ).html( '' ).removeClass( 'active-message' );

        // Update #ws-form-empty-viewer-header to "Loading..."
        $( '#ws-form-empty-viewer-header' ).addClass( 'active-message' ).find( 'h3' ).html( 'Loading ...' );

        if( litem.hasClass( 'active-item' ) )
        {
            litem.removeClass( 'active-item' );
            $( '#ws-form-empty-viewer-header' ).addClass( 'active-message' ).find( 'h3' ).html( 'No message selected' );
        }
        else
        {
            // Change the active item in the messages list
            $( this ).addClass( 'active-item' ).siblings().removeClass( 'active-item' );

            // Fetch the targeted message from the DB
            // Save the attributes
            let ar = { 
                c : 'wsfs', 
                m : 'form_sub_display',
                i : sub_id,
            };

            // Mark message as read in db
            wsform_admin_ajax( ar ).done( function( res ) {

                let result = JSON.parse( res );
                // console.log( result );

                // If mark message as read in db is true
                if( result.error == false )
                {
                    // alert('hi')
                    // Reset and hide header
                    $( '#ws-form-empty-viewer-header' ).removeClass( 'active-message' ).find( 'h3' ).html( 'No message selected' );
            
                    // Populate the viewer space
                    $( '#ws-form-active-message-con' ).addClass( 'active-message' ).html( result.output );
            
                    // Remove unread dot if present
                    if( litem.hasClass( 'message-unread' ) )
                    {
                        litem.removeClass( 'message-unread' );

                        // Save pointer to item in atts if there's a problem with db connection
                        let msg_item = 'li.wsform-message-list-item[rel="' + sub_id + '"]';

                        // Save the attributes
                        let atts = { 
                            c : 'wsfs', 
                            m : 'update_sub_meta', 
                            i : sub_id,
                            o : '_wsform_sub_is_read', 
                            v : 1,
                            vm : 'bool',
                            i : sub_id,
                            msg_rel : msg_item 
                        };

                        // Mark message as read in db
                        wsform_admin_ajax( atts ).done( function( r ) {
                            // var atts = { c : 'wsfs', m : 'mark_message_read', msg_id : new_msg_id, msg_rel : msg_item };
                            let output = JSON.parse( r );

                            // If there was an error, mark message as unread, then show error notice
                            if( output.error == true )
                            {
                                litem.addClass( 'message-unread' );
                                display_error( output.error_msg );
                            }

                            update_messages_count();

                        }).fail( display_error );
                    }
                }
                // ... if it's not
                else
                {
                    display_error( result.error_msg );
                }
        
            }).fail( display_error );
        }
    });

    // Send message to trash
    $( document ).on( 'click', 'span.wsform-msg-ctl.trash-message', function() {

        var msg_id = $( this ).parents( '.wsform-message-item' ).attr( 'rel' );

        if( confirm( 'Are you sure you want to send this message to the trash?' ) )
        {
            arm_popup();

            // Save the attributes
            let atts = {
                c : 'wsfs', 
                m : 'trash_sub', 
                // o : 'post_status', 
                // v : 'trash', 
                i : msg_id 
            };

            // Send message to trash in database
            wsform_admin_ajax( atts ).done( refresh_responses ).fail( display_error );
        }
    });

    // Recover a message
    $( document ).on( 'click', 'span.wsform-msg-ctl.recover-message', function() {
        var msg_id = $( this ).parents( '.wsform-message-item' ).attr( 'rel' );

        arm_popup();

        // Save the attributes
        var atts = {
            c : 'wsfs', 
            m : 'recover_sub', 
            i : msg_id 
        };

        // Recover message in database
        wsform_admin_ajax( atts ).done( refresh_responses ).fail( display_error );
    });

    // Permanently delete a message
    $( document ).on( 'click', 'span.wsform-msg-ctl.delete-message', function() {
        let msg_id = $( this ).parents( '.wsform-message-item' ).attr( 'rel' );

        if( confirm( 'Are you sure you want to permanently delete this message? This cannot be undone!' ) )
        {
            // Save the attributes
            let atts = {
                c : 'wsfs', 
                m : 'delete_sub', 
                i : msg_id
            };

            // Send message to trash in database
            wsform_admin_ajax( atts ).done( function( res ) {

                let result = JSON.parse( res );

                // If there wasn't error, mark message as unread, then show error notice
                if( result.error === false )
                {
                    update_messages_count();
                    clear_reader();
                    $( '#ws-form-message-list-item-' + msg_id ).remove();
                }
                else
                {
                    alert('some failure here');
                    // SHOW ERROR NOTICE HERE
                }
                console.log( result );
                
            }).fail( display_error );
        }
    });

	// Update elements input when element value is changed
	$( document ).on( 'change', '.wsform-data-input', function() {
        wsform_check_for_duplicate_ids();
		wsform_update_sg_input();
	});


    // Open download dialogue
    $( document ).on( 'click', '.wsform-popup.download-responses', function() {

        arm_popup();
        
        // Save the attributes
        let atts = {
            c : 'wsf', 
            m : 'form_responses_download_dialog', 
            r : $( this ).attr( 'rel' ),
            i : $( '#wsform-responses' ).attr( 'data-form-id' )
        };

        // Send message to trash in database
        wsform_admin_ajax( atts ).done( function( res ) {
            let result = JSON.parse( res );
            console.log( result );
    
            if( result.error == true )
            {
                display_error( result.error_msg )
            }
            else
            {
                $( '#wsform-popup-dialog-box' ).html( result.output );
            }

        }).fail( display_error );
    });



    // Refresh responses
    $( document ).on( 'click', '.wsform-popup.refresh-responses', function() {

        arm_popup();
        // clear_reader();

        let post_status = $( '#wsform-messages-list-area' ).attr( 'data-post-status' ) == 'trash' ? 'trash' : 'publish';

        // Save the attributes
        let atts = {
            c : 'wsf', 
            m : 'form_responses_refresh', 
            i : $( '#wsform-responses' ).attr( 'data-form-id' ),
            s : post_status
        };

        console.log( atts );

        // Refresh the viewer
        wsform_admin_ajax( atts ).done( refresh_responses ).fail( display_error );
    });


    function refresh_responses( res )
    {
        let result = JSON.parse( res );
        console.log( result );

        remove_popup();
        clear_reader();

        if( result.error == false )
        {
            $( '#wsform-messages-list' ).html( result.output );
        }
        else
        {
            display_error( result.error_msg );
        }
    }







    // Show / hide trash
    $( document ).on( 'click', '#trash-toggle', function( e ) {

        e.preventDefault();

        arm_popup();

        let post_status = $( this ).hasClass( 'show-trash' ) ? 'trash' : 'publish';

        // Save the attributes
        let atts = {
            c : 'wsf', 
            m : 'form_responses_refresh', 
            i : $( '#wsform-responses' ).attr( 'data-form-id' ),
            s : post_status
        };

        // console.log( atts );

        // Send message to trash in database
        wsform_admin_ajax( atts ).done( refresh_responses ).fail( display_error );
    });



    // Empty trash
    $( document ).on( 'click', '#empty-trash', function( e ) {

        e.preventDefault();

		if( confirm( 'Are you sure you want to empty the trash? This cannot be undone!' ) == true )
        {
            arm_popup();
            clear_reader();
    
            // Save the attributes
            let atts = {
                c : 'wsf', 
                m : 'empty_trash',
                i : $( '#wsform-responses' ).attr( 'data-form-id' ),
            };
    
            // Send ajax command
            wsform_admin_ajax( atts ).done( refresh_responses ).fail( display_error );
        }
    });



    function clear_reader()
    {
        $( '.wsform-message-item' ).remove();
        $( '#ws-form-empty-viewer-header' ).addClass( 'active-message' ).find( 'h3' ).html( 'No message selected' );
    }




    function arm_popup()
    {
        let popup   = '<div id="wsform-popup" class="wsform-popup-wrapper">'
                    + '<div id="wsform-popup-bg">'
                    + '<div id="wsform-popup-dialog-box"><p>Loading ...</p></div>'
                    + '</div></div>';
        $( '.wrap' ).prepend( popup );
    }


    function remove_popup()
    {
        $( '#wsform-popup' ).remove();
    }



    function display_error( t = 'There was an unknown error. Please try reloading the page.' )
    {
        remove_popup();
        $( '#ws-form-empty-viewer-header' ).find( 'h3' ).html( 'No message selected' );

        if( typeof t == 'string' ) alert( t );
        else alert( 'There was an unknown error' );
        
        console.log( t );
    }



    function activate_dialog_box( res )
    {
        
    }



    // Close download dialogue
    $( document ).on( 'click', '.ws-form-dialog-button.cancel-this', function() {
        $( '#wsform-popup' ).remove();
    });

    // Show/hide items
    $( document ).on( 'click', 'input[type="radio"]', function() {
        $( '.wsf-reveal-hide' ).each( function() {
            var tgt = $( '#' + $( this ).attr( 'rel' ) );
            if( $( this ).is( ':checked' ) ) 
            {
                tgt.removeClass( 'ws-forms-deac-items' );
                tgt.find( 'input,select' ).prop( 'disabled', false );
            }
            else 
            {
                tgt.addClass( 'ws-forms-deac-items' );
                tgt.find( 'input,select' ).prop( 'disabled', true );
            }
        });
    });


    // // Click to download subs
    // $( document ).on( 'click', '.download-this', function( e ) {

    //     e.preventDefault();

    //     var form = $( 'form#ws-forms-download-responses' );
    //     let fdata = form.serialize();

    //     fetch( wsform_ajax_admin.ws_forms_ajax, {
    //         method: "POST",
    //         body: JSON.stringify({
    //             c : 'wsf',
    //             d : fdata,
    //             m : 'download_subs',
    //             i : $( '#wsform-responses' ).attr( 'data-form-id' ),
    //         }),
    //         headers: {
    //           "Content-type": "application/json; charset=UTF-8"
    //         }
    //     })
    //         .then((response) => response.json())
    //         .then((json) => console.log(json));
    // });


    // Click to download files
    $( document ).on ( 'click', '.download-this', function ( e ) {
        e.preventDefault();
        let form_id = $( '#wsform-responses' ).attr( 'data-form-id' );
        let form = $( 'form#ws-forms-download-responses' );
        let atts = form.serialize();
        let data = atts + '&c=wsf&m=download_subs&i=' + form_id;
        window.open( wsform_ajax_admin.get_file_url + data, '_blank' );
        $( '#wsform-popup' ).remove();
    });


    // // Click to download files
    // $( document ).on ( 'click', '.download-this', function ( e ) {
    //     e.preventDefault();
    //     var form = $( 'form#ws-forms-download-responses' );
    //     let fdata = form.serializeArray();

    //     // Save the attributes
    //     let atts = {
    //         c : 'wsf',
    //         d : fdata,
    //         m : 'download_subs',
    //         i : $( '#wsform-responses' ).attr( 'data-form-id' ),
    //     };

    //     // console.log( atts );

    //     // Request the file to download
    //     wsform_admin_ajax( atts ).done( download_file ).fail( display_error );


    //     $( '#wsform-popup' ).remove();
    // });




    function download_file( html )
    {
        // alert(html)
        var newp = window.open( '' );
        newp.document.write( html );
    }




	// // Update labels on sortable item
	// $( document ).on( 'keyup', '.wsform-keyup-change', function() {
	// 	var input = $( this );
	// 	var val = input.val();
    //     var par = input.parents( '.wsform-sg-item' );
	// 	var label = par.find( '.wsform-sg-input-label' );
    //     var id = par.attr( 'data-wsform-ident' );
	// 	label.html( val );

    //     // Change ID
    //     var this_id_field = par.find( '.wsform-id' );
    //     var label_t = val.toLowerCase().replace( /[^\w\s]/g, '' ).replace( /[\s]/g, '_' ).replace( '__', '_' );
    //     this_id_field.val( id + label_t );
	// });

	// Sortable items control box
	$( document ).on( 'click', '.wsform-sg-item-control, .wsform-delete-item', function() {
		var par = $( this ).parents( '.wsform-sg-item' );
		if( confirm( 'Are you sure you want to delete this form element?' ) == true )
		{
			par.fadeOut(200).remove();
			wsform_update_sg_input();
		}
	});



    $( '.copy-to-clipboard' ).on( 'click', function() {

        let text = $( this ).attr( 'data-text-to-copy' );

        if( text == 'undefined' ) // DEBUG
        {
            text = $( this ).html();
        }

        copy_content( text );

    });


    const copy_content = async ( text ) => 
    {
        try 
        {
            await navigator.clipboard.writeText( text );
            alert( 'Content copied to clipboard' );
        }
        catch ( err )
        {
            console.error('Failed to copy: ', err);
        }
    }
    
    // // Copy text to clipboard
    // function copy_to_clipboard() 
    // {
    //     // Get the text field
    //     var copyText = document.getElementById("myInput");
      
    //     // Select the text field
    //     copyText.select();
    //     copyText.setSelectionRange(0, 99999); // For mobile devices
      
    //     // Copy the text inside the text field
    //     navigator.clipboard.writeText(copyText.value);
        
    //     // Alert the copied text
    //     alert("Copied the text: " + copyText.value);
    // }




	// Update field ID when label changes
	//$( document ).on( 'change', '.webbsitesform-label', function() {
	//	// inventory all ID names
	//	var label = $( this );
	//	var ids = [];
	//	var c = 0;
	//
	//	var idfield = label.closest( '.wsform-sg-item-body' ).find( '.webbsitesform-id' );
	//
	//	$( '.webbsitesform-id' ).each( function() {
	//		ids.push( $( this ).val() );
	//	});
	//
	//	var thisval = label.val().toLowerCase().replace( /\s/g, "_" ).replace( /[^A-Za-z0-9_]+/g, '' );
	//	var valcopy = thisval;
	//
	//
	//	if( ids.indexOf( thisval ) >= 0 )
	//	{
	//		while( ids.indexOf( thisval ) >= 0 )
	//		{
	//			c++;
	//			thisval = valcopy + '_' + c;
	//
	//			if( ids.indexOf( thisval ) < 0 )
	//			{
	//				idfield.val( thisval );
	//			}
	//		}
	//	}
	//	else
	//	{
	//		idfield.val( thisval );
	//	}
	//});

	// Select All Checkboxes
	$( document ).on( 'click', '.wsform-sel-all', function( e ) {

		e.preventDefault();

		// make sure we select the right group of boxes
		var btn = $( this );
		var boxes_par = btn.closest( '.wsform-checkboxes-div' );
		var boxes = boxes_par.find( '.wsform-checkbox' );

		// check 'em all
		boxes.prop( 'checked', true );

		// Trigger the change action
		$( '.wsform-checkbox' ).trigger( 'change' );
	});

	//// Update checkbox input
	//$( document ).on( 'change', '.wsform-checkbox', function() {
	//	update_wsform_checkboxes( $( this ) );
	//});

	// Add hidden inputs for .wsform-checkbox
	$( '.wsform-checkbox' ).each( function() {
		var cb = $( this );
		var id = cb.attr( 'name' );
		var val = cb.val();
		var par = cb.parents( 'label' );
		par.append( '<input type="hidden" id="input' + id + '" name="' + id + '" value="' + val + '" />' );
		cb.removeAttr( 'name' ).removeAttr( 'value' );
	});

	// Update checkbox hidden elements on click
	$( document ).on( 'change', '.wsform-checkbox', function() {
		var cb = $( this );
		var hi = cb.parents( 'label' ).children( 'input[type="hidden"]' );
		if( cb.prop( 'checked' ) == true ) hi.val( 'checked' );
		else hi.val( 'not checked' );
	});

	// Check the associated box on keyup
	$( '.wsform-output-watcher' ).keyup( function() {
		if( $( this ).val() == '' ) return false;
		//var rel = $( this ).attr( 'rel' );
		var tgt = $( '#input' + $( this ).attr( 'rel' ) );
		tgt.val( 'checked' ).parents( 'label' ).children( '.wsform-output-choose' ).prop( 'checked', true );
		//btn.prop( 'checked', true );
		//tgt.val( 'checked' );
	});

	$( '#_wsform_form_output_slug' ).change( function() {
		var tgt = $( '#input_wsform_output_format_email_response' );
		if( $( this ).val() != '' ) tgt.val( 'checked' ).parents( 'label' ).children( '.wsform-output-choose' ).prop( 'checked', true );
		else tgt.val( 'not checked' ).parents( 'label' ).children( '.wsform-output-choose' ).prop( 'checked', false );
	});

	// FORMS
	// Required Elements warning box
	$( '#wsform-output-chooser-hide' ).on( 'click', function() {
		var chsr = $( this );
		var wb = $( '#wsform-name-email-required-note' );
		if( chsr.is( ':checked' ) ) wb.removeClass( 'hidden' );
		else wb.addClass( 'hidden' );
	});

	// Add form element
	$( '.wsform-element-add' ).click( function( e ) {

		e.preventDefault();
        arm_popup();
		
		let el = $( this );
		let type = el.attr( 'data-element-type' );
		let form_id = el.parent().attr( 'data-wsform-form-id' );

		// Close the open items
		$( '.wsform-sg-item.open' ).removeClass( 'open' );

		// Create a unique identifier
		let ct = $( '.wsform-sg-item' ).length;
        let seq = '__f' + form_id + '_e' + ( Number( ct ) + 1 ) + '__';
        let id = seq + type;

        // Save the attributes
        let atts = {
            c : 'wsf', 
            m : 'render_form_element_ajax', 
            i : form_id,
            t : type,
            e : id,
            s : seq
        };

        // console.log( atts );

        // Send ajax request
        wsform_admin_ajax( atts ).done( function( res ) {

            let result = JSON.parse( res );
            // console.log( result );

            remove_popup();

            if( result.error == false )
            {
                $( '#wsform-elements' ).append( result.output );
                wsform_update_sg_input();
            }
            else
            {
                display_error( result.error_msg );
            }


        }).fail( display_error );
	});


	// Duplicate form element
	$( document ).on( 'click', '.wsform-dup-item', function() {
		//var ct = $( '.wsform-sg-item' ).length;
		var item = $( this ).closest( 'li.wsform-sg-item' );
		//var container = $( this ).closest( '.wsform-sortable-group' );
		//var form_id = container.attr( 'data-wsform-form-id' );
		var wsform_id = item.find( '.wsform-id' );


		var cloned_item = item.clone();
		var cloned_item_label = item.find( '.wsform-label' ).val() + ' copy';
		var cloned_item_id = wsform_id.val() + '_copy';
		cloned_item.find( '.wsform-sg-input-label' ).html( cloned_item_label );
		cloned_item.find( '.wsform-label' ).val( cloned_item_label );
		cloned_item.find( '.wsform-id' ).val( cloned_item_id );

		item.removeClass( 'open' ).after( cloned_item );
	});


	// Open and close sortable items
	$( document ).on( 'click', '.ico-wsform-sg-toggle-item', function() {
		var item = $( this ).closest( 'li.wsform-sg-item' );
		item.toggleClass( 'open' );
		item.siblings().removeClass( 'open' );
	});

	// Color palettes
	$( '.wsform-color-entry' ).spectrum({
		showAlpha: true,
		showPalette: true,
		showInput: true,
		preferredFormat: "rgb",
		palette: [
			['black', 'white', 'blanchedalmond'],
			['rgb(255, 128, 0);', 'hsv 100 70 50', 'lightyellow']
		]
	});


    // // Download doc
    // $( document ).on( 'click', '.wsform-msg-ctl.download-message', function() {
    //     wsform_generate_pdf();
    // });


	// // display color preview (deprecated)
	// $('.color-entry').on('change', function() {

	// 	var input = $(this);
	// 	var color = input.val();

	// 	if( color.length > 0 && color.charAt( 0 ) != '#' )
	// 	{
	// 		color = '#' + color;
	// 		input.val( color );
	// 	}

	// 	var preview = input.parents().children('.color-preview');
	// 	preview.attr( 'style', 'background-color: ' + color );

	// });

	// choose between form types
	$( '.wsform-form-type-choose' ).click( function() {

		var tgt = $( this ).attr( 'rel' );

		if( tgt == 'custom' )
		{
			$( '#wf-form-custom' ).addClass( 'active' );
			$( '#wf-form-styles, #wf-form-builder' ).removeClass( 'active' );
		}
		else
		{
			$( '#wf-form-custom' ).removeClass( 'active' );
			$( '#wf-form-styles, #wf-form-builder' ).addClass( 'active' );
		}


	});

	// generate random encryption key
	$( '.wsform-generate-key' ).click( function(e) {
		e.preventDefault();
		var str = '';
		for(a = 0 ; a < 2 ; a++ ) str += Math.random().toString(36).replace(/[^a-z0-9]+/g, '');
		$( '#wsform-ssl-key' ).val( str );
	});

	// update checkbox value
	$( document ).on( 'click', '.wsform-sg-checkbox', function() {
		var cb = $( this );
		if( cb.prop( 'checked' ) == true ) cb.val( 'checked' );
		else cb.val( 'not checked' ).prop( 'checked', false );
	});


	jQuery.fn.outerHtml = function( r ) { return (this[0]) ? this[0].outerHTML : ''; };


	function wsform_update_sg_input()
	{
		var items_arr = [];

		$( '#wsform-elements' ).children( '.wsform-sg-item' ).each( function() {

			var item = $( this );
			var inputs_obj = {};
			var valu = '';

			item.find( '.wsform-data-input' ).each( function() {
				var data = $( this );
				var key = data.attr( 'data-wsform-key' );
				if( data.val() != '' )  valu = data.val();
				else valu = data.attr( 'data-wsform-value' );
				inputs_obj[key] = valu;
			});

			items_arr.push( inputs_obj );

		});

		var items_json = JSON.stringify( items_arr );

		// Base 64 encode it
		var items_base64 = btoa( items_json );

		$( '#wsform-sg-input' ).val( items_base64 );
	}


    function wsform_check_for_duplicate_ids()
    {
		const items_arr = [];
        const matches = [];
        // const match = false;

		$( '#wsform-elements' ).children( '.wsform-sg-item' ).each( function() {
            
            let el = $( this );
            let tval = el.find( 'input.wsform-data-input.wsform-id' ).val();

            if( items_arr.includes( tval ) )
            {
                matches.push( tval );
                el.addClass( 'error duplicate-id' );
            }
            else
            {
                items_arr.push( tval );
                el.removeClass( 'error duplicate-id' );
            }

            items_arr.push( tval );

		});

        if( matches.length > 0 )
        {
            alert( "You have more than one element with IDs that are similar. Please ensure that all IDs are unique." );
        }
    }


    function wsform_admin_ajax( a = {} )
    {
		var data = { 
            action : 'wsform_action_obj', 
            atts : a, 
            // _ajax_nonce : 'asdfasdf'
            _ajax_nonce : wsform_ajax_admin.nonce 
        };

        return $.ajax({ 
            url: ajaxurl, 
            type: 'POST', 
            data: data 
        });
    }




    

    function mark_msg_read( res )
    {
    }





    function update_messages_count()
    {
        setTimeout( function() {
            let stat = $( '#wsform-messages-list-area' ).attr( 'data-post-status' );
            let messages_unread = $( 'li.wsform-message-list-item.message-unread' ).length;
            let text = $( 'li.wsform-message-list-item' ).length;

            if( text == 1 ) text += ' response';
            else text += ' responses';
    
            if( stat == 'publish' )
            {
                if( messages_unread > 0 ) text += ', ' + messages_unread + ' new';
                $( '#wsform-messages-count' ).html( text );
            }
            else
            {
                $( '#wsform-messages-count' ).html( text + ' in trash' );
            }
        }, 100);
    }





	// function wsform_add_form_element( form_id, type )
	// {
	// 	// Close the open items
	// 	$( '.wsform-sg-item.open' ).removeClass( 'open' );

	// 	// Create a unique identifier
	// 	var ct = $( '.wsform-sg-item' ).length;
    //     var pf = '__f' + form_id + '_e' + ( Number( ct ) + 1 ) + '__';
	// 	// var id = 'form_' + form_id + '_element_' + ( Number( ct ) + 1 );
    //     var id = pf + type;

	// 	var new_item = wsform_input_types[type];

	// 	new_item = new_item.replace( 'new_element', id )
    //     .replace( 'wsform-sg-item', 'wsform-sg-item open' )
    //     .replace( 'data-wsform-ident=""', 'data-wsform-ident="' + pf + '"' );

	// 	$( '#wsform-elements' ).append( new_item );

	// 	// ctl.val( 'add_element' );

	// 	wsform_update_sg_input();
	// }


	//function wsform_update_checkboxes( item )
	//{
	//	var checks_arr = [];
	//
	//	// make sure we select the right group of boxes
	//	var boxes_par = item.closest( '.wsform-checkbox-div' );
	//	var boxes = boxes_par.find( '.wsform-checkbox' );
	//	var boxes_input = boxes_par.find( '.wsform-hidden-input' );
	//
	//	// get the ones that are checked
	//	boxes.each( function() {
	//
	//		if( $( this ).is( ':checked' ) )
	//		{
	//			checks_arr.push( $( this ).val() );
	//		}
	//
	//	});
	//
	//	var checks_json = JSON.stringify( checks_arr );
	//
	//	boxes_input.val( checks_json );
	//}


});



    // function fancy_confirm( text, header = 'Alert' )
    // {
    //     let popup = '<div id="wsform-popup" class="wsform-popup-wrapper">'
    //               + '<div id="wsform-popup-bg">'
    //               + '<div id="wsform-popup-dialog-box"><h1>' + header + '</h1>'
    //               + '<p>' + text + '</p>'
    //               + '<div class="ws-forms-popup-buttons">'
    //               + '<span id="ok-this" class="ws-form-dialog-button button-primary">OK</span> '
    //               + '<span id="cancel-this" class="ws-form-dialog-button button">Cancel</span>'
    //               + '</div></div></div></div>';
    //     $( '.wrap' ).prepend( popup );

    //     $( '#ok-this' ).click( function( e ) {
    //         e.preventDefault();
    //         return true;
    //     });

    //     $( '#cancel-this' ).click( function( e ) {
    //         alert('hello')
    //         e.preventDefault();
    //         return false;
    //     });
    // }



