<?php 

function wpdocs_save_post_shortcut() {
    ?>
<script type="text/javascript">
jQuery(document).ready(function($) {
    $(window).keydown(function(event) {
        if (event.ctrlKey || event
            .metaKey) { // This line of code will check if either Ctrl (Windows) or Command (Mac) button is pressed.
            switch (String.fromCharCode(event.which).toLowerCase()) {
                case 's':
                    event.preventDefault();
                    $('#publish')
                .click(); // This is the default ID for the WordPress publish button. Adjust if necessary.
                    break;
            }
        }
    });
});
</script>
<?php
}
add_action( 'admin_print_footer_scripts', 'wpdocs_save_post_shortcut' );