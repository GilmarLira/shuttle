var isiPad = navigator.userAgent.match(/iPad/i) !== null;
var isiPhone = navigator.userAgent.match(/iPhone/i) !== null;
var CANVAS_SIZE;
var BIG_CANVAS_SIZE;
var BACKING_SCALE = window.devicePixelRatio;
var WINDOW_ORIENTATION = window.orientation;

console.log("Pixel ratio: " + BACKING_SCALE);
console.log("Window orientation: " + WINDOW_ORIENTATION);


if (isiPad) {
	if (WINDOW_ORIENTATION === 0 || WINDOW_ORIENTATION == 180) {
		CANVAS_SIZE = 160;
		CANVAS_LINE_WIDTH = 14;
		CANVAS_RADIUS = 57;

		BIG_CANVAS_SIZE = 552;
		BIG_CANVAS_LINE_WIDTH = 56;
		BIG_CANVAS_RADIUS = 248;
	} else {
		CANVAS_SIZE = 100;
		CANVAS_LINE_WIDTH = 8;
		CANVAS_RADIUS = 34;

		BIG_CANVAS_SIZE = 400;
		BIG_CANVAS_LINE_WIDTH = 40;
		BIG_CANVAS_RADIUS = 180;
	}

}

else if (isiPhone) {
	CANVAS_SIZE = 100;
	CANVAS_LINE_WIDTH = 8;
	CANVAS_RADIUS = 34;

	BIG_CANVAS_SIZE = 270;
	BIG_CANVAS_LINE_WIDTH = 25;
	BIG_CANVAS_RADIUS = 120;

}

else {
	CANVAS_SIZE = 100;
	CANVAS_LINE_WIDTH = 8;
	CANVAS_RADIUS = 34;

	BIG_CANVAS_SIZE = 400;
	BIG_CANVAS_LINE_WIDTH = 40;
	BIG_CANVAS_RADIUS = 180;
}





window.onorientationchange = function(){
	document.location.reload();
}

$(document).ready(function(){
	if (WINDOW_ORIENTATION == 0 || WINDOW_ORIENTATION == 180) {
		if(window.innerWidth == 320) {
			$(".list").click(function(){
				$(this).addClass("active");
				$("#detailer").stop().animate({left: '0px'}, 500, function(){

				});
/* 				$("#lister").stop().animate({left: '-320px'}, 500, function(){});			 */
			});
			$("#detailer").click(function(){
				$("#lister").stop().animate({left: '0px'}, 500, function(){});
				$(this).stop().animate({
					left: '320px'
				}, 500, function(){
					$(".list").removeClass("active");
				});


			});
		} else {
			$(".list").click(function(){
				$(this).addClass("active");
				$("#detailer").show().css('zIndex', '100').stop().animate({left: '0px'}, 500, function(){});
/* 				$("#lister").css('zIndex', 'auto').stop().animate({left: '-768px'}, 500, function(){}); */
			});

			$("#detailer").click(function(){
				$("#detailer").stop().animate({
					left: '768px'
				}, 500, function(){});
				$("#lister").stop().animate({left: '0px'}, 500, function(){
					$(".list").removeClass("active");
				});

			});

		}
	} else {
		if(window.innerWidth == 320) {
			$("#detailer").css('left','320px').hide();
			$(".list").click(function(){
				$(this).addClass("active");
				$("#detailer").removeClass("active");
				/*
$("#lister").css('zIndex','auto').stop().animate({left: '-320'}, 400, function(){
					$(this).hide();
				});
*/
				$("#detailer").css({zIndex: 100}).show().stop().animate({left: '0px'}, 400);
			});
			$("#detailer").click(function(){
				$("#detailer").addClass("active");
				$("#detailer").css('zIndex','auto').stop().animate({
					left: 320
				}, 400, function(){
					$(this).hide();
					$(".list").removeClass("active");
				});
/* 				$("#lister").css('zIndex','100').show().stop().animate({left: 0}, 400); */

			});
		} else {


		}
	}
});
