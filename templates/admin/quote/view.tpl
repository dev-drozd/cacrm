<section class="pnl fw">
	<div class="sTitle">
		<span class="fa fa-chevron-right"></span>{title}
	</div>
	<div class="uTitle dClear">
		<figure>
			<div>
				[ava]<img src="/uploads/images/users/{customer-id}/thumb_{image}">[not-ava]<span class="fa fa-user-secret"></span>[/ava]
			</div>
		</figure>
		<div class="uName">
			<div>
				[customer]<a href="/users/view/{customer-id}" onclick="Page.get(this.href); return false;">[/customer]{name} {lastname}[customer]</a>[/customer][company] ({company})[/company]
				<p><b>Phone: </b><a href="tel:{phone}">{phone}</a>
					<a href="https://wa.me/{whatsapp}" target="_blank" style="color: #00e676;margin-left: 7px;">
						<span class="fa fa-whatsapp"></span>
					</a>
				</p>
				<p><b>Email: </b> <a href="mailto:{email}">{email}</a></p>
				<p><b>Date: </b> {date}</p>
				[ip]<p><b>From ip: </b> <a href="https://www.infobyip.com/ip-{ip}.html" target="_blank">{ip}</a></p>[/ip]
				[pathname]<p><b>From page: </b> <a href="https://yoursite.com{pathname}" target="_blank">{pathname}</a></p>[/pathname]
			</div>
			[store]
			<div class="address">
				<div class="feedback-middle">
					Store:<br>
					<span><a href="/objects/edit/{store-id}" onclick="Page.get(this.href); return false;">{store}</a></span>
				</div>
			</div>
			[/store]
		</div>
		<div class="answered">
			<p>{issue}</p>
		</div>
	</div>
	[answered]
	<div class="answered">
		<a href="/users/view/{send-id}" onclick="Page.get(this.href); return false;">{answ-name} {answ-lastname}</a>
		<p data-date="{send-date}">{reply-text}</p>
	</div>
	[not-answered]

	[/answered]
	{sms}
		<div class="addNote">
			<form class="uForm" action="/quote/send" method="post" id="quote-reply">
				<input type="hidden" name="id" value="{id}">
				<div class="sTitle">Reply to client</div>
				<div class="iGroup fw">
					<textarea name="text" required></textarea>
				</div>
				<div class="iGroup">
					<label>Send reply to email</label>
					<input type="checkbox" name="email" checked>
				</div>
				<div class="iGroup">
					<label>Send reply to phone</label>
					<input type="checkbox" name="sms" checked>
				</div>
				<div class="sGroup">
					<button type="submit" class="btn btnSubmit">Reply</button>
				</div>
			</form>
		</div>
		<script>
		$('#quote-reply').ajaxSubmit({
			callback: function(r){
				Page.get(location.href);
			},
			check: function(){
			}
		});
		</script>
</section>