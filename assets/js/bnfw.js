jQuery(document).ready(function($) {
    $(".select2").select2();

    $("#bnfw_user_role_toggle, #bnfw_user_toggle").click(function() {
        $("#bnfw_user_role_container").toggle();
        $("#bnfw_user_container").toggle();
    });
});
