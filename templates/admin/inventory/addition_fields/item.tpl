<div class="tr" id="addition_field_{id}">
	<div class="td wp20 lh45">
		<a href="/users/view/{staff-id}" onclick="Page.get(this.href); return false;">
			[ava]
				<img src="/uploads/images/users/{staff-id}/thumb_{image}" class="miniRound">
			[not-ava]
				<span class="fa fa-user-secret miniRound"></span>
			[/ava]
			{staff-name} {staff-lastname}
		</a>
	</div>
	<div class="td w125"><span class="thShort">Name: </span>{name}</div>
	<div class="td wp10"><span class="thShort">Price: </span>{price}</div>
	<div class="td wp10"><span class="thShort">Tax: </span>{tax}</div>
	<div class="td wp15 lh45">
		<a href="/objects/edit/{store-id}" onclick="Page.get(this.href); return false;">
			[sava]
				<img src="/uploads/images/stores/{store-id}/thumb_{store-image}" class="miniRound">
			[not-sava]
				<span class="fa fa-user-secret miniRound"></span>
			[/sava]
			{store}
		</a>
	</div>
	<div class="td wp10"><span class="thShort">Invoice: </span>[inv]<a href="/invoices/view/{invoice-id}" onclick="Page.get(this.href); return false;">#{invoice-id}</a>[/inv]</div>
	<div class="td wp10"><span class="thShort">Type: </span>{type}</div>
	<div class="td wp15">
		<button class="btn btnPoints" type="button" onclick="inventory.approveField({id})">
			<span class="fa fa-check"></span> Approve
		</button>
	</div>
	<div class="td wp15">
		<i class="fa fa-times" onclick="inventory.delAddField(this)" style="color: #ca1313;font-size: 20px;"></i>
	</div>
</div>