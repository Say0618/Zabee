
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> -->
 <script src="<?php echo assets_url('plugins/jquery.nestable.js')?>"></script>
 <script type="text/javascript">
 var menuItems = [];
 var updateOutput;
$(document).ready(function()
{
    $("#my_form").validate({
        rules: {
            parent:{
                    required: true,
            },
            menu_link:{
                    required: true,
            },
            name: {
                required: {
                    depends:function(){
                        $(this).val($.trim($(this).val()));
                        return true;
                    }
                },
               // name: true
            },
            menu_link: {
                required: {
                    depends:function(){
                        $(this).val($.trim($(this).val()));
                        return true;
                    }
                },
                //url: true,
               // menu_link: true
            },
            },
            messages:{
				name:{
					required: "Please Enter Name.",
					
                },
                parent:{
					required: "Please Enter Parent  Menu.",
					
                },
                menu_link:{
					required: "Please Enter Menu Link.",
                   // url: "Proper link required.",
					
                },
            },
        });


    updateOutput = function(e)
    {
        var list   = e.length ? e : $(e.target),
            output = list.data('output');
        //console.log(output);
        if (window.JSON) {
            menuItems = JSON.stringify(list.nestable('serialize'));
            output.val(window.JSON.stringify(list.nestable('serialize')));//, null, 2));
        } else {
            output.val('JSON browser support required for this demo.');
        }
    };

    $('#nestable').nestable({
        group: 1
    })
    .on('change', updateOutput);
    updateOutput($('#nestable').data('output', $('#nestable-output')));


});
function remove_menu(id){
   $("." + id).parent().remove();
   }



  
  function save() { 
    var data = new Array();
    var order = [];
    var count ,position = 0;
    $('.dd-item').each(function(i) {
       data.push({id:$(this).attr("id"),menu_order:$(this).attr("data-menu_order"),parent_id:$(this).attr("data-parent_id")});
       //order.push($(this).attr("data-menu_order"));
    });
    
    $.ajax({
        url:'<?php echo base_url('seller/dashboard/save_list'); ?>',
        type:'post',
        data:{data:data},
        success:function(data){
           alert('Your changes were successfully saved');
        }
    })
  }
  /*  var submit_button = $('#submit_form');
    submit_button.click(function() {

    var name = $('#name').val(); 
     var parent = $('#parent').val();
     var menu_link = $('#menu_link').val();
    //var end_date = $('seconddate').val();

    var data = 'name=' + name +'&parent='+ parent +'&menu_link='+ menu_link;
    $.ajax({

        type: 'POST',
        url:  '<?php echo base_url('seller/dashboard/create_menu_process'); ?>',
        //dataType : "json",
        data: data, 


        success : function(data)
        { //probably this request will return anything, it'll be put in var "data"
        		 console.log(data);
              var container = $('#container'); //jquery selector (get element by id)
               if(data)
               {
              container.html(data);
               }
        }
            });
                return false;
                  //alert("success");
                  // e.preventDefault(); // could also use: return false;
              });
   // var data = new Array();
   //  $('.dd-item').each(function() {
   //     data[($(this).attr("id"))] = {
   //      'title': $(this).attr("data-title"),
   //      'link': $(this).attr("data-link")
   //     };
   //  });*/

  
   </script>