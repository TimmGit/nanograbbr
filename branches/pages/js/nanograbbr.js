function login(needCheck) {
	var opt = {	    
	    method: 'post',
	    postBody: 'password=' + $F('password') + '&version=' + needCheck,
	    onSuccess: function(t) {
			var ajaxResponse = Try.these(
			function() { return new DOMParser().parseFromString(t.responseText, 'text/xml'); },
			function() { var xmldom = new ActiveXObject('Microsoft.XMLDOM'); 
				xmldom.loadXML(t.responseText); return xmldom; }
			);						
			var result =
				ajaxResponse.getElementsByTagName('result')[0].firstChild.nodeValue;
			if (result == "good") {
				var url =
					ajaxResponse.getElementsByTagName('url')[0].firstChild.nodeValue;				
				window.location = url;				
			} else alert('Wrong password');
	    },
	    on404: function(t) {
	        alert('Error 404: location "' + t.statusText + '" was not found.');
	    },
	    onFailure: function(t) {
	        alert('Error ' + t.status + ' -- ' + t.statusText);
	    }
	}	
	new Ajax.Request(site_path+'ajax/login', opt);	
} // login

function logout() {
	var opt = {	    
	    method: 'post',
	    postBody: '',
	    onSuccess: function(t) {
			var ajaxResponse = Try.these(
			function() { return new DOMParser().parseFromString(t.responseText, 'text/xml'); },
			function() { var xmldom = new ActiveXObject('Microsoft.XMLDOM'); 
				xmldom.loadXML(t.responseText); return xmldom; }
			);				
			var result =
				ajaxResponse.getElementsByTagName('result')[0].firstChild.nodeValue;									
			if (result == "good") {
				var url =
					ajaxResponse.getElementsByTagName('url')[0].firstChild.nodeValue;
				window.location = url;				
			} else alert('Wrong password');
	    },
	    on404: function(t) {
	        alert('Error 404: location "' + t.statusText + '" was not found.');
	    },
	    onFailure: function(t) {
	        alert('Error ' + t.status + ' -- ' + t.statusText);
	    }
	}	
	new Ajax.Request(site_path+'ajax/logout', opt);	
} // logout

function getHTML(code, element_id) {
	var opt = {	    
	    method: 'post',
	    postBody: 'code=' + code + '&element=' + element_id,
	    onSuccess: function(t) {
	    	Element.update(element_id, t.responseText);	    	
	    },
	    on404: function(t) {
	        alert('Error 404: location "' + t.statusText + '" was not found.');
	    },
	    onFailure: function(t) {
	        alert('Error ' + t.status + ' -- ' + t.statusText);
	    }
	}	
	new Ajax.Request(site_path+'ajax/html', opt);		
} // getHTML

var showed_form = 0;
function showForm(form_type) {
	if (showed_form != 0) {
		$('form_post_' + showed_form).hide();
	}
	$('form_post_' + form_type).show();
	showed_form = form_type;
}

function editPost(post_id) {	
	var opt = {	    
	    method: 'post',
	    postBody: 'post_id='+post_id,
	    onSuccess: function(t) {
			var ajaxResponse = Try.these(
			function() { return new DOMParser().parseFromString(t.responseText, 'text/xml'); },
			function() { var xmldom = new ActiveXObject('Microsoft.XMLDOM'); 
				xmldom.loadXML(t.responseText); return xmldom; }
			);			
			var result =
				ajaxResponse.getElementsByTagName('result')[0].firstChild.nodeValue;			
			
			if (result == "bad") {
				var msg =
					ajaxResponse.getElementsByTagName('msg')[0].firstChild.nodeValue;							
				alert(msg);
				return;
			} else {				
				var id =
					ajaxResponse.getElementsByTagName('id')[0].firstChild.nodeValue;							
				var post_type =
					ajaxResponse.getElementsByTagName('post_type')[0].firstChild.nodeValue;							
				var title =
					ajaxResponse.getElementsByTagName('title')[0].firstChild.nodeValue;							
				var text =
					ajaxResponse.getElementsByTagName('text')[0].firstChild.nodeValue;			
				var url =
					ajaxResponse.getElementsByTagName('url')[0].firstChild.nodeValue;							
				var feed_id =
					ajaxResponse.getElementsByTagName('feed_id')[0].firstChild.nodeValue;							
				var comments =
					ajaxResponse.getElementsByTagName('comments')[0].firstChild.nodeValue;				
				showForm(post_type);		
				$(post_type+'_edit').show();
				$(post_type+'_create').hide();								
				if (text != 0) $(post_type+'_text_id').setValue(text);
				if (title != 0) $(post_type+'_title_id').setValue(title);						
				if (url != 0) $(post_type+'_url_id').setValue(url);				
				if (id != 0) {
					$(post_type+'_post_id_id').setValue(id);
					$(post_type+'_delete_btn').show();
				}
				$(post_type+'_post_type_id').setValue(post_type);						
				if (comments==1) {
					$(post_type+'_allow_comment_id').checked = 1;	
				} else {
					$(post_type+'_allow_comment_id').checked = 0;	
				}
				Field.activate($('form_top_id'));					
				
			}
	    },
	    on404: function(t) {
	        alert('Error 404: location "' + t.statusText + '" was not found.');
	    },
	    onFailure: function(t) {
	        alert('Error ' + t.status + ' -- ' + t.statusText);
	    }
	}	
	new Ajax.Request(site_path+'ajax/post/get', opt);		
} // editPost

