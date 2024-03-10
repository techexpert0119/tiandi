function getId(f) {
  return document.getElementById(f);
}

function listfiles(fpath,file,fid,filter) {
  pshow = window.open( "listfiles.php?fpath="+fpath+"&file="+file+"&fid="+fid+"&filter="+filter , "listfiles", "toolbar=no,location=no,menubar=no,directories=no,status=no,scrollbars=yes,resizable=yes,width=840,height=600,left=10,top=10" );
  if ( ( typeof(pshow) != "object" ) || !pshow || pshow.closed ) {
   return false;
  }
  else {
    pshow.window.focus();
  }
}

function esubmit(enc) {
  if(enc) {
    if($('#eform').attr('data-submit')) {
      return true;
    }
    $('#eform').attr('data-submit',false);
  }
  var elem = document.eform.elements;
  var cki = new Array;
  var ckv = new Array;
  for(var i = 0; i < elem.length; i++) {
    if(elem[i].type == 'file' && elem[i].value == '') {
      // disable empty file upload fields
      elem[i].disabled = true ;
    }
    if(enc && (elem[i].className == 'ckeditor') && (v = CKEDITOR.instances[elem[i].id].getData())) {
      // array of ckeditor fields
      cki.push(elem[i].id);
      ckv.push(v);
    }
  }
  if(enc && cki.length) {
    // encrypt ckeditor html
    var data = { cki: cki, ckv: ckv };
    $.post("../scripts/iencode.php", data, function(res) {
      if(res) {
        res = JSON.parse(res);
        $.each(res, function(id, val){
          document.getElementById(id).outerHTML += '<input type="hidden" name="' + id + '_encoded" value="' + val + '">';
          document.getElementById(id).disabled = true ;
        });
        $('#eform').attr('data-submit',true);
        $('#eform').submit();
      }
    });
    return false ;  
  }
  else {
    return true ;  
  }
}

function jselmore(fpath,dval,fid,filefilt) {
  // file more
  fops = getId(fid).options;
  if($('#'+fid).attr('class') == 'chosen-select') {
    $('#'+fid).chosen("destroy");
  }
  fops.length=0;
  $.getJSON('jselect.php?action=jselmore&fpath='+fpath+'&filefilt='+filefilt+'&dval='+dval, 
  function(data) {
    fops[fops.length] = new Option('** Select **', 0, false, false);
    $.each(data, function(id, fn) {
      if(fn == dval) {
        fops[fops.length] = new Option(fn, fn, true, true);
      }
      else {
        fops[fops.length] = new Option(fn, fn, false, false);
      }
    });
    if($('#'+fid).attr('class') == 'chosen-select') {
      start_chosen('#'+fid);
    }
  });
}

function jrelook(fid) {
  // lookup selector
  fops = getId(fid).options;
  dval = $('#'+fid+' option:selected').map(function(){
    return $(this).val();
  }).get();
  topsel = '' ;
  if(fops.length) {
    topsel = fops[0].text ;
  }
  if($('#'+fid).attr('class') == 'chosen-select') {
    $('#'+fid).chosen("destroy");
  }
  fops.length=0;
  $.post('jselect.php', { 
    action: 'jrelook',
    sq_lookt: $('#'+fid+'_sqlookt').val(),
    sq_lookk: $('#'+fid+'_sqlookk').val(),
    sq_lookv: $('#'+fid+'_sqlookv').val(),
    sq_lookd: $('#'+fid+'_sqlookd').val(),
    sq_lookl: $('#'+fid+'_sqlookl').val(),
    sq_lookf: $('#'+fid+'_sqlookf').val()
  }, 
  function(data) {
    if(topsel == '** Select **') {
      fops[fops.length] = new Option('** Select **', 0, false, false);
    }
    $.each(data['k'], function(k,v) {
      fn = data['v'][k].replace('&pound;','�');
      id = v.toString();
      if(dval.indexOf(id) >= 0) {
        fops[fops.length] = new Option(fn, id, true, true);
      }
      else {
        fops[fops.length] = new Option(fn, id, false, false);
      }
    });
    if($('#'+fid).attr('class') == 'chosen-select') {
      start_chosen('#'+fid);
    }
  }, 'json');
}

