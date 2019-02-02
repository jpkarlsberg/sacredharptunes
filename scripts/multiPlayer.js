window.onload = function(){

   // Pause all other players on page if one is playing
   document.addEventListener('play', function(e){
       var audios = document.getElementsByTagName('audio');
       for(var i = 0, len = audios.length; i < len;i++){
           if(audios[i] != e.target){
               audios[i].pause();
           }
       }
   }, true);

}
