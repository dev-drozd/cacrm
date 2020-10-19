<div class="tr" id= "inventory_{id}">
	<div class="td w100">
		<span class="thShort">Stock: </span>{name}
	</div>
	<div class="td">
		-
	</div>
	<div class="td hnt hntTop" data-title="Click to edit">
		<span class="thShort">Price: </span>{currency} <span class="servPrice edprice" onclick="issues.pPrice(this, {id}, {issue-id}, 'inventory_info')" data-price="{price}" data-cost-price="{cost-price}">{price}</span>
	</div>
	<div class="td">
		Inventory
	</div>
	<div class="td w10">
		<span class="thShort">Options: </span>
		<a href="/inventory/view/{id}" onclick="Page.get(this.href); return false;" class="hnt hntTop" data-title="View device"><span class="fa fa-eye"></span></a>
		<a href="/inventory/edit/{id}" onclick="Page.get(this.href); return false;" class="hnt hntTop" data-title="Edit device"><span class="fa fa-pencil"></span></a>
		<!--<a href="/issues/add/{id}" onclick="Page.get(this.href); return false;" class="hnt hntTop" data-title="Add issue"><span class="fa fa-plus"></span></a>-->
		<a href="javascript:issues.delInv({id}, 'stock', {issue-id});" class="hnt hntTop" data-title="Delete device"><span class="fa fa-times"></span></a>
		[invoice-partial]
			<a href="/invoices/make_refund/{invoice}?type=inventory&id={id}" onclick="Page.get(this.href); return false;" class="hnt hntTop green" data-title="RMA"><span class="fa fa-arrow-left"></span></a>
		[/invoice-partial]
	</div>
</div>