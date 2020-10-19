<div class="sUser {confirmed}" id="inventory_{id}">
	<a [service][add-service]href="/inventory/edit/service/{id}" onclick="Page.get(this.href);"[/add-service][not-service]href="/inventory/view/{id}" onclick="Page.get(this.href);"[/service] class="uInfo wp30">
		{name}
	</a>
	<div class="uInfo wp10">
		[stock]<span class="thShort">{lang=Quantity}: </span>{quantity}[/stock]
	</div>
	<div class="uInfo wp15">
		<p class="trade_price"><span class="thShort">{lang=Store}: </span><span>{store}</span></p>
	</div>
	<div class="uInfo" style="width: 30px">
		[craiglist]<a href="{craiglist_url}" target="_blank">Go</a>[/craiglist]
	</div>
	<div class="uInfo wp10">
		<p class="trade_price"><span class="thShort">{lang=SalePrice}: </span><span[owner] ondblclick="inventory.ownerPrice({id}, 'price', this);"[/owner]>{currency}{price}</span></p>
	</div>
	<div class="uInfo wp10">
		[stock]<p class="trade_price"><span class="thShort">{lang=PurchasePrice}: </span><span[owner] ondblclick="inventory.ownerPrice({id}, 'purchase', this);"[/owner]>{purchase-currency}{purchase_price}</span></p>[/stock]
	</div>
	<div class="pStatus confBtn wp15">
		[cr-info]<b>Created</b>:<br>{cr-user}<br>{cr-date}[cr-issue]<br>Issue: <a href="/issues/view/{cr-issue}">Issue #{cr-issue}</a>[/cr-issue][/cr-info]
		[stock][notconfirmed][not-notconfirmed]
		[cn-info]<br><b>Confirmed</b>:<br>{cn-user}<br>{cn-date}[/cn-info]
		[/notconfirmed][/stock]
	</div>
	[add-service]
	<div class="uMore" style="width: 30px">
		<span class="fa fa-ellipsis-h" onclick="$(this).next().toggle(0);"></span>
		<ul>
			[service]
				[edit-iservice]<li><a href="/inventory/edit/service/{id}" onclick="Page.get(this.href); return false;" ><span class="fa fa-pencil"></span> {lang=editService}</a></li>[/edit-iservice]
				<li><a href="javascript:inventory.dub({id})" onclick="Page.get(this.href); return false;" ><span class="fa fa-copy"></span> {lang=dublicate}</a></li>
				[del-iservice]<li><a href="javascript:inventory.del({id})"><span class="fa fa-times"></span> {lang=delService}</a></li>[/del-iservice]
			[not-service]
				[notconfirmed]<li><a href="javascript:inventory.confirmed({id}, this);"><span class="fa fa-check"></span> {lang=Confirm}</a></li>[/notconfirmed]
				<li><a href="/inventory/edit/{id}" onclick="Page.get(this.href); return false;" ><span class="fa fa-pencil"></span> {lang=editInventory}</a></li>
				<li><a href="/inventory/view/{id}" onclick="Page.get(this.href); return false;" ><span class="fa fa-eye"></span> {lang=viewInventory}</a></li>
				[del][not-del]<li><a href="javascript:inventory.del({id})"><span class="fa fa-times"></span> {lang=delInventory}</a></li>[/del]
			[/service]
		</ul>
	</div>
	[not-add-service]
		[service]
		[not-service]
			[notconfirmed]
			<div class="uMore wp5">
				<span class="fa fa-ellipsis-h" onclick="$(this).next().toggle(0);"></span>
				<ul>
					<li><a href="javascript:inventory.confirmed({id}, this);"><span class="fa fa-check"></span> {lang=Confirm}</a></li>
				</ul>
			</div>
			[/notconfirmed]
		[/service]
	[/add-service]
</div>