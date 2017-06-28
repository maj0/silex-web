/**
   author: sajid mahmoood
   fix width and color properties of selected elements
**/
function fixWidth() {
  //alert("This is the first.");
  var aInputs = $('input');
  if(aInputs.length) {
	  //alert('color='+$(aInputs[0]).css('offset-width'));
	  var oInput = $(aInputs[0]);
	  $('textarea,select').each(function(index) {
		  //alert('width='+aInputs[0].offsetWidth);  
		  $(this).css('width', aInputs[0].offsetWidth);
		  $(this).css('color', oInput.css('color'));
	  });
  }
}

/*
 * load event handler
 */
function addLoadEvent(func) {
  var oldonload = window.onload;
  if (typeof window.onload != 'function') {
    window.onload = func;
  } else {
    window.onload = function() {
      if (oldonload) {
        oldonload();
      }
      func();
    }
  }
}
addLoadEvent(fixWidth); // fix width of textarea and select elements
