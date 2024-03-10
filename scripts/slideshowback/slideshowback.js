var slideOuter = '.slide_set'; // outer class (start with .)
var slideSlide = '.slide_img'; // slide divs (start with .)
var slidePrev =  '.slide_prev'; // slide prev (start with .)
var slideNext =  '.slide_next'; // slide next (start with .)
var slideLink =  '.slide_lnk'; // slide next (start with .)

var defaultMethod = 'fade'; // fade, slideleft, slideright, slidein, slideout (slide all require jquery-ui) - override with data-method=""
var defaultInterval = 7000; // zero for manual - override with data-interval=""
var defaultFade = 1000; // override with data-fade=""

var slideMethod = [] ;
var slideInterval = [] ;
var slideFade = [] ;
var slideCount = [] ;
var slideItem = [] ;
var slideLoop = [] ;
var slideActive = [] ; // not used
var slideTotal = [] ;

$(function(){

  $(slideOuter).on( "swipeleft", function(event) {
    s = $(this).index(slideOuter);
    // alert(s);
    slideGoPrev(s);
  });

  $(slideOuter).on( "swiperight", function(event) {
    s = $(this).index(slideOuter);
    // alert(s);
    slideGoNext(s);
  });

});

$(document).ready(function(){

  $(slideOuter).each(function(s) {
    slideMethod[s] = $(this).attr('data-method') ? $(this).attr('data-method') : defaultMethod;
    slideInterval[s] = $(this).attr('data-interval') ? parseInt($(this).attr('data-interval')) : defaultInterval;
    slideFade[s] = $(this).attr('data-fade') ? parseInt($(this).attr('data-fade')) : defaultFade;
    $['slideLoop'+s] = '' ;
    loadImages(s);
    slideStart(s);
  });

  if(slidePrev) {
    $(slidePrev).click(function(e) {
      s = $(this).parent().index(slideOuter);
      // alert(s);
      slideGoPrev(s);
      e.preventDefault();
    });
  }

  if(slideNext) {
    $(slideNext).click(function(e) {
      s = $(this).parent().index(slideOuter);
      // alert(s);
      slideGoNext(s);
      e.preventDefault();
    });
  }

});

function slideSet(s) {
  return slideOuter + ':eq('+s+')';
}

function loadImages(s) {
  setgo = slideSet(s);
  $(setgo).children(slideSlide).each(function(index) {
    if(index) {
      $(this).css('z-index','7').css('display','block').hide();
    }
    else {
      // first to top
      $(this).css('z-index','9').css('display','block').show();
    }
  });
  $(setgo).children(slideSlide).children(slideLink).css('display','block').show();
}

function slideStart(s) {
  setgo = slideSet(s);
  slideCount[s] = $(setgo).children(slideSlide).length;
  slideTotal[s] = slideCount[s] ;
  if(slideTotal[s] > 1) {
    slideItem[s] = 0;
    slideActive[s] = 0 ;
    if(slideInterval[s] > 0) {
      slideLoop[s] = setInterval(slideForward, slideInterval[s], s);
    }
  }
}

function slideStop(s) {
  if(typeof slideLoop[s] !== 'undefined') {
    clearInterval(slideLoop[s]);
  }
}

function slideForward(s) {
  if(! slideActive[s]) {

    setgo = slideSet(s);
    sthis = slideItem[s];
    if(slideItem[s] == slideCount[s] - 1){ slideItem[s] = 0; } else{ slideItem[s]++; }
    snext = slideItem[s];
    slideActive[s] = 0 ;

    $(setgo).children(slideSlide).not($(setgo).children(slideSlide).eq(sthis)).not($(setgo).children(slideSlide).eq(snext)).css('z-index','7');
    $(setgo).children(slideSlide).eq(sthis).stop(true,true);
    $(setgo).children(slideSlide).eq(snext).stop(true,true);

    if(slideMethod[s] == 'fade') {
      $(setgo).children(slideSlide).eq(sthis).css('z-index','8').show().fadeOut(slideFade[s], function() { slideActive[s] = 0 ; } );
      $(setgo).children(slideSlide).eq(snext).css('z-index','9').hide().fadeIn(slideFade[s], function() { slideActive[s] = 0 ; } );
    }
    else if(slideMethod[s] == 'slidein') {
      // slide next over
      $(setgo).children(slideSlide).eq(sthis).css('z-index','8').show();
      $(setgo).children(slideSlide).eq(snext).css('z-index','9').hide().show("slide", { direction: "right" }, slideFade[s], function() { slideActive[s] = 0 ; } );
    }
    else if(slideMethod[s] == 'slideout') {
      // slide this out
      $(setgo).children(slideSlide).eq(sthis).css('z-index','9').show().hide("slide", { direction: "left" }, slideFade[s], function() { slideActive[s] = 0 ; } );
      $(setgo).children(slideSlide).eq(snext).css('z-index','8').show();
    }
    else if(slideMethod[s] == 'slideright') {
      // slide both
      $(setgo).children(slideSlide).eq(sthis).css('z-index','8').show().hide("slide", { direction: "right" }, slideFade[s], function() { slideActive[s] = 0 ; } );
      $(setgo).children(slideSlide).eq(snext).css('z-index','9').hide().show("slide", { direction: "left" }, slideFade[s], function() { slideActive[s] = 0 ; } );
    }
    else if(slideMethod[s] == 'slideleft') {
      // slide left
      $(setgo).children(slideSlide).eq(sthis).css('z-index','8').show().hide("slide", { direction: "left" }, slideFade[s], function() { slideActive[s] = 0 ; } );
      $(setgo).children(slideSlide).eq(snext).css('z-index','9').hide().show("slide", { direction: "right" }, slideFade[s], function() { slideActive[s] = 0 ; } );
    }

  }
}

