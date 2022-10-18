<script src="<?php echo assets_url('common/js/sugar.js'); ?>"></script>
<script src="<?php echo assets_url('plugins/chart.js/chart.js/dist/Chart.min.js'); ?>"></script>
<script>
var pageLink = window.location.protocol+'//'+window.location.host+window.location.pathname;
var oTable;
<?php if($chart_data['status'] == 1 && count($chart_data['data']) > 0) { ?>
var chartData = JSON.parse('<?php echo json_encode($chart_data['data']); ?>');
var ctx = document.getElementById('myChart');
var d1 = new Date('<?php echo $startDate; ?>').reset();
var d2 = new Date('<?php echo $endDate; ?>').reset();
var response = getDatesBetweenTwoDates(d1,d2,chartData);
var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: response.dates,
        datasets: [{
            label: 'Views per day',
            data: response.data,
        }]
    },
    options: {
		title: {
            display: true,
            text: 'Product views in ast 30 days'
        },
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true
                },
				display: true
            }]
        }
    }
});
<?php } ?>
$(document).ready(function(){
	 $("#endRange").datepicker({ 
        numberOfMonths: 1,
		maxDate: 0,
        onSelect: function(selected) {
           $("#txtFromDate").datepicker("option","maxDate", selected)
		   if(selected !=""){
				$("#endRange-error").remove();
			}
        }
    });
	$("#startRange").datepicker({ 
        numberOfMonths: 1,
		maxDate: 0,
        onSelect: function(selected) {
           $("#endRange").datepicker("option","minDate", selected)
		   if(selected !=""){
				$("#startRange-error").remove();
			}
        }
    });
	oTable = $('.datatables').dataTable({
		"dom": 'Bfrtip',
		"buttons": [
            'copy', 'csv', 'excel', 'pdf'
        ],
		"aaSorting": [[1, "desc"]],
		"aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
		"iDisplayLength": 100,
		'processing': true,
		'serverSide': true,
		'serverMethod': 'post',
		//'searching': false, // Remove default Search Control
		'ajax': {
			'url':'<?=base_url()?>seller/analytics/get',
			'data': function(data){
				data.startDate = ($('#startRange').val() == '')?'':$('#startRange').val();
				data.endDate = ($('#endRange').val() == '')?'':$('#endRange').val();
			}
		},
		'columns': [
			{ data: 'sno' ,"bSortable": false},
			{ data: 'date_time', "bSortable": true},
			{ data: 'product_name' ,"bSortable": false },
			{ data: 'store_name' ,"bSortable": true },
			{ data: 'product_views' ,"bSortable": true }
		]
	});
	$('#generateReport').click(function(){
		var startDate = $("#startRange").val();
		var endDate = $("#endRange").val();
		if(startDate != '' && endDate != '')
			oTable.fnDraw();
		else 
			$('#error_report').text('Select date range first');
	});
});

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
</script>