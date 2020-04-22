jQuery(document).ready(function($){

    //button 
    jQuery(document.body).on('change', 'input[name="custom_button"]', function(){
    
      var value = 0;
      if(jQuery(this).is(':checked')) value = 1;

      console.log('value: ' + value);
      jQuery.ajax({
          type : 'post',
          dataType: 'json',
          data : {
          'value'               : value,
          'action'              : 'custom_buttonfunction' 
          },
          url : youtubeAjax,
          success:function(data){
              if(data.message == 'success'){
                  console.log(data);
              }
          }
      });
    });
    
    //button 
    jQuery(document.body).on('change', 'input[name="enable_full_screen"]', function(){

      var value = 0;
      if(jQuery(this).is(':checked')) value = 1;

      console.log('value: ' + value);
      jQuery.ajax({
          type : 'post',
          dataType: 'json',
          data : {
          'value'               : value,
          'action'              : 'enable_full_screenfunction' 
          },
          url : youtubeAjax,
          success:function(data){
              if(data.message == 'success'){
                  console.log(data);
              }
          }
      });
    });

    //image uploder
    var mediaUploader;
  
    $('#upload-button').click(function(e) {
      e.preventDefault();
      // If the uploader object has already been created, reopen the dialog
        if (mediaUploader) {
        mediaUploader.open();
        return;
      }
      // Extend the wp.media object
      mediaUploader = wp.media.frames.file_frame = wp.media({
        title: 'Choose Image',
        button: {
        text: 'Choose Image'
      }, multiple: false });
  
      // When a file is selected, grab the URL and set it as the text field's value
      mediaUploader.on('select', function() {
        attachment = mediaUploader.state().get('selection').first().toJSON();
        $('#image-url').val(attachment.url);
      });
      // Open the uploader dialog
      mediaUploader.open();
    });

});




  