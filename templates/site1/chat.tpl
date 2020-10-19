<!DOCTYPE html>
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=yes" />
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <title>Your Company</title>
		<link rel="stylesheet" type="text/css" href="{theme}/css/font-awesome.min.css">
		<link rel="stylesheet" type="text/css" href="{theme}/css/style.css">
		<script src="{theme}/js/jquery-3.1.1.js"></script>
		<script src="{theme}/js/chat.js"></script>
	</head>
	<body>
		<div class="chat hide" id="chat">
			<div class="chatTitle">
				Live chat
				<span class="fa fa-times"></span>
			</div>
			<span id="cnt"></span>
		</div>
		
		<script>
		function polling(){
			$.getJSON('/sub/{guest-id}.sock?time='+$.now()+'&confirm_purchase=0&chat_support=0&all=0&chat_support=0', function(j){
				switch(j.type){
					case 'support_msg':
						var a = new Audio();
						if(j.content){
							a.src = 'http://www.formilla.com/remoteAssets/media/Notification_mp3.mp3';
							$('#chat #cnt').html(j.content);
							$('#chat .chatTitle').mousedown();
						}
						a.play();
					break;
					
					case 'chat_message':
						var a = new Audio();
						$('#chat .chatTitle').mousedown();
						$('#chat .dialog').append(j.message).scrollTop(999999999);;
						a.src = 'http://www.formilla.com/remoteAssets/media/Notification_mp3.mp3';
						a.play();
					break;
				}
				polling();
			});
		}
		polling();
		</script>
	</body>
</html>