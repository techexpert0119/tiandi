var isMega = $('body').hasClass('meganav') ? true : false ;
var isTrans = $('body').hasClass('menutrans') ? true : false;
var mobWidth = isMega ? 1140 : 800 ;
var miniTop = isMega ? 20 : 150;
var isMobile = false;
var isMini = false;

var cname = 'tiandi-cookie-approval';
var dom = 'e-tiandi.com';
var loc = 'tiandi';

var fs = 0;
var flagsc = '';
var form_flags = [];
var bsk_timer = '';

$(document).ready(function(){
  // ready except images
  mob_check();
  // ticker();

  document.cookie="testcookie";
  // alert(document.cookie.indexOf("testcookie"));
  if((document.cookie.indexOf("testcookie") < 0) && (typeof $('#cookiemessage') !== 'undefined') && $('#cookiemessage').length) {
    $('#cookiemessage').show();
  }

  $("#cookiepclose A").click(function(e) {
    // cookie notice clicked
    cpClose();
    setCookie(cname,'y',365);
    e.preventDefault();
  });

  $("#mob_icon").click(function(e) {
    if($('#menu').css('display') == 'none') {
      mob_open();
    }
    else {
      mob_close();
    }
    e.preventDefault();
  });

  $("#megamobi").click(function(e) {
    if($('#megamobm').css('display') == 'none') {
      megamob_open();
    }
    else {
      megamob_close();
    }
    e.preventDefault();
  });

  $("#bsk_icon a").click(function(e) {
    if($('#bsk_pop').css('display') == 'none') {
      bsk_open();
    }
    else {
      bsk_close();
    }
    e.preventDefault();
  });

  $("#curr_now a").click(function(e) {
    if($('#curr_pop').css('display') == 'none') {
      curr_open();
    }
    else {
      curr_close();
    }
    e.preventDefault();
  });

  $(".menuc").click(function(e) {
    $(".menu0").not($(this).parent().find(".menu0")).css('z-index',999998).slideUp();
    if($(this).parent().find(".menu0").is(':visible')) {
      menu_close($(this));
    }
    else {
      menu_open($(this));
    }
    e.preventDefault();
  });

  $("#partnertab1a").click(function(e) {
    $("#partnerpop").animate({right: 0 }, 500); 
    e.preventDefault();
  });

  $("#partnerpopclose").click(function(e) {
    $("#partnerpop").animate({right: '-240px' }, 500); 
    e.preventDefault();
  });

  $(".langsel1").click(function(e) {
    if($(this).siblings(".langsel2").is(':visible')) {
      $(this).parent().removeClass('langselo');
      $(this).siblings(".langsel2").hide();
    }
    else {
      $(this).parent().addClass('langselo');
      $(this).siblings(".langsel2").show();
    }
    e.preventDefault();
  });

  $("#megamopen").click(function(e) {
    $('#megamopen').hide();
    $('#megamclose').show();
    $('#megamenu').show();
    $('.megamainc').removeClass('meganavclick');
    $('#megamain1').addClass('meganavclick');
    mega_open($('#megadrop1'));
    e.preventDefault();
  });

  $("#megamclose").click(function(e) {
    $('#megamopen').show();
    $('#megamclose').hide();
    $('.megamainc').removeClass('meganavclick');
    $('.hover_all').each(function( index ) {
      $(this).removeClass($(this).attr('data-click'));
    });
    $('#meganav2').css('background-image','none');
    if($('body').hasClass('megamini')) {
      $('BODY').removeClass('megaopen');
    }
    $(".megadrop").slideUp(function(){ 
      $('.megadropr').hide();
      $('#megamenu').hide();
      $('BODY').removeClass('megaopen');
    });
    e.preventDefault();
  });

  $(".megamobmc").click(function(e) {
    $(".megamobm0").not($(this).parent().find(".megamobm0")).slideUp();
    if($(this).parent().find(".megamobm0").is(':visible')) {
      $(this).parent().find(".megamobm0").slideUp();
    }
    else {
      $(this).parent().find(".megamobm0").slideDown();
    }
    e.preventDefault();
  });

  $(".megamainc").click(function(e) {
    t = $('#megadrop' + $(this).attr('id').replace('megamain',''));
    $('.megamainc').removeClass('meganavclick');
    if(! t.is(':visible')) {
      $(this).addClass('meganavclick');
    }
    mega_open(t);
    e.preventDefault();
  });

  $(".megadropn").click(function(e) {
    t = $('#megadropr' + $(this).attr('id').replace('megadropn',''));
    mega_sub(t);
    $('.hover_all').each(function( index ) {
      $(this).removeClass($(this).attr('data-click'));
    });
    $(this).children('span').addClass($(this).children('span').attr('data-click'));
    $('#meganav2').attr('data-back',$(this).children('span').attr('data-image'));
    if($(this).children('span').attr('data-image')) {
      $('#meganav2').css('background-image',"url('" + $(this).children('span').attr('data-image') + "')");
    }
    else {
      $('#meganav2').css('background-image','none');
    }
    e.preventDefault();
  });

  $(".megadropi").hover(function() {
    if($(this).children('span').attr('data-image')) {
      $('#meganav2').css('background-image',"url('" + $(this).children('span').attr('data-image') + "')");
    }
    else {
      $('#meganav2').css('background-image','none');
    }
  },function() {
    if($('#meganav2').attr('data-back')) {
      $('#meganav2').css('background-image',"url('" + $('#meganav2').attr('data-back') + "')");
    }
  }
  );

  $("#cover").click(function(e) {
    menu_close();
    mega_close();
    bsk_close();
    curr_close();
    alert_close();
    exit_close();
  });

  $("#content").click(function(e) {
    menu_close();
    mega_close();
    bsk_close();
    curr_close();
    alert_close();
  });

  $("#img_main").click(function(e) {
    menu_close();
    mega_close();
    bsk_close();
    curr_close();
    alert_close();
  });

  $('.prodpopclose').hover(function(){  
      $(this).find('img').attr('src',$(this).find('img').attr('src').replace('.png','1.png'));    
    },     
    function(){    
      $(this).find('img').attr('src',$(this).find('img').attr('src').replace('1.png','.png'));    
  });

  $('#mob_icon').hover(function(){  
      $('#mob_icon img').attr('src',$('#mob_icon img').attr('src').replace('.png','1.png'));    
    },     
    function(){    
      $('#mob_icon img').attr('src',$('#mob_icon img').attr('src').replace('1.png','.png'));    
  });

  $('#megamobi').hover(function(){  
      $('#megamobi img').attr('src',$('#megamobi img').attr('src').replace('.png','1.png'));    
    },     
    function(){    
      $('#megamobi img').attr('src',$('#megamobi img').attr('src').replace('1.png','.png'));    
  });

  $('#megamopen').hover(function(){  
      $('#megamopen img').attr('src',$('#megamopen img').attr('src').replace('.png','1.png'));    
    },     
    function(){    
      $('#megamopen img').attr('src',$('#megamopen img').attr('src').replace('1.png','.png'));    
  });

  $('#megamclose').hover(function(){  
      $('#megamclose img').attr('src',$('#megamclose img').attr('src').replace('.png','1.png'));    
    },     
    function(){    
      $('#megamclose img').attr('src',$('#megamclose img').attr('src').replace('1.png','.png'));    
  });

  $(".mimage").hover(function() {
    $(this).find('.mcircle').css('background-color',$(this).attr('data-hover'));
  }, function() {
    $(this).find('.mcircle').css('background-color',$(this).attr('data-back'));
  });

  $("A").click(function(e) {
    var href = $(this).attr("href");
    if(href.length > 1) {
      if((p = href.indexOf("#")) != -1) {
        var url = href.substring(0, p);
        var hash = href.substring(p + 1);
        if(hash && ((p == 0) || (url && (window.location.href.indexOf(url) != -1)))) {
          // # is first or url matches page
          anchorTo(hash);
          e.preventDefault();
        }
      }
      else if(href && ((p = href.indexOf("?action=")) > 0) && (a = href.substring(p + 8)) && (typeof $('#'+a) !== 'undefined') && $('#'+a).length) {
        $('.action').hide();
        $('#'+a).show();
        e.preventDefault();
      }
    }
  });

  // remove tag title
  $("A").hover(function() {
    $(this).attr("data-title", $(this).attr('title'));
    $(this).attr('title', '');
  }, function() {
    $(this).attr('title', $(this).attr("data-title"));
  });

  if(window.location.hash) {
    anchorTo(window.location.hash.substring(1));
  }

  $('.slide_set').not('#home_set').mouseenter(function() {
    $(this).find('.slide_next').fadeIn();
    $(this).find('.slide_prev').fadeIn();
    $(this).find('.slide_mag').each(function(){$(this).fadeIn();});
  });

  $('.slide_set').not('#home_set').mouseleave(function() {
    $(this).find('.slide_next').fadeOut();
    $(this).find('.slide_prev').fadeOut();
    $(this).find('.slide_mag').each(function(){$(this).fadeOut();});
  });

  /*
  if(location.search == '?signup') {
    exit_open();
  }

  var exitshown = getCookie('exitshown') == 'y' ? true : false;
  if($('#exitmodal').length && exitshown == false) {
    setTimeout(() => {
      $(document).on("mouseout", evt => {
        if(evt.toElement == null && evt.relatedTarget == null) {
          exitshown = true ;
          setCookie('exitshown','y');
          $(evt.currentTarget).off("mouseout");
          exit_open();
        }
      });
    }, 20000);
  }
  */

  $("#exitclose").click(function(e) {
    exit_close();
    e.preventDefault();
  });

});

