$(document).ready(function(){
  start_chosen('.chosen-select');
  $('.chosen-select').on('change', function(evt, params) {
    s = $(this).find("option:selected");
    if(s.text().indexOf("MORE") >= 0 && s.text().indexOf("...") >= 0) {
      o = s.attr('onclick').replace('return false;', '');
      if(o.indexOf('jselmore(') >= 0) {
        eval(o);
      }
    }
    i = $(this).attr('id');
    if((typeof i !== 'undefined' && i !== false) && (i.indexOf("uf_") == 0)) {
      $(this).closest('tr').find('.chkchg').val('y');
    }
  });
});

function start_chosen(t) {
  $(t).each(function() {
    ph = $(this).attr('placeholder') ? $(this).attr('placeholder') : "Click to select options" ;
    pd = parseInt($(this).css('padding-left')) + parseInt($(this).css('padding-right')) + parseInt($(this).css('border-left-width')) + parseInt($(this).css('border-right-width')) ;
    wd = $(this).attr('data-width') ? $(this).attr('data-width') : parseInt($(this).width()) + pd ;
    $(this).chosen("destroy");
    $(this).chosen({
      disable_search_threshold: 10,
      placeholder_text_multiple: ph,
      width: wd
    });
  });
}

function change_chosen(fname,file) {
  $('#' + fname).val(file);
  $('#' + fname).trigger("chosen:updated");
}