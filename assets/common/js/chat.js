var getUrl = window.location;
/*var baseUrl = getUrl .protocol + "//" + getUrl.host;
if(getUrl.pathname.split('/')[1] == "admin"){
	baseUrl += "/admin";
}*/
var baseUrl = getUrl .protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1]+ "/" + getUrl.pathname.split('/')[2];
console.log(getUrl.pathname.split('/')[3]);
if(getUrl.pathname.split('/')[3] == "seller"){
	baseUrl += "/seller";
}
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
	/*$(".media.conversation small").click(function (e) {
		e.preventDefault(); //prevent '#' from being added to the url
		$(this).prev('span.elipsis').fadeToggle(500);
	});*/
	//$(".msg-wrap").scrollTop($(".msg-wrap")[0].scrollHeight);
});
function loadPreviousMessage(){
	if(checkMessageLength == 0){
		loadLimit = loadLimit+7;
		$('#loader').show();
		$.ajax({
			type: "POST",
			cache:false,
			dataType: "JSON",
			async:true,
			url: baseUrl+'/message/getMessages',
			data: {'sender_id':$("#sender_id").val(),'desc':1,'loadLimit':loadLimit},
			success:function(response){
				console.log(response);
				if(response.status == 1){
					var data = response.messages;
					for (var message in data) {
						checkNoToday = $('.msg-date strong').filter(function() {
							return $(this).text().trim() != message;
						});
						checkToday = $('.msg-date strong').filter(function() {
							return $(this).text().trim() == message;
						});
						for (var who in data[message].messages) {
							$(data[message].messages[who]).each(function(index, element) {
								insertChat(who, element.text, element.sender_name, element.pic, element.sendtime,1)
							});
						}
						if(checkNoToday.length > 0 && checkToday.length ==0){
							$("div.msg-wrap").prepend('<div class="alert alert-info msg-date"><strong>'+message+'</strong></div>');
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
function insertChat(who, text, name,pic, date,pre){
	if(text !=""){
		var control = "";
		if(date == ""){ 
			date = formatAMPM(new Date());
		}
		if (who == "me"){
			
			control = '<div class="media msg" style="background:#5e5e69;">'+
						'<a class="pull-right" href="#">'+
							'<img class="media-object" style="width: 32px; height: 32px;" src="'+pic+'">'+
					   '</a>'+
						'<div class="media-body text-right">'+
							'<small class="pull-left time"><i class="fa fa-clock-o"></i> '+date+'</small>'+
							'<h5 class="media-heading">'+name+'</h5>'+
							'<small class="">'+text+'</small>'+
						'</div>'+
					'</div>';                    
		}else{
		   control = '<div class="media msg" style="background:#2f333e;">'+
						'<a class="pull-left" href="#">'+
							'<img class="media-object" style="width: 32px; height: 32px;" src="'+pic+'">'+
					   '</a>'+
						'<div class="media-body">'+
							'<small class="pull-right time"><i class="fa fa-clock-o"></i> '+date+'</small>'+
							'<h5 class="media-heading">'+name+'</h5>'+
							'<small class="">'+text+'</small>'+
						'</div>'+
					'</div>';                    
		}
		if(pre !=1){
			$("div.msg-wrap").append(control).fadeIn('slow');
			$("div.msg-wrap").animate({scrollTop: $('div.msg-wrap').prop("scrollHeight")}, 100);
		}else{
			$("div.msg-wrap").prepend(control).fadeIn('slow');
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
	if(text !=""){
		name = $("#name").val();
		pic = $("#user_pic").val();
		sender_id = $("#userid").val();
		checkNoToday = $('.msg-date strong').filter(function() {
			return $(this).text().trim() != 'Today';
		});
		checkToday = $('.msg-date strong').filter(function() {
			return $(this).text().trim() == 'Today';
		});
		if(checkNoToday.length > 0 && checkToday.length ==0){
			$("div.msg-wrap").append('<div class="alert alert-info msg-date"><strong>Today</strong></div>');
		}
		$(".conversation-wrap").prepend($("#media-"+s_id))
		$("#message-"+s_id).text(text);
		$.ajax({
			type: "POST",
			cache:false,
			dataType: "JSON",
			async:true,
			url: baseUrl+'/message/saveMessage',
			data:{'receiver_id':s_id,'message':text},
			success: function(response){
				if(response.status == 1){
					console.log('save');
				}
			}
		});
		insertChat('me',text,name,pic,"",0);
		$(".send-message").val('');
	}
});
function openChat(sender_id){
	s_id = sender_id;
	loadLimit = 0;
	checkMessageLength = 0;
	$('#chatPanel').text('');
	$('#chatPanel').removeClass('hide');
	$('#chatPanel').text($('#media-'+sender_id).find('.media-heading').text()+"'s Message");
	$("#sender_id").val(sender_id);
	$('.media').removeClass('active');
	$('#media-'+s_id).addClass('active');
	$("div.msg-wrap").html('');
	$("#preloadView2").show();	
	$("div.msg-wrap").append('<div class="alert alert-info msg-date loadPreviousMessage"><a href="javascript:"  onClick="loadPreviousMessage()">Load Previous Messages</a></div>');
	$.ajax({
		type: "POST",
		cache:false,
		dataType: "JSON",
		async:true,
		url: baseUrl+'/message/getMessages',
		data:{'sender_id':sender_id},
		success: function(response){
			if(response.status == 1){
				var data = response.messages;
				for (var message in data) {
					$("div.msg-wrap").append('<div class="alert alert-info msg-date"><strong>'+message+'</strong></div>');
					for (var who in data[message].messages) {
						$(data[message].messages[who]).each(function(index, element) {
                			insertChat(who, element.text, element.sender_name, element.pic, element.sendtime,0)
                        });
					}
				}
				$("#preloadView2").hide();	
			}
		}
	});
	$('.message-wrap').removeClass('hide');
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