$(window).on('load',function(){
  mob_check();
  menu_mini();
  cookiebar();
  dispWin();
  home_slidesize();
});

$(window).resize(function(){
  mob_check();
  menu_mini();
  dispWin();
  home_slidesize();
});

$(window).scroll(function(e) {
  mob_check();
  menu_mini();
  if(! isMobile) {
    menu_close();
  }
});

function prodspec(i) {
  if(i) {
    $('BODY').addClass('pop-open');
    $('#porodpop'+i).show();
  }
  else {
    $('BODY').removeClass('pop-open');
    $('.prodpopouter').hide();
  }
  return false;
}

function news_submit() {
  email = $('#news_email').val().trim();
  $('#news_email').removeClass('form_error');
  if(email && ValidateEmail(email)) { 
    var data = new FormData();
    data.append('email', email);
    data.append('sess',$('#sess').attr('data-sess'));
    data.append('action', 'news');
    $.ajax({
      url: 'jquery.php',
      method: "post",
      processData: false,
      contentType: false,
      data: data,
      success: function (resp) {
        resp = JSON.parse(resp);
        if(resp.res) {
          $('#newsformi').hide();
          $('#newsforms').html($('#newsformd').html());
        }
        else {
          $('#news_email').addClass('form_error');
        }
      }
    });
  }
  else {
    $('#news_email').addClass('form_error');
  }
  return false;
}

