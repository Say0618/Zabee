var getUrl = window.location;
var parameter = "open";
var chatBackgroundColor = "#f3f3f3";
var isSafari = /constructor/i.test(window.HTMLElement) || (function (p) { return p.toString() === "[object SafariRemoteNotification]"; })(!window['safari'] || safari.pushNotification);
var baseUrl = getUrl .protocol + "//" + getUrl.host;//+"/" +getUrl.pathname.split('/')[1];
/*if(getUrl.pathname.split('/')[1] == "admin"){
	baseUrl += "/admin";
}*/
//var baseUrl = server;//getUrl .protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1]+ "/" + getUrl.pathname.split('/')[2];
if(getUrl.pathname.split('/')[1] == "seller"){
	baseUrl += "/seller";
	chatBackgroundColor = "#2f333e";
}
/*var baseUrl = getUrl.protocol + "//" + getUrl.host;
if(getUrl.pathname.split('/')[1] == "seller"){
	baseUrl += "/seller";
	chatBackgroundColor = "#2f333e";
}*/
var s_id = "";
var loadLimit =0;
var checkMessageLength = 0;
$(document).ready(function(){
	/*$(".close").click(function(){
        $("#info").animate({left:"+=10px"}).animate({left:"-5000px"});
    });*/
	$("#alert-info").animate({left:"+=5px"},40).animate({top:"+=5px"},40)
    .animate({top:"-=10px"},40).animate({left:"-=10px"},40)
    .animate({top:"+=5px"},40).animate({left:"+=5px"},40)
    .animate({left:"+=5px"},40).animate({top:"+=5px"},40)
    .animate({top:"-=10px"},40).animate({left:"-=10px"},40)
    .animate({top:"+=5px"},40).animate({left:"+=5px"},40);
	$(".media.conversation small").each(function () {
		text = $(this).text();
		if (text.length > 25) {
			$(this).html(text.substr(0, 25) + '<span class="elipsis">' + text.substr(25) + '</span>...');
		}
	});
	function getParameterByName(name, url) {
		if (!url) url = window.location.href;
		name = name.replace(/[\[\]]/g, "\\$&");
		var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
			results = regex.exec(url);
		if (!results) return null;
		if (!results[2]) return '';
		return atob(results[2].replace(/\+/g, " "));
	}
	var checkGet = getParameterByName(parameter,getUrl);
	if(checkGet){
		checkGet = checkGet.split(',');
		if(checkGet.length == 8){
			checkGet[1] = (checkGet[1])?checkGet[1]:0;
			openChat(checkGet[0],checkGet[1],checkGet[2],checkGet[3],checkGet[4],checkGet[5],checkGet[6],checkGet[7])
		}
	}
	/*$(".media.conversation small").click(function (e) {
		e.preventDefault(); //prevent '#' from being added to the url
		$(this).prev('span.elipsis').fadeToggle(500);
	});*/
	//$(".msg-wrap").scrollTop($(".msg-wrap")[0].scrollHeight);
});
function loadPreviousMessage(){
	if(checkMessageLength == 0){
		var checkDateClass;
		loadLimit = loadLimit+7;
		$('#loader').show();
		$.ajax({
			type: "POST",
			cache:false,
			dataType: "JSON",
			async:true,
			url: baseUrl+'/message/getMessages',
			data: {'sender_id':$("#sender_id").val(),'desc':1,'loadLimit':loadLimit,'product_variant_id':$("#product_variant_id").val(),'item_type':$("#item_type").val()},
			success:function(response){
				//console.log(response);
				if(response.status == 1){
					var data = response.messages;
					var dateClass = "";
					for (var message in data) {
						dateClass = message.replace(/ /gi, "");
						checkDateClass = 0;
						checkNoToday = $('.msg-date strong').filter(function() {
							return $(this).text().trim() != message;
						});
						checkToday = $('.msg-date strong').filter(function() {
							return $(this).text().trim() == message;
						});
						/*for (var who in data[message].messages) {
							$(data[message].messages[who]).each(function(index, element) {
								insertChat(element.who, element.text, element.sender_name, element.pic, element.sendtime,1)
							});
						}*/
						for (var who in data[message].messages) {
							if(checkNoToday.length > 0 && checkToday.length ==0 && checkDateClass==0){
								$("div.msg-wrap").prepend('<div class="alert alert-info msg-date '+dateClass+'"><strong>'+message+'</strong></div>');
							}
							$(data[message].messages[who]).each(function(index, element) {
								if(isSafari){
									element.sendtime = element.sendtime.replace(/-/g,'/')
									date = new Date(element.sendtime+" UTC");
									date =  new Date(date.toString());
								}else{
									date = new Date(element.sendtime+" UTC");
									date =  new Date(date.toString());
								}
								
								insertChat(element.who, element.text, element.sender_name, element.pic,date,1,dateClass)
							});
							checkDateClass++;
						}
					}
					$('.loadPreviousMessage').remove();
					$("div.msg-wrap").prepend('<div class="alert alert-info msg-date loadPreviousMessage"><a href="javascript:" class="loadPreviousMessage" onClick="loadPreviousMessage()">Load Previous Messages</a></div>');
				}else{
					checkNoToday = $('.msg-date strong').filter(function() {
						return $(this).text().trim() != "No previous message.";
					});
					checkToday = $('.msg-date strong').filter(function() {
						return $(this).text().trim() == "No previous message.";
					});
					if(checkNoToday.length > 0 && checkToday.length ==0){
						$("div.msg-wrap").prepend('<div class="alert alert-info msg-date"><strong style="color:rebeccapurple">No previous message.</strong></div>');
						$('.loadPreviousMessage').remove();
					}
					
					checkMessageLength = 1;
				}
				//$('.inner').prepend(data);
				$('#loader').hide();
				$('div.msg-wrap').scrollTop(30); // Scroll alittle way down, to allow user to scroll more
			}
		});
	}
}

