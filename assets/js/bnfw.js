jQuery(document).ready(function($) {
    function toggle_fields() {
    	var show_fields = $('#show-fields').is(":checked");

    	if ( show_fields ) {
			$('#email, #cc, #bcc').show();
    	} else {
			$('#email, #cc, #bcc').hide();
    	}
    }

    $(".select2").select2();
	toggle_fields();

    if ( 'user-password' === $('#notification').val() || 'new-user' === $('#notification').val() || 'welcome-email' === $('#notification').val() || 'reply-comment' === $('#notification').val() ) {
		$('#toggle-fields, #email, #cc, #bcc, #users, #email-formatting, #current-user').hide();
		$('#user-password-msg').show();
    } else {
		$('#toggle-fields, #users, #email-formatting, #current-user').show();
		toggle_fields();
		$('#user-password-msg').hide();
    }

    $('#notification').on('change', function() {
		var $this = $(this);
		if ( 'user-password' === $this.val() || 'new-user' === $this.val() || 'welcome-email' === $this.val() || 'reply-comment' === $this.val() ) {
			$('#toggle-fields, #email, #cc, #bcc, #users, #email-formatting, #current-user').hide();
			$('#user-password-msg').show();
		} else {
			$('#toggle-fields, #users, #email-formatting, #current-user').show();
			$('#user-password-msg').hide();
			toggle_fields();
		}
    });

    $('#show-fields').change(function() {
    	toggle_fields();
    });

	// send test email
	$( '#test-email' ).click(function() {
		$( '#send-test-email' ).val( 'true' );
	});

	$( '#shortcode-help' ).on( 'click', function() {
		var notification = $( '#notification' ).val();

		$(this).attr( 'href', 'http://www.voltronik.co.uk/wordpress-plugins/better-notifications-for-wordpress-shortcodes/?notification=' + notification );
	});
});
