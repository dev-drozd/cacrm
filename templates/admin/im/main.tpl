<style>
body {
	background: #fff;
}
</style>
<link rel="stylesheet" href="{theme}/css/emoji.css">
<div class="im">

	<div class="imChats">
		<!--<div class="imNoCont">No chats</div>-->
		<div class="imDialog" data-id="{id}">
			{messages}
		</div>
		<div class="imMessage">
			<textarea id="message" placeholder="Line break by Ctrl+Enter" onpaste="Im.paste(event);" onbeforecopy="console.log(e);">{text}</textarea>
			<div class="imFunc">
				<div class="flLeft sendAll" style="display: [send-all]block[not-send-all]none[/send-all];">
					<input type="checkbox" name="sendAll" id="sendAll">
					<span>{lang=SendIndividually}</span>
				</div>
				<button class="btn btnIm" onclick="Im.send(this)">{lang=Send}</button>
				<ul>
					<li><span class="fa fa-picture-o imageUpload"></span></li>
					<li><span class="fa fa-file filesUpload"></span></li>
				</ul>
			</div>
			<div class="dClear"></div>
			 <div class="imAttach">
				<div class="thumbnails">
					<!-- <div class="thumb">
						<img src="/uploads/images/bugs/66/thumb_5786b9b32cadd6.99202668.jpg" onclick="showPhoto(this.src);">
						<span class="fa fa-times"></span>
					</div> -->
				</div>
				<ul class="files">
					<!-- <li><a href="#"><span class="fa fa-file"></span> FIle name</a> <span class="fa fa-times"></span></li>
					<li><a href="#"><span class="fa fa-file"></span> FIle name</a> <span class="fa fa-times"></span></li> -->
				</ul>
			</div> 
		</div>
	</div>
	
	<div class="imLeft">
		<span class="fa fa-group" onclick="$('.imLeft').toggleClass('open');"></span>
		<div class="imSearch">
			<input type="search" name="imSearch" placeholder="{lang=UserSearch}" onkeyup="Im.search(this)" style="padding: 10px 10px 10px 45px;">
		</div>
		[chat-support]
		<div class="imTabs[support] support[/support][emails] email[/emails]">
			<span class="active" onclick="Im.tab(this);"><span class="fa fa-user"></span> Staff</span>
			<span onclick="Im.tab(this, 1);"><span class="fa fa-support"></span> Support</span>
			[email-receive]<span onclick="Im.tab(this, 2);"><span class="fa fa-envelope"></span> Email</span>[/email-receive]
			<i></i>
		</div>
		<div class="emails_list" style="display:[emails]block[not-emails]none[/emails];padding: 10px;">
			<select id="emails_list" onchange="Im.readEmail(this.value)">
				{email-list}
			</select>
		</div>
		[not-chat-support]
		[email-receive]
		<div class="imTabs[emails] email[/emails]">
			<span class="active" onclick="Im.tab(this);"><span class="fa fa-user"></span> Staff</span>
			<span onclick="Im.tab(this, 2);"><span class="fa fa-envelope"></span> Email</span>
			<i></i>
		</div>
		[/email-receive]
		[/chat-support]
		<div class="imUser gc" data-id="all" onclick="Im.open(this);"[support] style="display: none;"[/support][emails] style="display: none;"[/emails]>
			<span class="fa fa-group imImg"></span> {lang=GroupChat}
		</div>
		<div class="imDialogs">
			{dialogues}
		</div>
		[new-email]
		<div class="newEmail" [emails][not-emails] style="display: none;"[/emails] onclick="Im.newEmail();">
			<span class="fa fa-plus"></span> New email
		</div>
		[/new-email]
	</div>