function partnerpop_submit() {
  email = $('#partnerpop_email').length ? $('#partnerpop_email').val().trim() : '';
  name = $('#partnerpop_name').length ? $('#partnerpop_name').val() : '';
  company = $('#partnerpop_company').length ? $('#partnerpop_company').val() : '';
  phone = $('#partnerpop_phone').length ? $('#partnerpop_phone').val() : '';
  locaton = $('#partnerpop_location').length ? $('#partnerpop_location').val() : '';
  details = $('#partnerpop_details').length ? $('#partnerpop_details').val() : '';
  mailing = $('#partnerpop_mailing').length && $('#partnerpop_mailing').is(':checked') ? 'yes' : '';
  $('.partnerpopformb').removeClass('form_error');
  err = 0 ;
  if(! (email && ValidateEmail(email))) { $('#partnerpop_email').addClass('form_error'); err++; }
  if(! name) { $('#partnerpop_name').addClass('form_error'); err++; }
  if(! company) { $('#partnerpop_company').addClass('form_error'); err++; }
  if(! err) {
    var data = new FormData();
    data.append('email', email);
    data.append('name', name);
    data.append('company', company);
    data.append('phone', phone);
    data.append('location', locaton);
    data.append('details', details);
    data.append('mailing', mailing);
    data.append('sess',$('#sess').attr('data-sess'));
    data.append('action', 'partner');
    $.ajax({
      url: 'jquery.php',
      method: "post",
      processData: false,
      contentType: false,
      data: data,
      success: function (resp) {
        resp = JSON.parse(resp);
        if(resp.res) {
          $('#partnerpopformf').hide();
          $('#partnerpopformt').show();
        }
        else {
          $('#partnerpop_email').addClass('form_error');
        }
      }
    });
  }
  return false;
}

