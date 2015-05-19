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

    if ( 'user-password' == $('#notification').val() || 'new-user' == $('#notification').val() || 'welcome-email' == $('#notification') ) {
		$('#toggle-fields, #email, #cc, #bcc, #users').hide();
		$('#user-password-msg').show();
    } else {
		$('#toggle-fields, #users').show();
		toggle_fields();
		$('#user-password-msg').hide();
    }

    $('#notification').on('change', function() {
		var $this = $(this);
		if ( 'user-password' === $this.val() || 'new-user' === $this.val() || 'welcome-email' == $this.val() ) {
			$('#toggle-fields, #email, #cc, #bcc, #users').hide();
			$('#user-password-msg').show();
		} else {
			$('#toggle-fields, #users').show();
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
});
