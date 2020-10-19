<div class="sUser [comments]{confirmed}[/comments][rma_request] returnRequest[/rma_request] {class}" id="purchase_{id}">
	<div class="uThumb">
		[photo]<img src="/uploads/images/{id}/thumb_{photo}" onclick="showPhoto(this.src);">[not-photo]<span class="fa fa-desktop"></span>[/photo]
	</div>
	
	<a href="[edit]/purchases/edit/{id}[not-edit]#[/edit]" onclick="Page.get(this.href); return false;" class="uInfo">
		<div class="uInfo pch">
			<span class="thShort">Name: </span>{name}<br>
			<span class="thShort">Date: </span><i>{date}</i>
			[transaction]<br>Transaction: <i>{transaction}</i>[/transaction]
		</div>
	</a>
	<div class="pStatus">
		<span [comment]class="hnt hntTop" data-title="{comment}"[/comment]><span class="thShort">Status: </span>{status}</span>
		<br><span class="thShort">Tracking: </span>{tracking}
		<br><span class="thShort">Estimated: </span><span class="estimated">{estimated}</span>
	</div>
	<div class="pStatus" style="width: 100px">
		<span class="thShort">Price: </span>{purchase-currency}{price}
		<br><span class="thShort">Quantity: </span> Q-ty: {quantity}
		<br><span class="thShort">Total: </span>{purchase-currency}{total}
	</div>
	<div class="muInfo">
		[rma_page]
			[request_rma]Requested: <a href="/users/view/{request-rma-id}" onclick="Page.get(this.href); return false;">{request-rma-name} {request-rma-lastname}</a><br>[/request_rma]
			[confirm_rma]Confirmed: <a href="/users/view/{confirm-rma-id}" onclick="Page.get(this.href); return false;">{confirm-rma-name} {confirm-rma-lastname}</a><br>[/confirm_rma]
			[pickup_rma]Picked up: <a href="/users/view/{pickup-rma-id}" onclick="Page.get(this.href); return false;">{pickup-rma-name} {pickup-rma-lastname}</a><br>[/pickup_rma]
		[not-rma_page]
			[create]Created: <a href="/users/view/{create-id}" onclick="Page.get(this.href); return false;">{create-name} {create-lastname}</a><br>[/create]
			[confirm]Confirmed: <a href="/users/view/{confirm-id}" onclick="Page.get(this.href); return false;">{confirm-name} {confirm-lastname}</a><br>[/confirm]
			[edited]Edited: <a href="/users/view/{edited-id}" onclick="Page.get(this.href); return false;">{edited-name} {edited-lastname}</a>[/edited]
			[request_rma]Requested: <a href="/users/view/{request-rma-id}" onclick="Page.get(this.href); return false;">{request-rma-name} {request-rma-lastname}</a><br>[/request_rma]
			[customer]Customer: <a href="/users/view/{customer-id}" onclick="Page.get(this.href); return false;">{customer-name} {customer-lastname}</a><br>[/customer]
		[/rma_page]
		[rma_any]<b>Status: RMA</b>[/rma_any]
	</div>
	[notdel]
	<div class="uMore">
		<span class="fa fa-ellipsis-h" onclick="$(this).next().toggle(0);"></span>
		<ul>
			[edit]<li><a href="/purchases/edit/{id}" onclick="Page.get(this.href); return false;" ><span class="fa fa-pencil"></span> {lang=editPurchase}</a></li>[/edit]
			[delete]<li><a href="javascript:purchases.confirmDel({id})"><span class="fa fa-times"></span> {lang=delPurchase}</a></li>[/delete]
			[confirm]
				[received][not-received]<li><a href="javascript:purchases.[in-store]reciveStock[not-in-store]receiveMdl[/in-store]({id});"><span class="fa fa-archive"></span> Receive</a></li>[/received]
			[rma]
				[can_confirm]
					[rma_close][not-rma_close]<li><a href="javascript:purchases.rma({id}, 'pickup')"><span class="fa fa-car"></span> Pick up</a></li>[/rma_close]
					<li><a href="javascript:purchases.rmaRestore({id})"><span class="fa fa-times"></span> Cancel returning</a></li>
				[/can_confirm]
			[not-rma]
				[rma_request]
					[can_confirm]
						<li><a href="javascript:purchases.rmaReadComment({id});"><span class="fa fa-check"></span> Confirm returm</a></li>
						<li><a href="javascript:purchases.rmaRestore({id})"><span class="fa fa-times"></span> Cancel returning</a></li>
					[/can_confirm]
				[not-rma_request]
					<li><a href="javascript:purchases.confirmRma({id})"><span class="fa fa-exclamation"></span> Return request</a></li>
				[/rma_request]
			[/rma]
			[/confirm]
		</ul>
		[comments]
			<!--<span class="fa fa-comment purchComments" onclick="purchases.comments({id})"></span>-->
		[/comments]
	</div>
	[/notdel]
</div>