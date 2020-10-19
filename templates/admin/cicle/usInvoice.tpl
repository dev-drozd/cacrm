<div class="tr">
	<div class="td w10">
		<span class="thShort">ID: </span>{id}
	</div>
	<div class="td">
		<span class="thShort">Date: </span>{date}
	</div>					
	<div class="td">
		<span class="thShort">Total: </span>{total}
	</div>
	<div class="td">
		<span class="thShort">Paid: </span>{paid}
	</div>
	<div class="td">
		<span class="thShort">Due: </span>{due}
	</div>
	<div class="td">
		<span class="thShort">Status: </span>{status}
	</div>
	<div class="td w100">
		<a href="/invoices/view/{id}" onclick="Page.get(this.href);" class="hnt hntTop" data-title="View invoice"><span class="fa fa-eye"></span></a>
		[edit-invoce]<a href="/invoices/edit/{id}" onclick="Page.get(this.href);" class="hnt hntTop green" data-title="Edit invoice"><span class="fa fa-pencil"></span></a>[/edit-invoce]
	</div>
</div>