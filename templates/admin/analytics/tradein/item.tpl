<div class="tr">
	<div class="td">{id}</div>
	<div class="td">{name}</div>
	<div class="td">[purchased]<a href="/invoices/view/{invoice}" onclick="Page.get(this.href); reutrn false;" class="hnt hntTop" data-title="View invoice"[partial] style="color: #d04646;"[/partial]><span class="fa fa-check gr"></span></a>[/purchased]</div>
	<div class="td">${purchase_price}</div>
	<div class="td">${sale_price}</div>
	<div class="td">${proceed}</div>
	<div class="td">{type}</div>
</div>