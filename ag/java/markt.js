/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */


var DHTML = 0, DOM = 0, MS = 0, NS = 0, OP = 0;

function showText(textid) {
Textbox.style.visible=true;
}

function DHTML_init() {

 if (window.opera) {
     OP = 1;
 }
 if(document.getElementById) {
   DHTML = 1;
   DOM = 1;
 }
 if(document.all && !OP) {
   DHTML = 1;
   MS = 1;
 }
if(document.layers && !OP) {
   DHTML = 1;
   NS = 1;
 }
}

function getElem(p1,p2,p3) {
 var Elem;
 if(DOM) {
   if(p1.toLowerCase()=="id") {
     if (typeof document.getElementById(p2) == "object")
     Elem = document.getElementById(p2);
     else Elem = void(0);
     return(Elem);
   }
   else if(p1.toLowerCase()=="name") {
     if (typeof document.getElementsByName(p2) == "object")
     Elem = document.getElementsByName(p2)[p3];
     else Elem = void(0);
     return(Elem);
   }
   else if(p1.toLowerCase()=="tagname") {
     if (typeof document.getElementsByTagName(p2) == "object" ||
        (OP && typeof document.getElementsByTagName(p2) == "function"))
     Elem = document.getElementsByTagName(p2)[p3];
     else Elem = void(0);
     return(Elem);
   }
   else return void(0);
 }
 else if(MS) {
   if(p1.toLowerCase()=="id") {
     if (typeof document.all[p2] == "object")
     Elem = document.all[p2];
     else Elem = void(0);
     return(Elem);
   }
   else if(p1.toLowerCase()=="tagname") {
     if (typeof document.all.tags(p2) == "object")
     Elem = document.all.tags(p2)[p3];
     else Elem = void(0);
     return(Elem);
   }
   else if(p1.toLowerCase()=="name") {
     if (typeof document[p2] == "object")
     Elem = document[p2];
     else Elem = void(0);
     return(Elem);
   }
   else return void(0);
 }
 else if(NS) {
   if(p1.toLowerCase()=="id" || p1.toLowerCase()=="name") {
   if (typeof document[p2] == "object")
     Elem = document[p2];
     else Elem = void(0);
     return(Elem);
   }
   else if(p1.toLowerCase()=="index") {
    if (typeof document.layers[p2] == "object")
     Elem = document.layers[p2];
    else Elem = void(0);
     return(Elem);
   }
   else return void(0);
 }
}

function getCont(p1,p2,p3) {
   var Cont;
   if(DOM && getElem(p1,p2,p3) && getElem(p1,p2,p3).firstChild) {
     if(getElem(p1,p2,p3).firstChild.nodeType == 3)
       Cont = getElem(p1,p2,p3).firstChild.nodeValue;
     else
       Cont = "";
     return(Cont);
   }
   else if(MS && getElem(p1,p2,p3)) {
     Cont = getElem(p1,p2,p3).innerText;
     return(Cont);
   }
   else return void(0);
}

function getAttr(p1,p2,p3,p4) {
   var Attr;
   if((DOM || MS) && getElem(p1,p2,p3)) {
     Attr = getElem(p1,p2,p3).getAttribute(p4);
     return(Attr);
   }
   else if (NS && getElem(p1,p2)) {
       if (typeof getElem(p1,p2)[p3] == "object")
        Attr=getElem(p1,p2)[p3][p4]
       else
        Attr=getElem(p1,p2)[p4]
         return Attr;
       }
   else return void(0);
}

function setCont(p1,p2,p3,p4) {
   if(DOM && getElem(p1,p2,p3) && getElem(p1,p2,p3).firstChild)
     getElem(p1,p2,p3).firstChild.nodeValue = p4;
   else if(MS && getElem(p1,p2,p3))
     getElem(p1,p2,p3).innerText = p4;
   else if(NS && getElem(p1,p2,p3)) {
     getElem(p1,p2,p3).document.open();
     getElem(p1,p2,p3).document.write(p4);
     getElem(p1,p2,p3).document.close();
   }
}

DHTML_init();


function Menue() {
 if(DOM) {
   if(MS)
//getElem("id","Nav",null).style.top = window.event.clientY;
getElem("id","Nav",null).style.left = window.event.clientX;
   else
getElem("id","Nav",null).style.top = window.event.clientY;

 }
 if(DOM || MS) {
    if (!DOM) getElem("id","Nav",null).style.top = document.body.scrollTop + 50;
    if (OP) getElem("id","NavLinks",null).style.pixelTop = NavLinksPos;
    getElem("id","Nav",null).style.visibility = "visible";
   }
 else if(NS) {
  getElem("id","Nav",null).top = window.pageYOffset + 50;
  getElem("id","Nav",null).visibility = "show";
 }

}

function noMenue() {
 if(DOM || MS)
  getElem("id","Nav",null).style.visibility = "hidden";
 if(NS)
  getElem("id","Nav",null).visibility = "hide";
}
function handleMove(ev) {
 if(!MS) {
   Event = ev;
   if(window.event.shiftKey)
     Menue();
   else 
     noMenue();
 }
}

function MShandleMove() {
 if(MS) {
 if(window.event.shiftKey)
    Menue();
  else 
    noMenue();
  }

}


function Event_init() {
 if(DOM && !MS && !OP) {
  getElem("tagname","body",0).addEventListener("mousemove", handleMove, true);
 }
 if(NS) {
  document.captureEvents(Event.MOUSEMOVE);
  document.onmousemove=handleMove;
 }
 if (DOM && OP) {
    document.onmousemove=handleMove;
    NavLinksPos=42; //Position des Bereiches NavLinks
    getElem("id","NavLinks",null).style.pixelTop=NavLinksPos;
 }
 if (MS) getElem("tagname","body",0).onmousemove=MShandleMove;
}