function deletePost(post_type) {	
	var opt = {	    
	    method: 'post',
	    postBody: 'post_id='+$F(post_type+'_post_id_id')+'&posttype='+post_type,
	    onSuccess: function(t) {
			var ajaxResponse = Try.these(
			function() { return new DOMParser().parseFromString(t.responseText, 'text/xml'); },
			function() { var xmldom = new ActiveXObject('Microsoft.XMLDOM'); 
				xmldom.loadXML(t.responseText); return xmldom; }
			);			
			var result =
				ajaxResponse.getElementsByTagName('result')[0].firstChild.nodeValue;			
			if (result == "good") {
				var url =
					ajaxResponse.getElementsByTagName('url')[0].firstChild.nodeValue;			
				window.location = url;
			} else {
				var msg =
					ajaxResponse.getElementsByTagName('msg')[0].firstChild.nodeValue;
				alert(msg);				
			}
	    },
	    on404: function(t) {
	        alert('Error 404: location "' + t.statusText + '" was not found.');
	    },
	    onFailure: function(t) {
	        alert('Error ' + t.status + ' -- ' + t.statusText);
	    }
	}	
	new Ajax.Request(site_path+'ajax/post/delete', opt);	
} // deletePost

function savePost(post_type) {
	var post_body = '';
	switch (post_type) {
		case "text":
			post_body = Form.serialize('form_post_text');
			break;
		case "quote":	
			post_body = Form.serialize('form_post_quote');
			break;
		case "link":	
			post_body = Form.serialize('form_post_link');
			break;
		case "video":	
			post_body = Form.serialize('form_post_video');
			break;
		case "rss":	
			post_body = Form.serialize('form_post_rss');
			break;
		case "feed":	
			post_body = Form.serialize('form_post_feed');
			break;
	}	
	var opt = {	    
	    method: 'post',
	    postBody: post_body,
	    onSuccess: function(t) {	    	
			var ajaxResponse = Try.these(
			function() { return new DOMParser().parseFromString(t.responseText, 'text/xml'); },
			function() { var xmldom = new ActiveXObject('Microsoft.XMLDOM'); 
				xmldom.loadXML(t.responseText); return xmldom; }
			);			
			var result =
				ajaxResponse.getElementsByTagName('result')[0].firstChild.nodeValue;			
			var post_id =
				ajaxResponse.getElementsByTagName('post_id')[0].firstChild.nodeValue;			
			var url =
				ajaxResponse.getElementsByTagName('url')[0].firstChild.nodeValue;			
			if (result == "good" && post_id >= 1) {
				window.location = url;
			}			
	    },
	    on404: function(t) {
	        alert('Error 404: location "' + t.statusText + '" was not found.');
	    },
	    onFailure: function(t) {
	        alert('Error ' + t.status + ' -- ' + t.statusText);
	    }
	}	
	new Ajax.Request(site_path+'ajax/post/save', opt);		
} // savePost

function sendComment() {
	if (!$F('captcha_id')) {
		var opt = {	    
		    method: 'post',
		    postBody: Form.serialize('form_comment'),
		    onSuccess: function(t) {
				var ajaxResponse = Try.these(
				function() { return new DOMParser().parseFromString(t.responseText, 'text/xml'); },
				function() { var xmldom = new ActiveXObject('Microsoft.XMLDOM'); 
					xmldom.loadXML(t.responseText); return xmldom; }
				);			
				var result =
					ajaxResponse.getElementsByTagName('result')[0].firstChild.nodeValue;			
				if (result == "bad") {
					alert('Something wrong...');
				} else {			
					window.location = window.location;				
				}
		    },
		    on404: function(t) {
		        alert('Error 404: location "' + t.statusText + '" was not found.');
		    },
		    onFailure: function(t) {
		        alert('Error ' + t.status + ' -- ' + t.statusText);
		    }
		}
		new Ajax.Request(site_path+'ajax/comment', opt);		
		return false;
	} else {
		var ret;
		if (!($F('comment_id'))) return false;
		var opt = {	    
		    method: 'post',
		    postBody: 'captcha=' + $F('captcha_id'),
		    onSuccess: function(t) {
				var ajaxResponse = Try.these(
				function() { return new DOMParser().parseFromString(t.responseText, 'text/xml'); },
				function() { var xmldom = new ActiveXObject('Microsoft.XMLDOM'); 
					xmldom.loadXML(t.responseText); return xmldom; }
				);			
				var result =
					ajaxResponse.getElementsByTagName('result')[0].firstChild.nodeValue;			
				if (result == "bad") {
					var msg =
						ajaxResponse.getElementsByTagName('msg')[0].firstChild.nodeValue;							
					if (msg != 'null') alert(msg);
					$('captcha_error').show();
					$('captcha_id').focus();
					return false;
				} else {			
					$('captcha_error').hide();				
					$('new_comment').submit();
				}
		    },
		    on404: function(t) {
		        alert('Error 404: location "' + t.statusText + '" was not found.');
		    },
		    onFailure: function(t) {
		        alert('Error ' + t.status + ' -- ' + t.statusText);
		    }
		}
		new Ajax.Request(site_path+'ajax/captcha/', opt);	
		return false;			
	}
} // sendComment

