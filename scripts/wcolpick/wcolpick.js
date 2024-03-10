function showpicker(f) {
  fid = '#'+f;
  pid = '#'+f+'_picker';
  $.each($('.color_picker'),function(){
    // hide others
    if($(this).attr('id') != f+'_picker') {
      $(this).hides();
    }
  });
  if($(pid).length) {
    // exists
    if($(pid).children('.wcolpick').is(':visible')) {
      // hide
      $(pid).hides();
    }
    else {
      // show
      $(pid).shows();
    }
  }
  else {
    // create
    $(fid).next('a').after('<div class="color_picker" id="'+f+'_picker"></div>');
    $(pid).loads({
      layout: 'rgbhex',
      enableSubmit: true,
      enableAlpha: false,
      colorSelOutline:false,
      colorOutline:false,
      compactLayout:true,
      variant:'small',
      color:$(fid).attr('value'),
      onSubmit: function(ev) {
        $(fid).val('#'+ev.hex.toUpperCase());
        $(fid).next('a').css('background-color','#'+ev.hex.toUpperCase());
        $(ev.el).hides();
      },
    });
  }
  return false;
}

var pattern = new RegExp("^#([a-fA-F0-9]){3}$|[a-fA-F0-9]{6}$");

function changepicker(f) {
  fid = '#'+f;
  pid = '#'+f+'_picker';
  v = $(fid).val().replace('#','').toUpperCase() ;
  if(v) {
    $(fid).val('#' + v);
  }
  if(!pattern.test(v)) {
    v = 'FFFFFF';
  }
  $(fid).next('a').css('background-color','#' + v);
  if($(pid).length) {
    $(pid).setColor(v,true);
  }
}