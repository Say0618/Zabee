function ajaxRequest(data, url, successCallback, type){
	$.ajax({
		url: url,
		type: "POST",
		dataType: type,
		data: data,
		success: function(data, textStatus, XMLHttpRequest) {
			successCallback(data, textStatus, XMLHttpRequest);
		},
		error: function(xhr, ajaxOptions, thrownError){
			//showModal('Error', 'Error, please try again', false);
			console.log(xhr);
			console.log(ajaxOptions);
			console.log(thrownError);
			//errorCallback(xhr, ajaxOptions, thrownError);
		}
	});
}

function showModal(title, body, isElement){
	if(isElement){
		$('#notification').find('.modal-body').html(body);
	} else {
		$('#notification').find('.modal-body p').html(body);
	}
	$('#notification').find('.modal-title').html(title);
	if($('#notification .modal-fullscreen').length > 0){
		$('#notification .modal-fullscreen').addClass('modal-dialog').removeClass('modal-fullscreen');
	}
	$('#notification').modal('show');
}

$(document).on('hide.bs.modal','#notification', function () {
	$('#notification').find('.modal-body').html('<p></p>');
	$('#notification').find('.modal-title').html('');
});

function isValidEmailAddress(emailAddress) {
	var pattern = new RegExp(
			/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
	return pattern.test(emailAddress);
}

function CheckOutRender(element, data) {
    this.data = data;
    this.element = element;
    element.innerText = data;
    element.addEventListener("change", this, false);
}
function getCookie(cname) {
	var name = cname + "=";
	var decodedCookie = decodeURIComponent(document.cookie);
	var ca = decodedCookie.split(';');
	for(var i = 0; i <ca.length; i++) {
	  var c = ca[i];
	  while (c.charAt(0) == ' ') {
		c = c.substring(1);
	  }
	  if (c.indexOf(name) == 0) {
		return c.substring(name.length, c.length);
	  }
	}
	return "";
  }
  
  function getUTCDateTime()
  {
	  var UTCDateTime = new Date().toISOString().slice(0, 19).replace('T', ' ');
	  return UTCDateTime;
  }
  function getDateTime(date, raw){
	  if(!raw)
		  var CurrDateTime = Date.create(new Date(date)).format('{Mon} {dd}, {yyyy} {hh}:{mm} {TT}');
	  else
		  var CurrDateTime = Date.create(new Date(date)).format('{yyyy}/{MM}/{dd} {HH}:{mm}:{ss}');
	  return CurrDateTime;
  }

function getDatesBetweenTwoDates(from_date, to_date, chart_data){
	var dates = [];
	var data = [];
	var response = [];
	from_date = new Date(from_date).getTime();
	to_date = new Date(to_date).getTime();
	var k = 0;
	var m = 0;
	for(var i = from_date;i<to_date;){
		date = new Date(from_date).addDays(k);
		if(date){
			if(chart_data.length > m && new Date(chart_data[m].date_time).getTime() == date.getTime()){
				data.push(chart_data[m].daily_views);
				m++;
			} else {
				data.push(0);
			}
			dates.push(date.format('{Mon} {dd}, {yyyy}'));
		}
		i = date.getTime();
		k++;
	}
	response.dates = dates;
	response.data = data;
	return response;
}