function deleteComment() {
		var opt = {	    
		    method: 'post',
		    postBody: Form.serialize('form_comment')+'&delete=ok',
		    onSuccess: function(t) {
				var ajaxResponse = Try.these(
				function() { return new DOMParser().parseFromString(t.responseText, 'text/xml'); },
				function() { var xmldom = new ActiveXObject('Microsoft.XMLDOM'); 
					xmldom.loadXML(t.responseText); return xmldom; }
				);			
				var result =
					ajaxResponse.getElementsByTagName('result')[0].firstChild.nodeValue;			
				if (result == "bad") {
					alert('Something wrong...');
				} else {			
					window.location = window.location;				
				}
		    },
		    on404: function(t) {
		        alert('Error 404: location "' + t.statusText + '" was not found.');
		    },
		    onFailure: function(t) {
		        alert('Error ' + t.status + ' -- ' + t.statusText);
		    }
		}
		new Ajax.Request(site_path+'ajax/comment', opt);		
		return false;	
} // deleteComment

function getRss() {
		var opt = {	    
		    method: 'post',
		    postBody: 'feed_id='+$F('my_rss'),
		    onSuccess: function(t) {
				var ajaxResponse = Try.these(
				function() { return new DOMParser().parseFromString(t.responseText, 'text/xml'); },
				function() { var xmldom = new ActiveXObject('Microsoft.XMLDOM'); 
					xmldom.loadXML(t.responseText); return xmldom; }
				);			
				var result =
					ajaxResponse.getElementsByTagName('result')[0].firstChild.nodeValue;			
				if (result == "bad") {
					alert('Something wrong...');
				} else {			
					var url =
						ajaxResponse.getElementsByTagName('rss_url')[0].firstChild.nodeValue;
					var period =
						ajaxResponse.getElementsByTagName('update_period')[0].firstChild.nodeValue;
					var id =
						ajaxResponse.getElementsByTagName('rss_id')[0].firstChild.nodeValue;
					$('rss_url_id').setValue(url);
					$('rss_period_id').setValue(period);
					$('rss_post_id_id').setValue(id);
					$('rss_delete_btn').show();
				}
		    },
		    on404: function(t) {
		        alert('Error 404: location "' + t.statusText + '" was not found.');
		    },
		    onFailure: function(t) {
		        alert('Error ' + t.status + ' -- ' + t.statusText);
		    }
		}
		new Ajax.Request(site_path+'ajax/rssfeed', opt);		
		return false;	
} // getRss

function rssUpdater() {
		var opt = {	    
		    method: 'post',
		    postBody: '',
		    onSuccess: function(t) {},
		    on404: function(t) {},
		    onFailure: function(t) {}
		}
		new Ajax.Request(site_path+'ajax/rssupdater', opt);		
		return true;		
} // rssUpdater

/* Функция вывода метки времени Unix в человеческом формате. Яваскрипт нужен для того, чтобы учесть временной пояс пользователя. */
function TimestampToHuman(TmSt, DateOnly){
	var Today = new Date();
	var theDate = new Date(TmSt * 1000);
	var Month = new Array(12);
	var FH;
	var FM;
	var Result = "";

	Month[0] = "января";
	Month[1] = "февраля";
	Month[2] = "марта";
	Month[3] = "апреля";
	Month[4] = "мая";
	Month[5] = "июня";
	Month[6] = "июля";
	Month[7] = "августа";
	Month[8] = "сентября";
	Month[9] = "октября";
	Month[10] = "ноября";
	Month[11] = "декабря";

	if(theDate.getHours().toString().length == 1){
		FH = "0" + theDate.getHours();
	} else {
		FH = theDate.getHours();
	}

	if(theDate.getMinutes().toString().length == 1){
		FM = "0" + theDate.getMinutes();
	} else {
		FM = theDate.getMinutes();
	}

	if(Today.getDate() != theDate.getDate() || Today.getMonth() != theDate.getMonth() || Today.getFullYear() != theDate.getFullYear()){
		Result = theDate.getDate() + " " + Month[theDate.getMonth()];
	}

	if(Today.getFullYear() != theDate.getFullYear()){
		Result += " " + theDate.getFullYear();
	}

	if(!DateOnly) {
		if(Result) Result += ",";
		Result += " " + FH + ":" + FM;
	}

	return Result;
}  // TimestampToHuman