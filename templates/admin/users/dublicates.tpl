<div class="pnl fw lPnl">
	<div class="pnlTitle">
		User dublicates
	</div>
	
	<div class="row-filters">
		<select name="type" onchange="user.dublicateType(this.value);">
			<option value="email">Email</option>
			<option value="name">Name</option>
			<option value="phone">Phone</option>
		</select>
	</div>
	
	<div class="userList noTbl">
		{users}
	</div>
	{include="doload.tpl"}
</div>