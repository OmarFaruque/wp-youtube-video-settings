jQuery(document).ready(function($){

    // Settings form submit
    jQuery(document.body).on('change', 'select#alleventpage', function(e){
        jQuery(this).closest('form').submit();
    });

}); // End Document ready