/**
 * @author Pavel Senko; 
 * (c) 2009 dumanoid.ru
 */

var xmlhttp
/*@cc_on @*/
/*@if (@_jscript_version >= 5)
  try {
  xmlhttp=new ActiveXObject("Msxml2.XMLHTTP")
 } catch (e) {
  try {
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP")
  } catch (E) {
   xmlhttp=false
  }
 }
@else
 xmlhttp=false
@end @*/
if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
	try {
		xmlhttp = new myXMLHttpRequest();
	} catch (e) {
		xmlhttp=false
	}
}

// use $('myId') instead of document.getElementById('myId')

var $ = function (id, doc) {
	if((id)&&((typeof id == "string")||(id instanceof String))){
		if (!doc) { doc = document; }
		var ele = doc.getElementById(id);
		// workaround bug in IE and Opera 8.2 where getElementById returns wrong element
		if (ele && (ele.id != id) && doc.all) {
			ele = null;
		// get all matching elements with this id
			eles = doc.all[id];
			if (eles) {
			// if more than 1, choose first with the correct id
				if (eles.length) {
					for (var i=0; i < eles.length; i++) {
						if (eles[i].id == id) {
							ele = eles[i];
							break;
						}
					}
					// return 1 and only element
				} else { ele = eles; }
			}
		}
		return ele;
	}
	return id; // assume it's a node
}


function showhide(id) {
	var theObj = $(id);
	var theDisp = theObj.style.display == "none" ? "block" : "none";
	theObj.style.display = theDisp;
}

function myXMLHttpRequest() {
	
 	var xmlhttplocal=false;
 	
 	if (window.ActiveXObject) {
 	
		try {
			xmlhttplocal= new ActiveXObject("Msxml2.XMLHTTP")
		} catch (e) {
			try {
				xmlhttplocal= new ActiveXObject("Microsoft.XMLHTTP")
			} catch (E) {
				xmlhttplocal=false;
			}
		}
		
	} else if (window.XMLHttpRequest) {
		
		xmlhttplocal = new XMLHttpRequest();
		if (xmlhttplocal.overrideMimeType) {
			xmlhttplocal.overrideMimeType('text/xml');
		}
	}
	
	if (!xmlhttplocal) {
		alert('Giving up :( Cannot create an XMLHTTP instance');
		return false;
	}
	
	return xmlhttplocal;

}


function isIE() {
	var useragent = navigator.userAgent;
	var pos = useragent.indexOf('MSIE');
	if (pos > -1) {
		return false;
	} else {
		return true;
	}

}