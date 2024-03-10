function runonload() {
  // dtime();
}

function getId(f) {
  return document.getElementById(f);
}

function dtime() {
  // needs formatDate.js
  var d = new Date();
  getId('dt').innerHTML = d.formatDate("l jS F Y");
  setTimeout("dtime()", 10000);
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_showHideLayers() { //v6.0
  var i,p,v,obj,args=MM_showHideLayers.arguments;
  for (i=0; i<(args.length-2); i+=3) if ((obj=MM_findObj(args[i]))!=null) { v=args[i+2];
    if (obj.style) { obj=obj.style; v=(v=='show')?'visible':(v=='hide')?'hidden':v; }
    obj.visibility=v; }
}

function show(pn,pw,ph) {
  pshow = window.open(pn, "ppwin", "toolbar=no,location=no,menubar=no,directories=no,status=no,scrollbars=no,resizable=yes,width="+pw+",height="+ph+",left=0,top=0" );
  if ( ( typeof(pshow) != "object" ) || !pshow || pshow.closed ) {
   return false;
  }
  else {
    pshow.window.focus();
  }
}

function cshow(pn,pw,ph) {
  var aw = 12 ;
  var ah = 83 ;
  mw = screen.availWidth - aw ; 
  mh = screen.availHeight - ah ; 
  if( ph > mh ) {
    pw =  Math.round( mh * pw / ph ) ;
    ph = mh ;
  }
  if( pw > mw ) {
    ph =  Math.round( mw * ph / pw ) ;
    pw = mw ;
  }
	pl = Math.round( ( mw - pw - aw) / 2 ) ;
	pt = Math.round( ( mh - ph - ah) / 2 ) ;
	pshow = window.open(pn, "ppwin", "toolbar=no,location=no,menubar=no,directories=no,status=no,scrollbars=no,resizable=yes,width="+pw+",height="+ph+",left="+pl+",top="+pt );
	if ( ( typeof(pshow) != "object" ) || !pshow || pshow.closed ) {
		return false;
	}
	else {
		pshow.window.focus();
	}
}

function Blink(layerName,color1,color2,bspeed){
  if(color1 === undefined) { color1='#004E82' ; }
  if(color2 === undefined) { color2='#CF1F26' ; }
  if(bspeed === undefined) { bspeed=500 ; }
  tcol = getId(layerName).style.color ;
  if(tcol.indexOf('rgb')==0) {
    // convert firefox rgb(r,g,b) to hex
    tarr = tcol.substring(4,tcol.length-1).split(',') ;
    tcol = '#';
    for (i=0;i<3;i++) {
      tbit = (tarr[i]-0).toString(16) ;
      if(tbit.length==1) { tbit = '0' + tbit ; }
      tcol = tcol + tbit ;
    }
  }
  tcol = tcol.toUpperCase();
  if(tcol==color2) {
    getId(layerName).style.color=color1;
  }
  else {
    getId(layerName).style.color=color2;
  }
  setTimeout("Blink('"+layerName+"','"+color1+"','"+color2+"',"+bspeed+")",bspeed);
}

function checkBrowser() {
  var rv = 999;
  if (navigator.appName == 'Microsoft Internet Explorer')  {
    var ua = navigator.userAgent;
    var re  = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
    if (re.exec(ua) != null) {
      rv = parseFloat( RegExp.$1 );
    }
  }
  return rv ;
}
