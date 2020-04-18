
 jQuery(document).ready(function($){  
 var i=1;
      $('#add').click(function(){ 
      	var html = '';
      	var type = $('input[name="quiz_type"]').val();
        var quiz_id = $('select[name="item_list"]').val();
        var quiz_number =$('input[name="item_number"]').val();
        var selected_text =$('#item_select option:selected').text();

        console.log(quiz_id); 
     
          html+='<li id="li'+i+'">';
              html+='<div class="text_quize_name">';

                html+=selected_text;

              html+='</div>';

              html+='<input type="hidden" value="'+quiz_id+'" name="js_quiz_id[]" />';
              
              html+='<input type="hidden" value="'+quiz_number+'" name="js_item_number[]" />';
              

              html+='<div class="text_quize_id">';

                html+=quiz_number;

		          html+='</div>';  
              html+='<div class="text_quize_button">';  
		            html+='<button type="button" name="remove" id="'+i+'" class="button btn btn-danger btn_remove">X</button>';
		          html+='</div>';
          html+='</li>'; 

				 if( quiz_id != '') $('#dynamic_field ul').append(html); 

         $('#item_select option:eq(0)').prop('selected', true);

      i++; 
      
      }); 

      $(document).on('click', '.btn_remove', function(){  
           var button_id = $(this).attr("id");   
           $('#li'+button_id+'').remove();  
      }); 


       $('#add1').click(function(e){ 
        e.preventDefault();
        $('.single_item.js_dinamic').remove();
        var html = '<div class="single_item js_dinamic"><input type="text" name="new_category" class="ct_name" />';
        $('.add_category').append(html);
      }); 

      $(document).on('click', '.btn_remove', function(){  
           var button_id = $(this).attr("id");   
           $('#cat'+button_id+'').remove();  
      });


             $('#submit').click(function(){            
           $.ajax({   
                url:prefix, 
                method:"POST",  
                data:$('#add_name').serialize(),  
                success:function(data)  
                {  
                     alert(data);  
                     $('#add_name')[0].reset();  
                }  
           });  
      }); 

       
       ////select2
       $('#item_select2').select2();
       ////select2

       //////////////submit ck////////
       $(document.body).on('submit','form#add_name',function(e){
        if(!$('.text_quize_name').length){
          e.preventDefault();
        }
 
       });


 });////end of document
