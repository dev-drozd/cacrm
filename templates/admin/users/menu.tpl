<aside class="sideNvg">
	<div class="sTitle">
		<span class="fa fa-chevron-right"></span>{lang=Manage}
		[user_new]<a href="/users/new" onclick="Page.get(this.href); return false;">
			<span class="stng fa fa-cog"></span>
		</a>[/user_new]
	</div>
	<ul class="mng">
		[add]<li><a href="/users/add{group-id}" onclick="Page.get(this.href); return false;"><span class="fa fa-user-plus" style="color: #A2CE4E;"></span>Add {group}</a></li>[/add]
		<li><a href="/users{group-id}" onclick="Page.get(this.href); return false;"><span class="fa fa-users" style="color: #299CCE;"></span>All {group}</a></li>
		<li><a href="/users/dublicates" onclick="Page.get(this.href); return false;"><span class="fa fa-clone" style="color: #299CCE;"></span>Dublicates</a></li>
	</ul>
</aside>