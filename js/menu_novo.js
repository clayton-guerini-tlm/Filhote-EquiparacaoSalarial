var j = jQuery.noConflict();
j(document).ready(function(){
	j('#menu > li').bind('mouseover', menu_open);
	j('#menu > li').bind('mouseout',  menu_timer);
});
document.onclick = menu_close;

var timeout         = 500;
var closetimer		= 0;
var ddmenuitem      = 0;

function menu_open(){
	menu_canceltimer();
	menu_close();
	ddmenuitem = j(this).find('ul').eq(0).css('visibility', 'visible');
}

function menu_close(){
	if(ddmenuitem) ddmenuitem.css('visibility', 'hidden');
}

function menu_timer(){
	closetimer = window.setTimeout(menu_close, timeout);
}

function menu_canceltimer(){
	if(closetimer){
		window.clearTimeout(closetimer);
		closetimer = null;
	}
}