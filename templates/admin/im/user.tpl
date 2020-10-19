<div class="imUser {class}" data-id="{id}" onclick="Im.open(this);">
	[ava]
		<img src="/uploads/images/users/{id}/thumb_{ava}" class="imImg">
	[not-ava]
		<span class="fa fa-user imImg"></span>
	[/ava] <span class="msg">{name} {lastname} {msg}</span>
	[msg-text]<span class="msg">{msg-text}</span>[/msg-text][online]<span class="online"></span>[/online]
	[email]<span class="fa fa-times red" onclick="Im.delTree(this);return false;"></span>[/email]
</div>