function slideBackward(s) {
  if(! slideActive[s]) {

    setgo = slideSet(s);
    sthis = slideItem[s];
    if(slideItem[s] == 0){ slideItem[s] = slideCount[s] - 1; } else{ slideItem[s]--; }
    snext = slideItem[s];
    slideActive[s] = 0 ;

    $(setgo).children(slideSlide).not($(setgo).children(slideSlide).eq(sthis)).not($(setgo).children(slideSlide).eq(snext)).css('z-index','7');
    $(setgo).children(slideSlide).eq(sthis).stop(true,true);
    $(setgo).children(slideSlide).eq(snext).stop(true,true);

    if(slideMethod[s] == 'fade') {
      $(setgo).children(slideSlide).eq(sthis).css('z-index','8').show().fadeOut(slideFade[s], function() { slideActive[s] = 0 ; } );
      $(setgo).children(slideSlide).eq(snext).css('z-index','9').hide().fadeIn(slideFade[s], function() { slideActive[s] = 0 ; } );
    }
    else if(slideMethod[s] == 'slidein') {
      // slide next over
      $(setgo).children(slideSlide).eq(sthis).css('z-index','8').show();
      $(setgo).children(slideSlide).eq(snext).css('z-index','9').hide().show("slide", { direction: "left" }, slideFade[s], function() { slideActive[s] = 0 ; } );
    }
    else if(slideMethod[s] == 'slideout') {
      // slide this out
      $(setgo).children(slideSlide).eq(sthis).css('z-index','9').show().hide("slide", { direction: "right" }, slideFade[s], function() { slideActive[s] = 0 ; } );
      $(setgo).children(slideSlide).eq(snext).css('z-index','8').show();
    }
    else if(slideMethod[s] == 'slideright') {
      // slide both
      $(setgo).children(slideSlide).eq(sthis).css('z-index','8').show().hide("slide", { direction: "left" }, slideFade[s], function() { slideActive[s] = 0 ; } );
      $(setgo).children(slideSlide).eq(snext).css('z-index','9').hide().show("slide", { direction: "right" }, slideFade[s], function() { slideActive[s] = 0 ; } );
    }
    else if(slideMethod[s] == 'slideleft') {
      // slide left
      $(setgo).children(slideSlide).eq(sthis).css('z-index','8').show().hide("slide", { direction: "right" }, slideFade[s], function() { slideActive[s] = 0 ; } );
      $(setgo).children(slideSlide).eq(snext).css('z-index','9').hide().show("slide", { direction: "left" }, slideFade[s], function() { slideActive[s] = 0 ; } );
    }

  }
}

function slideGoPrev(s) {
  if(slideTotal[s] > 1 && ! slideActive[s]) {
    setgo = slideSet(s);
    clearInterval(slideLoop[s]);
    slideBackward(s);
    if(slideInterval[s] > 0) {
      slideLoop[s] = setInterval(slideBackward, slideInterval[s], s);
    }
  }
}

function slideGoNext(s) {
  if(slideTotal[s] > 1 && ! slideActive[s]) {
    setgo = slideSet(s);
    clearInterval(slideLoop[s]);
    slideForward(s);
    if(slideInterval[s] > 0) {
      slideLoop[s] = setInterval(slideForward, slideInterval[s], s);
    }
  }
}
