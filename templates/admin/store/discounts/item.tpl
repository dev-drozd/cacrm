<div class="sUser {confirmed}" id="discount_{id}">
	<div class="uInfo">
		#{id} 
		[customer]<p>Used by <a href="/users/view/{user-id}" onclick="Page.get(this.href); return false;">{user-name}</a></p>[/customer]
	</div>
	<div class="pInfo">
		Experience date:<br>{date}
	</div>
	<div class="pInfo">
		{amount}%
	</div>
	<div class="uMore">
		[used]
		[not-used]
		<span class="fa fa-ellipsis-h" onclick="$(this).next().toggle(0);"></span>
		<ul>
			<li><a href="javascript:store.delDiscount({id})"><span class="fa fa-times"></span> Del discount</a></li>
		</ul>
		[/used]
	</div>
</div>