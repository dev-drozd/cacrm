{include="users/sett_menu.tpl"}
<section class="mngContent">
	<div class="sTitle"><span class="fa fa-chevron-right"></span>Email multi sending</div>
	<form class="uForm" method="post" id="sending" action="/users/sending">
		<div class="iGroup fw">
			<label>
				Groups
				<span>Please select the groups that should receive the email</span>
			</label>
			<select name="groups[]" multiple required>{groups}</select>
		</div>
		<div class="iGroup">
			<label>
				Sex
			</label>
			<div class="iRight">
				<input type="radio" name="sex" value="Male" data-label="Male">
				<input type="radio" name="sex" value="Female" data-label="Female">
			</div>
		</div>
		<div class="iGroup fw">
			<label>
				Message subject
			</label>
			<input type="text" name="subject" required>
		</div>
		<div class="iGroup fw">
			<label>
				The your mail
				<span>Write the text you want to send, you can also use the tags: <code>{user-name}</code></span>
			</label>
			<textarea name="content" required></textarea>
		</div>
		<div class="iGroup">
			<label>Send by email</label>
			<input type="checkbox" name="send_email" checked>
		</div>
		<div class="iGroup">
			<label>Send to sms</label>
			<input type="checkbox" name="send_sms">
		</div>
		<div class="iGroup">
			<label>Send to messengers</label>
			<input type="checkbox" name="send_messengers">
		</div>
		<div class="sGroup">
			<button class="btn btnSubmit" type="submit"><span class="fa fa-save"></span> sending</button>
		</div>
	</form>
</section>
<script>

$('textarea[name="content"]').fEditor();
$('#sending').ajaxSubmit({
	callback: function(r){
		var d = JSON.parse(r);
		if(d.total > 0){
			box.open({
				title: 'Email multi sending',
				width: 630,
				content: '<span id="example">\
					<h2 align="center">The example your message</h2>\
					<div style="background: #f6f6f6; text-align: center;">\
					<div style="box-sizing: border-box; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; width: 600px; max-width: 100%; background: #ffffff; border: 1px solid #ddd; padding: 20px; font-family: monospace; font-size: 14px; line-height: 24px; color: #828282; text-align: center; margin: 30px auto;">\
						<div style="margin: -20px -20px 0; padding: 20px;">\
							<a href="http://yoursite.com/">\
								<img src="http://yoursite.com/templates/site/img/logo.png" style="width: 60%; margin: 25px 0;" height="186">\
							</a>\
						</div>\
						<div style="padding: 0 30px 30px;">\
							'+$('textarea[name="content"]').val().replace('{user-name}', _user.name+' '+_user.lastname)+'\
						</div>\
					</div>\
				</div>\
				</span>\
				<p align="center" id="process-sending">\
					According to your filter found '+d.total+' people, you want to start sending?\
				</p>',
				submit: ['Start sending', function(a){
					var alrt = $('#process-sending'),
						sending = $('<p/>', {
						align: 'center',
					}), _t = $(this);
					alrt.text('Attention! Do not close this window until the mailing is finished');
					function send(){
						$.post('/users/sending', {
							send: true
						}, function(r){
							console.log(r);
							if(r.status == 'processed'){
								sending.text('Sent '+r.sent+' out of '+r.total+' messages');
								setTimeout(function(){
									send();
								}, 300);
							} else {
								$(_t).find('.mdlClose.fa.fa-times').show();
								alrt.css({color: 'green'}).text('The mail is successfully sent');
							}
						}, 'json');
					}
					alrt.after(sending);
					$(_t).find('.mdlFooter').remove();
					$(_t).find('.mdlClose.fa.fa-times').hide();
					$('.mdl.show').css({width: 500});
					$('#example').remove();
					send();
					//alert('test');
				}]
			});
		}
	}
});
</script>