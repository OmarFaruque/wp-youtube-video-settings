

// jQuery(document).ready(function() {
//     jQuery('.oembed iframe').load(function() {
//         //jQuery(".oembed iframe").contents().find("head").append("<style> </style>"); 
//         jQuery(".oembed iframe").attr("style", "background-color:#F0B30C");
//         //jQuery(".ytp-show-cards-title").empty();
//         jQuery(".ytp-chrome-top.ytp-show-cards-title").attr("style", "display: none;"); 

//     });
// });

    // var head = jQuery("iframe").contents().find("head");
    // var css = '<style type="text/css">' +
    //         '.ytp-show-cards-title{display: none;}; ' +
    //         '</style>';
    // jQuery(head).append(css);

//     window.onload = function() {
//    var iframe = document.getElementsByTagName("iframe");
//    console.log(iframe);
 
//     for (i = 0; i < iframe.length; i++) {

        
//             var frameElement = iframe[i];
//             var doc = (frameElement.contentWindow || frameElement.contentDocument);
//             console.log(doc);
//             // frameElement.body.contentEditable = true;
//             frameElement.body.innerHTML = doc.body.innerHTML + '<style>'
//             + ' .ytp-chrome-top.ytp-show-cards-title {'
//             + ' display: none;'
//             + ' }'
//             + '</style>'
//         }  

//     }

// jQuery('.iframe_image1').click(function(){
//   jQuery(this).hide();
//   jQuery(".button-pl").show();
//   jQuery(".button-pu").show();
//   //jQuery("iframe#iframe1").attr("style", "pointer-events: none;");
// });

// jQuery('iframe#iframe1').click(function(){
//   jQuery("iframe#iframe1").attr("style", "pointer-events: none;");
//   alert('jony');
// });




// // global variable for the player
// var player, playing = false;

// // this function gets called when API is ready to use
// function onYouTubePlayerAPIReady() {
//   // create the global player from the specific iframe (#video)
//   player = new YT.Player('iframe1', {
//     events: {
//       // call this function when player is ready to use
//       'onReady': onPlayerReady,
//       // 'onStateChange': onPlayerStateChange
//     }
//   });
// }


// function onPlayerStateChange(event) {

//   if (event.data == YT.PlayerState.PLAYING) {
//      alert('video started');
//      playing = true;
//     }

//   else if(event.data == YT.PlayerState.PAUSED){
//         alert('video paused');
//         playing = false;
//    }
// }

// function onPlayerReady(event) {
//   event.target.playVideo();
// }

// function onPlayerReady(event) {
  
//   // bind events
//   var playButton = document.getElementById("play-button");
//   playButton.addEventListener("click", function(event) {
//     jQuery(".iframe_image").attr("style", "display: none;");
//     jQuery("iframe#iframe1").attr("style", "display: block; pointer-events: none;");

//     console.log('play-button');
//     player.playVideo();
//     //button.addEventListener('click', () => event.target.playVideo());
    
//   });
  
//   var pauseButton = document.getElementById("pause-button");
//   pauseButton.addEventListener("click", function() {
//     jQuery(".iframe_image").show();
//     jQuery("iframe#iframe1").attr("style", "z-index: -1;");
//     console.log('pause-button');
//     player.pauseVideo();
    
//   });
  
// }

// // Inject YouTube API script
// var tag = document.createElement('script');
// tag.src = "//www.youtube.com/player_api";
// var firstScriptTag = document.getElementsByTagName('script')[0];
// firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);


  // jQuery(document).ready(function() {
  //   jQuery('iframe').attr('id','iframe1');
  //   // jQuery('#iframe1').removeClass('hover');  
  // });
    

// var cssLink = document.createElement("link");
// cssLink.href = "style.css"; 
// cssLink.rel = "stylesheet"; 
// cssLink.type = "text/css"; 
// frames['iframe1'].document.head.appendChild(cssLink);

// window.onload = function() {
//    var iframe = document.getElementsByTagName("iframe");
//    console.log(iframe);
 
//     for (i = 0; i < iframe.length; i++) {
//         jQuery('iframe').attr('id','iframe1');
//         var cssLink = document.createElement("link");
//         cssLink.href = "youtube_custom_settings_iframe.css"; 
//         cssLink.rel = "stylesheet"; 
//         cssLink.type = "text/css";
//         console.log(frames['iframe1']);
//         frames['iframe1'].document.head.appendChild(cssLink);
//     }
// }

//"use strict"; document.addEventListener('DOMContentLoaded', function(){if (window.hideYTActivated) return; let onYouTubeIframeAPIReadyCallbacks=[]; for (let playerWrap of document.querySelectorAll(".hytPlayerWrap")){let playerFrame=playerWrap.querySelector("iframe"); let tag=document.createElement('script'); tag.src="https://www.youtube.com/iframe_api"; let firstScriptTag=document.getElementsByTagName('script')[0]; firstScriptTag.parentNode.insertBefore(tag, firstScriptTag); let onPlayerStateChange=function(event){if (event.data==YT.PlayerState.ENDED){playerWrap.classList.add("ended");}else if (event.data==YT.PlayerState.PAUSED){playerWrap.classList.add("paused");}else if (event.data==YT.PlayerState.PLAYING){playerWrap.classList.remove("ended"); playerWrap.classList.remove("paused");}}; let player; onYouTubeIframeAPIReadyCallbacks.push(function(){player=new YT.Player(playerFrame,{events:{'onStateChange': onPlayerStateChange}});}); playerWrap.addEventListener("click", function(){let playerState=player.getPlayerState(); if (playerState==YT.PlayerState.ENDED){player.seekTo(0);}else if (playerState==YT.PlayerState.PAUSED){player.playVideo();}});}window.onYouTubeIframeAPIReady=function(){for (let callback of onYouTubeIframeAPIReadyCallbacks){callback();}}; window.hideYTActivated=true;});



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


