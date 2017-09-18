window.addEventListener("resize", onResize);
window.addEventListener("load", onLoad);

var CONFIG = (function() {
     var private = {
         'HEADER_MAX_HEIGHT': '500',
         'HEADER_PIC_WIDTH':  '1000',
         'HEADER_PIC_MIN_WIDTH':  '600',
         'MOBILE_MAX_WIDTH': '800',
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
      
      headerImg.style.webkitTransform = str;
      headerImg.style.msTransform = str;
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
		headerImg.style.webkitTransform = css;
        headerImg.style.msTransform = css;
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
	  
	  $(".nav button.collapse").attr("value","off");
 	  $(".nav button.collapse").css({"background-color":"transparent"});
	  
	  if (window.innerWidth <= CONFIG.get('MOBILE_MAX_WIDTH') )
	  {    
		if ( $( ".nav button.collapse" ).is( ":hidden" ) )
	      $( ".nav button.collapse" ).css({"display":"initial"});
	    
	    if ( $( ".nav div.search" ).is( ":visible" ) )
	      $( ".nav div.search" ).css({"display":"none"});
	    
	    if ( $( ".nav ul" ).is( ":visible" ) )
	      $( ".nav ul" ).css({"display":"none"});
	  }
	  else
	  {
		if ( $( ".nav button.collapse" ).is( ":visible" ) )
	      $( ".nav button.collapse" ).css({"display":"none"});
	    
	    if ( $( ".nav div.search" ).is( ":visible" ) )
	     $( ".nav div.search" ).css({"display":"none"});
	    
	    if ( $( ".nav ul" ).is( ":hidden" ) )
	      $( ".nav ul" ).css({"display":"initial"});
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
function validateSearchForm()
{
	var x = document.forms["searchForm"]["what"].value;
    if (x == "") {
        alert("Please enter a term to search");
        return false;
    }
}

$(document).ready(function()
{
	$(".nav div.search button.searchBtn").focus();
	
	$(".nav button.collapse").click(function(){
		
		if ( $( ".nav div.search" ).is( ":visible" ) )
			$( ".nav div.search" ).css({"display":"none"});
  
		var onoff = $(".nav button.collapse").attr("value");

		if (onoff == "on")
		{
			$(".nav button.collapse").attr("value","off");
			$(".nav button.collapse").css({"background-color":"transparent"});
  
			if ( $(".nav ul").is( ":visible" ) )   
				$(".nav ul").slideToggle(100);
		}
		else
		{
			$(".nav button.collapse").attr("value","on");
			$(".nav button.collapse").css({"background-color":"darkorange"});
  
			if ( $(".nav ul").is( ":hidden" ) )   
				$(".nav ul").slideToggle(100);
		}
    });
	
	$(".nav ul li #search").click(function(){

		$(".nav ul").css({"display":"none"});
		//$(".nav div.search").css({"display":"initial"});
		$(".nav div.search").animate({width: 'toggle'});

		if ( window.innerWidth <= CONFIG.get('MOBILE_MAX_WIDTH') ) 
		{
			$(".nav div.search button.cancelBtn").css({"display":"none"});
			$(".nav div.search button.searchBtn").css({"display":"none"});
		}
		else
		{
			$(".nav div.search button.cancelBtn").css({"display":"initial"});
			$(".nav div.search button.searchBtn").css({"display":"initial"});
		}
	});
	
	$(".nav div.search button.cancelBtn").click(function(){
		
		$(".nav div.search").animate({width: 'toggle'});
		$(".nav div.search").promise().done(function(){
			// will be called when all the animations on the queue finish
			$(".nav ul").css({"display":"initial"});
		});
	});
});




