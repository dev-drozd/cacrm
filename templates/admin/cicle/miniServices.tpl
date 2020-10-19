<div class="tr dev" id= "miniService_{id}">
	<div class="td">
		<span class="thShort">Service: </span>{name}
	</div>
	<div class="td w100 servQuantity" data-quantity="{quantity}" ondblclick="issues.serviceQuantity(this, '{id}', {issue-id}, {quantity});">
		<span class="thShort">Quantity: </span>{quantity}
	</div>
	<div class="td w100 miniServPrice">
		<span class="thShort">Price: </span>{currency}<span class="msPrice qPrice"  data-price="{sprice}" data-inv_price="{inv_price}" ondblclick="issues.pPrice(this, '{id}', {issue-id}, 'service_info')">{price}</span>
	</div>
</div>