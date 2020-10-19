<div class="tr dev" id= "service_{id}" [comment]onclick="issues.toggleList(this, event);"[/comment]>
	<div class="td w10">
		<span class="fa fa-[comment]chevron-right[not-comment]circle[/comment] isOpen"></span> <strong>#{id-show}</strong>
	</div>
	<div class="td">
		<span class="thShort">Service: </span>{name}
	</div>
	<div class="td w100 servQuantity" data-quantity="{quantity}" ondblclick="issues.serviceQuantity(this, '{id}', {issue-id}, {quantity});">
		<span class="thShort">Quantity: </span>{quantity}
	</div>
	<div class="td w100">
		<span class="thShort">Price: </span>{currency} <span class="servPrice qPrice" data-price="{sprice}" data-inv_price="{inv_price}" ondblclick="issues.pPrice(this, '{id}', {issue-id}, 'service_info')">{price}</span>
	</div>
	<div class="td w100">
		<span class="thShort">Actions: </span>
		<a href="javascript:issues.addComment('{id}', {issue-id});"><span class="fa fa-comment"></span></a>
		<a href="javascript:issues.delInv('{id}', 'service', {issue-id});"><span class="fa fa-times"></span></a>
	</div>
</div>
[comment]
<div class="tr issues" id="service_com_{id}">
	<a href="/users/view/{staff-id}">{staff-name}</a>: <span>{comment}</span>
</div>
[/comment]