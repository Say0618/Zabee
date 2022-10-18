$('.miniCartBtn').click(function(){
	clearCounter();
});

$(".item_qty").on('keydown keypress', function (e) {
    if (e.keyCode == 13) {
        return false;
    }
});


if(localStorage.itemCounter){
	$('.count-scolor').html(localStorage.itemCounter);
}

function clearCounter(){
	localStorage.itemCounter = ''
	$('.count-scolor').html('');
}