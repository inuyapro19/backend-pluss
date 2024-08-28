/*(function(){

var parallax = document.querySelectorAll(".parallax"),
     speed = 0.2;

 window.onscroll = function(){
   [].slice.call(parallax).forEach(function(el,i){

     var windowYOffset = window.pageYOffset,
         elBackgrounPos = "0 50" + (windowYOffset * speed) + "px";

     el.style.backgroundPosition = elBackgrounPos;

   });
 };

})();*/