function partner_submit() {
  email = $('#partner_email').length ? $('#partner_email').val().trim() : '';
  name = $('#partner_name').length ? $('#partner_name').val() : '';
  company = $('#partner_company').length ? $('#partner_company').val() : '';
  phone = $('#partner_phone').length ? $('#partner_phone').val() : '';
  locaton = $('#partner_location').length ? $('#partner_location').val() : '';
  details = $('#partner_details').length ? $('#partner_details').val() : '';
  mailing = $('#partner_mailing').length && $('#partner_mailing').is(':checked') ? 'yes' : '';
  $('.partnerformb').removeClass('form_error');
  err = 0 ;
  if(! (email && ValidateEmail(email))) { $('#partner_email').addClass('form_error'); err++; }
  if(! name) { $('#partner_name').addClass('form_error'); err++; }
  if(! company) { $('#partner_company').addClass('form_error'); err++; }
  if(! phone) { $('#partner_phone').addClass('form_error'); err++; }
  if(! locaton) { $('#partner_location').addClass('form_error'); err++; }
  if(! details) { $('#partner_details').addClass('form_error'); err++; }
  if(! err) {
    var data = new FormData();
    data.append('email', email);
    data.append('name', name);
    data.append('company', company);
    data.append('phone', phone);
    data.append('location', locaton);
    data.append('details', details);
    data.append('mailing', mailing);
    data.append('sess',$('#sess').attr('data-sess'));
    data.append('action', 'partner');
    $.ajax({
      url: 'jquery.php',
      method: "post",
      processData: false,
      contentType: false,
      data: data,
      success: function (resp) {
        resp = JSON.parse(resp);
        if(resp.res) {
          $('#partnerformf').hide();
          $('#partnerformt').show();
          anchorTo('partnerformt');
        }
        else {
          $('#partner_email').addClass('form_error');
        }
      }
    });
  }
  return false;
}

function contact_submit() {
  email = $('#contact_email').length ? $('#contact_email').val().trim() : '';
  name = $('#contact_name').length ? $('#contact_name').val() : '';
  company = $('#contact_company').length ? $('#contact_company').val() : '';
  phone = $('#contact_phone').length ? $('#contact_phone').val() : '';
  locaton = $('#contact_location').length ? $('#contact_location').val() : '';
  details = $('#contact_details').length ? $('#contact_details').val() : '';
  mailing = $('#contact_mailing').length && $('#contact_mailing').is(':checked') ? 'yes' : '';
  $('.contactformb').removeClass('form_error');
  err = 0 ;
  if(! (email && ValidateEmail(email))) { $('#contact_email').addClass('form_error'); err++; }
  if(! name) { $('#contact_name').addClass('form_error'); err++; }
  if(! company) { $('#contact_company').addClass('form_error'); err++; }
  if(! phone) { $('#contact_phone').addClass('form_error'); err++; }
  if(! locaton) { $('#contact_location').addClass('form_error'); err++; }
  if(! details) { $('#contact_details').addClass('form_error'); err++; }
  if(! err) {
    var data = new FormData();
    data.append('email', email);
    data.append('name', name);
    data.append('company', company);
    data.append('phone', phone);
    data.append('location', locaton);
    data.append('details', details);
    data.append('mailing', mailing);
    data.append('sess',$('#sess').attr('data-sess'));
    data.append('action', 'contact');
    $.ajax({
      url: 'jquery.php',
      method: "post",
      processData: false,
      contentType: false,
      data: data,
      success: function (resp) {
        resp = JSON.parse(resp);
        if(resp.res) {
          $('#contactformf').hide();
          $('#contactformt').show();
          anchorTo('contactformt');
        }
        else {
          $('#contact_email').addClass('form_error');
        }
      }
    });
  }
  return false;
}

function home_slidesize() {
  $('#home_slider').show();
  if($('#home_slider').length) {
    $('#home_slider').after('<div id="home_temp" style="visibility:hidden">' + $("#home_slider").html().replace('class="slide_set"','class="slide_temp"').replace('id="home_set""','id="home_temp""') + '</div>');
    mt = 0 ;
    mi = 0 ;
    $('#home_temp').find('.slide_img').each(function(s) {
      $(this).css('display','block');
      t = $(this).find('.slide_imgt').outerHeight();
      i = $(this).find('.slide_imgi').outerHeight();
      if(t > mt) {
        mt = t ;
      }
      if(i > mi) {
        mi = i ;
      }
    });
    $('#home_temp').remove();
    h = 0 ;
    if(mt > 0 && mi > 0) {
      if(winWidth() <= 900) {
        h = mt + mi + 110 ;
      }
      else {
        mt = mt + 100;
        h = (mt > mi ? mt : mi) ;
      }
    }
    if(h) {
      // console.log(mt + '+' + mi + '=' + h);
      $('#home_slider').height(h);
    }
  }
  $('#home_slider').css('visibility','visible');
}

function mob_check() {
  if(winWidth() > mobWidth) {
    // is desktop now
    if(isMobile) {
      // change from mobile to desktop
      if(isMega) {
      }
      else {
        $('#menu').show();
      }
    }
    isMobile = false ;
  }
  else {
    // is mobile now
    if(! isMobile) {
      // change from desktop to mobile
      if(isMega) {
      }
      else {
        $('#menu').hide();
        $('.menu0').hide();
      }
    }
    isMobile = true ;
  }
}

