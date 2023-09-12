	jQuery( document ).ready(function( $ ) {

		// Style the "select" elements
		$( '.wsform-select-container' ).each( function() {

			var con = $( this );
			var select = con.children( 'select' );
			var opts = select.children( 'option' );
			var opts_str = '';
			var id = select.attr( 'data-field-id' );
			var div = con.closest( '.wsform-input-div' );
			//var label = select.attr( 'data-field-label' );
			var name = select.attr( 'name' );
			var def = select.attr( 'value' );

			var vals = [];
			var opt = '';
            var val = '';

			var req = select.hasClass( 'wsform-required-input' ) ? 1 : 0;
			var conclass = req == 1 ? 'wsform-select-field wsform-input wsform-required-input' : 'wsform-select-field wsform-input';

			opts.each( function() {
				html = $( this ).html();

				if( html != 'Select...' )
				{
					val = $( this ).val();
					opt = '<span class="wsform-select-option" data-wsform-select-value="' + val + '" data-wsform-select-label="wf_' + id + '" data-wsform-select-input="wf_hidden_input_' + id + '">' + html + '</span>';
					vals.push( opt );
					opts_str += opt;
				}

			});

			// con.append( '<input id="wf_' + id + '" class="' + conclass + '" type="text" data-field-desc="select" name="' + name + '" placeholder="Select..." value="' + def + '" readonly="readonly" />' )
			   //.append( '<input id="wf_hidden_input_' + id + '" class="wsform-select-hidden-input" name="' + name + '" type="hidden" data-field-desc="select-hidden" value="' + def + '" />' )
			con.append( '<div class="wsform-select-div-wrapper" id="wf_select_div_wf_' + id + '"><div class="wsform-select-div">' + opts_str + '</div></div>' );

			div.data( 'vals', vals );

			select.addClass( 'hidden' );

		});


        // Data validation
		$( '.wsform-input' ).on( 'focus', function() {

			var input = $( this );
			//var el = input.closest( '.wsform-input-div' );

			if( ! input.hasClass( 'wsform-select-field' ) )
			{
				var div = input.closest( '.wsform-input-div' );

				if( div.is( '.select, .file' ) ) return;

				div.addClass( 'occupado' );

				input.blur( function()
				{
					// alert( input.val() );
					// if( input.val() == '' ) div.removeClass( 'occupado' );
					if( input.hasClass( 'wsform-required-input' ) ) validate_data( div );
				});
			}

		});


		$( '.wsform-select-field' ).focus( function() {

			var field = $( this );
			var div = field.closest( '.wsform-input-div' );
			var tgt = div.find( '.wsform-select-div-wrapper' );
			// var v = field.val();
			div.addClass( 'occupado' );
			tgt.addClass( 'display' );

			// field.click( function() {
			// 	if( field.is( 'focus' ) ) field.blur();
			// });

			$( '.wsform-select-option' ).click( function() {
				var opt = $( this );
				// var optval = opt.html();
                var optval = opt.attr( 'data-wsform-select-value' );
				opt.closest( '.wsform-line' ).find( '.wsform-input' ).val( optval );
				tgt.removeClass( 'display' );
				validate_data( div );
			});

			field.blur( function() {
				setTimeout( function() {
					tgt.removeClass( 'display' );
					if( field.val() == '' ) div.removeClass( 'occupado' );
					// alert(field.val())
				},200);
			});

		});












		$( '.wsform-button-input.wsform-required-input' ).click( function() {
			var el = $( this ).closest( '.wsform-input-div' );
			validate_data( el );
		});


		$( '.wsform-form' ).submit(function( e ) {

			e.preventDefault();
            var form = $( this );

            var req_inpt = form.find( '.required-input-field' );
            var res = 0;
			var success_msg = form.attr( 'data-success-msg' );
            var err_div = $( '#wsform-error-msg' );

            req_inpt.each( function() {
                let r = validate_data( $( this ).find( '.wsform-input-div' ) );
                if( r == false ) res++;
            });

            setTimeout( function() {
                
                if( res > 0 )
                {
                    err_div.removeClass( 'hidden' );
                    $( 'html,body' ).animate({scrollTop: err_div.offset().top - 100 }, 400);
                }
                else
                {
                    arm_popup( 'Sending ...' );

                    if( ! err_div.hasClass( 'hidden' ) ) err_div.addClass( 'hidden' );

                    // Save the attributes
                    let atts = {
                        c : 'wsfs', 
                        m : 'form_submit',
                        d : form.serializeArray()
                    };
            
                    // Send ajax
                    wsform_ajax( atts ).done( function( res ) {

                        let result = JSON.parse( res );
                        console.log( result );

                        if( result.error == true )
                        {
                            fancy_alert( result.error_msg );
                        }
                        else
                        {
                            fancy_alert( success_msg );
                        }

                    }).fail( function() {
                        fancy_alert( 'Sorry, there was a problem sending the form. Please try reloading the page.' );
                    });

                }
            },100);
		});


        $( document ).on( 'click', '.close-dialog', function() {
            remove_popup();
        });


        function wsform_ajax( a = {} )
        {
            var data = { 
                action : 'wsform_action_obj', 
                atts : a, 
                // _ajax_nonce : 'asdfasdf'
                _ajax_nonce : wsform_ajax_pub.nonce 
            };
            return $.ajax({ 
                url: wsform_ajax_pub.ajax_url, 
                type: 'POST', 
                data: data 
            });
        }
    
    
    
        // function validate_form( form )
		// {

		// }



		// function send_form_data( form )
		// {
        //     var form_data = form.serializeArray();
        //     console.log(form_data)
        //     return;







        //     // alert('hi')
		// 	// var note = $( '#wsform-status-msg' );
		// 	var btn = $( '.wsform-form-submit-button' );

		// 	$( '#wsform-status-msg' ).addClass( 'hidden' ).html( '' );
		// 	btn.addClass( 'wsform-button-busy' ).val( 'Sending ...' );

        //     var form_data = new FormData( form[0] );

		// 	// Important line; will return 400 error otherwise
        //     form_data.append( 'action', 'wsform_action' );

		// 	// Tells the function which callback to use
		// 	form_data.append( 'func', 'wsform_form_submit' );
            
        //     $.ajax({
        //         url: wsform_ajax.ajax_url,
        //         type: 'POST',
        //         contentType: false,
        //         processData: false,
        //         data: form_data
		// 	}).done( function( res ) {
		// 		$( '#wsform-forms-test-return' ).html( res );
		// 		btn.removeClass( 'wsform-button-busy' ).val( 'Submit' );
		// 		if( parseInt( res ) == 1 ) note.addClass( 'wsform-success' ).html( 'Your message has been sent!' );
		// 		else note.addClass( 'wsform-error' ).html( 'Sorry, there was an error sending the email. Please contact us.' );
		// 	}).fail( function( res ) {
		// 		$( '#wsform-forms-test-return' ).html( res );
		// 		btn.removeClass( 'wsform-button-busy' ).val( 'Submit' );
		// 		note.addClass( 'mpsd-form-error' ).html( 'Sorry, there was a problem with the email form. Please contact us.' );
		// 	});
		// }


		function validate_data( div )
		{
			var input = div.find( '.wsform-input, .wsform-button-input' );
			var con = div.closest( '.wsform-line' );

			if( ! input.hasClass( 'wsform-required-input' ) ) return true;

			var value = input.val();
			var type = div.attr( 'data-field-desc' );
			var err_con = con.find( '.err-msg' );
			var res = '';
			var msg = '';

			var pattern = {};
			pattern.date = /^\d{4}\-\d{2}\-\d{2}$/;
			pattern.email = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
			pattern.minlength1 = /^[\s\S]+$/;
			pattern.minlength2 = /^[\s\S]{2,}$/;
			pattern.minlength5 = /^[\s\S]{5,}$/;
			pattern.number = /^\d+$/;
			pattern.tel = /^[0-9\s-\(\)]{7,}$/;
			pattern.time = /^\d{1,2}:*\d{2}\s*([AM|PM|am|pm]{2})*$/;
			pattern.url = /(http(s)?:\/\/.)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&=]*)/;

			switch( type )
			{
				case 'number' :
					res = pattern.number.test( value );
					msg = 'Please enter a number';
					break;

				case 'date' :
					res = pattern.date.test( value );
					msg = 'Please enter a valid date (e.g. mm/dd/yyyy)';
					break;

				case 'your-name' :
					res = pattern.minlength2.test( value );
					msg = 'Please enter your name';
					break;

				case 'your-email' :
					res = pattern.email.test( value );
					msg = 'Please enter your email address (e.g. you@domain.com)';
					break;

				case 'email' :
					res = pattern.email.test( value );
					msg = 'Please enter a valid email address (e.g. something@domain.com)';
					break;

				case 'file' :
					var count = div.find( '.wsform-file-attachment' ).length;
					res = count > 0 ? true : false;
					msg = 'Please select file(s)';
					break;

				case 'radio' :
				case 'checkbox' :
                    var ct = div.find( '.wsform-button-input:checked' ).length;
                    // alert(ct)
					res = ct > 0 ? true : false;
					msg = type == 'radio' ? 'Please select an item' : 'Please check at least one item';
					break;

				case 'select' :
                    // alert('hi')
					res = pattern.minlength1.test( value );
					msg = 'Please select a value';
					break;

				case 'tel' :
					res = pattern.tel.test( value );
					msg = 'Please enter a valid phone number';
					break;

				case 'time' :
					res = pattern.time.test( value );
					msg = 'Please enter a valid time (e.g. 12:45 pm, 15:30, 0600)';
					break;

				case 'textarea' :
					res = pattern.minlength1.test( value );
					msg = 'Text length too short';
					break;

				case 'url' :
					res = pattern.url.test( value );
					msg = 'Please enter a URL (e.g. domain.com)';
					break;

				default :
					res = pattern.minlength1.test( value );
					msg = 'Please enter a value';
					break;
			}

			if( res === true )
			{
				con.removeClass( 'not-passed has-error' ).addClass( 'passed' );
				err_con.remove();
                return true;
			}
			else
			{
				con.removeClass( 'passed' ).addClass( 'not-passed has-error' );
				if( err_con.length < 1 ) div.append( '<span class="err-msg">' + msg + '</span>' );
				else err_con.html( msg );
                return false;
			}
		}


		function simplify_bytes( bytes )
		{
			if( bytes > 1000000000 ) return ( bytes / 1000000000 ).toFixed( 1 ) + ' GB';
			else if( bytes > 1000000 ) return ( bytes / 1000000 ).toFixed( 1 ) + ' MB';
			else if( bytes > 1000 ) return ( bytes / 1000 ).toFixed( 1 ) + ' KB';
			else return bytes + ' bytes';
		}


        function arm_popup( t = 'Loading ...' )
        {
            let popup = $( '#wsform-popup' );
            let form_body = $( '#wsform-body' );

            let phtml = '<div id="wsform-popup-bg">'
                      + '<div id="wsform-popup-dialog-box"><p>' + t + '</p></div>'
                      + '</div>';

            if( popup.length > 0 )
            {
                popup.html( phtml );
            }
            else
            {
                form_body.prepend( '<div id="wsform-popup" class="wsform-popup-wrapper">' + phtml + '</div>' );
            }
        }



        function fancy_alert( text, btxt = 'OK' )
        {
            let popup = $( '#wsform-popup' );
            let form_body = $( '#wsform-body' );

            let phtml = '<div id="wsform-popup-bg">'
                      + '<div id="wsform-popup-dialog-box"><p>' + text + '</p>'
                      + '<span class="wsform-submit-span"><button class="wsform-button small close-dialog anim align-center">' 
                      + btxt + '</button></span>'
                      + '</div></div>';

            if( popup.length > 0 )
            {
                popup.html( phtml );
            }
            else
            {
                form_body.prepend( '<div id="wsform-popup" class="wsform-popup-wrapper">' + phtml + '</div>' );
            }
        }


    
    
        function remove_popup()
        {
            $( '#wsform-popup' ).remove();
        }
    

        // FILE UPLOADS


		$( '.wsform-file-input-button' ).change( function( e ) {

			e.preventDefault();

			var btn = $( this );
			var form = btn.closest( '.wsform-form' );
			var files = e.target.files;
			var list = '';
			var iname = btn.attr( 'data-wsform-input-name' );

			// console.log(files);

			for( c = 0 ; c < files.length ; c++ )
			{
				var filename = files[c].name;
				var l = files[c].name.length;
				var filesize = simplify_bytes( files[c].size );
				var filetype = files[c].type;

				if( filetype.length == 0 ) filetype = 'file/unknown';

				if( l > 40 ) filename = files[c].name.substring( 0, 10 ) + '...' + files[c].name.substring( ( l - 10 ), l );

				list += '<li class="wsform-file-upload-item wsform-uploading" rel="' + filename + '"><span class="wsform-upload-filename"><strong>' + filename + '</strong></span> &ndash; <span class="wsform-upload-meta"><em>' + filetype + ', ' + filesize + '</em></span><span class="wsform-remove-upload wsform-hide"></li>';

			}

			btn.closest( 'div' ).find( '.wsform-file-input-list' ).append( list );

			// upload_files( form, iname );

            var form_data = new FormData( form[0] );

			// Important line; will return 400 error otherwise
            form_data.append( 'action', 'wsform_action' );

			// Tells the function which callback to use
			form_data.append( 'func', 'wsform_files_upload' );

			// Tells the function which file input to use
			form_data.append( 'wf_field_name', iname );

            $.ajax({
                url: wsform_ajax_pub.ajax_url,
                type: 'POST',
                contentType: false,
                processData: false,
                data: form_data
			}).done( function( res ) {

                // $( '#wsform-forms-test-return' ).html( res );

    			var results = JSON.parse( res );
                var input = form.find( 'input[data-wsform-input-name="' + name + '"]' );
                var div = input.closest( 'div.wsform-file-input-area' );
                var con = div.closest( '.wsform-input-div' );
                var err_list = div.find( '.wsform-file-input-errors' );

    			console.log( results );
    
                for( c = 0 ; c < results.length ; c++ )
                {
                    var ofn = results[c].orig_filename;
                    var tgt = $( 'li[rel="' + ofn + '"]');
                    var path = results[c].filename;
                    var input_name = input.attr( 'data-wsform-input-id' );
                    var error = results[c].error;
                    var err_msg = results[c].err_msg;
    
                    if( error == 0 )
                    {
                        tgt.removeClass( 'wsform-uploading' )
                           .addClass( 'wsform-uploaded' )
                           .attr( 'rel', path );
                        tgt.find( '.wsform-remove-upload' ).removeClass( 'wsform-hide' );
    
                        div.append( '<input type="hidden" class="wsform-file-attachment" name="wf_input[' + input_name + '][files][]" value="' + path + '" />' );
                    }
                    else
                    {
                        tgt.remove();
                        err_list.append( '<li><strong>' + ofn + ': </strong>' + err_msg + '</li>' );
                    }
                }
    
                // Error reporting
                var err_li = err_list.find( 'li' );
    
                setTimeout(function() {
                    err_li.fadeOut( 1500, function() {
                        $( this ).remove();
                    });
                }, 3000);
    
                // Clear file input
                input.replaceWith( input.val( '' ).clone( true ) );
    
                // Validate the input
                validate_data( con );

			}).fail( function( res ) {
				alert( 'Sorry, there was a problem with the form: ' + res );
			});
		});


		$( document ).on( 'click', '.wsform-remove-upload', function() {

			// remove_attachment( $( this ) );
            let item = $( this );

			var tgt = item.closest( 'li' );
			var filename = tgt.attr( 'rel' );
			var div = tgt.closest( '.wsform-input-div' );

			tgt.removeClass( 'wsform-uploaded' ).addClass( 'wsform-deleting' );

			var data = {
				'action': 'wsform_action',
				'func': 'wsform_files_delete',
				'filename': filename
			};

            $.ajax({
                url: wsform_ajax_pub.ajax_url,
                type: 'POST',
                data: data
			}).done( function( res ) {
				if( parseInt( res ) == 1 )
				{
					tgt.fadeOut( 200 ).delay( 200 ).remove();
					$( 'input[value="' + filename + '"]' ).remove();
				}
				else
				{
					alert( 'Sorry, there was a problem' );
					tgt.removeClass( 'wsform-uploading' ).addClass( 'wsform-uploaded' );
				}
				validate_data( div );
				$( '#wsform-forms-test-return' ).html( res );
			}).fail( function( res ) {
				alert( 'Sorry, there was a problem with the form: ' + res );
				$( '#wsform-forms-test-return' ).html( res );
			});
		});





		// function upload_files( form, name )
		// {
		// }



		// function upload_result( form, name, res )
		// {
			// var results = JSON.parse( res );
			// var input = form.find( 'input[data-wsform-input-name="' + name + '"]' );
			// var div = input.closest( 'div.wsform-file-input-area' );
			// var con = div.closest( '.wsform-input-div' );
			// var err_list = div.find( '.wsform-file-input-errors' );

			// console.log( results );

			// for( c = 0 ; c < results.length ; c++ )
			// {
			// 	var ofn = results[c].orig_filename;
			// 	var tgt = $( 'li[rel="' + ofn + '"]');
			// 	var path = results[c].filename;
			// 	var input_name = input.attr( 'data-wsform-input-id' );
			// 	var error = results[c].error;
			// 	var err_msg = results[c].err_msg;

			// 	if( error == 0 )
			// 	{
			// 		tgt.removeClass( 'wsform-uploading' )
			// 		   .addClass( 'wsform-uploaded' )
			// 		   .attr( 'rel', path );
			// 		tgt.find( '.wsform-remove-upload' ).removeClass( 'wsform-hide' );

			// 		div.append( '<input type="hidden" class="wsform-file-attachment" name="wf_input[' + input_name + '][files][]" value="' + path + '" />' );
			// 	}
			// 	else
			// 	{
			// 		tgt.remove();
			// 		err_list.append( '<li><strong>' + ofn + ': </strong>' + err_msg + '</li>' );
			// 	}
			// }

			// // fade_out_and_remove( err_list.find( 'li' ) );

            // var err_li = err_list.find( 'li' );

			// setTimeout(function() {
			// 	err_li.fadeOut( 1500, function() {
			// 		$( this ).remove();
			// 	});
			// }, 3000);

			// // Clear file input
			// input.replaceWith( input.val( '' ).clone( true ) );

			// // Validate the input
			// validate_data( con );
		// }



		// function fade_out_and_remove( item )
		// {
		// 	setTimeout(function() {
		// 		item.fadeOut( 1500, function() {
		// 			$( this ).remove();
		// 		});
		// 	}, 3000);
		// }


	});