function jreorder(fname) {
  // multi order
  selopt = $('.'+fname+'_sel').map(function(){
    return $(this).attr('id').replace(fname+'_sel_', '');
  }).get();
  selval = $('.'+fname+'_sel').map(function(){
    return $(this).val();
  }).get();
  notopt = $('.'+fname+'_not').map(function(){
    return $(this).attr('id').replace(fname+'_not_', '');
  }).get();
  notval = $('.'+fname+'_not').map(function(){
    return $(this).val();
  }).get();
  $.post('jselect.php', { 
    action: 'jreorder',
    fname: fname,
    selopt: selopt,
    selval: selval,
    notopt: notopt,
    notval: notval,
    sq_lookt: $('#'+fname+'_sqlookt').val(),
    sq_lookk: $('#'+fname+'_sqlookk').val(),
    sq_lookv: $('#'+fname+'_sqlookv').val(),
    sq_lookd: $('#'+fname+'_sqlookd').val(),
    sq_lookl: $('#'+fname+'_sqlookl').val(),
    sq_lookf: $('#'+fname+'_sqlookf').val()
  }, 
  function(data) {
    $('#'+fname+'_multsel').html(data['sel']);
    $('#'+fname+'_multnot').html(data['not']);
  }, 'json');
}

function onbrowse(fname) {
  upfile = document.eform['up_'+fname].value ;
  pos = upfile.lastIndexOf("\\");
  if(pos >= 0) {
    upfile = upfile.substring(pos+1);
  }
  upfile1 = upfile.replace(/[^A-Za-z0-9_\-\.]/g,'_').toLowerCase();
  flen = document.eform[fname].length ;
  for(i=1;i<=flen;i++) {
    if(typeof document.eform[fname][i] == 'object') {
      fchk = document.eform[fname][i].value ;
      if( (upfile == fchk) || (upfile1 == fchk) ) {
        ask = confirm("Overwrite file " + fchk + " ?");
        if(!ask) {
          document.eform['up_'+fname].value = '';
          elem = getId('id_up_'+fname) ;
          elem.parentNode.innerHTML = elem.parentNode.innerHTML;
        }
        break;
      }
    }
  }
}

fldchg = 0 ;
function chkleave() {
  if(fldchg && ! confirm('Leave without saving changes?')) {
    return false ;
  }
  return true ;
}

var mlttmo ;
function aform(f,m) {
  clearTimeout(mlttmo);
  mlttmo = setTimeout(function() { avform(f,m); }, 1000);
}

function avform(f,m) {
  if(confirm("Are you sure you want to change all?")) {
    for(i=0;i<=m;i++) {
      if($('#uf_'+f+'_'+i).length) {
        if($('#af_'+f).val()) {
          $('#uf_'+f+'_'+i).val($('#af_'+f).val());
        }
        else {
          $('#uf_'+f+'_'+i).val('');
        }
      }
    }
    return true;
  }
  return false;
}

function arform(f,m) {
  if(confirm("Are you sure you want to change all?")) {
    for(i=0;i<=m;i++) {
      if($('#uf_'+f+'_'+i).length) {
        $('#uf_'+f+'_'+i).prop('checked',true);
      }
    }
    return true;
  }
  return false;
}

function acform(f,m) {
  if(confirm("Are you sure you want to change all?")) {
    for(i=0;i<=m;i++) {
      if($('#uf_'+f+'_'+i).length) {
        if($('#af_'+f).is(':checked')) {
          $('#uf_'+f+'_'+i).prop('checked',true);
        }
        else {
          $('#uf_'+f+'_'+i).prop('checked',false);
        }
      }
    }
    return true;
  }
  return false;
}

function ssfilter(f) {
  e = $('#'+f+'_ssf');
  v = e.val().toLowerCase() ;
  if(v && v.length > 1) {
    $('.'+f+'_ssr').each( function() {
      t = $(this).find('.'+f+'_sst').text();
      if(t.toLowerCase().indexOf(v) >= 0) {
        $(this).show();
      }
      else {
        $(this).hide();
      }
    });
  }
  else {
    $('.'+f+'_ssr').show();
  }
}

