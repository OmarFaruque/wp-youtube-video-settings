
jQuery(document).ready(function($){
  
  
  //jQuery('iframe').attr('id','iframe1');

  jQuery(document).find('iframe').each(function(v, k){
    jQuery(this).attr('class','iframe');
    jQuery(this).attr('id','iframe'+v);
  });



  //youtube script
  var tag = document.createElement('script');
  tag.src = "//www.youtube.com/iframe_api";
  var firstScriptTag = document.getElementsByTagName('script')[0];
  firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

  var playerDivs = document.querySelectorAll(".iframe"); // get .player nodes
  var playerDivsArr = [].slice.call(playerDivs); // nodelist to array to use forEach();
  var players = new Array(playerDivsArr.length); // gets the yt-player objects
  var waypoints = new Array(playerDivsArr.length);

 // console.log(waypoints);

  // when youtube stuff is ready
  onYouTubeIframeAPIReady = function () {
    
    // create yt players
    playerDivsArr.forEach(function(e, i) { // forEach ...
      
      players[i] = new YT.Player(e.id, {

        events: {
          'onReady': onPlayerReady,
          'onStateChange': onPlayerStateChange
          }

      });

    });
    
  }

  onPlayerStateChange = function (event) {
    // console.log(event.target.f);
    players.forEach(function(yt, i) {

      var thisiframe = event.target.f.id;
      
      if (event.data == YT.PlayerState.ENDED) {
          jQuery('#'+thisiframe).next('.start-video').fadeIn('normal');
      }

      else if (event.data == YT.PlayerState.PLAYING) {
        
        jQuery('#'+thisiframe).next(".start-video").attr("style", "display: none;");
        //jQuery('#'+thisiframe).closest('div.oembed').find(".video_play_op").attr("style", "cursor: unset;");
        //console.log(thisiframe);
        playing = true;
      }

      else if(event.data == YT.PlayerState.PAUSED){
        //console.log(event.target.f.id);

        jQuery('#'+thisiframe).next(".start-video").attr("style", "display: block;"); 
        jQuery('#'+thisiframe).closest('div.oembed').find(".video_overly_ch_m1").attr("style", "z-index: 9; background-color: transparent;");
      }

    });

  }
  function onPlayerReady(event) {
    
    players.forEach(function(yt, i) {

      jQuery(document).on('click', '.start-video', function (e) {
        //console.log(players[i].f.id);
        var thisfid = jQuery(this).prev('iframe').attr('id');
       // console.log('this event id: ' + thisfid);

        jQuery(this).fadeOut('normal');
        jQuery(this).attr("style", "display: none;");

        if(thisfid == players[i].f.id){
          players[i].playVideo();
        }
      });

      jQuery(document).on('click', '.video_play_op', function (e) {
  
        var thisfid = jQuery(this).closest('div.oembed').find('iframe').attr('id');
        //console.log(thisfid);

        if(thisfid == players[i].f.id){
          players[i].pauseVideo();
        }
      });

    });

  }


  

  var all_url = youtubeAjax['all_url'];
  //console.log(all_url);
  //console.log(all_url.length);

  for (i = 0; i < all_url.length; i++) {

    var all_url_arr = all_url[i].split(".");
    var all_url_var = all_url_arr[1];

    //console.log(all_url_var);

    jQuery('a').each(function(v, k){

      var href = jQuery(this).attr('href');
      var href_arr = href.split(".");
      var href_var = href_arr[1];

      //console.log(all_url_var);

      if(all_url_var.includes(href_var)){
          this.remove();
          //this.closest('li').remove();
      }

    });
  }

});

!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src='https://weatherwidget.io/js/widget.min.js';fjs.parentNode.insertBefore(js,fjs);}}(document,'script','weatherwidget-io-js');