function mega_open(t) {
  bsk_close();
  curr_close();
  alert_close();
  if(t.is(':visible')) {
    if($('body').hasClass('megamini')) {
      $('BODY').removeClass('megaopen');
    }
    t.slideUp(function(){
      $('.megadropr').hide(function() {
        if(! $('body').hasClass('megamini')) {
          $('BODY').removeClass('megaopen');
        }
      });
    });
  }
  else {
    $('BODY').addClass('megaopen');
    t.slideDown(function(){
    });
  }
  $(".megadrop").not(t).slideUp();
}

function mega_close(t) {
  $('BODY').removeClass('megaopen');
  if(t) {
    t.slideUp(function(){$('.megadropr').hide();});
  }
  else {
    $(".megadrop").slideUp(function(){ $('.megadropr').hide();});
  }
}

function mega_sub(t) {
  $(".megadropr").not(t).css('display','none');
  t.css('display','inline-block');
}

function menu_open(t) {
  bsk_close();
  curr_close();
  alert_close();
  exit_close();
  if(isTrans && ! (isMini || isMobile)) {
    $('#cover').show();
  }
  t.parent().find(".menu0").css('z-index',999999).slideDown();
}

function menu_close(t) {
  if(t) {
    t.parent().find(".menu0").slideUp(function(){$('#cover').hide();});
  }
  else {
    $(".menu0").css('z-index',999998).slideUp(function(){$('#cover').hide();});  
  }
}

function menu_mini() {
  var viewportTop = parseInt($(window).scrollTop());
  cls = isMega ? 'megamini' : 'menumini';
  if(! $('BODY').hasClass('menuminfix')) {
    if(viewportTop > miniTop && ! isMini) {
      isMini = true;
      $('BODY').addClass(cls);
    }
    else if(viewportTop == 0 && isMini) {
      isMini = false;
      $('BODY').removeClass(cls);
    }
  }
}

function mob_open() {
  bsk_close();
  curr_close();
  exit_close();
  if(isMobile) {
    $('#menu').slideDown();
    if(isTrans) {
      $('#header1').css('background-color','#000000');
    }
    if($('#bsk_pop').css('display') == 'block') {
      $('#bsk_pop').slideUp();
    }
  }
  return false;
}

function mob_close() {
  if(isMobile) {
    $('#menu').slideUp(function() {
      if(isTrans) {
        $('#header1').css('background-color','transparent');
      }
    });
  }
  return false;
}

function megamob_open() {
  bsk_close();
  curr_close();
  exit_close();
  $('#megamobm').slideDown();
  return false;
}

function megamob_close() {
  $('#megamobm').slideUp();
  return false;
}

function bsk_open() {
  mob_close();
  curr_close();
  alert_close();
  exit_close();
  $('#bsk_pop').slideDown();
  return false;
}

function bsk_close() {
  clearTimeout(bsk_timer);
  $('#bsk_pop').slideUp();
  return false;
}

function alert_open(t) {
  exit_close();
  $('#alert_pop').html(t);
  $('#alert_pop').show();
  $('#cover').show();
  return false;
}

function alert_close() {
  $('#alert_pop').html('');
  $('#alert_pop').hide();
  $('#cover').hide();
  return false;
}

function curr_open() {
  mob_close();
  bsk_close();
  alert_close();
  exit_close();
  $('#curr_pop').slideDown();
  return false;
}

function curr_close() {
  $('#curr_pop').slideUp();
  return false;
}

function curr_set(c) {
  $('#curr_select').val(c);
  $('#curr_form').submit();
  return false;
}

function exit_open() {
  $('#exitmodal').show();
  $('#cover').show();
}

function exit_close() {
  $('#exitmodal').hide();
  $('#cover').hide();
}

function tsnow() {
  return Math.floor(Date.now() / 1000) ;
}