</div>
<script src="{theme}/js/im.js"></script>
<script>
$(document).ready(function(){
	Im.setFocus();
	$('#message').keydown(function(e){
        if (e.keyCode == 13) {
            if (e.ctrlKey) {
                var val = this.value;
                if (typeof this.selectionStart == "number" && typeof this.selectionEnd == "number") {
                    var start = this.selectionStart;
                    this.value = val.slice(0, start) + "\n" + val.slice(this.selectionEnd);
                    this.selectionStart = this.selectionEnd = start + 1;
                } else if (document.selection && document.selection.createRange) {
                    this.focus();
                    var range = document.selection.createRange();
                    range.text = "\r\n";
                    range.collapse(false);
                    range.select();
                }
            } else {
                e.preventDefault();
                Im.send();
            }
        }
	});
	$('.imDialog').scrollTop(999999999);
	
	$('.imUser[data-id="' + $('.imDialog').attr('data-id') + '"]').addClass('active');
	
	$('.imageUpload').upload({
		count: 0,
		multiple: true,
		max: 5,
		check: function(e){
			var self = this;
			if(!e.error){
				var img = new Image();
				img.src = URL.createObjectURL(e.file);
				img.setAttribute('onclick', 'showPhoto(this.src);');
				$('.imMessage .thumbnails').append($('<div/>', {
					class: 'thumb',
					html: img
				}).append($('<span/>', {
					class: 'fa fa-times'
				}).click(function(){
					delete self.files[e.file.name];
					$(this).parent().remove();
					$('.imDialog').css('height', $('.im').height() - $('.imMessage').height() - 35);
				})));
			} else if(e.error == 'max'){
				alr.show({
				   class: 'alrDanger',
				   content: lang[51],
				   delay: 2
				});
			} else if(e.error == 'type'){
				alr.show({
				   class: 'alrDanger',
				   content: lang[50],
				   delay: 2
				});
			} else if(e.error == 'size'){
				alr.show({
				   class: 'alrDanger',
				   content: lang[49],
				   delay: 2
				});
			}
			$('.imDialog').css('height', $('.im').height() - $('.imMessage').height() - 35);
			$('.imDialog').scrollTop(999999999);
		}
	});
	
	$('.filesUpload').upload({
		count: 0,
		multiple: true,
		max: 5,
		accept: '*',
		types: ['png','jpg','gif','mp3','application/apk', 'apk','application/octet-stream','application/vnd.android.package-archive', 'vnd.openxmlformats-officedocument.wordprocessingml.document','msword','vnd.ms-excel','vnd.openxmlformats-officedocument.spreadsheetml.sheet','vnd.ms-powerpoint','vnd.openxmlformats-officedocument.presentationml.presentation','rtf','pdf','vnd.adobe.photoshop','vnd.djvu','fb2','ps','jpeg','plain','csv','vnd.android.package-archive'],
		check: function(e){
			var self = this;
			if(!e.error){
				$('.imMessage .files').append($('<li/>', {
					html: $('<a/>', {
						href: URL.createObjectURL(e.file),
						html: e.file.name
					})
				}).append($('<span/>', {
					class: 'fa fa-times'
				}).click(function(){
					delete self.files[e.file.name];
					$(this).parent().remove();
				})));
			} else if(e.error == 'max'){
				alr.show({
				   class: 'alrDanger',
				   content: lang[51],
				   delay: 2
				});
			} else if(e.error == 'type'){
				alr.show({
				   class: 'alrDanger',
				   content: 'Forbidden file type',
				   delay: 2
				});
			} else if(e.error == 'size'){
				alr.show({
				   class: 'alrDanger',
				   content: lang[49],
				   delay: 2
				});
			}
			$('.imDialog').css('height', $('.im').height() - $('.imMessage').height() - 35);
			$('.imDialog').scrollTop(999999999);
		}
	});
	$('.imDialog').scroll(function() {
		if ($('.imDialog').scrollTop() <= 20) {
			Im.history();
		}
	});
	
	$('.imDialogs').scroll(function() {
		if ($('.imDialogs').scrollTop() >= $('.imDialogs').height()*0.1) {
			if (Im.uf) return;
			Im.uf = true;
			$.post(location.href, {
				page: Im.upage,
			}, function(r) {
				if (r.res_count) {
					Im.upage ++;
					Im.uf = false;
					$('.imDialogs').append(r.content);
				}
			}, 'json');
		}
	});
	//$('.imMesText').emoji();
	//$('.left-nav-tabs > a:last').click();
});
</script>
<style>
	.mini-chat-search, .mini-chat {
		display: none;
	}
	.info {
		display: none;
	}
	
	@media (max-width: 767px) {
		body {
			overflow: hidden;
		}		
			
		.im {
			margin: 0;
		}
		
		.im, .imLeft {
			height: -webkit-calc(100vh - 50px);
			height: -moz-calc(100vh - 50px);
			height: calc(100vh - 50px);
		}
		
		.imLeft {
			z-index: 99999999999;
		}
		
		.imDialogs {
			height: -webkit-calc(100vh - 194px);
			height: -moz-calc(100vh - 194px);
			height: calc(100vh - 194px);
		}
		
		.imDialog {
			height: -webkit-calc(100vh - 220px);
			height: -moz-calc(100vh - 220px);
			height: calc(100vh - 220px);
		}

		#page {
			margin: 0;
			width: 100%;
			max-width: 100%;
		}

		.mobBtn, .bugBtn {
			display: none;
		}
	}
	
	@media (max-width: 600px) {
		.im {
			margin: -10px 0 0;
		}
	}
	
	@media (max-width: 450px) {
		.sendAll > .cbWrap + span {
			display: none;
		}
		
		.imMessage {
			padding: 10px;
		}

		.imMessage > textarea {
			height: 60px;
		}

		.imFunc {
			margin-top: 0;
		}
		
		.imDialog {
			height: -webkit-calc(100vh - 230px);
			height: -moz-calc(100vh - 230px);
			height: calc(100vh - 230px);
		}
	}
</style>