window.addEventListener("resize", onResize);
window.addEventListener("load", onLoad);

var CONFIG = (function() {
     var private = {
         'HEADER_MAX_HEIGHT': '500',
         'HEADER_PIC_WIDTH':  '1000',
         'HEADER_PIC_MIN_WIDTH':  '600',
         'MOBILE_MAX_WIDTH': '800',
         'JSON_HEADERS_FILE': './data/header-images.json'
     };

     return {
        get: function(name) { return private[name]; }
    };
})();

function adjustHeaderPicturePosition()
{
	//document.getElementById("xpto").innerHTML = window.innerWidth;
    var clientWidth = window.innerWidth;
	var headerImg = document.getElementById("header-picture");
	
	
    if ( clientWidth < CONFIG.get('HEADER_PIC_MIN_WIDTH') ) 
    {
      //document.getElementById("xpto").innerHTML = window.innerWidth;
      
      var deltaX = ( CONFIG.get('HEADER_PIC_MIN_WIDTH') - clientWidth ) / 2.0;
      deltaX = -1 * Math.floor(deltaX);
      
      var str = "translateX("+ deltaX + "px)";
      
      //document.getElementById("xpto").innerHTML = "translateX("+ deltaX + "px)";
      headerImg.style.msTransform = str;
      headerImg.style.webkitTransform = str;
      headerImg.style.transform = str;
    }
    else if ( clientWidth < CONFIG.get('HEADER_PIC_WIDTH') ) 
	{	
      headerImg.style.transform = "translateY(0px)";
	}
	else
	{
		// header img width is set to 100% so its equal to the window width
		// the height has to be half the width to keep the original picture 
		// proportion
	    var currentImgHeight = clientWidth / 2.0;
	    
	    // the amount the picture has to be moved up is equal to  
	    // (currentImgHeight - MAX_HEIGHT )/ 2
	    var delta = (currentImgHeight - CONFIG.get('HEADER_MAX_HEIGHT')) / 2;
		delta = -1 * Math.floor(delta);  
		
        var css = "translateY("+ delta + "px)";
		
        // move the picture up delta pixels
        headerImg.style.msTransform = css;
        headerImg.style.webkitTransform = css;
        headerImg.style.transform = css; 
		//=================================================================
		// END OF header picture top ajustment code
		//=================================================================
	}
}


function rectfyNavDysplay()
{
  //==================================================================
  //        Set the correct behaviour of the menu button
  //==================================================================
  var nav = document.getElementsByClassName("nav");
  var btn = nav[0].querySelector("button.collapse");
  var ul  = nav[0].getElementsByTagName("ul");
  
  btn.value="off";
  btn.style.background = "transparent";
  
  if (window.innerWidth <= CONFIG.get('MOBILE_MAX_WIDTH'))
  {    
	ul[0].style.display = "none";
  }
  else
  {
	ul[0].style.display = "initial";
  }
}
function onResize() 
{
	adjustHeaderPicturePosition();
	rectfyNavDysplay();
}
function onLoad()
{
    onResize();
}


$(document).ready(function(){
  $(".nav button.collapse").click(function(){
    var onoff = $(".nav button.collapse").attr("value");
          
    if (onoff == "on"){
      $(".nav button.collapse").attr("value","off");
      $(".nav button.collapse").css({"background":"transparent"});
    }
    else{
      $(".nav button.collapse").attr("value","on");
      $(".nav button.collapse").css({"background":"darkorange"});
    }
    $(".nav ul").slideToggle(100);
  });
});