function formatAMPM(date) {
    var hours = date.getHours();
    var minutes = date.getMinutes();
    var ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12; // the hour '0' should be '12'
    minutes = minutes < 10 ? '0'+minutes : minutes;
    var strTime = hours + ':' + minutes + ' ' + ampm;
    return strTime;
} 

//-- No use time. It is a javaScript effect.
function insertChat(who, text, name,pic, date,pre,dateClass){
	if(text !=""){
		var control = "";
		if(date == ""){ 
			date = formatAMPM(new Date());
		}else{
			date = formatAMPM(date);
		}
		if (who == "me"){
			
			control = 	'<div class="media-body mb-2 text-right">'+
								//'<a href="javascript:void(0)"><img class="media-object" style="width: 32px; height: 32px;" src="'+pic+'"></a>'+
								//'<h5 class="media-heading">'+name+'</h5>'+
								'<span class="receiver msg" style="background:#f3f3f3;border-radius: 5px 5px 0px 5px;">'+text+'</span>'+
								'<br/><span class="time my-1"><i class="fas fa-check-double"></i> '+date+'</span>'+
							'</div>';                       
		}else{
			control = 	
							'<div class="media-body mb-2">'+
								//'<a href="javascript:void(0)" class="mb-1"><img class="media-object" style="width: 32px; height: 32px;" src="'+pic+'"></a>'+
								//'<h5 class="media-heading sender">'+name+'</h5>'+
								'<span class="sender msg" style="background:'+chatBackgroundColor+';border-radius: 5px 5px 5px 0px;">'+text+'</span>'+
								'<br/><span class="time sender my-1"><i class="fas fa-check-double"></i> '+date+'</span>'+
							'</div>';                  
		}
		if(pre !=1){
			$("div.msg-wrap").append(control).fadeIn('slow');
			$("div.msg-wrap").animate({scrollTop: $('div.msg-wrap').prop("scrollHeight")}, 100);
		}else{
			//if(dateClass == "Today"){
				 $(control).insertAfter('.'+dateClass);
			//}else{
			//	$("div.msg-wrap").prepend(control).fadeIn('slow');
			//}
		}
	}
}

/*function resetChat(){
    $("ul#chat_ul").empty();
}*/
$(".send-message").click(function(){
    $('div#alert-info').alert('close');
});

