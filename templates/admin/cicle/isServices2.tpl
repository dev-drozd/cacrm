<div class="tr dev" id= "service_{id}" [comment]onclick="issues.toggleList(this, event);"[/comment]>
	<div class="td w100">
		<span class="thShort">Name: </span>{name}
	</div>
	<div class="td servQuantity" data-quantity="{quantity}" ondblclick="issues.serviceQuantity(this, '{id}', {issue-id}, {quantity});">
		<span class="thShort">Quantity: </span>{quantity}
	</div>
	<div class="td hnt hntTop" data-title="Click to edit">
		<span class="thShort">Price: </span>{currency} <span class="servPrice qPrice edprice" data-price="{sprice}" data-title="Edit price" data-inv_price="{inv_price}" onclick="issues.pPrice(this, '{id}', {issue-id}, 'service_info')">{price}</span>
	</div>
	<div class="td">
		{type}
	</div>
	<div class="td w10" style="text-align: center;">
		<span class="thShort">Options: </span>
		<!-- <a href="javascript:issues.addComment('{id}', {issue-id});"><span class="fa fa-comment"></span></a> -->
		<a href="javascript:issues.delInv('{id}', 'service', {issue-id});" class="hnt hntBottom" data-title="Remove">
			<span class="fa fa-times" style="color:#CE1212"></span>
		</a>
	</div>
</div>
[comment]
<div class="tr issues" id="service_com_{id}">
	<a href="/users/view/{staff-id}">{staff-name}</a>: <span>{comment}</span>
</div>
[/comment]