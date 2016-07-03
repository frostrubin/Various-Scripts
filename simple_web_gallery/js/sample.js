function initGallery(containerWidth, maxRowHeight, spacing) {
  // Note that some of the images are the same but have been cropped differently; this is not a result
  // of the gallery js
  var images = [
    {image:"image_name.jpg", thumbnail:"image_name.jpg", width:"540", height:"360"},
    {image:"image_2.jpg", thumbnail:"image_2.jpg", width:"713", height:"360"}
  ];
  
  // var frames = [{ height: 360, width: 1000 },
  // 	{ height: 400, width: 300 },
  // 	{ height: 400, width: 300 }];
  var frames = new Array();
  images.forEach(function(image, index) {
    var obj = new Object();
    obj.height = parseInt(image.height);
    obj.width = parseInt(image.width);
    frames.push(obj);
  });

  $("#gallery").css("width", containerWidth);
  var rows = layoutFrames(frames, containerWidth, maxRowHeight, spacing);
  var index = 0;

  rows.forEach(function(row, rowIndex) {
	row.forEach(function(image, imgIndex) {
	  $("#gallery").append("<a class='gallery-item' href='images/" + images[index].image + "'>" + "<div style='width:" + (image.width) + "px; height:" + (image.height) + "px; margin-bottom:10px;' class='frame'></div></a>")
	  $("#gallery .frame").last().append("<img src='thumbnails/" + images[index].thumbnail + "' />")
    	// .append("<div class='title'><p class='title'>" + images[index].title + "</p></div>")
    	// .hover(function(event) {
    	// 	$(this).find("div.title").css("display", "block");
    	// }, function(event) {
    	// 	$(this).find("div.title").css("display", "none");
    	// });

	  if (imgIndex != 0) {
	    $("#gallery .frame").last().css("margin-left", spacing + "px");
	  }
	  index++;
    });
  });

  $(".frame").css("margin-bottom", spacing + "px");
}

$(function() { 
  var width = (window.innerWidth > 0) ? window.innerWidth : screen.width;
  //var height = (window.innerHeight > 0) ? window.innerHeight : screen.height;
  var gWidth = width * 0.8;
  var gHeight = 360;
  var gSpacing = 10;
  initGallery(gWidth, gHeight, gSpacing);
});

$(document).ready(function() {
  $('.gallery-item').magnificPopup({
    type: 'image',
    gallery:{
      enabled: true
    },
    callbacks: {
      open: function() {
        $( ".mfp-img" ).attr("style", 'max-height: ' + myHeight + "px");
      },
      change: function() {
        $( ".mfp-img" ).attr("style", 'max-height: ' + myHeight + "px");
      },
      resize: function() {
        $( ".mfp-img" ).attr("style", 'max-height: ' + myHeight + "px");
      },
      imageLoadComplete: function() {
        $( ".mfp-img" ).attr("style", 'max-height: ' + myHeight + "px");
      }
    },
    closeOnContentClick: true,
    mainClass: 'mfp-img-mobile',
    image: {
        verticalFit: true
    }
  });
});