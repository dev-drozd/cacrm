<aside class="sideNvg">
	<div class="sTitle"><span class="fa fa-chevron-right"></span>{lang=Manage}</div>
	<ul class="mng">
		<li><a href="/issues/add/{device-id}" onclick="Page.get(this.href); return false;"><span class="fa fa-cart-plus" style="color: #A2CE4E;"></span>{lang=addIssue}</a></li>
		[view][show][confirmed][invoice][not-invoice]<li><a href="#" onclick="issues.updateStatus({id}, {nconfirmed}); return false;"><span class="fa fa-check" style="color: #A2CE4E;"></span>Update status</a></li>[/invoice][/confirmed][/show][/view]
	</ul>
</aside>