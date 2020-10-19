<div class="sUser {confirmed}" id="inventory_{id}">
	<a href="/inventory/[service]edit/service/[not-service]view/[/service]{id}" onclick="Page.get(this.href); return false;" class="uInfo">
			{name}
			[tradein]<p class="trade_price">{lang=Price}: <span>{currency}{trade-cr-price}</span>
			<p class="trade_price">{lang=PurchasePrice}: <span>{purchase-currency}{purchase_price}</span>
			[trade-conf]<br>{lang=ConfirmedPrice}: <span class="cn_price">${trade-cn-price}</span>[/trade-conf]</p>
			[not-tradein]
			<p class="trade_price">{lang=Price}: <span>{currency}{price}</span></p>
			<p class="trade_price">{lang=Store}: <span>{store}</span></p>
			[/tradein]
	</a>
	<div class="pStatus confBtn">
		[tradein]
			Created: {trade-cr-user}<br>{trade-cr-date}
			[trade-conf]<br>Confirmed:{trade-cn-user}<br>{trade-cn-date}[/trade-conf]
		[not-tradein]
			[cr-info]Created: {cr-user}<br>{cr-date}[cr-issue]<br>Issue: <a href="/issues/view/{cr-issue}">Issue #{cr-issue}</a>[/cr-issue][/cr-info]
			[notconfirmed][not-notconfirmed]
			[service][not-service][cn-info]<br>Confirmed:{cn-user}<br>{cn-date}[/cn-info][/service]
			[/notconfirmed]
		[/tradein]
	</div>
	<div class="pStatus nIssue">
		[tradein]
			[trade-conf]
			[not-trade-conf]
				<a href="javascript:inventory.tradeConfirm({trade-id}, '{trade-cr-price}');" class="trCOnf">{lang=Confirm}</a>
			[/trade-conf]
		[not-tradein]
		[/tradein]
	</div>
	<div class="uMore">
		<span class="fa fa-ellipsis-h" onclick="$(this).next().toggle(0);"></span>
		<ul>
			[service]
				[edit-iservice]<li><a href="/inventory/edit/service/{id}" onclick="Page.get(this.href); return false;" ><span class="fa fa-pencil"></span> {lang=editService}</a></li>[/edit-iservice]
				<li><a href="javascript:inventory.dub({id})" onclick="Page.get(this.href); return false;" ><span class="fa fa-copy"></span> {lang=dublicate}</a></li>
				[del][not-del]<li><a href="javascript:inventory.del({id})"><span class="fa fa-times"></span> {lang=delService}</a></li>[/del]
			[not-service]
				[notconfirmed]<li><a href="javascript:inventory.confirmed({id}, this);"><span class="fa fa-check"></span> Confirm</a></li>[/notconfirmed]
				<li><a href="/inventory/edit/{id}" onclick="Page.get(this.href); return false;" ><span class="fa fa-pencil"></span> {lang=editInventory}</a></li>
				<li><a href="/inventory/view/{id}" onclick="Page.get(this.href); return false;" ><span class="fa fa-eye"></span> {lang=viewInventory}</a></li>
				[del][not-del]<li><a href="javascript:inventory.del({id})"><span class="fa fa-times"></span> {lang=delInventory}</a></li>[/del]
			[/service]
		</ul>
	</div>
</div>