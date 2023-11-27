var activeTab;
var activeTab_ss = null;
var menuTimeOut = null;
 var GetVar = function() {
     activeTab = document.getElementById('active');
     if (!activeTab) {
         activeTab = document.getElementById('active-sel');
     }
     return activeTab;
 };
 var mouseIn;
 var DropDown;
 var menuHover = function (element) {
     clearTimeout(menuTimeOut);
     if(activeTab_ss==null)
        activeTab_ss = document.getElementById('active');
     if (DropDown) {
        DropDown.style.display = "none";
     }
     GetVar().removeAttribute('id');
     element.id='active';

     var LeftAbs = 0;

     for (var i = 0; element != element.parentNode.childNodes[i]; i++) {
         if (typeof (element.parentNode.childNodes[i].tagName) != "undefined") {
             LeftAbs = LeftAbs + element.parentNode.childNodes[i].clientWidth;
         }
     }

     mouseIn = true;
     DropDown = document.getElementById(element.getAttribute("xtitle"));
     if (DropDown) {
          DropDown.style.display = "block";
          DropDown.style.left = LeftAbs - 3 + "px";
         element.id = 'active';
     } else {
         element.id = 'active-sel';
     }
 };
 var GetBack = function() {
     if(!mouseIn) {
        GetVar().removeAttribute('id');
         activeTab_ss.id = 'active';
         if (DropDown) {
            DropDown.style.display = "none";
         }
     }
 };
 var menuOut = function () {
     menuTimeOut = setTimeout(GetBack,500);
     mouseIn = false;
 };

var MouseOnTab = function (element,active,id) {
    clearTimeout(menuTimeOut);
    var ids = {1:'active-big',2:'left-active',3:'right-active'};

    for (var key in ids) {
        var name = document.getElementById(ids[key]);
        if (name) {
            name.removeAttribute('id');
        }
    }

    var infoblock = {1:'suite',2:'touch',3:'scheduler',4:'others'};

    for (key in infoblock) {
        document.getElementById(infoblock[key]).style.display = 'none';
    }

    element.id = active;
    document.getElementById(id).style.display = 'block';
};