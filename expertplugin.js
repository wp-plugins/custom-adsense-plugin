
window.$id = function (a) {
    if (!a) a = document;
    if (a.nodeType) {
        return a
    }
    if (typeof a == "string") return document.getElementById(a)
};

var getElementsByClassName = function (className, tag, elm){
	if (document.getElementsByClassName) {
		getElementsByClassName = function (className, tag, elm) {
			elm = elm || document;
			var elements = elm.getElementsByClassName(className),
				nodeName = (tag)? new RegExp("\\b" + tag + "\\b", "i") : null,
				returnElements = [],
				current;
			for(var i=0, il=elements.length; i<il; i+=1){
				current = elements[i];
				if(!nodeName || nodeName.test(current.nodeName)) {
					returnElements.push(current);
				}
			}
			return returnElements;
		};
	}
	else if (document.evaluate) {
		getElementsByClassName = function (className, tag, elm) {
			tag = tag || "*";
			elm = elm || document;
			var classes = className.split(" "),
				classesToCheck = "",
				xhtmlNamespace = "http://www.w3.org/1999/xhtml",
				namespaceResolver = (document.documentElement.namespaceURI === xhtmlNamespace)? xhtmlNamespace : null,
				returnElements = [],
				elements,
				node;
			for(var j=0, jl=classes.length; j<jl; j+=1){
				classesToCheck += "[contains(concat(' ', @class, ' '), ' " + classes[j] + " ')]";
			}
			try	{
				elements = document.evaluate(".//" + tag + classesToCheck, elm, namespaceResolver, 0, null);
			}
			catch (e) {
				elements = document.evaluate(".//" + tag + classesToCheck, elm, null, 0, null);
			}
			while ((node = elements.iterateNext())) {
				returnElements.push(node);
			}
			return returnElements;
		};
	}
	else {
		getElementsByClassName = function (className, tag, elm) {
			tag = tag || "*";
			elm = elm || document;
			var classes = className.split(" "),
				classesToCheck = [],
				elements = (tag === "*" && elm.all)? elm.all : elm.getElementsByTagName(tag),
				current,
				returnElements = [],
				match;
			for(var k=0, kl=classes.length; k<kl; k+=1){
				classesToCheck.push(new RegExp("(^|\\s)" + classes[k] + "(\\s|$)"));
			}
			for(var l=0, ll=elements.length; l<ll; l+=1){
				current = elements[l];
				match = false;
				for(var m=0, ml=classesToCheck.length; m<ml; m+=1){
					match = classesToCheck[m].test(current.className);
					if (!match) {
						break;
					}
				}
				if (match) {
					returnElements.push(current);
				}
			}
			return returnElements;
		};
	}
	return getElementsByClassName(className, tag, elm);
};
var getStringToXMLObject = function (a) {
        try {
            xmlDoc = new ActiveXObject("Microsoft.XMLDOM");
            xmlDoc.async = "false";
            xmlDoc.loadXML(a);
            return xmlDoc
        } catch (e) {
            try {
                parser = new DOMParser();
                xmlDoc = parser.parseFromString(a, "text/xml");
                return xmlDoc
            } catch (e) {
                return false
            }
        }
};
var elems = getElementsByClassName( 'post' ), newp = document.createElement( "div" );

GmPost = {};

GmPost.createSpace = function( a, b) {

	newp.className = pouter; //elems[1].className;

	newp.innerHTML = ptemplate;
	if(ptype == "home" && a == "yes"){

		if(elems.length > 1){
			elems[1].parentNode.insertBefore( newp, elems[1] );
		}else{
			elems[0].parentNode.appendChild( newp );
		}
	}else if(ptype == "single" && b == "yes"){
		elems[0].parentNode.insertBefore( newp, elems[0].nextSibling );
	}
	
	$id('post_username').innerHTML = post_username;
	
};

GmPost.writeTitle = function(ptitle) {
	$id( 'gm_title' ).innerHTML = ptitle;
};

GmPost.writeDescription = function(pdesc, fburl) {
	var fblikebut = '<div style="float:right;width:80px;"><iframe src="http://www.facebook.com/plugins/like.php?href='+fburl+'&amp;layout=box_count&amp;show_faces=true&amp;width=80&amp;action=like&amp;colorscheme=light&amp;height=65" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:80px; height:65px;" allowTransparency="true"></iframe></div>';
	$id( 'gm_desc' ).innerHTML = "<p>" + fblikebut + pdesc + "</p>";
};


GmPost.Start = function() {
	//var bightml = $id( 'gm_hiddenpost' ).innerHTML;

	var bightml = $id( 'hiddenad' ).innerHTML;
	
	var hstart = bightml.toLowerCase().indexOf( "<wp>" );
	var hend = bightml.toLowerCase().indexOf( "</wp>" );


	if( hstart != -1 && hend != -1 ) {
		
		var ahtml = bightml.substring(hstart,hend+5);
		//ahtml = ahtml.replace( /\<\!\-\-\[CDATA\[/g, '<![CDATA[' );  // Firefox makes cdata as comment rearrange it
		//ahtml = ahtml.replace( /\]\]\-\-\>/g, ']]>' );  // Firefox makes cdata as comment rearrange it
		ahtml = ahtml.replace( /\<ptitle\>/gi, '<ptitle><![CDATA[');
		ahtml = ahtml.replace( /\<\/ptitle\>/gi, ']]></ptitle>');
		ahtml = ahtml.replace( /\<pcontent\>/gi, '<pcontent><![CDATA[');
		ahtml = ahtml.replace( /\<\/pcontent\>/gi, ']]></pcontent>');
		ahtml = ahtml.replace( /\<fblikeurl\>/gi, '<fblikeurl><![CDATA[');
		ahtml = ahtml.replace( /\<\/fblikeurl\>/gi, ']]></fblikeurl>');
		ahtml = ahtml.replace( /\<thumbnail\>/gi, '<thumbnail><![CDATA[');
		ahtml = ahtml.replace( /\<\/thumbnail\>/gi, ']]></thumbnail>');
		ahtml = ahtml.replace( /\n/g, '' );
		var xmlObj = getStringToXMLObject( ahtml );
		var x = xmlObj.documentElement.childNodes;
		if(x[6].childNodes[0].nodeValue != "yes" &&  x[7].childNodes[0].nodeValue != "yes" ){
			return false;
		}
		GmPost.createSpace( x[6].childNodes[0].nodeValue, x[7].childNodes[0].nodeValue );
		GmPost.writeTitle( x[4].childNodes[0].nodeValue );
		GmPost.writeDescription( x[5].childNodes[0].nodeValue , encodeURIComponent(x[3].childNodes[0].nodeValue));

	}
}
GmPost.Start();
//setTimeout("GmPost.Start()",3000);