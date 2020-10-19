<div class="tr {class}" id="act_{id}">
	<div class="td lh45">
		[users]
			{users}
		[not-users]
			[uid]
			<span class="thShort flLeft" style="margin-right: 10px;">Customer: </span><a href="/users/view/{uid}" target="_blank">
				[ava]<img src="/uploads/images/users/{uid}/thumb_{ava}" class="miniRound">[not-ava]<span class="fa fa-user-secret miniRound"></span>[/ava]
				{name} {lastname}
			</a>
			[not-uid]
			No user
			[/uid]
		[/users]
	</div>
	<div class="td"><span class="thShort">Event: </span>{event}</div>
	<div class="td"><span class="thShort">Status: </span>{status}</div>
	<div class="td"><span class="thShort">Store: </span>{object}</div>
	<div class="td">
		[del]<span class="fa fa-times flRight cashCom red" onclick="camera.delAct(this.parentNode.parentNode, {id})"></span>[/del]
		<span class="thShort">Date: </span><span style="display:inline-block; width: 120px;">{date}</span>
		<span class="fa fa-comment flRight cashCom" onclick="camera.comments({id})"></span>
		<span class="fa fa-exclamation-circle flRight cashCom red" onclick="Page.get('/im/{uid}?text=Camera;{id}')"></span>
		<span class="fa fa-camera flRight cashCom" onclick="camera.showVideo({id});"></span>
	</div>
</div>