$(".send-message-btn").click(function(event){
	event.preventDefault();
	text = $(".send-message").val();
	seller_id = $("#seller_id").val();
	if(text ==""){
		alert('Please type a text.');
	}else if(text !="" && seller_id !=""){
		name = $("#name").val();
		pic = $("#user_pic").val();
		sender_id = $("#userid").val();
		product_variant_id = $("#product_variant_id").val();
		item_type = $("#item_type").val();
		seller_id = $("#seller_id").val();
		buyer_id = $("#buyer_id").val();
		item_id = $("#item_id").val();
		product_variant_id = (product_variant_id)?product_variant_id:0;
		//var c_time = new Date().getTime();
		//var UTCDateTime = new Date(c_time).toUTCString().slice(0, -4);
		var UTCDateTime = new Date().toISOString().slice(0, 19).replace('T', ' ');
		checkNoToday = $('.msg-date strong').filter(function() {
			return $(this).text().trim() != 'Today';
		});
		checkToday = $('.msg-date strong').filter(function() {
			return $(this).text().trim() == 'Today';
		});
		if(checkNoToday.length > 0 && checkToday.length ==0){
			$("div.msg-wrap").append('<div class="alert alert-info msg-date Today"><strong>Today</strong></div>');
		}
		$(".conversation-wrap").prepend($("#media-"+s_id+'-'+product_variant_id+'-'+item_type))
		$("#message-"+s_id+"-"+product_variant_id+"-"+item_type).text(text);
		$.ajax({
			type: "POST",
			cache:false,
			dataType: "JSON",
			async:true,
			url: baseUrl+'/message/saveMessage',
			data:{'receiver_id':s_id,'message':text,'product_variant_id':product_variant_id,'item_type':item_type,"seller_id":seller_id,"buyer_id":buyer_id,'item_id':item_id,'time':UTCDateTime},
			success: function(response){
				if(response.status == 1){
					console.log('save');
				}
			}
		});
		insertChat('me',text,name,pic,"",0,'Today');
		$(".send-message").val('');
	}else{
		alert("Please Select User");
	}
});
function openChat(sender_id,product_variant_id,item_type,seller_id,buyer_id,item_id,product_link,receiver_id){
	s_id = sender_id;
	loadLimit = 0;
	checkMessageLength = 0;
	var utcDT = new Date().toISOString().slice(0, 19).replace('T', ' ');
	var seentime = $('#media-'+s_id+'-'+product_variant_id+'-'+item_type).attr('data-seentime');
	if(seentime == ""){
		var textNotification = $("#textNotification").html();
		$('#notification-'+s_id+'-'+product_variant_id+'-'+item_type).remove();
		if(textNotification != ""){
			if(textNotification == 1){
				$("#textNotification").html("");
			}else{
				textNotification = textNotification-1;
				$("#textNotification").html(textNotification);
			}
		}
	}
	var dateClass = "";
	$('#chatPanel').text('');
	$('#chatPanel').removeClass('invisible');
	//$('#chatPanel').text($('#media-'+sender_id+'-'+product_variant_id+'-'+item_type).find('.media-heading').text()+"'s Message");
	$('#chatPanel').text(product_link);
	$("#sender_id").val(sender_id);
	$('#seller_id').val(seller_id);
	$('#buyer_id').val(buyer_id);
	$('#item_id').val(item_id);
	$("#product_variant_id").val(product_variant_id);
	$("#item_type").val(item_type);
	$('.media').removeClass('active');
	$('#media-'+s_id+'-'+product_variant_id+'-'+item_type).addClass('active');
	$("div.msg-wrap").html('');
	$("#preloadView2").show();	
	$("div.msg-wrap").append('<div class="alert alert-info msg-date loadPreviousMessage"><a href="javascript:"  onClick="loadPreviousMessage()">Load Previous Messages</a></div>');
	$.ajax({
		type: "POST",
		cache:false,
		dataType: "JSON",
		async:true,
		url: baseUrl+'/message/getMessages',
		data:{'sender_id':sender_id,'product_variant_id':product_variant_id,'item_type':item_type},
		success: function(response){
			if(response.status == 1){
				$("#user_pic").val(response.user_pic);
				var data = response.messages;
				for (var message in data) {
					dateClass = message.replace(/ /gi, "");
					$("div.msg-wrap").append('<div class="alert alert-info msg-date '+dateClass+'"><strong>'+convertDate(message)+'</strong></div>');
					//console.log(message);
					for (var who in data[message].messages) {
						$(data[message].messages[who]).each(function(index, element) {
							/*if(isSafari){
								element.sendtime = element.sendtime.replace(/-/g,'/')
								date = new Date(element.sendtime+" UTC");
								date =  new Date(date.toString());
							}else{*/
								element.sendtime = element.sendtime.replace(/-/g,'/')
								date = new Date(element.sendtime+" UTC");
								date =  new Date(date.toString());
							//}
							insertChat(element.who, element.text, element.sender_name, element.pic,date,0,dateClass)
                        });
					}
				}
				$("#preloadView2").hide();	
			}else{
				$("div.msg-wrap").append('<div class="alert alert-info msg-date">'+response.error+'</div>');
				$("#preloadView2").hide();
			}
		}
	});
	$.ajax({
		type: "POST",
		cache:false,
		dataType: "JSON",
		async:true,
		url: baseUrl+'/message/saveSeenTime',
		data:{'receiver_id':receiver_id,'product_variant_id':product_variant_id,'item_type':item_type,"seller_id":seller_id,"buyer_id":buyer_id,'item_id':item_id,'seen_datetime':utcDT},
		success: function(response){
			if(response.status == 1){
				console.log('save');
			}
		},error:function(xhr, status, error){
			var err = eval("(" + xhr.responseText + ")");
		  	alert(err.Message);
		}
	});
	$('.message-wrap').removeClass('d-none');
}
function convertUTCDateToLocalDate(date) {
    var newDate = new Date(date.getTime()+date.getTimezoneOffset()*60*1000);
	//newDate.setTime(date.getTime()+date.getTimezoneOffset()*60*1000);
    var offset = date.getTimezoneOffset() / 60;
    var hours = date.getHours();

    newDate.setHours(hours - offset);

    return newDate;   
}

function convertDate(date){
	date = date.split(" ");
	return date[1]+" "+date[0]+", "+date[2];
}
function imageExist(url) {
   var img = new Image();
   img.src = url;
   return img.height != 0;
}
/*$(".mytext").on("keyup", function(e){
    if (e.which == 13){
        var text = $(this).val();
        if (text !== ""){
            insertChat("me", text,3000);              
            $(this).val('');
        }
    }
});*/

//-- Clear Chat
//resetChat();

//-- NOTE: No use time on insertChat.