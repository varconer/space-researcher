var vcseText = [
	// eng
	[
		"Game over",
		"Jamp to system `%map%` successful",
		"Your in system `%map%`"
	],
	// rus
	[
		"Вы проиграли",
		"Прыжок в систему `%map%` завершен",
		"Вы находитесь в системе `%map%`"
	]
];

function vcseGetWindowWidth()
{
	return $(window).width()-25;
}

function vcseGetWindowHeight()
{
	return $(window).height()-25;
}

function vcseGoTo(toObj, toX, toY, state, creation)
{
	var changeX = $("#"+toObj).position().left - $("#myship").position().left;
	var changeY = $("#"+toObj).position().top - $("#myship").position().top;
	
	if ( (Math.abs(changeX)<vcseSectorSizeY+30 && Math.abs(changeY)<vcseSectorSizeY+30) || creation=='1' ) {
		// request to server
		$.ajax({ 
			url: '/space-researcher/vcse/_move.php', 
			data: 'toX='+toX+'&toY='+toY+'&player='+vcsePlayer+'&pass='+vcsePass+'&state='+state, 
			cache: false, 
			success: function(reply){ 
				if (reply) {
					// split reply
					var arrReply = reply.split('#js#');
					var html = arrReply[0];
					var js = '';
					if (arrReply[1] != undefined) js = arrReply[1];
					
					// show panel
					$('#panel').html(html); 
					
					// run js
					if (js) eval(js);
					
					// calc fly (after rotate)
					var changeX = $("#"+toObj).position().left - $("#myship").position().left;
					var changeY = $("#"+toObj).position().top - $("#myship").position().top;
					if (correctShipPosition) {
						changeX = changeX - 9;
						changeY = changeY - 16;
					}
					
					// animate
					$("#myship").animate({"left": "+="+changeX+"px", "top": "+="+changeY+"px"}, shipSpeed);
					$("#myshipmark").animate({"left": "+="+changeX+"px", "top": "+="+changeY+"px"}, shipSpeed);
					$("#content").animate({"left": "+="+(-changeX)+"px", "top": "+="+(-changeY)+"px"}, shipSpeed);
					
					// game over
					if (vcseGameOver) {
						alert(vcseText[vcseLang][0]);
						document.location.replace('space.php', 'index.php');
					}
				}
			} 
		}); 
	}
}

function vcseChangeMode()
{	
	// request to server
	$.ajax({ 
		url: '/space-researcher/vcse/_mode.php', 
		data: 'player='+vcsePlayer+'&pass='+vcsePass, 
		cache: false, 
		success: function(html){ 
			// show info
			$('#panel').html(html); 
		} 
	}); 
}

function vcsePanelSlide(panel, idType, id)
{	
	// request to server
	$.ajax({ 
		url: '/space-researcher/vcse/_panel.php', 
		data: 'player='+vcsePlayer+'&pass='+vcsePass+'&panel='+panel+'&item_id_type='+idType+'&item_id='+id, 
		cache: false, 
		success: function(html){ 
			// show info
			$('#panel').html(html); 
		} 
	}); 
}

function vcseViewTarget(idTarget)
{
	var windowWidth = vcseGetWindowWidth();
	var windowHeight = vcseGetWindowHeight();
	var posTarget = $("#"+idTarget).position();
	var distX = (windowWidth / 2) - posTarget.left - (vcseSectorSizeX / 2);
	var distY = (windowHeight / 2) - posTarget.top - (vcseSectorSizeY / 2);
	$("#content").offset({"top": "0px", "left": "0px"});
	$("#content").animate({"left": distX+"px", "top": distY+"px"}, "fast");
	
	var targetX = (windowWidth / 2) - 37 + 8;
	var targetY = (windowHeight / 2) - 32 + 10;
	$("#target").css("left", targetX+"px");
	$("#target").css("top", targetY+"px");
	$("#target").fadeIn(1000, function(){
		$("#target").fadeOut(1000);
	});
}

function vcseShipToCenter()
{
	vcseViewTarget("myship");
}

function vcseStore(action, idType, id, quantity)
{
	// request to server
	$.ajax({ 
		url: '/space-researcher/vcse/_store.php', 
		data: 'player='+vcsePlayer+'&pass='+vcsePass+'&item_action='+action+'&item_id_type='+idType+'&item_id='+id+'&item_quantity='+quantity, 
		cache: false, 
		success: function(html){ 
			// show info
			$('#panel').html(html); 
		} 
	}); 
}

function vcseStoreSlideUp(contentId)
{
	var curTop = $("#"+contentId).css('top');
	if (curTop != "0px") $("#"+contentId).animate({"top": "+=65px"}, "normal");
	
	// request to server
	$.ajax({ 
		url: '/space-researcher/vcse/_store_slide.php', 
		data: 'player='+vcsePlayer+'&pass='+vcsePass+'&content_id='+contentId+'&direction=-1', 
		cache: false
	}); 
}

