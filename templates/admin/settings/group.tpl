<div class="sUser" id="group_{id}">
	<!--<div class="uThumb">
		[ava]<img src="/uploads/images/{id}/thumb_{ava}" onclick="showPhoto(this.src);">[not-ava]<span class="fa fa-user-secret"></span>[/ava]
	</div>-->
	<div class="uInfo">
		<div class="uInfo">
			{name}
		</div>
	</div>
	<div class="uMore">
		<span class="fa fa-ellipsis-h" onclick="$(this).next().toggle(0);"></span>
		<ul>
			<li><a href="#" onclick="Settings.addGroup({id}, '{name}', {pay}, {timer}, {week_hours}, {rating}); return false;"><span class="fa fa-pencil"></span> {lang=EditGroup}</a></li>
			<li><a href="/settings/#Privileges#{id}" onclick="Page.get(this.href); return false;"><span class="fa fa-user-secret"></span> {lang=EditPrivileges}</a></li>
			[custom]<li><a href="javascript:Settings.delGroup({id})"><span class="fa fa-times"></span> {lang=DeleteGroup}</a></li>[/custom]
		</ul>
	</div>
</div>