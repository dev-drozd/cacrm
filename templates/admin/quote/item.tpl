<div class="sUser" id="quote_{id}" style="background: {color};">
	<div class="uInfo" style="width: 20%;" onclick="Page.get('/quote/view/{id}');">
		[customer]<a href="/users/view/{customer-id}" onclick="Page.get(this.href); return false;">{name} {lastname}</a>[not-customer]{name} {lastname}[/customer]
	</div>
	<div class="pInfo">
		<span><a href="tel:{phone}">{phone}</a></span><br>
		<span><a href="mailto:{email}">{email}</a></span>
	</div>
	<div class="pInfo" style="text-align: left;" onclick="Page.get('/quote/view/{id}');">
		{issue}...
		<p align="right">{date}</p>
	</div>
	<div class="uMore">
		<span class="fa fa-ellipsis-h" onclick="$(this).next().toggle(0);"></span>
		<ul>
			<li><a href="/quote/view/{id}" onclick="Page.get(this.href); return false;"><span class="fa fa-pencil"></span>View</a></li>
			<li><a href="javascript:quote.del({id});"><span class="fa fa-times"></span> Delete</a></li>					
		</ul>
	</div>
</div>