function exit_submit() {
  ok = true ;
  email = $('#exit_email').length ? $('#exit_email').val().trim() : '';
  $('#exit_email').css('background-color','#ffffff');
  $('#exit_name').css('background-color','#ffffff');
  if(! (email && ValidateEmail(email))) {
    $('#exit_email').css('background-color','#FFC0C0');
    ok = false;
  }
  if(! $('#exit_name').val()) {
    $('#exit_name').css('background-color','#FFC0C0');
    ok = false;
  }
  if(ok == true) {
    var data = new FormData();
    data.append('action', 'subscribe_form');
    data.append('email', email);
    data.append('name', $('#exit_name').val());
    data.append('sess',$('#sess').attr('data-sess'));
    $.ajax({
      url: 'jquery.php',
      method: "post",
      processData: false,
      contentType: false,
      data: data,
      dataType: "html",
      success: function (resp) {
        resp = JSON.parse(resp);
        exit_close();
        alert_open('<div id="poptext">' + resp.msg + '</div><div class="button"><a onclick="alert_close();return false;" href="#">CLOSE</a></div>');
      },
      error: function (e) {
        // bad response
        exit_close();
      }
    });
  }
}

function formSend(formid,inputc,filefld,captcha,flagsc,action,errors,thanks,funcdone) {
  // prevent fast re-post
  ts = tsnow(); if(ts - fs < 1) { return ; } fs = ts;
  if(! formid) { return; }

  form_pass = [];
  form_fail = [];
  form_quest = [];
  phone_check = [];
  pass_check = [];
  form_flags = [];
  $formid = $('#' + formid) ;
  $errors = errors ? $formid.find('#' + errors) : '' ;
  $thanks = thanks ? $formid.parent().find('#' + thanks) : '' ; // thanks is outside form
  $inputc = $formid.find('.' + inputc + ':visible') ;
  flagsc = '.' + flagsc ;
  errorm = '';
  fstid = '';
  fstate = '';
  fcountry = '';

  $inputc.each( function (k, v) {
    p = $(this);
    id = v.id;
    name = v.name;
    value = v.value;
    required = p.attr('data-required');
    pass = p.attr('data-pass');
    // alert(id + ' : ' + value + ' : ' + required + ' : ' + pass);

    if(typeof required !== 'undefined' && required !== false && required) {
      if(required == 'email' && ! ValidateEmail(value)) {
        form_fail.push(id);
      }
      else if((required == 'text' || required == 'state' || required == 'country') && ! value) {
        form_fail.push(id);
      }
      else if(required == 'pass') { // must be 2 fields
        pass_check.push({'field':id,'value':value})
      }
      else if(required == 'phone') { // must be 2 fields
        phone_check.push({'field':id,'value':value})
      }
      else {
        form_pass.push(id);
      }
    }
  });


  if(form_fail.length) {
    errorm = errorm + '<p>Please complete the fields highlighted above with <b>&#x2715;</b></p>';
  }

  if(pass_check.length > 1) {
    if(pass_check[0].value.length < 5) {
      form_fail.push(pass_check[0].field);
      form_fail.push(pass_check[1].field);
      errorm = errorm + '<p>Password must be at least 5 characters</p>';
    }
    else {
      form_pass.push(pass_check[0].field);
      form_pass.push(pass_check[1].field);
    }
    if(pass_check[0].value != pass_check[1].value) {
      form_fail.push(pass_check[0].field);
      form_fail.push(pass_check[1].field);
      errorm = errorm + '<p>Password and confirmation do not match</p>';
    }
    else {
      form_pass.push(pass_check[1].field);
    }
  }

  if(phone_check.length > 1) {
    if(phone_check[0].value) {
      form_pass.push(phone_check[0].field);
    }
    else {
      form_quest.push(phone_check[0].field);
    }
    if(phone_check[1].value) {
      form_pass.push(phone_check[1].field);
    }
    else {
      form_quest.push(phone_check[1].field);
    }
    if(! (phone_check[0].value || phone_check[1].value)) {
      errorm = errorm + '<p>At least one phone number is required</p>';
    }
  }

  if(captcha && ! grecaptcha.getResponse($('#'+formid).find('.g-recaptcha').attr('data-widget')).length) {
    errorm = errorm + '<p>reCaptcha validation failed</p>';
  }

  $.each(form_pass, function (k, v) {
    form_flags.push({'field':v,'flag':'tick'});
  });
  $.each(form_fail, function (k, v) {
    form_flags.push({'field':v,'flag':'cross'});
  });
  $.each(form_quest, function (k, v) {
    form_flags.push({'field':v,'flag':'quest'});
  });

  if(errorm) {
    formFlags($formid);
    if($errors) {
      $errors.html(errorm).show();
    }
    return false ;
  }

  if(action) {
    var data = new FormData();
    var form_arr = $formid.serializeArray();

    $.each(form_arr, function (k, v) {
      data.append(v.name, v.value);
    });
    data.append('action', action);
    if(filefld) {
      data.append(filefld, $('#' + filefld)[0].files[0]);
    }
    data.append('sess',$('#sess').attr('data-sess'));

    $.ajax({
      url: 'jquery.php',
      method: "post",
      processData: false,
      contentType: false,
      data: data,
      success: function (resp) {
        // response
        if(resp) {
          resp = JSON.parse(resp);
          if(resp.res) {
            // pass
            if($errors) {
              $errors.html('').hide();
            }
            if($thanks) {
              if(resp.msg) {
                $thanks.html(resp.msg);
              }
              $thanks.show();
            }
            if(funcdone) {
              window [funcdone]();
            }
            else {
              $formid.hide();
            }
            res = resp.res.toString();
            if(res.indexOf("location:") > -1) {
              window.location.replace(res.substring(9));
            }
          }
          else {
            // fail
            $formid.show();
            if($thanks) {
              $thanks.hide();
            }
            if($errors) {
              $errors.html(resp.msg).show();
            }
          }
          form_flags = resp.arr;
          formFlags($formid);
        }
      },
      error: function (e) {
        // bad response
      }
    });
    return false ;
  }
  else {
    return true;
  }
}

