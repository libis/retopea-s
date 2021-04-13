(function($) {
    function fixIframeAspect() {
        $('iframe').each(function () {
            var aspect = $(this).attr('height') / $(this).attr('width');
            $(this).height($(this).width() * aspect);
        });
    }

    function framerateCallback(callback) {
        var waiting = false;
        callback = callback.bind(this);
        return function () {
            if (!waiting) {
                waiting = true;
                window.requestAnimationFrame(function () {
                    callback();
                    waiting = false;
                });
            }
        }
    }



    $(document).ready(function() {
        $('.nav-menu').addClass('closed');

        $('.menu-btn').click(function() {
             $('.nav-menu').toggleClass('open').toggleClass('closed');
             //console.log($('.menu-btn').text());
             if($('.menu-btn').text() == '\u2630'){
               $('.menu-btn').text('\u2715');
             }else{
               $('.menu-btn').text('\u2630');
             }
        });

        $('#lang-switcher').find('.ui-dropdown-list-trigger').each(function() {
        	$(this).click(function(){
            alert('stop');
      			$(this).parent().toggleClass('active');
      		});

        });

        // Maintain iframe aspect ratios
        $(window).on('load resize', framerateCallback(fixIframeAspect));
        fixIframeAspect();
    });
})(jQuery);