function upimage(field,path,width,height) {
  $('#res_'+field).html('');
  if($('#up_id_'+field)[0].files[0]) {
    console.log($('#up_id_'+field)[0].files[0]);
    $('#res_'+field).html('Uploading: ' + $('#up_id_'+field)[0].files[0]['name']);
    var data = new FormData();
    data.append('action', 'upimage');
    data.append('field', field);
    data.append('path', path);
    data.append('width', width);
    data.append('height', height);
    data.append('file', $('#up_id_'+field)[0].files[0]);
    $.ajax({
      url: 'jquery.php',
      method: "post",
      processData: false,
      contentType: false,
      data: data,
      success: function (resp) {
        // response
        resp = JSON.parse(resp);
        if(resp.res) {
          // pass
          msg = 'Upload complete';
          if(resp.msg) {
            console.log(resp.msg);
            msg = msg + ', ' + resp.msg;
          }
          $('#res_'+field).html(msg);
          $('#new_'+field).html(resp.data + $('#new_'+field).html());
        }
        else {
          // fail
          $('#res_'+field).html(resp.msg);
        }
      },
      error: function (e) {
        // bad response
        $('#res_'+field).html('ERROR');
      }
    });
  }
}

function upshopi(id) {
  $('#upshopa').html('');
  if($('#id_upshopi')[0].files[0]) {
    console.log($('#id_upshopi')[0].files[0]);
    $('#upshopa').html('Uploading: ' + $('#id_upshopi')[0].files[0]['name']);
    var data = new FormData();
    data.append('action', 'upshopi');
    data.append('i_id', id);
    data.append('id_upshopi', $('#id_upshopi')[0].files[0]);
    $.ajax({
      url: 'jquery.php',
      method: "post",
      processData: false,
      contentType: false,
      data: data,
      success: function (resp) {
        // response
        resp = JSON.parse(resp);
        if(resp.res) {
          // pass
          msg = 'Upload complete';
          if(resp.msg) {
            console.log(resp.msg);
            msg = msg + ', ' + resp.msg;
          }
          $('#upshopa').html(msg);
          $('#upshopv').html($('#upshopv').html() + resp.data);
        }
        else {
          // fail
          $('#upshopa').html(resp.msg);
        }
      },
      error: function (e) {
        // bad response
        $('#upshopa').html('ERROR');
      }
    });
  }
}

function selimgopts(id) {
  opts = new Array();
  html = '';
  str = '';
  if($('#selimages_'+id).is(':visible')) {
    $('#selimages_'+id).children('.selimage').each( function() {
      if($(this).find('input').val()) {
        opts.push( {'id' : $(this).find('img').attr('data-id'), 'ord' : $(this).find('input').val() } );
      }
    });
    opts.sort((a, b) => (a.ord > b.ord) ? 1 : -1)
    opts.forEach(function(v, i) {
      str = str + v.id + ',';
      html = html + '<img data-id=' + v.id + ' src="' + $('#imgthumb_'+v.id).attr('src') + '" width="30"> ';
    });
    $('#o_upd_'+id).val('y');
    $('#o_images_'+id).val(str.substring(0, str.length - 1));
    $('#imgschosen_'+id).html(html);
    $('.selimages').hide();
  }
  else {
    $('.selimages').hide();
    c = 1 ;
    $('#imgschosen_' + id).children('img').each( function() {
      ns = $(this).attr('src').lastIndexOf('/');
      src = $(this).attr('src').substring(ns + 1);
      html = html + '<div class="selimage" style="padding:5px;"><img title="' + src + '" style="display:inline-block;vertical-align:middle;margin-right:5px;" width="50" src="' + $(this).attr('src') + '" data-id="' + $(this).attr('data-id') + '"><input style="display:inline-block;vertical-align:middle;width:20px;" value="' + c + '"></div>';
      opts.push($(this).attr('data-id'));
      c++;
    });
    $('.imgthumbs').each( function() {
      ns = $(this).attr('src').lastIndexOf('/');
      src = $(this).attr('src').substring(ns + 1);
      if(! opts.includes($(this).attr('data-id'))) {
        html = html + '<div class="selimage" style="padding:5px;"><img title="' + src + '" style="display:inline-block;vertical-align:middle;margin-right:5px;" width="50" src="' + $(this).attr('src') + '" data-id="' + $(this).attr('data-id') + '"><input style="display:inline-block;vertical-align:middle;width:20px;" value=""></div>';
      }
    });
    if(html) {
      $('#selimages_'+id).html(html).show();
    }
  }
}