function country_state(s,i) {
  v = $('#'+s).val();
  // alert(v);
  if(v == 'US') {
    $('#'+i).show();
    $('#'+i).find('select').attr('data-required','state').val('');
  }
  else {
    $('#'+i).hide();
    $('#'+i).find('select').attr('data-required','').val('');
  }
}

function isValidPostcode(p) { 
  var postcodeRegEx = /[A-Z]{1,2}[0-9]{1,2} ?[0-9][A-Z]{2}/i; 
  return postcodeRegEx.test(p); 
}

function check_fail(err) {
  $formid = $('#check_form');
  $formid.find('.form_input').each( function (k, v) {
    f = $(this).attr('id');
    if(err.includes(f)) {
      form_flags.push({'field':f,'flag':'cross'});
    }
    else {
      form_flags.push({'field':f,'flag':'tick'});
    }
  });
  formFlags($formid);
}

function formFlags($formid) {
  $.each(form_flags, function(i,v) {
    if(v.field) {
      if(v.flag == 'tick') {
        $formid.find('#' + v.field).next(flagsc).html('&#x2713;').css('color','#008000');
      }
      else if(v.flag == 'cross') {
        $formid.find('#' + v.field).next(flagsc).html('&#x2715;').css('color','#CC0000');
      }
      else if(v.flag == 'quest') {
        $formid.find('#' + v.field).next(flagsc).html('*').css('color','#202020');
      }  
    }
  });
}

function edit_done() {
  $('#u_email').html($('#email').val());
  $('#user').val($('#email').val());
  $('#u_forename').html($('#forename').val());
  $('#u_surname').html($('#surname').val());
  address = $('#address1').val();
  if($('#address2').val()) {
    address = address + '<br>' + $('#address2').val();
  }
  address = address + '<br>' + $('#city').val();
  if($('#country').val() == 'US') {
    address = address + '<br>' + $('#state').val() + ' ' + $('#postcode').val() ;
  }
  else {
    address = address + '<br>' + $('#postcode').val() ;
  }
  address = address + '<br>' + $('#country option:selected').text();
  $('#u_address').html(address);
  $('#u_phone').html($('#phone').val());
  $('#u_mobile').html($('#mobile').val());
  $('#u_news').html($('#news').is(':checked') ? 'Yes' : 'No');
  $('#account_view').show();
  $('#account_change').html('CHANGES SAVED');
  $('#account_edit').hide();
}

function pass_done() {
  $('#pass').val('');
  $('#passn1').val('');
  $('#passn2').val('');
  $('#account_view').show();
  $('#account_change').html('CHANGES SAVED');
  $('#account_edit').hide();
}

function basket_action(i_id,o_id) {
  num = $('#q_' + i_id + '_' + o_id).val();
  // alert(i_id + ' - ' + o_id + ' - ' + num);
  var data = new FormData();
  data.append('action', 'basket_action');
  data.append('i_id', i_id);
  data.append('o_id', o_id);
  data.append('num', num);
  data.append('sess',$('#sess').attr('data-sess'));
  $.ajax({
    url: 'jquery.php',
    method: "post",
    processData: false,
    contentType: false,
    data: data,
    dataType: "html",
    success: function (resp) {
      $('#bsk_popi').html(resp);
      bnum = parseInt($('#bsk_num').text()) + parseInt(num);
      $('#bsk_num').text(bnum);
      if(bnum > 0) {
        $('#bsk_num').show();
      }
      else {
        $('#bsk_num').hide();
      }
      bsk_open();
      bsk_timer = setTimeout(bsk_close, 5000);
    },
    error: function (e) {
      // bad response
    }
  });
}

