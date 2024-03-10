$(function(){
  $('.scroll-pane').each(
    function(){
      $(this).jScrollPane({
        verticalDragMinHeight: 60,
		    verticalDragMaxHeight: 60,
        mouseWheelSpeed: 100,
        showArrows: false
      });
      var api = $(this).data('jsp');
      var throttleTimeout;
      $(window).bind('resize',
        function(){
          if (!throttleTimeout) {
            throttleTimeout = setTimeout(
              function(){
                api.reinitialise();
                throttleTimeout = null;
              },200
            );
          }
        }
      );
    }
  )
});
