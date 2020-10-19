<div class="tr" id="purchase_{id}">
	<div class="td w10">
		<span class="thShort">ID: </span><a href="/purchases/edit/{id}" onclick="Page.get(this.href); return false;">{id}</a>
	</div>
	<div class="td">
		<span class="thShort">Name: </span>{name}
	</div>
	<div class="td">
		<span class="thShort">Status: </span>
		[can_receive]
			<a href="javascript:purchases.[instore]reciveStock[not-instore]receiveMdl[/instore]({id});">Recived</a>
		[not-can_receive]
			{status}
		[/can_receive]
	</div>
	<div class="td noWrap">
		<span class="thShort">Link: </span>{link}
	</div>
	<div class="td">
		<span class="thShort">Price: </span>{currency} <span class="purPrice" ondblclick="issues.pPrice(this, {id}, {issue-id}, 'purchase_info')" data-price="{price}">{price}</span>
	</div>
	<div class="td w100">
		<span class="thShort">Action: </span>
		<a href="/purchases/edit/{id}" onclick="Page.get(this.href); return false;" class="hnt hntTop green" data-title="Edit purchase"><span class="fa fa-pencil"></span></a>
		<a href="javascript:issues.delPur({id}, {issue-id})" class="hnt hntTop" data-title="Del purchase"><span class="fa fa-times"></span></a>
		[invoice-partial]
			<a href="/invoices/make_refund/{invoice}?type=purchase&id={id}" onclick="Page.get(this.href); return false;" class="hnt hntTop green" data-title="RMA"><span class="fa fa-arrow-left"></span></a>
		[/invoice-partial]
	</div>
</div>