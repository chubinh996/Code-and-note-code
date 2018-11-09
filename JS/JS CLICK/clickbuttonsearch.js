
require([
  'jquery'
  ], function(jQuery){
    (function($) {
      $('.action-search').click(function(){
        $(this).parents('.block-search').addClass('active');
      })
      jQuery(document).mouseup(function(e) {
        var popup = jQuery(".block-search");
        if (!popup.is(e.target) && popup.has(e.target).length == 0) {
          jQuery(".block-search").removeClass('active');
        }
      });
    })(jQuery);
  });