function vcseStoreSlideDown(contentId, countItems)
{
	var curTop = $("#"+contentId).css('top');
	var minTop = (Math.ceil(countItems / 4) - 3) * 65;
	if (curTop != "-"+minTop+"px") {
		$("#"+contentId).animate({"top": "-=65px"}, "normal");
	
		// request to server
		$.ajax({ 
			url: '/space-researcher/vcse/_store_slide.php', 
			data: 'player='+vcsePlayer+'&pass='+vcsePass+'&content_id='+contentId+'&direction=1', 
			cache: false
		}); 
	}
}

function vcseSet(type, subtype, pid)
{
	// request to server
	$.ajax({ 
		url: '/space-researcher/vcse/_set.php', 
		data: 'player='+vcsePlayer+'&pass='+vcsePass+'&type='+type+'&set='+pid, 
		cache: false, 
		success: function(reply){ 
			if (reply) {
				// show panel
				$('#panel').html(reply); 
				
				// run js
				if (type == 'hull') {
					$('#myship').attr('src', '/space-researcher/img/hull-'+subtype+'.png');
					$('#myship_panel').attr('src', '/space-researcher/img/hull-'+subtype+'.png');
				}
			}
		} 
	}); 
}

function vcseStartTimeBar()
{
	// v1.0
	/* var offsetTensity = $("#tensity").offset();
	$("#timebar").css("top", offsetTensity.top+"px"); */
	
	$("#timebar").css("width", "1px");
	$("#timebar").stop();
	$("#timebar").animate({"width": "228px"}, 300000, "linear", function(){
		reloadMap();
	});
}

function vcseEndCreation()
{
	// request to server
	$.ajax({ 
		url: '/space-researcher/vcse/_end_creation.php', 
		data: 'player='+vcsePlayer+'&pass='+vcsePass, 
		cache: false, 
		success: function(reply){ 
			if (reply) {
				// split reply
				var arrReply = reply.split('#sep#');
				var panel = arrReply[0];
				var sectors = '';
				if (arrReply[1] != undefined) sectors = arrReply[1];
				var objects = '';
				if (arrReply[2] != undefined) objects = arrReply[2];
				// show info
				$('#panel').html(panel); 
				$('#sectors').html(sectors); 
				$('#objects').html(objects); 
				// js
				$('#myship').attr('onclick', 'vcseChangeMode();');
				$('#myship').attr('alt', vcsePlayer);
				$('#myship').attr('title', vcsePlayer);
				
				// time bar
				vcseStartTimeBar();
			}
		} 
	}); 
}

function vcseCreate(toX, toY, objType, objSubType, objName)
{
	// request to server
	$.ajax({ 
		url: '/space-researcher/vcse/_creation.php', 
		data: 'player='+vcsePlayer+'&pass='+vcsePass+'&toX='+toX+'&toY='+toY+'&objType='+objType+'&objSubType='+objSubType+'&objName='+objName, 
		cache: false, 
		success: function(reply){ 
			if (reply) {
				// split reply
				var arrReply = reply.split('#js#');
				var html = arrReply[0];
				var js = '';
				if (arrReply[1] != undefined) js = arrReply[1];
				
				// show panel
				$('#panel').html(html); 
				
				// run js
				if (js) eval(js);
			}
		} 
	}); 
}

function vcseJamp(map)
{
	$('#content').fadeOut("slow", function(){
		// request to server
		$.ajax({ 
			url: '/space-researcher/vcse/_jamp.php', 
			data: 'player='+vcsePlayer+'&pass='+vcsePass+'&jamp='+map, 
			cache: false, 
			success: function(reply){ 
				if (reply) {
					// show
					$('#content').html(reply);
					$('#content').fadeIn("slow", function(){
						alert(vcseText[vcseLang][1].replace('%map%', map));
						
						// time bar
						//space-researcher/vcseStartTimeBar();
					});	
				}
			} 
		}); 
	});
}

function reloadMap()
{
	// request to server
	$.ajax({ 
		url: '/space-researcher/vcse/_reload_map.php', 
		data: 'player='+vcsePlayer+'&pass='+vcsePass+'&reload_map=1', 
		cache: false, 
		success: function(reply){ 
			if (reply) {
				// show
				$('#sectors').html(reply);
				
				// time bar
				vcseStartTimeBar();
			}
		} 
	}); 
}

function vcseLoad(map)
{
	// set window size
	var windowWidth = vcseGetWindowWidth();
	var windowHeight = vcseGetWindowHeight();
	var pleerWidth = 175;//$("#pleer").width();
	var pleerLeft = windowWidth - pleerWidth;
	$("#window").width(windowWidth);
	$("#window").height(windowHeight);
	$("#content").draggable();
	$("#pleer").css("left", pleerLeft+"px");
	// set ship to center of window
	vcseShipToCenter();
	
	alert(vcseText[vcseLang][2].replace('%map%', map));
	
	// time bar
	vcseStartTimeBar();
}