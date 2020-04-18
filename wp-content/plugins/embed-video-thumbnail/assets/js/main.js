(function($) {
    var IKANAWEB_EVT = {
        init: function () {
            $(document).on('click','.ikn-evt-container',
                IKANAWEB_EVT.displayEmbed
            );
        },
        displayEmbed: function () {
            var iframe = document.createElement("iframe"),
                parentElement = $(this).parent(),
                embed = parentElement.data('embed-url');
            iframe.setAttribute("src", embed);
            iframe.setAttribute("frameborder", "0");
            iframe.setAttribute("allowfullscreen", "1");
            parentElement.html(iframe);
            parentElement.find('iframe').trigger('click');
        }
    };
    $(function(){
        IKANAWEB_EVT.init();
    });
})(jQuery);