$(document).ready(function(){
  $('#id_i_price').on("input",function() { price_calc(); });
  $('#id_i_discprice').on("input",function() { price_calc(); });
  $('#id_i_discpcnt').on("input",function() { price_calc(); });
  $('input[name="i_discsubs"]').on("input",function() { price_calc(); });
  $('.o_price').on("input",function() { price_calc(); });
  $('.o_discprice').on("input",function() { price_calc(); });
  $('.o_discpcnt').on("input",function() { price_calc(); });
  $('.o_visible').on("input",function() { price_calc(); });
});

function price_calc() {
  var $i_pricemin = 0;
  var $i_pricemax = 0;

  // get for item
  var $i_price = Number($('#id_i_price').val()); 
  var $i_discprice = Number($('#id_i_discprice').val()); 
  var $i_discpcnt = Number($('#id_i_discpcnt').val());
  var $i_discsubs = $('input[name="i_discsubs"]:checked').val();
  var $i_pricecalc = $i_price;

  // maths like php
  if($i_discprice > 0 && $i_discprice < $i_price) {
    // fixed item discount
    $i_pricecalc = $i_price - $i_discprice ;
  }
  else if($i_discpcnt > 0) {
    // percent item discount
    $i_pricecalc = ($i_price * (100 - $i_discpcnt)) / 100;
  }

  // options
  $.each( $('.o_options'), function (k, v) {
    $o_price = Number($(this).find('.o_price').val()); 
    $o_discprice = Number($(this).find('.o_discprice').val()); 
    $o_discpcnt = Number($(this).find('.o_discpcnt').val()); 
    $o_visible = Number($(this).find('.o_visible:checked').val());

    // maths like php
    if($o_price <= 0) {
      $o_price = $i_price;
    }
    $o_pricecalc = $o_price;
    if($o_discprice > 0 && $o_discprice < $o_price) {
      // fixed option discount 
      $o_pricecalc = $o_price - $o_discprice ;
    }
    else if($o_discpcnt > 0) {
      // percent option discount
      $o_pricecalc = $o_price * (100 - $o_discpcnt) / 100 ;
    }
    else if($i_discsubs == 'yes') {
      if($i_discprice > 0 && $i_discprice < $o_price) {
        // fixed option discount from item
        $o_pricecalc = $o_price - $i_discprice ;
      }
      else if($i_discpcnt > 0) {
        // percent option discount from item
        $o_pricecalc = $o_price * (100 - $i_discpcnt) / 100 ;
      }
    }

    // min/max calc
    if($o_pricecalc > $i_pricemax) {
      $i_pricemax = $o_pricecalc;
    }
    if(($i_pricemin == 0) || ($o_pricecalc > 0 && $o_pricecalc < $i_pricemin)) {
      $i_pricemin = $o_pricecalc;
    }

    $(this).find('.o_pricecalc').html(parseFloat(Math.round($o_pricecalc)).toFixed(2)); 
    $(this).find('.o_upd').each(function() {
      $(this).val('y');
    });

  });

  // set item values
  $('#id_i_pricecalc').html(parseFloat(Math.round($i_pricecalc)).toFixed(2));
  $('#id_i_pricemin').html(parseFloat(Math.round($i_pricemin)).toFixed(2));
  $('#id_i_pricemax').html(parseFloat(Math.round($i_pricemax)).toFixed(2));
}

function copy_clip(v) {
  navigator.clipboard.writeText(v).then(function() {
    alert("COPIED TO CLIPBOARD");
  }, function() {
    alert("COPY TO CLIPBOARD FAILED");
  });
}
