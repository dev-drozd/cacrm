<div class="tr" data-id="{id}" data-type="{type}" id="tr_{type}_{id}">
	<div class="td">
		[edit]<span class="fa fa-times del" onclick="invoices.delInvItem(this, '{type}', {id})"></span>[/edit]
		[stock]<a href="/inventory/view/{id}" target="_blank">[/stock]<span class="catname">{catname}</span> <span class="iname">{name}</span>[stock]</a>[/stock]
	</div>
	<div class="td w10">
		1
	</div>
	<div class="td w100[tradein] nPay trIn[/tradein]">
		[tradein][edit]<input type="number" step="0.01" min="0" name="ti_price" onchange="invoices.total();" value="{price}" price="{sale}" readonly>[not-edit]$ {price}[/edit][not-tradein]$ {price}[/tradein]
	</div>
	<div class="td w10">
		[tradein]no[not-tradein]yes[/tradein]
	</div>
</div>