function add_wish(i_id,o_id) {
  var data = new FormData();
  data.append('action', 'wish_add');
  data.append('i_id', i_id);
  data.append('o_id', o_id);
  data.append('sess',$('#sess').attr('data-sess'));
  $.ajax({
    url: 'jquery.php',
    method: "post",
    processData: false,
    contentType: false,
    data: data,
    dataType: "html",
    success: function (resp) {
      alert_open(resp);
    },
    error: function (e) {
      // bad response
    }
  });
}

function deliv_change() {
  if($('#deliv_same').is(':checked')) {
    if($('#deliv_section').is(':visible')) {
      $('#deliv_section').slideUp();
    }
  }
  else {
    if(! $('#deliv_section').is(':visible')) {
      $('#deliv_section').slideDown();
    }
  }
}

function ticker() {
  if($('#ticker').length && ((max = $('#ticker').children('.ticker').length) > 1)) {
    next = 1 ;
    setInterval(function(){
      curr = next ;
      if(next == max){
        next = 1 ;
      }
      else {
        next += 1 ;
      }
      $('#ticker').children('.ticker:nth-of-type(' + curr + ')').animate({top:'-100%'}, 500, function() {
        $('#ticker').children('.ticker:nth-of-type(' + curr + ')').css('top','100%');
      });
      $('#ticker').children('.ticker:nth-of-type(' + next + ')').animate({top:0}, 500);
    }, 7000);
  }
}

// alert(ValidateEmail(' willow@makeitchina.com'));
function ValidateEmail(email) {
  return true ;
  // return (email && email.trim().match(/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/)) ? true : false ;
}

function dispWin() {
  // $('#cssSize').html(' ('  + winWidth() + ' x ' + winHeight() + ') ');
}

function anchorTo(h) {
  var anchorPos = 0 ;
  if($('A[name="' + h + '"]').length) {
    anchorPos = $('A[name="' + h + '"]').offset().top;
  }
  else if($('#' + h).length) {
    anchorPos = $('#' + h).offset().top;
  }  
  var scrollPos = anchorPos - 50 ;
  scrollPge(scrollPos,500);
}

function scrollPge(scrollPos,speed) {
  if(! speed) { speed = 1000; }
  $('html, body').animate({ scrollTop: scrollPos }, speed, function() {
    window.scrollTo(0,scrollPos);
  });
}

function winWidth() {
  // return document.documentElement.clientWidth ;
  return $(window).outerWidth();
}

function winHeight() {
  // return document.documentElement.clientHeight ;
  return $(window).outerHeight();
}

function cookiebar() {
  if( (document.referrer && ( document.referrer.indexOf(dom) > 0 || document.referrer.indexOf(loc) > 0 )) || getCookie(cname + '-t') ) {
    // second page or session cookie
    setCookie(cname,'y',365);
  }
  else if( ! getCookie(cname) ) {
    // first visit
    cpOpen();
  }
  if(! document.referrer) {
    // set session cookie for ios without referrer
    setCookie(cname + '-t','y');
  }
}

function cpOpen() {
  $('#cookiepolicy').slideDown("slow");
  $('#footer1').css('padding-bottom','40px');
}

function cpClose() {
  $('#cookiepolicy').slideUp("slow",function(){$('#footer1').css('padding-bottom','0');});
}

function setCookie(cname,cvalue,exdays) {
  var d = new Date();
  var extime = 0;
  if(exdays) {
    d.setTime(d.getTime()+(exdays*24*60*60*1000));
    var extime = d.toGMTString();
  }
  var host = window.location.hostname;
  var fpath = window.location.pathname.split('/')[1];
  var path = '/';
  document.cookie = cname + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; SameSite=Lax";
  if(fpath && (host == '127.0.0.1' || host == 'localhost' || host.indexOf('192.168.0') >= 0)) {
    path = '/' + fpath + '/' ;
    document.cookie = cname + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; SameSite=Lax";
  }
  document.cookie = cname + "=" + cvalue + "; expires=" + extime + "; path=" + path + "; SameSite=Lax";
}

function getCookie(cname) {
  var name = cname + "=";
  var ca = document.cookie.split(';');
  for(var i=0; i<ca.length; i++) {
    var c = ca[i].trim();
    if (c.indexOf(name)==0) {
      return c.substring(name.length,c.length);
    }
  }
  return "";
} 
