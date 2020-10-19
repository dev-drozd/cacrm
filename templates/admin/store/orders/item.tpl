<div class="sUser" id="order_{id}">
	<div class="invInfo wp10" onclick="Page.get('/store/orders/{id}');" [color]style="background: {color}!important;"[/color]>
		{id}
	</div>
	<div class="invInfo wpDate" onclick="Page.get('/store/orders/{id}');" [color]style="background: {color}!important;"[/color]>
		{date}
	</div>
	<div class="invInfo wp15" [color]style="background: {color}!important;"[/color]>
		<a href="/users/view/{uid}" onclick="Page.get(this.href); return false;">{uname}</a>
		<br>{uphone}
	</div>
	<div class="invInfo wp10" [color]style="background: {color}!important;"[/color]>
		{currency}{total}
	</div>
	<div class="invInfo wp20" [color]style="background: {color}!important;"[/color]>
		<b>Payment:</b> {payment}
		<br><b>Delivery:</b> {delivery}
	</div>
	<div class="invInfo wp15" [color]style="background: {color}!important;"[/color]>
		{status}
	</div>
	<div class="uMore" [color]style="background: {color}!important;"[/color]>
		<span class="fa fa-ellipsis-h" onclick="$(this).next().toggle(0);"></span>
		<ul>
			<li>
				<a href="/store/orders/{id}" onclick="Page.get(this.href); return false;">
					<span class="fa fa-pencil"></span> View order
				</a>
			</li>
			<li>
				<a href="javascript:store.delOrder({id})">
					<span class="fa fa-times"></span> Del order
				</a>
			</li>
		</ul>
	</div>
</div>